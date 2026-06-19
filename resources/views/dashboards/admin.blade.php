<x-layouts.app-shell title="Dashboard">
    @php
        $pendingTotal = ($pendingLoanCount ?? 0) + ($pendingExpenseCount ?? 0) + ($pendingLeaveCount ?? 0);
    @endphp

    <div class="space-y-6">
        {{-- Heading --}}
        <div>
            <h2 class="text-xl font-semibold">System Administration</h2>
            <p class="text-sm text-base-content/60">Central command for users, approvals and configuration.</p>
        </div>

        {{-- KPI cards --}}
        <div class="grid grid-cols-2 gap-4 lg:grid-cols-4">
            @foreach ([
                ['label' => 'Total Users', 'value' => $totalUsers ?? 0, 'icon' => 'o-users', 'accent' => 'border-l-primary', 'text' => 'text-primary'],
                ['label' => 'Projects', 'value' => $totalProjects ?? 0, 'icon' => 'o-briefcase', 'accent' => 'border-l-info', 'text' => 'text-info'],
                ['label' => 'Employees', 'value' => $totalEmployees ?? 0, 'icon' => 'o-identification', 'accent' => 'border-l-success', 'text' => 'text-success'],
                ['label' => 'Pending Approvals', 'value' => $pendingTotal, 'icon' => 'o-clock', 'accent' => 'border-l-warning', 'text' => 'text-warning'],
            ] as $kpi)
                <div class="card-interactive flex items-center justify-between rounded-lg border border-base-300 border-l-4 {{ $kpi['accent'] }} bg-base-100 px-4 py-3 shadow-sm">
                    <div>
                        <div class="text-[11px] font-medium uppercase tracking-wide text-base-content/50">{{ $kpi['label'] }}</div>
                        <div class="mt-1 text-2xl font-bold tabular-nums {{ $kpi['text'] }}">{{ $kpi['value'] }}</div>
                    </div>
                    <div class="grid h-10 w-10 place-items-center rounded-lg bg-base-200 {{ $kpi['text'] }}">
                        <x-mary-icon name="{{ $kpi['icon'] }}" class="h-5 w-5" />
                    </div>
                </div>
            @endforeach
        </div>

        {{-- Vitality + Activity --}}
        <div class="grid gap-4 lg:grid-cols-3">
            {{-- System vitality --}}
            <div class="card-interactive rounded-xl border border-base-300 bg-base-100 p-5 shadow-sm">
                <div class="flex items-start justify-between">
                    <div>
                        <h3 class="font-semibold">System Vitality</h3>
                        <p class="text-xs text-base-content/50">Operational health index</p>
                    </div>
                    @php($h = $systemHealth ?? 100)
                    <span class="badge badge-sm {{ $h > 80 ? 'badge-success' : ($h > 60 ? 'badge-warning' : 'badge-error') }}">
                        {{ $h > 80 ? 'Optimal' : ($h > 60 ? 'Stable' : 'Critical') }}
                    </span>
                </div>
                <div id="healthRadarChart" class="mt-2"></div>
                <div class="mt-2 flex items-center justify-between border-t border-base-200 pt-3 text-sm">
                    <span class="text-base-content/50">Operational uptime</span>
                    <span class="font-semibold">99.9%</span>
                </div>
            </div>

            {{-- Activity stream --}}
            <div class="card-interactive rounded-xl border border-base-300 bg-base-100 p-5 shadow-sm lg:col-span-2">
                <div class="mb-4 flex items-center justify-between">
                    <div>
                        <h3 class="font-semibold">Recent Activity</h3>
                        <p class="text-xs text-base-content/50">Live stream of system events</p>
                    </div>
                    <a href="{{ route('admin.activity-logs') }}" class="btn btn-ghost btn-xs">View all</a>
                </div>
                <div class="max-h-72 space-y-1 overflow-y-auto">
                    @forelse ($activities as $activity)
                        <div class="flex items-start gap-3 rounded-lg px-2 py-2 hover:bg-base-200/50">
                            <span class="mt-0.5 grid h-8 w-8 shrink-0 place-items-center rounded-full bg-primary/10 text-primary">
                                <x-mary-icon name="{{ $activity->action === 'created' ? 'o-plus' : ($activity->action === 'updated' ? 'o-pencil-square' : 'o-information-circle') }}" class="h-4 w-4" />
                            </span>
                            <div class="min-w-0 flex-1">
                                <p class="text-sm">
                                    <span class="font-medium">{{ $activity->user->name ?? 'System' }}</span>
                                    <span class="text-base-content/60">{{ $activity->action }}</span>
                                    <span class="font-medium">{{ class_basename($activity->model_type) }}</span>
                                </p>
                                <p class="text-xs text-base-content/40">{{ $activity->created_at->diffForHumans() }}</p>
                            </div>
                        </div>
                    @empty
                        <div class="flex flex-col items-center gap-2 py-10 text-base-content/40">
                            <x-mary-icon name="o-bolt-slash" class="h-8 w-8" />
                            <p class="text-sm">No recent activity.</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>

        {{-- Approvals queue --}}
        @if ($pendingTotal > 0)
            <div class="rounded-xl border border-warning/30 bg-warning/5 p-5">
                <div class="mb-3 flex items-center gap-2">
                    <x-mary-icon name="o-exclamation-triangle" class="h-5 w-5 text-warning" />
                    <h3 class="font-semibold">Pending Approvals</h3>
                </div>
                <div class="grid gap-3 sm:grid-cols-3">
                    @if (($pendingLeaveCount ?? 0) > 0)
                        <a href="{{ route('admin.requests.leave-approvals.index') }}" class="card-interactive flex items-center justify-between rounded-lg border border-base-300 bg-base-100 px-4 py-3 shadow-sm">
                            <span class="text-sm font-medium">Leave Requests</span>
                            <span class="badge badge-warning">{{ $pendingLeaveCount }}</span>
                        </a>
                    @endif
                    @if (($pendingExpenseCount ?? 0) > 0)
                        <a href="{{ route('admin.requests.finance') }}" class="card-interactive flex items-center justify-between rounded-lg border border-base-300 bg-base-100 px-4 py-3 shadow-sm">
                            <span class="text-sm font-medium">Expenses</span>
                            <span class="badge badge-warning">{{ $pendingExpenseCount }}</span>
                        </a>
                    @endif
                    @if (($pendingLoanCount ?? 0) > 0)
                        <a href="{{ route('admin.requests.items') }}" class="card-interactive flex items-center justify-between rounded-lg border border-base-300 bg-base-100 px-4 py-3 shadow-sm">
                            <span class="text-sm font-medium">Inventory Loans</span>
                            <span class="badge badge-warning">{{ $pendingLoanCount }}</span>
                        </a>
                    @endif
                </div>
            </div>
        @endif

        {{-- Module grid --}}
        <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
            @foreach ([
                ['title' => 'User Management', 'desc' => 'Accounts, roles and access control.', 'icon' => 'o-users', 'route' => 'admin.users.index', 'cta' => 'Manage users'],
                ['title' => 'Human Resources', 'desc' => 'Employees, attendance and leave.', 'icon' => 'o-identification', 'route' => 'admin.hr', 'cta' => 'Open HR hub'],
                ['title' => 'Inventory Control', 'desc' => 'Stock, equipment loans and audit trails.', 'icon' => 'o-cube', 'route' => 'admin.inventory', 'cta' => 'Open inventory'],
                ['title' => 'Financial Operations', 'desc' => 'Project budgets and expense approvals.', 'icon' => 'o-banknotes', 'route' => 'admin.finance', 'cta' => 'Open finance'],
                ['title' => 'Maintenance', 'desc' => 'Backups, logs and recycle bin.', 'icon' => 'o-wrench-screwdriver', 'route' => 'admin.maintenance.index', 'cta' => 'Open maintenance'],
                ['title' => 'Settings', 'desc' => 'System parameters and notifications.', 'icon' => 'o-cog-6-tooth', 'route' => 'admin.system-settings.index', 'cta' => 'Open settings'],
            ] as $mod)
                <div class="card-interactive flex flex-col rounded-xl border border-base-300 bg-base-100 p-5 shadow-sm">
                    <div class="mb-3 grid h-10 w-10 place-items-center rounded-lg bg-primary/10 text-primary">
                        <x-mary-icon name="{{ $mod['icon'] }}" class="h-5 w-5" />
                    </div>
                    <h4 class="font-semibold">{{ $mod['title'] }}</h4>
                    <p class="mt-1 mb-4 text-sm text-base-content/60">{{ $mod['desc'] }}</p>
                    <a href="{{ route($mod['route']) }}" class="btn btn-outline btn-sm mt-auto w-fit">{{ $mod['cta'] }}</a>
                </div>
            @endforeach
        </div>
    </div>

    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            var el = document.querySelector("#healthRadarChart");
            if (!el || typeof ApexCharts === 'undefined') return;
            new ApexCharts(el, {
                series: [{{ $systemHealth ?? 98 }}],
                chart: { height: 260, type: 'radialBar', sparkline: { enabled: false }, toolbar: { show: false } },
                plotOptions: {
                    radialBar: {
                        hollow: { size: '62%' },
                        track: { background: '#e3e8ef' },
                        dataLabels: {
                            name: { show: true, fontSize: '12px', color: '#64748b', offsetY: -6, formatter: () => 'Vitality' },
                            value: { fontSize: '30px', fontWeight: 700, color: '#0d1b2a', offsetY: 6, formatter: (v) => v + '%' }
                        }
                    }
                },
                fill: { colors: ['#f5a623'] },
                stroke: { lineCap: 'round' },
            }).render();
        });
    </script>
    @endpush
</x-layouts.app-shell>
