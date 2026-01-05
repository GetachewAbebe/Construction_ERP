@extends('layouts.app')

@section('title', 'Inventory Dashboard')

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
        box-shadow: 0 10px 20px rgba(5, 150, 105, 0.2);
    }
    
    .stat-card {
        background: white;
        border-radius: 16px;
        padding: 1.5rem;
        transition: all 0.3s ease;
        border: 1px solid rgba(0, 0, 0, 0.05);
    }
    
    .stat-card:hover {
        background: #f8f9fa;
        transform: translateX(8px);
        border-color: var(--erp-primary);
    }
    
    .action-button {
        background: white;
        border: 2px solid #e9ecef;
        border-radius: 14px;
        padding: 1.25rem 1.5rem;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        position: relative;
        overflow: hidden;
    }
    
    .action-button::before {
        content: '';
        position: absolute;
        top: 0;
        left: -100%;
        width: 100%;
        height: 100%;
        background: var(--gradient);
        transition: left 0.3s ease;
        z-index: 0;
    }
    
    .action-button:hover::before {
        left: 0;
    }
    
    .action-button:hover {
        border-color: transparent;
        transform: translateX(8px);
        box-shadow: 0 8px 24px rgba(0, 0, 0, 0.12);
    }
    
    .action-button:hover * {
        color: white !important;
        position: relative;
        z-index: 1;
    }
    
    .page-title {
        font-size: 2.5rem;
        font-weight: 800;
        background: linear-gradient(135deg, var(--erp-deep) 0%, var(--erp-primary) 100%);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
        margin-bottom: 0.5rem;
    }
    
    .badge-modern {
        padding: 0.5rem 1rem;
        border-radius: 12px;
        font-weight: 600;
        font-size: 0.875rem;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
    }
    
    .progress-modern {
        height: 8px;
        border-radius: 10px;
        background: #e9ecef;
        overflow: hidden;
    }
    
    .progress-bar-modern {
        height: 100%;
        border-radius: 10px;
        background: var(--gradient);
        transition: width 0.6s ease;
    }
    
    .chart-placeholder {
        height: 200px;
        background: linear-gradient(180deg, rgba(115, 175, 111, 0.1) 0%, rgba(115, 175, 111, 0.05) 100%);
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        border: 2px dashed rgba(115, 175, 111, 0.3);
    }
    
    .floating-badge {
        position: absolute;
        top: 1rem;
        right: 1rem;
        background: rgba(255, 255, 255, 0.95);
        backdrop-filter: blur(10px);
        padding: 0.5rem 1rem;
        border-radius: 20px;
        font-size: 0.75rem;
        font-weight: 600;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
    }
</style>
@endpush

@section('content')
    <div class="dashboard-container">
        <div class="container">

<div class="py-4 px-2">
    {{-- Premium Header Section --}}
        <h1 class="display-4 fw-800 text-erp-deep mb-2 tracking-tight">Inventory Dashboard</h1>

    <div class="row g-4 mb-5">
        {{-- Total Items --}}
        <div class="col-md-4">
            <div class="hardened-glass stagger-entrance h-100">
                <div class="metric-icon" style="background: var(--gradient-primary);">
                    <i class="bi bi-box-seam fs-4"></i>
                </div>
                <h6 class="text-muted text-uppercase fw-bold small mb-1">Total Materials</h6>
                <div class="display-5 fw-800 text-erp-deep mb-2">{{ $totalItems ?? 0 }}</div>
                <div class="premium-badge d-inline-block" style="width: fit-content;">In Stock</div>
            </div>
        </div>

        {{-- Low Stock --}}
        <div class="col-md-4">
            <div class="hardened-glass stagger-entrance h-100">
                <div class="metric-icon" style="background: var(--gradient-warning);">
                    <i class="bi bi-exclamation-triangle fs-4"></i>
                </div>
                <h6 class="text-muted text-uppercase fw-bold small mb-1">Low Stock</h6>
                <div class="display-5 fw-800 text-warning mb-2">{{ $lowStockCount ?? 0 }}</div>
                <div class="badge bg-warning-subtle text-warning px-3 py-1 rounded-pill small fw-bold">Action Required</div>
            </div>
        </div>

        {{-- Out of Stock --}}
        <div class="col-md-4">
            <div class="hardened-glass stagger-entrance h-100">
                <div class="metric-icon" style="background: var(--gradient-danger);">
                    <i class="bi bi-x-circle fs-4"></i>
                </div>
                <h6 class="text-muted text-uppercase fw-bold small mb-1">Out of Stock</h6>
                <div class="display-5 fw-800 text-danger mb-2">{{ $outOfStockCount ?? 0 }}</div>
                <div class="badge bg-danger-subtle text-danger px-3 py-1 rounded-pill small fw-bold">Critical Level</div>
            </div>
        </div>
    </div>
            </div>

            <div class="row g-4">
                {{-- Left Column: Stats & Chart --}}
                <div class="col-lg-8">
                    {{-- Inventory Health --}}
                    <div class="hardened-glass p-4 mb-4">
                        <div class="d-flex align-items-center justify-content-between mb-4">
                            <h5 class="fw-800 mb-0 text-erp-deep">
                                <i class="bi bi-heart-pulse me-2"></i>
                                Inventory Health
                            </h5>
                            @php
                                $total = $totalItems ?? 1;
                                $healthy = $total - ($lowStockCount ?? 0) - ($outOfStockCount ?? 0);
                                $healthPercentage = round(($healthy / $total) * 100);
                            @endphp
                            <div class="premium-badge">
                                {{ $healthPercentage }}% System Stability
                            </div>
                        </div>

                        <div class="row g-3 mb-4">
                            <div class="col-md-4">
                                <div class="stat-card">
                                    <div class="d-flex align-items-center gap-3">
                                        <div style="width: 48px; height: 48px; border-radius: 12px; background: var(--gradient-success); display: flex; align-items: center; justify-content: center;">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="white" viewBox="0 0 16 16">
                                                <path d="M10.97 4.97a.75.75 0 0 1 1.07 1.05l-3.99 4.99a.75.75 0 0 1-1.08.02L4.324 8.384a.75.75 0 1 1 1.06-1.06l2.094 2.093 3.473-4.425a.267.267 0 0 1 .02-.022z"/>
                                            </svg>
                                        </div>
                                        <div>
                                            <h4 class="mb-0 fw-bold">{{ ($totalItems ?? 0) - ($outOfStockCount ?? 0) }}</h4>
                                            <p class="text-muted small mb-0">In Stock</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="stat-card">
                                    <div class="d-flex align-items-center gap-3">
                                        <div style="width: 48px; height: 48px; border-radius: 12px; background: var(--gradient-warning); display: flex; align-items: center; justify-content: center;">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="white" viewBox="0 0 16 16">
                                                <path d="M8.982 1.566a1.13 1.13 0 0 0-1.96 0L.165 13.233c-.457.778.091 1.767.98 1.767h13.713c.889 0 1.438-.99.98-1.767L8.982 1.566zM8 5c.535 0 .954.462.9.995l-.35 3.507a.552.552 0 0 1-1.1 0L7.1 5.995A.905.905 0 0 1 8 5zm.002 6a1 1 0 1 1 0 2 1 1 0 0 1 0-2z"/>
                                            </svg>
                                        </div>
                                        <div>
                                            <h4 class="mb-0 fw-bold">{{ $lowStockCount ?? 0 }}</h4>
                                            <p class="text-muted small mb-0">Low Stock</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="stat-card">
                                    <div class="d-flex align-items-center gap-3">
                                        <div style="width: 48px; height: 48px; border-radius: 12px; background: var(--gradient-danger); display: flex; align-items: center; justify-content: center;">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="white" viewBox="0 0 16 16">
                                                <path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14zm0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16z"/>
                                                <path d="M4.646 4.646a.5.5 0 0 1 .708 0L8 7.293l2.646-2.647a.5.5 0 0 1 .708.708L8.707 8l2.647 2.646a.5.5 0 0 1-.708.708L8 8.707l-2.646 2.647a.5.5 0 0 1-.708-.708L7.293 8 4.646 5.354a.5.5 0 0 1 0-.708z"/>
                                            </svg>
                                        </div>
                                        <div>
                                            <h4 class="mb-0 fw-bold">{{ $outOfStockCount ?? 0 }}</h4>
                                            <p class="text-muted small mb-0">Out of Stock</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="progress-modern mb-2">
                            <div class="progress-bar-modern" style="width: {{ $healthPercentage }}%; --gradient: var(--gradient-success);"></div>
                        </div>
                        <p class="text-muted small mb-0">Overall inventory health based on stock availability</p>
                    </div>

                    {{-- Stock Levels Chart --}}
                    <div class="glass-card p-4">
                        <h5 class="fw-bold mb-3" style="color: var(--erp-deep);">
                            <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" fill="currentColor" class="me-2" viewBox="0 0 16 16">
                                <path d="M0 0h1v15h15v1H0V0Zm14.817 3.113a.5.5 0 0 1 .07.704l-4.5 5.5a.5.5 0 0 1-.74.037L7.06 6.767l-3.656 5.027a.5.5 0 0 1-.808-.588l4-5.5a.5.5 0 0 1 .758-.06l2.609 2.61 4.15-5.073a.5.5 0 0 1 .704-.07Z"/>
                            </svg>
                            Stock Levels (Top 10 Items)
                        </h5>
                        <div id="stockLevelsChart" style="min-height: 350px;"></div>
                    </div>
                </div>

                {{-- Right Column: Actions & Alerts --}}
                <div class="col-lg-4">
                    {{-- Action Center --}}
                    <div class="hardened-glass p-4 mb-4">
                        <div class="d-flex align-items-center justify-content-between mb-4">
                            <h5 class="fw-800 text-erp-deep mb-0">Action Center</h5>
                            <i class="bi bi-lightning-charge text-warning"></i>
                        </div>
                        <p class="text-muted small mb-4">Streamline your material logistics workflow.</p>

                        <div class="d-grid gap-3">
                            <a href="{{ Auth::user()->hasRole('Administrator') ? route('admin.inventory.items.index') : route('inventory.items.create') }}" class="sidebar-link active bg-primary text-white border-0 py-3">
                                <i class="bi bi-plus-circle me-2"></i>
                                <span class="fw-semibold">Add New Item</span>
                            </a>

                            <a href="{{ Auth::user()->hasRole('Administrator') ? route('admin.inventory.items.index') : route('inventory.items.index') }}" class="sidebar-link py-3 bg-white border">
                                <i class="bi bi-search me-2 text-primary"></i>
                                <span class="fw-semibold text-dark">Browse Inventory</span>
                            </a>

                            <a href="{{ Auth::user()->hasRole('Administrator') ? route('admin.inventory.loans.index') : route('inventory.loans.create') }}" class="sidebar-link py-3 bg-white border">
                                <i class="bi bi-person-up me-2 text-success"></i>
                                <span class="fw-semibold text-dark">Lend Material</span>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        @php
            $topItems = \App\Models\InventoryItem::orderBy('quantity', 'desc')->take(10)->get();
            $itemNames = $topItems->pluck('name')->toArray();
            $itemQuantities = $topItems->pluck('quantity')->toArray();
        @endphp

        var options = {
            series: [{
                name: 'Quantity',
                data: {!! json_encode($itemQuantities) !!}
            }],
            chart: {
                type: 'bar',
                height: 350,
                toolbar: { show: false },
                fontFamily: 'Outfit, sans-serif'
            },
            plotOptions: {
                bar: {
                    borderRadius: 10,
                    columnWidth: '50%',
                    distributed: true,
                }
            },
            dataLabels: {
                enabled: false
            },
            colors: ['#667eea', '#764ba2', '#11998e', '#38ef7d', '#f093fb', '#f5576c', '#fa709a', '#fee140', '#4facfe', '#00f2fe'],
            xaxis: {
                categories: {!! json_encode($itemNames) !!},
                labels: {
                    style: {
                        colors: '#64748b',
                        fontSize: '12px'
                    }
                }
            },
            yaxis: {
                labels: {
                    style: {
                        colors: '#64748b'
                    }
                }
            },
            grid: {
                borderColor: '#f1f5f9',
            },
            tooltip: {
                theme: 'light'
            },
            legend: {
                show: false
            }
        };

        var chart = new ApexCharts(document.querySelector("#stockLevelsChart"), options);
        chart.render();
    });
</script>
@endpush
@endsection
