@extends('layouts.app')

@section('title', 'Logistics Command Center | Enterprise Intelligence')

@section('content')
<style>
    :root {
        --dash-bg: #f8fafc;
        --cmd-sidebar: #0f172a;
        --emerald-glow: rgba(16, 185, 129, 0.15);
        --glass-bg: rgba(255, 255, 255, 0.7);
        --glass-border: rgba(255, 255, 255, 0.4);
    }

    body {
        background-color: var(--dash-bg);
        background-image: 
            radial-gradient(at 0% 0%, rgba(16, 185, 129, 0.05) 0px, transparent 50%),
            radial-gradient(at 100% 100%, rgba(59, 130, 246, 0.05) 0px, transparent 50%);
    }

    /* Premium Command Bridge */
    .command-bridge {
        background: var(--glass-bg);
        backdrop-filter: blur(20px);
        -webkit-backdrop-filter: blur(20px);
        border: 1px solid var(--glass-border);
        border-radius: 35px;
        overflow: hidden;
        box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.05);
        margin-top: 1rem;
    }

    .sidebar-control {
        background: linear-gradient(165deg, #0f172a 0%, #1e293b 100%);
        color: #ffffff;
        padding: 3.5rem 2.5rem;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        position: relative;
        overflow: hidden;
    }

    .sidebar-control::after {
        content: '';
        position: absolute;
        top: -50%;
        left: -50%;
        width: 200%;
        height: 200%;
        background: radial-gradient(circle, rgba(16, 185, 129, 0.1) 0%, transparent 70%);
        pointer-events: none;
    }

    /* Metric HUD Enhancement */
    .metric-hud-item {
        background: #ffffff;
        border: 1px solid rgba(226, 232, 240, 0.6);
        border-radius: 20px;
        padding: 1.5rem;
        transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        position: relative;
        overflow: hidden;
    }

    .metric-hud-item:hover {
        transform: translateY(-5px);
        box-shadow: 0 15px 30px rgba(0,0,0,0.04);
        border-color: #cbd5e1;
    }

    .metric-hud-item.active-loan {
        background: linear-gradient(135deg, #0f172a 0%, #334155 100%);
        color: #fff;
        border: none;
    }

    .health-gauge-box {
        background: linear-gradient(135deg, rgba(255,255,255,0.4) 0%, rgba(248,250,252,0.8) 100%);
        position: relative;
    }

    /* Kinetic Navigation Buttons */
    .btn-command {
        background: rgba(255, 255, 255, 0.03);
        backdrop-filter: blur(5px);
        border: 1px solid rgba(255, 255, 255, 0.08);
        color: #94a3b8;
        font-weight: 700;
        padding: 1.25rem;
        border-radius: 16px;
        transition: all 0.3s ease;
        text-align: left;
        display: flex;
        align-items: center;
        gap: 15px;
        text-decoration: none;
        margin-bottom: 1rem;
    }

    .btn-command:hover {
        background: rgba(255, 255, 255, 0.1);
        color: #ffffff;
        transform: translateX(8px);
        border-color: rgba(255, 255, 255, 0.2);
    }

    .btn-command.active-op {
        background: #ffffff;
        color: var(--cmd-sidebar);
        border: none;
        box-shadow: 0 10px 20px rgba(0,0,0,0.2);
        transform: scale(1.02);
    }

    .btn-command.active-op:hover {
        transform: scale(1.02) translateX(0);
    }

    /* Gauge Refinement */
    .gauge-container {
        position: relative;
        filter: drop-shadow(0 0 15px var(--emerald-glow));
    }

    .health-indicator-ring {
        transition: stroke-dashoffset 2s cubic-bezier(0.4, 0, 0.2, 1);
    }

    /* Typography Upgrades */
    .tracking-enterprise {
        letter-spacing: -0.02em;
        line-height: 1.1;
    }
    
    .label-enterprise {
        font-size: 0.7rem;
        font-weight: 900;
        text-transform: uppercase;
        letter-spacing: 0.15em;
        color: #64748b;
    }

    /* Animations */
    @keyframes float {
        0% { transform: translateY(0px); }
        50% { transform: translateY(-10px); }
        100% { transform: translateY(0px); }
    }
    .floating-asset { animation: float 6s ease-in-out infinite; }

    .pulse-lite {
        animation: pulse-lite 2s infinite;
    }
    @keyframes pulse-lite {
        0% { transform: scale(1); opacity: 1; }
        50% { transform: scale(1.05); opacity: 0.8; }
        100% { transform: scale(1); opacity: 1; }
    }
</style>

<div class="dashboard-container mt-4">
    <div class="container-fluid px-md-5 py-2">
        
        {{-- High-End Header --}}
        <div class="page-header-premium mb-5">
            <h1 class="display-3 fw-900 text-erp-deep mb-0 tracking-tight">Inventory Dashboard</h1>
        </div>

        {{-- Main Command Bridge --}}
        <div class="command-bridge mb-5 stagger-entrance">
            <div class="row g-0">
                {{-- Health Intelligence --}}
                <div class="col-lg-4 health-gauge-box p-5 text-center border-end border-light border-opacity-50">
                    <div class="mb-5">
                        <span class="label-enterprise">Overall Status</span>
                        <h4 class="fw-900 text-erp-deep mt-2">Stock Health</h4>
                    </div>

                    <div class="gauge-container d-inline-flex align-items-center justify-content-center mb-5">
                        <svg width="240" height="240" viewBox="0 0 120 120">
                            <!-- Background Track -->
                            <circle cx="60" cy="60" r="52" fill="none" stroke="#f1f5f9" stroke-width="10" />
                            <!-- Health Ring -->
                            <circle cx="60" cy="60" r="52" fill="none" stroke="url(#emeraldGradient)" 
                                    stroke-width="10" 
                                    class="health-indicator-ring"
                                    stroke-dasharray="326.72" 
                                    stroke-dashoffset="{{ 326.72 - (326.72 * $healthPercentage / 100) }}"
                                    stroke-linecap="round" 
                                    transform="rotate(-90 60 60)" />
                            <defs>
                                <linearGradient id="emeraldGradient" x1="0%" y1="0%" x2="100%" y2="0%">
                                    <stop offset="0%" stop-color="#10b981" />
                                    <stop offset="100%" stop-color="#059669" />
                                </linearGradient>
                            </defs>
                        </svg>
                        <div class="position-absolute text-center">
                            <div class="display-3 fw-900 mb-0 text-erp-deep">{{ $healthPercentage }}%</div>
                            <div class="label-enterprise">Stable</div>
                        </div>
                    </div>

                    <div class="p-4 bg-white bg-opacity-40 backdrop-blur rounded-4 border border-white border-opacity-60 shadow-sm mx-2">
                        <div class="d-flex justify-content-between label-enterprise mb-3">
                            <span>Total Items in System</span>
                            <span class="text-erp-deep fw-900">{{ $totalItems }}</span>
                        </div>
                        <div class="progress" style="height: 10px; border-radius: 20px; background: rgba(226, 232, 240, 0.5);">
                            <div class="progress-bar bg-success shadow-sm" role="progressbar" 
                                 style="width: {{ $healthPercentage }}%; border-radius: 20px;"></div>
                        </div>
                    </div>
                </div>

                {{-- Distribution Matrix --}}
                <div class="col-lg-5 p-5">
                    <div class="mb-5">
                        <span class="label-enterprise">Stock Overview</span>
                        <h4 class="fw-900 text-erp-deep mt-2">Current Inventory</h4>
                    </div>

                    <div class="d-grid gap-3">
                        <a href="{{ (Auth::user()->hasRole('Administrator') || Auth::user()->hasRole('Admin')) ? route('inventory.loans.index') : route('inventory.loans.create') }}" 
                           class="metric-hud-item active-loan d-flex align-items-center justify-content-between text-decoration-none transition-all">
                            <div class="d-flex align-items-center gap-4">
                                {{-- Icon removed --}}
                                <div>
                                    <div class="fw-800 fs-5">Items on Loan</div>
                                    <div class="label-enterprise mt-1">Dispatched Assets</div>
                                </div>
                            </div>
                            <div class="display-6 fw-900 mb-0">{{ $openLoanCount }}</div>
                        </a>

                        <div class="metric-hud-item d-flex align-items-center justify-content-between">
                            <div class="d-flex align-items-center gap-4">
                                {{-- Icon removed --}}
                                <div>
                                    <div class="fw-800 text-erp-deep fs-5">Stable Stock</div>
                                    <div class="label-enterprise mt-1">Healthy levels (>5 Units)</div>
                                </div>
                            </div>
                            <div class="display-6 fw-900 text-erp-deep mb-0">{{ $stableItemsCount }}</div>
                        </div>

                        <div class="metric-hud-item d-flex align-items-center justify-content-between">
                            <div class="d-flex align-items-center gap-4">
                                {{-- Icon removed --}}
                                <div>
                                    <div class="fw-800 text-erp-deep fs-5">Low Stock</div>
                                    <div class="label-enterprise mt-1">Risk of exhaustion (1-5 Units)</div>
                                </div>
                            </div>
                            <div class="display-6 fw-900 text-warning mb-0">{{ $lowStockCount }}</div>
                        </div>

                        <div class="metric-hud-item d-flex align-items-center justify-content-between">
                            <div class="d-flex align-items-center gap-4">
                                {{-- Icon removed --}}
                                <div>
                                    <div class="fw-800 text-erp-deep fs-5">Out of Stock</div>
                                    <div class="label-enterprise mt-1">Zero quantity records</div>
                                </div>
                            </div>
                            <div class="display-6 fw-900 text-danger mb-0">{{ $zeroStockCount }}</div>
                        </div>
                    </div>
                </div>

                {{-- Unified Operations Sidebar --}}
                <div class="col-lg-3 sidebar-control">
                    <div class="top-ops">
                        <div class="mb-5">
                            <span class="label-enterprise text-white-50">Management Hub</span>
                            <h4 class="fw-900 text-white mt-2">Quick Actions</h4>
                        </div>
                        
                        <div class="d-grid">
                            <a href="{{ route('inventory.items.index') }}" class="btn-command">
                                {{-- Icon removed --}}
                                <span>All Inventory</span>
                            </a>

                            <a href="{{ route('inventory.items.create') }}" class="btn-command active-op">
                                {{-- Icon removed --}}
                                <span>Add New Item</span>
                            </a>

                            <a href="{{ (Auth::user()->hasRole('Administrator') || Auth::user()->hasRole('Admin')) ? route('inventory.loans.index') : route('inventory.loans.create') }}" class="btn-command">
                                <div class="p-2 rounded-3 bg-white bg-opacity-10"><i class="bi bi-arrow-left-right"></i></div>
                                <span>Loan Management</span>
                            </a>

                            <a href="{{ route('inventory.logs.index') }}" class="btn-command">
                                <div class="p-2 rounded-3 bg-white bg-opacity-10"><i class="bi bi-clock-history"></i></div>
                                <span>Activity Logs</span>
                            </a>
                        </div>
                    </div>

                    <div class="bottom-branding text-center pt-5 border-top border-white border-opacity-10 mt-5">
                        <p class="label-enterprise text-white-50 mb-0">Security Version 4.2.0</p>
                    </div>
                </div>
            </div>
        </div>

        {{-- Row 2: Deep Analytics & Critical List --}}
        <div class="row g-5">
            {{-- Enterprise Chart --}}
            <div class="col-lg-8">
                <div class="bg-white p-5 rounded-4 shadow-soft border border-light h-100 position-relative overflow-hidden">
                    <div class="d-flex justify-content-between align-items-start mb-5 position-relative z-index-1">
                        <div>
                            <span class="label-enterprise">Stock Analytics</span>
                            <h4 class="fw-900 text-erp-deep mt-2">Top Items in Stock</h4>
                            <p class="text-muted small mb-0">Breakdown of leading resource volumes</p>
                        </div>
                        <button class="btn btn-light rounded-pill px-4 x-small fw-900 text-uppercase tracking-widest border">
                            Real-time Data
                        </button>
                    </div>
                    <div id="stockLevelsChart" style="min-height: 400px;" class="position-relative z-index-1"></div>
                    
                    {{-- Subtle background decoration --}}
                    <div class="position-absolute top-0 end-0 p-5 mt-5 opacity-05">
                        <i class="bi bi-bar-chart-fill display-1"></i>
                    </div>
                </div>
            </div>

            {{-- Critical Alerts --}}
            <div class="col-lg-4">
                <div class="bg-white p-5 rounded-4 shadow-soft border border-light h-100">
                    <div class="d-flex align-items-center justify-content-between mb-5">
                        <div>
                            <span class="label-enterprise">Critical Alerts</span>
                            <h4 class="fw-900 text-erp-deep mt-2">Low Stock Alerts</h4>
                        </div>
                        <div class="bg-danger p-2 rounded-circle pulse-lite shadow-soft"></div>
                    </div>
                    
                    <div class="alerts-tray pe-2" style="max-height: 400px; overflow-y: auto;">
                        @forelse($recentAlerts as $alert)
                            <div class="metric-hud-item p-4 mb-3 border-start border-4 border-danger shadow-sm">
                                <div class="d-flex justify-content-between align-items-center mb-1">
                                    <div class="fw-900 text-erp-deep">{{ Str::limit($alert->name, 20) }}</div>
                                    <div class="fw-900 text-danger fs-5">{{ $alert->quantity }}</div>
                                </div>
                                <div class="d-flex justify-content-between align-items-center">
                                    <div class="label-enterprise x-small">{{ $alert->item_no }}</div>
                                    <div class="label-enterprise x-small">{{ $alert->unit_of_measurement }}</div>
                                </div>
                            </div>
                        @empty
                            <div class="text-center py-5 opacity-50">
                                <i class="bi bi-shield-check text-success display-1"></i>
                                <h6 class="fw-900 text-erp-deep mt-4">Zero Logistics Friction</h6>
                                <p class="text-muted x-small uppercase fw-800 tracking-widest mt-2">All assets are above reserve levels.</p>
                            </div>
                        @endforelse
                    </div>

                    @if(count($recentAlerts) > 0)
                        <div class="mt-5 text-center">
                            <a href="{{ route('inventory.items.index') }}?status=low_stock" 
                               class="btn btn-erp-deep w-100 rounded-pill py-3 fw-900 tracking-widest uppercase fs-7 shadow-lg">
                                Manage Risk Registry
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
                name: 'Stock Volume',
                data: {!! json_encode($chartData) !!}
            }],
            chart: {
                type: 'bar',
                height: 400,
                toolbar: { show: false },
                fontFamily: 'Outfit, sans-serif',
                animations: {
                    enabled: true,
                    easing: 'easeinout',
                    speed: 800,
                    animateOnRender: true
                }
            },
            plotOptions: {
                bar: {
                    borderRadius: 12,
                    columnWidth: '35%',
                    distributed: true,
                    dataLabels: { position: 'top' }
                }
            },
            stroke: {
                show: true,
                width: 2,
                colors: ['transparent']
            },
            dataLabels: {
                enabled: true,
                offsetY: -30,
                style: {
                    fontSize: '12px',
                    colors: ["#0f172a"],
                    fontWeight: 900
                },
                formatter: function (val) {
                    return val.toLocaleString();
                }
            },
            colors: ['#10b981', '#059669', '#0f172a', '#334155', '#64748b', '#94a3b8', '#cbd5e1', '#e2e8f0', '#064e3b', '#065f46'],
            xaxis: {
                categories: {!! json_encode($chartCategories) !!},
                labels: {
                    style: {
                        colors: '#64748b',
                        fontSize: '11px',
                        fontWeight: 900,
                        cssClass: 'label-enterprise'
                    },
                    rotate: -45,
                    trim: true,
                    offsetY: 5
                },
                axisBorder: { show: false },
                axisTicks: { show: false }
            },
            yaxis: {
                labels: {
                    style: { colors: '#94a3b8', fontWeight: 600 }
                }
            },
            grid: {
                borderColor: '#f1f5f9',
                strokeDashArray: 4,
                padding: { top: 20 }
            },
            tooltip: { 
                theme: 'dark',
                y: {
                    formatter: function (val) {
                        return val + " Units Available";
                    }
                }
            },
            legend: { show: false }
        };

        var chart = new ApexCharts(document.querySelector("#stockLevelsChart"), options);
        chart.render();
    });
</script>
@endpush
@endsection
