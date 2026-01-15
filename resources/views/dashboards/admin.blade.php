@extends('layouts.app')

@section('title', 'Admin Overview | Natanem Engineering')



@section('content')
<div class="py-4 px-2">
    {{-- Page Header --}}
    <div class="d-flex flex-column mb-5">
        <h1 class="display-4 fw-800 text-erp-deep mb-2 tracking-tight">Admin Dashboard</h1>
    </div>

    {{-- HIGHLIGHT METRICS --}}
    <div class="row g-4">
        {{-- Users --}}
        <div class="col-md-6 col-lg-4">
            <div class="hardened-glass stagger-entrance">
                <div class="metric-icon">
                    <i class="bi bi-person-gear fs-4"></i>
                </div>
                <h5 class="fw-800 mb-2">User Management</h5>
                <p class="text-muted small mb-4 flex-grow-1">
                    Manage system accounts, roles, and administrative access levels safely.
                </p>
                <a href="{{ route('admin.users.index') }}" class="btn btn-erp-deep rounded-pill px-4 fw-bold">
                    System Users
                </a>
            </div>
        </div>

        {{-- HR --}}
        <div class="col-md-6 col-lg-4">
            <div class="hardened-glass stagger-entrance">
                <div class="metric-icon" style="background: var(--gradient-primary);">
                    <i class="bi bi-person-vcard fs-4"></i>
                </div>
                <h5 class="fw-800 mb-2">Human Resources</h5>
                <p class="text-muted small mb-4 flex-grow-1">
                    Review Human Resource statistics, departments, and active employee status.
                </p>
                <a href="{{ route('admin.hr') }}" class="btn btn-outline-erp-deep rounded-pill px-4 fw-bold">
                    Human Resource Hub
                </a>
            </div>
        </div>

        {{-- Inventory --}}
        <div class="col-md-6 col-lg-4">
            <div class="hardened-glass stagger-entrance">
                <div class="metric-icon" style="background: var(--gradient-primary);">
                    <i class="bi bi-boxes fs-4"></i>
                </div>
                <h5 class="fw-800 mb-2">Inventory Control</h5>
                <p class="text-muted small mb-4 flex-grow-1">
                    Manage construction materials, track stock movements, and audit logs.
                </p>
                <a href="{{ route('admin.inventory') }}" class="btn btn-outline-erp-deep rounded-pill px-4 fw-bold">
                    Stock Manager
                </a>
            </div>
        </div>

        {{-- Finance --}}
        <div class="col-md-6 col-lg-4">
            <div class="hardened-glass stagger-entrance">
                <div class="metric-icon" style="background: var(--gradient-primary);">
                    <i class="bi bi-wallet2 fs-4"></i>
                </div>
                <h5 class="fw-800 mb-2">Financial Operations</h5>
                <p class="text-muted small mb-4 flex-grow-1">
                    Track project budgets, expenses, billing, and financial distributions.
                </p>
                <a href="{{ route('admin.finance') }}" class="btn btn-outline-erp-deep rounded-pill px-4 fw-bold">
                    Finance Hub
                </a>
            </div>
        </div>

        {{-- System Settings --}}
        <div class="col-md-6 col-lg-4">
            <div class="hardened-glass stagger-entrance">
                <div class="metric-icon" style="background: linear-gradient(135deg, #64748b 0%, #334155 100%);">
                    <i class="bi bi-gear-wide-connected fs-4"></i>
                </div>
                <h5 class="fw-800 mb-2">Global Settings</h5>
                <p class="text-muted small mb-4 flex-grow-1">
                    Configure organizational rules, attendance shifts, and system-wide defaults.
                </p>
                <a href="{{ route('admin.attendance-settings.index') }}" class="btn btn-outline-erp-deep rounded-pill px-4 fw-bold">
                    System Config
                </a>
            </div>
        </div>


    </div>
</div>
@endsection
