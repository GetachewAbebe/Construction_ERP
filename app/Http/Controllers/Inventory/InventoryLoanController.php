<?php

namespace App\Http\Controllers\Inventory;

use App\Http\Controllers\Controller;
use App\Models\InventoryItem;
use App\Models\InventoryLoan;
use App\Models\Employee;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;
use App\Models\User;
use App\Notifications\InventoryLoanStatusNotification;

use App\Services\InventoryService;

class InventoryLoanController extends Controller
{
    protected InventoryService $inventoryService;

    public function __construct(InventoryService $inventoryService)
    {
        $this->inventoryService = $inventoryService;
    }

    /**
     * List all loans for Inventory (Inventory Manager + Admin).
     * Route: GET /inventory/loans
     */
    public function index(Request $request): View
    {
        $q = trim((string) $request->input('q', ''));
        $status = $request->input('status');

        $loans = InventoryLoan::with(['item', 'employee.position_rel', 'approvedBy', 'rejectedBy'])
            ->when($q, function ($query, $q) {
                $query->whereHas('employee', function ($sub) use ($q) {
                    $sub->where('first_name', 'like', "%{$q}%")
                        ->orWhere('last_name', 'like', "%{$q}%");
                })->orWhereHas('item', function ($sub) use ($q) {
                    $sub->where('name', 'like', "%{$q}%")
                        ->orWhere('item_no', 'like', "%{$q}%");
                });
            })
            ->when($status, function ($query, $status) {
                $query->where('status', $status);
            })
            ->orderByRaw("CASE WHEN status = 'pending' THEN 0 WHEN status = 'approved' THEN 1 ELSE 2 END")
            ->latest()
            ->paginate(20)
            ->withQueryString();

        return view('inventory.loans.index', compact('loans', 'q', 'status'));
    }

    /**
     * Show create loan form.
     * Route: GET /inventory/loans/create
     */
    public function create(): View
    {
        $items = InventoryItem::orderBy('name')->where('quantity', '>', 0)->get();
        $employees = Employee::orderBy('first_name')->get();

        return view('inventory.loans.create', compact('items', 'employees'));
    }

    /**
     * Store a new loan request.
     * Route: POST /inventory/loans
     *
     * NOTE:
     *  - We DO NOT change item quantity here.
     *  - Quantity is decremented only when Admin approves.
     */
    public function store(\App\Http\Requests\Inventory\StoreInventoryLoanRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $item = InventoryItem::findOrFail($data['inventory_item_id']);
        
        if ($item->available_quantity < $data['quantity']) {
            return back()->withInput()->withErrors([
                'quantity' => "Insufficient stock. Only {$item->available_quantity} units available."
            ]);
        }

        try {
            DB::transaction(function () use ($data) {
                $loan = InventoryLoan::create(array_merge($data, [
                    'requested_by_user_id' => auth()->id(),
                    'requested_at'         => $data['requested_at'] ?? now(),
                    'status'               => 'pending',
                ]));

                $admins = User::role('Administrator')->get();
                try {
                    Notification::send($admins, new InventoryLoanStatusNotification($loan, 'request'));
                } catch (\Exception $e) {
                    \Log::warning('Loan notification failed: ' . $e->getMessage());
                }
            });

            return redirect()->route('inventory.loans.index')
                ->with('success', 'Asset loan request has been successfully queued for approval.');
        } catch (\Exception $e) {
            \Log::error('Loan request failed: ' . $e->getMessage());
            return back()->withInput()->with('error', 'Critical failure during loan initialization.');
        }
    }

    public function show(InventoryLoan $loan): View
    {
        $loan->load(['item', 'employee', 'approvedBy', 'rejectedBy']);
        return view('inventory.loans.show', compact('loan'));
    }

    public function edit(InventoryLoan $loan): View
    {
        $loan->load(['item', 'employee']);
        $items     = InventoryItem::orderBy('name')->get();
        $employees = Employee::orderBy('first_name')->get();

        return view('inventory.loans.edit', compact('loan', 'items', 'employees'));
    }

    public function update(\App\Http\Requests\Inventory\UpdateInventoryLoanRequest $request, InventoryLoan $loan): RedirectResponse
    {
        if ($loan->status !== 'pending') {
            return back()->with('error', 'Modification rejected: only pending requests can be altered.');
        }

        $data = $request->validated();
        $item = InventoryItem::findOrFail($data['inventory_item_id']);

        if ($item->available_quantity < $data['quantity']) {
            return back()->withInput()->withErrors([
                'quantity' => "Insufficient stock for update. Only {$item->available_quantity} units available."
            ]);
        }

        try {
            $loan->update($data);
            return redirect()->route('inventory.loans.index')
                ->with('success', 'Loan request parameters updated.');
        } catch (\Exception $e) {
            \Log::error('Loan update failed: ' . $e->getMessage());
            return back()->withInput()->with('error', 'Failed to update loan record.');
        }
    }

    /**
     * Delete loan (cannot delete approved).
     * Route: DELETE /inventory/loans/{loan}
     */
    public function destroy(InventoryLoan $loan): RedirectResponse
    {
        if ($loan->status === 'approved') {
            return back()->with('error', 'Cannot delete an approved loan. Mark it as returned instead.');
        }

        $loan->delete();

        return redirect()
            ->route('inventory.loans.index')
            ->with('success', 'Loan deleted.');
    }

    /**
     * Mark an approved loan as returned.
     * Route: POST /inventory/loans/{loan}/mark-returned
     */
    public function markReturned(InventoryLoan $loan): RedirectResponse
    {
        if ($loan->status !== 'approved') {
            return back()->with('status', 'Only approved loans can be marked as returned.');
        }

        DB::transaction(function () use ($loan) {
            $item = $loan->item()->lockForUpdate()->first();

            if ($item) {
                // Use Service to log change and handle quantity
                $this->inventoryService->logLoanChange(
                    $item, 
                    $loan->quantity, 
                    'loan_returned', 
                    "Returned loan ID: {$loan->id} from employee: {$loan->employee->name}"
                );
            }

            $loan->status      = 'returned';
            $loan->returned_at = now();
            $loan->save();
        });

    }
}
