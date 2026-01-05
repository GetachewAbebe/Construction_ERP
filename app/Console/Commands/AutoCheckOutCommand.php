<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Attendance;
use App\Services\AttendanceService;
use Carbon\Carbon;

class AutoCheckOutCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:auto-check-out';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Automatically check out employees who forgot to check out at the end of the day.';

    /**
     * Execute the console command.
     */
    public function handle(AttendanceService $attendanceService)
    {
        $today = Carbon::today();
        $openAttendances = Attendance::whereDate('date', $today)
            ->whereNull('clock_out')
            ->get();

        if ($openAttendances->isEmpty()) {
            $this->info('No open attendance records found for today.');
            return;
        }

        $shiftEndTime = $attendanceService->getSetting('shift_end_time', '17:00');
        $checkoutTime = Carbon::parse($shiftEndTime)->setDate($today->year, $today->month, $today->day);

        // If current time is earlier than shift end, use current time? 
        // No, typically this runs at midnight or end of day, so we use shift_end_time.
        
        $count = 0;
        foreach ($openAttendances as $attendance) {
            $attendance->update([
                'clock_out' => $checkoutTime,
                'note' => ($attendance->note ? $attendance->note . ' | ' : '') . 'Auto-checked out by system.',
            ]);
            $count++;
        }

        $this->info("Successfully auto-checked out {$count} employees.");
    }
}
