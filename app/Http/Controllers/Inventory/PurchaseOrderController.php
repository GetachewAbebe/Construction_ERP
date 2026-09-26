<?php

declare(strict_types=1);

namespace App\Http\Controllers\Inventory;

use App\Http\Controllers\Controller;
use App\Models\InventoryItem;
use App\Models\Project;
use App\Models\PurchaseOrder;
use App\Models\PurchaseOrderItem;
use App\Models\PurchaseRequisition;
use App\Models\Vendor;
use App\Services\QrCodeService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Inertia\Response;

class PurchaseOrderController extends Controller
{
    /**
     * Display a listing of purchase orders.
     */
    public function index(Request $request): Response
    {
        $q = trim((string) $request->input('q', ''));
        $status = $request->input('status');
        $vendorId = $request->input('vendor_id');
        $projectId = $request->input('project_id');

        $query = PurchaseOrder::with(['vendor', 'project', 'creator', 'items'])
            ->when($q !== '', function ($query) use ($q) {
                $query->where(function ($sub) use ($q) {
                    $lower = mb_strtolower($q);
                    $sub->where(DB::raw('LOWER(po_no)'), 'like', "%{$lower}%")
                        ->orWhere(DB::raw('LOWER(delivery_site)'), 'like', "%{$lower}%")
                        ->orWhereHas('vendor', fn ($v) => $v->where(DB::raw('LOWER(name)'), 'like', "%{$lower}%"));
                });
            })
            ->when($status, fn ($query) => $query->where('status', $status))
            ->when($vendorId, fn ($query) => $query->where('vendor_id', $vendorId))
            ->when($projectId, fn ($query) => $query->where('project_id', $projectId))
            ->orderByDesc('id');

        $orders = $query->paginate(15)->withQueryString();

        $stats = [
            'total' => PurchaseOrder::count(),
            'issued' => PurchaseOrder::where('status', 'issued')->count(),
            'partially_received' => PurchaseOrder::where('status', 'partially_received')->count(),
            'received' => PurchaseOrder::where('status', 'received')->count(),
            'total_value' => (float) PurchaseOrder::whereNotIn('status', ['cancelled'])->sum('total_amount'),
        ];

        $vendors = Vendor::select('id', 'name', 'code')->orderBy('name')->get();
        $projects = Project::select('id', 'name', 'location')->orderBy('name')->get();

        return Inertia::render('Inventory/PurchaseOrders/Index', [
            'orders' => $orders,
            'stats' => $stats,
            'vendors' => $vendors,
            'projects' => $projects,
            'filters' => [
                'q' => $q,
                'status' => $status,
                'vendor_id' => $vendorId,
                'project_id' => $projectId,
            ],
        ]);
    }

    /**
     * Show the form for creating a new purchase order.
     */
    public function create(Request $request)
    {
        $prId = $request->input('pr_id');
        $sourceRequisition = null;

        if ($prId) {
            $sourceRequisition = PurchaseRequisition::with(['project', 'items.inventoryItem'])
                ->where('id', $prId)
                ->where('status', 'approved')
                ->first();
        }

        $vendors = Vendor::where('is_active', true)->select('id', 'name', 'code', 'payment_terms', 'phone')->orderBy('name')->get();
        $projects = Project::select('id', 'name', 'location')->orderBy('name')->get();
        $inventoryItems = InventoryItem::select('id', 'name', 'item_no', 'unit_of_measurement')->orderBy('name')->get();

        $approvedRequisitions = PurchaseRequisition::where('status', 'approved')
            ->select('id', 'requisition_no', 'project_id', 'required_date')
            ->with('project:id,name')
            ->orderByDesc('id')
            ->get();

        return Inertia::render('Inventory/PurchaseOrders/Create', [
            'vendors' => $vendors,
            'projects' => $projects,
            'inventoryItems' => $inventoryItems,
            'approvedRequisitions' => $approvedRequisitions,
            'sourceRequisition' => $sourceRequisition,
            'suggestedPoNo' => PurchaseOrder::generatePoNo(),
        ]);
    }

    /**
     * Store a newly created purchase order.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'purchase_requisition_id' => 'nullable|exists:purchase_requisitions,id',
            'project_id' => 'nullable|exists:projects,id',
            'vendor_id' => 'required|exists:vendors,id',
            'order_date' => 'required|date',
            'delivery_due_date' => 'nullable|date|after_or_equal:order_date',
            'delivery_site' => 'nullable|string|max:255',
            'payment_terms' => 'nullable|string|max:255',
            'tax_rate' => 'nullable|numeric|min:0|max:100',
            'notes' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.inventory_item_id' => 'nullable|exists:inventory_items,id',
            'items.*.item_name' => 'required|string|max:255',
            'items.*.unit_of_measurement' => 'required|string|max:50',
            'items.*.quantity' => 'required|numeric|min:0.01',
            'items.*.unit_price' => 'required|numeric|min:0',
        ]);

        $po = DB::transaction(function () use ($validated) {
            $taxRate = isset($validated['tax_rate']) ? (float) $validated['tax_rate'] : 15.00;

            $subtotal = 0.0;
            $itemsData = [];
            foreach ($validated['items'] as $item) {
                $qty = (float) $item['quantity'];
                $price = (float) $item['unit_price'];
                $total = round($qty * $price, 2);
                $subtotal += $total;

                $itemsData[] = [
                    'inventory_item_id' => $item['inventory_item_id'] ?? null,
                    'item_name' => $item['item_name'],
                    'unit_of_measurement' => $item['unit_of_measurement'],
                    'quantity' => $qty,
                    'unit_price' => $price,
                    'total_price' => $total,
                    'quantity_received' => 0,
                ];
            }

            $taxAmount = round($subtotal * ($taxRate / 100), 2);
            $totalAmount = round($subtotal + $taxAmount, 2);

            $purchaseOrder = PurchaseOrder::create([
                'po_no' => PurchaseOrder::generatePoNo(),
                'purchase_requisition_id' => $validated['purchase_requisition_id'] ?? null,
                'project_id' => $validated['project_id'] ?? null,
                'vendor_id' => $validated['vendor_id'],
                'created_by' => Auth::id(),
                'order_date' => $validated['order_date'],
                'delivery_due_date' => $validated['delivery_due_date'] ?? null,
                'delivery_site' => $validated['delivery_site'] ?? null,
                'payment_terms' => $validated['payment_terms'] ?? null,
                'status' => 'issued',
                'subtotal' => $subtotal,
                'tax_rate' => $taxRate,
                'tax_amount' => $taxAmount,
                'total_amount' => $totalAmount,
                'notes' => $validated['notes'] ?? null,
            ]);

            foreach ($itemsData as $row) {
                $row['purchase_order_id'] = $purchaseOrder->id;
                PurchaseOrderItem::create($row);
            }

            // If created from a requisition, update requisition status to ordered
            if (!empty($validated['purchase_requisition_id'])) {
                PurchaseRequisition::where('id', $validated['purchase_requisition_id'])
                    ->update(['status' => 'ordered']);
            }

            return $purchaseOrder;
        });

        return redirect()->route('inventory.purchase-orders.show', $po->id)
            ->with('success', "Purchase order {$po->po_no} issued successfully.");
    }

    /**
     * Display the specified purchase order.
     */
    public function show(PurchaseOrder $purchaseOrder): Response
    {
        $purchaseOrder->load([
            'vendor',
            'project',
            'creator',
            'requisition',
            'items.inventoryItem',
            'goodsReceivingNotes.receiver',
        ]);

        return Inertia::render('Inventory/PurchaseOrders/Show', [
            'order' => $purchaseOrder,
        ]);
    }

    /**
     * Render printable corporate purchase order document with QR code.
     */
    public function print(PurchaseOrder $purchaseOrder, QrCodeService $qr)
    {
        $purchaseOrder->load(['vendor', 'project', 'creator', 'items', 'requisition']);
        $verificationUrl = route('verify.purchase-order', $purchaseOrder->id);
        $qrCodeSvg = $qr->svg($verificationUrl, 100, '#0f172a');

        return view('prints.purchase-order', [
            'order' => $purchaseOrder,
            'qrCodeSvg' => $qrCodeSvg,
            'verificationUrl' => $verificationUrl,
        ]);
    }

    /**
     * Cancel the purchase order.
     */
    public function cancel(PurchaseOrder $purchaseOrder)
    {
        if ($purchaseOrder->status === 'received') {
            return back()->with('error', 'Cannot cancel a purchase order that has already been fully received.');
        }

        $purchaseOrder->update(['status' => 'cancelled']);

        return back()->with('success', "Purchase order {$purchaseOrder->po_no} has been cancelled.");
    }
}
