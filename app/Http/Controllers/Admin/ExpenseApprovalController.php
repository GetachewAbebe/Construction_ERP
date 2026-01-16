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
        $expense->update([
            'status' => 'approved',
            'approved_by' => Auth::id(),
        ]);

        $expense->user->notify(new ExpenseStatusNotification($expense, 'status_change'));

        // Notify Financial Managers
        $financialManagers = \App\Models\User::role('FinancialManager')->get();
        if ($financialManagers->isNotEmpty()) {
            Notification::send($financialManagers, new ExpenseStatusNotification($expense, 'status_update'));
        }

        // Notify Inventory Managers (if applicable for expense context, though FinancialManager is more common)
        $inventoryManagers = \App\Models\User::role('InventoryManager')->get();
        if ($inventoryManagers->isNotEmpty()) {
            Notification::send($inventoryManagers, new ExpenseStatusNotification($expense, 'status_update'));
        }

        // Clear notification for admin
        Auth::user()->unreadNotifications()
            ->where('data->expense_id', $expense->id)
            ->get()
            ->markAsRead();

        return back()->with('status', 'Expense approved successfully.');
    }

    /**
     * Reject an expense.
     */
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

        $expense->user->notify(new ExpenseStatusNotification($expense, 'status_change'));

        // Notify Financial Managers
        $financeManagers = \App\Models\User::role('FinancialManager')->get();
        if ($financeManagers->isNotEmpty()) {
            \Illuminate\Support\Facades\Notification::send($financeManagers, new ExpenseStatusNotification($expense, 'status_update'));
        }

        // Clear notification for admin
        Auth::user()->unreadNotifications()
            ->where('data->expense_id', $expense->id)
            ->get()
            ->markAsRead();

        return back()->with('status', 'Expense rejected.');
    }
}
