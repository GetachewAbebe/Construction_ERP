<?php

declare(strict_types=1);

namespace App\Livewire\Finance;

use App\Enums\ExpenseStatus;
use App\Models\Expense;
use App\Models\Project;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;
use Mary\Traits\Toast;

class ExpensesTable extends Component
{
    use Toast;
    use WithPagination;

    #[Url(as: 'q')]
    public string $search = '';

    #[Url]
    public string $status = '';

    #[Url]
    public ?int $projectId = null;

    // Modal state
    public bool $showCreateModal = false;

    public bool $showVoucherModal = false;

    public ?Expense $selectedExpense = null;

    public ?int $project_id = null;

    public string $category = '';

    public ?float $amount = null;

    public ?string $expense_date = null;

    public ?string $description = null;

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function updatedStatus(): void
    {
        $this->resetPage();
    }

    public function updatedProjectId(): void
    {
        $this->resetPage();
    }

    public function clearFilters(): void
    {
        $this->reset('search', 'status', 'projectId');
        $this->resetPage();
    }

    public function create(): void
    {
        $this->reset([
            'project_id',
            'category',
            'amount',
            'description',
        ]);
        $this->expense_date = now()->format('Y-m-d');
        $this->showCreateModal = true;
    }

    public function viewVoucher(int $id): void
    {
        $this->selectedExpense = Expense::with(['project', 'user', 'approvedBy'])->findOrFail($id);
        $this->showVoucherModal = true;
    }

    /**
     * @return array<string, mixed>
     */
    protected function rules(): array
    {
        return [
            'project_id' => ['required', 'exists:projects,id'],
            'category' => ['required', 'string', 'max:255'],
            'amount' => ['required', 'numeric', 'min:0.01'],
            'expense_date' => ['required', 'date'],
            'description' => ['nullable', 'string', 'max:1000'],
        ];
    }

    public function save(): void
    {
        $validated = $this->validate();

        /** @var \App\Models\Project $project */
        $project = Project::findOrFail($validated['project_id']);

        $alreadySpent = (float) $project->expenses()
            ->whereIn('status', [ExpenseStatus::Pending->value, ExpenseStatus::Approved->value])
            ->sum('amount');
        $remaining = (float) $project->budget - $alreadySpent;

        if ((float) $validated['amount'] > $remaining) {
            $this->error('Budget Exceeded: Project only has ETB '.number_format($remaining, 2).' remaining.');

            return;
        }

        $expense = $project->expenses()->create([
            'user_id' => Auth::id(),
            'category' => $validated['category'],
            'amount' => $validated['amount'],
            'expense_date' => $validated['expense_date'],
            'description' => $validated['description'],
            'status' => ExpenseStatus::Pending->value,
        ]);

        // Dispatch notifications
        try {
            $managers = User::role('FinancialManager')->get();
            if ($managers->isEmpty()) {
                $managers = User::role('Administrator')->get();
            }
            if ($managers->isNotEmpty()) {
                \Illuminate\Support\Facades\Notification::send($managers, new \App\Notifications\ExpenseStatusNotification($expense, 'request'));
            }
        } catch (\Exception $e) {
            \Log::warning('Expense notification failed: '.$e->getMessage());
        }

        $this->success('Field transaction recorded and queued for financial authorization.');
        $this->showCreateModal = false;
    }

    public function render(): View
    {
        $query = Expense::with(['project', 'user']);

        if ($this->search !== '') {
            $q = $this->search;
            $query->where(function ($sub) use ($q) {
                $sub->where('category', 'like', "%{$q}%")
                    ->orWhere('description', 'like', "%{$q}%");
            });
        }

        if ($this->status !== '') {
            $query->where('status', $this->status);
        }

        if ($this->projectId) {
            $query->where('project_id', $this->projectId);
        }

        $expenses = $query->latest('expense_date')->paginate(15);

        $projects = Project::whereIn('status', ['active', 'operational', 'In Progress'])
            ->orderBy('name')
            ->get(['id', 'name', 'budget']);

        $totals = [
            'total_amount' => (float) Expense::where('status', ExpenseStatus::Approved->value)->sum('amount'),
            'pending_count' => Expense::where('status', ExpenseStatus::Pending->value)->count(),
            'total_count' => Expense::count(),
        ];

        return view('livewire.finance.expenses-table', [
            'expenses' => $expenses,
            'projects' => $projects,
            'totals' => $totals,
        ]);
    }
}
