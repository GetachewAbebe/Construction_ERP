<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

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

class ExpenseApprovalController extends Controller
{
    public function index()
    {
        $pendingExpenses = Expense::with(['project', 'user'])
            ->where('status', 'pending')
            ->latest()
            ->paginate(15);

        return view('admin.requests.finance', compact('pendingExpenses'));
    }

    public function approve(Expense $expense)
    {
        $expense->update([
            'status' => 'approved',
            'approved_by' => Auth::id(),
        ]);

        $this->notifyParties($expense);

        return back()->with('status', 'Requisition authorized successfully.');
    }

    public function reject(Request $request, Expense $expense)
    {
        $request->validate([
            'rejection_reason' => 'required|string|max:500',
        ]);

        $expense->update([
            'status' => 'rejected',
            'rejected_by' => Auth::id(),
            'rejection_reason' => $request->rejection_reason,
        ]);

        $this->notifyParties($expense);

        return back()->with('status', 'Requisition declined.');
    }

    private function notifyParties(Expense $expense): void
    {
        try {
            $expense->user->notify(new ExpenseStatusNotification($expense, 'status_change'));
            Mail::to($expense->user->email)
                ->send(new ExpenseRequestStatusMail($expense, $expense->user));
        } catch (\Exception $e) {
            Log::warning('Expense requester notification failed: '.$e->getMessage());
        }

        try {
            $financialManagers = User::role(['Financial Manager', 'FinancialManager'])->get();
            if ($financialManagers->isNotEmpty()) {
                Notification::send($financialManagers, new ExpenseStatusNotification($expense, 'status_update'));
            }
        } catch (\Exception $e) {
            Log::warning('Expense manager notification failed: '.$e->getMessage());
        }

        try {
            Auth::user()->unreadNotifications()
                ->where('data->expense_id', $expense->id)
                ->get()
                ->markAsRead();
        } catch (\Exception $e) {
            Log::warning('Failed to mark expense notifications as read: '.$e->getMessage());
        }
    }
}
