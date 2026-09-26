<?php

declare(strict_types=1);

namespace App\Http\Controllers\Inventory;

use App\Http\Controllers\Controller;
use App\Models\InventoryItem;
use App\Models\Project;
use App\Models\PurchaseRequisition;
use App\Models\PurchaseRequisitionItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Inertia\Response;

class PurchaseRequisitionController extends Controller
{
    /**
     * Display a listing of purchase requisitions.
     */
    public function index(Request $request): Response
    {
        $q = trim((string) $request->input('q', ''));
        $status = $request->input('status');
        $priority = $request->input('priority');
        $projectId = $request->input('project_id');

        $query = PurchaseRequisition::with(['project', 'requester', 'approver', 'items'])
            ->when($q !== '', function ($query) use ($q) {
                $query->where(function ($sub) use ($q) {
                    $lower = mb_strtolower($q);
                    $sub->where(DB::raw('LOWER(requisition_no)'), 'like', "%{$lower}%")
                        ->orWhere(DB::raw('LOWER(purpose)'), 'like', "%{$lower}%")
                        ->orWhere(DB::raw('LOWER(remarks)'), 'like', "%{$lower}%");
                });
            })
            ->when($status, fn ($query) => $query->where('status', $status))
            ->when($priority, fn ($query) => $query->where('priority', $priority))
            ->when($projectId, fn ($query) => $query->where('project_id', $projectId))
            ->orderByDesc('id');

        $requisitions = $query->paginate(15)->withQueryString();

        $stats = [
            'total' => PurchaseRequisition::count(),
            'pending' => PurchaseRequisition::where('status', 'pending')->count(),
            'approved' => PurchaseRequisition::where('status', 'approved')->count(),
            'completed' => PurchaseRequisition::where('status', 'completed')->count(),
        ];

        $projects = Project::select('id', 'name', 'location')->orderBy('name')->get();

        return Inertia::render('Inventory/Requisitions/Index', [
            'requisitions' => $requisitions,
            'stats' => $stats,
            'projects' => $projects,
            'filters' => [
                'q' => $q,
                'status' => $status,
                'priority' => $priority,
                'project_id' => $projectId,
            ],
        ]);
    }

    /**
     * Show form for creating a new purchase requisition.
     */
    public function create()
    {
        $projects = Project::select('id', 'name', 'location')->orderBy('name')->get();
        $inventoryItems = InventoryItem::select('id', 'name', 'item_no', 'unit_of_measurement', 'quantity')
            ->orderBy('name')
            ->get();

        return Inertia::render('Inventory/Requisitions/Create', [
            'projects' => $projects,
            'inventoryItems' => $inventoryItems,
            'suggestedRequisitionNo' => PurchaseRequisition::generateRequisitionNo(),
        ]);
    }

    /**
     * Store a newly created purchase requisition.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'project_id' => 'nullable|exists:projects,id',
            'required_date' => 'required|date',
            'priority' => 'required|in:low,medium,high,urgent',
            'purpose' => 'nullable|string|max:255',
            'remarks' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.inventory_item_id' => 'nullable|exists:inventory_items,id',
            'items.*.item_name' => 'required|string|max:255',
            'items.*.unit_of_measurement' => 'required|string|max:50',
            'items.*.quantity_requested' => 'required|numeric|min:0.01',
            'items.*.estimated_unit_price' => 'nullable|numeric|min:0',
            'items.*.specifications' => 'nullable|string|max:500',
        ]);

        DB::transaction(function () use ($validated) {
            $requisition = PurchaseRequisition::create([
                'requisition_no' => PurchaseRequisition::generateRequisitionNo(),
                'project_id' => $validated['project_id'] ?? null,
                'requested_by' => Auth::id(),
                'required_date' => $validated['required_date'],
                'priority' => $validated['priority'],
                'status' => 'pending',
                'purpose' => $validated['purpose'] ?? null,
                'remarks' => $validated['remarks'] ?? null,
            ]);

            foreach ($validated['items'] as $item) {
                PurchaseRequisitionItem::create([
                    'purchase_requisition_id' => $requisition->id,
                    'inventory_item_id' => $item['inventory_item_id'] ?? null,
                    'item_name' => $item['item_name'],
                    'unit_of_measurement' => $item['unit_of_measurement'],
                    'quantity_requested' => $item['quantity_requested'],
                    'quantity_approved' => $item['quantity_requested'],
                    'estimated_unit_price' => $item['estimated_unit_price'] ?? 0,
                    'specifications' => $item['specifications'] ?? null,
                ]);
            }
        });

        return redirect()->route('inventory.requisitions.index')->with('success', 'Purchase requisition submitted successfully for review.');
    }

    /**
     * Display the specified purchase requisition.
     */
    public function show(PurchaseRequisition $requisition): Response
    {
        $requisition->load(['project', 'requester', 'approver', 'items.inventoryItem', 'purchaseOrders.vendor']);

        return Inertia::render('Inventory/Requisitions/Show', [
            'requisition' => $requisition,
        ]);
    }

    /**
     * Approve the requisition.
     */
    public function approve(Request $request, PurchaseRequisition $requisition)
    {
        if ($requisition->status !== 'pending') {
            return back()->with('error', 'Only pending requisitions can be approved.');
        }

        $validated = $request->validate([
            'items' => 'nullable|array',
            'items.*.id' => 'required|exists:purchase_requisition_items,id',
            'items.*.quantity_approved' => 'required|numeric|min:0',
        ]);

        DB::transaction(function () use ($requisition, $validated) {
            if (!empty($validated['items'])) {
                foreach ($validated['items'] as $itemData) {
                    PurchaseRequisitionItem::where('id', $itemData['id'])
                        ->where('purchase_requisition_id', $requisition->id)
                        ->update(['quantity_approved' => $itemData['quantity_approved']]);
                }
            }

            $requisition->update([
                'status' => 'approved',
                'approved_by' => Auth::id(),
                'approved_at' => now(),
            ]);
        });

        return back()->with('success', "Requisition {$requisition->requisition_no} approved successfully.");
    }

    /**
     * Reject the requisition.
     */
    public function reject(Request $request, PurchaseRequisition $requisition)
    {
        if ($requisition->status !== 'pending') {
            return back()->with('error', 'Only pending requisitions can be rejected.');
        }

        $validated = $request->validate([
            'rejection_reason' => 'required|string|max:1000',
        ]);

        $requisition->update([
            'status' => 'rejected',
            'approved_by' => Auth::id(),
            'approved_at' => now(),
            'rejection_reason' => $validated['rejection_reason'],
        ]);

        return back()->with('success', "Requisition {$requisition->requisition_no} rejected.");
    }

    /**
     * Remove the specified requisition.
     */
    public function destroy(PurchaseRequisition $requisition)
    {
        if (in_array($requisition->status, ['ordered', 'completed'])) {
            return back()->with('error', 'Cannot delete requisitions that have already been ordered.');
        }

        $requisition->delete();

        return redirect()->route('inventory.requisitions.index')->with('success', 'Purchase requisition deleted successfully.');
    }
}
