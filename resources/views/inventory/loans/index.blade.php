<x-layouts.app-shell title="Tool & Equipment Loans">
    @php
        $statusMeta = [
            'pending'  => ['bg-warning/10 text-warning border-warning/20', 'Pending'],
            'approved' => ['bg-success/10 text-success border-success/20', 'Active'],
            'returned' => ['bg-info/10 text-info border-info/20', 'Returned'],
            'rejected' => ['bg-error/10 text-error border-error/20', 'Rejected'],
        ];
    @endphp

    <div class="space-y-5">
        <div class="flex flex-wrap items-center justify-between gap-3">
            <div>
                <h2 class="text-xl font-semibold">Tool &amp; Equipment Loans</h2>
                <p class="text-sm text-base-content/60">Item assignments and equipment tracking.</p>
            </div>
            <a href="{{ route('inventory.loans.create') }}" class="btn btn-primary btn-sm">
                <x-mary-icon name="o-plus" class="h-4 w-4" /> New loan
            </a>
        </div>

        @if (session('success'))
            <div role="alert" class="alert alert-success py-2 text-sm"><span>{{ session('success') }}</span></div>
        @endif
        @if (session('error'))
            <div role="alert" class="alert alert-error py-2 text-sm"><span>{{ session('error') }}</span></div>
        @endif

        <div class="rounded-lg border border-base-300 bg-base-100 shadow-sm">
            <form action="{{ route('inventory.loans.index') }}" method="GET" class="flex flex-col gap-3 border-b border-base-200 p-4 sm:flex-row sm:items-center">
                <label class="input input-bordered input-sm flex flex-1 items-center gap-2">
                    <x-mary-icon name="o-magnifying-glass" class="h-4 w-4 opacity-40" />
                    <input type="text" name="q" value="{{ request('q') }}" placeholder="Employee or item…" class="grow" />
                </label>
                <select name="status" class="select select-bordered select-sm w-full sm:w-44">
                    <option value="">All status</option>
                    <option value="pending" @selected(request('status') === 'pending')>Pending</option>
                    <option value="approved" @selected(request('status') === 'approved')>Active</option>
                    <option value="returned" @selected(request('status') === 'returned')>Returned</option>
                    <option value="rejected" @selected(request('status') === 'rejected')>Rejected</option>
                </select>
                <div class="flex gap-2">
                    <button type="submit" class="btn btn-primary btn-sm">Search</button>
                    <a href="{{ route('inventory.loans.index') }}" class="btn btn-ghost btn-sm btn-square"><x-mary-icon name="o-arrow-path" class="h-4 w-4" /></a>
                </div>
            </form>

            <div class="overflow-x-auto">
                <table class="table">
                    <thead>
                        <tr class="text-[11px] uppercase tracking-wide text-base-content/60">
                            <th>Item</th><th>Borrowed by</th><th class="text-center">Qty</th><th>Status</th><th>Period</th><th class="text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($loans as $loan)
                            @php([$pill, $label] = $statusMeta[$loan->status] ?? ['bg-base-200 text-base-content/60 border-base-300', ucfirst($loan->status)])
                            <tr class="hover:bg-base-200/40">
                                <td>
                                    <div class="font-medium">{{ optional($loan->item)->name ?? 'Unknown' }}</div>
                                    <div class="font-mono text-xs text-base-content/50">{{ optional($loan->item)->item_no ?? '—' }}</div>
                                </td>
                                <td>
                                    <div class="text-sm font-medium">{{ optional($loan->employee)->name ?? '—' }}</div>
                                    <div class="text-xs text-base-content/50">{{ optional(optional($loan->employee)->position_rel)->title ?? 'Staff' }}</div>
                                </td>
                                <td class="text-center font-medium">{{ $loan->quantity }}</td>
                                <td><span class="inline-flex rounded-full border px-2.5 py-0.5 text-xs font-medium {{ $pill }}">{{ $label }}</span></td>
                                <td class="text-sm text-base-content/60">
                                    <div>Issued: {{ optional($loan->requested_at)->format('d M') ?? '—' }}</div>
                                    @if ($loan->status === 'returned')
                                        <div class="text-success">Closed: {{ optional($loan->returned_at)->format('d M') }}</div>
                                    @elseif ($loan->due_date)
                                        <div>Due: {{ optional($loan->due_date)->format('d M') }}</div>
                                    @endif
                                </td>
                                <td>
                                    <div class="flex justify-end gap-2">
                                        @if ($loan->status === 'approved')
                                            <form action="{{ route('inventory.loans.mark-returned', $loan) }}" method="POST" onsubmit="return confirm('Acknowledge item return?')">
                                                @csrf
                                                <button type="submit" class="btn btn-outline btn-xs">Mark returned</button>
                                            </form>
                                        @elseif ($loan->status === 'pending')
                                            <a href="{{ route('inventory.loans.edit', $loan) }}" class="btn btn-ghost btn-xs">Edit</a>
                                        @else
                                            <a href="{{ route('inventory.loans.show', $loan) }}" class="btn btn-ghost btn-xs">View</a>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="6" class="py-12 text-center text-base-content/40">No loans recorded.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if ($loans->hasPages())
                <div class="border-t border-base-200 p-4">{{ $loans->withQueryString()->links() }}</div>
            @endif
        </div>
    </div>
</x-layouts.app-shell>
