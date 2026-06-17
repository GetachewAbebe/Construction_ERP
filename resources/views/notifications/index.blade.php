<x-layouts.app-shell title="Notifications">
    @php($isAdmin = Auth::user()->hasRole('Administrator') || Auth::user()->hasRole('Admin'))

    <div class="space-y-5">
        <div class="flex flex-wrap items-center justify-between gap-3">
            <div>
                <h2 class="text-xl font-semibold">Notifications</h2>
                <p class="text-sm text-base-content/60">System alerts and activity.</p>
            </div>
            <form action="{{ route('notifications.mark-all-as-read') }}" method="POST">
                @csrf <button type="submit" class="btn btn-ghost btn-sm">Mark all as read</button>
            </form>
        </div>

        @if (session('success'))
            <div role="alert" class="alert alert-success py-2 text-sm"><span>{{ session('success') }}</span></div>
        @endif

        <div class="rounded-lg border border-base-300 bg-base-100 shadow-sm">
            <div class="overflow-x-auto">
                <table class="table">
                    <thead>
                        <tr class="text-[11px] uppercase tracking-wide text-base-content/60">
                            <th>Notification</th><th>Status</th><th>Date</th><th class="text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($notifications as $notification)
                            @php($d = $notification->data)
                            <tr class="{{ $notification->read_at ? '' : 'bg-primary/5' }} hover:bg-base-200/40">
                                <td>
                                    <div class="flex items-center gap-3">
                                        <div class="grid h-9 w-9 shrink-0 place-items-center rounded-full bg-primary/10 text-primary"><x-mary-icon name="o-bell" class="h-4 w-4" /></div>
                                        <div>
                                            <div class="font-medium leading-tight">{{ $d['title'] ?? 'System Alert' }}</div>
                                            <div class="text-xs text-base-content/50">{{ $d['message'] ?? 'Click for details' }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    @if ($notification->read_at)
                                        <span class="badge badge-ghost badge-sm">Read</span>
                                    @else
                                        <span class="badge badge-error badge-sm">Unread</span>
                                    @endif
                                </td>
                                <td class="whitespace-nowrap text-sm text-base-content/60">
                                    {{ $notification->created_at->format('M d, Y') }}
                                    <div class="text-xs text-base-content/40">{{ $notification->created_at->diffForHumans() }}</div>
                                </td>
                                <td>
                                    <div class="flex justify-end gap-2">
                                        @if ($isAdmin && ! $notification->read_at && str_ends_with($d['type'] ?? '', '_request'))
                                            @if (isset($d['expense_id']))
                                                <form action="{{ route('admin.finance.expenses.approve', $d['expense_id']) }}" method="POST">@csrf<button class="btn btn-success btn-xs">Approve</button></form>
                                            @elseif (isset($d['leave_id']))
                                                <form action="{{ route('admin.requests.leave.approve', $d['leave_id']) }}" method="POST">@csrf<button class="btn btn-success btn-xs">Approve</button></form>
                                            @elseif (isset($d['loan_id']))
                                                <form action="{{ route('admin.requests.items.approve', $d['loan_id']) }}" method="POST">@csrf<button class="btn btn-success btn-xs">Approve</button></form>
                                            @endif
                                        @endif
                                        <a href="{{ $d['url'] ?? '#' }}" class="btn btn-ghost btn-xs" onclick="markRead('{{ $notification->id }}')">View</a>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4">
                                    <div class="flex flex-col items-center gap-2 py-12 text-base-content/40">
                                        <x-mary-icon name="o-inbox" class="h-10 w-10" />
                                        <p class="text-sm">You're all caught up.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if ($notifications->hasPages())
                <div class="border-t border-base-200 p-4">{{ $notifications->links() }}</div>
            @endif
        </div>
    </div>

    @push('scripts')
    <script>
        function markRead(id) {
            fetch('/notifications/' + id + '/mark-as-read', {
                method: 'POST',
                headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
            }).catch(() => {});
        }
    </script>
    @endpush
</x-layouts.app-shell>
