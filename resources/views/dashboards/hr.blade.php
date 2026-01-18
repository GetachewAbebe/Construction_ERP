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
    
    /* New Avatar Styling */
    .avatar-pill {
        width: 48px;
        height: 48px;
        border-radius: 50%;
        object-fit: cover;
        border: 2px solid #ffffff;
        box-shadow: 0 4px 10px rgba(0,0,0,0.1);
        transition: transform 0.2s ease;
    }
    
    .avatar-fallback {
        width: 48px;
        height: 48px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 700;
        font-size: 1.1rem; 
        color: white;
        border: 2px solid #ffffff;
        box-shadow: 0 4px 10px rgba(0,0,0,0.1);
        background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
    }

    .table-modern tbody tr:hover .avatar-pill,
    .table-modern tbody tr:hover .avatar-fallback {
        transform: scale(1.1);
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
                        <h5 class="fw-800 text-erp-deep mb-0">Attendance & Punctuality</h5>
                        <p class="text-muted small mb-0">Weekly breakdown of On-Time vs. Late check-ins</p>
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
                    <a href="{{ (Auth::user()->hasRole('Administrator') || Auth::user()->hasRole('Admin')) ? route('admin.hr.employees.index') : route('hr.employees.index') }}" class="btn btn-sm btn-light rounded-pill px-3 fw-bold">View All</a>
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
                                        @php
                                            // Pre-calculate fallback gradient
                                            $gradients = [
                                                'linear-gradient(135deg, #6366f1 0%, #a855f7 100%)', // Indigo-Purple
                                                'linear-gradient(135deg, #3b82f6 0%, #06b6d4 100%)', // Blue-Cyan
                                                'linear-gradient(135deg, #10b981 0%, #34d399 100%)', // Emerald-Teal
                                                'linear-gradient(135deg, #f59e0b 0%, #fbbf24 100%)', // Amber-Yellow
                                                'linear-gradient(135deg, #ec4899 0%, #f43f5e 100%)'  // Pink-Rose
                                            ];
                                            $index = ord(substr($emp->first_name, 0, 1)) % count($gradients);
                                            $bg = $gradients[$index];
                                            $initials = strtoupper(substr($emp->first_name, 0, 1)) . strtoupper(substr($emp->last_name, 0, 1));
                                        @endphp

                                        @if($emp->profile_picture_url)
                                            <div class="position-relative">
                                                <img src="{{ $emp->profile_picture_url }}" class="avatar-pill" alt="{{ $emp->first_name }}"
                                                     onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                                                <div class="avatar-fallback" style="background: {{ $bg }}; display: none;">
                                                    {{ $initials }}
                                                </div>
                                            </div>
                                        @else
                                            <div class="avatar-fallback" style="background: {{ $bg }};">
                                                {{ $initials }}
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
                    <a href="{{ (Auth::user()->hasRole('Administrator') || Auth::user()->hasRole('Admin')) ? route('admin.hr.employees.index') : route('hr.employees.index') }}" class="btn btn-outline-erp-deep w-100 py-2 rounded-4 fw-bold">
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
                name: 'On Time',
                data: {!! json_encode($onTimeData) !!}
            }, {
                name: 'Late',
                data: {!! json_encode($lateData) !!}
            }],
            chart: {
                type: 'bar',
                height: 350,
                stacked: false, // Grouped side-by-side
                toolbar: { show: false },
                fontFamily: 'Outfit, sans-serif'
            },
            plotOptions: {
                bar: {
                    horizontal: false,
                    columnWidth: '55%',
                    borderRadius: 6,
                    borderRadiusApplication: 'end'
                },
            },
            dataLabels: {
                enabled: false
            },
            stroke: {
                show: true,
                width: 2,
                colors: ['transparent']
            },
            colors: ['#10b981', '#f59e0b'], // Emerald, Amber
            xaxis: {
                categories: {!! json_encode($chartLabels) !!},
                labels: {
                    style: { colors: '#64748b', fontSize: '12px', fontWeight: 600 }
                },
                axisBorder: { show: false },
                axisTicks: { show: false }
            },
            yaxis: {
                labels: {
                    style: { colors: '#64748b' }
                }
            },
            fill: {
                opacity: 1
            },
            grid: {
                borderColor: '#f1f5f9',
                strokeDashArray: 4,
                yaxis: {
                    lines: { show: true }
                }
            },
            legend: {
                position: 'top',
                horizontalAlign: 'right',
                markers: { radius: 12 },
            },
            tooltip: {
                theme: 'light',
                y: {
                    formatter: function (val) {
                        return val + " employees"
                    }
                }
            }
        };

        var chart = new ApexCharts(document.querySelector("#attendanceChart"), options);
        chart.render();
    });
</script>
@endpush
@endsection
