<?php

namespace App\Http\Controllers\Inventory;

use App\Http\Controllers\Controller;
use App\Models\InventoryLog;
use Illuminate\Http\Request;

class InventoryLogController extends Controller
{
    /**
     * Display a listing of inventory logs (audit trail).
     */
    public function index(Request $request)
    {
        $query = InventoryLog::with(['item', 'user'])->latest();

        // Optional filtering by item
        if ($request->filled('item_id')) {
            $query->where('inventory_item_id', $request->item_id);
        }

        // Optional filtering by user
        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        $logs = $query->paginate(25);

        return view('inventory.logs.index', compact('logs'));
    }
}
