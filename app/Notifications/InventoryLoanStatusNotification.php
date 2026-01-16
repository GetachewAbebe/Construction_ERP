<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use App\Models\InventoryLoan;

class InventoryLoanStatusNotification extends Notification
{
    use Queueable;

    protected $loan;
    protected $type;

    public function __construct(InventoryLoan $loan, $type = 'status_change')
    {
        $this->loan = $loan;
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
                'type' => 'inventory_request',
                'title' => 'New Item Request',
                'message' => "{$this->loan->employee->user->name} requested {$this->loan->item->name}.",
                'loan_id' => $this->loan->id,
                'url' => route('admin.requests.items'),
                'icon' => 'bi-box-seam',
                'color' => 'primary',
                'priority' => 'medium'
            ];
        }

        if ($this->type === 'status_update') {
            $name = $this->loan->employee->user->name ?? $this->loan->employee->name ?? 'An employee';
            
            return [
                'type' => 'inventory_status',
                'title' => "Inventory Request " . ucfirst($this->loan->status),
                'message' => "{$name}'s request for {$this->loan->item->name} has been {$this->loan->status}.",
                'loan_id' => $this->loan->id,
                'url' => route('inventory.loans.index'),
                'icon' => $this->loan->status === 'approved' ? 'bi-patch-check' : 'bi-patch-exclamation',
                'color' => $this->loan->status === 'approved' ? 'success' : 'danger',
                'priority' => 'medium'
            ];
        }

        $color = $this->loan->status === 'approved' ? 'success' : 'danger';
        return [
            'type' => 'inventory_status',
            'title' => "Inventory Request " . ucfirst($this->loan->status),
            'message' => "Your request for {$this->loan->item->name} has been {$this->loan->status}.",
            'loan_id' => $this->loan->id,
            'url' => route('inventory.loans.index'),
            'icon' => $this->loan->status === 'approved' ? 'bi-patch-check' : 'bi-patch-exclamation',
            'color' => $color,
            'priority' => 'medium'
        ];
    }
}
