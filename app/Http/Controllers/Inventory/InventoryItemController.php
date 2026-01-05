<?php

namespace App\Http\Controllers\Inventory;

use App\Http\Controllers\Controller;
use App\Models\InventoryItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Database\QueryException;

use App\Services\InventoryService;

class InventoryItemController extends Controller
{
    protected InventoryService $inventoryService;

    public function __construct(InventoryService $inventoryService)
    {
        $this->inventoryService = $inventoryService;
    }

    public function index(Request $request)
    {
        $q    = trim((string) $request->input('q', ''));
        $loc  = trim((string) $request->input('store_location', ''));
        $from = $request->input('from_date');
        $to   = $request->input('to_date');

        $items = InventoryItem::query()
            ->when($q !== '', function ($query) use ($q) {
                $query->where(function ($qq) use ($q) {
                    $qq->where('item_no', 'ILIKE', "%{$q}%")
                       ->orWhere('name', 'ILIKE', "%{$q}%")
                       ->orWhere('description', 'ILIKE', "%{$q}%");
                });
            })
            ->when($loc !== '', fn($query) => $query->where('store_location', 'ILIKE', "%{$loc}%"))
            ->when($from, fn($query) => $query->whereDate('in_date', '>=', $from))
            ->when($to,   fn($query) => $query->whereDate('in_date', '<=', $to))
            ->latest('created_at')
            ->paginate(10)
            ->withQueryString();

        $totals = [
            'total'     => InventoryItem::count(),
            'low_stock' => InventoryItem::where('quantity', '<=', 5)->count(),
            'locations' => InventoryItem::whereNotNull('store_location')->distinct('store_location')->count('store_location'),
        ];

        return view('inventory.items.index', compact('items', 'totals', 'q', 'loc', 'from', 'to'));
    }

    public function create()
    {
        return view('inventory.items.create');
    }

    public function store(Request $request)
    {
        // Normalize quantity like "2,000" => 2000
        $request->merge([
            'quantity' => $request->filled('quantity')
                ? (int) preg_replace('/[^\d\-]/', '', (string) $request->input('quantity'))
                : null,
        ]);

        $data = $request->validate([
            'item_no'             => ['required','string','max:255'],
            'name'                => ['required','string','max:255'],
            'description'         => ['nullable','string','max:1000'],
            'unit_of_measurement' => ['required','string','max:255'],
            'quantity'            => ['required','integer','min:0'],
            'store_location'      => ['required','string','max:255'],
            'in_date'             => ['required','date'],
        ]);

        try {
            // App-level duplicate guard (adjust to your business rule)
            $exists = InventoryItem::where('item_no', $data['item_no'])
                        ->where('name', $data['name'])
                        ->exists();
            if ($exists) {
                return back()
                    ->withInput()
                    ->with('error', 'Duplicate item: the same Item No & Name already exists.');
            }

            DB::transaction(function () use ($data) {
                $item = InventoryItem::create($data);

                // Log initial stock
                \App\Models\InventoryLog::create([
                    'inventory_item_id' => $item->id,
                    'user_id'           => auth()->id(),
                    'change_amount'     => $item->quantity,
                    'previous_quantity' => 0,
                    'new_quantity'      => $item->quantity,
                    'reason'            => 'created',
                    'remarks'           => 'Initial stock entry',
                ]);
            });

            return redirect()
                ->route('inventory.items.index')
                ->with('status', 'Item created successfully.');
        } catch (QueryException $e) {
            Log::error('InventoryItem store DB error', ['message' => $e->getMessage()]);
            return back()->withInput()->with('error', 'Database error: '.$e->getMessage());
        } catch (\Throwable $e) {
            Log::error('InventoryItem store failed', ['message' => $e->getMessage()]);
            return back()->withInput()->with('error', 'Could not save the item. Please try again.');
        }
    }

    public function edit(InventoryItem $item)
    {
        return view('inventory.items.edit', compact('item'));
    }

    public function update(Request $request, InventoryItem $item)
    {
        $request->merge([
            'quantity' => $request->filled('quantity')
                ? (int) preg_replace('/[^\d\-]/', '', (string) $request->input('quantity'))
                : null,
        ]);

        $data = $request->validate([
            'item_no'             => ['required','string','max:255'],
            'name'                => ['required','string','max:255'],
            'description'         => ['nullable','string','max:1000'],
            'unit_of_measurement' => ['required','string','max:255'],
            'quantity'            => ['required','integer','min:0'],
            'store_location'      => ['required','string','max:255'],
            'in_date'             => ['required','date'],
        ]);

        try {
            // prevent updating to a duplicate of another record
            $exists = InventoryItem::where('item_no', $data['item_no'])
                ->where('name', $data['name'])
                ->where('id', '!=', $item->id)
                ->exists();
            if ($exists) {
                return back()
                    ->withInput()
                    ->with('error', 'Duplicate item: the same Item No & Name already exists.');
            }

            $newQuantity = $data['quantity'];
            unset($data['quantity']); // Handle quantity via Service

            DB::transaction(function () use ($item, $data, $newQuantity) {
                $item->update($data);
                $this->inventoryService->adjustQuantity($item, $newQuantity, 'updated', 'Manual update via inventory form');
            });

            return redirect()
                ->route('inventory.items.index')
                ->with('status', 'Item updated successfully.');
        } catch (\Throwable $e) {
            Log::error('InventoryItem update failed', ['message' => $e->getMessage(), 'id' => $item->id]);
            return back()->withInput()->with('error', 'Could not update the item. Please try again.');
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
                ->with('status', 'Item deleted.');
        } catch (\Throwable $e) {
            Log::error('InventoryItem delete failed', ['message' => $e->getMessage(), 'id' => $item->id]);
            return back()->with('error', 'Could not delete the item.');
        }
    }
}
