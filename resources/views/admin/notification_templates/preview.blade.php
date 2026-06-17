<x-layouts.app-shell title="Template Preview">
    <div class="mx-auto max-w-3xl space-y-5">
        <div class="flex items-center justify-between gap-3">
            <div>
                <h2 class="text-xl font-semibold">Preview — {{ $notificationTemplate->name }}</h2>
                <p class="text-sm text-base-content/60">Rendered with sample values.</p>
            </div>
            <a href="{{ route('admin.notification-templates.index') }}" class="btn btn-ghost btn-sm">
                <x-mary-icon name="o-arrow-left" class="h-4 w-4" /> Back
            </a>
        </div>

        <div class="rounded-xl border border-base-300 bg-base-100 shadow-sm">
            <div class="flex flex-wrap items-center gap-2 border-b border-base-200 px-5 py-3 text-sm">
                <span class="badge badge-ghost badge-sm font-mono">{{ $notificationTemplate->key }}</span>
                <span class="badge badge-info badge-sm capitalize">{{ $notificationTemplate->type }}</span>
                @if ($notificationTemplate->subject)
                    <span class="text-base-content/60">Subject: <span class="font-medium text-base-content">{{ $notificationTemplate->subject }}</span></span>
                @endif
            </div>
            <div class="p-5">
                <div class="whitespace-pre-wrap rounded-lg bg-base-200/50 p-4 text-sm">{{ $renderedBody }}</div>
            </div>
        </div>

        @if (!empty($sampleData))
            <div class="rounded-xl border border-base-300 bg-base-100 p-5 shadow-sm">
                <h3 class="mb-3 text-sm font-semibold uppercase tracking-wide text-base-content/60">Sample variables</h3>
                <div class="grid gap-2 sm:grid-cols-2">
                    @foreach ($sampleData as $k => $v)
                        @php($ph = '{{ '.$k.' }}')
                        <div class="text-sm"><span class="font-mono text-base-content/60">{{ $ph }}</span> → <span class="font-medium">{{ $v }}</span></div>
                    @endforeach
                </div>
            </div>
        @endif

        <a href="{{ route('admin.notification-templates.edit', $notificationTemplate) }}" class="btn btn-primary btn-sm">
            <x-mary-icon name="o-pencil-square" class="h-4 w-4" /> Edit template
        </a>
    </div>
</x-layouts.app-shell>
