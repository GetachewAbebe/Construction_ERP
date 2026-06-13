<?php

declare(strict_types=1);

namespace App\Http\Controllers\Finance;

use App\Http\Controllers\Controller;
use App\Models\Expense;
use App\Models\Project;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ExpenseController extends Controller
{
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
        $projects = Project::where('status', 'operational')->get();
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

        // Lines 70 & 72: Inline Type declaration blocks clear Ambiguity for PHPStan
        /** @var \App\Models\Project $project */
        $project = Project::findOrFail($validated['project_id']);

        // Explicit float conversion checks to guarantee math operations inside PHPStan strict mode
        if ((float) $project->budget < (float) $validated['amount']) {
            return back()->withInput()->withErrors(['amount' => 'Transaction value exceeds remaining total site allocation metrics.']);
        }

        $project->expenses()->create($validated);

        return redirect()->route('finance.expenses.index')
            ->with('success', 'Field transaction log verified and recorded successfully.');
    }

    /**
     * Show the form for editing the specified expenditure asset.
     */
    public function edit(Expense $expense): View
    {
        $projects = Project::where('status', 'operational')->get();
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

        // Lines 121 & 123: Inline Type declaration blocks
        /** @var \App\Models\Project $project */
        $project = Project::findOrFail($validated['project_id']);

        if ((float) $project->budget < (float) $validated['amount']) {
            return back()->withInput()->withErrors(['amount' => 'Modified transaction value breaches total layout budget limits.']);
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