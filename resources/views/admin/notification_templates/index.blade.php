<x-layouts.app-shell title="Notification Templates">
    @php($typeBadge = ['email' => 'badge-info', 'notification' => 'badge-primary', 'sms' => 'badge-success'])

    <div class="space-y-5">
        <div class="flex flex-wrap items-center justify-between gap-3">
            <div>
                <h2 class="text-xl font-semibold">Notification Templates</h2>
                <p class="text-sm text-base-content/60">Email, in-app and SMS message templates.</p>
            </div>
            <a href="{{ route('admin.notification-templates.create') }}" class="btn btn-primary btn-sm">
                <x-mary-icon name="o-plus" class="h-4 w-4" /> New template
            </a>
        </div>

        @if (session('success'))
            <div role="alert" class="alert alert-success py-2 text-sm"><span>{{ session('success') }}</span></div>
        @endif

        <div class="rounded-lg border border-base-300 bg-base-100 shadow-sm">
            <div class="overflow-x-auto">
                <table class="table">
                    <thead>
                        <tr class="text-[11px] uppercase tracking-wide text-base-content/60">
                            <th>Template</th><th>Key</th><th>Type</th><th>Active</th><th class="text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($templates as $t)
                            <tr class="hover:bg-base-200/40">
                                <td>
                                    <div class="font-medium">{{ $t->name }}</div>
                                    <div class="text-xs text-base-content/50">{{ $t->subject }}</div>
                                </td>
                                <td class="font-mono text-xs">{{ $t->key }}</td>
                                <td><span class="badge badge-sm {{ $typeBadge[$t->type] ?? 'badge-ghost' }} capitalize">{{ $t->type }}</span></td>
                                <td>
                                    @if ($t->is_active)
                                        <span class="inline-flex items-center gap-1.5 text-sm"><span class="h-1.5 w-1.5 rounded-full bg-success"></span>Active</span>
                                    @else
                                        <span class="inline-flex items-center gap-1.5 text-sm text-base-content/50"><span class="h-1.5 w-1.5 rounded-full bg-base-content/30"></span>Inactive</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="flex justify-end gap-2">
                                        <a href="{{ route('admin.notification-templates.preview', $t) }}" class="btn btn-ghost btn-xs">Preview</a>
                                        <a href="{{ route('admin.notification-templates.edit', $t) }}" class="btn btn-ghost btn-xs">Edit</a>
                                        <form method="POST" action="{{ route('admin.notification-templates.destroy', $t) }}" onsubmit="return confirm('Delete template {{ $t->name }}?')">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="btn btn-ghost btn-xs text-error">Delete</button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="5" class="py-10 text-center text-base-content/40">No templates registered yet.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-layouts.app-shell>
