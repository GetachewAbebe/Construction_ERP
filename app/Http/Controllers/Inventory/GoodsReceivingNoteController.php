<?php

declare(strict_types=1);

namespace App\Http\Controllers\Inventory;

use App\Http\Controllers\Controller;
use App\Models\GoodsReceivingNote;
use App\Models\GoodsReceivingNoteItem;
use App\Models\InventoryItem;
use App\Models\InventoryLog;
use App\Models\Project;
use App\Models\PurchaseOrder;
use App\Models\PurchaseOrderItem;
use App\Models\PurchaseRequisition;
use App\Services\QrCodeService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Inertia\Response;

class GoodsReceivingNoteController extends Controller
{
    /**
     * Display a listing of goods receiving notes.
     */
    public function index(Request $request): Response
    {
        $q = trim((string) $request->input('q', ''));
        $projectId = $request->input('project_id');
        $vendorId = $request->input('vendor_id');

        $query = GoodsReceivingNote::with(['purchaseOrder', 'project', 'vendor', 'receiver', 'items'])
            ->when($q !== '', function ($query) use ($q) {
                $query->where(function ($sub) use ($q) {
                    $lower = mb_strtolower($q);
                    $sub->where(DB::raw('LOWER(grn_no)'), 'like', "%{$lower}%")
                        ->orWhere(DB::raw('LOWER(delivery_note_no)'), 'like', "%{$lower}%")
                        ->orWhereHas('purchaseOrder', fn ($po) => $po->where(DB::raw('LOWER(po_no)'), 'like', "%{$lower}%"));
                });
            })
            ->when($projectId, fn ($query) => $query->where('project_id', $projectId))
            ->when($vendorId, fn ($query) => $query->where('vendor_id', $vendorId))
            ->orderByDesc('id');

        $notes = $query->paginate(15)->withQueryString();

        $stats = [
            'total' => GoodsReceivingNote::count(),
            'today' => GoodsReceivingNote::whereDate('received_date', now()->toDateString())->count(),
            'this_month' => GoodsReceivingNote::whereMonth('received_date', now()->month)
                ->whereYear('received_date', now()->year)
                ->count(),
        ];

        $projects = Project::select('id', 'name', 'location')->orderBy('name')->get();

        return Inertia::render('Inventory/GoodsReceiving/Index', [
            'notes' => $notes,
            'stats' => $stats,
            'projects' => $projects,
            'filters' => [
                'q' => $q,
                'project_id' => $projectId,
                'vendor_id' => $vendorId,
            ],
        ]);
    }

    /**
     * Show the form for creating a new goods receiving note (receiving against a PO).
     */
    public function create(Request $request)
    {
        $poId = $request->input('po_id');
        $selectedPo = null;

        if ($poId) {
            $selectedPo = PurchaseOrder::with(['vendor', 'project', 'items.inventoryItem'])
                ->where('id', $poId)
                ->whereIn('status', ['issued', 'partially_received'])
                ->first();
        }

        // List active POs awaiting full receipt
        $openOrders = PurchaseOrder::with(['vendor:id,name', 'project:id,name'])
            ->whereIn('status', ['issued', 'partially_received'])
            ->orderByDesc('id')
            ->get();

        return Inertia::render('Inventory/GoodsReceiving/Create', [
            'openOrders' => $openOrders,
            'selectedPo' => $selectedPo,
            'suggestedGrnNo' => GoodsReceivingNote::generateGrnNo(),
        ]);
    }

    /**
     * Store a newly created goods receiving note and dynamically increment warehouse inventory.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'purchase_order_id' => 'required|exists:purchase_orders,id',
            'received_date' => 'required|date',
            'delivery_note_no' => 'nullable|string|max:100',
            'remarks' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.purchase_order_item_id' => 'required|exists:purchase_order_items,id',
            'items.*.inventory_item_id' => 'nullable|exists:inventory_items,id',
            'items.*.item_name' => 'required|string|max:255',
            'items.*.unit_of_measurement' => 'required|string|max:50',
            'items.*.quantity_delivered' => 'required|numeric|min:0',
            'items.*.quantity_accepted' => 'required|numeric|min:0',
            'items.*.quantity_rejected' => 'nullable|numeric|min:0',
            'items.*.rejection_reason' => 'nullable|string|max:255',
        ]);

        $grn = DB::transaction(function () use ($validated) {
            $po = PurchaseOrder::with('items')->findOrFail($validated['purchase_order_id']);

            $grn = GoodsReceivingNote::create([
                'grn_no' => GoodsReceivingNote::generateGrnNo(),
                'purchase_order_id' => $po->id,
                'project_id' => $po->project_id,
                'vendor_id' => $po->vendor_id,
                'received_by' => Auth::id(),
                'received_date' => $validated['received_date'],
                'delivery_note_no' => $validated['delivery_note_no'] ?? null,
                'status' => 'received',
                'remarks' => $validated['remarks'] ?? null,
            ]);

            foreach ($validated['items'] as $itemData) {
                $qtyAccepted = (float) $itemData['quantity_accepted'];
                $qtyDelivered = (float) $itemData['quantity_delivered'];
                $qtyRejected = isset($itemData['quantity_rejected']) ? (float) $itemData['quantity_rejected'] : 0.0;

                // Create GRN item record
                GoodsReceivingNoteItem::create([
                    'goods_receiving_note_id' => $grn->id,
                    'purchase_order_item_id' => $itemData['purchase_order_item_id'],
                    'inventory_item_id' => $itemData['inventory_item_id'] ?? null,
                    'item_name' => $itemData['item_name'],
                    'unit_of_measurement' => $itemData['unit_of_measurement'],
                    'quantity_delivered' => $qtyDelivered,
                    'quantity_accepted' => $qtyAccepted,
                    'quantity_rejected' => $qtyRejected,
                    'rejection_reason' => $itemData['rejection_reason'] ?? null,
                ]);

                // Update purchase order item quantity_received
                $poItem = PurchaseOrderItem::find($itemData['purchase_order_item_id']);
                if ($poItem) {
                    $poItem->increment('quantity_received', $qtyAccepted);
                }

                // If accepted > 0, update or insert into inventory_items & log it
                if ($qtyAccepted > 0) {
                    $invItem = null;
                    if (!empty($itemData['inventory_item_id'])) {
                        $invItem = InventoryItem::find($itemData['inventory_item_id']);
                    }

                    // If not directly linked by ID, attempt matching by item_name
                    if (!$invItem) {
                        $invItem = InventoryItem::where(DB::raw('LOWER(name)'), mb_strtolower(trim($itemData['item_name'])))->first();
                    }

                    if ($invItem) {
                        $prevQty = (int) $invItem->quantity;
                        $intAccepted = (int) round($qtyAccepted);
                        $newQty = $prevQty + $intAccepted;

                        $invItem->update(['quantity' => $newQty]);

                        InventoryLog::create([
                            'inventory_item_id' => $invItem->id,
                            'user_id' => Auth::id(),
                            'change_amount' => $intAccepted,
                            'previous_quantity' => $prevQty,
                            'new_quantity' => $newQty,
                            'reason' => 'grn_received',
                            'remarks' => "Stock receipt via {$grn->grn_no} (PO: {$po->po_no}, Delivery Note: " . ($validated['delivery_note_no'] ?? 'N/A') . ")",
                        ]);
                    }
                }
            }

            // Check if PO is now fully received or partially received
            $po->refresh();
            $po->load('items');

            $allReceived = true;
            $anyReceived = false;

            foreach ($po->items as $item) {
                if ((float) $item->quantity_received < (float) $item->quantity) {
                    $allReceived = false;
                }
                if ((float) $item->quantity_received > 0) {
                    $anyReceived = true;
                }
            }

            if ($allReceived) {
                $po->update(['status' => 'received']);
                if ($po->purchase_requisition_id) {
                    PurchaseRequisition::where('id', $po->purchase_requisition_id)->update(['status' => 'completed']);
                }
            } elseif ($anyReceived) {
                $po->update(['status' => 'partially_received']);
                if ($po->purchase_requisition_id) {
                    PurchaseRequisition::where('id', $po->purchase_requisition_id)->update(['status' => 'partially_received']);
                }
            }

            return $grn;
        });

        return redirect()->route('inventory.goods-receiving.show', $grn->id)
            ->with('success', "Goods Receiving Note {$grn->grn_no} processed and inventory updated successfully.");
    }

    /**
     * Display the specified goods receiving note.
     */
    public function show(GoodsReceivingNote $goodsReceiving): Response
    {
        $goodsReceiving->load([
            'purchaseOrder.vendor',
            'project',
            'vendor',
            'receiver',
            'items.inventoryItem',
            'items.purchaseOrderItem',
        ]);

        return Inertia::render('Inventory/GoodsReceiving/Show', [
            'note' => $goodsReceiving,
        ]);
    }

    /**
     * Render printable corporate Store Receiving Voucher with QR code.
     */
    public function print(GoodsReceivingNote $goodsReceiving, QrCodeService $qr)
    {
        $goodsReceiving->load(['purchaseOrder', 'project', 'vendor', 'receiver', 'items']);
        $verificationUrl = route('verify.goods-receiving', $goodsReceiving->id);
        $qrCodeSvg = $qr->svg($verificationUrl, 100, '#0f172a');

        return view('prints.goods-receiving-note', [
            'note' => $goodsReceiving,
            'qrCodeSvg' => $qrCodeSvg,
            'verificationUrl' => $verificationUrl,
        ]);
    }
}
