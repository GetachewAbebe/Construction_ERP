<?php

declare(strict_types=1);

namespace App\Http\Controllers\Operations;

use App\Http\Controllers\Controller;
use App\Models\Equipment;
use App\Models\EquipmentMaintenanceSchedule;
use App\Models\InventoryItem;
use App\Models\MaintenanceWorkOrder;
use App\Models\MaintenanceWorkOrderPart;
use App\Models\Project;
use App\Models\User;
use App\Services\QrCodeService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;
use Inertia\Inertia;
use Inertia\Response;

class MaintenanceWorkOrderController extends Controller
{
    public function index(Request $request): Response
    {
        $query = MaintenanceWorkOrder::query()
            ->with([
                'equipment:id,name,plate_number,type,operating_hours,next_service_hours,status',
                'project:id,name',
                'assignedMechanic:id,name',
                'expense:id,reference_no,amount',
            ]);

        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('work_order_no', 'ILIKE', "%{$search}%")
                  ->orWhere('title', 'ILIKE', "%{$search}%")
                  ->orWhereHas('equipment', function ($eq) use ($search) {
                      $eq->where('name', 'ILIKE', "%{$search}%")
                         ->orWhere('plate_number', 'ILIKE', "%{$search}%");
                  });
            });
        }

        if ($equipmentId = $request->input('equipment_id')) {
            $query->where('equipment_id', $equipmentId);
        }

        if ($projectId = $request->input('project_id')) {
            $query->where('project_id', $projectId);
        }

        if ($status = $request->input('status')) {
            $query->where('status', $status);
        }

        if ($orderType = $request->input('order_type')) {
            $query->where('order_type', $orderType);
        }

        if ($priority = $request->input('priority')) {
            $query->where('priority', $priority);
        }

        $workOrders = $query->orderByDesc('id')
            ->paginate(15)
            ->withQueryString();

        $stats = [
            'active_breakdowns' => MaintenanceWorkOrder::where('order_type', MaintenanceWorkOrder::TYPE_BREAKDOWN)
                ->whereIn('status', [MaintenanceWorkOrder::STATUS_PENDING, MaintenanceWorkOrder::STATUS_IN_PROGRESS])
                ->count(),
            'pending_orders' => MaintenanceWorkOrder::where('status', MaintenanceWorkOrder::STATUS_PENDING)->count(),
            'total_completed' => MaintenanceWorkOrder::where('status', MaintenanceWorkOrder::STATUS_COMPLETED)->count(),
            'total_maintenance_costs' => (float) MaintenanceWorkOrder::where('status', MaintenanceWorkOrder::STATUS_COMPLETED)->sum('total_cost'),
            'total_downtime_hours' => (float) MaintenanceWorkOrder::sum('downtime_hours'),
            'service_due_machines' => Equipment::whereNotNull('next_service_hours')
                ->whereRaw('operating_hours >= (next_service_hours - 25)')
                ->count(),
        ];

        return Inertia::render('Operations/Maintenance/Index', [
            'workOrders' => $workOrders,
            'equipmentList' => Equipment::select('id', 'name', 'plate_number', 'type', 'status', 'operating_hours', 'next_service_hours')->orderBy('name')->get(),
            'projects' => Project::select('id', 'name')->orderBy('name')->get(),
            'stats' => $stats,
            'filters' => $request->only(['search', 'equipment_id', 'project_id', 'status', 'order_type', 'priority']),
        ]);
    }

    public function create(Request $request): Response
    {
        $selectedEquipmentId = $request->input('equipment_id') ? (int) $request->input('equipment_id') : null;

        $equipmentList = Equipment::select('id', 'name', 'plate_number', 'type', 'status', 'operating_hours', 'next_service_hours', 'project_id')
            ->orderBy('name')
            ->get();

        $projects = Project::select('id', 'name', 'location')->orderBy('name')->get();
        $mechanics = User::select('id', 'name')->orderBy('name')->get();

        // Warehouse inventory spare parts & lubricants
        $stockParts = InventoryItem::query()
            ->select('id', 'item_no', 'name', 'quantity', 'unit_of_measurement')
            ->where('quantity', '>', 0)
            ->orderBy('name')
            ->get();

        $schedules = EquipmentMaintenanceSchedule::where('is_active', true)->get();

        return Inertia::render('Operations/Maintenance/Create', [
            'equipmentList' => $equipmentList,
            'projects' => $projects,
            'mechanics' => $mechanics,
            'stockParts' => $stockParts,
            'schedules' => $schedules,
            'selectedEquipmentId' => $selectedEquipmentId,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'equipment_id' => ['required', 'exists:equipment,id'],
            'project_id' => ['nullable', 'exists:projects,id'],
            'order_type' => ['required', 'string', 'in:preventive,breakdown,inspection,tire_tracks'],
            'priority' => ['required', 'string', 'in:routine,urgent,critical_downtime'],
            'title' => ['required', 'string', 'max:255'],
            'operating_hours' => ['required', 'numeric', 'min:0'],
            'fault_description' => ['nullable', 'string'],
            'assigned_mechanic_id' => ['nullable', 'exists:users,id'],
            'start_date' => ['nullable', 'date'],
            'labor_cost' => ['nullable', 'numeric', 'min:0'],
            'external_cost' => ['nullable', 'numeric', 'min:0'],
            'notes' => ['nullable', 'string'],
            'parts' => ['nullable', 'array'],
            'parts.*.inventory_item_id' => ['nullable', 'exists:inventory_items,id'],
            'parts.*.part_name' => ['required_with:parts', 'string', 'max:255'],
            'parts.*.part_number' => ['nullable', 'string', 'max:100'],
            'parts.*.quantity' => ['required_with:parts', 'numeric', 'min:0.01'],
            'parts.*.unit_of_measurement' => ['nullable', 'string', 'max:50'],
            'parts.*.unit_price' => ['nullable', 'numeric', 'min:0'],
        ]);

        $workOrder = DB::transaction(function () use ($validated) {
            $machine = Equipment::findOrFail($validated['equipment_id']);
            $workOrderNo = MaintenanceWorkOrder::generateWorkOrderNo();

            $order = MaintenanceWorkOrder::create([
                'work_order_no' => $workOrderNo,
                'equipment_id' => $machine->id,
                'project_id' => $validated['project_id'] ?? $machine->project_id,
                'order_type' => $validated['order_type'],
                'priority' => $validated['priority'],
                'status' => MaintenanceWorkOrder::STATUS_IN_PROGRESS,
                'title' => $validated['title'],
                'operating_hours' => $validated['operating_hours'],
                'fault_description' => $validated['fault_description'] ?? null,
                'assigned_mechanic_id' => $validated['assigned_mechanic_id'] ?? null,
                'start_date' => $validated['start_date'] ?? now(),
                'labor_cost' => $validated['labor_cost'] ?? 0.00,
                'external_cost' => $validated['external_cost'] ?? 0.00,
                'notes' => $validated['notes'] ?? null,
                'created_by' => Auth::id(),
            ]);

            // Update machine status to maintenance or breakdown
            if ($validated['order_type'] === MaintenanceWorkOrder::TYPE_BREAKDOWN) {
                $machine->status = 'breakdown';
            } else {
                $machine->status = 'maintenance';
            }
            $machine->save();

            // Create parts
            if (! empty($validated['parts'])) {
                foreach ($validated['parts'] as $partData) {
                    $part = new MaintenanceWorkOrderPart([
                        'maintenance_work_order_id' => $order->id,
                        'inventory_item_id' => $partData['inventory_item_id'] ?? null,
                        'part_name' => $partData['part_name'],
                        'part_number' => $partData['part_number'] ?? null,
                        'quantity' => $partData['quantity'],
                        'unit_of_measurement' => $partData['unit_of_measurement'] ?? 'pcs',
                        'unit_price' => $partData['unit_price'] ?? 0.00,
                    ]);
                    $part->calculateTotal();
                    $part->save();
                }
            }

            $order->recalculateTotals();

            return $order;
        });

        return redirect()->route('operations.maintenance.show', $workOrder->id)
            ->with('success', "Work Order {$workOrder->work_order_no} opened successfully for {$workOrder->equipment->name}.");
    }

    public function show(MaintenanceWorkOrder $workOrder): Response
    {
        $workOrder->load([
            'equipment:id,name,plate_number,type,operating_hours,next_service_hours,status,fuel_type,purchase_date',
            'project:id,name,location',
            'assignedMechanic:id,name',
            'creator:id,name',
            'completer:id,name',
            'expense:id,reference_no,amount,category,status',
            'parts.inventoryItem:id,item_no,name,quantity',
        ]);

        return Inertia::render('Operations/Maintenance/Show', [
            'workOrder' => $workOrder,
        ]);
    }

    public function complete(Request $request, MaintenanceWorkOrder $workOrder): RedirectResponse
    {
        if ($workOrder->status === MaintenanceWorkOrder::STATUS_COMPLETED) {
            return redirect()->back()->with('error', 'This work order is already completed and closed.');
        }

        $validated = $request->validate([
            'final_operating_hours' => ['nullable', 'numeric', 'min:0'],
            'downtime_hours' => ['nullable', 'numeric', 'min:0'],
            'work_performed' => ['required', 'string'],
        ]);

        DB::transaction(function () use ($workOrder, $validated) {
            $workOrder->complete(
                Auth::user(),
                isset($validated['final_operating_hours']) ? (float) $validated['final_operating_hours'] : null,
                isset($validated['downtime_hours']) ? (float) $validated['downtime_hours'] : null,
                $validated['work_performed']
            );
        });

        return redirect()->back()
            ->with('success', "Work Order {$workOrder->work_order_no} closed! Machine restored to operational status, inventory deducted, and project expense posted.");
    }

    public function destroy(MaintenanceWorkOrder $workOrder): RedirectResponse
    {
        if ($workOrder->status === MaintenanceWorkOrder::STATUS_COMPLETED) {
            return redirect()->back()->with('error', 'Cannot delete a completed maintenance work order with closed inventory and financial records.');
        }

        $workOrder->delete();

        return redirect()->route('operations.maintenance.index')
            ->with('success', "Work order {$workOrder->work_order_no} removed.");
    }

    public function print(MaintenanceWorkOrder $workOrder, QrCodeService $qr): View
    {
        $workOrder->load([
            'equipment',
            'project',
            'assignedMechanic',
            'creator',
            'completer',
            'parts.inventoryItem',
        ]);

        $verificationUrl = route('verify.maintenance-order', $workOrder->work_order_no);
        $qrCodeSvg = $qr->svg($verificationUrl, 100, '#0f172a');

        return view('prints.maintenance-work-order', [
            'workOrder' => $workOrder,
            'qrCodeSvg' => $qrCodeSvg,
            'verificationUrl' => $verificationUrl,
        ]);
    }
}
