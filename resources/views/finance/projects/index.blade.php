<x-layouts.app-shell title="Projects">
    @php
        $statusBadge = [
            'active' => 'badge-success', 'operational' => 'badge-success', 'In Progress' => 'badge-success',
            'completed' => 'badge-info', 'Completed' => 'badge-info',
            'on_hold' => 'badge-warning', 'On Hold' => 'badge-warning', 'Planned' => 'badge-ghost',
        ];
    @endphp

    <div class="space-y-5">
        <div class="flex flex-wrap items-center justify-between gap-3">
            <div>
                <h2 class="text-xl font-semibold">Project Registry</h2>
                <p class="text-sm text-base-content/60">Construction & engineering portfolio.</p>
            </div>
            <a href="{{ route('finance.projects.create') }}" class="btn btn-primary btn-sm">
                <x-mary-icon name="o-plus" class="h-4 w-4" /> New project
            </a>
        </div>

        @if (session('success'))
            <div role="alert" class="alert alert-success py-2 text-sm"><span>{{ session('success') }}</span></div>
        @endif

        {{-- KPIs + search --}}
        <div class="grid gap-4 lg:grid-cols-4">
            <form action="{{ route('finance.projects.index') }}" method="GET" class="flex items-center gap-2 rounded-lg border border-base-300 bg-base-100 p-3 shadow-sm lg:col-span-2">
                <label class="input input-bordered input-sm flex flex-1 items-center gap-2">
                    <x-mary-icon name="o-magnifying-glass" class="h-4 w-4 opacity-40" />
                    <input type="text" name="q" value="{{ request('q') }}" placeholder="Name, code or location…" class="grow" />
                </label>
                <button type="submit" class="btn btn-primary btn-sm">Search</button>
                @if (request('q'))<a href="{{ route('finance.projects.index') }}" class="btn btn-ghost btn-sm">Reset</a>@endif
            </form>
            <div class="rounded-lg border border-base-300 border-l-4 border-l-success bg-base-100 px-4 py-3 shadow-sm">
                <div class="text-[11px] font-medium uppercase tracking-wide text-base-content/50">Active sites</div>
                <div class="mt-1 text-2xl font-bold">{{ $projects->total() }}</div>
            </div>
            <div class="rounded-lg border border-base-300 border-l-4 border-l-primary bg-base-100 px-4 py-3 shadow-sm">
                <div class="text-[11px] font-medium uppercase tracking-wide text-base-content/50">Portfolio budget</div>
                <div class="mt-1 text-xl font-bold tabular-nums">ETB {{ number_format($projects->sum('budget') / 1000000, 1) }}M</div>
            </div>
        </div>

        {{-- Project cards --}}
        <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-3">
            @forelse ($projects as $project)
                @php
                    $spent = $project->total_approved_spending ?? 0;
                    $usage = $project->budget > 0 ? min(($spent / $project->budget) * 100, 100) : 0;
                    $remaining = $project->budget - $spent;
                    $bar = $usage > 90 ? 'bg-error' : ($usage > 75 ? 'bg-warning' : 'bg-success');
                @endphp
                <div class="flex flex-col rounded-xl border border-base-300 bg-base-100 p-5 shadow-sm">
                    <div class="mb-4 flex items-start justify-between gap-3">
                        <div class="flex items-center gap-3">
                            <div class="grid h-11 w-11 shrink-0 place-items-center rounded-lg bg-primary/10 text-primary"><x-mary-icon name="o-building-office-2" class="h-6 w-6" /></div>
                            <div>
                                <h3 class="font-semibold leading-tight">{{ $project->name }}</h3>
                                <div class="text-xs text-base-content/50">{{ $project->location ?? 'Location N/A' }}</div>
                            </div>
                        </div>
                        <span class="badge badge-sm {{ $statusBadge[$project->status] ?? 'badge-ghost' }}">{{ $project->status ?? 'Operational' }}</span>
                    </div>

                    <div class="mb-4 grid grid-cols-2 gap-3 rounded-lg bg-base-200/50 p-3 text-center">
                        <div>
                            <div class="text-[10px] font-semibold uppercase tracking-wide text-base-content/50">Budget</div>
                            <div class="font-mono text-sm font-semibold">ETB {{ number_format($project->budget, 0) }}</div>
                        </div>
                        <div>
                            <div class="text-[10px] font-semibold uppercase tracking-wide text-base-content/50">Spent</div>
                            <div class="font-mono text-sm font-semibold text-error">ETB {{ number_format($spent, 0) }}</div>
                        </div>
                    </div>

                    <div class="mt-auto">
                        <div class="mb-1 flex justify-between text-xs">
                            <span class="text-base-content/50">Budget used</span>
                            <span class="font-semibold {{ $usage > 90 ? 'text-error' : '' }}">{{ round($usage) }}%</span>
                        </div>
                        <div class="mb-3 h-2 w-full overflow-hidden rounded-full bg-base-300">
                            <div class="h-full rounded-full {{ $bar }}" style="width: {{ $usage }}%"></div>
                        </div>
                        <div class="mb-3 flex items-center justify-between border-b border-base-200 pb-3 text-xs">
                            <span class="text-base-content/50">{{ $project->expenses_count }} invoices</span>
                            <span class="font-semibold {{ $remaining < 0 ? 'text-error' : 'text-base-content/70' }}">Left: ETB {{ number_format($remaining, 0) }}</span>
                        </div>
                        <div class="flex justify-end gap-2">
                            <a href="{{ route('finance.projects.show', $project) }}" class="btn btn-ghost btn-xs">View</a>
                            <a href="{{ route('finance.projects.edit', $project) }}" class="btn btn-outline btn-xs">Edit</a>
                            <form action="{{ route('finance.projects.destroy', $project) }}" method="POST" onsubmit="return confirm('Archive {{ $project->name }}?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn btn-ghost btn-xs text-error">Delete</button>
                            </form>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-span-full flex flex-col items-center gap-2 rounded-xl border border-dashed border-base-300 py-16 text-base-content/40">
                    <x-mary-icon name="o-building-office-2" class="h-10 w-10" />
                    <p class="text-sm">No projects registered yet.</p>
                </div>
            @endforelse
        </div>

        @if ($projects->hasPages())
            <div>{{ $projects->withQueryString()->links() }}</div>
        @endif
    </div>
</x-layouts.app-shell>
