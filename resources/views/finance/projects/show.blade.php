<x-layouts.app-shell title="Project Details">
    @php
        $statusBadge = [
            'active' => 'badge-success', 'operational' => 'badge-success', 'In Progress' => 'badge-success',
            'completed' => 'badge-info', 'Completed' => 'badge-info',
            'on_hold' => 'badge-warning', 'On Hold' => 'badge-warning',
            'cancelled' => 'badge-error', 'Cancelled' => 'badge-error',
        ];
        $remaining = $project->budget - $project->total_expenses;
    @endphp

    <div class="mx-auto max-w-4xl space-y-5">
        <div class="no-print flex items-center justify-between gap-3">
            <a href="{{ route('finance.projects.index') }}" class="btn btn-ghost btn-sm"><x-mary-icon name="o-arrow-left" class="h-4 w-4" /> Registry</a>
            <div class="flex gap-2">
                <a href="{{ route('finance.projects.edit', $project) }}" class="btn btn-outline btn-sm">Edit</a>
                <button onclick="window.print()" class="btn btn-primary btn-sm"><x-mary-icon name="o-printer" class="h-4 w-4" /> Report</button>
            </div>
        </div>

        <div id="voucher" class="rounded-xl border border-base-300 bg-base-100 p-8 shadow-sm">
            <div class="flex items-start justify-between border-b-2 border-base-content/80 pb-5">
                <div>
                    <div class="text-2xl font-extrabold tracking-tight text-secondary">NATANEM</div>
                    <div class="text-xs font-semibold uppercase tracking-widest text-base-content/50">Engineering &amp; Infrastructure</div>
                </div>
                <div class="text-right">
                    <div class="text-lg font-bold">SITE INTELLIGENCE</div>
                    <span class="badge {{ $statusBadge[$project->status] ?? 'badge-ghost' }} badge-sm mt-1">{{ strtoupper($project->status) }}</span>
                </div>
            </div>

            <div class="grid grid-cols-3 gap-4 py-5 text-sm">
                <div>
                    <div class="text-[11px] font-bold uppercase tracking-wide text-base-content/40">Site</div>
                    <div class="mt-1 font-semibold">{{ $project->name }}</div>
                </div>
                <div class="text-center">
                    <div class="text-[11px] font-bold uppercase tracking-wide text-base-content/40">Location</div>
                    <div class="mt-1 font-semibold">{{ $project->location ?? '—' }}</div>
                </div>
                <div class="text-right">
                    <div class="text-[11px] font-bold uppercase tracking-wide text-base-content/40">Timeline</div>
                    <div class="mt-1 font-semibold">{{ optional($project->start_date)->format('M d, Y') ?? 'TBA' }} → {{ optional($project->end_date)->format('M d, Y') ?? 'TBA' }}</div>
                </div>
            </div>

            {{-- Financials --}}
            <div class="grid grid-cols-3 gap-4">
                <div class="rounded-lg border-l-4 border-l-base-content bg-base-200/40 p-4">
                    <div class="text-[11px] uppercase tracking-wide text-base-content/50">Budget</div>
                    <div class="mt-1 text-xl font-bold">ETB {{ number_format($project->budget, 0) }}</div>
                </div>
                <div class="rounded-lg border-l-4 border-l-error bg-base-200/40 p-4">
                    <div class="text-[11px] uppercase tracking-wide text-base-content/50">Spent</div>
                    <div class="mt-1 text-xl font-bold text-error">ETB {{ number_format($project->total_expenses, 0) }}</div>
                </div>
                <div class="rounded-lg border-l-4 border-l-success bg-base-200/40 p-4">
                    <div class="text-[11px] uppercase tracking-wide text-base-content/50">Remaining</div>
                    <div class="mt-1 text-xl font-bold {{ $remaining < 0 ? 'text-error' : 'text-success' }}">ETB {{ number_format($remaining, 0) }}</div>
                </div>
            </div>

            <div class="mt-5">
                <h3 class="mb-2 border-l-4 border-base-content pl-3 font-bold">Scope</h3>
                <p class="rounded-lg border border-base-200 bg-base-200/40 p-3 text-sm text-base-content/70">{{ $project->description ?: 'No scope defined.' }}</p>
            </div>

            <div class="mt-5">
                <h3 class="mb-2 border-l-4 border-base-content pl-3 font-bold">Recent expenditures</h3>
                <div class="overflow-x-auto rounded-lg border border-base-200">
                    <table class="table table-sm">
                        <thead>
                            <tr class="text-[11px] uppercase tracking-wide text-base-content/60"><th>Ref</th><th>Category</th><th>Date</th><th class="text-right">Amount</th></tr>
                        </thead>
                        <tbody>
                            @forelse ($project->expenses->sortByDesc('expense_date')->take(12) as $expense)
                                <tr>
                                    <td class="font-mono text-xs">#{{ str_pad((string) $expense->id, 4, '0', STR_PAD_LEFT) }}</td>
                                    <td>
                                        <div class="font-medium">{{ ucfirst($expense->category) }}</div>
                                        <div class="max-w-xs truncate text-xs text-base-content/50">{{ $expense->description }}</div>
                                    </td>
                                    <td class="text-sm text-base-content/60">{{ $expense->expense_date->format('M d, Y') }}</td>
                                    <td class="text-right font-semibold">ETB {{ number_format($expense->amount, 0) }}</td>
                                </tr>
                            @empty
                                <tr><td colspan="4" class="py-6 text-center text-base-content/40">No transactions in this site ledger.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <style>
        @media print {
            body > aside, body > div > header, .no-print { display: none !important; }
            body > div { padding-left: 0 !important; }
            #voucher { border: none !important; box-shadow: none !important; }
        }
    </style>
    @endpush
</x-layouts.app-shell>
