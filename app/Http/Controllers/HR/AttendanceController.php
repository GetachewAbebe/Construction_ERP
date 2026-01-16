<?php

namespace App\Http\Controllers\HR;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\Employee;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;

use App\Services\AttendanceService;

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
            'attendances'      => $attendances,
            'employees'        => $employees,
            'todayStats'       => $todayStats,
            'myOpenAttendance' => $myOpenAttendance,
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
        if ($user->can('manage-attendance') && !empty($data['employee_id'])) {
            $employeeId = $data['employee_id'];
        } else {
            $employeeId = $user->employee_id;
        }

        if (!$employeeId) {
            return back()->with('error', 'Authentication mismatch: No linked employee profile detected.');
        }

        try {
            $employee = Employee::findOrFail($employeeId);

            // Prevent duplicate open sessions
            $existingOpen = Attendance::where('employee_id', $employee->id)
                ->whereDate('date', $today)
                ->whereNull('clock_out')
                ->first();

            if ($existingOpen) {
                return back()->with('error', 'Security check: Active session already in progress for this associate.');
            }

            // Record attendance via Service status context
            $now = Carbon::now(config('attendance.timezone', config('app.timezone')));
            $status = $this->attendanceService->determineStatus($now);

            Attendance::create([
                'employee_id' => $employee->id,
                'date'        => $today,
                'clock_in'    => $now,
                'status'      => $status,
                'ip_address'  => $request->ip(),
            ]);

            return back()->with('success', "Access granted: {$employee->name} checked in successfully at " . $now->format('H:i'));

        } catch (\Exception $e) {
            \Log::error('Attendance check-in failed: ' . $e->getMessage());
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
        if ($user && !$user->can('manage-attendance')) {
            if ($user->employee_id !== $attendance->employee_id) {
                abort(403, 'You are not allowed to check out this employee.');
            }
        }

        if ($attendance->clock_out) {
            return back()->with('error', 'Employee already checked out.');
        }

        $attendance->update([
            'clock_out' => Carbon::now(),
        ]);

        return back()->with('success', 'Checked out successfully.');
    }

    /**
     * Monthly summary page (per employee & department).
     */
    public function monthlySummary(Request $request)
    {
        $now = Carbon::now();

        $year  = (int) $request->input('year', $now->year);
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
            'year'             => $year,
            'month'            => $month,
            'departmentFilter' => $departmentFilter,
            'departments'      => $departments,
        ]));
    }

    /**
     * CSV export for monthly summary (same filters as page).
     */
    public function exportMonthlySummaryCsv(Request $request)
    {
        $now = Carbon::now();

        $year  = (int) $request->input('year', $now->year);
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
            'Content-Type'        => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            'Cache-Control'       => 'no-store, no-cache',
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
                    $employee->first_name . ' ' . $employee->last_name,
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
