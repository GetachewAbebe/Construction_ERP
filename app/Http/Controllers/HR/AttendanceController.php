<?php

namespace App\Http\Controllers\HR;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\Employee;
use App\Services\AttendanceService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AttendanceController extends Controller
{
    protected AttendanceService $attendanceService;

    public function __construct(AttendanceService $attendanceService)
    {
        $this->attendanceService = $attendanceService;
    }

    /**
     * Daily attendance listing with filters & stats.
     */
    public function index(Request $request)
    {
        $employees = Employee::orderBy('first_name')->orderBy('last_name')->get();

        $query = Attendance::with('employee');

        // Filters
        if ($request->filled('date_from')) {
            $query->whereDate('date', '>=', $request->input('date_from'));
        }

        if ($request->filled('date_to')) {
            $query->whereDate('date', '<=', $request->input('date_to'));
        }

        if ($request->filled('employee_filter')) {
            $query->where('employee_id', $request->input('employee_filter'));
        }

        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        $attendances = $query
            ->orderByDesc('date')
            ->orderByDesc('clock_in')
            ->paginate(20);

        // Today stats via Service
        $today = Carbon::today();
        $todayStats = $this->attendanceService->getDailyStats($today);

        // Current user's open attendance for quick check-out
        $myOpenAttendance = null;
        if (Auth::check() && Auth::user()->employee_id) {
            $myOpenAttendance = Attendance::where('employee_id', Auth::user()->employee_id)
                ->whereDate('date', $today)
                ->whereNull('clock_out')
                ->first();
        }

        return view('hr.attendance.index', [
            'attendances' => $attendances,
            'employees' => $employees,
            'todayStats' => $todayStats,
            'myOpenAttendance' => $myOpenAttendance,
        ]);
    }

    /**
     * Display the daily batch attendance sheet for HR.
     */
    public function dailySheet(Request $request)
    {
        $this->authorize('manage-attendance');

        $date = $request->filled('date') ? Carbon::parse($request->date) : Carbon::today();
        $employees = Employee::orderBy('first_name')->get();
        
        $attendances = Attendance::whereDate('date', $date)->get()->keyBy('employee_id');

        return view('hr.attendance.daily-sheet', [
            'date' => $date,
            'employees' => $employees,
            'attendances' => $attendances
        ]);
    }

    /**
     * Store bulk session-based attendance.
     */
    public function storeDailySheet(Request $request)
    {
        $this->authorize('manage-attendance');

        $request->validate([
            'date' => 'required|date',
            'attendance' => 'required|array',
            'attendance.*.morning' => 'required|in:present,absent,leave,late',
            'attendance.*.afternoon' => 'required|in:present,absent,leave',
        ]);

        $date = Carbon::parse($request->date);

        DB::transaction(function () use ($request, $date) {
            foreach ($request->attendance as $employeeId => $data) {
                // Ignore if not present (both sessions absent)
                if ($data['morning'] === 'absent' && $data['afternoon'] === 'absent') {
                    continue;
                }

                $this->attendanceService->markSessionAttendance(
                    (int) $employeeId,
                    $date,
                    $data['morning'],
                    $data['afternoon'],
                    $data['note'] ?? null
                );
            }
        });

        return redirect()->route('hr.attendance.index')
            ->with('success', "Daily Attendance sheet for {$date->format('Y-m-d')} has been validated and saved.");
    }

    /**
     * Display the weekly batch attendance sheet (Monday to Saturday).
     */
    public function weeklySheet(Request $request)
    {
        $this->authorize('manage-attendance');

        // Determine current week (Monday to Saturday)
        $date = $request->filled('date') ? Carbon::parse($request->date) : Carbon::today();
        $monday = (clone $date)->startOfWeek(Carbon::MONDAY);
        $saturday = (clone $monday)->addDays(5);

        $employees = Employee::orderBy('first_name')->get();
        
        // Fetch all attendance for these employees within the week range
        $attendances = Attendance::whereBetween('date', [$monday->toDateString(), $saturday->toDateString()])
            ->get()
            ->groupBy(['employee_id', function ($item) {
                return $item->date->toDateString();
            }]);

        return view('hr.attendance.weekly-sheet', [
            'monday' => $monday,
            'saturday' => $saturday,
            'employees' => $employees,
            'attendances' => $attendances,
            'date' => $date
        ]);
    }

    /**
     * Store bulk weekly attendance.
     */
    public function storeWeeklySheet(Request $request)
    {
        $this->authorize('manage-attendance');

        $request->validate([
            'week_start' => 'required|date',
            'attendance' => 'required|array',
        ]);

        $weekStart = Carbon::parse($request->week_start);

        DB::transaction(function () use ($request) {
            foreach ($request->attendance as $employeeId => $dates) {
                foreach ($dates as $dateString => $data) {
                    $morning = $data['morning'] ?? 'absent';
                    $afternoon = $data['evening'] ?? 'absent'; // Mapping 'evening' from UI to 'afternoon' in DB

                    // Ignore if both sessions are absent
                    if ($morning === 'absent' && $afternoon === 'absent') {
                        continue;
                    }

                    $this->attendanceService->markSessionAttendance(
                        (int) $employeeId,
                        Carbon::parse($dateString),
                        $morning,
                        $afternoon,
                        null
                    );
                }
            }
        });

        return redirect()->route('hr.attendance.index')
            ->with('success', "Weekly Batch Attendance has been validated and synchronized.");
    }

    /**
     * AJAX Toggle for button-based attendance.
     */
    public function toggleSession(Request $request)
    {
        $this->authorize('manage-attendance');

        $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'date' => 'required|date',
            'session' => 'required|in:morning,afternoon',
            'action' => 'required|in:check-in,check-out,leave,reset'
        ]);

        $employeeId = $request->employee_id;
        $date = Carbon::parse($request->date);

        // --- NEW: Strict Date Lock ---
        if (!$date->isToday()) {
            return response()->json([
                'success' => false, 
                'message' => 'Attendance can only be managed for the current day (' . Carbon::today()->format('M d, Y') . ').'
            ], 422);
        }

        $session = $request->session;
        $action = $request->action;

        // Fetch current status
        $att = Attendance::where('employee_id', $employeeId)->where('date', $date->toDateString())->first();
        $currentStatus = $session === 'morning' ? ($att ? $att->morning_status : 'absent') : ($att ? $att->afternoon_status : 'absent');

        $newStatus = Attendance::SESSION_ABSENT;

        if ($action === 'check-in') {
            // Toggle morning
            if (in_array($currentStatus, [Attendance::SESSION_PRESENT, Attendance::SESSION_LATE])) {
                $newStatus = Attendance::SESSION_ABSENT;
            } else {
                if ($date->isToday()) {
                    $tempAtt = $this->attendanceService->autoCheckIn(Employee::find($employeeId), $date);
                    $newStatus = $tempAtt->morning_status;
                } else {
                    $newStatus = Attendance::SESSION_PRESENT;
                }
            }
        } elseif ($action === 'check-out') {
            // Toggle afternoon
            if ($currentStatus === Attendance::SESSION_PRESENT) {
                $newStatus = Attendance::SESSION_ABSENT;
            } else {
                if ($date->isToday()) {
                    if (!$att) {
                        $att = $this->attendanceService->autoCheckIn(Employee::find($employeeId), $date);
                    }
                    $tempAtt = $this->attendanceService->autoCheckOut($att, $date);
                    $newStatus = $tempAtt->afternoon_status;
                } else {
                    $newStatus = Attendance::SESSION_PRESENT;
                }
            }
        }

        $attendance = $this->attendanceService->toggleSessionStatus(
            (int) $employeeId,
            $date->toDateString(),
            $session,
            $newStatus
        );

        return response()->json([
            'success' => true,
            'status' => $session === 'morning' ? $attendance->morning_status : $attendance->afternoon_status,
            'time' => ($session === 'morning' ? ($attendance->clock_in ? $attendance->clock_in->format('H:i') : null) : ($attendance->clock_out ? $attendance->clock_out->format('H:i') : null)),
            'total_credit' => $attendance->total_credit
        ]);
    }

    /**
     * Weekly Salary Analysis report based on attendance credits.
     */
    public function weeklySalary(Request $request)
    {
        $this->authorize('manage-attendance');

        // Default to current week (starting Monday)
        $date = $request->filled('week_start') ? Carbon::parse($request->week_start) : Carbon::now()->startOfWeek();
        
        $employees = Employee::orderBy('first_name')->get();
        $analysis = [];

        foreach ($employees as $employee) {
            $analysis[] = $this->attendanceService->calculateWeeklySalaryAnalysis($employee, $date);
            // We'll wrap the employee in the result
            $analysis[count($analysis)-1]['employee'] = $employee;
        }

        return view('hr.attendance.weekly-salary', [
            'weekStart' => $date,
            'weekEnd' => (clone $date)->addDays(6),
            'analysis' => $analysis
        ]);
    }

    /**
     * Check-in logic with simple role-based behavior.
     */
    public function checkIn(\App\Http\Requests\HR\CheckInRequest $request)
    {
        $data = $request->validated();
        $user = Auth::user();
        $today = Carbon::today();

        // Determine target employee
        $employeeId = null;
        if ($user->can('manage-attendance') && ! empty($data['employee_id'])) {
            $employeeId = $data['employee_id'];
        } else {
            $employeeId = $user->employee_id;
        }

        if (! $employeeId) {
            return back()->with('error', 'Authentication mismatch: No linked employee profile detected.');
        }

        try {
            $employee = Employee::findOrFail($employeeId);

            // Record attendance via automated rule-based service
            $attendance = $this->attendanceService->autoCheckIn($employee);

            return back()->with('success', "Access granted: {$employee->name} checked in successfully at ".$attendance->clock_in->format('H:i') . " (" . strtoupper($attendance->morning_status) . ")");

        } catch (\Exception $e) {
            \Log::error('Attendance check-in failed: '.$e->getMessage());

            return back()->with('error', 'System failure during access recording.');
        }
    }

    /**
     * Check-out logic with basic authorization.
     */
    public function checkOut(Request $request, $id)
    {
        $attendance = Attendance::with('employee')->findOrFail($id);
        $user = Auth::user();

        // If user is not HR/admin, they can only check out themselves
        if ($user && ! $user->can('manage-attendance')) {
            if ($user->employee_id !== $attendance->employee_id) {
                abort(403, 'You are not allowed to check out this employee.');
            }
        }

        if ($attendance->clock_out) {
            return back()->with('error', 'Employee already checked out.');
        }

        $this->attendanceService->autoCheckOut($attendance);

        return back()->with('success', 'Checked out successfully at ' . $attendance->clock_out->format('H:i') . " (" . strtoupper($attendance->afternoon_status) . ")");
    }

    /**
     * Monthly summary page (per employee & department).
     */
    public function monthlySummary(Request $request)
    {
        $now = Carbon::now();

        $year = (int) $request->input('year', $now->year);
        $month = (int) $request->input('month', $now->month);
        $departmentFilter = $request->input('department');

        // Departments list for filter
        $departments = Employee::query()
            ->whereNotNull('department')
            ->select('department')
            ->distinct()
            ->orderBy('department')
            ->pluck('department');

        $summary = $this->attendanceService->buildMonthlySummaryData($year, $month, $departmentFilter);

        return view('hr.attendance.monthly-summary', array_merge($summary, [
            'year' => $year,
            'month' => $month,
            'departmentFilter' => $departmentFilter,
            'departments' => $departments,
        ]));
    }

    /**
     * CSV export for monthly summary (same filters as page).
     */
    public function exportMonthlySummaryCsv(Request $request)
    {
        $now = Carbon::now();

        $year = (int) $request->input('year', $now->year);
        $month = (int) $request->input('month', $now->month);
        $departmentFilter = $request->input('department');

        $summary = $this->attendanceService->buildMonthlySummaryData($year, $month, $departmentFilter);

        $perEmployee = $summary['perEmployee'];
        $startOfMonth = $summary['startOfMonth'];

        $filename = sprintf(
            'attendance_summary_%s_%d-%02d.csv',
            $departmentFilter ?: 'all',
            $year,
            $month
        );

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="'.$filename.'"',
            'Cache-Control' => 'no-store, no-cache',
        ];

        return response()->streamDownload(function () use ($perEmployee, $startOfMonth) {
            $handle = fopen('php://output', 'w');

            // Header row
            fputcsv($handle, [
                'Employee ID',
                'Employee Name',
                'Department',
                'Month',
                'Present Days',
                'Late Days',
                'Records',
                'Total Hours',
                'Avg Hours/Day',
            ]);

            foreach ($perEmployee as $summaryRow) {
                $employee = $summaryRow['employee'];

                fputcsv($handle, [
                    $employee->id,
                    $employee->first_name.' '.$employee->last_name,
                    $employee->department,
                    $startOfMonth->format('Y-m'),
                    $summaryRow['present_days'],
                    $summaryRow['late_days'],
                    $summaryRow['records'],
                    $summaryRow['total_hours'],
                    $summaryRow['avg_hours'],
                ]);
            }

            fclose($handle);
        }, $filename, $headers);
    }
}
