@extends('layouts.app')

@section('title', 'Admin Command Center | Natanem Engineering')

@section('content')
<div class="py-4 px-2">
    {{-- Page Header --}}
    <div class="page-header-premium mb-5">
        <h1 class="display-3 fw-900 text-erp-deep mb-2 tracking-tight">System Administration</h1>
        <p class="text-muted fs-5 mb-0">Central command for user management, approvals, and system configuration.</p>
    </div>

    {{-- EXECUTIVE INSIGHTS / SYSTEM PULSE --}}
    <div class="row g-4 mb-5 stagger-entrance">
        {{-- System Health Radar --}}
        <div class="col-lg-4">
            <div class="erp-card h-100 p-4 hardened-glass position-relative overflow-hidden" style="min-height: 380px;">
                <div class="d-flex justify-content-between align-items-start mb-4">
                    <div>
                        <h5 class="fw-800 text-erp-deep mb-1">System Vitality</h5>
                        <p class="text-muted small">Real-time operational health index</p>
                    </div>
                    @if(($systemHealth ?? 100) > 80)
                        <div class="badge bg-success-soft text-success rounded-pill px-3 py-2 fw-bold border border-success-subtle">
                            OPTIMAL
                        </div>
                    @elseif(($systemHealth ?? 100) > 60)
                        <div class="badge bg-warning-soft text-warning rounded-pill px-3 py-2 fw-bold border border-warning-subtle">
                            STABLE
                        </div>
                    @else
                        <div class="badge bg-danger-soft text-danger rounded-pill px-3 py-2 fw-bold border border-danger-subtle">
                            CRITICAL
                        </div>
                    @endif
                </div>
                
                <div id="healthRadarChart"></div>
                
                <div class="position-absolute bottom-0 start-0 w-100 p-4 bg-white-50 border-top" style="backdrop-filter: blur(5px);">
                    <div class="d-flex justify-content-between align-items-center">
                        <span class="text-muted small fw-bold">OPERATIONAL UPTIME</span>
                        <span class="text-erp-deep fw-800 fs-5">99.9%</span>
                    </div>
                </div>
            </div>
        </div>

        {{-- Live Pulse / Activity Stream --}}
        <div class="col-lg-8">
            <div class="erp-card h-100 p-4 hardened-glass" style="min-height: 380px;">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <div>
                        <h5 class="fw-800 text-erp-deep mb-1">Enterprise Pulse</h5>
                        <p class="text-muted small">Live stream of critical system activities</p>
                    </div>
                    <a href="{{ route('admin.activity-logs') }}" class="btn btn-sm btn-white border-0 shadow-sm rounded-pill px-3 fw-bold">
                        View Full History
                    </a>
                </div>

                <div class="activity-stream" style="max-height: 250px; overflow-y: auto;">
                    @forelse($activities as $activity)
                    <div class="activity-item d-flex gap-3 mb-3 pb-3 border-bottom border-light-subtle">
                        <div class="activity-icon-container">
                            <div class="rounded-circle bg-primary-soft text-primary d-flex align-items-center justify-content-center" style="width: 32px; height: 32px;">
                                <i class="bi {{ $activity->action == 'created' ? 'bi-plus-circle' : ($activity->action == 'updated' ? 'bi-pencil-square' : 'bi-info-circle') }} small"></i>
                            </div>
                        </div>
                        <div class="activity-content flex-grow-1">
                            <div class="d-flex justify-content-between align-items-start">
                                <p class="mb-0 small fw-800 text-erp-deep">
                                    {{ $activity->user->name ?? 'System' }} 
                                    <span class="fw-normal text-muted">{{ $activity->action }}</span> 
                                    {{ class_basename($activity->model_type) }}
                                </p>
                                <span class="x-small text-muted">{{ $activity->created_at->diffForHumans() }}</span>
                            </div>
                        </div>
                    </div>
                    @empty
                    <div class="text-center py-5">
                        <i class="bi bi-activity fs-1 text-muted opacity-25"></i>
                        <p class="text-muted small mt-2">No recent activity detected.</p>
                    </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    {{-- ALERT / ACTION CENTER (Only if pending items exist) --}}
    @if(($pendingLeaveCount ?? 0) > 0 || ($pendingExpenseCount ?? 0) > 0 || ($pendingLoanCount ?? 0) > 0)
    <div class="card bg-danger-soft border-0 mb-5 shadow-sm overflow-hidden stagger-entrance" style="border-radius: 20px;">
        <div class="card-body p-4">
            <div class="d-flex align-items-center gap-3 mb-3">
                <div class="bg-danger text-white rounded-circle d-flex align-items-center justify-content-center pulse" style="width: 40px; height: 40px;">
                    <i class="bi bi-exclamation-triangle-fill"></i>
                </div>
                <h5 class="fw-800 text-erp-deep mb-0">High Priority Approvals Queue</h5>
            </div>
            
            <div class="row g-3">
                @if(($pendingLeaveCount ?? 0) > 0)
                    <div class="col-md-4">
                        <a href="{{ route('admin.requests.leave-approvals.index') }}" class="d-flex align-items-center justify-content-between p-3 bg-white rounded-3 shadow-sm text-decoration-none transition-all hover-translate-y">
                            <span class="text-muted fw-bold small text-uppercase">Leave Requests</span>
                            <span class="badge bg-danger rounded-pill px-3">{{ $pendingLeaveCount }}</span>
                        </a>
                    </div>
                @endif
                @if(($pendingExpenseCount ?? 0) > 0)
                    <div class="col-md-4">
                        <a href="{{ route('admin.requests.finance') }}" class="d-flex align-items-center justify-content-between p-3 bg-white rounded-3 shadow-sm text-decoration-none transition-all hover-translate-y">
                            <span class="text-muted fw-bold small text-uppercase">Financial Requisitions</span>
                            <span class="badge bg-danger rounded-pill px-3">{{ $pendingExpenseCount }}</span>
                        </a>
                    </div>
                @endif
                @if(($pendingLoanCount ?? 0) > 0)
                    <div class="col-md-4">
                        <a href="{{ route('admin.requests.items') }}" class="d-flex align-items-center justify-content-between p-3 bg-white rounded-3 shadow-sm text-decoration-none transition-all hover-translate-y">
                            <span class="text-muted fw-bold small text-uppercase">Inventory Loans</span>
                            <span class="badge bg-danger rounded-pill px-3">{{ $pendingLoanCount }}</span>
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </div>
    @endif

    {{-- MODULE NAVIGATION GRID --}}
    <div class="row g-4">
        {{-- Users --}}
        <div class="col-md-6 col-lg-4">
            <div class="erp-card h-100 p-4 stagger-entrance">
                {{-- Icon removed per user request --}}
                <h5 class="fw-800 mb-2">User Management</h5>
                <p class="text-muted small mb-4">
                    Manage system accounts, assign roles, and control access permissions.
                </p>
                <div class="mt-auto">
                    <a href="{{ route('admin.users.index') }}" class="btn btn-erp-deep rounded-pill px-4 fw-bold w-100">
                        Manage Users
                    </a>
                    <a href="{{ route('admin.roles.index') }}" class="btn btn-sm btn-link text-muted w-100 mt-2 text-decoration-none">
                        Manage Roles
                    </a>
                </div>
            </div>
        </div>

        {{-- HR Hub --}}
        <div class="col-md-6 col-lg-4">
            <div class="erp-card h-100 p-4 stagger-entrance">
                {{-- Icon removed per user request --}}
                <h5 class="fw-800 mb-2">Human Resources</h5>
                <p class="text-muted small mb-4">
                    View employee directory, attendance logs, and leave history.
                </p>
                <div class="mt-auto">
                    <a href="{{ route('admin.hr') }}" class="btn btn-outline-erp-deep rounded-pill px-4 fw-bold w-100">
                        Enter HR Hub
                    </a>
                </div>
            </div>
        </div>

        {{-- Inventory Hub --}}
        <div class="col-md-6 col-lg-4">
            <div class="erp-card h-100 p-4 stagger-entrance">
                {{-- Icon removed per user request --}}
                <h5 class="fw-800 mb-2">Inventory Control</h5>
                <p class="text-muted small mb-4">
                    Track material stock, manage equipment loans, and view audit trails.
                </p>
                <div class="mt-auto">
                    <a href="{{ route('admin.inventory') }}" class="btn btn-outline-erp-deep rounded-pill px-4 fw-bold w-100">
                        Enter Stock Manager
                    </a>
                </div>
            </div>
        </div>

        {{-- Finance Hub --}}
        <div class="col-md-6 col-lg-4">
            <div class="erp-card h-100 p-4 stagger-entrance">
                {{-- Icon removed per user request --}}
                <h5 class="fw-800 mb-2">Financial Operations</h5>
                <p class="text-muted small mb-4">
                    Oversee project budgets, approve expenses, and analyze spending.
                </p>
                <div class="mt-auto">
                    <a href="{{ route('admin.finance') }}" class="btn btn-outline-erp-deep rounded-pill px-4 fw-bold w-100">
                        Enter Finance Hub
                    </a>
                </div>
            </div>
        </div>

        {{-- Logs & Trash (Maintenance) --}}
        <div class="col-md-6 col-lg-4">
            <div class="erp-card h-100 p-4 stagger-entrance">
                {{-- Icon removed per user request --}}
                <h5 class="fw-800 mb-2">System Maintenance</h5>
                <p class="text-muted small mb-4">
                    View activity logs, system health, backups, and restore deleted items.
                </p>
                <div class="mt-auto">
                    <div class="d-grid gap-2">
                        <a href="{{ route('admin.maintenance.index') }}" class="btn btn-sm btn-white border shadow-sm fw-bold">Maintenance</a>
                        <a href="{{ route('admin.trash.index') }}" class="btn btn-sm btn-white border shadow-sm fw-bold">Recycle Bin</a>
                        <a href="{{ route('admin.activity-logs') }}" class="btn btn-sm btn-white border shadow-sm fw-bold">Activity Logs</a>
                    </div>
                </div>
            </div>
        </div>

        {{-- Global Settings --}}
        <div class="col-md-6 col-lg-4">
            <div class="erp-card h-100 p-4 stagger-entrance">
                {{-- Icon removed per user request --}}
                <h5 class="fw-800 mb-2">Global Config</h5>
                <p class="text-muted small mb-4">
                    Adjust system parameters, attendance rules, and notification templates.
                </p>
                <div class="mt-auto">
                    <div class="d-grid gap-2">
                        <a href="{{ route('admin.attendance-settings.index') }}" class="btn btn-sm btn-white border shadow-sm fw-bold">Attendance Rules</a>
                        <a href="{{ route('admin.system-settings.index') }}" class="btn btn-sm btn-white border shadow-sm fw-bold">System Settings</a>
                        <a href="{{ route('admin.notification-templates.index') }}" class="btn btn-sm btn-white border shadow-sm fw-bold">Notifications</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // System Health Radar Chart
        var healthOptions = {
            series: [{{ $systemHealth ?? 98 }}],
            chart: {
                height: 300,
                type: 'radialBar',
                toolbar: { show: false }
            },
            plotOptions: {
                radialBar: {
                    startAngle: -135,
                    endAngle: 135,
                    hollow: {
                        margin: 0,
                        size: '70%',
                        background: 'transparent',
                    },
                    track: {
                        background: '#e2e8f0',
                        strokeWidth: '97%',
                        margin: 5,
                    },
                    dataLabels: {
                        name: {
                            show: true,
                            fontSize: '14px',
                            fontWeight: 800,
                            color: '#64748b',
                            offsetY: -10
                        },
                        value: {
                            offsetY: 15,
                            fontSize: '32px',
                            fontWeight: 900,
                            color: '#1e293b',
                            formatter: function (val) {
                                return val + "%";
                            }
                        }
                    }
                }
            },
            fill: {
                type: 'gradient',
                gradient: {
                    shade: 'dark',
                    type: 'horizontal',
                    shadeIntensity: 0.5,
                    gradientToColors: ['#3b82f6'],
                    inverseColors: true,
                    opacityFrom: 1,
                    opacityTo: 1,
                    stops: [0, 100]
                }
            },
            stroke: {
                lineCap: 'round'
            },
            labels: ['VITALITY INDEX'],
        };

        var chart = new ApexCharts(document.querySelector("#healthRadarChart"), healthOptions);
        chart.render();
    });
</script>
@endpush
@endsection
