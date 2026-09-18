<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Enums\LeaveStatus;
use App\Http\Controllers\Controller;
use App\Models\LeaveRequest;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class HrOverviewController extends Controller
{
    public function index()
    {
        // Pending leave approvals
        $pendingQuery = LeaveRequest::query()
            ->with('employee')
            ->where('status', LeaveStatus::Pending->value)
            ->latest();

        $pendingLeavesCount = (clone $pendingQuery)->count();
        $recentPending = (clone $pendingQuery)->limit(6)->get();

        // Attendance snapshot
        $attendanceToday = 0;
        $lateToday = 0;

        if (Schema::hasTable('attendances')) {
            $today = now()->toDateString();

            $attendanceToday = DB::table('attendances')
                ->whereDate('date', $today)
                ->whereIn('morning_status', ['present', 'late'])
                ->count();

            $lateToday = DB::table('attendances')
                ->whereDate('date', $today)
                ->where('morning_status', 'late')
                ->count();
        }

        return view('admin.sections.hr', [
            'pendingLeavesCount' => $pendingLeavesCount,
            'recentPending' => $recentPending,
            'attendanceToday' => $attendanceToday,
            'lateToday' => $lateToday,
        ]);
    }
}
