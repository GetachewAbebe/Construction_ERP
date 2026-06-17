<x-layouts.app-shell title="Leave Approvals">
    <div class="space-y-5">
        <div class="flex items-center justify-between gap-3">
            <div>
                <h2 class="text-xl font-semibold">Leave Administration</h2>
                <p class="text-sm text-base-content/60">Review and adjudicate employee absence requests.</p>
            </div>
            <a href="{{ route('admin.dashboard') }}" class="btn btn-ghost btn-sm">
                <x-mary-icon name="o-arrow-left" class="h-4 w-4" /> Dashboard
            </a>
        </div>

        @if (session('success'))
            <div role="alert" class="alert alert-success py-2 text-sm"><span>{{ session('success') }}</span></div>
        @endif
        @if (session('error'))
            <div role="alert" class="alert alert-error py-2 text-sm"><span>{{ session('error') }}</span></div>
        @endif

        {{-- Pending --}}
        <div class="rounded-lg border border-base-300 bg-base-100 shadow-sm">
            <div class="flex items-center justify-between border-b border-base-200 px-5 py-3">
                <h3 class="font-semibold">Pending Adjudication</h3>
                <span class="badge badge-warning badge-sm">{{ $pending->total() }} active</span>
            </div>
            <div class="overflow-x-auto">
                <table class="table">
                    <thead>
                        <tr class="text-[11px] uppercase tracking-wide text-base-content/60">
                            <th>Personnel</th><th>Absence Period</th><th>Filed</th><th class="text-right">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($pending as $leave)
                            <tr>
                                <td>
                                    <div class="flex items-center gap-2">
                                        <span class="grid h-8 w-8 place-items-center rounded-full bg-base-300/60 text-[10px] font-semibold">{{ strtoupper(mb_substr(optional($leave->employee)->first_name ?? 'U', 0, 1)) }}</span>
                                        <div>
                                            <div class="text-sm font-medium">{{ optional($leave->employee)->name ?? 'Unknown' }}</div>
                                            <div class="text-xs text-base-content/50">ID #{{ optional($leave->employee)->id ?? '—' }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div class="text-sm font-medium">{{ optional($leave->start_date)->format('M d') }} — {{ optional($leave->end_date)->format('M d, Y') }}</div>
                                    <div class="text-xs text-base-content/50">{{ Str::limit($leave->reason, 40) }}</div>
                                </td>
                                <td class="whitespace-nowrap text-sm text-base-content/60">{{ optional($leave->created_at)->format('M d, Y') }}</td>
                                <td>
                                    <div class="flex justify-end gap-2">
                                        <form method="POST" action="{{ route('admin.requests.leave.approve', $leave) }}" onsubmit="return confirm('Authorize this leave request?')">
                                            @csrf
                                            <button type="submit" class="btn btn-primary btn-xs">Approve</button>
                                        </form>
                                        <form method="POST" action="{{ route('admin.requests.leave.reject', $leave) }}" onsubmit="return confirm('Decline this leave request?')">
                                            @csrf
                                            <button type="submit" class="btn btn-ghost btn-xs text-error">Reject</button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="4" class="py-12 text-center text-base-content/40">All leave requests have been adjudicated.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if ($pending->hasPages())
                <div class="border-t border-base-200 p-4">{{ $pending->links() }}</div>
            @endif
        </div>

        {{-- History --}}
        <div class="rounded-lg border border-base-300 bg-base-100 shadow-sm">
            <div class="border-b border-base-200 px-5 py-3">
                <h3 class="font-semibold">Authorization Archive</h3>
            </div>
            <div class="overflow-x-auto">
                <table class="table">
                    <thead>
                        <tr class="text-[11px] uppercase tracking-wide text-base-content/60">
                            <th>Personnel</th><th>Authorized Absence</th><th class="text-right">Authorized by</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($approved as $record)
                            <tr>
                                <td>
                                    <div class="text-sm font-medium">{{ optional($record->employee)->name ?? 'Unknown' }}</div>
                                    <div class="text-xs text-base-content/50">{{ Str::limit($record->reason, 50) }}</div>
                                </td>
                                <td>
                                    <span class="badge badge-ghost badge-sm">{{ optional($record->start_date)->format('M d') }} — {{ optional($record->end_date)->format('M d, Y') }}</span>
                                </td>
                                <td class="text-right">
                                    <div class="text-sm font-medium text-success">{{ optional($record->approver)->name ?? 'System' }}</div>
                                    <div class="text-xs text-base-content/50">{{ optional($record->approved_at)->format('M d, Y H:i') }}</div>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="3" class="py-10 text-center text-base-content/40">No historical approvals found.</td></tr>
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
