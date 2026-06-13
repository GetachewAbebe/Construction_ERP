<?php

declare(strict_types=1);

namespace App\Http\Controllers\Finance;

use App\Http\Controllers\Controller;
use App\Mail\ExpenseRequestStatusMail;
use App\Mail\NewExpenseRequestMail;
use App\Models\Expense;
use App\Models\Project;
use App\Models\User;
use App\Notifications\ExpenseStatusNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Notification;

class ExpenseController extends Controller
{
    public function index(Request $request)
    {
        $category = $request->input('category');
        $projectId = $request->input('project_id');
        $status = $request->input('status');
        $search = $request->input('q');

        $expenses = Expense::query()
            ->when($category, fn ($q) => $q->where('category', $category))
            ->when($projectId, fn ($q) => $q->where('project_id', $projectId))
            ->when($status, fn ($q) => $q->where('status', $status))
            ->when($search, function ($q) use ($search) {
                // High-performance search optimized by joining instead of running slow subqueries
                $q->leftJoin('users', 'expenses.user_id', '=', 'users.id')
                  ->select('expenses.*') 
                  ->where(function ($sub) use ($search) {
                      $sub->where('expenses.description', 'like', "%{$search}%")
                          ->orWhere('expenses.reference_no', 'like', "%{$search}%")
                          ->orWhere('users.first_name', 'like', "%{$search}%")
                          ->orWhere('users.last_name', 'like', "%{$search}%");
                  });
            })
            ->with(['project', 'user'])
            ->latest('expense_date')
            ->paginate(15);

        // Optimization: Select only 'id' and 'name' to keep memory footprint incredibly light
        $projects = Project::orderBy('name')->get(['id', 'name']);

        return view('finance.expenses.index', compact('expenses', 'projects'));
    }

    public function create()
    {
        $projects = Project::orderBy('name')->get(['id', 'name']);
        return view('finance.expenses.create', compact('projects'));
    }

    public function store(\App\Http\Requests\Finance\StoreExpenseRequest $request)
    {
        $data = $request->validated();
        $data['user_id'] = auth()->id();
        $data['status'] = 'pending';

        try {
            // Enterprise Budget Guard on Creation
            $project = Project::findOrFail($data['project_id']);
            $approvedSpending = $project->expenses()->where('status', 'approved')->sum('amount');
            
            if (($approvedSpending + $data['amount']) > $project->budget) {
                return back()->withInput()->with('error', 'Requisition Rejected: The requested amount exceeds Natanem Engineering’s remaining budget limits for this project.');
            }

            $expense = Expense::create($data);

            $admins = User::role(['Administrator', 'Admin'])->get();
            try {
                Notification::send($admins, new ExpenseStatusNotification($expense, 'request'));
                foreach ($admins as $admin) {
                    Mail::to($admin->email)->send(new NewExpenseRequestMail($expense, auth()->user()));
                }
            } catch (\Exception $e) {
                Log::warning('Expense notification failed: '.$e->getMessage());
            }

            return redirect()->route('finance.expenses.index')
                ->with('success', 'Financial requisition has been logged and queued for authorization.');

        } catch (\Exception $e) {
            Log::error('Expense recording failed: '.$e->getMessage());
            return back()->withInput()->with('error', 'Critical Error: Failed to record expense transaction.');
        }
    }

    public function show(Expense $expense)
    {
        return view('finance.expenses.show', compact('expense'));
    }

    public function edit(Expense $expense)
    {
        $projects = Project::orderBy('name')->get(['id', 'name']);
        return view('finance.expenses.edit', compact('expense', 'projects'));
    }

    public function update(\App\Http\Requests\Finance\UpdateExpenseRequest $request, Expense $expense)
    {
        $data = $request->validated();

        try {
            // Guard budget if amount is altered during an edit
            if (isset($data['amount']) || isset($data['project_id'])) {
                $pid = $data['project_id'] ?? $expense->project_id;
                $amt = $data['amount'] ?? $expense->amount;
                
                $project = Project::findOrFail($pid);
                $approvedSpending = $project->expenses()->where('status', 'approved')->where('id', '!=', $expense->id)->sum('amount');

                if (($approvedSpending + $amt) > $project->budget) {
                    return back()->withInput()->with('error', 'Modification Blocked: Adjusted amount breaks project budget constraints.');
                }
            }

            $expense->update($data);
            return redirect()->route('finance.expenses.index')
                ->with('success', 'Requisition details have been successfully updated.');
        } catch (\Exception $e) {
            Log::error('Expense update failed: '.$e->getMessage());
            return back()->withInput()->with('error', 'Critical Error: Failed to update expense record.');
        }
    }

    public function approve(Expense $expense)
    {
        try {
            return DB::transaction(function () use ($expense) {
                // Strict Approval Lockout Check
                $project = $expense->project;
                $approvedSpending = $project->expenses()->where('status', 'approved')->sum('amount');

                if (($approvedSpending + $expense->amount) > $project->budget) {
                    return back()->with('error', 'Authorization Blocked: Requisition cannot be approved because the project budget limit has been reached.');
                }

                $expense->update([
                    'status' => 'approved',
                    'approved_by' => auth()->id(),
                    'rejected_by' => null,
                    'rejection_reason' => null,
                ]);

                if ($expense->user) {
                    try {
                        $expense->user->notify(new ExpenseStatusNotification($expense, 'approved'));
                        Mail::to($expense->user->email)->send(new ExpenseRequestStatusMail($expense, $expense->user));
                    } catch (\Exception $e) {
                        Log::warning('Notification failed: '.$e->getMessage());
                    }
                }

                return back()->with('success', "Requisition #{$expense->id} has been authorized.");
            });
        } catch (\Exception $e) {
            return back()->with('error', 'Authorization failed: '.$e->getMessage());
        }
    }

    public function reject(Expense $expense)
    {
        try {
            $expense->update([
                'status' => 'rejected',
                'rejected_by' => auth()->id(),
                'approved_by' => null,
                'rejection_reason' => request('reason'),
            ]);

            if ($expense->user) {
                try {
                    $expense->user->notify(new ExpenseStatusNotification($expense, 'rejected'));
                    Mail::to($expense->user->email)->send(new ExpenseRequestStatusMail($expense, $expense->user));
                } catch (\Exception $e) {
                    Log::warning('Notification failed: '.$e->getMessage());
                }
            }

            return back()->with('success', "Requisition #{$expense->id} has been declined.");
        } catch (\Exception $e) {
            return back()->with('error', 'Rejection failed: '.$e->getMessage());
        }
    }

    public function destroy(Expense $expense)
    {
        try {
            $expense->delete();
            return redirect()->route('finance.expenses.index')
                ->with('success', 'Requisition record has been removed.');
        } catch (\Exception $e) {
            Log::error('Expense deletion failed: '.$e->getMessage());
            return back()->with('error', 'Critical Error: Failed to delete expense record.');
        }
    }
}