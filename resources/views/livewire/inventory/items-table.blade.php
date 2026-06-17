@php
    $statusOptions = [
        ['id' => 'in_stock', 'name' => 'In stock'],
        ['id' => 'low_stock', 'name' => 'Low stock'],
        ['id' => 'out_of_stock', 'name' => 'Out of stock'],
    ];

    $stockBadge = function (int $qty) {
        if ($qty <= 0) return ['badge-error', 'Out of stock'];
        if ($qty <= 5) return ['badge-warning', 'Low stock'];
        return ['badge-success', 'In stock'];
    };
@endphp

<div>
    {{-- Stat cards --}}
    <div class="grid grid-cols-1 gap-4 sm:grid-cols-3 mb-5">
        <x-mary-stat title="Total items" :value="$totals['total']" icon="o-cube" color="text-primary" />
        <x-mary-stat title="Low stock" :value="$totals['low']" icon="o-exclamation-triangle" color="text-warning" />
        <x-mary-stat title="Out of stock" :value="$totals['out']" icon="o-x-circle" color="text-error" />
    </div>

    <x-mary-card>
        {{-- Toolbar --}}
        <div class="flex flex-col gap-3 lg:flex-row lg:items-end lg:justify-between">
            <div class="flex flex-col gap-3 sm:flex-row sm:items-end">
                <x-mary-input
                    label="Search"
                    placeholder="Item no, name or description…"
                    wire:model.live.debounce.400ms="search"
                    icon="o-magnifying-glass"
                    clearable
                    class="w-full sm:w-72" />

                <x-mary-select
                    label="Status"
                    placeholder="All"
                    :options="$statusOptions"
                    wire:model.live="status"
                    class="w-full sm:w-40" />

                <x-mary-select
                    label="Classification"
                    placeholder="All"
                    :options="$classifications"
                    wire:model.live="classificationId"
                    class="w-full sm:w-52" />

                @if ($search !== '' || $status !== '' || $classificationId)
                    <x-mary-button label="Clear" wire:click="clearFilters" class="btn-ghost btn-sm" icon="o-x-mark" />
                @endif
            </div>

            <x-mary-button label="New item" wire:click="create" class="btn-primary" icon="o-plus" />
        </div>

        {{-- Table --}}
        <div class="relative mt-5 overflow-x-auto">
            <div wire:loading.flex class="absolute inset-0 z-10 items-center justify-center bg-base-100/60">
                <span class="loading loading-spinner loading-lg text-primary"></span>
            </div>

            <table class="table">
                <thead>
                    <tr>
                        <th>Item</th>
                        <th>Classification</th>
                        <th class="text-center">Qty</th>
                        <th class="text-center">Available</th>
                        <th>Status</th>
                        <th>Location</th>
                        <th class="text-right">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($items as $item)
                        @php([$badge, $label] = $stockBadge((int) $item->quantity))
                        <tr wire:key="item-{{ $item->id }}">
                            <td>
                                <div class="font-medium">{{ $item->name }}</div>
                                <div class="text-xs text-base-content/50">{{ $item->item_no }}</div>
                            </td>
                            <td class="text-sm">{{ $item->classification?->name ?? '—' }}</td>
                            <td class="text-center">{{ $item->quantity }}</td>
                            <td class="text-center">{{ $item->available_quantity }}</td>
                            <td><span class="badge {{ $badge }} badge-sm">{{ $label }}</span></td>
                            <td class="text-sm text-base-content/70">{{ $item->store_location ?? '—' }}</td>
                            <td>
                                <div class="flex justify-end gap-2">
                                    <x-mary-button icon="o-pencil-square" wire:click="editItem({{ $item->id }})"
                                        class="btn-ghost btn-xs" tooltip="Edit" />
                                    <x-mary-button icon="o-trash" wire:click="deleteItem({{ $item->id }})"
                                        wire:confirm="Delete this item?" spinner="deleteItem({{ $item->id }})"
                                        class="btn-ghost btn-xs text-error" tooltip="Delete" />
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-10 text-center text-base-content/50">
                                No items match your filters.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-4">{{ $items->links() }}</div>
    </x-mary-card>

    {{-- Create / Edit modal --}}
    <x-mary-modal wire:model="showForm" :title="$editingId ? 'Edit item' : 'New item'" separator box-class="max-w-2xl">
        <div class="grid grid-cols-1 gap-3 sm:grid-cols-2">
            <x-mary-input label="Item no" wire:model="item_no" />
            <x-mary-input label="Name" wire:model="name" />
            <x-mary-select label="Classification" placeholder="None" :options="$classifications" wire:model="classification_id" />
            <x-mary-input label="Unit of measurement" wire:model="unit_of_measurement" placeholder="pcs, kg, m…" />
            <x-mary-input label="Quantity" type="number" min="0" wire:model="quantity" />
            <x-mary-input label="Store location" wire:model="store_location" />
            <x-mary-input label="In date" type="date" wire:model="in_date" />
            <div class="sm:col-span-2">
                <x-mary-textarea label="Description" wire:model="description" rows="2" />
            </div>
        </div>

        <x-slot:actions>
            <x-mary-button label="Cancel" wire:click="$set('showForm', false)" />
            <x-mary-button :label="$editingId ? 'Update' : 'Create'" wire:click="save" spinner="save" class="btn-primary" />
        </x-slot:actions>
    </x-mary-modal>
</div>
