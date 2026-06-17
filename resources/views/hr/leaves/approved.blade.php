<x-layouts.app-shell title="Authorized Absences">
    <div class="space-y-5">
        <div class="flex items-center justify-between gap-3">
            <div>
                <h2 class="text-xl font-semibold">Authorized Absence Logs</h2>
                <p class="text-sm text-base-content/60">History of approved leave transactions.</p>
            </div>
            <a href="{{ route('hr.leaves.index') }}" class="btn btn-ghost btn-sm">
                <x-mary-icon name="o-arrow-left" class="h-4 w-4" /> Active portfolio
            </a>
        </div>

        <div class="rounded-lg border border-base-300 bg-base-100 shadow-sm">
            <div class="overflow-x-auto">
                <table class="table">
                    <thead>
                        <tr class="text-[11px] uppercase tracking-wide text-base-content/60">
                            <th>Personnel</th><th>Absence span</th><th>Validator</th><th>Validated</th><th class="text-right">Outcome</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($approved as $row)
                            <tr class="hover:bg-base-200/40">
                                <td>
                                    <div class="font-medium">{{ $row->employee->name ?? '—' }}</div>
                                    <div class="text-xs text-base-content/50">#LV-{{ $row->id }}</div>
                                </td>
                                <td class="font-mono text-sm">{{ \Carbon\Carbon::parse($row->start_date)->format('Y-m-d') }} → {{ \Carbon\Carbon::parse($row->end_date)->format('Y-m-d') }}</td>
                                <td class="text-sm">{{ $row->approver->name ?? 'System' }}</td>
                                <td class="text-sm text-base-content/60">{{ $row->approved_at ? $row->approved_at->format('M d, Y H:i') : '—' }}</td>
                                <td class="text-right"><span class="badge badge-success badge-sm">Verified</span></td>
                            </tr>
                        @empty
                            <tr><td colspan="5" class="py-12 text-center text-base-content/40">No authorized absences yet.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if ($approved->hasPages())
                <div class="border-t border-base-200 p-4">{{ $approved->links() }}</div>
            @endif
        </div>
    </div>
</x-layouts.app-shell>
