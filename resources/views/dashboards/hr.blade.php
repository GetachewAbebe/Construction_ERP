<x-layouts.app-shell title="HR Dashboard">
    <div class="space-y-6">
        <div>
            <h2 class="text-xl font-semibold">Human Resources</h2>
            <p class="text-sm text-base-content/60">Workforce overview, attendance and leave.</p>
        </div>

        {{-- KPIs --}}
        <div class="grid grid-cols-2 gap-4 lg:grid-cols-4">
            @foreach ([
                ['label' => 'Employees', 'value' => $employeeCount ?? 0, 'icon' => 'o-users', 'accent' => 'border-l-primary', 'text' => 'text-primary'],
                ['label' => 'Active', 'value' => $activeEmployees ?? 0, 'icon' => 'o-check-badge', 'accent' => 'border-l-success', 'text' => 'text-success'],
                ['label' => 'On Leave Today', 'value' => $onLeaveTodayCount ?? 0, 'icon' => 'o-sun', 'accent' => 'border-l-info', 'text' => 'text-info'],
                ['label' => 'Pending Leaves', 'value' => $pendingLeaveApprovals ?? 0, 'icon' => 'o-clock', 'accent' => 'border-l-warning', 'text' => 'text-warning'],
            ] as $kpi)
                <div class="flex items-center justify-between rounded-lg border border-base-300 border-l-4 {{ $kpi['accent'] }} bg-base-100 px-4 py-3 shadow-sm">
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

        <div class="grid gap-4 lg:grid-cols-3">
            {{-- Attendance chart --}}
            <div class="rounded-xl border border-base-300 bg-base-100 p-5 shadow-sm lg:col-span-2">
                <h3 class="font-semibold">Attendance — last 7 days</h3>
                <p class="text-xs text-base-content/50">Present vs late check-ins</p>
                <div id="attendanceChart" class="mt-3"></div>
            </div>

            {{-- Department breakdown --}}
            <div class="rounded-xl border border-base-300 bg-base-100 p-5 shadow-sm">
                <h3 class="mb-3 font-semibold">Top Departments</h3>
                <div class="space-y-3">
                    @forelse ($departmentStats as $d)
                        <div class="flex items-center justify-between text-sm">
                            <span class="truncate">{{ $d->name }}</span>
                            <span class="badge badge-ghost badge-sm">{{ $d->total }}</span>
                        </div>
                    @empty
                        <p class="text-sm text-base-content/40">No department data.</p>
                    @endforelse
                </div>
            </div>
        </div>

        {{-- Latest employees --}}
        <div class="rounded-xl border border-base-300 bg-base-100 shadow-sm">
            <div class="flex items-center justify-between border-b border-base-200 px-5 py-3">
                <h3 class="font-semibold">Latest Employees</h3>
                <a href="{{ route('hr.employees.index') }}" class="btn btn-ghost btn-xs">View all</a>
            </div>
            <div class="overflow-x-auto">
                <table class="table table-sm">
                    <thead>
                        <tr class="text-[11px] uppercase tracking-wide text-base-content/60">
                            <th>Employee</th><th>Department</th><th>Position</th><th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($latestEmployees as $emp)
                            <tr>
                                <td class="font-medium">{{ $emp->name }}</td>
                                <td class="text-sm text-base-content/70">{{ $emp->department_rel->name ?? '—' }}</td>
                                <td class="text-sm text-base-content/70">{{ $emp->position_rel->name ?? '—' }}</td>
                                <td><span class="badge badge-sm {{ ($emp->status === 'Active') ? 'badge-success' : 'badge-ghost' }}">{{ $emp->status ?? '—' }}</span></td>
                            </tr>
                        @empty
                            <tr><td colspan="4" class="py-8 text-center text-base-content/40">No employees yet.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            var el = document.querySelector('#attendanceChart');
            if (!el || typeof ApexCharts === 'undefined') return;
            new ApexCharts(el, {
                chart: { type: 'bar', height: 280, toolbar: { show: false } },
                series: [
                    { name: 'Present', data: @json($onTimeData) },
                    { name: 'Late', data: @json($lateData) },
                ],
                colors: ['#16a34a', '#f5a623'],
                plotOptions: { bar: { borderRadius: 4, columnWidth: '45%' } },
                dataLabels: { enabled: false },
                xaxis: { categories: @json($chartLabels) },
                legend: { position: 'top', horizontalAlign: 'right' },
                grid: { borderColor: '#e3e8ef' },
            }).render();
        });
    </script>
    @endpush
</x-layouts.app-shell>
