<?php

declare(strict_types=1);

namespace App\Http\Controllers\Operations;

use App\Http\Controllers\Controller;
use App\Models\Equipment;
use App\Models\EquipmentLog;
use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class EquipmentController extends Controller
{
    public function index(Request $request)
    {
        $query = Equipment::with('project');

        if ($request->filled('q')) {
            $q = $request->q;
            $query->where(function ($sub) use ($q) {
                $sub->where('name', 'like', "%{$q}%")
                    ->orWhere('plate_number', 'like', "%{$q}%")
                    ->orWhere('type', 'like', "%{$q}%");
            });
        }

        if ($request->filled('status')) {
            if ($request->status === 'service_due') {
                $query->whereNotNull('next_service_hours')
                    ->whereRaw('operating_hours >= (next_service_hours - 25)');
            } else {
                $query->where('status', $request->status);
            }
        }

        if ($request->filled('project_id')) {
            $query->where('project_id', $request->project_id);
        }

        $equipmentList = $query->orderBy('name')->paginate(12)->withQueryString();
        $projects = Project::orderBy('name')->get(['id', 'name']);

        $serviceDueCount = Equipment::whereNotNull('next_service_hours')
            ->whereRaw('operating_hours >= (next_service_hours - 25)')
            ->count();

        $totals = [
            'total' => Equipment::count(),
            'operational' => Equipment::where('status', 'operational')->count(),
            'maintenance' => Equipment::where('status', 'maintenance')->count(),
            'breakdown' => Equipment::where('status', 'breakdown')->count(),
            'service_due' => $serviceDueCount,
        ];

        $user = Auth::user();
        $isInventoryUser = $user && ($user->hasRole(['InventoryManager', 'Inventory Manager']) || str_contains(strtolower((string) $user->role), 'inventory'));
        $isInventoryContext = $request->routeIs('inventory.*') || $request->is('inventory/*') || $isInventoryUser;
        $canRegister = $isInventoryContext;

        return Inertia::render('Equipment/Index', [
            'equipmentList' => $equipmentList,
            'projects' => $projects,
            'totals' => $totals,
            'filters' => $request->only(['q', 'status', 'project_id']),
            'canRegister' => $canRegister,
            'isInventoryContext' => $isInventoryContext,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'plate_number' => ['nullable', 'string', 'max:100'],
            'type' => ['required', 'string', 'max:100'],
            'project_id' => ['nullable', 'exists:projects,id'],
            'status' => ['required', 'in:operational,maintenance,breakdown,idle'],
            'operating_hours' => ['required', 'numeric', 'min:0'],
            'next_service_hours' => ['nullable', 'numeric', 'min:0'],
            'fuel_type' => ['required', 'string', 'max:50'],
            'purchase_date' => ['nullable', 'date'],
            'purchase_cost' => ['nullable', 'numeric', 'min:0'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ]);

        Equipment::create($validated);

        $redirectRoute = ($request->routeIs('inventory.*') || $request->is('inventory/*'))
            ? 'inventory.equipment.index'
            : 'equipment.index';

        return redirect()->route($redirectRoute)
            ->with('success', 'Heavy machinery successfully registered into company fleet.');
    }

    public function update(Request $request, Equipment $equipment)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'plate_number' => ['nullable', 'string', 'max:100'],
            'type' => ['required', 'string', 'max:100'],
            'project_id' => ['nullable', 'exists:projects,id'],
            'status' => ['required', 'in:operational,maintenance,breakdown,idle'],
            'operating_hours' => ['required', 'numeric', 'min:0'],
            'next_service_hours' => ['nullable', 'numeric', 'min:0'],
            'fuel_type' => ['required', 'string', 'max:50'],
            'purchase_date' => ['nullable', 'date'],
            'purchase_cost' => ['nullable', 'numeric', 'min:0'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ]);

        $equipment->update($validated);

        $redirectRoute = ($request->routeIs('inventory.*') || $request->is('inventory/*'))
            ? 'inventory.equipment.index'
            : 'equipment.index';

        return redirect()->route($redirectRoute)
            ->with('success', 'Machinery fleet specifications updated successfully.');
    }

    public function destroy(Request $request, Equipment $equipment)
    {
        $equipment->delete();

        $redirectRoute = ($request->routeIs('inventory.*') || $request->is('inventory/*'))
            ? 'inventory.equipment.index'
            : 'equipment.index';

        return redirect()->route($redirectRoute)
            ->with('success', 'Machinery unit decommissioned from active telemetry.');
    }

    public function storeLog(Request $request, Equipment $equipment)
    {
        $validated = $request->validate([
            'log_type' => ['required', 'in:service,repair,fuel,hours_update,breakdown'],
            'hours_at_log' => ['nullable', 'numeric', 'min:0'],
            'cost' => ['required', 'numeric', 'min:0'],
            'description' => ['required', 'string', 'max:1000'],
            'logged_at' => ['required', 'date'],
        ]);

        EquipmentLog::create([
            'equipment_id' => $equipment->id,
            'user_id' => Auth::id(),
            'log_type' => $validated['log_type'],
            'hours_at_log' => $validated['hours_at_log'],
            'cost' => $validated['cost'],
            'description' => $validated['description'],
            'logged_at' => $validated['logged_at'],
        ]);

        if (!empty($validated['hours_at_log']) && (float) $validated['hours_at_log'] > (float) $equipment->operating_hours) {
            $equipment->operating_hours = $validated['hours_at_log'];
        }

        if ($validated['log_type'] === 'service') {
            $equipment->next_service_hours = (float) $equipment->operating_hours + 250.00;
            $equipment->status = 'operational';
        } elseif ($validated['log_type'] === 'breakdown') {
            $equipment->status = 'breakdown';
        }

        $equipment->save();

        return redirect()->route('equipment.index')
            ->with('success', 'Maintenance event logged successfully.');
    }

    /**
     * Export fleet telemetry and heavy machinery registry as CSV.
     */
    public function exportCsv(Request $request)
    {
        $query = Equipment::with('project');

        if ($request->filled('q')) {
            $q = $request->q;
            $query->where(function ($sub) use ($q) {
                $sub->where('name', 'like', "%{$q}%")
                    ->orWhere('plate_number', 'like', "%{$q}%")
                    ->orWhere('type', 'like', "%{$q}%");
            });
        }

        if ($request->filled('status')) {
            if ($request->status === 'service_due') {
                $query->whereNotNull('next_service_hours')
                    ->whereRaw('operating_hours >= (next_service_hours - 25)');
            } else {
                $query->where('status', $request->status);
            }
        }

        if ($request->filled('project_id')) {
            $query->where('project_id', $request->project_id);
        }

        $filename = 'equipment_fleet_telemetry_'.date('Y-m-d_His').'.csv';

        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0',
        ];

        $callback = function () use ($query) {
            $file = fopen('php://output', 'w');
            fputs($file, "\xEF\xBB\xBF");

            fputcsv($file, [
                'ID',
                'Machinery / Plant Name',
                'Plate / Serial No',
                'Type',
                'Assigned Project',
                'Status',
                'Operating Hours',
                'Next Service (Hours)',
                'Fuel Type',
                'Purchase Date',
                'Purchase Cost (ETB)',
            ]);

            $query->orderBy('name')->chunk(100, function ($equipments) use ($file) {
                foreach ($equipments as $eq) {
                    fputcsv($file, [
                        $eq->id,
                        $eq->name,
                        $eq->plate_number ?? 'N/A',
                        $eq->type,
                        $eq->project?->name ?? 'Unassigned / Yard',
                        strtoupper((string) $eq->status),
                        number_format((float) $eq->operating_hours, 2, '.', ''),
                        $eq->next_service_hours ?? 'N/A',
                        $eq->fuel_type ?? 'Diesel',
                        $eq->purchase_date ? (string) $eq->purchase_date : 'N/A',
                        $eq->purchase_cost ? number_format((float) $eq->purchase_cost, 2, '.', '') : '0.00',
                    ]);
                }
            });

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
