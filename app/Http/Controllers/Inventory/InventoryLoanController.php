<?php

declare(strict_types=1);

namespace App\Http\Controllers\Inventory;

use App\Http\Controllers\Controller;
use App\Mail\NewInventoryLoanRequestMail;
use App\Models\Employee;
use App\Models\InventoryItem;
use App\Models\InventoryLoan;
use App\Models\User;
use App\Notifications\InventoryLoanStatusNotification;
use App\Services\InventoryService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Notification;
use Inertia\Inertia;
use Inertia\Response;

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
    public function index(Request $request): Response
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

        return Inertia::render('Inventory/Loans/Index', compact('loans', 'q', 'status'));
    }

    /**
     * Show create loan form.
     * Route: GET /inventory/loans/create
     */
    public function create(): Response
    {
        $items = InventoryItem::orderBy('name')->where('quantity', '>', 0)->get();
        $employees = Employee::orderBy('first_name')->get();

        return Inertia::render('Inventory/Loans/Create', compact('items', 'employees'));
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
                'quantity' => "Insufficient stock. Only {$item->available_quantity} units available.",
            ]);
        }

        try {
            DB::transaction(function () use ($data) {
                $loan = InventoryLoan::create(array_merge($data, [
                    'requested_by_user_id' => auth()->id(),
                    'requested_at' => $data['requested_at'] ?? now(),
                    'status' => 'pending',
                ]));

                $admins = User::role('Administrator')->get();
                if ($admins->isEmpty()) {
                    $admins = User::where('id', 1)->get();
                }

                try {
                    if ($admins->isNotEmpty()) {
                        Notification::send($admins, new InventoryLoanStatusNotification($loan, 'request'));
                        foreach ($admins as $admin) {
                            Mail::to($admin->email)->send(new NewInventoryLoanRequestMail($loan, auth()->user()));
                        }
                    }
                } catch (\Exception $e) {
                    \Log::warning('Loan notification failed: '.$e->getMessage());
                }
            });

            return redirect()->route('inventory.loans.index')
                ->with('success', 'Asset loan request has been successfully queued for approval.');
        } catch (\Exception $e) {
            \Log::error('Loan request failed: '.$e->getMessage());

            return back()->withInput()->with('error', 'Critical failure during loan initialization.');
        }
    }

    public function show(InventoryLoan $loan): Response
    {
        $loan->load(['item', 'employee', 'approvedBy', 'rejectedBy']);

        return Inertia::render('Inventory/Loans/Show', compact('loan'));
    }

    public function edit(InventoryLoan $loan): Response
    {
        $loan->load(['item', 'employee']);
        $items = InventoryItem::orderBy('name')->get();
        $employees = Employee::orderBy('first_name')->get();

        return Inertia::render('Inventory/Loans/Edit', compact('loan', 'items', 'employees'));
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
                'quantity' => "Insufficient stock for update. Only {$item->available_quantity} units available.",
            ]);
        }

        try {
            $loan->update($data);

            return redirect()->route('inventory.loans.index')
                ->with('success', 'Loan request parameters updated.');
        } catch (\Exception $e) {
            \Log::error('Loan update failed: '.$e->getMessage());

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
            // Lock the loan row and re-check inside the transaction so a double
            // "mark returned" can't credit the stock back twice.
            $loan = InventoryLoan::whereKey($loan->id)->lockForUpdate()->first();

            if (! $loan || $loan->status !== 'approved') {
                return;
            }

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

            $loan->status = 'returned';
            $loan->returned_at = now();
            $loan->save();
        });

        return redirect()->route('inventory.loans.index')
            ->with('success', 'Loan successfully marked as returned.');
    }

    /**
     * Export all loan ledger records as a CSV download.
     * Route: GET /inventory/loans/export
     */
    public function exportCsv(Request $request)
    {
        $loans = InventoryLoan::with(['item', 'employee', 'approvedBy', 'rejectedBy'])
            ->latest('id')
            ->get();

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="inventory_loans_export_'.date('Y-m-d_His').'.csv"',
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0',
        ];

        $callback = function () use ($loans) {
            $file = fopen('php://output', 'w');
            fputcsv($file, [
                'Loan ID',
                'Item No',
                'Item Name',
                'Borrower / Employee',
                'Quantity Borrowed',
                'Status',
                'Requested At',
                'Approved At',
                'Returned At',
                'Approved By',
                'Remarks / Purpose',
            ]);

            foreach ($loans as $loan) {
                $borrowerName = trim(($loan->employee->first_name ?? '').' '.($loan->employee->last_name ?? ''));
                if (! $borrowerName && $loan->employee) {
                    $borrowerName = $loan->employee->name ?? 'Employee #'.$loan->employee_id;
                }

                fputcsv($file, [
                    $loan->id,
                    $loan->item?->item_no ?? 'N/A',
                    $loan->item?->name ?? 'N/A',
                    $borrowerName ?: 'N/A',
                    $loan->quantity,
                    strtoupper($loan->status),
                    $loan->requested_at ? (is_string($loan->requested_at) ? substr($loan->requested_at, 0, 10) : $loan->requested_at->format('Y-m-d')) : '',
                    $loan->approved_at ? (is_string($loan->approved_at) ? substr($loan->approved_at, 0, 10) : $loan->approved_at->format('Y-m-d')) : '',
                    $loan->returned_at ? (is_string($loan->returned_at) ? substr($loan->returned_at, 0, 10) : $loan->returned_at->format('Y-m-d')) : '',
                    $loan->approvedBy?->name ?? 'N/A',
                    $loan->remarks ?? '',
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
