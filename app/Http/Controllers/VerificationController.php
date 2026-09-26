<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Expense;
use App\Models\InventoryLoan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class VerificationController extends Controller
{
    /**
     * Mobile-optimized verification screen for Inventory Loan Gate Passes.
     */
    public function verifyGatePass(InventoryLoan $loan): View
    {
        $loan->load(['inventoryItem', 'user', 'employee.department_rel', 'employee.position_rel', 'approvedBy']);

        return view('verification.gate-pass', [
            'loan' => $loan,
        ]);
    }

    /**
     * One-tap site delivery confirmation for field storekeepers and site supervisors.
     */
    public function confirmGatePassReceipt(Request $request, InventoryLoan $loan)
    {
        if ($loan->status !== 'approved') {
            return back()->with('error', 'Only approved gate passes can be marked as received on site.');
        }

        $verifierName = Auth::user() ? Auth::user()->name : 'Site Supervisor (Verified Scan)';
        $existingNotes = $loan->notes ? $loan->notes . "\n" : '';
        $loan->update([
            'notes' => $existingNotes . "• Confirmed received at project site on " . now()->format('M d, Y H:i') . " by {$verifierName}.",
        ]);

        return back()->with('success', 'Site delivery confirmed successfully! Material movement record updated.');
    }

    /**
     * Mobile-optimized verification screen for Corporate Expense Payment Vouchers.
     */
    public function verifyExpenseVoucher(Expense $expense): View
    {
        $expense->load(['project', 'user', 'approvedBy']);

        return view('verification.expense-voucher', [
            'expense' => $expense,
        ]);
    }

    /**
     * Mobile-optimized verification screen for Purchase Orders.
     */
    public function verifyPurchaseOrder(\App\Models\PurchaseOrder $order): View
    {
        $order->load(['vendor', 'project', 'creator', 'items']);

        return view('verification.purchase-order', [
            'order' => $order,
        ]);
    }

    /**
     * Mobile-optimized verification screen for Goods Receiving Notes.
     */
    public function verifyGoodsReceivingNote(\App\Models\GoodsReceivingNote $note): View
    {
        $note->load(['purchaseOrder', 'project', 'vendor', 'receiver', 'items']);

        return view('verification.goods-receiving-note', [
            'note' => $note,
        ]);
    }

    /**
     * Mobile-optimized verification screen for Client Interim Payment Certificates.
     */
    public function verifyClientIpc(\App\Models\ClientPaymentCertificate $certificate): View
    {
        $certificate->load(['project', 'preparer', 'approver']);

        return view('verification.client-ipc', [
            'certificate' => $certificate,
        ]);
    }

    /**
     * Mobile-optimized verification screen for Site Casual Labor Muster Rolls.
     */
    public function verifyMusterRoll(string $musterRollNo): View
    {
        $musterRoll = \App\Models\MusterRoll::where('muster_roll_no', $musterRollNo)
            ->with(['project', 'supervisor', 'approvedBy', 'paidBy', 'items.casualLaborer'])
            ->firstOrFail();

        return view('verification.muster-roll', [
            'musterRoll' => $musterRoll,
        ]);
    }

    /**
     * Mobile-optimized verification screen for Fleet Maintenance Work Orders.
     */
    public function verifyMaintenanceOrder(string $workOrderNo): View
    {
        $workOrder = \App\Models\MaintenanceWorkOrder::where('work_order_no', $workOrderNo)
            ->with(['equipment', 'project', 'assignedMechanic', 'creator', 'completer', 'parts.inventoryItem'])
            ->firstOrFail();

        return view('verification.maintenance-work-order', [
            'workOrder' => $workOrder,
        ]);
    }
}
