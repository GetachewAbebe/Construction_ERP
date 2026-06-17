<x-layouts.app-shell title="Leave Management">
    @php
        $isAdmin = auth()->user()->hasRole('Administrator') || auth()->user()->hasRole('Admin');
        $statusBadge = ['Pending' => 'badge-warning', 'Approved' => 'badge-success', 'Rejected' => 'badge-error'];
        $active = ! request('view') || request('view') === 'active';
    @endphp

    <div class="space-y-5">
        <div class="flex flex-wrap items-center justify-between gap-3">
            <div>
                <h2 class="text-xl font-semibold">Leave Management</h2>
                <p class="text-sm text-base-content/60">Requests and authorization logs.</p>
            </div>
            <a href="{{ route('hr.leaves.create') }}" class="btn btn-primary btn-sm">
                <x-mary-icon name="o-calendar-days" class="h-4 w-4" /> New request
            </a>
        </div>

        @if (session('success'))
            <div role="alert" class="alert alert-success py-2 text-sm"><span>{{ session('success') }}</span></div>
        @endif
        @if (session('error'))
            <div role="alert" class="alert alert-error py-2 text-sm"><span>{{ session('error') }}</span></div>
        @endif

        {{-- Tabs --}}
        <div role="tablist" class="tabs tabs-boxed w-fit">
            <a href="{{ route('hr.leaves.index', ['view' => 'active']) }}" class="tab {{ $active ? 'tab-active' : '' }}">Active Portfolio</a>
            <a href="{{ route('hr.leaves.index', ['view' => 'logs']) }}" class="tab {{ request('view') === 'logs' ? 'tab-active' : '' }}">Execution Logs</a>
        </div>

        @if ($view === 'active')
            <div class="grid grid-cols-3 gap-4">
                <div class="rounded-lg border border-base-300 border-l-4 border-l-warning bg-base-100 px-4 py-3 shadow-sm">
                    <div class="text-[11px] font-medium uppercase tracking-wide text-base-content/50">Pending</div>
                    <div class="mt-1 text-2xl font-bold text-warning">{{ $pendingCount }}</div>
                </div>
                <div class="rounded-lg border border-base-300 border-l-4 border-l-success bg-base-100 px-4 py-3 shadow-sm">
                    <div class="text-[11px] font-medium uppercase tracking-wide text-base-content/50">Approved</div>
                    <div class="mt-1 text-2xl font-bold text-success">{{ $approvedCount }}</div>
                </div>
                <div class="rounded-lg border border-base-300 border-l-4 border-l-error bg-base-100 px-4 py-3 shadow-sm">
                    <div class="text-[11px] font-medium uppercase tracking-wide text-base-content/50">Rejected</div>
                    <div class="mt-1 text-2xl font-bold text-error">{{ $rejectedCount }}</div>
                </div>
            </div>
        @endif

        <div class="rounded-lg border border-base-300 bg-base-100 shadow-sm">
            <div class="overflow-x-auto">
                @if ($view === 'active')
                    <table class="table">
                        <thead>
                            <tr class="text-[11px] uppercase tracking-wide text-base-content/60">
                                <th>Personnel</th><th>Period</th><th>Status</th><th class="text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($requests as $leave)
                                <tr class="hover:bg-base-200/40">
                                    <td>
                                        <div class="font-medium">{{ optional($leave->employee)->name ?? '—' }}</div>
                                        <div class="text-xs text-base-content/50">{{ optional($leave->employee)->position ?? 'General' }}</div>
                                    </td>
                                    <td>
                                        <div class="text-sm font-medium">{{ \Carbon\Carbon::parse($leave->start_date)->format('M d') }} — {{ \Carbon\Carbon::parse($leave->end_date)->format('M d, Y') }}</div>
                                        <div class="text-xs text-base-content/50">{{ \Carbon\Carbon::parse($leave->start_date)->diffInDays(\Carbon\Carbon::parse($leave->end_date)) + 1 }} days</div>
                                    </td>
                                    <td><span class="badge badge-sm {{ $statusBadge[$leave->status] ?? 'badge-ghost' }}">{{ $leave->status }}</span></td>
                                    <td>
                                        <div class="flex justify-end gap-2">
                                            @if ($leave->status === 'Pending' && $isAdmin)
                                                <form method="POST" action="{{ route('admin.requests.leave.approve', $leave) }}" onsubmit="return confirm('Approve this request?')">
                                                    @csrf <button type="submit" class="btn btn-success btn-xs">Approve</button>
                                                </form>
                                                <form method="POST" action="{{ route('admin.requests.leave.reject', $leave) }}" onsubmit="return confirm('Reject this request?')">
                                                    @csrf <button type="submit" class="btn btn-ghost btn-xs text-error">Reject</button>
                                                </form>
                                            @endif
                                            <a href="{{ route('hr.leaves.show', $leave) }}" class="btn btn-ghost btn-xs">Details</a>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="4" class="py-12 text-center text-base-content/40">No active leave filings.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                @else
                    <table class="table">
                        <thead>
                            <tr class="text-[11px] uppercase tracking-wide text-base-content/60">
                                <th>Personnel</th><th>Absence</th><th>Validator</th><th>Validated</th><th class="text-right">Outcome</th>
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
                                <tr><td colspan="5" class="py-12 text-center text-base-content/40">No authorized absences.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                @endif
            </div>

            @php($paginationData = $view === 'active' ? $requests : $approved)
            @if ($paginationData->hasPages())
                <div class="border-t border-base-200 p-4">{{ $paginationData->withQueryString()->links() }}</div>
            @endif
        </div>
    </div>
</x-layouts.app-shell>
