<x-layouts.app-shell title="Trash">
    <div class="space-y-5">
        <div>
            <h2 class="text-xl font-semibold">Recycle Bin</h2>
            <p class="text-sm text-base-content/60">Soft-deleted records you can restore.</p>
        </div>

        @if (session('success'))
            <div role="alert" class="alert alert-success py-2 text-sm"><span>{{ session('success') }}</span></div>
        @endif
        @if (session('error'))
            <div role="alert" class="alert alert-error py-2 text-sm"><span>{{ session('error') }}</span></div>
        @endif

        <div class="rounded-lg border border-base-300 bg-base-100 shadow-sm">
            <div class="overflow-x-auto">
                <table class="table">
                    <thead>
                        <tr class="text-[11px] uppercase tracking-wide text-base-content/60">
                            <th>Type</th><th>Name</th><th>Deleted</th><th class="text-right">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($trashedItems as $item)
                            <tr class="hover:bg-base-200/40">
                                <td><span class="badge badge-ghost badge-sm">{{ $item['type'] }}</span></td>
                                <td class="font-medium">{{ $item['name'] }}</td>
                                <td class="whitespace-nowrap text-sm text-base-content/60">{{ optional($item['deleted_at'])->format('M d, Y H:i') ?? '—' }}</td>
                                <td>
                                    <div class="flex justify-end">
                                        <form method="POST" action="{{ route('admin.trash.restore') }}">
                                            @csrf
                                            <input type="hidden" name="model" value="{{ $item['model'] }}">
                                            <input type="hidden" name="id" value="{{ $item['id'] }}">
                                            <button type="submit" class="btn btn-ghost btn-xs text-primary">
                                                <x-mary-icon name="o-arrow-uturn-left" class="h-4 w-4" /> Restore
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4">
                                    <div class="flex flex-col items-center gap-2 py-12 text-base-content/40">
                                        <x-mary-icon name="o-trash" class="h-10 w-10" />
                                        <p class="text-sm">The recycle bin is empty.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-layouts.app-shell>
