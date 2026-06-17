<x-layouts.app-shell title="Leave Record">
    @php
        $statusColor = $leave->status === 'Approved' ? 'text-success' : ($leave->status === 'Rejected' ? 'text-error' : 'text-warning');
    @endphp

    <div class="mx-auto max-w-3xl space-y-5">
        <div class="no-print flex items-center justify-between gap-3">
            <a href="{{ route('hr.leaves.index') }}" class="btn btn-ghost btn-sm">
                <x-mary-icon name="o-arrow-left" class="h-4 w-4" /> Back
            </a>
            <button onclick="window.print()" class="btn btn-primary btn-sm"><x-mary-icon name="o-printer" class="h-4 w-4" /> Print</button>
        </div>

        <div id="voucher" class="rounded-xl border border-base-300 bg-base-100 p-8 shadow-sm">
            <div class="flex items-start justify-between border-b-2 border-base-content/80 pb-5">
                <div>
                    <div class="text-2xl font-extrabold tracking-tight text-secondary">NATANEM</div>
                    <div class="text-xs font-semibold uppercase tracking-widest text-base-content/50">Human Resource Management</div>
                </div>
                <div class="text-right">
                    <div class="text-lg font-bold">LEAVE RECORD</div>
                    <div class="mt-1 inline-block rounded bg-base-content px-2 py-1 text-xs font-bold text-base-100">#LRQ-{{ str_pad((string) $leave->id, 5, '0', STR_PAD_LEFT) }}</div>
                </div>
            </div>

            <div class="grid grid-cols-2 gap-4 py-5">
                <div>
                    <div class="text-[11px] font-bold uppercase tracking-wide text-base-content/40">Employee</div>
                    <div class="mt-1 text-lg font-semibold">{{ $leave->employee->name ?? '—' }}</div>
                    <div class="text-xs text-base-content/50">{{ $leave->employee->position ?? 'Associate' }} · {{ $leave->employee->department ?? 'General' }}</div>
                </div>
                <div class="text-right">
                    <div class="text-[11px] font-bold uppercase tracking-wide text-base-content/40">Status</div>
                    <div class="mt-1 text-2xl font-extrabold {{ $statusColor }}">{{ strtoupper($leave->status) }}</div>
                </div>
            </div>

            <h3 class="mb-3 border-l-4 border-base-content pl-3 font-bold">Leave dates</h3>
            <div class="grid grid-cols-3 gap-4">
                <div class="rounded-lg border border-base-200 bg-base-200/40 p-4">
                    <div class="text-[11px] uppercase tracking-wide text-base-content/40">Start</div>
                    <div class="mt-1 text-lg font-bold">{{ $leave->start_date->format('d M, Y') }}</div>
                </div>
                <div class="rounded-lg border border-base-200 bg-base-200/40 p-4">
                    <div class="text-[11px] uppercase tracking-wide text-base-content/40">End</div>
                    <div class="mt-1 text-lg font-bold">{{ $leave->end_date->format('d M, Y') }}</div>
                </div>
                <div class="rounded-lg bg-secondary p-4 text-center text-secondary-content">
                    <div class="text-[11px] uppercase tracking-wide opacity-70">Total days</div>
                    <div class="mt-1 text-3xl font-extrabold leading-none">{{ $leave->start_date->diffInDays($leave->end_date) + 1 }}</div>
                </div>
            </div>

            <div class="mt-5">
                <h3 class="mb-2 border-l-4 border-base-content pl-3 font-bold">Reason</h3>
                <p class="rounded-lg border border-base-200 bg-base-200/40 p-3 text-sm italic text-base-content/70">"{{ $leave->reason ?? 'Personal leave request.' }}"</p>
            </div>

            <div class="mt-8 grid grid-cols-3 gap-4 border-t-2 border-base-content/80 pt-6 text-center text-xs">
                <div>
                    <div class="mx-auto mb-2 w-32 border-t border-base-content/60"></div>
                    <div class="font-semibold uppercase text-base-content/40">Employee</div>
                    <div class="mt-1 font-bold">{{ $leave->employee->name ?? '' }}</div>
                </div>
                <div>
                    <div class="mx-auto mb-2 w-32 border-t border-base-content/60"></div>
                    <div class="font-semibold uppercase text-base-content/40">Supervisor</div>
                </div>
                <div>
                    @if ($leave->status === 'Approved')<div class="mb-2 inline-block rotate-[-8deg] rounded border-2 border-success px-2 py-0.5 text-[10px] font-extrabold uppercase text-success">Verified by HR</div>@endif
                    <div class="mx-auto mb-2 w-32 border-t border-base-content/60"></div>
                    <div class="font-semibold uppercase text-base-content/40">HR Director</div>
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
