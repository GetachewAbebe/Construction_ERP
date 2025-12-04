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

class InventoryLoanController extends Controller
{
    /**
     * List all loans for Inventory (Inventory Manager + Admin).
     * Route: GET /inventory/loans
     */
    public function index(): View
    {
        $loans = InventoryLoan::with(['item', 'employee', 'approvedBy', 'rejectedBy'])
            ->orderByRaw("CASE WHEN status = 'pending' THEN 0 WHEN status = 'approved' THEN 1 ELSE 2 END")
            ->latest()
            ->paginate(20);

        return view('inventory.loans.index', compact('loans'));
    }

    /**
     * Show create loan form.
     * Route: GET /inventory/loans/create
     */
    public function create(): View
    {
        $items = InventoryItem::orderBy('name')->get();

        // ✅ SAFE: don’t assume employees table has `name` or `full_name`
        // just order by id
        $employees = Employee::orderBy('id')->get();

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
    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'inventory_item_id' => ['required', 'exists:inventory_items,id'],
            'employee_id'       => ['required', 'exists:employees,id'],
            'quantity'          => ['required', 'integer', 'min:1'],
            'requested_at'      => ['nullable', 'date'],
            'due_date'          => ['nullable', 'date'],
            'notes'             => ['nullable', 'string', 'max:2000'],
        ]);

        DB::transaction(function () use ($data) {
            InventoryLoan::create([
                'inventory_item_id' => $data['inventory_item_id'],
                'employee_id'       => $data['employee_id'],
                'quantity'          => $data['quantity'],
                'requested_at'      => $data['requested_at'] ?? now(),
                'due_date'          => $data['due_date'] ?? null,
                'notes'             => $data['notes'] ?? null,
                'status'            => 'pending',   // default
            ]);
        });

        return redirect()
            ->route('inventory.loans.index')
            ->with('status', 'Loan request submitted for approval.');
    }

    /**
     * Show a single loan detail.
     * Route: GET /inventory/loans/{loan}
     */
    public function show(InventoryLoan $loan): View
    {
        $loan->load(['item', 'employee', 'approvedBy', 'rejectedBy']);

        return view('inventory.loans.show', compact('loan'));
    }

    /**
     * Edit loan (typically only while pending).
     * Route: GET /inventory/loans/{loan}/edit
     */
    public function edit(InventoryLoan $loan): View
    {
        $loan->load(['item', 'employee']);

        $items     = InventoryItem::orderBy('name')->get();
        // ✅ Same fix here
        $employees = Employee::orderBy('id')->get();

        return view('inventory.loans.edit', compact('loan', 'items', 'employees'));
    }

    /**
     * Update loan (only if still pending is recommended).
     * Route: PUT /inventory/loans/{loan}
     */
    public function update(Request $request, InventoryLoan $loan): RedirectResponse
    {
        if ($loan->status !== 'pending') {
            return back()->with('status', 'Only pending loans can be edited.');
        }

        $data = $request->validate([
            'inventory_item_id' => ['required', 'exists:inventory_items,id'],
            'employee_id'       => ['required', 'exists:employees,id'],
            'quantity'          => ['required', 'integer', 'min:1'],
            'requested_at'      => ['nullable', 'date'],
            'due_date'          => ['nullable', 'date'],
            'notes'             => ['nullable', 'string', 'max:2000'],
        ]);

        DB::transaction(function () use ($loan, $data) {
            $loan->inventory_item_id = $data['inventory_item_id'];
            $loan->employee_id       = $data['employee_id'];
            $loan->quantity          = $data['quantity'];
            $loan->requested_at      = $data['requested_at'] ?? $loan->requested_at;
            $loan->due_date          = $data['due_date'] ?? $loan->due_date;
            $loan->notes             = $data['notes'] ?? $loan->notes;
            $loan->save();
        });

        return redirect()
            ->route('inventory.loans.index')
            ->with('status', 'Loan updated successfully.');
    }

    /**
     * Delete loan (cannot delete approved).
     * Route: DELETE /inventory/loans/{loan}
     */
    public function destroy(InventoryLoan $loan): RedirectResponse
    {
        if ($loan->status === 'approved') {
            return back()->with('status', 'Cannot delete an approved loan. Mark it as returned instead.');
        }

        $loan->delete();

        return redirect()
            ->route('inventory.loans.index')
            ->with('status', 'Loan deleted.');
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
                $item->quantity = ($item->quantity ?? 0) + $loan->quantity;
                $item->save();
            }

            $loan->status      = 'returned';
            $loan->returned_at = now();
            $loan->save();
        });

        return back()->with('status', 'Loan marked as returned and stock updated.');
    }
}
