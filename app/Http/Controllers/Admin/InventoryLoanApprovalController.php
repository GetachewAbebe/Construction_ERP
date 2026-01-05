<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\InventoryLoan;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

use App\Services\InventoryService;

class InventoryLoanApprovalController extends Controller
{
    protected InventoryService $inventoryService;

    public function __construct(InventoryService $inventoryService)
    {
        $this->inventoryService = $inventoryService;
    }

    /**
     * Approve a loan:
     * - only if status = pending
     * - decrements item quantity
     */
    public function approve(InventoryLoan $loan, Request $request): RedirectResponse
    {
        if ($loan->status !== 'pending') {
            return back()->with('status', 'This request has already been processed.');
        }

        return DB::transaction(function () use ($loan) {
            $item = $loan->item;

            if (! $item) {
                return back()->with('error', 'Linked item not found for this request.');
            }

            if ($item->quantity < $loan->quantity) {
                return back()->with('error', 'Not enough stock to approve this request.');
            }

            // Use Service to log change and handle quantity
            $this->inventoryService->logLoanChange(
                $item, 
                -$loan->quantity, 
                'loan_approved', 
                "Approved loan ID: {$loan->id} for employee: {$loan->employee->name}"
            );

            // Update loan status
            $loan->status      = 'approved';
            $loan->approved_by = auth()->id();
            $loan->approved_at = now();
            $loan->save();

            return back()->with('status', 'Loan approved and item quantity updated.');
        });
    }


    /**
     * Reject a loan:
     * - only if status = pending
     * - DOES NOT change item quantity
     */
    public function reject(InventoryLoan $loan, Request $request): RedirectResponse
    {
        if ($loan->status !== 'pending') {
            return back()->with('status', 'This request has already been processed.');
        }

        $loan->status      = 'rejected';
        $loan->approved_by = auth()->id();
        $loan->approved_at = now();
        $loan->save();

        return back()->with('status', 'Loan request rejected.');
    }
}
