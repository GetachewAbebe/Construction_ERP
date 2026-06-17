<x-layouts.app-shell title="Asset Classifications">
    <div class="space-y-5">
        <div class="flex flex-wrap items-center justify-between gap-3">
            <div>
                <h2 class="text-xl font-semibold">Asset Classifications</h2>
                <p class="text-sm text-base-content/60">Hierarchical categories for inventory items.</p>
            </div>
            <a href="{{ route('inventory.asset-classifications.create') }}" class="btn btn-primary btn-sm">
                <x-mary-icon name="o-plus" class="h-4 w-4" /> New category
            </a>
        </div>

        @if (session('success'))
            <div role="alert" class="alert alert-success py-2 text-sm"><span>{{ session('success') }}</span></div>
        @endif
        @if (session('error'))
            <div role="alert" class="alert alert-error py-2 text-sm"><span>{{ session('error') }}</span></div>
        @endif

        <div class="rounded-lg border border-base-300 bg-base-100 shadow-sm">
            <div class="overflow-x-auto">
                <table class="table">
                    <thead>
                        <tr class="text-[11px] uppercase tracking-wide text-base-content/60">
                            <th>Category</th><th>Level</th><th>Full Path</th><th class="text-center">Items</th><th class="text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($classifications as $item)
                            <tr class="hover:bg-base-200/40">
                                <td>
                                    <div class="flex items-center gap-3">
                                        <div class="grid h-9 w-9 shrink-0 place-items-center rounded-lg bg-primary/10 text-primary">
                                            <x-mary-icon name="o-square-3-stack-3d" class="h-5 w-5" />
                                        </div>
                                        <div>
                                            <div class="font-medium leading-tight">{{ $item->name }}</div>
                                            <div class="text-xs text-base-content/50">CODE: {{ $item->code }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td><span class="badge badge-sm {{ $item->depth == 0 ? 'badge-primary' : 'badge-ghost' }}">Level {{ $item->depth + 1 }}</span></td>
                                <td class="text-sm text-base-content/60">{{ $item->full_nomenclature }}</td>
                                <td class="text-center font-medium tabular-nums">{{ number_format($item->recursive_asset_count) }}</td>
                                <td>
                                    <div class="flex justify-end gap-2">
                                        <a href="{{ route('inventory.asset-classifications.edit', $item) }}" class="btn btn-ghost btn-xs">Edit</a>
                                        <form action="{{ route('inventory.asset-classifications.destroy', $item) }}" method="POST" onsubmit="return confirm('Delete category {{ $item->name }}?')">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="btn btn-ghost btn-xs text-error">Delete</button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="5" class="py-10 text-center text-base-content/40">No classifications found.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if ($classifications->hasPages())
                <div class="border-t border-base-200 p-4">{{ $classifications->links() }}</div>
            @endif
        </div>
    </div>
</x-layouts.app-shell>
