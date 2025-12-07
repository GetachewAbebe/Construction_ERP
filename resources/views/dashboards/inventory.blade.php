@extends('layouts.app')

@section('title', 'Inventory Dashboard')

@push('head')
<style>
    :root {
        --gradient-primary: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        --gradient-success: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
        --gradient-warning: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
        --gradient-danger: linear-gradient(135deg, #fa709a 0%, #fee140 100%);
        --gradient-info: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
    }
    
    .dashboard-container {
        background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
        min-height: calc(100vh - 100px);
        padding: 2rem 0;
    }
    
    .glass-card {
        background: rgba(255, 255, 255, 0.95);
        backdrop-filter: blur(10px);
        border-radius: 20px;
        border: 1px solid rgba(255, 255, 255, 0.8);
        box-shadow: 0 8px 32px rgba(0, 0, 0, 0.08);
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }
    
    .glass-card:hover {
        transform: translateY(-8px);
        box-shadow: 0 16px 48px rgba(0, 0, 0, 0.12);
    }
    
    .metric-card-modern {
        position: relative;
        overflow: hidden;
    }
    
    .metric-card-modern::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 4px;
        background: var(--gradient);
    }
    
    .metric-number {
        font-size: 3.5rem;
        font-weight: 800;
        line-height: 1;
        background: var(--gradient);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
    }
    
    .icon-box {
        width: 64px;
        height: 64px;
        border-radius: 16px;
        display: flex;
        align-items: center;
        justify-content: center;
        background: var(--gradient);
        box-shadow: 0 8px 24px rgba(0, 0, 0, 0.15);
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
        border-color: var(--erp-green);
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
        background: linear-gradient(135deg, var(--erp-deep) 0%, var(--erp-green) 100%);
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

            {{-- HEADER SECTION --}}
            <div class="row mb-5">
                <div class="col-lg-8">
                    <h1 class="page-title">Inventory Dashboard</h1>
                    <p class="text-muted fs-5 mb-0">Real-time insights and inventory management at your fingertips</p>
                </div>
                <div class="col-lg-4 text-lg-end mt-3 mt-lg-0">
                    <div class="badge-modern" style="background: rgba(115, 175, 111, 0.1); color: var(--erp-deep);">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16">
                            <path d="M8 3.5a.5.5 0 0 0-1 0V9a.5.5 0 0 0 .252.434l3.5 2a.5.5 0 0 0 .496-.868L8 8.71V3.5z"/>
                            <path d="M8 16A8 8 0 1 0 8 0a8 8 0 0 0 0 16zm7-8A7 7 0 1 1 1 8a7 7 0 0 1 14 0z"/>
                        </svg>
                        Updated {{ now()->format('H:i') }}
                    </div>
                </div>
            </div>

            {{-- METRIC CARDS --}}
            <div class="row g-4 mb-5">
                {{-- Total Items --}}
                <div class="col-lg-4">
                    <div class="glass-card metric-card-modern p-4" style="--gradient: var(--gradient-primary);">
                        <div class="d-flex justify-content-between align-items-start mb-4">
                            <div class="flex-grow-1">
                                <p class="text-muted text-uppercase small fw-bold mb-3 letter-spacing-1">Total Items</p>
                                <div class="metric-number mb-2">{{ $totalItems ?? 0 }}</div>
                                <div class="badge-modern" style="background: rgba(102, 126, 234, 0.1); color: #667eea;">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="currentColor" viewBox="0 0 16 16">
                                        <path d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0zm-3.97-3.03a.75.75 0 0 0-1.08.022L7.477 9.417 5.384 7.323a.75.75 0 0 0-1.06 1.06L6.97 11.03a.75.75 0 0 0 1.079-.02l3.992-4.99a.75.75 0 0 0-.01-1.05z"/>
                                    </svg>
                                    All Items
                                </div>
                            </div>
                            <div class="icon-box" style="--gradient: var(--gradient-primary);">
                                <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" fill="white" viewBox="0 0 16 16">
                                    <path d="M2.5 1a1 1 0 0 0-1 1v1a1 1 0 0 0 1 1H3v9a2 2 0 0 0 2 2h6a2 2 0 0 0 2-2V4h.5a1 1 0 0 0 1-1V2a1 1 0 0 0-1-1H10a1 1 0 0 0-1-1H7a1 1 0 0 0-1 1H2.5zm3 4a.5.5 0 0 1 .5.5v7a.5.5 0 0 1-1 0v-7a.5.5 0 0 1 .5-.5zM8 5a.5.5 0 0 1 .5.5v7a.5.5 0 0 1-1 0v-7A.5.5 0 0 1 8 5zm3 .5v7a.5.5 0 0 1-1 0v-7a.5.5 0 0 1 1 0z"/>
                                </svg>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Low Stock --}}
                <div class="col-lg-4">
                    <div class="glass-card metric-card-modern p-4" style="--gradient: var(--gradient-warning);">
                        <div class="d-flex justify-content-between align-items-start mb-4">
                            <div class="flex-grow-1">
                                <p class="text-muted text-uppercase small fw-bold mb-3 letter-spacing-1">Low Stock</p>
                                <div class="metric-number mb-2">{{ $lowStockCount ?? 0 }}</div>
                                <div class="badge-modern" style="background: rgba(245, 87, 108, 0.1); color: #f5576c;">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="currentColor" viewBox="0 0 16 16">
                                        <path d="M8.982 1.566a1.13 1.13 0 0 0-1.96 0L.165 13.233c-.457.778.091 1.767.98 1.767h13.713c.889 0 1.438-.99.98-1.767L8.982 1.566zM8 5c.535 0 .954.462.9.995l-.35 3.507a.552.552 0 0 1-1.1 0L7.1 5.995A.905.905 0 0 1 8 5zm.002 6a1 1 0 1 1 0 2 1 1 0 0 1 0-2z"/>
                                    </svg>
                                    Attention
                                </div>
                            </div>
                            <div class="icon-box" style="--gradient: var(--gradient-warning);">
                                <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" fill="white" viewBox="0 0 16 16">
                                    <path d="M8.982 1.566a1.13 1.13 0 0 0-1.96 0L.165 13.233c-.457.778.091 1.767.98 1.767h13.713c.889 0 1.438-.99.98-1.767L8.982 1.566zM8 5c.535 0 .954.462.9.995l-.35 3.507a.552.552 0 0 1-1.1 0L7.1 5.995A.905.905 0 0 1 8 5zm.002 6a1 1 0 1 1 0 2 1 1 0 0 1 0-2z"/>
                                </svg>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Out of Stock --}}
                <div class="col-lg-4">
                    <div class="glass-card metric-card-modern p-4" style="--gradient: var(--gradient-danger);">
                        <div class="d-flex justify-content-between align-items-start mb-4">
                            <div class="flex-grow-1">
                                <p class="text-muted text-uppercase small fw-bold mb-3 letter-spacing-1">Out of Stock</p>
                                <div class="metric-number mb-2">{{ $outOfStockCount ?? 0 }}</div>
                                <div class="badge-modern" style="background: rgba(250, 112, 154, 0.1); color: #fa709a;">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="currentColor" viewBox="0 0 16 16">
                                        <path d="M11.46.146A.5.5 0 0 0 11.107 0H4.893a.5.5 0 0 0-.353.146L.146 4.54A.5.5 0 0 0 0 4.893v6.214a.5.5 0 0 0 .146.353l4.394 4.394a.5.5 0 0 0 .353.146h6.214a.5.5 0 0 0 .353-.146l4.394-4.394a.5.5 0 0 0 .146-.353V4.893a.5.5 0 0 0-.146-.353L11.46.146zM8 4c.535 0 .954.462.9.995l-.35 3.507a.552.552 0 0 1-1.1 0L7.1 4.995A.905.905 0 0 1 8 4zm.002 6a1 1 0 1 1 0 2 1 1 0 0 1 0-2z"/>
                                    </svg>
                                    Critical
                                </div>
                            </div>
                            <div class="icon-box" style="--gradient: var(--gradient-danger);">
                                <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" fill="white" viewBox="0 0 16 16">
                                    <path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14zm0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16z"/>
                                    <path d="M4.646 4.646a.5.5 0 0 1 .708 0L8 7.293l2.646-2.647a.5.5 0 0 1 .708.708L8.707 8l2.647 2.646a.5.5 0 0 1-.708.708L8 8.707l-2.646 2.647a.5.5 0 0 1-.708-.708L7.293 8 4.646 5.354a.5.5 0 0 1 0-.708z"/>
                                </svg>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- MAIN CONTENT ROW --}}
            <div class="row g-4">
                {{-- Left Column: Stats & Chart --}}
                <div class="col-lg-8">
                    {{-- Inventory Health --}}
                    <div class="glass-card p-4 mb-4">
                        <div class="d-flex align-items-center justify-content-between mb-4">
                            <h5 class="fw-bold mb-0" style="color: var(--erp-deep);">
                                <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" fill="currentColor" class="me-2" viewBox="0 0 16 16">
                                    <path d="M4 11H2v3h2v-3zm5-4H7v7h2V7zm5-5v12h-2V2h2zm-2-1a1 1 0 0 0-1 1v12a1 1 0 0 0 1 1h2a1 1 0 0 0 1-1V2a1 1 0 0 0-1-1h-2zM6 7a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1v7a1 1 0 0 1-1 1H7a1 1 0 0 1-1-1V7zm-5 4a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1v3a1 1 0 0 1-1 1H2a1 1 0 0 1-1-1v-3z"/>
                                </svg>
                                Inventory Health
                            </h5>
                            @php
                                $total = $totalItems ?? 1;
                                $healthy = $total - ($lowStockCount ?? 0) - ($outOfStockCount ?? 0);
                                $healthPercentage = round(($healthy / $total) * 100);
                            @endphp
                            <span class="badge-modern" style="background: rgba(17, 153, 142, 0.1); color: #11998e;">
                                {{ $healthPercentage }}% Healthy
                            </span>
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

                    {{-- Stock Trend Chart Placeholder --}}
                    <div class="glass-card p-4">
                        <h5 class="fw-bold mb-3" style="color: var(--erp-deep);">
                            <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" fill="currentColor" class="me-2" viewBox="0 0 16 16">
                                <path d="M0 0h1v15h15v1H0V0Zm14.817 3.113a.5.5 0 0 1 .07.704l-4.5 5.5a.5.5 0 0 1-.74.037L7.06 6.767l-3.656 5.027a.5.5 0 0 1-.808-.588l4-5.5a.5.5 0 0 1 .758-.06l2.609 2.61 4.15-5.073a.5.5 0 0 1 .704-.07Z"/>
                            </svg>
                            Stock Trends
                        </h5>
                        <div class="chart-placeholder">
                            <div class="text-center">
                                <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" fill="var(--erp-green)" class="mb-2" viewBox="0 0 16 16">
                                    <path d="M0 0h1v15h15v1H0V0Zm14.817 3.113a.5.5 0 0 1 .07.704l-4.5 5.5a.5.5 0 0 1-.74.037L7.06 6.767l-3.656 5.027a.5.5 0 0 1-.808-.588l4-5.5a.5.5 0 0 1 .758-.06l2.609 2.61 4.15-5.073a.5.5 0 0 1 .704-.07Z"/>
                                </svg>
                                <p class="text-muted mb-0">Chart visualization coming soon</p>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Right Column: Quick Actions --}}
                <div class="col-lg-4">
                    <div class="glass-card p-4">
                        <h5 class="fw-bold mb-1" style="color: var(--erp-deep);">
                            <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" fill="currentColor" class="me-2" viewBox="0 0 16 16">
                                <path fill-rule="evenodd" d="M8 3a5 5 0 1 0 4.546 2.914.5.5 0 0 1 .908-.417A6 6 0 1 1 8 2v1z"/>
                                <path d="M8 4.466V.534a.25.25 0 0 1 .41-.192l2.36 1.966c.12.1.12.284 0 .384L8.41 4.658A.25.25 0 0 1 8 4.466z"/>
                            </svg>
                            Quick Actions
                        </h5>
                        <p class="text-muted small mb-4">Streamline your workflow</p>

                        <div class="d-grid gap-3">
                            <a href="{{ route('inventory.items.create') }}" class="action-button text-decoration-none" style="--gradient: var(--gradient-primary);">
                                <div class="d-flex align-items-center justify-content-between">
                                    <div class="d-flex align-items-center gap-3">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" fill="currentColor" viewBox="0 0 16 16">
                                            <path d="M8 4a.5.5 0 0 1 .5.5v3h3a.5.5 0 0 1 0 1h-3v3a.5.5 0 0 1-1 0v-3h-3a.5.5 0 0 1 0-1h3v-3A.5.5 0 0 1 8 4z"/>
                                        </svg>
                                        <span class="fw-semibold">Add New Item</span>
                                    </div>
                                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="currentColor" viewBox="0 0 16 16">
                                        <path fill-rule="evenodd" d="M4.646 1.646a.5.5 0 0 1 .708 0l6 6a.5.5 0 0 1 0 .708l-6 6a.5.5 0 0 1-.708-.708L10.293 8 4.646 2.354a.5.5 0 0 1 0-.708z"/>
                                    </svg>
                                </div>
                            </a>

                            <a href="{{ route('inventory.items.index') }}" class="action-button text-decoration-none" style="--gradient: var(--gradient-info);">
                                <div class="d-flex align-items-center justify-content-between">
                                    <div class="d-flex align-items-center gap-3">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" fill="currentColor" viewBox="0 0 16 16">
                                            <path d="M1 2.5A1.5 1.5 0 0 1 2.5 1h3A1.5 1.5 0 0 1 7 2.5v3A1.5 1.5 0 0 1 5.5 7h-3A1.5 1.5 0 0 1 1 5.5v-3zm8 0A1.5 1.5 0 0 1 10.5 1h3A1.5 1.5 0 0 1 15 2.5v3A1.5 1.5 0 0 1 13.5 7h-3A1.5 1.5 0 0 1 9 5.5v-3zm-8 8A1.5 1.5 0 0 1 2.5 9h3A1.5 1.5 0 0 1 7 10.5v3A1.5 1.5 0 0 1 5.5 15h-3A1.5 1.5 0 0 1 1 13.5v-3zm8 0A1.5 1.5 0 0 1 10.5 9h3a1.5 1.5 0 0 1 1.5 1.5v3a1.5 1.5 0 0 1-1.5 1.5h-3A1.5 1.5 0 0 1 9 13.5v-3z"/>
                                        </svg>
                                        <span class="fw-semibold">Browse Items</span>
                                    </div>
                                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="currentColor" viewBox="0 0 16 16">
                                        <path fill-rule="evenodd" d="M4.646 1.646a.5.5 0 0 1 .708 0l6 6a.5.5 0 0 1 0 .708l-6 6a.5.5 0 0 1-.708-.708L10.293 8 4.646 2.354a.5.5 0 0 1 0-.708z"/>
                                    </svg>
                                </div>
                            </a>

                            <a href="{{ route('inventory.loans.create') }}" class="action-button text-decoration-none" style="--gradient: var(--gradient-success);">
                                <div class="d-flex align-items-center justify-content-between">
                                    <div class="d-flex align-items-center gap-3">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" fill="currentColor" viewBox="0 0 16 16">
                                            <path d="M11 6a3 3 0 1 1-6 0 3 3 0 0 1 6 0z"/>
                                            <path fill-rule="evenodd" d="M0 8a8 8 0 1 1 16 0A8 8 0 0 1 0 8zm8-7a7 7 0 0 0-5.468 11.37C3.242 11.226 4.805 10 8 10s4.757 1.225 5.468 2.37A7 7 0 0 0 8 1z"/>
                                        </svg>
                                        <span class="fw-semibold">Lend to Employee</span>
                                    </div>
                                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="currentColor" viewBox="0 0 16 16">
                                        <path fill-rule="evenodd" d="M4.646 1.646a.5.5 0 0 1 .708 0l6 6a.5.5 0 0 1 0 .708l-6 6a.5.5 0 0 1-.708-.708L10.293 8 4.646 2.354a.5.5 0 0 1 0-.708z"/>
                                    </svg>
                                </div>
                            </a>

                            <a href="{{ route('inventory.loans.index') }}" class="action-button text-decoration-none" style="--gradient: var(--gradient-warning);">
                                <div class="d-flex align-items-center justify-content-between">
                                    <div class="d-flex align-items-center gap-3">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" fill="currentColor" viewBox="0 0 16 16">
                                            <path d="M8.5 5.5a.5.5 0 0 0-1 0v3.362l-1.429 2.38a.5.5 0 1 0 .858.515l1.5-2.5A.5.5 0 0 0 8.5 9V5.5z"/>
                                            <path d="M6.5 0a.5.5 0 0 0 0 1H7v1.07a7.001 7.001 0 0 0-3.273 12.474l-.602.602a.5.5 0 0 0 .707.708l.746-.746A6.97 6.97 0 0 0 8 16a6.97 6.97 0 0 0 3.422-.892l.746.746a.5.5 0 0 0 .707-.708l-.601-.602A7.001 7.001 0 0 0 9 2.07V1h.5a.5.5 0 0 0 0-1h-3zm1.038 3.018a6.093 6.093 0 0 1 .924 0 6 6 0 1 1-.924 0zM0 3.5c0 .753.333 1.429.86 1.887A8.035 8.035 0 0 1 4.387 1.86 2.5 2.5 0 0 0 0 3.5zM13.5 1c-.753 0-1.429.333-1.887.86a8.035 8.035 0 0 1 3.527 3.527A2.5 2.5 0 0 0 13.5 1z"/>
                                        </svg>
                                        <span class="fw-semibold">Loan History</span>
                                    </div>
                                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="currentColor" viewBox="0 0 16 16">
                                        <path fill-rule="evenodd" d="M4.646 1.646a.5.5 0 0 1 .708 0l6 6a.5.5 0 0 1 0 .708l-6 6a.5.5 0 0 1-.708-.708L10.293 8 4.646 2.354a.5.5 0 0 1 0-.708z"/>
                                    </svg>
                                </div>
                            </a>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
@endsection
