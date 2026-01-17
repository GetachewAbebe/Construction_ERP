<?php

namespace App\Http\Controllers\Finance;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Expense;
use App\Models\Project;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;
use App\Models\User;
use App\Notifications\ExpenseStatusNotification;

class ExpenseController extends Controller
{
    public function index(Request $request)
    {
        $category = $request->input('category');
        $projectId = $request->input('project_id');
        $status = $request->input('status');
        $search = $request->input('q');

        $expenses = Expense::query()
            ->when($category, fn($q) => $q->where('category', $category))
            ->when($projectId, fn($q) => $q->where('project_id', $projectId))
            ->when($status, fn($q) => $q->where('status', $status))
            ->when($search, function($q) use ($search) {
                $q->where('description', 'like', "%{$search}%")
                  ->orWhere('reference_no', 'like', "%{$search}%")
                  ->orWhereHas('user', fn($sq) => $sq->where('first_name', 'like', "%{$search}%")->orWhere('last_name', 'like', "%{$search}%"));
            })
            ->with(['project', 'user'])
            ->latest('expense_date')
            ->paginate(15);

        $projects = Project::orderBy('name')->get();

        return view('finance.expenses.index', compact('expenses', 'projects'));
    }

    public function create()
    {
        $projects = Project::orderBy('name')->get();
        return view('finance.expenses.create', compact('projects'));
    }

    public function store(\App\Http\Requests\Finance\StoreExpenseRequest $request)
    {
        $data = $request->validated();
        $data['user_id'] = auth()->id();
        $data['status'] = 'pending'; // Default status on creation

        try {
            $expense = Expense::create($data);
            
            // Notify Administrators (Support both naming variants)
            $admins = User::role(['Administrator', 'Admin'])->get();
            try {
                Notification::send($admins, new ExpenseStatusNotification($expense, 'request'));
            } catch (\Exception $e) {
                Log::warning('Expense notification failed: ' . $e->getMessage());
            }

            return redirect()->route('finance.expenses.index')
                ->with('success', 'Financial requisition has been successfully logged and queued for approval.');

        } catch (\Exception $e) {
            Log::error('Expense recording failed: ' . $e->getMessage());
            return back()->withInput()->with('error', 'Critical Error: Failed to record expense transaction.');
        }
    }

    public function show(Expense $expense)
    {
        return view('finance.expenses.show', compact('expense'));
    }

    public function edit(Expense $expense)
    {
        $projects = Project::orderBy('name')->get();
        return view('finance.expenses.edit', compact('expense', 'projects'));
    }

    public function update(\App\Http\Requests\Finance\UpdateExpenseRequest $request, Expense $expense)
    {
        $data = $request->validated();

        try {
            $expense->update($data);
            return redirect()->route('finance.expenses.index')
                ->with('success', 'Expense transaction details have been successfully modified.');
        } catch (\Exception $e) {
            Log::error('Expense update failed: ' . $e->getMessage());
            return back()->withInput()->with('error', 'Critical Error: Failed to update expense record.');
        }
    }

    public function approve(Expense $expense)
    {
        try {
            $expense->update([
                'status' => 'approved',
                'approved_by' => auth()->id(),
                'rejected_by' => null, // Reset if previously rejected
                'rejection_reason' => null
            ]);
            
            // Notify Requester
            if ($expense->user) {
                try {
                    $expense->user->notify(new ExpenseStatusNotification($expense, 'approved'));
                } catch (\Exception $e) {
                    Log::warning('Notification failed: ' . $e->getMessage());
                }
            }

            return back()->with('success', "Expense #{$expense->id} has been formally authorized.");
        } catch (\Exception $e) {
            return back()->with('error', 'Authorization failed: ' . $e->getMessage());
        }
    }

    public function reject(Expense $expense)
    {
        try {
            $expense->update([
                'status' => 'rejected',
                'rejected_by' => auth()->id(),
                'approved_by' => null,
                'rejection_reason' => request('reason') // Optional, if I add a modal later
            ]);
            
            // Notify Requester
            if ($expense->user) {
                try {
                    $expense->user->notify(new ExpenseStatusNotification($expense, 'rejected'));
                } catch (\Exception $e) {
                    Log::warning('Notification failed: ' . $e->getMessage());
                }
            }

            return back()->with('success', "Expense #{$expense->id} has been declined.");
        } catch (\Exception $e) {
            return back()->with('error', 'Rejection failed: ' . $e->getMessage());
        }
    }

    public function destroy(Expense $expense)
    {
        try {
            $expense->delete();
            return redirect()->route('finance.expenses.index')
                ->with('success', 'Expense record has been permanently expunged from the ledger.');
        } catch (\Exception $e) {
            Log::error('Expense deletion failed: ' . $e->getMessage());
            return back()->with('error', 'Critical Error: Failed to delete expense record.');
        }
    }

    public function report(Expense $expense)
    {
        return view('finance.expenses.report', compact('expense'));
    }
}

