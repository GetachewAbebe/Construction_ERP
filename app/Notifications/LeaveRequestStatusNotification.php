<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use App\Models\LeaveRequest;

class LeaveRequestStatusNotification extends Notification
{
    use Queueable;

    protected $leaveRequest;
    protected $type;

    public function __construct(LeaveRequest $leaveRequest, $type = 'status_change')
    {
        $this->leaveRequest = $leaveRequest;
        $this->type = $type;
    }

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        if ($this->type === 'request') {
            return [
                'type' => 'leave_request',
                'title' => 'New Leave Request',
                'message' => "{$this->leaveRequest->employee->user->name} has requested leave from {$this->leaveRequest->start_date->format('M d')} to {$this->leaveRequest->end_date->format('M d')}.",
                'leave_id' => $this->leaveRequest->id,
                'url' => route('admin.requests.leave-approvals.index'),
                'icon' => 'bi-calendar-event',
                'color' => 'primary',
                'priority' => $this->leaveRequest->start_date->diffInDays($this->leaveRequest->end_date) > 5 ? 'high' : 'medium'
            ];
        }

        $color = $this->leaveRequest->status === 'Approved' ? 'success' : 'danger';
        return [
            'type' => 'leave_status',
            'title' => "Leave Request {$this->leaveRequest->status}",
            'message' => "Your leave request for {$this->leaveRequest->start_date->format('M d')} has been {$this->leaveRequest->status}.",
            'leave_id' => $this->leaveRequest->id,
            'url' => route('hr.leaves.index'),
            'icon' => $this->leaveRequest->status === 'Approved' ? 'bi-calendar-check' : 'bi-calendar-x',
            'color' => $color,
            'priority' => 'medium'
        ];
    }
}
