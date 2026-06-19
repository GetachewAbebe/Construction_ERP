<x-layouts.app-shell title="Finance Dashboard">
    <div class="space-y-6">
        <div>
            <h2 class="text-xl font-semibold">Financial Operations</h2>
            <p class="text-sm text-base-content/60">Budgets, expenses and project portfolio.</p>
        </div>

        {{-- KPIs --}}
        <div class="grid grid-cols-2 gap-4 lg:grid-cols-4">
            @foreach ([
                ['label' => 'Projects', 'value' => number_format($totalProjects ?? 0), 'icon' => 'o-briefcase', 'accent' => 'border-l-primary', 'text' => 'text-primary'],
                ['label' => 'Total Budget', 'value' => number_format($totalBudget ?? 0), 'icon' => 'o-banknotes', 'accent' => 'border-l-info', 'text' => 'text-info'],
                ['label' => 'Total Expenses', 'value' => number_format($totalExpenses ?? 0), 'icon' => 'o-credit-card', 'accent' => 'border-l-warning', 'text' => 'text-warning'],
                ['label' => 'Remaining', 'value' => number_format($remainingBudget ?? 0), 'icon' => 'o-wallet', 'accent' => 'border-l-success', 'text' => 'text-success'],
            ] as $kpi)
                <div class="card-interactive flex items-center justify-between rounded-lg border border-base-300 border-l-4 {{ $kpi['accent'] }} bg-base-100 px-4 py-3 shadow-sm">
                    <div>
                        <div class="text-[11px] font-medium uppercase tracking-wide text-base-content/50">{{ $kpi['label'] }}</div>
                        <div class="mt-1 text-xl font-bold tabular-nums {{ $kpi['text'] }}">{{ $kpi['value'] }}</div>
                    </div>
                    <div class="grid h-10 w-10 place-items-center rounded-lg bg-base-200 {{ $kpi['text'] }}">
                        <x-mary-icon name="{{ $kpi['icon'] }}" class="h-5 w-5" />
                    </div>
                </div>
            @endforeach
        </div>

        {{-- Budget usage bar --}}
        <div class="card-interactive rounded-xl border border-base-300 bg-base-100 p-5 shadow-sm">
            <div class="mb-2 flex items-center justify-between text-sm">
                <span class="font-medium">Budget utilisation</span>
                <span class="text-base-content/60">{{ $usagePercentage ?? 0 }}%</span>
            </div>
            <div class="h-2.5 w-full overflow-hidden rounded-full bg-base-300">
                <div class="h-full rounded-full {{ ($usagePercentage ?? 0) > 90 ? 'bg-error' : (($usagePercentage ?? 0) > 70 ? 'bg-warning' : 'bg-success') }}"
                     style="width: {{ min(100, $usagePercentage ?? 0) }}%"></div>
            </div>
        </div>

        <div class="grid gap-4 lg:grid-cols-3">
            {{-- Portfolio chart --}}
            <div class="card-interactive rounded-xl border border-base-300 bg-base-100 p-5 shadow-sm lg:col-span-2">
                <h3 class="font-semibold">Project Portfolio</h3>
                <p class="text-xs text-base-content/50">Budget vs spend by project</p>
                <div id="portfolioChart" class="mt-3"></div>
            </div>

            {{-- Recent projects --}}
            <div class="card-interactive rounded-xl border border-base-300 bg-base-100 p-5 shadow-sm">
                <div class="mb-3 flex items-center justify-between">
                    <h3 class="font-semibold">Recent Projects</h3>
                    <a href="{{ route('finance.projects.index') }}" class="btn btn-ghost btn-xs">All</a>
                </div>
                <div class="space-y-3">
                    @forelse ($recentProjects as $p)
                        <div class="flex items-center justify-between gap-2 text-sm">
                            <span class="truncate">{{ $p->name }}</span>
                            <span class="badge badge-ghost badge-sm shrink-0 capitalize">{{ $p->status ?? '—' }}</span>
                        </div>
                    @empty
                        <p class="text-sm text-base-content/40">No projects yet.</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            var el = document.querySelector('#portfolioChart');
            if (!el || typeof ApexCharts === 'undefined') return;
            new ApexCharts(el, {
                chart: { type: 'bar', height: 300, toolbar: { show: false } },
                series: [
                    { name: 'Budget', data: @json($portfolioBudgets) },
                    { name: 'Spent', data: @json($portfolioExpenses) },
                ],
                colors: ['#1a3a5c', '#f5a623'],
                plotOptions: { bar: { borderRadius: 4, columnWidth: '55%' } },
                dataLabels: { enabled: false },
                xaxis: { categories: @json($portfolioLabels) },
                legend: { position: 'top', horizontalAlign: 'right' },
                grid: { borderColor: '#e3e8ef' },
            }).render();
        });
    </script>
    @endpush
</x-layouts.app-shell>
