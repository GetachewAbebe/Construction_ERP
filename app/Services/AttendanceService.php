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
     * Get attendance statistics for a specific date (Session Aware).
     */
    public function getDailyStats(Carbon $date): array
    {
        $presentAM = Attendance::whereDate('date', $date)
            ->where('morning_status', Attendance::SESSION_PRESENT)
            ->count();

        $presentPM = Attendance::whereDate('date', $date)
            ->where('afternoon_status', Attendance::SESSION_PRESENT)
            ->count();

        $totalEmployees = Employee::count();
        
        return [
            'morning_present' => $presentAM,
            'afternoon_present' => $presentPM,
            'capacity' => $totalEmployees,
        ];
    }

    /**
     * Mark or update attendance for a specific session.
     * Calculated credit: 1.0 for both present/late, 0.5 for one, 0 for none.
     */
    public function markSessionAttendance(int $employeeId, Carbon $date, string $morning, string $afternoon, ?string $note = null): Attendance
    {
        $attendance = Attendance::firstOrNew([
            'employee_id' => $employeeId,
            'date' => $date->toDateString(),
        ]);

        $attendance->morning_status = $morning;
        $attendance->afternoon_status = $afternoon;
        $attendance->note = $note;
        
        // Auto-set clock times if missing for today's date
        if ($date->isToday()) {
            if (in_array($morning, [Attendance::SESSION_PRESENT, Attendance::SESSION_LATE]) && !$attendance->clock_in) {
                $attendance->clock_in = Carbon::now();
            }
            if (in_array($afternoon, [Attendance::SESSION_PRESENT]) && !$attendance->clock_out) {
                $attendance->clock_out = Carbon::now();
            }
        }

        $attendance->total_credit = $attendance->calculateCredits();
        $attendance->save();

        return $attendance;
    }

    /**
     * Automatic check-in logic based on 8:30 AM threshold.
     */
    public function autoCheckIn(Employee $employee, ?Carbon $date = null): Attendance
    {
        $now = $date ?? Carbon::now();
        $attendance = Attendance::firstOrNew([
            'employee_id' => $employee->id,
            'date' => $now->toDateString(),
        ]);

        if ($attendance->exists && $attendance->clock_in) {
            return $attendance;
        }

        $attendance->clock_in = $now;
        
        // Threshold: 08:30 AM
        $threshold = (clone $now)->setTime(8, 30, 0);
        
        if ($now->lte($threshold)) {
            $attendance->morning_status = Attendance::SESSION_PRESENT;
        } else {
            $attendance->morning_status = Attendance::SESSION_LATE;
        }

        $attendance->afternoon_status = Attendance::SESSION_ABSENT;
        $attendance->total_credit = $attendance->calculateCredits();
        $attendance->save();

        return $attendance;
    }

    /**
     * Automatic check-out logic based on 5:30 PM threshold.
     */
    public function autoCheckOut(Attendance $attendance, ?Carbon $date = null): Attendance
    {
        $now = $date ?? Carbon::now();
        $attendance->clock_out = $now;

        // Threshold: 05:30 PM (17:30)
        $threshold = (clone $now)->setTime(17, 30, 0);

        if ($now->gte($threshold)) {
            $attendance->afternoon_status = Attendance::SESSION_PRESENT;
        } else {
            $attendance->afternoon_status = Attendance::SESSION_ABSENT;
        }

        $attendance->total_credit = $attendance->calculateCredits();
        $attendance->save();

        return $attendance;
    }

    /**
     * Specialized toggle for AJAX grid buttons.
     */
    public function toggleSessionStatus(int $employeeId, string $dateString, string $session, string $status): Attendance
    {
        $date = Carbon::parse($dateString);
        $attendance = Attendance::firstOrNew([
            'employee_id' => $employeeId,
            'date' => $date->toDateString(),
        ]);

        if ($session === 'morning') {
            $attendance->morning_status = $status;
            if (in_array($status, [Attendance::SESSION_PRESENT, Attendance::SESSION_LATE])) {
                // If it's today, we want the CURRENT time. 
                // We overwrite if it's currently null or midnight (default from seeder).
                if ($date->isToday()) {
                    if (!$attendance->clock_in || $attendance->clock_in->format('H:i:s') === '00:00:00') {
                        $attendance->clock_in = Carbon::now();
                    }
                } else {
                    // Past/Future day: assign 8:00 AM as standard
                    $attendance->clock_in = (clone $date)->setTime(8, 0, 0);
                }
            } else {
                // Absent/Leave: clear the clock time
                $attendance->clock_in = null;
            }
        } else {
            $attendance->afternoon_status = $status;
            if ($status === Attendance::SESSION_PRESENT) {
                if ($date->isToday()) {
                    if (!$attendance->clock_out || $attendance->clock_out->format('H:i:s') === '00:00:00') {
                        $attendance->clock_out = Carbon::now();
                    }
                } else {
                    // Standard 5:30 PM for past entries
                    $attendance->clock_out = (clone $date)->setTime(17, 30, 0);
                }
            } else {
                $attendance->clock_out = null;
            }
        }

        $attendance->total_credit = $attendance->calculateCredits();
        $attendance->save();

        return $attendance;
    }

    /**
     * Build monthly summary data with credit-based accounting.
     */
    public function buildMonthlySummaryData(int $year, int $month, ?string $departmentFilter = null): array
    {
        $startOfMonth = Carbon::createFromDate($year, $month, 1)->startOfDay();
        $endOfMonth = (clone $startOfMonth)->endOfMonth();

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

        $allEmployees = Employee::all();
        if ($departmentFilter) {
            $allEmployees = $allEmployees->where('department', $departmentFilter);
        }

        $attendancesGrouped = $attendances->groupBy('employee_id');

        $perEmployee = $allEmployees->map(function (Employee $employee) use ($attendancesGrouped) {
                $rows = $attendancesGrouped->get($employee->id, collect());
                
                $totalCredits = $rows->sum('total_credit');
                $records = $rows->count();

                $monthlySalary = (float) ($employee->salary ?: 0);
                $dailyRate = $monthlySalary / 22; 
                $payableAmount = $totalCredits * $dailyRate;

                return [
                    'employee' => $employee,
                    'total_credits' => $totalCredits,
                    'records' => $records,
                    'payable_amount' => $payableAmount,
                ];
            })
            ->sortBy(function ($row) {
                return [
                    $row['employee']->department,
                    $row['employee']->first_name,
                ];
            });

        return [
            'perEmployee' => $perEmployee,
            'totalEmployeesInScope' => $perEmployee->count(),
            'totalCreditsInScope' => $perEmployee->sum('total_credits'),
            'startOfMonth' => $startOfMonth,
            'endOfMonth' => $endOfMonth,
        ];
    }

    /**
     * Calculate weekly salary analysis.
     */
    public function calculateWeeklySalaryAnalysis(Employee $employee, Carbon $startDate): array
    {
        $endDate = (clone $startDate)->addDays(6);
        $attendances = Attendance::where('employee_id', $employee->id)
            ->whereBetween('date', [$startDate->toDateString(), $endDate->toDateString()])
            ->get();

        $totalCredits = $attendances->sum('total_credit');
        $baseSalary = (float) ($employee->salary ?: 0);
        $dailyRate = $baseSalary / 22; 
        $payableAmount = $totalCredits * $dailyRate;

        return [
            'credits' => $totalCredits,
            'daily_rate' => $dailyRate,
            'payable_amount' => $payableAmount,
            'start_date' => $startDate,
            'end_date' => $endDate,
            'attendances' => $attendances
        ];
    }
}
