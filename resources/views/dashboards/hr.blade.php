<x-layouts.app-shell title="HR Hub">
    <div class="space-y-6">
        {{-- HR Hero Banner --}}
        <div class="glass-hero rounded-2xl p-6 sm:p-7 text-white shadow-xl relative overflow-hidden">
            <div class="absolute -right-10 -bottom-10 w-60 h-60 bg-blue-500/10 rounded-full blur-2xl pointer-events-none"></div>

            <div class="relative z-10 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                <div>
                    <div class="flex items-center gap-2 mb-1">
                        <span class="px-2.5 py-0.5 rounded-full bg-blue-500/20 text-blue-300 text-[10px] font-bold tracking-wider uppercase border border-blue-500/30">
                            People & Culture Hub
                        </span>
                        <span class="text-xs text-slate-400">· {{ now()->format('l, M d, Y') }}</span>
                    </div>
                    <h2 class="text-2xl sm:text-3xl font-extrabold tracking-tight font-display">
                        Human Resource Management
                    </h2>
                    <p class="text-xs sm:text-sm text-slate-300">
                        Workforce directory, daily shift attendance sheets, and leave authorizations.
                    </p>
                </div>

                <div class="flex items-center gap-2">
                    <a href="{{ route('hr.employees.index') }}" class="btn btn-sm btn-primary shadow-lg shadow-amber-500/20">
                        <x-mary-icon name="o-user-plus" class="w-4 h-4" /> Add Employee
                    </a>
                    <a href="{{ route('hr.attendance.index') }}" class="btn btn-sm bg-white/10 hover:bg-white/20 text-white border-white/10">
                        <x-mary-icon name="o-clock" class="w-4 h-4 text-emerald-400" /> Attendance Sheet
                    </a>
                </div>
            </div>
        </div>

        {{-- KPI Cards --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            @foreach ([
                ['label' => 'Total Workforce', 'value' => $employeeCount ?? 0, 'sub' => 'Registered Personnel', 'icon' => 'o-users', 'color' => 'text-primary', 'bg' => 'bg-primary/10'],
                ['label' => 'Active Status', 'value' => $activeEmployees ?? 0, 'sub' => 'Operational Staff', 'icon' => 'o-check-badge', 'color' => 'text-success', 'bg' => 'bg-success/10'],
                ['label' => 'On Leave Today', 'value' => $onLeaveTodayCount ?? 0, 'sub' => 'Authorized Absences', 'icon' => 'o-sun', 'color' => 'text-info', 'bg' => 'bg-info/10'],
                ['label' => 'Pending Filings', 'value' => $pendingLeaveApprovals ?? 0, 'sub' => 'Review Queue', 'icon' => 'o-clock', 'color' => 'text-warning', 'bg' => 'bg-warning/10'],
            ] as $kpi)
                <div class="card-interactive rounded-2xl border border-base-300/80 bg-base-100 p-5 shadow-sm">
                    <div class="flex items-center justify-between">
                        <div class="space-y-1">
                            <div class="text-[11px] font-bold uppercase tracking-wider text-base-content/50">{{ $kpi['label'] }}</div>
                            <div class="text-3xl font-black font-display {{ $kpi['color'] }}">{{ $kpi['value'] }}</div>
                            <div class="text-[11px] text-base-content/60 font-medium">{{ $kpi['sub'] }}</div>
                        </div>
                        <div class="p-3.5 rounded-2xl {{ $kpi['bg'] }} {{ $kpi['color'] }}">
                            <x-mary-icon name="{{ $kpi['icon'] }}" class="w-6 h-6" />
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="grid gap-6 lg:grid-cols-3">
            {{-- Attendance chart --}}
            <div class="card-interactive rounded-2xl border border-base-300/80 bg-base-100 p-5 shadow-sm lg:col-span-2">
                <div class="flex items-center justify-between mb-2">
                    <div>
                        <h3 class="font-bold text-base font-display">Workforce Attendance Flow</h3>
                        <p class="text-xs text-base-content/50">Weekly attendance volume and punctuality</p>
                    </div>
                </div>
                <div id="attendanceChart" class="mt-3"></div>
            </div>

            {{-- Department breakdown --}}
            <div class="card-interactive rounded-2xl border border-base-300/80 bg-base-100 p-5 shadow-sm">
                <h3 class="mb-3 font-bold text-base font-display">Department Distribution</h3>
                <div class="space-y-3">
                    @forelse ($departmentStats as $d)
                        <div class="flex items-center justify-between p-2.5 rounded-xl bg-base-200/50 text-xs">
                            <span class="font-semibold text-base-content truncate">{{ $d->name }}</span>
                            <span class="badge badge-primary badge-sm font-bold">{{ $d->total }} Staff</span>
                        </div>
                    @empty
                        <p class="text-sm text-base-content/40">No department data.</p>
                    @endforelse
                </div>
            </div>
        </div>

        {{-- Latest employees --}}
        <div class="card-interactive rounded-2xl border border-base-300/80 bg-base-100 shadow-sm overflow-hidden">
            <div class="flex items-center justify-between border-b border-base-200 px-5 py-4">
                <h3 class="font-bold text-base font-display">Recent Onboardings</h3>
                <a href="{{ route('hr.employees.index') }}" class="btn btn-ghost btn-xs text-primary">View all personnel</a>
            </div>
            <div class="overflow-x-auto">
                <table class="table table-zebra w-full text-sm">
                    <thead>
                        <tr class="text-xs uppercase bg-base-200/50 text-base-content/60">
                            <th>Employee</th>
                            <th>Department</th>
                            <th>Position</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($recentEmployees as $e)
                            <tr class="hover">
                                <td class="font-semibold">{{ $e->first_name }} {{ $e->last_name }}</td>
                                <td>{{ $e->department ?? 'General' }}</td>
                                <td>{{ $e->position ?? 'Staff' }}</td>
                                <td><span class="badge badge-success badge-sm font-semibold">{{ $e->status ?? 'Active' }}</span></td>
                            </tr>
                        @empty
                            <tr><td colspan="4" class="py-8 text-center text-base-content/40">No employee records.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            var el = document.querySelector("#attendanceChart");
            if (!el || typeof ApexCharts === 'undefined') return;
            new ApexCharts(el, {
                series: [{ name: 'Present', data: [28, 32, 34, 30, 35, 33, 31] }, { name: 'Late / Leave', data: [3, 2, 1, 4, 2, 1, 3] }],
                chart: { height: 240, type: 'area', toolbar: { show: false } },
                colors: ['#10b981', '#f59e0b'],
                stroke: { curve: 'smooth', width: 2.5 },
                fill: { type: 'gradient', gradient: { opacityFrom: 0.3, opacityTo: 0.05 } },
                xaxis: { categories: ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'], labels: { style: { colors: '#94a3b8', fontSize: '11px' } } },
                legend: { position: 'top', horizontalAlign: 'right', fontSize: '11px' }
            }).render();
        });
    </script>
    @endpush
</x-layouts.app-shell>
