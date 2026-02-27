<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Mail\LeaveRequestStatusMail;
use App\Models\EmployeeOnLeave;
use App\Models\LeaveRequest;
use App\Notifications\LeaveRequestStatusNotification;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

class LeaveApprovalController extends Controller
{
    public function index()
    {
        $pending = LeaveRequest::with('employee')
            ->where('status', 'Pending')
            ->orderByDesc('id')
            ->paginate(20);

        $approved = EmployeeOnLeave::with('employee', 'approver')
            ->orderByDesc('approved_at')
            ->paginate(20);

        return view('admin.requests.leave-approvals', compact('pending', 'approved'));
    }

    public function approve(LeaveRequest $leave)
    {
        if ($leave->status !== 'Pending') {
            return back()->with('error', 'This request is already processed.');
        }

        DB::transaction(function () use ($leave) {
            $leave->update([
                'status' => 'Approved',
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

        if ($leave->employee && $leave->employee->user) {
            $leave->employee->user->notify(new LeaveRequestStatusNotification($leave, 'status_change'));
            try {
                Mail::to($leave->employee->user->email)->send(new LeaveRequestStatusMail($leave, $leave->employee->user));
            } catch (\Exception $e) {
                \Log::error('Mail failed: '.$e->getMessage());
            }
        }

        // Notify Human Resource Managers
        $hrManagers = \App\Models\User::role('HumanResourceManager')->get();
        if ($hrManagers->isNotEmpty()) {
            \Illuminate\Support\Facades\Notification::send($hrManagers, new LeaveRequestStatusNotification($leave, 'status_update'));
        }

        // Mark related notification as read for the admin
        auth()->user()->unreadNotifications()
            ->where('data->leave_id', $leave->id)
            ->get()
            ->markAsRead();

        return back()->with('success', 'Leave approved and recorded.');
    }

    public function reject(LeaveRequest $leave)
    {
        if ($leave->status !== 'Pending') {
            return back()->with('error', 'This request is already processed.');
        }

        $leave->update([
            'status' => 'Rejected',
            'approved_by' => auth()->id(),
            'approved_at' => now(),
        ]);

        if ($leave->employee && $leave->employee->user) {
            $leave->employee->user->notify(new LeaveRequestStatusNotification($leave, 'status_change'));
            try {
                Mail::to($leave->employee->user->email)->send(new LeaveRequestStatusMail($leave, $leave->employee->user));
            } catch (\Exception $e) {
                \Log::error('Mail failed: '.$e->getMessage());
            }
        }

        // Notify Human Resource Managers
        $hrManagers = \App\Models\User::role('HumanResourceManager')->get();
        if ($hrManagers->isNotEmpty()) {
            \Illuminate\Support\Facades\Notification::send($hrManagers, new LeaveRequestStatusNotification($leave, 'status_update'));
        }

        // Mark related notification as read for the admin
        auth()->user()->unreadNotifications()
            ->where('data->leave_id', $leave->id)
            ->get()
            ->markAsRead();

        return back()->with('success', 'Leave rejected.');
    }
}
