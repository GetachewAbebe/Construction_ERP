<?php

declare(strict_types=1);

namespace App\Http\Controllers\Finance;

use App\Enums\ExpenseStatus;
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
    public function index(Request $request)
    {
        $query = Expense::with('project');

        if ($request->filled('q')) {
            $search = $request->input('q');
            $query->where('category', 'like', "%{$search}%")
                ->orWhere('description', 'like', "%{$search}%");
        }

        $expenses = $query->latest('expense_date')->paginate(15);

        return \Inertia\Inertia::render('Finance/Expenses/Index', [
            'expenses' => $expenses,
            'q' => $request->input('q') ?? '',
        ]);
    }

    /**
     * Show the form for creating a new expenditure logic.
     */
    public function create()
    {
        $projects = Project::whereIn('status', self::EXPENSEABLE_PROJECT_STATUSES)
            ->orderBy('name')
            ->get();

        return \Inertia\Inertia::render('Finance/Expenses/Create', compact('projects'));
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
            'reference_no' => 'nullable|string|max:255',
            'attachment' => 'nullable|file|mimes:pdf,jpg,jpeg,png,webp|max:10240',
        ]);

        /** @var \App\Models\Project $project */
        $project = Project::findOrFail($validated['project_id']);

        // Remaining budget = total budget minus active and approved expenses (excluding rejected).
        $alreadySpent = (float) $project->expenses()
            ->whereIn('status', [ExpenseStatus::Pending->value, ExpenseStatus::Approved->value])
            ->sum('amount');
        $remaining = (float) $project->budget - $alreadySpent;

        if ((float) $validated['amount'] > $remaining) {
            return back()->withInput()->withErrors([
                'amount' => 'Amount exceeds the remaining budget for this project (ETB '.number_format($remaining, 2).' left).',
            ]);
        }

        if ($request->hasFile('attachment')) {
            $path = $request->file('attachment')->store('expenses', 'public');
            $validated['attachment_path'] = $path;
        }
        unset($validated['attachment']);

        $validated['user_id'] = auth()->id();
        $validated['status'] = ExpenseStatus::Pending->value;
        $expense = $project->expenses()->create($validated);

        // Notify Financial Managers
        try {
            $managers = User::role('FinancialManager')->get();
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
    public function show(Expense $expense)
    {
        $expense->load(['project', 'user', 'approvedBy']);

        return \Inertia\Inertia::render('Finance/Expenses/Show', compact('expense'));
    }

    /**
     * Show the form for editing the specified expenditure asset.
     */
    public function edit(Expense $expense)
    {
        if ($expense->status !== ExpenseStatus::Pending->value) {
            return redirect()->route('finance.expenses.index')
                ->with('error', 'Processed or approved expenses cannot be edited.');
        }

        $projects = Project::whereIn('status', self::EXPENSEABLE_PROJECT_STATUSES)
            ->orderBy('name')
            ->get();

        return \Inertia\Inertia::render('Finance/Expenses/Edit', compact('expense', 'projects'));
    }

    /**
     * Update the specified expenditure asset in storage.
     */
    public function update(Request $request, Expense $expense): RedirectResponse
    {
        if ($expense->status !== ExpenseStatus::Pending->value) {
            return redirect()->route('finance.expenses.index')
                ->with('error', 'Processed or approved expenses cannot be edited.');
        }

        $validated = $request->validate([
            'project_id' => 'required|exists:projects,id',
            'category' => 'required|string|max:255',
            'amount' => 'required|numeric|min:0.01',
            'description' => 'nullable|string',
            'expense_date' => 'required|date',
            'reference_no' => 'nullable|string|max:255',
            'attachment' => 'nullable|file|mimes:pdf,jpg,jpeg,png,webp|max:10240',
        ]);

        /** @var \App\Models\Project $project */
        $project = Project::findOrFail($validated['project_id']);

        // Remaining budget excludes the expense being edited and excludes rejected expenses.
        $alreadySpent = (float) $project->expenses()
            ->where('id', '!=', $expense->id)
            ->whereIn('status', [ExpenseStatus::Pending->value, ExpenseStatus::Approved->value])
            ->sum('amount');
        $remaining = (float) $project->budget - $alreadySpent;

        if ((float) $validated['amount'] > $remaining) {
            return back()->withInput()->withErrors([
                'amount' => 'Amount exceeds the remaining budget for this project (ETB '.number_format($remaining, 2).' left).',
            ]);
        }

        if ($request->hasFile('attachment')) {
            if ($expense->attachment_path && \Illuminate\Support\Facades\Storage::disk('public')->exists($expense->attachment_path)) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($expense->attachment_path);
            }
            $validated['attachment_path'] = $request->file('attachment')->store('expenses', 'public');
        }
        unset($validated['attachment']);

        $expense->update($validated);

        return redirect()->route('finance.expenses.index')
            ->with('success', 'Field expenditure parameters redefined successfully.');
    }

    /**
     * Remove the specified expenditure asset from database memory tracking.
     */
    public function destroy(Expense $expense): RedirectResponse
    {
        if ($expense->status !== ExpenseStatus::Pending->value) {
            return redirect()->route('finance.expenses.index')
                ->with('error', 'Processed or approved expenses cannot be deleted.');
        }

        $expense->delete();

        return redirect()->route('finance.expenses.index')
            ->with('success', 'Transaction entry removed. Capital recovered to portfolio allocation ledger.');
    }

    /**
     * Stream out full expenditure transactions as a CSV report file.
     */
    public function exportCsv(Request $request)
    {
        $expenses = Expense::with(['project', 'user'])->orderByDesc('expense_date')->get();

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="expenses_export_'.date('Y-m-d_His').'.csv"',
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0',
        ];

        $callback = function () use ($expenses) {
            $file = fopen('php://output', 'w');
            fputcsv($file, ['ID', 'Reference No', 'Project', 'Category', 'Amount (ETB)', 'Date', 'Status', 'Logged By', 'Receipt Attached', 'Description']);

            foreach ($expenses as $expense) {
                fputcsv($file, [
                    $expense->id,
                    $expense->reference_no ?? 'N/A',
                    $expense->project?->name ?? 'N/A',
                    $expense->category,
                    number_format((float) $expense->amount, 2, '.', ''),
                    $expense->expense_date?->format('Y-m-d') ?? '',
                    strtoupper($expense->status),
                    $expense->user?->name ?? 'N/A',
                    $expense->attachment_path ? 'Yes' : 'No',
                    $expense->description ?? '',
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
