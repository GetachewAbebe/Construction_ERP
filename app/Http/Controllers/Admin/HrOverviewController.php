<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\LeaveRequest;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class HrOverviewController extends Controller
{
    public function index()
    {
        // ----- Pending leave approvals (robust to schema differences)
        $hasStatus      = Schema::hasColumn('leave_requests', 'status');
        $hasApprovedAt  = Schema::hasColumn('leave_requests', 'approved_at');
        $hasRejectedAt  = Schema::hasColumn('leave_requests', 'rejected_at');

        $pendingQuery = LeaveRequest::query()->with('employee')->latest();

        if ($hasStatus) {
            $pendingQuery->where('status', 'pending');
        } elseif ($hasApprovedAt && $hasRejectedAt) {
            $pendingQuery->whereNull('approved_at')->whereNull('rejected_at');
        }
        // Otherwise: show latest requests as a fallback.

        $pendingLeavesCount = (clone $pendingQuery)->count();
        $recentPending      = (clone $pendingQuery)->limit(6)->get();

        // ----- Attendance snapshot (optional; safe if table not created yet)
        $attendanceToday = 0;
        $lateToday       = 0;

        if (Schema::hasTable('attendance_records')) {
            // Example schema: attendance_records(employee_id, date, status, check_in_time)
            $today = now()->toDateString();

            $attendanceToday = DB::table('attendance_records')
                ->whereDate('date', $today)
                ->count();

            $lateToday = DB::table('attendance_records')
                ->whereDate('date', $today)
                ->where('status', 'late')
                ->count();
        }

        return view('admin.sections.hr', [
            'pendingLeavesCount' => $pendingLeavesCount,
            'recentPending'      => $recentPending,
            'attendanceToday'    => $attendanceToday,
            'lateToday'          => $lateToday,
        ]);
    }
}
