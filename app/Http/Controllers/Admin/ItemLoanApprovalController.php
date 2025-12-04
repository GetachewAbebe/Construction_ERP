<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\InventoryLoan;

class ItemLoanApprovalController extends Controller
{
    /**
     * Show all item lending requests (for Admin).
     */
    public function index()
    {
        // Load loans with related item and employee
        $loans = InventoryLoan::with(['item', 'employee'])
            ->latest()
            ->paginate(20);

        return view('admin.requests.items', compact('loans'));
    }
}
