<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Mail\InventoryLoanStatusMail;
use App\Models\InventoryLoan;
use App\Notifications\InventoryLoanStatusNotification;
use App\Services\InventoryService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Notification;
use Illuminate\View\View;

class InventoryLoanApprovalController extends Controller
{
    protected InventoryService $inventoryService;

    public function __construct(InventoryService $inventoryService)
    {
        $this->inventoryService = $inventoryService;
    }

    /**
     * List inventory loan requests for Admin approval.
     */
    public function index(): View
    {
        $loans = InventoryLoan::with(['item', 'employee'])
            ->orderByRaw("CASE WHEN status = 'pending' THEN 0 ELSE 1 END")
            ->latest()
            ->paginate(20);

        return view('admin.requests.items', compact('loans'));
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
            $loan->status = 'approved';
            $loan->approved_by = auth()->id();
            $loan->approved_at = now();
            $loan->save();

            if ($loan->employee && $loan->employee->user) {
                $loan->employee->user->notify(new InventoryLoanStatusNotification($loan, 'status_change'));
                try {
                    Mail::to($loan->employee->user->email)->send(new InventoryLoanStatusMail($loan, $loan->employee->user));
                } catch (\Exception $e) {
                    \Log::error('Loan status mail failed: '.$e->getMessage());
                }
            }

            // Notify Inventory Managers
            $inventoryManagers = \App\Models\User::role('InventoryManager')->get();
            if ($inventoryManagers->isNotEmpty()) {
                Notification::send($inventoryManagers, new InventoryLoanStatusNotification($loan, 'status_update'));
            }

            // Clear notification for admin
            auth()->user()->unreadNotifications()
                ->where('data->loan_id', $loan->id)
                ->get()
                ->markAsRead();

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

        $loan->status = 'rejected';
        $loan->approved_by = auth()->id();
        $loan->approved_at = now();
        $loan->save();

        if ($loan->employee && $loan->employee->user) {
            $loan->employee->user->notify(new InventoryLoanStatusNotification($loan, 'status_change'));
            try {
                Mail::to($loan->employee->user->email)->send(new InventoryLoanStatusMail($loan, $loan->employee->user));
            } catch (\Exception $e) {
                \Log::error('Loan status mail failed: '.$e->getMessage());
            }
        }

        // Notify Inventory Managers
        $inventoryManagers = \App\Models\User::role('InventoryManager')->get();
        if ($inventoryManagers->isNotEmpty()) {
            Notification::send($inventoryManagers, new InventoryLoanStatusNotification($loan, 'status_update'));
        }

        // Clear notification for admin
        auth()->user()->unreadNotifications()
            ->where('data->loan_id', $loan->id)
            ->get()
            ->markAsRead();

        return back()->with('status', 'Loan request rejected.');
    }
}
