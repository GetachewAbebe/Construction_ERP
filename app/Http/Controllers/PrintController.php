<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Expense;
use App\Models\InventoryLoan;
use Illuminate\View\View;

class PrintController extends Controller
{
    /**
     * Render printable corporate payment voucher for an expense.
     */
    public function expenseVoucher(Expense $expense): View
    {
        $expense->load(['project', 'user', 'approvedBy']);

        return view('prints.expense-voucher', [
            'expense' => $expense,
        ]);
    }

    /**
     * Render printable store gate pass & material issue note for an inventory loan.
     */
    public function loanGatePass(InventoryLoan $loan): View
    {
        $loan->load(['inventoryItem', 'user', 'employee', 'approvedBy']);

        return view('prints.loan-gate-pass', [
            'loan' => $loan,
        ]);
    }
}
