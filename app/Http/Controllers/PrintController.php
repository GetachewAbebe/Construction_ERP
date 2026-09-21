<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Expense;
use App\Models\InventoryLoan;
use App\Services\QrCodeService;
use Illuminate\View\View;

class PrintController extends Controller
{
    /**
     * Render printable corporate payment voucher for an expense.
     */
    public function expenseVoucher(Expense $expense, QrCodeService $qr): View
    {
        $expense->load(['project', 'user', 'approvedBy']);
        $verificationUrl = route('verify.expense-voucher', $expense->id);
        $qrCodeSvg = $qr->svg($verificationUrl, 100, '#1e3a8a');

        return view('prints.expense-voucher', [
            'expense' => $expense,
            'qrCodeSvg' => $qrCodeSvg,
            'verificationUrl' => $verificationUrl,
        ]);
    }

    /**
     * Render printable store gate pass & material issue note for an inventory loan.
     */
    public function loanGatePass(InventoryLoan $loan, QrCodeService $qr): View
    {
        $loan->load(['inventoryItem', 'user', 'employee.department_rel', 'employee.position_rel', 'approvedBy']);
        $verificationUrl = route('verify.gate-pass', $loan->id);
        $qrCodeSvg = $qr->svg($verificationUrl, 100, '#0f172a');

        return view('prints.loan-gate-pass', [
            'loan' => $loan,
            'qrCodeSvg' => $qrCodeSvg,
            'verificationUrl' => $verificationUrl,
        ]);
    }
}
