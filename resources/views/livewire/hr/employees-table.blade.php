@php
    $statusOptions = [
        ['id' => 'Active', 'name' => 'Active'],
        ['id' => 'Inactive', 'name' => 'Inactive'],
        ['id' => 'On Leave', 'name' => 'On Leave'],
    ];

    $headers = [
        ['key' => 'name', 'label' => 'Staff Member'],
        ['key' => 'department_rel.name', 'label' => 'Department'],
        ['key' => 'position_rel.title', 'label' => 'Position'],
        ['key' => 'phone', 'label' => 'Contact'],
        ['key' => 'status', 'label' => 'Status'],
        ['key' => 'hire_date', 'label' => 'Hire Date'],
        ['key' => 'actions', 'label' => 'Actions', 'sortable' => false],
    ];
@endphp

<div>
    {{-- Header Stat Cards --}}
    <div class="grid grid-cols-1 gap-4 sm:grid-cols-3 mb-6">
        <x-mary-stat title="Total Staff" :value="$totals['total']" icon="o-users" color="text-primary" />
        <x-mary-stat title="Active Employees" :value="$totals['active']" icon="o-check-circle" color="text-success" />
        <x-mary-stat title="Inactive / Resigned" :value="$totals['inactive']" icon="o-user-minus" color="text-base-content/50" />
    </div>

    <x-mary-card>
        {{-- Action Toolbar --}}
        <div class="flex flex-col gap-3 lg:flex-row lg:items-end lg:justify-between mb-5">
            <div class="flex flex-col gap-3 sm:flex-row sm:items-end flex-1">
                <x-mary-input
                    label="Search Staff"
                    placeholder="Name, email, phone..."
                    wire:model.live.debounce.300ms="search"
                    icon="o-magnifying-glass"
                    clearable
                    class="w-full sm:w-64" />

                <x-mary-select
                    label="Department"
                    placeholder="All Departments"
                    :options="$departments"
                    wire:model.live="departmentId"
                    class="w-full sm:w-48" />

                <x-mary-select
                    label="Status"
                    placeholder="All Statuses"
                    :options="$statusOptions"
                    wire:model.live="status"
                    class="w-full sm:w-40" />

                @if ($search !== '' || $status !== '' || $departmentId)
                    <x-mary-button label="Reset" wire:click="clearFilters" class="btn-ghost btn-sm" icon="o-x-mark" />
                @endif
            </div>

            <x-mary-button label="Add Employee" wire:click="create" class="btn-primary" icon="o-user-plus" />
        </div>

        {{-- Table --}}
        <div class="relative overflow-x-auto">
            <div wire:loading.flex class="absolute inset-0 z-10 items-center justify-center bg-base-100/60 backdrop-blur-[1px]">
                <span class="loading loading-spinner loading-lg text-primary"></span>
            </div>

            <table class="table table-zebra w-full">
                <thead>
                    <tr class="bg-base-200/60 text-xs uppercase text-base-content/70">
                        <th>Employee</th>
                        <th>Department</th>
                        <th>Position</th>
                        <th>Contact</th>
                        <th>Status</th>
                        <th>Hire Date</th>
                        <th class="text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="text-sm divide-y divide-base-200">
                    @forelse ($employees as $employee)
                        <tr class="hover">
                            <td>
                                <div class="flex items-center gap-3">
                                    <div class="avatar placeholder">
                                        <div class="bg-primary/10 text-primary rounded-full w-10 h-10 ring-1 ring-primary/20">
                                            <span class="text-xs font-bold">
                                                {{ mb_substr($employee->first_name ?? 'E', 0, 1) }}{{ mb_substr($employee->last_name ?? 'M', 0, 1) }}
                                            </span>
                                        </div>
                                    </div>
                                    <div>
                                        <div class="font-semibold text-base-content">{{ $employee->name }}</div>
                                        <div class="text-xs text-base-content/60">{{ $employee->email }}</div>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <span class="badge badge-ghost font-medium">
                                    {{ $employee->department }}
                                </span>
                            </td>
                            <td>
                                <span class="text-sm font-medium text-base-content/80">
                                    {{ $employee->position }}
                                </span>
                            </td>
                            <td>
                                <div class="text-xs font-mono text-base-content/70">
                                    {{ $employee->phone ?? '—' }}
                                </div>
                            </td>
                            <td>
                                @if ($employee->status === 'Active')
                                    <span class="badge badge-success badge-sm font-semibold gap-1">
                                        <span class="w-1.5 h-1.5 rounded-full bg-white"></span>
                                        Active
                                    </span>
                                @elseif ($employee->status === 'On Leave')
                                    <span class="badge badge-warning badge-sm font-semibold">
                                        On Leave
                                    </span>
                                @else
                                    <span class="badge badge-error badge-sm font-semibold">
                                        Inactive
                                    </span>
                                @endif
                            </td>
                            <td>
                                <span class="text-xs text-base-content/70">
                                    {{ $employee->hire_date ? $employee->hire_date->format('M d, Y') : '—' }}
                                </span>
                            </td>
                            <td class="text-right space-x-1">
                                <x-mary-button icon="o-pencil-square" wire:click="edit({{ $employee->id }})" class="btn-ghost btn-xs text-info" title="Edit Profile" />
                                <x-mary-button icon="o-trash" wire:click="delete({{ $employee->id }})" wire:confirm="Are you sure you want to move this employee record to trash?" class="btn-ghost btn-xs text-error" title="Delete Profile" />
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-8 text-center text-base-content/50">
                                <x-mary-icon name="o-users" class="w-12 h-12 mx-auto mb-2 opacity-30" />
                                <p class="font-medium">No employee records match your criteria.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        <div class="mt-4">
            {{ $employees->links() }}
        </div>
    </x-mary-card>

    {{-- Employee Form Modal --}}
    <x-mary-modal wire:model="showModal" :title="$editingId ? 'Edit Employee Profile' : 'Register New Employee'" class="backdrop-blur">
        <form wire:submit="save" class="space-y-4">
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <x-mary-input label="First Name *" wire:model="first_name" placeholder="First Name" />
                <x-mary-input label="Last Name *" wire:model="last_name" placeholder="Last Name" />
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <x-mary-input label="Corporate Email *" wire:model="email" type="email" placeholder="name@natanem.com" />
                <x-mary-input label="Phone Number" wire:model="phone" placeholder="+251 9XX XXX XXX" />
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <x-mary-select label="Department" :options="$departments" wire:model="department_id" placeholder="Select Department" />
                <x-mary-select label="Designation / Position" :options="$positions" wire:model="position_id" placeholder="Select Position" />
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <x-mary-input label="Hire Date" wire:model="hire_date" type="date" />
                <x-mary-input label="Base Salary (ETB)" wire:model="salary" type="number" step="0.01" placeholder="0.00" />
                <x-mary-select label="Status *" :options="$statusOptions" wire:model="employee_status" />
            </div>

            <x-slot:actions>
                <x-mary-button label="Cancel" @click="$wire.showModal = false" class="btn-ghost" />
                <x-mary-button label="Save Employee" type="submit" class="btn-primary" icon="o-check" spinner="save" />
            </x-slot:actions>
        </form>
    </x-mary-modal>
</div>
