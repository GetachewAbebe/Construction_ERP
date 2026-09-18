<x-layouts.app-shell title="Finance Dashboard">
    <div class="space-y-6">
        {{-- Finance Hero Banner --}}
        <div class="glass-hero rounded-2xl p-6 sm:p-7 text-white shadow-xl relative overflow-hidden">
            <div class="absolute -right-10 -bottom-10 w-60 h-60 bg-emerald-500/10 rounded-full blur-2xl pointer-events-none"></div>

            <div class="relative z-10 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                <div>
                    <div class="flex items-center gap-2 mb-1">
                        <span class="px-2.5 py-0.5 rounded-full bg-emerald-500/20 text-emerald-300 text-[10px] font-bold tracking-wider uppercase border border-emerald-500/30">
                            Financial Operations
                        </span>
                        <span class="text-xs text-slate-400">· {{ now()->format('l, M d, Y') }}</span>
                    </div>
                    <h2 class="text-2xl sm:text-3xl font-extrabold tracking-tight font-display">
                        Financial Treasury & Budgets
                    </h2>
                    <p class="text-xs sm:text-sm text-slate-300">
                        Project capital allocations, field expenditure audits, and payment voucher workflows.
                    </p>
                </div>

                <div class="flex items-center gap-2">
                    <a href="{{ route('finance.expenses.index') }}" class="btn btn-sm btn-primary shadow-lg shadow-amber-500/20">
                        <x-mary-icon name="o-plus" class="w-4 h-4" /> Record Expense
                    </a>
                    <a href="{{ route('finance.projects.index') }}" class="btn btn-sm bg-white/10 hover:bg-white/20 text-white border-white/10">
                        <x-mary-icon name="o-briefcase" class="w-4 h-4 text-emerald-400" /> Projects
                    </a>
                </div>
            </div>
        </div>

        {{-- KPI Cards --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            @foreach ([
                ['label' => 'Total Projects', 'value' => number_format((float) ($totalProjects ?? 0)), 'sub' => 'Active Civil Portfolios', 'icon' => 'o-briefcase', 'color' => 'text-primary', 'bg' => 'bg-primary/10'],
                ['label' => 'Total Capital Budget', 'value' => 'ETB ' . number_format((float) ($totalBudget ?? 0), 0), 'sub' => 'Contract Value', 'icon' => 'o-banknotes', 'color' => 'text-info', 'bg' => 'bg-info/10'],
                ['label' => 'Cumulative Expenses', 'value' => 'ETB ' . number_format((float) ($totalExpenses ?? 0), 0), 'sub' => 'Disbursed Capital', 'icon' => 'o-credit-card', 'color' => 'text-warning', 'bg' => 'bg-warning/10'],
                ['label' => 'Treasury Balance', 'value' => 'ETB ' . number_format((float) ($remainingBudget ?? 0), 0), 'sub' => 'Unallocated Reserve', 'icon' => 'o-wallet', 'color' => 'text-success', 'bg' => 'bg-success/10'],
            ] as $kpi)
                <div class="card-interactive rounded-2xl border border-base-300/80 bg-base-100 p-5 shadow-sm">
                    <div class="flex items-center justify-between">
                        <div class="space-y-1">
                            <div class="text-[11px] font-bold uppercase tracking-wider text-base-content/50">{{ $kpi['label'] }}</div>
                            <div class="text-xl sm:text-2xl font-black font-display {{ $kpi['color'] }}">{{ $kpi['value'] }}</div>
                            <div class="text-[11px] text-base-content/60 font-medium">{{ $kpi['sub'] }}</div>
                        </div>
                        <div class="p-3.5 rounded-2xl {{ $kpi['bg'] }} {{ $kpi['color'] }}">
                            <x-mary-icon name="{{ $kpi['icon'] }}" class="w-6 h-6" />
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        {{-- Budget utilization progress --}}
        <div class="card-interactive rounded-2xl border border-base-300/80 bg-base-100 p-5 shadow-sm">
            <div class="mb-2.5 flex items-center justify-between text-xs">
                <span class="font-bold text-sm font-display">Overall Portfolio Budget Consumption</span>
                <span class="font-mono font-bold text-primary">{{ $usagePercentage ?? 0 }}% Committed</span>
            </div>
            <div class="h-3 w-full overflow-hidden rounded-full bg-base-200 p-0.5 border border-base-300">
                <div class="h-full rounded-full bg-gradient-to-r from-emerald-500 via-amber-500 to-rose-500 transition-all duration-500"
                     style="width: {{ min(100, $usagePercentage ?? 0) }}%"></div>
            </div>
        </div>

        <div class="grid gap-6 lg:grid-cols-3">
            {{-- Portfolio chart --}}
            <div class="card-interactive rounded-2xl border border-base-300/80 bg-base-100 p-5 shadow-sm lg:col-span-2">
                <h3 class="font-bold text-base font-display">Project Budget vs Spend Distribution</h3>
                <p class="text-xs text-base-content/50">Comparative expenditure analysis across active project sites</p>
                <div id="portfolioChart" class="mt-3"></div>
            </div>

            {{-- Recent projects --}}
            <div class="card-interactive rounded-2xl border border-base-300/80 bg-base-100 p-5 shadow-sm">
                <div class="mb-3 flex items-center justify-between">
                    <h3 class="font-bold text-base font-display">Active Projects</h3>
                    <a href="{{ route('finance.projects.index') }}" class="btn btn-ghost btn-xs text-primary">View all</a>
                </div>
                <div class="space-y-2.5">
                    @forelse ($recentProjects as $p)
                        <div class="p-3 rounded-xl bg-base-200/40 border border-base-200 flex items-center justify-between gap-2 text-xs">
                            <div class="min-w-0">
                                <div class="font-bold text-base-content truncate">{{ $p->name }}</div>
                                <div class="text-[10px] text-base-content/50">Budget: ETB {{ number_format((float) $p->budget, 0) }}</div>
                            </div>
                            <span class="badge badge-success badge-sm font-semibold capitalize">{{ $p->status ?? 'Active' }}</span>
                        </div>
                    @empty
                        <p class="text-sm text-base-content/40 py-6 text-center">No projects registered.</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            var el = document.querySelector("#portfolioChart");
            if (!el || typeof ApexCharts === 'undefined') return;
            new ApexCharts(el, {
                series: [{ name: 'Allocated Budget', data: [15, 22, 18, 25, 30] }, { name: 'Expenditure', data: [6, 12, 9, 14, 19] }],
                chart: { height: 240, type: 'bar', toolbar: { show: false } },
                colors: ['#f59e0b', '#10b981'],
                plotOptions: { bar: { columnWidth: '45%', borderRadius: 6 } },
                xaxis: { categories: ['Bole Tower', 'G+4 Res.', 'Highway Seg.', 'Warehouse', 'Site B'], labels: { style: { colors: '#94a3b8', fontSize: '10px' } } },
                legend: { position: 'top', horizontalAlign: 'right', fontSize: '11px' }
            }).render();
        });
    </script>
    @endpush
</x-layouts.app-shell>
