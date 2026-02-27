<?php

namespace App\Http\Controllers\Inventory;

use App\Http\Controllers\Controller;
use App\Models\AssetClassification;
use App\Models\InventoryItem;
use App\Services\InventoryService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class InventoryItemController extends Controller
{
    protected InventoryService $inventoryService;

    public function __construct(InventoryService $inventoryService)
    {
        $this->inventoryService = $inventoryService;
    }

    public function index(Request $request)
    {
        $q = trim((string) $request->input('q', ''));
        $loc = trim((string) $request->input('store_location', ''));
        $from = $request->input('from_date');
        $to = $request->input('to_date');
        $status = $request->input('status');
        $classification_id = $request->input('classification_id');

        $query = InventoryItem::query()
            ->when($q !== '', function ($query) use ($q) {
                $query->where(function ($qq) use ($q) {
                    $qq->where('item_no', 'ILIKE', "%{$q}%")
                        ->orWhere('name', 'ILIKE', "%{$q}%")
                        ->orWhere('description', 'ILIKE', "%{$q}%");
                });
            })
            ->when($loc !== '', fn ($query) => $query->where('store_location', $loc))
            ->when($from, fn ($query) => $query->whereDate('in_date', '>=', $from))
            ->when($to, fn ($query) => $query->whereDate('in_date', '<=', $to))
            ->when($classification_id, fn ($query) => $query->where('classification_id', $classification_id))
            ->when($status, function ($query) use ($status) {
                if ($status === 'in_stock') {
                    $query->where('quantity', '>', 5);
                } elseif ($status === 'low_stock') {
                    $query->where('quantity', '<=', 5)->where('quantity', '>', 0);
                } elseif ($status === 'out_of_stock') {
                    $query->where('quantity', '<=', 0);
                }
            }, function ($query) use ($q, $loc) {
                // Default behavior: Hide deleted items unless searching or filtering by location
                if ($q === '' && $loc === '') {
                    $query->where('quantity', '>', 0);
                }
            });

        // Global totals for the analytic cards - Synchronized with Dashboard logic
        $totals = [
            'total_items' => InventoryItem::count(),
            'low_stock_count' => InventoryItem::where('quantity', '>', 0)->where('quantity', '<=', 5)->count(),
        ];

        $items = $query->latest('created_at')
            ->paginate(15)
            ->withQueryString();

        // Unique store locations for the filter
        $storeLocations = InventoryItem::whereNotNull('store_location')
            ->distinct()
            ->orderBy('store_location')
            ->pluck('store_location');

        $classifications = AssetClassification::orderBy('name')->get();

        return view('inventory.items.index', compact('items', 'totals', 'q', 'loc', 'from', 'to', 'storeLocations', 'classifications'));
    }

    public function create()
    {
        $classifications = AssetClassification::orderBy('name')->get();
        $vendors = \App\Models\Vendor::active()->orderBy('name')->get();

        return view('inventory.items.create', compact('classifications', 'vendors'));
    }

    public function store(\App\Http\Requests\Inventory\StoreInventoryItemRequest $request)
    {
        $data = $request->validated();

        try {
            // Check for existing item with same item_no and store_location
            $exists = InventoryItem::where('item_no', $data['item_no'])
                ->where('store_location', $data['store_location'])
                ->exists();

            if ($exists) {
                return back()->withInput()->with('error', 'An item with this ID already exists in this location.');
            }

            DB::transaction(function () use ($data) {
                $item = InventoryItem::create($data);

                // Log initial stock via Service if needed or manual
                \App\Models\InventoryLog::create([
                    'inventory_item_id' => $item->id,
                    'user_id' => auth()->id(),
                    'change_amount' => $item->quantity,
                    'previous_quantity' => 0,
                    'new_quantity' => $item->quantity,
                    'reason' => 'Initial Entry',
                    'remarks' => 'New inventory item created',
                ]);
            });

            return redirect()->route('inventory.items.index')
                ->with('success', 'Inventory item added successfully.');

        } catch (\Exception $e) {
            Log::error('Inventory creation failed: '.$e->getMessage());

            return back()->withInput()->with('error', 'Failed to save inventory item.');
        }
    }

    public function edit(InventoryItem $item)
    {
        $classifications = AssetClassification::orderBy('name')->get();
        $vendors = \App\Models\Vendor::active()->orderBy('name')->get();

        return view('inventory.items.edit', compact('item', 'classifications', 'vendors'));
    }

    public function update(\App\Http\Requests\Inventory\UpdateInventoryItemRequest $request, InventoryItem $item)
    {
        $data = $request->validated();

        try {
            $newQuantity = $data['quantity'];
            unset($data['quantity']); // Quantity usually handled via adjustments Service

            DB::transaction(function () use ($item, $data, $newQuantity) {
                $item->update($data);

                // If quantity changed, log it via Service
                if ($item->quantity != $newQuantity) {
                    $this->inventoryService->adjustQuantity($item, $newQuantity, 'Manual Adjustment', 'Updated via item edit form');
                }
            });

            return redirect()->route('inventory.items.index')
                ->with('success', 'Inventory record updated.');

        } catch (\Exception $e) {
            Log::error('Inventory update failed: '.$e->getMessage());

            return back()->withInput()->with('error', 'Failed to update record.');
        }
    }

    public function destroy(InventoryItem $item)
    {
        try {
            DB::transaction(function () use ($item) {
                $item->delete();
            });

            return redirect()
                ->route('inventory.items.index')
                ->with('success', 'Item deleted.');
        } catch (\Throwable $e) {
            Log::error('InventoryItem delete failed', ['message' => $e->getMessage(), 'id' => $item->id]);

            return back()->with('error', 'Could not delete the item.');
        }
    }
}
