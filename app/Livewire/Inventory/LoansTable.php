<?php

declare(strict_types=1);

namespace App\Livewire\Inventory;

use App\Models\InventoryLoan;
use App\Services\InventoryService;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;
use Mary\Traits\Toast;

/**
 * POC: reactive Inventory Loans table built with Livewire + Mary UI.
 *
 * Mirrors the behaviour of InventoryLoanController + InventoryLoanApprovalController,
 * but without page reloads. Approve/reject/return reuse InventoryService so the
 * stock-quantity + InventoryLog side effects stay identical to the existing flow.
 */
#[Layout('components.layouts.poc')]
class LoansTable extends Component
{
    use Toast;
    use WithPagination;

    #[Url(as: 'q')]
    public string $search = '';

    #[Url]
    public string $status = '';

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function updatedStatus(): void
    {
        $this->resetPage();
    }

    public function clearFilters(): void
    {
        $this->reset('search', 'status');
        $this->resetPage();
    }

    /**
     * Toggle the status filter (used by the clickable stat cards).
     */
    public function setStatus(string $status): void
    {
        $this->status = $this->status === $status ? '' : $status;
        $this->resetPage();
    }

    public function approve(int $id, InventoryService $service): void
    {
        $result = DB::transaction(function () use ($id, $service) {
            // Lock the loan row and re-check status inside the transaction so two
            // concurrent approvals can't both deduct stock for the same request.
            $loan = InventoryLoan::with('employee')->whereKey($id)->lockForUpdate()->first();

            if (! $loan || $loan->status !== 'pending') {
                return 'already';
            }

            // Lock the item so two loans on the same item can't both pass the
            // stock check against a stale quantity.
            $item = $loan->item()->lockForUpdate()->first();

            if (! $item || $item->quantity < $loan->quantity) {
                return 'no_stock';
            }

            $service->logLoanChange(
                $item,
                -$loan->quantity,
                'loan_approved',
                "Approved loan ID: {$loan->id} for employee: {$loan->employee?->name}"
            );

            $loan->update([
                'status' => 'approved',
                'approved_by' => auth()->id(),
                'approved_at' => now(),
            ]);

            return 'ok';
        });

        match ($result) {
            'ok' => $this->success('Loan approved and stock updated.'),
            'no_stock' => $this->error('Not enough stock to approve this request.'),
            default => $this->warning('This request has already been processed.'),
        };
    }

    public function reject(int $id): void
    {
        $result = DB::transaction(function () use ($id) {
            // Lock the loan row and re-check so a concurrent approve can't deduct
            // stock for a request that we are rejecting (and vice versa).
            $loan = InventoryLoan::whereKey($id)->lockForUpdate()->first();

            if (! $loan || $loan->status !== 'pending') {
                return 'already';
            }

            $loan->update([
                'status' => 'rejected',
                'approved_by' => auth()->id(),
                'approved_at' => now(),
            ]);

            return 'ok';
        });

        $result === 'ok'
            ? $this->warning('Loan request rejected.')
            : $this->warning('This request has already been processed.');
    }

    public function markReturned(int $id, InventoryService $service): void
    {
        $result = DB::transaction(function () use ($id, $service) {
            // Lock the loan row and re-check inside the transaction so a double
            // "mark returned" can't credit the stock back twice.
            $loan = InventoryLoan::with('employee')->whereKey($id)->lockForUpdate()->first();

            if (! $loan || $loan->status !== 'approved') {
                return 'already';
            }

            $item = $loan->item()->lockForUpdate()->first();

            if ($item) {
                $service->logLoanChange(
                    $item,
                    $loan->quantity,
                    'loan_returned',
                    "Returned loan ID: {$loan->id} from employee: {$loan->employee?->name}"
                );
            }

            $loan->update([
                'status' => 'returned',
                'returned_at' => now(),
            ]);

            return 'ok';
        });

        $result === 'ok'
            ? $this->success('Loan marked as returned.')
            : $this->warning('Only approved loans can be marked as returned.');
    }

    public function render(): View
    {
        $loans = InventoryLoan::query()
            ->with(['item', 'employee'])
            ->when($this->search, function ($query) {
                $term = trim($this->search);
                $query->where(function ($sub) use ($term) {
                    $sub->whereHas('employee', fn ($e) => $e
                        ->where('first_name', 'like', "%{$term}%")
                        ->orWhere('last_name', 'like', "%{$term}%"))
                        ->orWhereHas('item', fn ($i) => $i
                            ->where('name', 'like', "%{$term}%")
                            ->orWhere('item_no', 'like', "%{$term}%"));
                });
            })
            ->when($this->status, fn ($query) => $query->where('status', $this->status))
            ->orderByRaw("CASE WHEN status = 'pending' THEN 0 WHEN status = 'approved' THEN 1 ELSE 2 END")
            ->latest()
            ->paginate(10);

        $counts = InventoryLoan::query()
            ->selectRaw('status, count(*) as aggregate')
            ->groupBy('status')
            ->pluck('aggregate', 'status');

        return view('livewire.inventory.loans-table', [
            'loans' => $loans,
            'counts' => $counts,
            'total' => $counts->sum(),
        ]);
    }
}
