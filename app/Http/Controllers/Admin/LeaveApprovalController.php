<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Enums\LeaveStatus;
use App\Http\Controllers\Controller;
use App\Mail\LeaveRequestStatusMail;
use App\Models\EmployeeOnLeave;
use App\Models\LeaveRequest;
use App\Models\User;
use App\Notifications\LeaveRequestStatusNotification;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Notification;

use Inertia\Inertia;
use Inertia\Response;

class LeaveApprovalController extends Controller
{
    public function index(): Response
    {
        // Auto-clear any dangling unread leave request notifications for requests already processed
        try {
            $decidedLeaveIds = LeaveRequest::where('status', '!=', LeaveStatus::Pending->value)->pluck('id');
            if ($decidedLeaveIds->isNotEmpty()) {
                \Illuminate\Notifications\DatabaseNotification::whereNull('read_at')
                    ->where(function ($q) {
                        $q->where('data', 'like', '%"type":"leave_request"%')
                          ->orWhere('data', 'like', '%"type": "leave_request"%');
                    })
                    ->where(function ($q) use ($decidedLeaveIds) {
                        foreach ($decidedLeaveIds as $id) {
                            $q->orWhere('data', 'like', '%"leave_id":'.$id.'%')
                              ->orWhere('data', 'like', '%"leave_id":"'.$id.'"%');
                        }
                    })
                    ->update(['read_at' => now()]);
            }
            if (auth()->check()) {
                auth()->user()->unsetRelation('unreadNotifications');
            }
        } catch (\Exception $e) {
            Log::warning('Failed to auto-clear processed leave notifications: '.$e->getMessage());
        }

        $pending = LeaveRequest::with('employee')
            ->where('status', LeaveStatus::Pending->value)
            ->orderByDesc('id')
            ->paginate(15)
            ->withQueryString();

        $approved = EmployeeOnLeave::with(['employee', 'approver'])
            ->orderByDesc('approved_at')
            ->paginate(15)
            ->withQueryString();

        $stats = [
            'pending_count' => LeaveRequest::where('status', LeaveStatus::Pending->value)->count(),
            'approved_this_month' => EmployeeOnLeave::whereMonth('approved_at', now()->month)->count(),
            'active_on_leave_today' => EmployeeOnLeave::whereDate('start_date', '<=', today())
                ->whereDate('end_date', '>=', today())
                ->count(),
        ];

        return Inertia::render('Admin/Requests/LeaveApprovals', [
            'pending' => $pending,
            'approved' => $approved,
            'stats' => $stats,
        ]);
    }

    public function approve(LeaveRequest $leave)
    {
        $this->markLeaveNotificationAsRead($leave);

        if ($leave->status !== LeaveStatus::Pending->value) {
            return back()->with('error', 'This request is already processed.');
        }

        DB::transaction(function () use ($leave) {
            $leave->update([
                'status' => LeaveStatus::Approved->value,
                'approved_by' => auth()->id(),
                'approved_at' => now(),
            ]);

            EmployeeOnLeave::create([
                'employee_id' => $leave->employee_id,
                'start_date' => $leave->start_date,
                'end_date' => $leave->end_date,
                'reason' => $leave->reason,
                'approved_by' => auth()->id(),
                'approved_at' => now(),
            ]);
        });

        $this->notifyParties($leave);

        return back()->with('success', 'Leave approved and recorded.');
    }

    public function reject(LeaveRequest $leave)
    {
        $this->markLeaveNotificationAsRead($leave);

        if ($leave->status !== LeaveStatus::Pending->value) {
            return back()->with('error', 'This request is already processed.');
        }

        $leave->update([
            'status' => LeaveStatus::Rejected->value,
            'approved_by' => auth()->id(),
            'approved_at' => now(),
        ]);

        $this->notifyParties($leave);

        return back()->with('success', 'Leave rejected.');
    }

    private function notifyParties(LeaveRequest $leave): void
    {
        if ($leave->employee && $leave->employee->user) {
            try {
                $leave->employee->user->notify(new LeaveRequestStatusNotification($leave, 'status_change'));
            } catch (\Exception $e) {
                Log::warning('Employee leave status notification failed: '.$e->getMessage());
            }

            try {
                Mail::to($leave->employee->user->email)
                    ->send(new LeaveRequestStatusMail($leave, $leave->employee->user));
            } catch (\Exception $e) {
                Log::error('Leave status mail failed: '.$e->getMessage());
            }
        }

        try {
            $hrManagers = User::role('HumanResourceManager')->get();
            if ($hrManagers->isNotEmpty()) {
                Notification::send($hrManagers, new LeaveRequestStatusNotification($leave, 'status_update'));
            }
        } catch (\Exception $e) {
            Log::warning('HR Manager leave notification failed: '.$e->getMessage());
        }

        $this->markLeaveNotificationAsRead($leave);
    }

    private function markLeaveNotificationAsRead(LeaveRequest $leave): void
    {
        try {
            \Illuminate\Notifications\DatabaseNotification::whereNull('read_at')
                ->where(function ($q) {
                    $q->where('data', 'like', '%"type":"leave_request"%')
                      ->orWhere('data', 'like', '%"type": "leave_request"%');
                })
                ->where(function ($q) use ($leave) {
                    $q->where('data', 'like', '%"leave_id":'.$leave->id.'%')
                      ->orWhere('data', 'like', '%"leave_id":"'.$leave->id.'"%')
                      ->orWhere('data', 'like', '%"leave_id": "'.$leave->id.'"%');
                })
                ->update(['read_at' => now()]);

            if (auth()->check()) {
                auth()->user()->unsetRelation('unreadNotifications');
            }
        } catch (\Exception $e) {
            Log::warning('Failed to mark leave notification as read: '.$e->getMessage());
        }
    }
}
