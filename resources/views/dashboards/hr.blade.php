@extends('layouts.app')

@section('title', 'HR Overview | Natanem Engineering')

@push('head')
<style>
    .metric-icon {
        width: 64px;
        height: 64px;
        border-radius: 20px;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-bottom: 1.5rem;
        background: var(--erp-primary);
        color: white;
        box-shadow: 0 10px 20px rgba(30, 64, 175, 0.2);
    }
</style>
@endpush

@section('content')
<div class="py-4 px-2">
    {{-- Premium Header --}}
    <div class="d-flex flex-column mb-5">
        <h1 class="display-4 fw-800 text-erp-deep mb-2 tracking-tight">Human Resource Dashboard</h1>
    </div>

    {{-- HIGHLIGHT METRICS --}}
    <div class="row g-4 mb-5">
        <div class="col-md-3">
            <div class="hardened-glass stagger-entrance">
                <div class="metric-icon">
                    <i class="bi bi-people fs-4"></i>
                </div>
                <h6 class="text-muted text-uppercase fw-bold small mb-1">Total Employees</h6>
                <div class="h2 fw-800 mb-0">{{ $employeeCount }}</div>
                <div class="text-primary small fw-bold mt-2">Active Human Resource</div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="hardened-glass stagger-entrance">
                <div class="metric-icon" style="background: linear-gradient(135deg, #10b981 0%, #34d399 100%);">
                    <i class="bi bi-person-check fs-4"></i>
                </div>
                <h6 class="text-muted text-uppercase fw-bold small mb-1">Active Staff</h6>
                <div class="h2 fw-800 mb-0">{{ $activeEmployees }}</div>
                <div class="text-success small fw-bold mt-2">On Duty Today</div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="hardened-glass stagger-entrance">
                <div class="metric-icon" style="background: linear-gradient(135deg, #f59e0b 0%, #fbbf24 100%);">
                    <i class="bi bi-briefcase fs-4"></i>
                </div>
                <h6 class="text-muted text-uppercase fw-bold small mb-1">On Leave Today</h6>
                <div class="h2 fw-800 mb-0">{{ $onLeaveTodayCount }}</div>
                <div class="text-warning small fw-bold mt-2">Active Approvals</div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="hardened-glass stagger-entrance">
                <div class="metric-icon" style="background: linear-gradient(135deg, #f43f5e 0%, #fb7185 100%);">
                    <i class="bi bi-bell fs-4"></i>
                </div>
                <h6 class="text-muted text-uppercase fw-bold small mb-1">Pending Requests</h6>
                <div class="h2 fw-800 mb-0 text-danger">{{ $pendingLeaveApprovals }}</div>
                <div class="text-danger small fw-bold mt-2">Action Required</div>
            </div>
        </div>
    </div>

    {{-- MAIN CONTENT ROW --}}
    <div class="row g-4 mb-5">
        {{-- Attendance Trend Chart --}}
        <div class="col-lg-12">
            <div class="glass-card-global stagger-entrance p-4 h-100 transition-all hover-translate-y">
                <div class="d-flex align-items-center justify-content-between mb-4">
                    <div>
                        <h5 class="fw-800 text-erp-deep mb-0">Attendance Trend</h5>
                        <p class="text-muted small mb-0">Check-in statistics for the last 7 days</p>
                    </div>
                </div>
                <div id="attendanceChart" style="min-height: 350px;"></div>
            </div>
        </div>
    </div>

    <div class="row g-4">
        {{-- Recent Activity Table --}}
        <div class="col-lg-8 stagger-entrance">
            <div class="glass-card-global h-100">
                <div class="card-header border-0 bg-transparent py-4 px-4 d-flex justify-content-between align-items-center">
                    <h5 class="fw-800 text-erp-deep mb-0">Recent Activity</h5>
                    <a href="{{ Auth::user()->hasRole('Administrator') ? route('admin.hr.employees.index') : route('hr.employees.index') }}" class="btn btn-sm btn-light rounded-pill px-3 fw-bold">View All</a>
                </div>
                <div class="table-responsive px-4 pb-4">
                    <table class="table table-modern align-middle mb-0">
                        <thead>
                            <tr>
                                <th>Employee</th>
                                <th>Role</th>
                                <th>Status</th>
                                <th class="text-end">Joined</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($latestEmployees as $emp)
                            <tr class="transition-all">
                                <td class="py-3">
                                    <div class="d-flex align-items-center gap-3">
                                        @if($emp->profile_picture)
                                            <img src="{{ asset('storage/' . $emp->profile_picture) }}" class="avatar-pill">
                                        @else
                                            <div class="avatar-pill bg-light text-muted d-flex align-items-center justify-content-center fw-bold">
                                                {{ substr($emp->first_name, 0, 1) }}
                                            </div>
                                        @endif
                                        <div>
                                            <div class="fw-bold text-dark">{{ optional($emp)->first_name }} {{ optional($emp)->last_name }}</div>
                                            <div class="text-muted" style="font-size: 0.75rem;">{{ $emp->email }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div class="fw-600 text-dark small">{{ $emp->position ?? 'N/A' }}</div>
                                    <div class="text-muted" style="font-size: 0.75rem;">{{ $emp->department ?? 'General' }}</div>
                                </td>
                                <td>
                                    @php
                                        $colors = match($emp->status) {
                                            'Active' => ['bg' => 'rgba(16, 185, 129, 0.1)', 'text' => '#10b981'],
                                            'On Leave' => ['bg' => 'rgba(245, 158, 11, 0.1)', 'text' => '#f59e0b'],
                                            'Terminated' => ['bg' => 'rgba(244, 63, 94, 0.1)', 'text' => '#f43f5e'],
                                            default => ['bg' => 'rgba(100, 116, 139, 0.1)', 'text' => '#64748b']
                                        };
                                    @endphp
                                    <span class="badge rounded-pill fw-bold" style="background: {{ $colors['bg'] }}; color: {{ $colors['text'] }}; padding: 0.5rem 1rem;">
                                        {{ $emp->status ?? 'Active' }}
                                    </span>
                                </td>
                                <td class="text-end text-muted small">
                                    {{ $emp->created_at->format('M d, Y') }}
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="4" class="text-center py-5 text-muted">No recent data available.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- Department Distribution --}}
        <div class="col-lg-4 stagger-entrance">
            <div class="glass-card-global h-100 p-4">
                <h5 class="fw-800 text-erp-deep mb-1">Department Stats</h5>
                <p class="text-muted small mb-4">Human Resource distribution analysis</p>
                
                <div class="dept-list">
                    @forelse($departmentStats as $dept)
                    <div class="mb-4">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span class="fw-bold text-dark">{{ $dept->name }}</span>
                            <span class="badge bg-light text-erp-deep rounded-pill">{{ $dept->total }}</span>
                        </div>
                        <div class="progress" style="height: 8px; border-radius: 4px; background: rgba(0,0,0,0.05);">
                            <div class="progress-bar" role="progressbar" 
                                 style="width: {{ $employeeCount > 0 ? ($dept->total / $employeeCount) * 100 : 0 }}%; background: linear-gradient(90deg, #4f46e5, #7c3aed); border-radius: 4px;">
                            </div>
                        </div>
                    </div>
                    @empty
                    <p class="text-center text-muted small py-4">No department data available.</p>
                    @endforelse
                </div>

                <div class="mt-4 pt-4 border-top">
                    <a href="{{ Auth::user()->hasRole('Administrator') ? route('admin.hr.employees.index') : route('hr.employees.index') }}" class="btn btn-outline-erp-deep w-100 py-2 rounded-4 fw-bold">
                        Browse Full Registry
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {


        var options = {
            series: [{
                name: 'Check-ins',
                data: {!! json_encode($chartData) !!}
            }],
            chart: {
                type: 'area',
                height: 350,
                toolbar: { show: false },
                fontFamily: 'Outfit, sans-serif',
                zoom: { enabled: false }
            },
            dataLabels: {
                enabled: false
            },
            stroke: {
                curve: 'smooth',
                width: 3,
                colors: ['#059669']
            },
            fill: {
                type: 'gradient',
                gradient: {
                    shadeIntensity: 1,
                    opacityFrom: 0.45,
                    opacityTo: 0.05,
                    stops: [20, 100, 100, 100]
                }
            },
            xaxis: {
                categories: {!! json_encode($chartLabels) !!},
                labels: {
                    style: { colors: '#64748b', fontSize: '12px' }
                }
            },
            yaxis: {
                labels: {
                    style: { colors: '#64748b' }
                }
            },
            grid: {
                borderColor: '#f1f5f9',
            },
            tooltip: {
                theme: 'light',
                x: { show: true }
            },
            markers: {
                size: 5,
                colors: ['#059669'],
                strokeColors: '#fff',
                strokeWidth: 2,
                hover: { size: 7 }
            }
        };

        var chart = new ApexCharts(document.querySelector("#attendanceChart"), options);
        chart.render();
    });
</script>
@endpush
@endsection
