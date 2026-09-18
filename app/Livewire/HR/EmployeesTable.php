<?php

declare(strict_types=1);

namespace App\Livewire\HR;

use App\Models\Department;
use App\Models\Employee;
use App\Models\Position;
use Illuminate\View\View;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;
use Mary\Traits\Toast;

class EmployeesTable extends Component
{
    use Toast;
    use WithPagination;

    #[Url(as: 'q')]
    public string $search = '';

    #[Url]
    public string $status = '';

    #[Url]
    public ?int $departmentId = null;

    // Modal state
    public bool $showModal = false;

    public ?int $editingId = null;

    public string $first_name = '';

    public string $last_name = '';

    public string $email = '';

    public ?int $department_id = null;

    public ?int $position_id = null;

    public ?string $phone = null;

    public ?string $hire_date = null;

    public ?string $salary = null;

    public string $employee_status = 'Active';

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function updatedStatus(): void
    {
        $this->resetPage();
    }

    public function updatedDepartmentId(): void
    {
        $this->resetPage();
    }

    public function clearFilters(): void
    {
        $this->reset('search', 'status', 'departmentId');
        $this->resetPage();
    }

    /**
     * @return array<string, mixed>
     */
    protected function rules(): array
    {
        $uniqueEmail = $this->editingId
            ? 'unique:employees,email,'.$this->editingId
            : 'unique:employees,email';

        return [
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', $uniqueEmail],
            'department_id' => ['nullable', 'exists:departments,id'],
            'position_id' => ['nullable', 'exists:positions,id'],
            'phone' => ['nullable', 'string', 'max:50'],
            'hire_date' => ['nullable', 'date'],
            'salary' => ['nullable', 'numeric', 'min:0'],
            'employee_status' => ['required', 'in:Active,Inactive,On Leave'],
        ];
    }

    public function create(): void
    {
        $this->reset([
            'editingId',
            'first_name',
            'last_name',
            'email',
            'department_id',
            'position_id',
            'phone',
            'hire_date',
            'salary',
        ]);
        $this->employee_status = 'Active';
        $this->showModal = true;
    }

    public function edit(int $id): void
    {
        $emp = Employee::findOrFail($id);

        $this->editingId = $emp->id;
        $this->first_name = (string) $emp->first_name;
        $this->last_name = (string) $emp->last_name;
        $this->email = (string) $emp->email;
        $this->department_id = $emp->department_id;
        $this->position_id = $emp->position_id;
        $this->phone = $emp->phone;
        $this->hire_date = $emp->hire_date ? $emp->hire_date->format('Y-m-d') : null;
        $this->salary = $emp->salary ? (string) $emp->salary : null;
        $this->employee_status = $emp->status ?? 'Active';

        $this->showModal = true;
    }

    public function save(): void
    {
        $validated = $this->validate();

        $data = [
            'first_name' => $validated['first_name'],
            'last_name' => $validated['last_name'],
            'email' => $validated['email'],
            'department_id' => $validated['department_id'],
            'position_id' => $validated['position_id'],
            'phone' => $validated['phone'],
            'hire_date' => $validated['hire_date'],
            'salary' => $validated['salary'],
            'status' => $validated['employee_status'],
        ];

        if ($this->editingId) {
            $emp = Employee::findOrFail($this->editingId);
            $emp->update($data);
            $this->success('Staff profile updated successfully.');
        } else {
            Employee::create($data);
            $this->success('New employee successfully registered into the system.');
        }

        $this->showModal = false;
    }

    public function delete(int $id): void
    {
        $emp = Employee::findOrFail($id);
        $emp->delete();
        $this->warning("Employee record for {$emp->name} moved to trash.");
    }

    public function render(): View
    {
        $query = Employee::with(['department_rel', 'position_rel']);

        if ($this->search !== '') {
            $q = $this->search;
            $query->where(function ($sub) use ($q) {
                $sub->where('first_name', 'like', "%{$q}%")
                    ->orWhere('last_name', 'like', "%{$q}%")
                    ->orWhere('email', 'like', "%{$q}%")
                    ->orWhere('phone', 'like', "%{$q}%");
            });
        }

        if ($this->status !== '') {
            $query->where('status', $this->status);
        }

        if ($this->departmentId) {
            $query->where('department_id', $this->departmentId);
        }

        $employees = $query->orderBy('first_name')->paginate(15);

        $totals = [
            'total' => Employee::count(),
            'active' => Employee::where('status', 'Active')->count(),
            'inactive' => Employee::where('status', 'Inactive')->count(),
        ];

        $departments = Department::orderBy('name')->get(['id', 'name'])->map(fn ($d) => ['id' => $d->id, 'name' => $d->name])->toArray();
        $positions = Position::orderBy('title')->get(['id', 'title'])->map(fn ($p) => ['id' => $p->id, 'name' => $p->title])->toArray();

        return view('livewire.hr.employees-table', [
            'employees' => $employees,
            'totals' => $totals,
            'departments' => $departments,
            'positions' => $positions,
        ]);
    }
}
