<x-layouts.app-shell title="Expenses">
    @php
        $projects = $projects ?? collect();
        $canApprove = Auth::user()->hasAnyRole(['Administrator', 'Admin', 'FinancialManager', 'Financial Manager']);
        $statusBadge = ['approved' => 'badge-success', 'rejected' => 'badge-error', 'pending' => 'badge-warning'];
    @endphp

    <div class="space-y-5">
        <div class="flex flex-wrap items-center justify-between gap-3">
            <div>
                <h2 class="text-xl font-semibold">Expenses</h2>
                <p class="text-sm text-base-content/60">Field expenditure requisitions.</p>
            </div>
            <a href="{{ route('finance.expenses.create') }}" class="btn btn-primary btn-sm">
                <x-mary-icon name="o-plus" class="h-4 w-4" /> New expense
            </a>
        </div>

        @if (session('success'))
            <div role="alert" class="alert alert-success py-2 text-sm"><span>{{ session('success') }}</span></div>
        @endif

        <div class="rounded-lg border border-base-300 bg-base-100 shadow-sm">
            <form action="{{ route('finance.expenses.index') }}" method="GET" class="grid gap-3 border-b border-base-200 p-4 sm:grid-cols-2 lg:grid-cols-5">
                <label class="input input-bordered input-sm flex items-center gap-2 lg:col-span-2">
                    <x-mary-icon name="o-magnifying-glass" class="h-4 w-4 opacity-40" />
                    <input type="text" name="q" value="{{ request('q') }}" placeholder="Description or category…" class="grow" />
                </label>
                <select name="project_id" class="select select-bordered select-sm w-full">
                    <option value="">All projects</option>
                    @foreach ($projects as $p)
                        <option value="{{ $p->id }}" @selected(request('project_id') == $p->id)>{{ $p->name }}</option>
                    @endforeach
                </select>
                <select name="status" class="select select-bordered select-sm w-full">
                    <option value="">All status</option>
                    <option value="pending" @selected(request('status') == 'pending')>Pending</option>
                    <option value="approved" @selected(request('status') == 'approved')>Approved</option>
                    <option value="rejected" @selected(request('status') == 'rejected')>Rejected</option>
                </select>
                <div class="flex gap-2">
                    <button type="submit" class="btn btn-primary btn-sm flex-1">Filter</button>
                    <a href="{{ route('finance.expenses.index') }}" class="btn btn-ghost btn-sm btn-square"><x-mary-icon name="o-arrow-path" class="h-4 w-4" /></a>
                </div>
            </form>

            <div class="overflow-x-auto">
                <table class="table">
                    <thead>
                        <tr class="text-[11px] uppercase tracking-wide text-base-content/60">
                            <th>Date / By</th><th>Project</th><th>Category</th><th class="text-center">Status</th><th class="text-right">Amount</th><th class="text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($expenses as $expense)
                            <tr class="hover:bg-base-200/40">
                                <td>
                                    <div class="font-medium">{{ \Carbon\Carbon::parse($expense->expense_date)->format('d M, Y') }}</div>
                                    <div class="text-xs text-base-content/50">{{ optional($expense->user)->name ?? 'Staff' }}</div>
                                </td>
                                <td>
                                    <div class="text-sm font-medium">{{ optional($expense->project)->name ?? 'Global' }}</div>
                                    <div class="max-w-[200px] truncate text-xs text-base-content/50">{{ $expense->description ?? '—' }}</div>
                                </td>
                                <td><span class="badge badge-ghost badge-sm">{{ ucfirst($expense->category) }}</span></td>
                                <td class="text-center"><span class="badge badge-sm {{ $statusBadge[$expense->status] ?? 'badge-warning' }}">{{ strtoupper($expense->status ?? 'pending') }}</span></td>
                                <td class="text-right font-semibold">ETB {{ number_format($expense->amount, 0) }}</td>
                                <td>
                                    <div class="flex justify-end gap-2">
                                        <a href="{{ route('finance.expenses.show', $expense) }}" class="btn btn-ghost btn-xs">{{ $expense->status === 'approved' ? 'Voucher' : 'View' }}</a>
                                        @if ($expense->status === 'pending')
                                            <a href="{{ route('finance.expenses.edit', $expense) }}" class="btn btn-outline btn-xs">Edit</a>
                                            @if ($canApprove)
                                                <form action="{{ route('finance.expenses.approve', $expense) }}" method="POST">
                                                    @csrf <button type="submit" class="btn btn-success btn-xs">Approve</button>
                                                </form>
                                            @endif
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="6" class="py-12 text-center text-base-content/40">No expenses found.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if ($expenses->hasPages())
                <div class="border-t border-base-200 p-4">{{ $expenses->withQueryString()->links() }}</div>
            @endif
        </div>
    </div>
</x-layouts.app-shell>
