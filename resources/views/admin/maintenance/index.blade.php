<x-layouts.app-shell title="Maintenance">
    <div class="space-y-5">
        <div>
            <h2 class="text-xl font-semibold">System Maintenance</h2>
            <p class="text-sm text-base-content/60">Health, cache, backups and logs.</p>
        </div>

        @if (session('success'))
            <div role="alert" class="alert alert-success py-2 text-sm"><span>{{ session('success') }}</span></div>
        @endif
        @if (session('error'))
            <div role="alert" class="alert alert-error py-2 text-sm"><span>{{ session('error') }}</span></div>
        @endif

        {{-- Migration --}}
        <div class="flex flex-col items-start justify-between gap-3 rounded-xl border border-base-300 bg-base-100 p-5 shadow-sm sm:flex-row sm:items-center">
            <div class="flex items-center gap-3">
                <div class="grid h-10 w-10 place-items-center rounded-lg bg-primary/10 text-primary"><x-mary-icon name="o-circle-stack" class="h-5 w-5" /></div>
                <div>
                    <h3 class="font-semibold">Database Schema</h3>
                    <p class="text-sm">
                        @if (($pendingMigrations ?? 0) > 0)
                            <span class="text-error">{{ $pendingMigrations }} pending migration(s) detected.</span>
                        @else
                            <span class="text-success">Schema is synchronized.</span>
                        @endif
                    </p>
                </div>
            </div>
            <form action="{{ route('admin.maintenance.migrate') }}" method="POST" onsubmit="return confirm('Run pending migrations? Database will be modified.')">
                @csrf
                <button type="submit" class="btn btn-primary btn-sm">Sync schema</button>
            </form>
        </div>

        {{-- Info grids --}}
        <div class="grid gap-4 lg:grid-cols-2">
            <div class="rounded-xl border border-base-300 bg-base-100 p-5 shadow-sm">
                <h3 class="mb-3 flex items-center gap-2 font-semibold"><x-mary-icon name="o-cpu-chip" class="h-5 w-5 text-primary" /> System</h3>
                <dl class="grid grid-cols-2 gap-3 text-sm">
                    @foreach ($systemInfo as $key => $value)
                        <div>
                            <dt class="text-xs uppercase tracking-wide text-base-content/50">{{ str_replace('_', ' ', $key) }}</dt>
                            <dd class="font-medium">{{ $value }}</dd>
                        </div>
                    @endforeach
                </dl>
            </div>
            <div class="rounded-xl border border-base-300 bg-base-100 p-5 shadow-sm">
                <h3 class="mb-3 flex items-center gap-2 font-semibold"><x-mary-icon name="o-server-stack" class="h-5 w-5 text-success" /> Storage &amp; Cache</h3>
                <dl class="grid grid-cols-2 gap-3 text-sm">
                    @foreach ($storageInfo as $key => $value)
                        <div>
                            <dt class="text-xs uppercase tracking-wide text-base-content/50">{{ str_replace('_', ' ', $key) }}</dt>
                            <dd class="font-medium">{{ $value }}</dd>
                        </div>
                    @endforeach
                    <div>
                        <dt class="text-xs uppercase tracking-wide text-base-content/50">Cache driver</dt>
                        <dd class="font-medium">{{ $cacheInfo['driver'] ?? '—' }}</dd>
                    </div>
                </dl>
            </div>
        </div>

        {{-- Actions --}}
        <div class="grid gap-4 sm:grid-cols-3">
            @foreach ([
                ['route' => 'admin.maintenance.clear-cache', 'icon' => 'o-trash', 'title' => 'Purge Cache', 'desc' => 'Clear config, route and view caches.', 'btn' => 'btn-warning', 'confirm' => 'Purge all caches?'],
                ['route' => 'admin.maintenance.optimize', 'icon' => 'o-bolt', 'title' => 'Optimize', 'desc' => 'Rebuild caches for performance.', 'btn' => 'btn-success', 'confirm' => 'Run optimization?'],
                ['route' => 'admin.maintenance.clear-logs', 'icon' => 'o-document-minus', 'title' => 'Clear Logs', 'desc' => 'Delete all log files.', 'btn' => 'btn-error', 'confirm' => 'Permanently delete all logs?'],
            ] as $a)
                <div class="flex flex-col items-center gap-3 rounded-xl border border-base-300 bg-base-100 p-5 text-center shadow-sm">
                    <div class="grid h-12 w-12 place-items-center rounded-full bg-base-200"><x-mary-icon name="{{ $a['icon'] }}" class="h-6 w-6" /></div>
                    <div>
                        <h4 class="font-semibold">{{ $a['title'] }}</h4>
                        <p class="mt-1 text-xs text-base-content/60">{{ $a['desc'] }}</p>
                    </div>
                    <form action="{{ route($a['route']) }}" method="POST" onsubmit="return confirm('{{ $a['confirm'] }}')" class="mt-auto">
                        @csrf
                        <button type="submit" class="btn {{ $a['btn'] }} btn-sm">{{ $a['title'] }}</button>
                    </form>
                </div>
            @endforeach
        </div>

        {{-- Backups --}}
        <div class="rounded-xl border border-base-300 bg-base-100 shadow-sm">
            <div class="flex items-center justify-between border-b border-base-200 px-5 py-3">
                <h3 class="font-semibold">Database Backups</h3>
                <form action="{{ route('admin.maintenance.create-backup') }}" method="POST" onsubmit="return confirm('Create a new backup?')">
                    @csrf
                    <button type="submit" class="btn btn-primary btn-xs"><x-mary-icon name="o-plus" class="h-4 w-4" /> Create backup</button>
                </form>
            </div>
            <div id="backupList" class="p-5 text-center text-sm text-base-content/50">
                <span class="loading loading-spinner loading-sm"></span> Loading backups…
            </div>
        </div>

        {{-- Logs --}}
        <div class="rounded-xl border border-base-300 bg-base-100 shadow-sm">
            <div class="border-b border-base-200 px-5 py-3"><h3 class="font-semibold">Recent Log Files</h3></div>
            @if (count($logFiles) > 0)
                <div class="overflow-x-auto">
                    <table class="table table-sm">
                        <thead><tr class="text-[11px] uppercase tracking-wide text-base-content/60"><th>File</th><th>Size</th><th>Modified</th></tr></thead>
                        <tbody>
                            @foreach ($logFiles as $log)
                                <tr><td class="font-mono text-xs">{{ $log['name'] }}</td><td>{{ $log['size'] }}</td><td class="text-base-content/60">{{ $log['modified'] }}</td></tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <p class="p-5 text-center text-sm text-base-content/40">No log files found.</p>
            @endif
        </div>
    </div>

    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            fetch('{{ route('admin.maintenance.list-backups') }}')
                .then(r => r.json())
                .then(data => {
                    const c = document.getElementById('backupList');
                    if (!data.length) { c.className = 'p-8 text-center text-sm text-base-content/40'; c.textContent = 'No backups found.'; return; }
                    let h = '<div class="overflow-x-auto"><table class="table table-sm"><thead><tr class="text-[11px] uppercase tracking-wide text-base-content/60"><th>Archive</th><th>Size</th><th>Created</th><th class="text-right">Action</th></tr></thead><tbody>';
                    data.forEach(b => {
                        h += `<tr><td class="font-mono text-xs">${b.name}</td><td>${b.size}</td><td class="text-base-content/60">${b.date}</td><td class="text-right"><a href="/admin/maintenance/backup/download/${b.name}" class="btn btn-ghost btn-xs text-primary">Download</a></td></tr>`;
                    });
                    h += '</tbody></table></div>';
                    c.className = ''; c.innerHTML = h;
                })
                .catch(() => {
                    const c = document.getElementById('backupList');
                    c.className = 'p-5 text-center text-sm text-error'; c.textContent = 'Failed to load backups.';
                });
        });
    </script>
    @endpush
</x-layouts.app-shell>
