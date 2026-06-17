<x-layouts.app-shell title="Inventory Items">
    @php
        $isAdmin = Auth::user()->hasRole('Administrator') || Auth::user()->hasRole('Admin');
    @endphp

    <div class="space-y-5">
        <div class="flex flex-wrap items-center justify-between gap-3">
            <div>
                <h2 class="text-xl font-semibold">Stock Inventory</h2>
                <p class="text-sm text-base-content/60">Materials, tools and site equipment.</p>
            </div>
            @unless ($isAdmin)
                <a href="{{ route('inventory.items.create') }}" class="btn btn-primary btn-sm">
                    <x-mary-icon name="o-plus" class="h-4 w-4" /> Add item
                </a>
            @endunless
        </div>

        @if (session('success'))
            <div role="alert" class="alert alert-success py-2 text-sm"><span>{{ session('success') }}</span></div>
        @endif
        @if (session('error'))
            <div role="alert" class="alert alert-error py-2 text-sm"><span>{{ session('error') }}</span></div>
        @endif

        {{-- KPIs --}}
        <div class="grid grid-cols-2 gap-4 sm:max-w-md">
            <div class="rounded-lg border border-base-300 border-l-4 border-l-success bg-base-100 px-4 py-3 shadow-sm">
                <div class="text-[11px] font-medium uppercase tracking-wide text-base-content/50">All Items</div>
                <div class="mt-1 text-2xl font-bold tabular-nums">{{ number_format($totals['total_items']) }}</div>
            </div>
            <div class="rounded-lg border border-base-300 border-l-4 border-l-warning bg-base-100 px-4 py-3 shadow-sm">
                <div class="text-[11px] font-medium uppercase tracking-wide text-base-content/50">Low Stock</div>
                <div class="mt-1 text-2xl font-bold tabular-nums text-warning">{{ $totals['low_stock_count'] }}</div>
            </div>
        </div>

        <div class="rounded-lg border border-base-300 bg-base-100 shadow-sm">
            {{-- Filters --}}
            <form action="{{ route('inventory.items.index') }}" method="GET" class="grid gap-3 border-b border-base-200 p-4 sm:grid-cols-2 lg:grid-cols-5">
                <select name="classification_id" class="select select-bordered select-sm w-full">
                    <option value="">All categories</option>
                    @foreach ($classifications as $cl)
                        <option value="{{ $cl->id }}" @selected(request('classification_id') == $cl->id)>{{ $cl->name }}</option>
                    @endforeach
                </select>
                <select name="store_location" class="select select-bordered select-sm w-full">
                    <option value="">All locations</option>
                    @foreach ($storeLocations as $location)
                        <option value="{{ $location }}" @selected(request('store_location') === $location)>{{ $location }}</option>
                    @endforeach
                </select>
                <select name="status" class="select select-bordered select-sm w-full">
                    <option value="">All status</option>
                    <option value="in_stock" @selected(request('status') === 'in_stock')>Stable</option>
                    <option value="low_stock" @selected(request('status') === 'low_stock')>Low stock</option>
                    <option value="out_of_stock" @selected(request('status') === 'out_of_stock')>Out of stock</option>
                </select>
                <label class="input input-bordered input-sm flex items-center gap-2">
                    <x-mary-icon name="o-magnifying-glass" class="h-4 w-4 opacity-40" />
                    <input type="text" name="q" value="{{ request('q') }}" placeholder="Name, ID…" class="grow" />
                </label>
                <div class="flex gap-2">
                    <button type="submit" class="btn btn-primary btn-sm flex-1">Filter</button>
                    <a href="{{ route('inventory.items.index') }}" class="btn btn-ghost btn-sm btn-square" title="Reset"><x-mary-icon name="o-arrow-path" class="h-4 w-4" /></a>
                </div>
            </form>

            {{-- Table --}}
            <div class="overflow-x-auto">
                <table class="table">
                    <thead>
                        <tr class="text-[11px] uppercase tracking-wide text-base-content/60">
                            <th>Item</th><th>Location</th><th class="text-center">Stock</th><th>Status</th><th class="text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($items as $item)
                            @php
                                $qty = $item->quantity;
                                $badge = $qty > 5 ? 'badge-success' : ($qty > 0 ? 'badge-warning' : 'badge-error');
                                $label = $qty > 5 ? 'Stable' : ($qty > 0 ? 'Low stock' : 'Out of stock');
                            @endphp
                            <tr class="hover:bg-base-200/40">
                                <td>
                                    <div class="flex items-center gap-3">
                                        <div class="grid h-9 w-9 shrink-0 place-items-center rounded-lg bg-primary/5 text-primary/70"><x-mary-icon name="o-cube" class="h-5 w-5" /></div>
                                        <div>
                                            <div class="font-medium leading-tight">{{ $item->name }}</div>
                                            <div class="flex flex-wrap items-center gap-1.5 text-xs text-base-content/50">
                                                <span>REF: {{ $item->item_no }}</span>
                                                @if ($item->classification)<span class="badge badge-ghost badge-xs">{{ $item->classification->name }}</span>@endif
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td class="text-sm text-base-content/70">{{ $item->store_location ?? '—' }}</td>
                                <td class="text-center">
                                    <div class="font-medium">{{ $item->quantity }}</div>
                                    <div class="text-xs text-base-content/40 uppercase">{{ $item->unit_of_measurement }}</div>
                                </td>
                                <td><span class="badge badge-sm {{ $badge }}">{{ $label }}</span></td>
                                <td>
                                    <div class="flex justify-end gap-2">
                                        @if ($item->quantity <= 0)
                                            <span class="text-xs text-base-content/40">Historical</span>
                                        @else
                                            <a href="{{ route('inventory.items.edit', $item) }}" class="btn btn-ghost btn-xs">Edit</a>
                                            <form action="{{ route('inventory.items.destroy', $item) }}" method="POST" onsubmit="return confirm('Archive this item?')">
                                                @csrf @method('DELETE')
                                                <button type="submit" class="btn btn-ghost btn-xs text-error">Delete</button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="5" class="py-10 text-center text-base-content/40">The inventory catalog is empty.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if ($items->hasPages())
                <div class="border-t border-base-200 p-4">{{ $items->withQueryString()->links() }}</div>
            @endif
        </div>
    </div>
</x-layouts.app-shell>
