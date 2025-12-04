<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\InventoryItem;
use App\Models\InventoryLoan;

class DashboardController extends Controller
{
    /**
     * Administrator dashboard
     * Shows high-level overview including pending item loans.
     */
    public function admin()
    {
        // Pending equipment/material loans that need Admin approval
        $pendingLoanCount = InventoryLoan::where('status', 'pending')->count();

        // Placeholder for future metrics (leave approvals, finance, etc.)
        // Keep as null so the Blade can safely show "—" if not used yet.
        $pendingLeaveCount = null;

        return view('dashboards.admin', [
            'pendingLoanCount'  => $pendingLoanCount,
            'pendingLeaveCount' => $pendingLeaveCount,
        ]);
    }

    /**
     * Human Resource dashboard
     * The current hr.blade.php uses static content only.
     */
    public function hr()
    {
        return view('dashboards.hr');
    }

    /**
     * Inventory dashboard
     * Uses real data for summary cards + open loans.
     */
    public function inventory()
    {
        $totalItems = InventoryItem::count();

        // Low stock: quantity > 0 and <= 5 (you can tune the threshold)
        $lowStockCount = InventoryItem::where('quantity', '>', 0)
            ->where('quantity', '<=', 5)
            ->count();

        // Out of stock: quantity <= 0
        $outOfStockCount = InventoryItem::where('quantity', '<=', 0)->count();

        // Open loans: loans not yet returned (approved or pending)
        $openLoanCount = InventoryLoan::whereIn('status', ['pending', 'approved'])->count();

        return view('dashboards.inventory', [
            'totalItems'      => $totalItems,
            'lowStockCount'   => $lowStockCount,
            'outOfStockCount' => $outOfStockCount,
            'openLoanCount'   => $openLoanCount,
        ]);
    }

    /**
     * Finance dashboard
     * Currently simple; you can pass metrics later.
     */
    public function finance()
    {
        return view('dashboards.finance');
    }
}
