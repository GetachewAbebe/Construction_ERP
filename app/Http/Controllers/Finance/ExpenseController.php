<?php

declare(strict_types=1);

namespace App\Http\Controllers\Finance;

use App\Http\Controllers\Controller;
use App\Models\Expense;
use App\Models\Project;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ExpenseController extends Controller
{
    private const EXPENSEABLE_PROJECT_STATUSES = ['active', 'operational', 'In Progress'];

    /**
     * Display a listing of field expenditures.
     */
    public function index(Request $request): View
    {
        $query = Expense::with('project');

        if ($request->filled('q')) {
            $search = $request->input('q');
            $query->where('category', 'like', "%{$search}%")
                ->orWhere('description', 'like', "%{$search}%");
        }

        $expenses = $query->latest('expense_date')->paginate(15);

        return view('finance.expenses.index', compact('expenses'));
    }

    /**
     * Show the form for creating a new expenditure logic.
     */
    public function create(): View
    {
        $projects = Project::whereIn('status', self::EXPENSEABLE_PROJECT_STATUSES)
            ->orderBy('name')
            ->get();

        return view('finance.expenses.create', compact('projects'));
    }

    /**
     * Store a newly created expenditure in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'project_id' => 'required|exists:projects,id',
            'category' => 'required|string|max:255',
            'amount' => 'required|numeric|min:0.01',
            'description' => 'nullable|string',
            'expense_date' => 'required|date',
        ]);

        /** @var \App\Models\Project $project */
        $project = Project::findOrFail($validated['project_id']);

        // Remaining budget = total budget minus everything already recorded on this project.
        $alreadySpent = (float) $project->expenses()->sum('amount');
        $remaining = (float) $project->budget - $alreadySpent;

        if ((float) $validated['amount'] > $remaining) {
            return back()->withInput()->withErrors([
                'amount' => 'Amount exceeds the remaining budget for this project (ETB '.number_format($remaining, 2).' left).',
            ]);
        }

        $validated['user_id'] = auth()->id();
        $expense = $project->expenses()->create($validated);

        // Notify Financial Managers
        try {
            $managers = User::role(['Financial Manager', 'FinancialManager'])->get();
            if ($managers->isEmpty()) {
                $managers = User::role('Administrator')->get();
            }
            if ($managers->isEmpty()) {
                $managers = User::where('id', 1)->get();
            }

            if ($managers->isNotEmpty()) {
                \Illuminate\Support\Facades\Notification::send($managers, new \App\Notifications\ExpenseStatusNotification($expense, 'request'));
                foreach ($managers as $manager) {
                    \Illuminate\Support\Facades\Mail::to($manager->email)->send(new \App\Mail\NewExpenseRequestMail($expense, auth()->user()));
                }
            }
        } catch (\Exception $e) {
            \Log::warning('New expense notification dispatch failed: '.$e->getMessage());
        }

        return redirect()->route('finance.expenses.index')
            ->with('success', 'Field transaction log verified and recorded successfully.');
    }

    /**
     * Display the expenditure voucher.
     */
    public function show(Expense $expense): View
    {
        $expense->load(['project', 'user', 'approvedBy']);

        return view('finance.expenses.show', compact('expense'));
    }

    /**
     * Show the form for editing the specified expenditure asset.
     */
    public function edit(Expense $expense): View
    {
        $projects = Project::whereIn('status', self::EXPENSEABLE_PROJECT_STATUSES)
            ->orderBy('name')
            ->get();

        return view('finance.expenses.edit', compact('expense', 'projects'));
    }

    /**
     * Update the specified expenditure asset in storage.
     */
    public function update(Request $request, Expense $expense): RedirectResponse
    {
        $validated = $request->validate([
            'project_id' => 'required|exists:projects,id',
            'category' => 'required|string|max:255',
            'amount' => 'required|numeric|min:0.01',
            'description' => 'nullable|string',
            'expense_date' => 'required|date',
        ]);

        /** @var \App\Models\Project $project */
        $project = Project::findOrFail($validated['project_id']);

        // Remaining budget excludes the expense being edited (so its old amount doesn't double-count).
        $alreadySpent = (float) $project->expenses()->where('id', '!=', $expense->id)->sum('amount');
        $remaining = (float) $project->budget - $alreadySpent;

        if ((float) $validated['amount'] > $remaining) {
            return back()->withInput()->withErrors([
                'amount' => 'Amount exceeds the remaining budget for this project (ETB '.number_format($remaining, 2).' left).',
            ]);
        }

        $expense->update($validated);

        return redirect()->route('finance.expenses.index')
            ->with('success', 'Field expenditure parameters redefined successfully.');
    }

    /**
     * Remove the specified expenditure asset from database memory tracking.
     */
    public function destroy(Expense $expense): RedirectResponse
    {
        // Line 144: Tracing relationship down via explicit validation hints
        /** @var \App\Models\Project $project */
        $project = $expense->project;

        $expense->delete();

        return redirect()->route('finance.expenses.index')
            ->with('success', 'Transaction entry removed. Capital recovered to portfolio allocation ledger.');
    }
}
