<x-layouts.app-shell title="Vendors">
    <div class="space-y-5">
        <div class="flex flex-wrap items-center justify-between gap-3">
            <div>
                <h2 class="text-xl font-semibold">Vendor Registry</h2>
                <p class="text-sm text-base-content/60">Suppliers and service providers.</p>
            </div>
            <a href="{{ route('inventory.vendors.create') }}" class="btn btn-primary btn-sm">
                <x-mary-icon name="o-plus" class="h-4 w-4" /> Add vendor
            </a>
        </div>

        @if (session('success'))
            <div role="alert" class="alert alert-success py-2 text-sm"><span>{{ session('success') }}</span></div>
        @endif
        @if (session('error'))
            <div role="alert" class="alert alert-error py-2 text-sm"><span>{{ session('error') }}</span></div>
        @endif

        <div class="rounded-lg border border-base-300 bg-base-100 shadow-sm">
            <form action="{{ route('inventory.vendors.index') }}" method="GET" class="flex flex-col gap-3 border-b border-base-200 p-4 sm:flex-row sm:items-center">
                <label class="input input-bordered input-sm flex flex-1 items-center gap-2">
                    <x-mary-icon name="o-magnifying-glass" class="h-4 w-4 opacity-40" />
                    <input type="text" name="q" value="{{ $q ?? '' }}" placeholder="Name, code, contact or email…" class="grow" />
                </label>
                <button type="submit" class="btn btn-primary btn-sm">Search</button>
            </form>

            <div class="overflow-x-auto">
                <table class="table">
                    <thead>
                        <tr class="text-[11px] uppercase tracking-wide text-base-content/60">
                            <th>Vendor</th><th>Contact</th><th>Financials</th><th>Category</th><th class="text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($vendors as $vendor)
                            <tr class="hover:bg-base-200/40">
                                <td>
                                    <div class="flex items-center gap-3">
                                        <div class="grid h-9 w-9 shrink-0 place-items-center rounded-lg bg-primary/10 text-primary"><x-mary-icon name="o-building-office" class="h-5 w-5" /></div>
                                        <div>
                                            <div class="font-medium leading-tight">{{ $vendor->name }}</div>
                                            <div class="flex items-center gap-1.5 text-xs text-base-content/50">
                                                <span>ID: {{ $vendor->code }}</span>
                                                @if ($vendor->is_active)
                                                    <span class="badge badge-success badge-xs">Active</span>
                                                @else
                                                    <span class="badge badge-ghost badge-xs">Inactive</span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td class="text-sm">
                                    <div class="font-medium">{{ $vendor->contact_person ?: 'No contact' }}</div>
                                    <div class="text-xs text-base-content/50">{{ $vendor->email ?: '—' }}</div>
                                    <div class="text-xs text-base-content/50">{{ $vendor->phone ?: '—' }}</div>
                                </td>
                                <td class="text-sm">
                                    <div class="text-xs uppercase tracking-wide text-base-content/40">TIN/VAT</div>
                                    <div class="font-medium">{{ $vendor->tax_id ?: '—' }}</div>
                                    <div class="text-xs text-base-content/50">Terms: {{ $vendor->payment_terms ?: '—' }}</div>
                                </td>
                                <td><span class="badge badge-ghost badge-sm">{{ $vendor->category ?: 'General' }}</span></td>
                                <td>
                                    <div class="flex justify-end gap-2">
                                        <a href="{{ route('inventory.vendors.edit', $vendor) }}" class="btn btn-ghost btn-xs">Edit</a>
                                        <form action="{{ route('inventory.vendors.destroy', $vendor) }}" method="POST" onsubmit="return confirm('Remove {{ $vendor->name }}?')">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="btn btn-ghost btn-xs text-error">Delete</button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="5" class="py-10 text-center text-base-content/40">No vendors registered.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if ($vendors->hasPages())
                <div class="border-t border-base-200 p-4">{{ $vendors->withQueryString()->links() }}</div>
            @endif
        </div>
    </div>
</x-layouts.app-shell>
