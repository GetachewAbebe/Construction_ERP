@php
    $typeOptions = [
        ['id' => 'Excavator', 'name' => 'Excavator'],
        ['id' => 'Loader', 'name' => 'Wheel Loader / Bulldozer'],
        ['id' => 'Concrete Mixer', 'name' => 'Concrete Mixer Truck'],
        ['id' => 'Dump Truck', 'name' => 'Dump Truck / Tipper'],
        ['id' => 'Crane', 'name' => 'Tower / Mobile Crane'],
        ['id' => 'Generator', 'name' => 'Heavy Generator / Compressor'],
        ['id' => 'Roller', 'name' => 'Vibratory Roller / Compactor'],
        ['id' => 'Light Vehicle', 'name' => 'Pickup / Site Vehicle'],
    ];

    $statusOptions = [
        ['id' => 'operational', 'name' => 'Operational (Ready)'],
        ['id' => 'maintenance', 'name' => 'Under Maintenance'],
        ['id' => 'breakdown', 'name' => 'Breakdown Alert'],
        ['id' => 'idle', 'name' => 'Idle / Stored'],
    ];

    $projectOptions = $projects->map(fn ($p) => ['id' => $p->id, 'name' => $p->name])->toArray();
@endphp

<div>
    {{-- Header Fleet Stat Cards --}}
    <div class="grid grid-cols-1 gap-4 sm:grid-cols-4 mb-6">
        <x-mary-stat title="Total Fleet Assets" :value="$totals['total']" icon="o-truck" color="text-primary" />
        <x-mary-stat title="Operational" :value="$totals['operational']" icon="o-check-circle" color="text-success" />
        <x-mary-stat title="Under Maintenance" :value="$totals['maintenance']" icon="o-wrench" color="text-warning" />
        <x-mary-stat title="Breakdown Alerts" :value="$totals['breakdown']" icon="o-exclamation-triangle" color="text-error" />
    </div>

    <x-mary-card>
        {{-- Toolbar --}}
        <div class="flex flex-col gap-3 lg:flex-row lg:items-end lg:justify-between mb-5">
            <div class="flex flex-col gap-3 sm:flex-row sm:items-end flex-1">
                <x-mary-input
                    label="Search Machinery"
                    placeholder="Plate number, model name..."
                    wire:model.live.debounce.300ms="search"
                    icon="o-magnifying-glass"
                    clearable
                    class="w-full sm:w-64" />

                <x-mary-select
                    label="Equipment Type"
                    placeholder="All Types"
                    :options="$typeOptions"
                    wire:model.live="type"
                    class="w-full sm:w-48" />

                <x-mary-select
                    label="Assigned Site"
                    placeholder="All Projects"
                    :options="$projectOptions"
                    wire:model.live="projectId"
                    class="w-full sm:w-48" />

                <x-mary-select
                    label="Status"
                    placeholder="All Statuses"
                    :options="$statusOptions"
                    wire:model.live="status"
                    class="w-full sm:w-40" />

                @if ($search !== '' || $status !== '' || $type !== '' || $projectId)
                    <x-mary-button label="Reset" wire:click="clearFilters" class="btn-ghost btn-sm" icon="o-x-mark" />
                @endif
            </div>

            <x-mary-button label="Register Machinery" wire:click="create" class="btn-primary" icon="o-plus" />
        </div>

        {{-- Equipment Cards Grid --}}
        <div class="relative">
            <div wire:loading.flex class="absolute inset-0 z-10 items-center justify-center bg-base-100/60 backdrop-blur-[1px]">
                <span class="loading loading-spinner loading-lg text-primary"></span>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                @forelse ($equipmentList as $item)
                    <div class="bento-card p-4 flex flex-col justify-between">
                        <div>
                            <div class="flex items-start justify-between gap-2">
                                <div>
                                    <div class="font-bold text-sm text-base-content">{{ $item->name }}</div>
                                    <div class="text-[11px] font-mono text-base-content/50 font-medium">
                                        {{ $item->plate_number ?? 'UNTAGGED' }} · <span class="text-blue-600 dark:text-blue-400 font-sans font-semibold">{{ $item->type }}</span>
                                    </div>
                                </div>
                                @if ($item->status === 'operational')
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-bold status-pill-success">
                                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span> Ready
                                    </span>
                                @elseif ($item->status === 'maintenance')
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-bold status-pill-warning">
                                        <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span> Service
                                    </span>
                                @elseif ($item->status === 'breakdown')
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-bold status-pill-error">
                                        <span class="w-1.5 h-1.5 rounded-full bg-rose-500"></span> Breakdown
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-bold status-pill-neutral">
                                        Idle
                                    </span>
                                @endif
                            </div>

                            <div class="mt-3.5 p-2.5 rounded-lg bg-base-200/50 space-y-1.5 text-xs">
                                <div class="flex justify-between">
                                    <span class="text-base-content/50">Site Location:</span>
                                    <span class="font-semibold text-base-content truncate max-w-[140px]">{{ $item->project->name ?? 'Base Yard' }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-base-content/50">Operating Meter:</span>
                                    <span class="font-mono font-bold text-base-content">{{ number_format((float) $item->operating_hours, 1) }} Hours</span>
                                </div>
                                <div class="flex justify-between items-center">
                                    <span class="text-base-content/50">Service Threshold:</span>
                                    @if ($item->isServiceOverdue())
                                        <span class="px-1.5 py-0.2 rounded bg-rose-500/10 text-rose-600 text-[10px] font-bold">
                                            Overdue ({{ number_format((float) $item->next_service_hours, 0) }}h)
                                        </span>
                                    @else
                                        <span class="font-mono text-base-content/70">Next: {{ $item->next_service_hours ? number_format((float) $item->next_service_hours, 0) . 'h' : '—' }}</span>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <div class="mt-3.5 pt-2.5 border-t border-base-200 flex items-center justify-between gap-2">
                            <div class="flex gap-1.5">
                                <x-mary-button label="Log Service" wire:click="openLogModal({{ $item->id }})" class="btn-xs btn-primary text-xs" icon="o-wrench" />
                                <x-mary-button label="History" wire:click="viewHistory({{ $item->id }})" class="btn-xs btn-ghost text-xs" icon="o-clock" />
                            </div>
                            <x-mary-button icon="o-pencil-square" wire:click="edit({{ $item->id }})" class="btn-ghost btn-xs text-base-content/50 hover:text-base-content" title="Edit Parameters" />
                        </div>
                    </div>
                @empty
                    <div class="col-span-full py-12 text-center text-base-content/40">
                        <x-mary-icon name="o-truck" class="w-10 h-10 mx-auto mb-2 opacity-30" />
                        <p class="font-medium">No heavy machinery or vehicles found.</p>
                    </div>
                @endforelse
            </div>
        </div>

        {{-- Pagination --}}
        <div class="mt-6">
            {{ $equipmentList->links() }}
        </div>
    </x-mary-card>

    {{-- Machinery Form Modal --}}
    <x-mary-modal wire:model="showFormModal" :title="$editingId ? 'Edit Heavy Equipment Asset' : 'Register New Heavy Machinery'" class="backdrop-blur">
        <form wire:submit="save" class="space-y-4">
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <x-mary-input label="Model / Asset Name *" wire:model="name" placeholder="e.g. Caterpillar 320D Excavator" />
                <x-mary-input label="Plate / Serial Number" wire:model="plate_number" placeholder="e.g. ET-3-98765" />
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <x-mary-select label="Machinery Category *" :options="$typeOptions" wire:model="equipment_type" />
                <x-mary-select label="Assigned Site Project" :options="$projectOptions" wire:model="project_id" placeholder="Base Yard (Unassigned)" />
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <x-mary-select label="Status *" :options="$statusOptions" wire:model="equipment_status" />
                <x-mary-input label="Current Hours *" wire:model="operating_hours" type="number" step="0.1" placeholder="0.0" />
                <x-mary-input label="Next Service Target (Hours)" wire:model="next_service_hours" type="number" step="1" placeholder="250" />
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <x-mary-input label="Fuel Type" wire:model="fuel_type" placeholder="Diesel / Petrol / Electric" />
                <x-mary-input label="Acquisition Cost (ETB)" wire:model="purchase_cost" type="number" step="0.01" placeholder="0.00" />
            </div>

            <x-mary-textarea label="Technical Remarks / Specifications" wire:model="notes" placeholder="Engine number, bucket capacity, warranty..." rows="2" />

            <x-slot:actions>
                <x-mary-button label="Cancel" @click="$wire.showFormModal = false" class="btn-ghost" />
                <x-mary-button label="Save Asset" type="submit" class="btn-primary" icon="o-check" spinner="save" />
            </x-slot:actions>
        </form>
    </x-mary-modal>

    {{-- Log Maintenance Modal --}}
    <x-mary-modal wire:model="showLogModal" title="Log Equipment Maintenance & Hours" class="backdrop-blur">
        @if ($selectedEquipment)
            <div class="p-3 mb-4 rounded-xl bg-base-200/60 border border-base-300 flex justify-between items-center text-xs">
                <div>
                    <div class="font-bold text-sm text-base-content">{{ $selectedEquipment->name }}</div>
                    <div class="font-mono text-base-content/60">{{ $selectedEquipment->plate_number ?? 'UNTAGGED' }}</div>
                </div>
                <div class="text-right font-mono">
                    <div class="text-base-content/50">Current Meter</div>
                    <div class="font-bold text-primary">{{ number_format((float) $selectedEquipment->operating_hours, 1) }}h</div>
                </div>
            </div>

            <form wire:submit="saveLog" class="space-y-4">
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <x-mary-select
                        label="Log Event Type *"
                        :options="[
                            ['id' => 'service', 'name' => 'Preventive Service (Oil, Filters)'],
                            ['id' => 'repair', 'name' => 'Corrective Repair / Spare Parts'],
                            ['id' => 'hours_update', 'name' => 'Operating Hours Update'],
                            ['id' => 'fuel', 'name' => 'Fuel Refill'],
                            ['id' => 'breakdown', 'name' => 'Breakdown Incident Report'],
                        ]"
                        wire:model="log_type" />

                    <x-mary-input label="New Operating Hours" wire:model="hours_at_log" type="number" step="0.1" placeholder="Updated meter reading" />
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <x-mary-input label="Cost / Expense Incurred (ETB)" wire:model="cost" type="number" step="0.01" placeholder="0.00" />
                    <x-mary-input label="Event Date *" wire:model="logged_at" type="date" />
                </div>

                <x-mary-textarea label="Work Description & Spare Parts Replaced *" wire:model="description" placeholder="Specify replaced filters, engine oil grade, mechanic notes..." rows="3" />

                <x-slot:actions>
                    <x-mary-button label="Cancel" @click="$wire.showLogModal = false" class="btn-ghost" />
                    <x-mary-button label="Commit Log" type="submit" class="btn-primary" icon="o-check" spinner="saveLog" />
                </x-slot:actions>
            </form>
        @endif
    </x-mary-modal>

    {{-- History Inspection Modal --}}
    <x-mary-modal wire:model="showHistoryModal" title="Machinery Maintenance History" class="backdrop-blur max-w-2xl">
        @if ($selectedEquipment)
            <div class="space-y-4">
                <div class="flex justify-between items-center p-3 rounded-lg bg-base-200/50 text-xs">
                    <div>
                        <div class="font-bold text-sm">{{ $selectedEquipment->name }}</div>
                        <div class="text-base-content/60">{{ $selectedEquipment->plate_number ?? 'UNTAGGED' }} · {{ $selectedEquipment->type }}</div>
                    </div>
                    <div class="text-right">
                        <span class="badge badge-sm {{ $selectedEquipment->status_badge }}">{{ $selectedEquipment->status }}</span>
                    </div>
                </div>

                <div class="max-h-80 overflow-y-auto divide-y divide-base-200">
                    @forelse ($selectedEquipment->logs->sortByDesc('logged_at') as $log)
                        <div class="py-3 text-xs space-y-1">
                            <div class="flex justify-between items-center font-semibold">
                                <span class="badge badge-outline badge-sm capitalize">{{ str_replace('_', ' ', $log->log_type) }}</span>
                                <span class="font-mono text-base-content/60">{{ $log->logged_at ? $log->logged_at->format('M d, Y') : '—' }}</span>
                            </div>
                            <p class="text-base-content/80 italic">{{ $log->description }}</p>
                            <div class="flex justify-between text-base-content/50 pt-1 font-mono">
                                <span>Meter: {{ $log->hours_at_log ? number_format((float) $log->hours_at_log, 1) . 'h' : '—' }}</span>
                                <span>Cost: ETB {{ number_format((float) $log->cost, 2) }}</span>
                            </div>
                        </div>
                    @empty
                        <div class="py-6 text-center text-base-content/40">No service logs recorded for this machine yet.</div>
                    @endforelse
                </div>
            </div>
        @endif

        <x-slot:actions>
            <x-mary-button label="Close" @click="$wire.showHistoryModal = false" class="btn-ghost" />
        </x-slot:actions>
    </x-mary-modal>
</div>
