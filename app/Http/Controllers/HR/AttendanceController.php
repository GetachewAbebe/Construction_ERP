<?php

namespace App\Http\Controllers\HR;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\Employee;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;

class AttendanceController extends Controller
{
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

        // Today stats
        $today = Carbon::today();

        $presentCount = Attendance::whereDate('date', $today)
            ->where('status', Attendance::STATUS_PRESENT)
            ->distinct('employee_id')
            ->count();

        $lateCount = Attendance::whereDate('date', $today)
            ->where('status', Attendance::STATUS_LATE)
            ->distinct('employee_id')
            ->count();

        $totalEmployees = Employee::count();
        $absentCount = max($totalEmployees - ($presentCount + $lateCount), 0);

        $todayStats = [
            'present' => $presentCount,
            'late'    => $lateCount,
            'absent'  => $absentCount,
        ];

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
    public function checkIn(Request $request)
    {
        $request->validate([
            'employee_id' => 'nullable|exists:employees,id',
        ]);

        $user = Auth::user();
        $today = Carbon::today();

        // Determine which employee to check in
        if ($user && $user->can('manage-attendance') && $request->filled('employee_id')) {
            $employeeId = $request->input('employee_id');
        } elseif ($user && $user->employee_id) {
            $employeeId = $user->employee_id;
        } else {
            // Fallback: require employee_id
            $request->validate([
                'employee_id' => 'required|exists:employees,id',
            ]);
            $employeeId = $request->input('employee_id');
        }

        $employee = Employee::findOrFail($employeeId);

        // Prevent duplicate open sessions for today
        $existingOpen = Attendance::where('employee_id', $employee->id)
            ->whereDate('date', $today)
            ->whereNull('clock_out')
            ->first();

        if ($existingOpen) {
            return back()->with('error', 'This employee is already checked in and not yet checked out.');
        }

        // Determine status: present vs late using config
        $shiftStartString = config('attendance.start_time', '09:00');
        $timezone = config('attendance.timezone', config('app.timezone'));
        $now = Carbon::now($timezone);
        $shiftStartToday = Carbon::parse($shiftStartString, $timezone)
            ->setDate($today->year, $today->month, $today->day);

        $status = $now->greaterThan($shiftStartToday)
            ? Attendance::STATUS_LATE
            : Attendance::STATUS_PRESENT;

        Attendance::create([
            'employee_id' => $employee->id,
            'date'        => $today,
            'clock_in'    => $now,
            'status'      => $status,
        ]);

        return back()->with('success', 'Check-in recorded successfully.');
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

        $summary = $this->buildMonthlySummaryData($year, $month, $departmentFilter);

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

        $summary = $this->buildMonthlySummaryData($year, $month, $departmentFilter);

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

    /**
     * Shared logic to build monthly summary data.
     */
    protected function buildMonthlySummaryData(int $year, int $month, ?string $departmentFilter = null): array
    {
        $startOfMonth = Carbon::createFromDate($year, $month, 1)->startOfDay();
        $endOfMonth   = (clone $startOfMonth)->endOfMonth();

        $attendancesQuery = Attendance::with('employee')
            ->whereBetween('date', [
                $startOfMonth->toDateString(),
                $endOfMonth->toDateString(),
            ]);

        if ($departmentFilter) {
            $attendancesQuery->whereHas('employee', function ($q) use ($departmentFilter) {
                $q->where('department', $departmentFilter);
            });
        }

        $attendances = $attendancesQuery->get();

        // Per employee summary
        $perEmployee = $attendances
            ->groupBy('employee_id')
            ->map(function (Collection $rows) {
                /** @var \App\Models\Attendance $first */
                $first = $rows->first();
                $employee = $first->employee;

                $presentDays = $rows->where('status', Attendance::STATUS_PRESENT)->count();
                $lateDays    = $rows->where('status', Attendance::STATUS_LATE)->count();
                $records     = $rows->count();

                $totalHours  = $rows->sum('worked_hours');

                return [
                    'employee'      => $employee,
                    'present_days'  => $presentDays,
                    'late_days'     => $lateDays,
                    'records'       => $records,
                    'total_hours'   => $totalHours,
                    'avg_hours'     => $records ? round($totalHours / $records, 2) : null,
                ];
            })
            ->sortBy(function ($row) {
                return [
                    $row['employee']->department,
                    $row['employee']->first_name,
                    $row['employee']->last_name,
                ];
            });

        // Per department summary
        $perDepartment = $perEmployee
            ->groupBy(function ($row) {
                return $row['employee']->department ?? 'Unassigned';
            })
            ->map(function (Collection $rows, $deptName) {
                return [
                    'department'      => $deptName,
                    'employees_count' => $rows->count(),
                    'present_days'    => $rows->sum('present_days'),
                    'late_days'       => $rows->sum('late_days'),
                    'total_hours'     => $rows->sum('total_hours'),
                ];
            })
            ->sortBy('department');

        // Global aggregates
        $totalEmployeesInScope = $perEmployee->count();
        $totalHoursInScope     = $perEmployee->sum('total_hours');
        $totalPresentDays      = $perEmployee->sum('present_days');
        $totalLateDays         = $perEmployee->sum('late_days');

        return [
            'perEmployee'           => $perEmployee,
            'perDepartment'         => $perDepartment,
            'totalEmployeesInScope' => $totalEmployeesInScope,
            'totalHoursInScope'     => $totalHoursInScope,
            'totalPresentDays'      => $totalPresentDays,
            'totalLateDays'         => $totalLateDays,
            'startOfMonth'          => $startOfMonth,
            'endOfMonth'            => $endOfMonth,
        ];
    }
}
