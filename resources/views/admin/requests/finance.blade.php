<x-layouts.app-shell title="Expense Approvals">
    <div class="space-y-5">
        <div class="flex items-center justify-between gap-3">
            <div>
                <h2 class="text-xl font-semibold">Finance Administration</h2>
                <p class="text-sm text-base-content/60">Review and authorize field expenditure requests.</p>
            </div>
            <a href="{{ route('admin.dashboard') }}" class="btn btn-ghost btn-sm">
                <x-mary-icon name="o-arrow-left" class="h-4 w-4" /> Dashboard
            </a>
        </div>

        @if (session('status'))
            <div role="alert" class="alert alert-success py-2 text-sm"><span>{{ session('status') }}</span></div>
        @endif

        <div class="rounded-lg border border-base-300 bg-base-100 shadow-sm">
            <div class="flex items-center justify-between border-b border-base-200 px-5 py-3">
                <h3 class="font-semibold">Pending Expenditures</h3>
                <span class="badge badge-warning badge-sm">{{ $pendingExpenses->total() }} pending</span>
            </div>

            @if ($pendingExpenses->isEmpty())
                <div class="flex flex-col items-center gap-2 py-14 text-base-content/40">
                    <x-mary-icon name="o-check-badge" class="h-10 w-10" />
                    <p class="text-sm">All financial requisitions have been processed.</p>
                </div>
            @else
                <div class="overflow-x-auto">
                    <table class="table">
                        <thead>
                            <tr class="text-[11px] uppercase tracking-wide text-base-content/60">
                                <th>Site &amp; Category</th><th>Amount</th><th>Requester</th><th>Date</th><th class="text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($pendingExpenses as $expense)
                                <tr x-data="{ reject: false }" class="align-top">
                                    <td>
                                        <div class="font-medium">{{ $expense->project->name ?? 'Global Site' }}</div>
                                        <div class="mt-0.5"><span class="badge badge-ghost badge-sm">{{ strtoupper($expense->category) }}</span></div>
                                        @if ($expense->description)
                                            <div class="mt-1 text-xs text-base-content/50">{{ Str::limit($expense->description, 60) }}</div>
                                        @endif
                                    </td>
                                    <td class="whitespace-nowrap">
                                        <div class="font-semibold"><span class="text-xs font-normal text-base-content/50">ETB</span> {{ number_format($expense->amount, 0) }}</div>
                                        <div class="text-xs text-base-content/40">REF: {{ $expense->reference_no ?? 'N/A' }}</div>
                                    </td>
                                    <td>
                                        <div class="flex items-center gap-2">
                                            <span class="grid h-7 w-7 place-items-center rounded-full bg-base-300/60 text-[10px] font-semibold">{{ strtoupper(mb_substr($expense->user->first_name ?? 'U', 0, 1)) }}</span>
                                            <span class="text-sm">{{ $expense->user->name ?? 'System' }}</span>
                                        </div>
                                    </td>
                                    <td class="whitespace-nowrap text-sm text-base-content/60">{{ optional($expense->expense_date)->format('M d, Y') }}</td>
                                    <td>
                                        <div class="flex justify-end gap-2">
                                            <form method="POST" action="{{ route('admin.requests.finance.approve', $expense) }}">
                                                @csrf
                                                <button type="submit" class="btn btn-primary btn-xs">Authorize</button>
                                            </form>
                                            <button type="button" @click="reject = !reject" class="btn btn-ghost btn-xs text-error">Decline</button>
                                        </div>
                                        <div x-show="reject" x-transition class="mt-3 rounded-lg border border-base-200 bg-base-200/40 p-3 text-left" style="display:none">
                                            <form method="POST" action="{{ route('admin.requests.finance.reject', $expense) }}">
                                                @csrf
                                                <label class="mb-1.5 block text-xs font-medium uppercase tracking-wide text-base-content/50">Rejection reason</label>
                                                <textarea name="rejection_reason" rows="2" required placeholder="e.g. Missing receipt or wrong category…"
                                                          class="textarea textarea-bordered w-full text-sm"></textarea>
                                                <div class="mt-2 flex gap-2">
                                                    <button type="submit" class="btn btn-error btn-xs">Confirm rejection</button>
                                                    <button type="button" @click="reject = false" class="btn btn-ghost btn-xs">Cancel</button>
                                                </div>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="border-t border-base-200 p-4">{{ $pendingExpenses->links() }}</div>
            @endif
        </div>
    </div>
</x-layouts.app-shell>
