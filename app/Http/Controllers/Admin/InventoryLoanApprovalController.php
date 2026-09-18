<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Enums\LoanStatus;
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
use Inertia\Inertia;
use Inertia\Response;

class InventoryLoanApprovalController extends Controller
{
    public function __construct(
        private readonly InventoryService $inventoryService,
    ) {}

    public function index(): Response
    {
        // Auto-clear any dangling unread loan request notifications for requests already processed
        try {
            $decidedLoanIds = InventoryLoan::where('status', '!=', LoanStatus::Pending->value)->pluck('id');
            if ($decidedLoanIds->isNotEmpty()) {
                \Illuminate\Notifications\DatabaseNotification::whereNull('read_at')
                    ->where(function ($q) {
                        $q->where('data', 'like', '%"type":"inventory_request"%')
                          ->orWhere('data', 'like', '%"type": "inventory_request"%');
                    })
                    ->where(function ($q) use ($decidedLoanIds) {
                        foreach ($decidedLoanIds as $id) {
                            $q->orWhere('data', 'like', '%"loan_id":'.$id.'%')
                              ->orWhere('data', 'like', '%"loan_id":"'.$id.'"%');
                        }
                    })
                    ->update(['read_at' => now()]);
            }
            if (auth()->check()) {
                auth()->user()->unsetRelation('unreadNotifications');
            }
        } catch (\Exception $e) {
            Log::warning('Failed to auto-clear processed loan notifications: '.$e->getMessage());
        }

        $pendingLoans = InventoryLoan::with(['item', 'employee'])
            ->where('status', LoanStatus::Pending->value)
            ->latest()
            ->paginate(15)
            ->withQueryString();

        $historyLoans = InventoryLoan::with(['item', 'employee'])
            ->where('status', '!=', LoanStatus::Pending->value)
            ->latest()
            ->paginate(15)
            ->withQueryString();

        $stats = [
            'pending_count' => InventoryLoan::where('status', LoanStatus::Pending->value)->count(),
            'active_borrowed' => InventoryLoan::where('status', LoanStatus::Approved->value)->whereNull('returned_at')->count(),
            'approved_this_month' => InventoryLoan::where('status', LoanStatus::Approved->value)
                ->whereMonth('approved_at', now()->month)
                ->count(),
        ];

        return Inertia::render('Admin/Requests/LoanApprovals', [
            'pendingLoans' => $pendingLoans,
            'historyLoans' => $historyLoans,
            'stats' => $stats,
        ]);
    }

    public function approve(InventoryLoan $loan, Request $request): RedirectResponse
    {
        $this->markLoanNotificationAsRead($loan);

        if ($loan->status !== LoanStatus::Pending->value) {
            return back()->with('status', 'This request has already been processed.');
        }

        return DB::transaction(function () use ($loan) {
            // Lock the loan row and re-check status inside the transaction so two
            // concurrent approvals can't both deduct stock for the same request.
            $loan = InventoryLoan::whereKey($loan->id)->lockForUpdate()->first();

            if (! $loan || $loan->status !== LoanStatus::Pending->value) {
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

            $loan->status = LoanStatus::Approved->value;
            $loan->approved_by = (int) auth()->id();
            $loan->approved_at = now();
            $loan->save();

            $this->notifyParties($loan);

            return back()->with('status', 'Loan approved and item quantity updated.');
        });
    }

    public function reject(InventoryLoan $loan, Request $request): RedirectResponse
    {
        $this->markLoanNotificationAsRead($loan);

        if ($loan->status !== LoanStatus::Pending->value) {
            return back()->with('status', 'This request has already been processed.');
        }

        return DB::transaction(function () use ($loan, $request) {
            // Lock the loan row and re-check so a concurrent approve can't deduct
            // stock for a request that we are rejecting (and vice versa).
            $loan = InventoryLoan::whereKey($loan->id)->lockForUpdate()->first();

            if (! $loan || $loan->status !== LoanStatus::Pending->value) {
                return back()->with('status', 'This request has already been processed.');
            }

            $loan->status = LoanStatus::Rejected->value;
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
            try {
                $loan->employee->user->notify(new InventoryLoanStatusNotification($loan, 'status_change'));
            } catch (\Exception $e) {
                Log::warning('Employee loan status notification failed: '.$e->getMessage());
            }

            try {
                Mail::to($loan->employee->user->email)
                    ->send(new InventoryLoanStatusMail($loan, $loan->employee->user));
            } catch (\Exception $e) {
                Log::error('Loan status mail failed: '.$e->getMessage());
            }
        }

        try {
            $inventoryManagers = User::role('InventoryManager')->get();
            if ($inventoryManagers->isNotEmpty()) {
                Notification::send($inventoryManagers, new InventoryLoanStatusNotification($loan, 'status_update'));
            }
        } catch (\Exception $e) {
            Log::warning('Inventory manager notification failed: '.$e->getMessage());
        }

        $this->markLoanNotificationAsRead($loan);
    }

    private function markLoanNotificationAsRead(InventoryLoan $loan): void
    {
        try {
            \Illuminate\Notifications\DatabaseNotification::whereNull('read_at')
                ->where(function ($q) {
                    $q->where('data', 'like', '%"type":"inventory_request"%')
                      ->orWhere('data', 'like', '%"type": "inventory_request"%');
                })
                ->where(function ($q) use ($loan) {
                    $q->where('data', 'like', '%"loan_id":'.$loan->id.'%')
                      ->orWhere('data', 'like', '%"loan_id":"'.$loan->id.'"%')
                      ->orWhere('data', 'like', '%"loan_id": "'.$loan->id.'"%');
                })
                ->update(['read_at' => now()]);

            if (auth()->check()) {
                auth()->user()->unsetRelation('unreadNotifications');
            }
        } catch (\Exception $e) {
            Log::warning('Failed to mark loan notification as read: '.$e->getMessage());
        }
    }
}
