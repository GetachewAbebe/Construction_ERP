<x-layouts.app-shell title="Lending Voucher">
    @php
        $statusColor = $loan->status === 'returned' ? 'text-info' : ($loan->status === 'approved' ? 'text-success' : 'text-warning');
    @endphp

    <div class="mx-auto max-w-3xl space-y-5">
        {{-- Toolbar (hidden on print) --}}
        <div class="no-print flex items-center justify-between gap-3">
            <a href="{{ route('inventory.loans.index') }}" class="btn btn-ghost btn-sm">
                <x-mary-icon name="o-arrow-left" class="h-4 w-4" /> Back
            </a>
            <div class="flex gap-2">
                @if ($loan->status === 'approved' && ! $loan->returned_at)
                    <form action="{{ route('inventory.loans.mark-returned', $loan) }}" method="POST">
                        @csrf
                        <button type="submit" class="btn btn-success btn-sm">Mark returned</button>
                    </form>
                @endif
                <button onclick="window.print()" class="btn btn-primary btn-sm"><x-mary-icon name="o-printer" class="h-4 w-4" /> Print</button>
            </div>
        </div>

        {{-- Voucher --}}
        <div id="voucher" class="rounded-xl border border-base-300 bg-base-100 p-8 shadow-sm">
            <div class="flex items-start justify-between border-b-2 border-base-content/80 pb-5">
                <div>
                    <div class="text-2xl font-extrabold tracking-tight text-secondary">NATANEM</div>
                    <div class="text-xs font-semibold uppercase tracking-widest text-base-content/50">Inventory &amp; Assets Management</div>
                </div>
                <div class="text-right">
                    <div class="text-lg font-bold">LENDING AUTHORIZATION</div>
                    <div class="mt-1 inline-block rounded bg-base-content px-2 py-1 text-xs font-bold text-base-100">#LND-{{ str_pad((string) $loan->id, 5, '0', STR_PAD_LEFT) }}</div>
                </div>
            </div>

            <div class="grid grid-cols-2 gap-4 py-5">
                <div>
                    <div class="text-[11px] font-bold uppercase tracking-wide text-base-content/40">Accountable custodian</div>
                    <div class="mt-1 text-lg font-semibold">{{ $loan->employee->name ?? '—' }}</div>
                    <div class="text-xs text-base-content/50">{{ strtoupper($loan->employee->position ?? 'Staff') }} — {{ strtoupper($loan->employee->department ?? 'Operations') }}</div>
                </div>
                <div class="text-right">
                    <div class="text-[11px] font-bold uppercase tracking-wide text-base-content/40">Status</div>
                    <div class="mt-1 text-2xl font-extrabold {{ $statusColor }}">{{ strtoupper($loan->status) }}</div>
                    @if ($loan->returned_at)
                        <div class="text-xs font-semibold text-info">Returned {{ $loan->returned_at->format('d M, Y') }}</div>
                    @endif
                </div>
            </div>

            <h3 class="mb-3 border-l-4 border-base-content pl-3 font-bold">Asset Distribution</h3>
            <table class="table">
                <thead>
                    <tr class="text-[11px] uppercase tracking-wide text-base-content/60"><th>Item</th><th class="text-center">Qty</th><th class="text-right">Due</th></tr>
                </thead>
                <tbody>
                    <tr>
                        <td>
                            <div class="font-semibold">{{ $loan->item->name ?? '—' }}</div>
                            <div class="text-xs text-base-content/50">No: {{ $loan->item->item_no ?? 'N/A' }} · {{ $loan->item->store_location ?? 'Main Warehouse' }}</div>
                        </td>
                        <td class="text-center">
                            <div class="text-lg font-bold">{{ $loan->quantity }}</div>
                            <div class="text-xs text-base-content/40 uppercase">{{ $loan->item->unit_of_measurement ?? 'units' }}</div>
                        </td>
                        <td class="text-right font-semibold">{{ optional($loan->due_date)->format('d M, Y') ?? 'Permanent' }}</td>
                    </tr>
                </tbody>
            </table>

            @if ($loan->notes)
                <div class="mt-4">
                    <h3 class="mb-2 border-l-4 border-base-content pl-3 font-bold">Notes</h3>
                    <p class="rounded-lg border-l-4 border-base-300 bg-base-200/50 p-3 text-sm text-base-content/70">{{ $loan->notes }}</p>
                </div>
            @endif

            <div class="mt-8 grid grid-cols-3 gap-4 border-t-2 border-base-content/80 pt-6 text-center text-xs">
                <div>
                    <div class="mx-auto mb-2 w-32 border-t border-base-content/60"></div>
                    <div class="font-semibold uppercase text-base-content/40">Custodian</div>
                    <div class="mt-1 font-bold">{{ $loan->employee->name ?? '' }}</div>
                </div>
                <div>
                    <div class="mx-auto mb-2 w-32 border-t border-base-content/60"></div>
                    <div class="font-semibold uppercase text-base-content/40">Verification</div>
                </div>
                <div>
                    <div class="mx-auto mb-2 w-32 border-t border-base-content/60"></div>
                    <div class="font-semibold uppercase text-base-content/40">Inventory manager</div>
                    <div class="mt-1 font-bold">{{ optional($loan->approvedBy)->name ?? 'Authorized' }}</div>
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
