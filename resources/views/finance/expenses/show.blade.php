<x-layouts.app-shell title="Expense Voucher">
    @php($canApprove = Auth::user()->hasAnyRole(['Administrator', 'Admin', 'FinancialManager', 'Financial Manager']))

    <div class="mx-auto max-w-3xl space-y-5">
        <div class="no-print flex items-center justify-between gap-3">
            <a href="{{ route('finance.expenses.index') }}" class="btn btn-ghost btn-sm"><x-mary-icon name="o-arrow-left" class="h-4 w-4" /> Requisitions</a>
            <div class="flex gap-2">
                @if ($canApprove && $expense->status === 'pending')
                    <form action="{{ route('finance.expenses.approve', $expense) }}" method="POST">@csrf<button type="submit" class="btn btn-success btn-sm">Authorize</button></form>
                @endif
                <button onclick="window.print()" class="btn btn-primary btn-sm"><x-mary-icon name="o-printer" class="h-4 w-4" /> Print</button>
            </div>
        </div>

        <div id="voucher" class="rounded-xl border border-base-300 bg-base-100 p-8 shadow-sm">
            <div class="flex items-start justify-between border-b-2 border-base-content/80 pb-5">
                <div>
                    <div class="text-2xl font-extrabold tracking-tight text-secondary">NATANEM</div>
                    <div class="text-xs font-semibold uppercase tracking-widest text-base-content/50">Engineering &amp; Infrastructure</div>
                    <div class="mt-2 text-xs text-base-content/50">Bole, Addis Ababa · finance@natanemeng.com</div>
                </div>
                <div class="text-right">
                    <div class="text-lg font-bold">EXPENSE VOUCHER</div>
                    <div class="mt-1 flex justify-end gap-1.5">
                        <span class="badge badge-ghost badge-sm">ID #{{ str_pad((string) $expense->id, 5, '0', STR_PAD_LEFT) }}</span>
                        <span class="badge badge-neutral badge-sm">REF {{ $expense->reference_no ?: 'GEN-'.$expense->id }}</span>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-3 gap-4 py-5">
                <div>
                    <div class="text-[11px] font-bold uppercase tracking-wide text-base-content/40">Issued</div>
                    <div class="mt-1 font-semibold">{{ $expense->expense_date->format('M d, Y') }}</div>
                </div>
                <div class="text-center">
                    <div class="text-[11px] font-bold uppercase tracking-wide text-base-content/40">Status</div>
                    <div class="mt-1"><span class="badge {{ $expense->status === 'approved' ? 'badge-success' : ($expense->status === 'rejected' ? 'badge-error' : 'badge-warning') }}">{{ strtoupper($expense->status) }}</span></div>
                </div>
                <div class="text-right">
                    <div class="text-[11px] font-bold uppercase tracking-wide text-base-content/40">Amount</div>
                    <div class="mt-1 text-2xl font-extrabold"><span class="text-sm font-normal text-base-content/50">ETB</span> {{ number_format($expense->amount, 0) }}</div>
                </div>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div class="rounded-lg border-l-4 border-l-base-content bg-base-200/40 p-4">
                    <div class="text-[11px] uppercase tracking-wide text-base-content/50">Project</div>
                    <div class="mt-1 font-semibold">{{ $expense->project->name ?? 'Global Operations' }}</div>
                    <div class="text-xs text-base-content/50">{{ $expense->project->location ?? 'Head Office' }}</div>
                </div>
                <div class="rounded-lg border-l-4 border-l-base-content bg-base-200/40 p-4">
                    <div class="text-[11px] uppercase tracking-wide text-base-content/50">Requester</div>
                    <div class="mt-1 font-semibold">{{ $expense->user->name ?? 'System' }}</div>
                </div>
            </div>

            <div class="mt-5 overflow-hidden rounded-lg border border-base-200">
                <table class="table">
                    <thead><tr class="text-[11px] uppercase tracking-wide text-base-content/60"><th>Classification &amp; narration</th><th class="text-right">Amount</th></tr></thead>
                    <tbody>
                        <tr>
                            <td>
                                <div class="font-semibold">{{ strtoupper($expense->category) }}</div>
                                <p class="mt-1 text-sm text-base-content/60">{{ $expense->description ?: 'Operational expenditure for field activities.' }}</p>
                            </td>
                            <td class="text-right font-semibold">ETB {{ number_format($expense->amount, 0) }}</td>
                        </tr>
                        <tr class="bg-base-200/50">
                            <td class="text-right font-bold uppercase text-base-content/60">Total</td>
                            <td class="text-right text-lg font-extrabold">ETB {{ number_format($expense->amount, 0) }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div class="mt-8 grid grid-cols-3 gap-4 border-t border-base-200 pt-6 text-center text-xs">
                <div><div class="mx-auto mb-2 w-28 border-t border-base-content/60"></div><div class="font-medium">{{ $expense->user->name ?? 'Staff' }}</div><div class="uppercase text-base-content/40">Requested by</div></div>
                <div><div class="mx-auto mb-2 w-28 border-t border-base-content/60"></div><div class="uppercase text-base-content/40">Verified by</div></div>
                <div>
                    @if ($expense->status === 'approved')<div class="mb-2 inline-block rotate-[-5deg] rounded border-2 border-success px-2 py-0.5 text-[10px] font-extrabold uppercase text-success">Authorized</div>@endif
                    <div class="mx-auto mb-2 w-28 border-t border-base-content/60"></div>
                    <div class="font-medium">{{ $expense->approvedBy->name ?? '—' }}</div>
                    <div class="uppercase text-base-content/40">Finance authorization</div>
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
