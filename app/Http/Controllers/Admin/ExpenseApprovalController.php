<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Enums\ExpenseStatus;
use App\Http\Controllers\Controller;
use App\Mail\ExpenseRequestStatusMail;
use App\Models\Expense;
use App\Models\User;
use App\Notifications\ExpenseStatusNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Notification;

use Inertia\Inertia;
use Inertia\Response;

class ExpenseApprovalController extends Controller
{
    public function index(): Response
    {
        // Auto-clear any dangling unread expense request notifications for requests already processed
        try {
            $decidedExpenseIds = Expense::where('status', '!=', ExpenseStatus::Pending->value)->pluck('id');
            if ($decidedExpenseIds->isNotEmpty()) {
                \Illuminate\Notifications\DatabaseNotification::whereNull('read_at')
                    ->where(function ($q) {
                        $q->where('data', 'like', '%"type":"expense_request"%')
                          ->orWhere('data', 'like', '%"type": "expense_request"%');
                    })
                    ->where(function ($q) use ($decidedExpenseIds) {
                        foreach ($decidedExpenseIds as $id) {
                            $q->orWhere('data', 'like', '%"expense_id":'.$id.'%')
                              ->orWhere('data', 'like', '%"expense_id":"'.$id.'"%');
                        }
                    })
                    ->update(['read_at' => now()]);
            }
            if (Auth::check()) {
                Auth::user()->unsetRelation('unreadNotifications');
            }
        } catch (\Exception $e) {
            Log::warning('Failed to auto-clear processed expense notifications: '.$e->getMessage());
        }

        $pendingExpenses = Expense::with(['project', 'user'])
            ->where('status', ExpenseStatus::Pending->value)
            ->latest()
            ->paginate(15)
            ->withQueryString();

        $historyExpenses = Expense::with(['project', 'user'])
            ->where('status', '!=', ExpenseStatus::Pending->value)
            ->latest()
            ->paginate(15)
            ->withQueryString();

        $stats = [
            'pending_count' => Expense::where('status', ExpenseStatus::Pending->value)->count(),
            'pending_amount' => (float) Expense::where('status', ExpenseStatus::Pending->value)->sum('amount'),
            'approved_this_month' => (float) Expense::where('status', ExpenseStatus::Approved->value)
                ->whereMonth('updated_at', now()->month)
                ->sum('amount'),
        ];

        return Inertia::render('Admin/Requests/FinanceApprovals', [
            'pendingExpenses' => $pendingExpenses,
            'historyExpenses' => $historyExpenses,
            'stats' => $stats,
        ]);
    }

    public function approve(Expense $expense)
    {
        $this->markExpenseNotificationAsRead($expense);

        if ($expense->status !== ExpenseStatus::Pending->value) {
            return back()->with('status', 'This expenditure has already been processed.');
        }

        $expense->update([
            'status' => ExpenseStatus::Approved->value,
            'approved_by' => Auth::id(),
        ]);

        $this->notifyParties($expense);

        return back()->with('status', 'Requisition authorized successfully.')->with('success', 'Requisition authorized successfully.');
    }

    public function reject(Request $request, Expense $expense)
    {
        $this->markExpenseNotificationAsRead($expense);

        if ($expense->status !== ExpenseStatus::Pending->value) {
            return back()->with('status', 'This expenditure has already been processed.');
        }

        $request->validate([
            'rejection_reason' => 'required|string|max:500',
        ]);

        $expense->update([
            'status' => ExpenseStatus::Rejected->value,
            'rejected_by' => Auth::id(),
            'rejection_reason' => $request->rejection_reason,
        ]);

        $this->notifyParties($expense);

        return back()->with('status', 'Requisition declined.')->with('success', 'Requisition declined.');
    }

    private function notifyParties(Expense $expense): void
    {
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

        $this->markExpenseNotificationAsRead($expense);
    }

    private function markExpenseNotificationAsRead(Expense $expense): void
    {
        try {
            \Illuminate\Notifications\DatabaseNotification::whereNull('read_at')
                ->where(function ($q) {
                    $q->where('data', 'like', '%"type":"expense_request"%')
                      ->orWhere('data', 'like', '%"type": "expense_request"%');
                })
                ->where(function ($q) use ($expense) {
                    $q->where('data', 'like', '%"expense_id":'.$expense->id.'%')
                      ->orWhere('data', 'like', '%"expense_id":"'.$expense->id.'"%')
                      ->orWhere('data', 'like', '%"expense_id": "'.$expense->id.'"%');
                })
                ->update(['read_at' => now()]);

            if (Auth::check()) {
                Auth::user()->unsetRelation('unreadNotifications');
            }
        } catch (\Exception $e) {
            Log::warning('Failed to mark expense notifications as read: '.$e->getMessage());
        }
    }
}
