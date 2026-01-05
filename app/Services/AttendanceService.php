<?php

namespace App\Services;

use App\Models\Attendance;
use App\Models\Employee;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class AttendanceService
{
    /**
     * Get attendance statistics for a specific date.
     */
    public function getDailyStats(Carbon $date): array
    {
        $presentCount = Attendance::whereDate('date', $date)
            ->where('status', Attendance::STATUS_PRESENT)
            ->distinct('employee_id')
            ->count();

        $lateCount = Attendance::whereDate('date', $date)
            ->where('status', Attendance::STATUS_LATE)
            ->distinct('employee_id')
            ->count();

        $totalEmployees = Employee::count();
        $absentCount = max($totalEmployees - ($presentCount + $lateCount), 0);

        return [
            'present' => $presentCount,
            'late'    => $lateCount,
            'absent'  => $absentCount,
        ];
    }

    /**
     * Determine attendance status based on shift start time.
     */
    public function determineStatus(Carbon $now): string
    {
        $shiftStartString = $this->getSetting('shift_start_time', '09:00');
        $timezone         = config('attendance.timezone', config('app.timezone'));
        
        $shiftStartToday = Carbon::parse($shiftStartString, $timezone)
            ->setDate($now->year, $now->month, $now->day);

        // Optional: Grace period
        $graceMinutes = (int) $this->getSetting('grace_period_minutes', 0);
        if ($graceMinutes > 0) {
            $shiftStartToday->addMinutes($graceMinutes);
        }

        return $now->greaterThan($shiftStartToday)
            ? Attendance::STATUS_LATE
            : Attendance::STATUS_PRESENT;
    }

    /**
     * Helper to get setting from DB with fallback.
     */
    public function getSetting(string $key, $default = null)
    {
        return \App\Models\AttendanceSetting::where('key', $key)->first()?->value ?? $default;
    }

    /**
     * Build monthly summary data.
     */
    public function buildMonthlySummaryData(int $year, int $month, ?string $departmentFilter = null): array
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
