<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use App\Models\Expense;

class ExpenseStatusNotification extends Notification
{
    use Queueable;

    protected $expense;
    protected $type; // 'request' or 'status_change'

    public function __construct(Expense $expense, $type = 'status_change')
    {
        $this->expense = $expense;
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
                'type' => 'expense_request',
                'title' => 'New Expense Request',
                'message' => "A new expense of ETB " . number_format($this->expense->amount, 2) . " for project {$this->expense->project->name} requires your approval.",
                'expense_id' => $this->expense->id,
                'url' => route('admin.requests.finance'),
                'icon' => 'bi-receipt',
                'color' => 'primary',
                'priority' => $this->expense->amount > 5000 ? 'high' : 'medium'
            ];
        }

        $badge = $this->expense->status === 'approved' ? 'success' : 'danger';
        $statusText = strtoupper($this->expense->status);

        return [
            'type' => 'expense_status',
            'title' => "Expense {$statusText}",
            'message' => "Your expense request of ETB " . number_format($this->expense->amount, 2) . " has been {$this->expense->status}.",
            'expense_id' => $this->expense->id,
            'url' => route('finance.expenses.index'),
            'icon' => $this->expense->status === 'approved' ? 'bi-check-circle' : 'bi-x-circle',
            'color' => $badge,
            'priority' => 'medium'
        ];
    }
}
