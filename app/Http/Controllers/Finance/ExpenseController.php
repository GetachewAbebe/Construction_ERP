<?php

namespace App\Http\Controllers\Finance;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Expense;
use App\Models\Project;
use Illuminate\Support\Facades\Log;

class ExpenseController extends Controller
{
    public function index(Request $request)
    {
        $category = $request->input('category');
        $projectId = $request->input('project_id');

        $expenses = Expense::query()
            ->when($category, fn($q) => $q->where('category', $category))
            ->when($projectId, fn($q) => $q->where('project_id', $projectId))
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

    public function store(Request $request)
    {
        $data = $request->validate([
            'project_id'   => 'required|exists:projects,id',
            'amount'       => 'required|numeric|min:0.01',
            'category'     => 'required|string|max:255',
            'description'  => 'nullable|string',
            'expense_date' => 'required|date',
            'reference_no' => 'nullable|string|max:255',
        ]);

        try {
            $data['user_id'] = auth()->id();
            Expense::create($data);
            return redirect()->route('finance.expenses.index')->with('status', 'Expense recorded successfully.');
        } catch (\Exception $e) {
            Log::error('Expense recording failed: ' . $e->getMessage());
            return back()->withInput()->with('error', 'Failed to record expense.');
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

    public function update(Request $request, Expense $expense)
    {
        $data = $request->validate([
            'project_id'   => 'required|exists:projects,id',
            'amount'       => 'required|numeric|min:0.01',
            'category'     => 'required|string|max:255',
            'description'  => 'nullable|string',
            'expense_date' => 'required|date',
            'reference_no' => 'nullable|string|max:255',
        ]);

        try {
            $expense->update($data);
            return redirect()->route('finance.expenses.index')->with('status', 'Expense updated successfully.');
        } catch (\Exception $e) {
            Log::error('Expense update failed: ' . $e->getMessage());
            return back()->withInput()->with('error', 'Failed to update expense.');
        }
    }

    public function destroy(Expense $expense)
    {
        try {
            $expense->delete();
            return redirect()->route('finance.expenses.index')->with('status', 'Expense deleted successfully.');
        } catch (\Exception $e) {
            Log::error('Expense deletion failed: ' . $e->getMessage());
            return back()->with('error', 'Failed to delete expense.');
        }
    }
}

