<?php

declare(strict_types=1);

namespace App\Listeners;

use App\Events\ExpenseApproved;
use App\Events\ExpenseRejected;
use App\Mail\ExpenseRequestStatusMail;
use App\Models\User;
use App\Notifications\ExpenseStatusNotification;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Notification;

class SendExpenseApprovalNotification
{
    public function handle(ExpenseApproved|ExpenseRejected $event): void
    {
        $expense = $event->expense;

        try {
            if ($expense->user) {
                $expense->user->notify(new ExpenseStatusNotification($expense, 'status_change'));
                Mail::to($expense->user->email)
                    ->send(new ExpenseRequestStatusMail($expense, $expense->user));
            }
        } catch (\Exception $e) {
            Log::warning('Expense requester notification failed: '.$e->getMessage());
        }

        try {
            $financialManagers = User::role('FinancialManager')->get();
            if ($financialManagers->isNotEmpty()) {
                Notification::send($financialManagers, new ExpenseStatusNotification($expense, 'status_update'));
            }
        } catch (\Exception $e) {
            Log::warning('Expense manager notification failed: '.$e->getMessage());
        }
    }
}
