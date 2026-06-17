<x-layouts.app-shell title="Inventory Logs">
    <div class="space-y-5">
        <div class="flex items-center justify-between gap-3">
            <div>
                <h2 class="text-xl font-semibold">Inventory Audit Trail</h2>
                <p class="text-sm text-base-content/60">Historical record of stock movements.</p>
            </div>
            <a href="{{ route('inventory.items.index') }}" class="btn btn-ghost btn-sm">
                <x-mary-icon name="o-arrow-left" class="h-4 w-4" /> Inventory
            </a>
        </div>

        <div class="rounded-lg border border-base-300 bg-base-100 shadow-sm">
            <div class="flex items-center justify-between border-b border-base-200 px-5 py-3">
                <h3 class="font-semibold">Transaction Log</h3>
                <span class="badge badge-ghost badge-sm">{{ $logs->total() }} records</span>
            </div>
            <div class="overflow-x-auto">
                <table class="table">
                    <thead>
                        <tr class="text-[11px] uppercase tracking-wide text-base-content/60">
                            <th>When</th><th>Item</th><th>By</th><th class="text-center">Change</th><th class="text-center">Balance</th><th>Context</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($logs as $log)
                            <tr class="hover:bg-base-200/40">
                                <td class="whitespace-nowrap">
                                    <div class="text-sm font-medium">{{ $log->created_at->format('M d, Y') }}</div>
                                    <div class="text-xs text-base-content/50">{{ $log->created_at->format('H:i:s') }}</div>
                                </td>
                                <td>
                                    <div class="text-sm font-medium">{{ optional($log->item)->name ?? 'Archived item' }}</div>
                                    <div class="font-mono text-xs text-base-content/50">{{ optional($log->item)->item_no ?? '—' }}</div>
                                </td>
                                <td class="text-sm">{{ optional($log->user)->name ?? 'System' }}</td>
                                <td class="text-center">
                                    @if ($log->change_amount > 0)
                                        <span class="badge badge-success badge-sm">+{{ $log->change_amount }}</span>
                                    @elseif ($log->change_amount < 0)
                                        <span class="badge badge-error badge-sm">{{ $log->change_amount }}</span>
                                    @else
                                        <span class="badge badge-ghost badge-sm">0</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    <div class="font-semibold">{{ $log->new_quantity }}</div>
                                    <div class="text-xs text-base-content/40 line-through">{{ $log->previous_quantity }}</div>
                                </td>
                                <td>
                                    <span class="badge badge-ghost badge-sm capitalize">{{ str_replace('_', ' ', $log->reason) }}</span>
                                    @if ($log->remarks)
                                        <div class="mt-0.5 max-w-xs truncate text-xs text-base-content/50" title="{{ $log->remarks }}">{{ $log->remarks }}</div>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="6" class="py-12 text-center text-base-content/40">No activity logs recorded.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if ($logs->hasPages())
                <div class="border-t border-base-200 p-4">{{ $logs->links() }}</div>
            @endif
        </div>
    </div>
</x-layouts.app-shell>
