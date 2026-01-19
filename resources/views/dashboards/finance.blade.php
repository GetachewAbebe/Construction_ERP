@extends('layouts.app')

@section('title', 'Finance Overview | Natanem Engineering')

@section('content')
<div class="py-4 px-2">
    {{-- Page Header --}}
    <div class="page-header-premium mb-5">
        <h1 class="display-3 fw-900 text-erp-deep mb-0 tracking-tight">Finance Dashboard</h1>
    </div>

    {{-- HIGHLIGHT METRICS --}}
    <div class="row g-4 mb-5">
        <div class="col-md-4">
            <div class="hardened-glass stagger-entrance h-100 p-4">
                {{-- Icon removed per user request --}}
                <h6 class="text-muted text-uppercase fw-bold small mb-1">Total Active Projects</h6>
                <div class="h2 fw-800 mb-0">{{ $totalProjects }}</div>
                <div class="text-success small fw-bold mt-2 d-flex align-items-center gap-1">
                    <span class="rounded-circle bg-success" style="width: 8px; height: 8px;"></span>
                    Operational
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="hardened-glass stagger-entrance h-100 p-4">
                {{-- Icon removed per user request --}}
                <h6 class="text-muted text-uppercase fw-bold small mb-1">Total Project Portfolio</h6>
                <div class="h2 fw-800 mb-0"><small class="text-muted fw-normal fs-6">ETB</small> {{ number_format($totalBudget, 0) }}</div>
                <p class="text-muted small mt-2 mb-0">Aggregate budget across all sites</p>
            </div>
        </div>

        <div class="col-md-4">
            <div class="hardened-glass stagger-entrance h-100 p-4">
                {{-- Icon removed per user request --}}
                <h6 class="text-muted text-uppercase fw-bold small mb-1">Certified Expenditures</h6>
                <div class="h2 fw-800 mb-0 text-danger"><small class="text-muted fw-normal fs-6">ETB</small> {{ number_format($totalExpenses, 0) }}</div>
                <div class="progress mt-3" style="height: 10px; border-radius: 10px;">
                    <div class="progress-bar" role="progressbar" style="width: {{ $usagePercentage }}%; background: var(--erp-primary);" aria-valuenow="{{ $usagePercentage }}" aria-valuemin="0" aria-valuemax="100"></div>
                </div>
                <div class="d-flex justify-content-between mt-1">
                    <span class="text-muted small fw-bold">{{ $usagePercentage }}% Portfolio Exhausted</span>
                    <span class="text-muted small"><small class="fw-normal">ETB</small> {{ number_format($remainingBudget, 0) }} Left</span>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4 mb-5">
        <div class="col-lg-12">
            <div class="erp-card p-4 shadow-lg rounded-4">
                <div class="d-flex align-items-center justify-content-between mb-4">
                    <div>
                        <h5 class="fw-800 text-erp-deep mb-1">Site-specific Financial Health</h5>
                        <p class="text-muted small mb-0">Correlation between allocated funds and actual field spending.</p>
                    </div>
                </div>
                <div id="portfolioChart" style="min-height: 400px;"></div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col stagger-entrance">
            <div class="erp-card p-4 shadow-lg rounded-4">
                <div class="d-flex align-items-center justify-content-between mb-4 pb-2 border-bottom">
                    <h5 class="fw-800 text-erp-deep mb-0">Active Construction Sites</h5>
                    <a href="{{ (Auth::user()->hasRole('Administrator') || Auth::user()->hasRole('Admin')) ? route('admin.finance.projects.index') : route('finance.projects.index') }}" class="btn btn-sm btn-erp-deep rounded-pill px-4">Detailed Registry</a>
                </div>
                
                <div class="row g-4">
                    @forelse($recentProjects as $p)
                        <div class="col-md-4">
                            <div class="p-4 border rounded-4 bg-light-soft hover-lift transition-all">
                                <h6 class="fw-800 text-erp-deep mb-1">{{ $p->name }}</h6>
                                <p class="text-muted small mb-3">{{ \Illuminate\Support\Str::limit($p->description, 60) }}</p>
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <span class="text-muted small fw-bold text-uppercase">Total Budget</span>
                                    <span class="fw-800 text-erp-deep"><small class="text-muted fw-normal x-small">ETB</small> {{ number_format($p->budget, 0) }}</span>
                                </div>
                                <div class="progress" style="height: 6px; border-radius: 10px;">
                                    @php
                                        $usage = 0;
                                        if($p->budget > 0 && ($p->expenses_sum_amount || $p->total_expenses)) {
                                            $val = $p->expenses_sum_amount ?? $p->total_expenses;
                                            $usage = ($val / $p->budget) * 100;
                                        }
                                    @endphp
                                    <div class="progress-bar bg-success rounded-pill" style="width: {{ $usage }}%"></div>
                                </div>
                                <div class="text-end mt-1">
                                    <span class="x-small fw-800 text-muted">{{ round($usage) }}% Used</span>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="col text-center py-5">
                            <p class="text-muted fw-bold">No active construction sites found.</p>
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
        var options = {
            series: [{
                name: 'Budget',
                data: {!! json_encode($portfolioBudgets) !!}
            }, {
                name: 'Spending',
                data: {!! json_encode($portfolioExpenses) !!}
            }],
            chart: {
                type: 'bar',
                height: 400,
                stacked: false,
                toolbar: { show: false },
                fontFamily: 'Outfit, sans-serif'
            },
            colors: ['#059669', '#ef4444'],
            plotOptions: {
                bar: {
                    horizontal: false,
                    columnWidth: '45%',
                    borderRadius: 10
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
                categories: {!! json_encode($portfolioLabels) !!},
                labels: {
                    style: { colors: '#64748b', fontSize: '12px', fontWeight: 600 }
                }
            },
            yaxis: {
                title: { text: 'Currency (ETB)', style: { color: '#64748b', fontWeight: 800 } },
                labels: {
                    formatter: function (val) { return val.toLocaleString() + " ETB"; },
                    style: { colors: '#64748b', fontWeight: 600 }
                }
            },
            fill: {
                opacity: 1
            },
            tooltip: {
                y: {
                    formatter: function (val) { return val.toLocaleString() + " ETB"; }
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
