@extends('layouts.app')

@section('title', 'Finance Overview | Natanem Engineering')

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
        <h1 class="display-4 fw-800 text-erp-deep mb-2 tracking-tight">Finance Dashboard</h1>

    @php
        $totalProjects = \App\Models\Project::count();
        $totalBudget = \App\Models\Project::sum('budget');
        $totalExpenses = \App\Models\Expense::sum('amount');
        $remainingBudget = $totalBudget - $totalExpenses;
        $usagePercentage = $totalBudget > 0 ? round(($totalExpenses / $totalBudget) * 100) : 0;
    @endphp

    {{-- HIGHLIGHT METRICS --}}
    <div class="row g-4 mb-5">
        <div class="col-md-4">
            <div class="hardened-glass stagger-entrance h-100">
                <div class="metric-icon">
                    <i class="bi bi-diagram-3 fs-4"></i>
                </div>
                <h6 class="text-muted text-uppercase fw-bold small mb-1">Total Active Projects</h6>
                <div class="h2 fw-800 mb-0">{{ $totalProjects }}</div>
                <div class="text-success small fw-bold mt-2 d-flex align-items-center gap-1">
                    <span class="erp-pill-dot" style="background: #38ef7d;"></span>
                    Operational
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="hardened-glass stagger-entrance h-100">
                <div class="metric-icon" style="background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);">
                    <i class="bi bi-wallet2 fs-4"></i>
                </div>
                <h6 class="text-muted text-uppercase fw-bold small mb-1">Total Budget Portfolio</h6>
                <div class="h2 fw-800 mb-0">${{ number_format($totalBudget, 2) }}</div>
                <p class="text-muted small mt-2 mb-0">Allocated across all sites</p>
            </div>
        </div>

        <div class="col-md-4">
            <div class="hardened-glass stagger-entrance h-100">
                <div class="metric-icon" style="background: linear-gradient(135deg, #fa709a 0%, #fee140 100%);">
                    <i class="bi bi-graph-down-arrow fs-4"></i>
                </div>
                <h6 class="text-muted text-uppercase fw-bold small mb-1">Total Expenses Used</h6>
                <div class="h2 fw-800 mb-0 text-danger">${{ number_format($totalExpenses, 2) }}</div>
                <div class="progress mt-3" style="height: 10px; border-radius: 10px;">
                    <div class="progress-bar" role="progressbar" style="width: {{ $usagePercentage }}%; background: var(--gradient-primary);" aria-valuenow="{{ $usagePercentage }}" aria-valuemin="0" aria-valuemax="100"></div>
                </div>
                <div class="d-flex justify-content-between mt-1">
                    <span class="text-muted small">{{ $usagePercentage }}% Exhausted</span>
                    <span class="text-muted small">${{ number_format($remainingBudget, 2) }} Left</span>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4 mb-5">
        <div class="col-lg-12 stagger-entrance">
            <div class="glass-card-global p-4">
                <div class="d-flex align-items-center justify-content-between mb-4">
                    <div>
                        <h5 class="fw-800 text-erp-deep mb-0">Project Portfolio Health</h5>
                        <p class="text-muted small mb-0">Comparison of allocated budget vs actual expenses</p>
                    </div>
                </div>
                <div id="portfolioChart" style="min-height: 400px;"></div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col stagger-entrance">
            <div class="glass-card-global p-4">
                <div class="d-flex align-items-center justify-content-between mb-4">
                    <h5 class="fw-800 text-erp-deep mb-0">Recent Projects</h5>
                    <a href="{{ Auth::user()->hasRole('Administrator') ? route('admin.finance.projects.index') : route('finance.projects.index') }}" class="btn btn-sm btn-outline-erp-deep rounded-pill px-3">Manage all</a>
                </div>
                
                <div class="row g-3">
                    @forelse(\App\Models\Project::latest()->take(3)->get() as $p)
                        <div class="col-md-4">
                            <div class="p-3 border rounded-4 bg-white hover-shadow transition-all">
                                <h6 class="fw-bold mb-1">{{ $p->name }}</h6>
                                <p class="text-muted small mb-2">{{ Str::limit($p->description, 50) }}</p>
                                <div class="d-flex justify-content-between small">
                                    <span class="text-muted">Budget:</span>
                                    <span class="fw-bold text-erp-deep">${{ number_format($p->budget, 0) }}</span>
                                </div>
                                <div class="progress mt-2" style="height: 4px;">
                                    <div class="progress-bar bg-success" style="width: {{ $p->budget_usage_percentage }}%"></div>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="col text-center py-5">
                            <p class="text-muted">No projects found. <a href="{{ Auth::user()->hasRole('Administrator') ? route('admin.finance.projects.index') : route('finance.projects.create') }}">Create one now</a></p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        @php
            $projects = \App\Models\Project::with('expenses')->take(8)->get();
            $names = $projects->pluck('name')->toArray();
            $budgets = $projects->pluck('budget')->toArray();
            $expenses = $projects->map(function($p) {
                return $p->expenses->sum('amount');
            })->toArray();
        @endphp

        var options = {
            series: [{
                name: 'Budget',
                data: {!! json_encode($budgets) !!}
            }, {
                name: 'Expenses',
                data: {!! json_encode($expenses) !!}
            }],
            chart: {
                type: 'bar',
                height: 400,
                stacked: false,
                toolbar: { show: false },
                fontFamily: 'Outfit, sans-serif'
            },
            colors: ['#059669', '#f59e0b'],
            plotOptions: {
                bar: {
                    horizontal: false,
                    columnWidth: '55%',
                    borderRadius: 8
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
            xaxis: {
                categories: {!! json_encode($names) !!},
                labels: {
                    style: { colors: '#64748b', fontSize: '12px' }
                }
            },
            yaxis: {
                title: { text: 'Amount ($)', style: { color: '#64748b' } },
                labels: {
                    formatter: function (val) { return "$" + val.toLocaleString(); },
                    style: { colors: '#64748b' }
                }
            },
            fill: {
                opacity: 1
            },
            tooltip: {
                y: {
                    formatter: function (val) { return "$" + val.toLocaleString(); }
                }
            },
            grid: {
                borderColor: '#f1f5f9'
            },
            legend: {
                position: 'top',
                horizontalAlign: 'right',
                fontWeight: 600,
                markers: { radius: 12 }
            }
        };

        var chart = new ApexCharts(document.querySelector("#portfolioChart"), options);
        chart.render();
    });
</script>
@endpush
@endsection
