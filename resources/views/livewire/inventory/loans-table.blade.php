@php
    $statusOptions = [
        ['id' => 'pending', 'name' => 'Pending'],
        ['id' => 'approved', 'name' => 'Approved'],
        ['id' => 'returned', 'name' => 'Returned'],
        ['id' => 'rejected', 'name' => 'Rejected'],
    ];

    // [pill classes, dot color, label] per status
    $statusMeta = [
        'pending'  => ['bg-warning/10 text-warning border-warning/20', 'bg-warning', 'Pending'],
        'approved' => ['bg-success/10 text-success border-success/20', 'bg-success', 'Approved'],
        'returned' => ['bg-info/10 text-info border-info/20',          'bg-info',    'Returned'],
        'rejected' => ['bg-error/10 text-error border-error/20',       'bg-error',   'Rejected'],
    ];

    $kpis = [
        ['key' => '',         'label' => 'Total requests', 'icon' => 'o-clipboard-document-list', 'accent' => 'border-l-primary', 'text' => 'text-primary'],
        ['key' => 'pending',  'label' => 'Pending',        'icon' => 'o-clock',                   'accent' => 'border-l-warning', 'text' => 'text-warning'],
        ['key' => 'approved', 'label' => 'Approved',       'icon' => 'o-check-circle',            'accent' => 'border-l-success', 'text' => 'text-success'],
        ['key' => 'returned', 'label' => 'Returned',       'icon' => 'o-arrow-uturn-left',        'accent' => 'border-l-info',    'text' => 'text-info'],
    ];

    $initials = fn (?string $name) => collect(explode(' ', trim((string) $name)))
        ->filter()->take(2)->map(fn ($p) => mb_strtoupper(mb_substr($p, 0, 1)))->implode('');
@endphp

<div class="space-y-6">
    {{-- Page title --}}
    <div class="flex flex-wrap items-center justify-between gap-3">
        <div>
            <h1 class="text-xl font-semibold text-base-content">Loan Requests</h1>
            <p class="text-sm text-base-content/50">Review and process tools lent to employees.</p>
        </div>
        <x-mary-button label="New Loan Request" icon="o-plus" class="btn-primary btn-sm" disabled
            tooltip-left="Wired up in a later step" />
    </div>

    {{-- KPI cards --}}
    <div class="grid grid-cols-2 gap-4 lg:grid-cols-4">
        @foreach ($kpis as $kpi)
            @php($value = $kpi['key'] === '' ? $total : ($counts[$kpi['key']] ?? 0))
            <button type="button"
                @if ($kpi['key'] === '') wire:click="clearFilters" @else wire:click="setStatus('{{ $kpi['key'] }}')" @endif
                class="flex items-center justify-between rounded-lg border border-base-300 border-l-4 {{ $kpi['accent'] }} bg-base-100 px-4 py-3 text-left shadow-sm transition hover:shadow
                       {{ ($kpi['key'] === '' ? $status === '' : $status === $kpi['key']) ? 'ring-1 ring-primary/40' : '' }}">
                <div>
                    <div class="text-[11px] font-medium uppercase tracking-wide text-base-content/50">{{ $kpi['label'] }}</div>
                    <div class="mt-1 text-2xl font-bold tabular-nums {{ $kpi['text'] }}">{{ $value }}</div>
                </div>
                <div class="grid h-10 w-10 place-items-center rounded-lg bg-base-200 {{ $kpi['text'] }}">
                    <x-mary-icon name="{{ $kpi['icon'] }}" class="w-5 h-5" />
                </div>
            </button>
        @endforeach
    </div>

    {{-- Data table --}}
    <div class="rounded-lg border border-base-300 bg-base-100 shadow-sm">
        {{-- Toolbar --}}
        <div class="flex flex-col gap-3 border-b border-base-200 px-4 py-3 sm:flex-row sm:items-center sm:justify-between">
            <div class="flex flex-col gap-2 sm:flex-row sm:items-center">
                <x-mary-input
                    placeholder="Search employee, item name or No.…"
                    wire:model.live.debounce.400ms="search"
                    icon="o-magnifying-glass"
                    clearable
                    class="w-full sm:w-80 input-sm" />

                <x-mary-select
                    placeholder="All statuses"
                    :options="$statusOptions"
                    wire:model.live="status"
                    class="w-full sm:w-44 select-sm" />

                @if ($search !== '' || $status !== '')
                    <x-mary-button label="Reset" wire:click="clearFilters" class="btn-ghost btn-sm" icon="o-x-mark" />
                @endif
            </div>

            <div class="text-xs text-base-content/50">
                Showing <span class="font-semibold text-base-content/70">{{ $loans->count() }}</span>
                of <span class="font-semibold text-base-content/70">{{ $loans->total() }}</span>
            </div>
        </div>

        {{-- Table --}}
        <div class="relative overflow-x-auto">
            <div wire:loading.flex class="absolute inset-0 z-10 items-center justify-center bg-base-100/70 backdrop-blur-[1px]">
                <span class="loading loading-spinner loading-md text-primary"></span>
            </div>

            <table class="table table-sm">
                <thead>
                    <tr class="bg-base-200/60 text-[11px] uppercase tracking-wide text-base-content/60">
                        <th class="py-3">Item</th>
                        <th>Employee</th>
                        <th class="text-center">Qty</th>
                        <th>Status</th>
                        <th>Requested</th>
                        <th class="text-right pr-4">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($loans as $loan)
                        @php([$pill, $dot, $label] = $statusMeta[$loan->status] ?? ['bg-base-200 text-base-content/60 border-base-300', 'bg-base-400', ucfirst($loan->status)])
                        <tr wire:key="loan-{{ $loan->id }}" class="border-base-200 hover:bg-base-200/40">
                            <td class="py-2.5">
                                <div class="flex items-center gap-3">
                                    <div class="grid h-8 w-8 shrink-0 place-items-center rounded-md bg-primary/5 text-primary/70">
                                        <x-mary-icon name="o-wrench-screwdriver" class="w-4 h-4" />
                                    </div>
                                    <div>
                                        <div class="font-medium leading-tight">{{ $loan->item?->name ?? '—' }}</div>
                                        <div class="text-xs text-base-content/40">{{ $loan->item?->item_no }}</div>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <div class="flex items-center gap-2">
                                    <div class="grid h-7 w-7 shrink-0 place-items-center rounded-full bg-base-300/60 text-[10px] font-semibold text-base-content/70">
                                        {{ $initials($loan->employee?->name) ?: '—' }}
                                    </div>
                                    <span class="text-sm">{{ $loan->employee?->name ?? '—' }}</span>
                                </div>
                            </td>
                            <td class="text-center font-medium tabular-nums">{{ $loan->quantity }}</td>
                            <td>
                                <span class="inline-flex items-center gap-1.5 rounded-full border px-2.5 py-0.5 text-xs font-medium {{ $pill }}">
                                    <span class="h-1.5 w-1.5 rounded-full {{ $dot }}"></span>{{ $label }}
                                </span>
                            </td>
                            <td class="text-sm text-base-content/60 whitespace-nowrap">
                                {{ optional($loan->requested_at ?? $loan->created_at)->format('d M Y') ?? '—' }}
                            </td>
                            <td class="pr-4">
                                <div class="flex justify-end gap-2">
                                    @if ($loan->status === 'pending')
                                        <x-mary-button label="Approve"
                                            wire:click="approve({{ $loan->id }})"
                                            wire:confirm="Approve this loan and deduct stock?"
                                            spinner="approve({{ $loan->id }})"
                                            class="btn-primary btn-xs" />
                                        <x-mary-button label="Reject"
                                            wire:click="reject({{ $loan->id }})"
                                            wire:confirm="Reject this loan request?"
                                            spinner="reject({{ $loan->id }})"
                                            class="btn-ghost btn-xs text-error" />
                                    @elseif ($loan->status === 'approved')
                                        <x-mary-button label="Mark Returned"
                                            wire:click="markReturned({{ $loan->id }})"
                                            wire:confirm="Mark this loan as returned and restore stock?"
                                            spinner="markReturned({{ $loan->id }})"
                                            class="btn-outline btn-xs" />
                                    @else
                                        <span class="text-xs text-base-content/30">—</span>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6">
                                <div class="flex flex-col items-center justify-center gap-2 py-14 text-base-content/40">
                                    <x-mary-icon name="o-inbox" class="w-10 h-10" />
                                    <p class="text-sm">No loan requests found.</p>
                                    @if ($search !== '' || $status !== '')
                                        <x-mary-button label="Reset filters" wire:click="clearFilters" class="btn-ghost btn-xs" />
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Footer / pagination --}}
        @if ($loans->hasPages())
            <div class="border-t border-base-200 px-4 py-3">
                {{ $loans->links() }}
            </div>
        @endif
    </div>
</div>
