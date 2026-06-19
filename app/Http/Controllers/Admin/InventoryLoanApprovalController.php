<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Mail\InventoryLoanStatusMail;
use App\Models\InventoryLoan;
use App\Models\User;
use App\Notifications\InventoryLoanStatusNotification;
use App\Services\InventoryService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Notification;
use Illuminate\View\View;

class InventoryLoanApprovalController extends Controller
{
    public function __construct(
        private readonly InventoryService $inventoryService,
    ) {}

    public function index(): View
    {
        $loans = InventoryLoan::with(['item', 'employee'])
            ->orderByRaw("CASE WHEN status = 'pending' THEN 0 ELSE 1 END")
            ->latest()
            ->paginate(20);

        return view('admin.requests.items', compact('loans'));
    }

    public function approve(InventoryLoan $loan, Request $request): RedirectResponse
    {
        if ($loan->status !== 'pending') {
            return back()->with('status', 'This request has already been processed.');
        }

        return DB::transaction(function () use ($loan) {
            // Lock the loan row and re-check status inside the transaction so two
            // concurrent approvals can't both deduct stock for the same request.
            $loan = InventoryLoan::whereKey($loan->id)->lockForUpdate()->first();

            if (! $loan || $loan->status !== 'pending') {
                return back()->with('status', 'This request has already been processed.');
            }

            // Lock the item row so two loans on the same item can't both pass the
            // stock check against a stale quantity.
            $item = $loan->item()->lockForUpdate()->first();

            if (! $item) {
                return back()->with('error', 'Linked item not found for this request.');
            }

            if ($item->quantity < $loan->quantity) {
                return back()->with('error', 'Not enough stock to approve this request.');
            }

            $this->inventoryService->logLoanChange(
                $item,
                -$loan->quantity,
                'loan_approved',
                "Approved loan ID: {$loan->id} for employee: {$loan->employee->name}"
            );

            $loan->status = 'approved';
            $loan->approved_by = (int) auth()->id();
            $loan->approved_at = now();
            $loan->save();

            $this->notifyParties($loan);

            return back()->with('status', 'Loan approved and item quantity updated.');
        });
    }

    public function reject(InventoryLoan $loan, Request $request): RedirectResponse
    {
        if ($loan->status !== 'pending') {
            return back()->with('status', 'This request has already been processed.');
        }

        return DB::transaction(function () use ($loan, $request) {
            // Lock the loan row and re-check so a concurrent approve can't deduct
            // stock for a request that we are rejecting (and vice versa).
            $loan = InventoryLoan::whereKey($loan->id)->lockForUpdate()->first();

            if (! $loan || $loan->status !== 'pending') {
                return back()->with('status', 'This request has already been processed.');
            }

            $loan->status = 'rejected';
            $loan->rejected_by = (int) auth()->id();
            $loan->rejected_at = now();
            $loan->rejection_reason = $request->input('rejection_reason');
            $loan->save();

            $this->notifyParties($loan);

            return back()->with('status', 'Loan request rejected.');
        });
    }

    private function notifyParties(InventoryLoan $loan): void
    {
        if ($loan->employee && $loan->employee->user) {
            $loan->employee->user->notify(new InventoryLoanStatusNotification($loan, 'status_change'));
            try {
                Mail::to($loan->employee->user->email)
                    ->send(new InventoryLoanStatusMail($loan, $loan->employee->user));
            } catch (\Exception $e) {
                Log::error('Loan status mail failed: '.$e->getMessage());
            }
        }

        $inventoryManagers = User::role('InventoryManager')->get();
        if ($inventoryManagers->isNotEmpty()) {
            Notification::send($inventoryManagers, new InventoryLoanStatusNotification($loan, 'status_update'));
        }

        auth()->user()->unreadNotifications()
            ->where('data->loan_id', (string) $loan->id)
            ->get()
            ->markAsRead();
    }
}
