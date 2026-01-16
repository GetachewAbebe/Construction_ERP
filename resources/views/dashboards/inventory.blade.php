@extends('layouts.app')

@section('title', 'Logistics Command Center | Natanem Engineering')

@section('content')
<style>
    :root {
        --dash-bg: #fdfdfd;
        --cmd-sidebar: #0f172a;
        --glass-border: rgba(226, 232, 240, 0.8);
    }

    body {
        background-color: var(--dash-bg);
    }

    /* Command Layout */
    .command-bridge {
        background: #ffffff;
        border: 1px solid var(--glass-border);
        border-radius: 24px;
        overflow: hidden;
        box-shadow: 0 10px 25px -5px rgba(0,0,0,0.02);
    }

    .sidebar-control {
        background: var(--cmd-sidebar);
        color: #ffffff;
        padding: 3rem 2rem;
        display: flex;
        flex-column: column;
        justify-content: space-between;
    }

    /* Metric HUD */
    .metric-hud-item {
        background: #f8fafc;
        border: 1px solid #f1f5f9;
        border-radius: 16px;
        padding: 1.25rem;
        transition: all 0.2s ease;
    }
    .metric-hud-item:hover {
        background: #ffffff;
        border-color: #cbd5e1;
        transform: translateX(5px);
    }

    .health-gauge-box {
        background: radial-gradient(circle at center, #f8fafc 0%, #ffffff 100%);
        border-right: 1px solid #f1f5f9;
    }

    .btn-command {
        background: rgba(255, 255, 255, 0.05);
        border: 1px solid rgba(255, 255, 255, 0.1);
        color: #ffffff;
        font-weight: 700;
        padding: 1rem;
        border-radius: 12px;
        transition: all 0.2s ease;
        text-align: left;
        display: flex;
        align-items: center;
        gap: 12px;
    }
    .btn-command:hover {
        background: rgba(255, 255, 255, 0.15);
        color: #ffffff;
        transform: translateY(-2px);
    }
    .btn-command.active-op {
        background: #ffffff;
        color: var(--cmd-sidebar);
        border: none;
    }

    .track-pill {
        font-size: 0.65rem;
        letter-spacing: 0.1em;
        font-weight: 900;
        text-transform: uppercase;
        padding: 4px 12px;
        border-radius: 20px;
    }
</style>

<div class="dashboard-container mt-3">
    <div class="container-fluid px-md-5 py-2">
        
        {{-- Clean Header Section --}}
        <div class="mb-4">
            <h1 class="h2 fw-900 text-erp-deep tracking-tight-custom">Inventory Dashboard</h1>
        </div>

        {{-- Main Command Bridge --}}
        <div class="command-bridge mb-5">
            <div class="row g-0">
                {{-- Health & Intelligence Column --}}
                <div class="col-lg-4 health-gauge-box p-5 text-center">
                    <div class="mb-5">
                        <h5 class="fw-900 text-erp-deep mb-0">Items Health</h5>
                    </div>

                    <div class="position-relative d-inline-flex align-items-center justify-content-center mb-4">
                        <svg width="220" height="220" viewBox="0 0 120 120">
                            <circle cx="60" cy="60" r="54" fill="none" stroke="#f1f5f9" stroke-width="8" />
                            <circle cx="60" cy="60" r="54" fill="none" stroke="var(--erp-primary)" stroke-width="8" 
                                    stroke-dasharray="339.29" stroke-dashoffset="{{ 339.29 - (339.29 * $healthPercentage / 100) }}"
                                    stroke-linecap="round" transform="rotate(-90 60 60)" style="transition: stroke-dashoffset 1.5s ease;" />
                        </svg>
                        <div class="position-absolute text-center">
                            <div class="display-4 fw-900 mb-0 text-erp-deep">{{ $healthPercentage }}%</div>
                            <div class="small text-muted fw-800 uppercase letter-spacing-1">Stable</div>
                        </div>
                    </div>

                    <div class="p-4 bg-light-soft rounded-4 border border-light mx-2">
                        <div class="d-flex justify-content-between small fw-800 mb-2">
                            <span>TOTAL ASSETS</span>
                            <span class="text-erp-deep">{{ $totalItems }}</span>
                        </div>
                        <div class="progress" style="height: 6px; border-radius: 10px; background: #e2e8f0;">
                            <div class="progress-bar bg-erp-primary" role="progressbar" style="width: {{ $healthPercentage }}%"></div>
                        </div>
                    </div>
                </div>

                {{-- Distribution HUD --}}
                <div class="col-lg-5 p-5 border-start border-light">
                    <div class="mb-5">
                        <h5 class="fw-900 text-erp-deep mb-0">Items Composition</h5>
                    </div>

                    <div class="d-grid gap-3">
                        <div class="metric-hud-item d-flex align-items-center justify-content-between bg-erp-deep text-white shadow-lg mb-2">
                            <div class="d-flex align-items-center gap-3">
                                <div class="p-2 bg-white bg-opacity-10 rounded-3 text-white"><i class="bi bi-truck-flatbed"></i></div>
                                <div>
                                    <div class="fw-800">Active Field Loans</div>
                                    <div class="x-small text-white-50 fw-600">Dispatched Assets</div>
                                </div>
                            </div>
                            <div class="h4 fw-900 mb-0">{{ $openLoanCount }}</div>
                        </div>
                        <div class="metric-hud-item d-flex align-items-center justify-content-between">
                            <div class="d-flex align-items-center gap-3">
                                <div class="p-2 bg-success text-white rounded-3"><i class="bi bi-check2-circle"></i></div>
                                <div>
                                    <div class="fw-800 text-erp-deep">Stable Stock</div>
                                    <div class="x-small text-muted fw-600">Items with Qty > 5</div>
                                </div>
                            </div>
                            <div class="h4 fw-900 text-erp-deep mb-0">{{ $stableItemsCount }}</div>
                        </div>

                        <div class="metric-hud-item d-flex align-items-center justify-content-between">
                            <div class="d-flex align-items-center gap-3">
                                <div class="p-2 bg-warning text-white rounded-3"><i class="bi bi-exclamation-square"></i></div>
                                <div>
                                    <div class="fw-800 text-erp-deep">Risk Alerts</div>
                                    <div class="x-small text-muted fw-600">Items with Qty 1-5</div>
                                </div>
                            </div>
                            <div class="h4 fw-900 text-warning mb-0">{{ $lowStockCount }}</div>
                        </div>

                        <div class="metric-hud-item d-flex align-items-center justify-content-between">
                            <div class="d-flex align-items-center gap-3">
                                <div class="p-2 bg-danger text-white rounded-3"><i class="bi bi-trash-fill"></i></div>
                                <div>
                                    <div class="fw-800 text-erp-deep">Deleted Records</div>
                                    <div class="x-small text-muted fw-600">Zero quantity historicals</div>
                                </div>
                            </div>
                            <div class="h4 fw-900 text-danger mb-0">{{ $zeroStockCount }}</div>
                        </div>

                    </div>
                </div>

                {{-- Operational Sidebar --}}
                <div class="col-lg-3 sidebar-control">
                    <div class="top-ops">
                        <h5 class="fw-900 mb-4 tracking-tight">Quick Actions</h5>
                        
                        <div class="d-grid gap-3">
                            <a href="{{ route('inventory.items.index') }}" class="btn-command">
                                <i class="bi bi-search"></i>
                                <span>Explore Items</span>
                            </a>

                            <a href="{{ route('inventory.items.create') }}" class="btn-command active-op">
                                <i class="bi bi-plus-circle-fill"></i>
                                <span>Register Items</span>
                            </a>

                            <a href="{{ (Auth::user()->hasRole('Administrator') || Auth::user()->hasRole('Admin')) ? route('inventory.loans.index') : route('inventory.loans.create') }}" class="btn-command">
                                <i class="bi bi-arrow-left-right"></i>
                                <span>Loan Management</span>
                            </a>
                        </div>
                    </div>

                </div>
            </div>
        </div>

        {{-- Row 2: Deep Analytics & Critical List --}}
        <div class="row g-4">
            {{-- Analytics Chart --}}
            <div class="col-lg-8">
                <div class="bg-white border border-light p-4 p-md-5 rounded-4 shadow-sm h-100">
                    <div class="d-flex justify-content-between align-items-start mb-5">
                        <div>
                            <h5 class="fw-900 text-erp-deep mb-1">Volume Intelligence</h5>
                            <p class="text-muted small">Resource distribution analysis (Top 10 Materials)</p>
                        </div>
                        <div class="badge bg-light text-erp-deep border px-3 py-2 fw-800">RANKED BY QTY</div>
                    </div>
                    <div id="stockLevelsChart" style="min-height: 380px;"></div>
                </div>
            </div>

            {{-- Critical Alerts --}}
            <div class="col-lg-4">
                <div class="bg-white border border-light p-4 p-md-5 rounded-4 shadow-sm h-100">
                    <div class="d-flex align-items-center justify-content-between mb-4">
                        <h5 class="fw-900 text-erp-deep mb-0">At Risk</h5>
                        <span class="badge bg-danger text-white rounded-pill px-2 py-1 x-small fw-900 uppercase">PRIORITY</span>
                    </div>
                    
                    <div class="alerts-tray scrollbar-hide">
                        @forelse($recentAlerts as $alert)
                            <div class="metric-hud-item p-3 mb-2 d-flex justify-content-between align-items-center">
                                <div>
                                    <div class="fw-900 text-erp-deep small">{{ Str::limit($alert->name, 25) }}</div>
                                    <div class="x-small text-muted fw-800">{{ $alert->item_no }}</div>
                                </div>
                                <div class="text-end">
                                    <div class="fw-900 text-danger">{{ $alert->quantity }}</div>
                                    <div class="x-small text-muted fw-bold">{{ $alert->unit_of_measurement }}</div>
                                </div>
                            </div>
                        @empty
                            <div class="text-center py-5">
                                <i class="bi bi-shield-check text-success display-4 opacity-25"></i>
                                <p class="text-muted small fw-600 mt-2">Logistics are Stable.</p>
                            </div>
                        @endforelse
                    </div>

                    @if(count($recentAlerts) > 0)
                        <div class="mt-4 pt-3 border-top text-center">
                            <a href="{{ route('inventory.items.index') }}?status=low_stock" class="text-erp-deep fw-800 x-small text-uppercase tracking-wider text-decoration-none">
                                View Registry <i class="bi bi-arrow-right ms-1"></i>
                            </a>
                        </div>
                    @endif
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
                name: 'Units',
                data: {!! json_encode($chartData) !!}
            }],
            chart: {
                type: 'bar',
                height: 380,
                toolbar: { show: false },
                fontFamily: 'Outfit, sans-serif'
            },
            plotOptions: {
                bar: {
                    borderRadius: 6,
                    columnWidth: '40%',
                    distributed: true,
                    dataLabels: { position: 'top' }
                }
            },
            dataLabels: {
                enabled: true,
                offsetY: -20,
                style: {
                    fontSize: '11px',
                    colors: ["#334155"],
                    fontWeight: 800
                }
            },
            colors: ['#059669', '#10b981', '#34d399', '#6ee7b7', '#a7f3d0', '#064e3b', '#065f46', '#047857', '#059669', '#10b981'],
            xaxis: {
                categories: {!! json_encode($chartCategories) !!},
                labels: {
                    style: {
                        colors: '#64748b',
                        fontSize: '11px',
                        fontWeight: 700
                    },
                    rotate: -45,
                    trim: true
                },
                axisBorder: { show: false },
                axisTicks: { show: false }
            },
            yaxis: {
                labels: {
                    style: { colors: '#64748b', fontWeight: 600 }
                }
            },
            grid: {
                borderColor: '#f1f5f9',
                strokeDashArray: 3
            },
            tooltip: { theme: 'dark' },
            legend: { show: false }
        };

        var chart = new ApexCharts(document.querySelector("#stockLevelsChart"), options);
        chart.render();
    });
</script>
@endpush
@endsection
