@php
    $weatherOptions = [
        ['id' => 'Clear / Sunny', 'name' => '☀️ Clear / Sunny'],
        ['id' => 'Rainy / Muddy', 'name' => '🌧️ Rainy / Muddy (Site Slowdown)'],
        ['id' => 'Overcast / Cloudy', 'name' => '☁️ Overcast / Cloudy'],
        ['id' => 'Windy / Dust Storm', 'name' => '💨 Windy / Dust Storm'],
        ['id' => 'Extreme Heat', 'name' => '🔥 Extreme Heat'],
    ];

    $projectOptions = $projects->map(fn ($p) => ['id' => $p->id, 'name' => $p->name])->toArray();
@endphp

<div>
    {{-- Header DPR Stat Cards --}}
    <div class="grid grid-cols-1 gap-4 sm:grid-cols-3 mb-6">
        <x-mary-stat title="Total Filed DPRs" :value="$totals['total_reports']" icon="o-document-text" color="text-primary" />
        <x-mary-stat title="Today's Site Logs" :value="$totals['today_reports']" icon="o-calendar-days" color="text-info" />
        <x-mary-stat title="Manpower Deployed Today" :value="$totals['total_manpower_today'] . ' Tradesmen'" icon="o-user-group" color="text-success" />
    </div>

    <x-mary-card>
        {{-- Toolbar --}}
        <div class="flex flex-col gap-3 lg:flex-row lg:items-end lg:justify-between mb-5">
            <div class="flex flex-col gap-3 sm:flex-row sm:items-end flex-1">
                <x-mary-input
                    label="Search Logs"
                    placeholder="Work performed, deliveries..."
                    wire:model.live.debounce.300ms="search"
                    icon="o-magnifying-glass"
                    clearable
                    class="w-full sm:w-64" />

                <x-mary-select
                    label="Filter Project"
                    placeholder="All Projects"
                    :options="$projectOptions"
                    wire:model.live="projectId"
                    class="w-full sm:w-56" />

                <x-mary-input
                    label="Report Date"
                    wire:model.live="date"
                    type="date"
                    class="w-full sm:w-44" />

                @if ($search !== '' || $projectId || $date)
                    <x-mary-button label="Reset" wire:click="clearFilters" class="btn-ghost btn-sm" icon="o-x-mark" />
                @endif
            </div>

            <x-mary-button label="Submit Daily Report" wire:click="create" class="btn-primary" icon="o-plus" />
        </div>

        {{-- Reports Timeline List --}}
        <div class="relative space-y-4">
            <div wire:loading.flex class="absolute inset-0 z-10 items-center justify-center bg-base-100/60 backdrop-blur-[1px]">
                <span class="loading loading-spinner loading-lg text-primary"></span>
            </div>

            @forelse ($reports as $report)
                <div class="rounded-xl border border-base-300 bg-base-100 p-5 shadow-sm hover:border-primary/40 transition-all">
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 pb-3 border-b border-base-200">
                        <div class="flex items-center gap-3">
                            <span class="grid h-10 w-10 place-items-center rounded-xl bg-primary/10 text-primary font-bold">
                                <x-mary-icon name="o-document-chart-bar" class="w-5 h-5" />
                            </span>
                            <div>
                                <div class="font-bold text-base text-base-content">{{ $report->project->name ?? 'Global Site' }}</div>
                                <div class="text-xs text-base-content/60">
                                    Report Date: <span class="font-mono font-semibold text-base-content">{{ $report->report_date ? $report->report_date->format('M d, Y (l)') : '—' }}</span> · Filed by <span class="font-medium text-base-content">{{ $report->author->name ?? 'Site Supervisor' }}</span>
                                </div>
                            </div>
                        </div>

                        <div class="flex items-center gap-2">
                            <span class="badge badge-outline text-xs gap-1 font-medium">
                                {{ $report->weather }}
                            </span>
                            <span class="badge badge-primary badge-outline text-xs font-semibold gap-1">
                                <x-mary-icon name="o-user-group" class="w-3.5 h-3.5" />
                                {{ $report->manpower_count }} Men on site
                            </span>
                            <x-mary-button label="Inspect" wire:click="viewDetails({{ $report->id }})" class="btn-ghost btn-xs text-primary" icon="o-eye" />
                        </div>
                    </div>

                    <div class="mt-3 text-sm">
                        <div class="font-semibold text-xs text-base-content/50 uppercase tracking-wider mb-1">Work Accomplished</div>
                        <p class="text-base-content/80 line-clamp-2 leading-relaxed">
                            {{ $report->work_performed }}
                        </p>
                    </div>

                    @if ($report->materials_received || $report->machinery_deployed)
                        <div class="mt-3 pt-3 border-t border-base-200/60 grid grid-cols-1 sm:grid-cols-2 gap-2 text-xs text-base-content/70">
                            @if ($report->materials_received)
                                <div class="truncate">
                                    <span class="font-semibold text-base-content">📦 Materials:</span> {{ $report->materials_received }}
                                </div>
                            @endif
                            @if ($report->machinery_deployed)
                                <div class="truncate">
                                    <span class="font-semibold text-base-content">🚜 Fleet:</span> {{ $report->machinery_deployed }}
                                </div>
                            @endif
                        </div>
                    @endif
                </div>
            @empty
                <div class="py-12 text-center text-base-content/50">
                    <x-mary-icon name="o-document-text" class="w-12 h-12 mx-auto mb-2 opacity-30" />
                    <p class="font-medium">No site daily reports found for this filter.</p>
                </div>
            @endforelse
        </div>

        {{-- Pagination --}}
        <div class="mt-6">
            {{ $reports->links() }}
        </div>
    </x-mary-card>

    {{-- DPR Submission Modal --}}
    <x-mary-modal wire:model="showCreateModal" title="Submit Site Daily Progress Report (DPR)" class="backdrop-blur max-w-2xl">
        <form wire:submit="save" class="space-y-4">
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <x-mary-select label="Project Site *" :options="$projectOptions" wire:model="project_id" placeholder="Select Construction Site" />
                <x-mary-input label="Report Date *" wire:model="report_date" type="date" />
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <x-mary-select label="Site Weather Condition *" :options="$weatherOptions" wire:model="weather" />
                <x-mary-input label="Total Manpower Headcount *" wire:model="manpower_count" type="number" step="1" placeholder="e.g. 35" />
            </div>

            <x-mary-textarea
                label="Summary of Work & Structural Tasks Executed *"
                wire:model="work_performed"
                placeholder="Detail today's activities (e.g. Slab reinforcement, 2nd floor column casting, masonry on grid 4-B)..."
                rows="4" />

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <x-mary-input label="Material Deliveries Received on Site" wire:model="materials_received" placeholder="e.g. 200 bags PPC Cement, 5 tons rebar" />
                <x-mary-input label="Heavy Machinery & Equipment Active" wire:model="machinery_deployed" placeholder="e.g. 1 Excavator, 2 Mixers, 1 Generator" />
            </div>

            <x-mary-input label="HSE / Safety & Incident Log" wire:model="safety_incidents" placeholder="Zero safety incidents reported today." />

            <x-slot:actions>
                <x-mary-button label="Cancel" @click="$wire.showCreateModal = false" class="btn-ghost" />
                <x-mary-button label="Submit DPR" type="submit" class="btn-primary" icon="o-paper-airplane" spinner="save" />
            </x-slot:actions>
        </form>
    </x-mary-modal>

    {{-- Detail Inspector Modal --}}
    <x-mary-modal wire:model="showDetailModal" title="Site Daily Progress Report" class="backdrop-blur max-w-2xl">
        @if ($selectedReport)
            <div class="space-y-4 text-sm">
                <div class="p-4 rounded-xl bg-base-200/50 border border-base-300 grid grid-cols-2 sm:grid-cols-3 gap-3 text-xs">
                    <div>
                        <div class="text-base-content/50 uppercase font-semibold">Project</div>
                        <div class="font-bold text-sm text-base-content">{{ $selectedReport->project->name ?? '—' }}</div>
                    </div>
                    <div>
                        <div class="text-base-content/50 uppercase font-semibold">Date</div>
                        <div class="font-bold font-mono">{{ $selectedReport->report_date ? $selectedReport->report_date->format('M d, Y') : '—' }}</div>
                    </div>
                    <div>
                        <div class="text-base-content/50 uppercase font-semibold">Site Weather</div>
                        <div class="font-medium">{{ $selectedReport->weather }}</div>
                    </div>
                    <div>
                        <div class="text-base-content/50 uppercase font-semibold">Site Headcount</div>
                        <div class="font-bold font-mono text-primary">{{ $selectedReport->manpower_count }} Tradesmen</div>
                    </div>
                    <div>
                        <div class="text-base-content/50 uppercase font-semibold">Site Engineer</div>
                        <div class="font-medium">{{ $selectedReport->author->name ?? 'Staff' }}</div>
                    </div>
                    <div>
                        <div class="text-base-content/50 uppercase font-semibold">Status</div>
                        <span class="badge badge-success badge-sm font-semibold capitalize">{{ $selectedReport->status }}</span>
                    </div>
                </div>

                <div class="space-y-3">
                    <div>
                        <div class="text-xs text-base-content/50 uppercase font-semibold mb-1">Work Accomplished</div>
                        <div class="p-3 rounded-lg bg-base-200/30 border border-base-200 text-base-content/90 whitespace-pre-line leading-relaxed">
                            {{ $selectedReport->work_performed }}
                        </div>
                    </div>

                    @if ($selectedReport->materials_received)
                        <div>
                            <div class="text-xs text-base-content/50 uppercase font-semibold mb-1">Material Deliveries Logged</div>
                            <div class="p-2.5 rounded-lg bg-base-200/20 border border-base-200 text-xs font-mono">
                                {{ $selectedReport->materials_received }}
                            </div>
                        </div>
                    @endif

                    @if ($selectedReport->machinery_deployed)
                        <div>
                            <div class="text-xs text-base-content/50 uppercase font-semibold mb-1">Machinery & Fleet Deployed</div>
                            <div class="p-2.5 rounded-lg bg-base-200/20 border border-base-200 text-xs">
                                {{ $selectedReport->machinery_deployed }}
                            </div>
                        </div>
                    @endif

                    <div>
                        <div class="text-xs text-base-content/50 uppercase font-semibold mb-1">Health, Safety & Environment (HSE)</div>
                        <div class="p-2.5 rounded-lg bg-base-200/20 border border-base-200 text-xs text-base-content/80">
                            🛡️ {{ $selectedReport->safety_incidents ?? 'Zero safety incidents.' }}
                        </div>
                    </div>
                </div>
            </div>
        @endif

        <x-slot:actions>
            <x-mary-button label="Close" @click="$wire.showDetailModal = false" class="btn-ghost" />
        </x-slot:actions>
    </x-mary-modal>
</div>
