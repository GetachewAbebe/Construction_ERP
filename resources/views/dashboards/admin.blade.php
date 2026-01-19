@extends('layouts.app')

@section('title', 'Admin Command Center | Natanem Engineering')

@section('content')
<div class="py-4 px-2">
    {{-- Page Header --}}
    <div class="page-header-premium mb-5">
        <h1 class="display-3 fw-900 text-erp-deep mb-2 tracking-tight">System Administration</h1>
        <p class="text-muted fs-5 mb-0">Central command for user management, approvals, and system configuration.</p>
    </div>

    {{-- ALERT / ACTION CENTER (Only if pending items exist) --}}
    @if(($pendingLeaveCount ?? 0) > 0 || ($pendingExpenseCount ?? 0) > 0 || ($pendingLoanCount ?? 0) > 0)
    <div class="card bg-warning-subtle border-0 mb-5 shadow-sm overflow-hidden stagger-entrance">
        <div class="card-body p-4">
            <div class="d-flex align-items-center gap-3 mb-3">
                <div class="bg-warning text-white rounded-circle d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                    <i class="bi bi-bell-fill"></i>
                </div>
                <h5 class="fw-800 text-erp-deep mb-0">Pending Approvals Required</h5>
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
@endsection
