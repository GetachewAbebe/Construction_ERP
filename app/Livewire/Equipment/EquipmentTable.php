<?php

declare(strict_types=1);

namespace App\Livewire\Equipment;

use App\Models\Equipment;
use App\Models\EquipmentLog;
use App\Models\Project;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;
use Mary\Traits\Toast;

class EquipmentTable extends Component
{
    use Toast;
    use WithPagination;

    #[Url(as: 'q')]
    public string $search = '';

    #[Url]
    public string $status = '';

    #[Url]
    public string $type = '';

    #[Url]
    public ?int $projectId = null;

    // Modals
    public bool $showFormModal = false;

    public bool $showLogModal = false;

    public bool $showHistoryModal = false;

    public ?Equipment $selectedEquipment = null;

    public ?int $editingId = null;

    // Form fields
    public string $name = '';

    public ?string $plate_number = null;

    public string $equipment_type = 'Excavator';

    public ?int $project_id = null;

    public string $equipment_status = 'operational';

    public float $operating_hours = 0.00;

    public ?float $next_service_hours = null;

    public string $fuel_type = 'Diesel';

    public ?string $purchase_date = null;

    public ?float $purchase_cost = null;

    public ?string $notes = null;

    // Log modal fields
    public string $log_type = 'service';

    public ?float $hours_at_log = null;

    public float $cost = 0.00;

    public ?string $description = null;

    public ?string $logged_at = null;

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function updatedStatus(): void
    {
        $this->resetPage();
    }

    public function updatedType(): void
    {
        $this->resetPage();
    }

    public function updatedProjectId(): void
    {
        $this->resetPage();
    }

    public function clearFilters(): void
    {
        $this->reset('search', 'status', 'type', 'projectId');
        $this->resetPage();
    }

    public function create(): void
    {
        $this->reset([
            'editingId',
            'name',
            'plate_number',
            'project_id',
            'operating_hours',
            'next_service_hours',
            'purchase_date',
            'purchase_cost',
            'notes',
        ]);
        $this->equipment_type = 'Excavator';
        $this->equipment_status = 'operational';
        $this->fuel_type = 'Diesel';
        $this->showFormModal = true;
    }

    public function edit(int $id): void
    {
        $eq = Equipment::findOrFail($id);
        $this->editingId = $eq->id;
        $this->name = $eq->name;
        $this->plate_number = $eq->plate_number;
        $this->equipment_type = $eq->type;
        $this->project_id = $eq->project_id;
        $this->equipment_status = $eq->status;
        $this->operating_hours = (float) $eq->operating_hours;
        $this->next_service_hours = $eq->next_service_hours ? (float) $eq->next_service_hours : null;
        $this->fuel_type = $eq->fuel_type;
        $this->purchase_date = $eq->purchase_date ? $eq->purchase_date->format('Y-m-d') : null;
        $this->purchase_cost = $eq->purchase_cost ? (float) $eq->purchase_cost : null;
        $this->notes = $eq->notes;

        $this->showFormModal = true;
    }

    /**
     * @return array<string, mixed>
     */
    protected function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'plate_number' => ['nullable', 'string', 'max:100'],
            'equipment_type' => ['required', 'string', 'max:100'],
            'project_id' => ['nullable', 'exists:projects,id'],
            'equipment_status' => ['required', 'in:operational,maintenance,breakdown,idle'],
            'operating_hours' => ['required', 'numeric', 'min:0'],
            'next_service_hours' => ['nullable', 'numeric', 'min:0'],
            'fuel_type' => ['required', 'string', 'max:50'],
            'purchase_date' => ['nullable', 'date'],
            'purchase_cost' => ['nullable', 'numeric', 'min:0'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ];
    }

    public function save(): void
    {
        $validated = $this->validate();

        $data = [
            'name' => $validated['name'],
            'plate_number' => $validated['plate_number'],
            'type' => $validated['equipment_type'],
            'project_id' => $validated['project_id'],
            'status' => $validated['equipment_status'],
            'operating_hours' => $validated['operating_hours'],
            'next_service_hours' => $validated['next_service_hours'],
            'fuel_type' => $validated['fuel_type'],
            'purchase_date' => $validated['purchase_date'],
            'purchase_cost' => $validated['purchase_cost'],
            'notes' => $validated['notes'],
        ];

        if ($this->editingId) {
            $eq = Equipment::findOrFail($this->editingId);
            $eq->update($data);
            $this->success('Machinery parameters updated successfully.');
        } else {
            Equipment::create($data);
            $this->success('New heavy machinery registered into company fleet.');
        }

        $this->showFormModal = false;
    }

    public function openLogModal(int $id): void
    {
        $this->selectedEquipment = Equipment::findOrFail($id);
        $this->hours_at_log = (float) $this->selectedEquipment->operating_hours;
        $this->log_type = 'service';
        $this->cost = 0.00;
        $this->description = null;
        $this->logged_at = now()->format('Y-m-d');
        $this->showLogModal = true;
    }

    public function saveLog(): void
    {
        $this->validate([
            'log_type' => ['required', 'in:service,repair,fuel,hours_update,breakdown'],
            'hours_at_log' => ['nullable', 'numeric', 'min:0'],
            'cost' => ['required', 'numeric', 'min:0'],
            'description' => ['required', 'string', 'max:1000'],
            'logged_at' => ['required', 'date'],
        ]);

        if (! $this->selectedEquipment) {
            return;
        }

        EquipmentLog::create([
            'equipment_id' => $this->selectedEquipment->id,
            'user_id' => Auth::id(),
            'log_type' => $this->log_type,
            'hours_at_log' => $this->hours_at_log,
            'cost' => $this->cost,
            'description' => $this->description,
            'logged_at' => $this->logged_at,
        ]);

        // Update machinery hours & status if hours or service was recorded
        if ($this->hours_at_log && $this->hours_at_log > (float) $this->selectedEquipment->operating_hours) {
            $this->selectedEquipment->operating_hours = $this->hours_at_log;
        }

        if ($this->log_type === 'service') {
            // Extend next service interval by 250 hours if applicable
            $currentHours = (float) $this->selectedEquipment->operating_hours;
            $this->selectedEquipment->next_service_hours = $currentHours + 250.00;
            $this->selectedEquipment->status = 'operational';
        } elseif ($this->log_type === 'breakdown') {
            $this->selectedEquipment->status = 'breakdown';
        }

        $this->selectedEquipment->save();

        $this->success('Maintenance event and operational hours recorded.');
        $this->showLogModal = false;
    }

    public function viewHistory(int $id): void
    {
        $this->selectedEquipment = Equipment::with(['logs.user', 'project'])->findOrFail($id);
        $this->showHistoryModal = true;
    }

    public function render(): View
    {
        $query = Equipment::with(['project']);

        if ($this->search !== '') {
            $q = $this->search;
            $query->where(function ($sub) use ($q) {
                $sub->where('name', 'like', "%{$q}%")
                    ->orWhere('plate_number', 'like', "%{$q}%")
                    ->orWhere('type', 'like', "%{$q}%");
            });
        }

        if ($this->status !== '') {
            $query->where('status', $this->status);
        }

        if ($this->type !== '') {
            $query->where('type', $this->type);
        }

        if ($this->projectId) {
            $query->where('project_id', $this->projectId);
        }

        $equipmentList = $query->orderBy('name')->paginate(12);

        $projects = Project::orderBy('name')->get(['id', 'name']);

        $totals = [
            'total' => Equipment::count(),
            'operational' => Equipment::where('status', 'operational')->count(),
            'maintenance' => Equipment::where('status', 'maintenance')->count(),
            'breakdown' => Equipment::where('status', 'breakdown')->count(),
        ];

        return view('livewire.equipment.equipment-table', [
            'equipmentList' => $equipmentList,
            'projects' => $projects,
            'totals' => $totals,
        ]);
    }
}
