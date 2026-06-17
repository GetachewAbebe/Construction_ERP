<x-layouts.app-shell title="Loan Approvals">
    @php
        $badge = ['approved' => 'badge-success', 'rejected' => 'badge-error', 'returned' => 'badge-info', 'pending' => 'badge-warning'];
    @endphp

    <div class="space-y-5">
        <div class="flex items-center justify-between gap-3">
            <div>
                <h2 class="text-xl font-semibold">Item Lending Requests</h2>
                <p class="text-sm text-base-content/60">Approve or reject inventory items requested by employees.</p>
            </div>
            <a href="{{ route('admin.dashboard') }}" class="btn btn-ghost btn-sm">
                <x-mary-icon name="o-arrow-left" class="h-4 w-4" /> Dashboard
            </a>
        </div>

        @if (session('status'))
            <div role="alert" class="alert alert-success py-2 text-sm"><span>{{ session('status') }}</span></div>
        @endif
        @if (session('error'))
            <div role="alert" class="alert alert-error py-2 text-sm"><span>{{ session('error') }}</span></div>
        @endif

        <div class="rounded-lg border border-base-300 bg-base-100 shadow-sm">
            @if ($loans->isEmpty())
                <div class="flex flex-col items-center gap-2 py-14 text-base-content/40">
                    <x-mary-icon name="o-inbox" class="h-10 w-10" />
                    <p class="text-sm">No item lending requests yet.</p>
                </div>
            @else
                <div class="overflow-x-auto">
                    <table class="table">
                        <thead>
                            <tr class="text-[11px] uppercase tracking-wide text-base-content/60">
                                <th>Requested</th><th>Item</th><th>Employee</th><th class="text-center">Qty</th><th>Status</th><th class="text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($loans as $loan)
                                @php($status = $loan->status ?? 'pending')
                                <tr>
                                    <td class="whitespace-nowrap text-sm text-base-content/60">{{ optional($loan->created_at)->format('Y-m-d H:i') ?? '—' }}</td>
                                    <td>
                                        <div class="font-medium">{{ $loan->item->name ?? '—' }}</div>
                                        <div class="text-xs text-base-content/50">No: {{ $loan->item->item_no ?? '—' }}</div>
                                    </td>
                                    <td>
                                        <div class="text-sm">{{ $loan->employee->name ?? '—' }}</div>
                                        <div class="text-xs text-base-content/50">ID: {{ $loan->employee->id ?? '—' }}</div>
                                    </td>
                                    <td class="text-center font-medium">{{ $loan->quantity }}</td>
                                    <td><span class="badge badge-sm {{ $badge[$status] ?? 'badge-ghost' }}">{{ ucfirst($status) }}</span></td>
                                    <td>
                                        <div class="flex justify-end gap-2">
                                            @if ($status === 'pending')
                                                <form method="POST" action="{{ route('admin.requests.items.approve', $loan) }}">
                                                    @csrf
                                                    <button type="submit" class="btn btn-primary btn-xs">Approve</button>
                                                </form>
                                                <form method="POST" action="{{ route('admin.requests.items.reject', $loan) }}"
                                                      onsubmit="return confirm('Reject this request?')">
                                                    @csrf
                                                    <button type="submit" class="btn btn-ghost btn-xs text-error">Reject</button>
                                                </form>
                                            @else
                                                <span class="text-xs text-base-content/30">—</span>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="border-t border-base-200 p-4">{{ $loans->links() }}</div>
            @endif
        </div>
    </div>
</x-layouts.app-shell>
