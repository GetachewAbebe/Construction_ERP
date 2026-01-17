<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Expense;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Notification;
use App\Notifications\ExpenseStatusNotification;

class ExpenseApprovalController extends Controller
{
    /**
     * List pending expenses for approval.
     */
    public function index()
    {
        $pendingExpenses = Expense::with(['project', 'user'])
            ->where('status', 'pending')
            ->latest()
            ->paginate(15);

        return view('admin.requests.finance', compact('pendingExpenses'));
    }

    /**
     * Approve an expense.
     */
    public function approve(Expense $expense)
    {
        try {
            $expense->update([
                'status' => 'approved',
                'approved_by' => Auth::id(),
            ]);

            // Notify Requester (wrapped in individual try-catch to not block approval if notification fails)
            try {
                $expense->user->notify(new ExpenseStatusNotification($expense, 'status_change'));
            } catch (\Exception $e) {
                \Log::warning('Requester notification failed: ' . $e->getMessage());
            }

            // Notify Financial Managers
            try {
                $financialManagers = \App\Models\User::role(['Financial Manager', 'FinancialManager'])->get();
                if ($financialManagers->isNotEmpty()) {
                    Notification::send($financialManagers, new ExpenseStatusNotification($expense, 'status_update'));
                }
            } catch (\Exception $e) {
                \Log::warning('Financial Manager notification failed: ' . $e->getMessage());
            }

            // Clear notification for admin
            try {
                Auth::user()->unreadNotifications()
                    ->where('data->expense_id', $expense->id)
                    ->get()
                    ->markAsRead();
            } catch (\Exception $e) {}

            return back()->with('status', 'Expense approved successfully.');
        } catch (\Exception $e) {
            return back()->with('error', 'Approval failed. This is likely due to missing database columns. Please run "Sync Database Schema" in the Maintenance page. Error: ' . $e->getMessage());
        }
    }

    /**
     * Reject an expense.
     */
    public function reject(Request $request, Expense $expense)
    {
        try {
            $request->validate([
                'rejection_reason' => 'required|string|max:500',
            ]);

            $expense->update([
                'status' => 'rejected',
                'rejected_by' => Auth::id(),
                'rejection_reason' => $request->rejection_reason,
            ]);

            try {
                $expense->user->notify(new ExpenseStatusNotification($expense, 'status_change'));
            } catch (\Exception $e) {}

            // Notify Financial Managers
            try {
                $financeManagers = \App\Models\User::role(['Financial Manager', 'FinancialManager'])->get();
                if ($financeManagers->isNotEmpty()) {
                    \Illuminate\Support\Facades\Notification::send($financeManagers, new ExpenseStatusNotification($expense, 'status_update'));
                }
            } catch (\Exception $e) {}

            // Clear notification for admin
            try {
                Auth::user()->unreadNotifications()
                    ->where('data->expense_id', $expense->id)
                    ->get()
                    ->markAsRead();
            } catch (\Exception $e) {}

            return back()->with('status', 'Expense rejected.');
        } catch (\Exception $e) {
            return back()->with('error', 'Rejection failed: ' . $e->getMessage());
        }
    }
}
