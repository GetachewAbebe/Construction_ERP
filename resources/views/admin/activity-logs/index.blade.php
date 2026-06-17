<x-layouts.app-shell title="Activity Logs">
    @php
        $actionBadge = [
            'created' => 'badge-success', 'updated' => 'badge-info',
            'deleted' => 'badge-error', 'restored' => 'badge-warning',
        ];
    @endphp

    <div class="space-y-5">
        <div>
            <h2 class="text-xl font-semibold">System Audit Trail</h2>
            <p class="text-sm text-base-content/60">Chronological record of data modifications.</p>
        </div>

        <div class="rounded-lg border border-base-300 bg-base-100 shadow-sm">
            <div class="overflow-x-auto">
                <table class="table">
                    <thead>
                        <tr class="text-[11px] uppercase tracking-wide text-base-content/60">
                            <th>When</th><th>User</th><th>Action</th><th>Resource</th><th>Changes</th><th class="text-right">Source</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($logs as $log)
                            <tr x-data="{ open: false }" class="align-top">
                                <td class="whitespace-nowrap">
                                    <div class="text-sm font-medium">{{ $log->created_at->format('M d, Y') }}</div>
                                    <div class="text-xs text-base-content/50">{{ $log->created_at->format('H:i:s') }}</div>
                                </td>
                                <td>
                                    <div class="flex items-center gap-2">
                                        <span class="grid h-7 w-7 place-items-center rounded-full bg-base-300/60 text-[10px] font-semibold">{{ strtoupper(mb_substr($log->user->name ?? 'S', 0, 1)) }}</span>
                                        <div>
                                            <div class="text-sm">{{ $log->user->name ?? 'System' }}</div>
                                            <div class="text-xs text-base-content/50">{{ $log->user->role ?? '—' }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td><span class="badge badge-sm {{ $actionBadge[$log->action] ?? 'badge-ghost' }} capitalize">{{ $log->action }}</span></td>
                                <td>
                                    <div class="text-sm font-medium">{{ class_basename($log->model_type) }}</div>
                                    <div class="text-xs text-base-content/50">#{{ $log->model_id }}</div>
                                </td>
                                <td>
                                    @if ($log->action === 'updated' && $log->changes)
                                        <button type="button" @click="open = !open" class="btn btn-ghost btn-xs">Inspect</button>
                                        <div x-show="open" x-transition class="mt-2 space-y-1 rounded-lg bg-base-200/50 p-2 text-xs" style="display:none">
                                            @foreach (($log->changes['after'] ?? []) as $key => $value)
                                                @if ($key !== 'updated_at')
                                                    <div>
                                                        <span class="font-medium uppercase tracking-wide text-base-content/50">{{ str_replace('_', ' ', $key) }}:</span>
                                                        <span class="line-through opacity-60">{{ $log->changes['before'][$key] ?? '∅' }}</span>
                                                        →
                                                        <span class="font-medium">{{ $value }}</span>
                                                    </div>
                                                @endif
                                            @endforeach
                                        </div>
                                    @else
                                        <span class="text-xs text-base-content/40">—</span>
                                    @endif
                                </td>
                                <td class="text-right">
                                    <div class="text-xs text-base-content/50">{{ $log->ip_address }}</div>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="6" class="py-12 text-center text-base-content/40">The audit trail is empty.</td></tr>
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
