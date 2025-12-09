@extends('layouts.app')

@section('title', 'HR Dashboard')

@section('content')
<style>
    :root {
        --color-brand: #4f46e5;
        --color-success: #10b981;
        --color-info: #0ea5e9;
        --color-danger: #f43f5e;
    }
    
    .card-stat {
        border: none;
        border-radius: 12px;
        background: #fff;
        box-shadow: 0 2px 10px rgba(0,0,0,0.03);
        transition: transform 0.2s, box-shadow 0.2s;
        border-top: 4px solid transparent;
        overflow: hidden;
    }
    .card-stat:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 20px rgba(0,0,0,0.06);
    }
    
    /* Variant Colors */
    .card-stat.stat-primary { border-top-color: var(--color-brand); }
    .card-stat.stat-success { border-top-color: var(--color-success); }
    .card-stat.stat-info { border-top-color: var(--color-info); }
    .card-stat.stat-danger { border-top-color: var(--color-danger); }
    
    .card-stat .icon-wrapper {
        width: 56px;
        height: 56px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.5rem;
        margin-left: auto; /* Push to right */
    }
    
    .card-stat.stat-primary .icon-wrapper { background: #e0e7ff; color: var(--color-brand); }
    .card-stat.stat-success .icon-wrapper { background: #d1fae5; color: var(--color-success); }
    .card-stat.stat-info .icon-wrapper { background: #e0f2fe; color: var(--color-info); }
    .card-stat.stat-danger .icon-wrapper { background: #ffe4e6; color: var(--color-danger); }

    .table-custom thead th {
        background-color: #f8fafc;
        color: #64748b;
        font-weight: 600;
        text-transform: uppercase;
        font-size: 0.75rem;
        letter-spacing: 0.05em;
        border-bottom: 1px solid #e2e8f0;
        padding-top: 1rem;
        padding-bottom: 1rem;
    }
    .avatar-circle {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        object-fit: cover;
        box-shadow: 0 2px 4px rgba(0,0,0,0.05);
    }
    .avatar-initial {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        background: #f1f5f9;
        color: #64748b;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 600;
        font-size: 0.9rem;
    }
</style>

<div class="container py-4">

    <!-- Premium Header -->
    <div class="row mb-5">
        <div class="col-12 d-flex justify-content-between align-items-center">
            <div>
                <h1 class="h3 fw-bold text-dark mb-1">Human Resources Overview</h1>
                <p class="text-secondary small mb-0">Welcome back, {{ Auth::user()->name }}. Here is your workforce at a glance.</p>
            </div>
            <div class="d-none d-md-block">
                <span class="badge bg-white text-secondary border px-3 py-2 rounded-pill fw-normal shadow-sm">
                    <i class="far fa-calendar-alt me-2"></i> {{ now()->format('l, F j, Y') }}
                </span>
            </div>
        </div>
    </div>

    <!-- Metric Cards: Top Border Style -->
    <div class="row g-4 mb-5">
        <!-- Card 1 -->
        <div class="col-12 col-md-6 col-lg-3">
            <div class="card card-stat stat-primary h-100">
                <div class="card-body p-4 d-flex justify-content-between align-items-start">
                    <div>
                        <div class="text-secondary small text-uppercase fw-bold mb-2 tracking-wide">Total Employees</div>
                        <h2 class="display-5 fw-bold text-dark mb-0">{{ $employeeCount }}</h2>
                        <div class="mt-2 text-primary small d-flex align-items-center gap-1">
                            <i class="fas fa-check-circle"></i> Registered
                        </div>
                    </div>
                    <div class="icon-wrapper">
                        <i class="fas fa-users"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Card 2 -->
        <div class="col-12 col-md-6 col-lg-3">
            <div class="card card-stat stat-success h-100">
                <div class="card-body p-4 d-flex justify-content-between align-items-start">
                    <div>
                        <div class="text-secondary small text-uppercase fw-bold mb-2 tracking-wide">Active Staff</div>
                        <h2 class="display-5 fw-bold text-dark mb-0">{{ $activeEmployees }}</h2>
                        <div class="mt-2 text-success small d-flex align-items-center gap-1">
                            <i class="fas fa-user-check"></i> On Duty
                        </div>
                    </div>
                    <div class="icon-wrapper">
                        <i class="fas fa-id-badge"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Card 3 -->
        <div class="col-12 col-md-6 col-lg-3">
            <div class="card card-stat stat-info h-100">
                <div class="card-body p-4 d-flex justify-content-between align-items-start">
                    <div>
                        <div class="text-secondary small text-uppercase fw-bold mb-2 tracking-wide">New Hires</div>
                        <h2 class="display-5 fw-bold text-dark mb-0">{{ $recentHires }}</h2>
                        <div class="mt-2 text-info small d-flex align-items-center gap-1">
                            <i class="fas fa-arrow-up"></i> Last 30 Days
                        </div>
                    </div>
                    <div class="icon-wrapper">
                        <i class="fas fa-user-plus"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Card 4 -->
        <div class="col-12 col-md-6 col-lg-3">
            <div class="card card-stat stat-danger h-100">
                <div class="card-body p-4 d-flex justify-content-between align-items-start">
                    <div>
                        <div class="text-secondary small text-uppercase fw-bold mb-2 tracking-wide">Pending Requests</div>
                        <h2 class="display-5 fw-bold text-dark mb-0">{{ $pendingLeaveApprovals }}</h2>
                        <div class="mt-2 text-danger small d-flex align-items-center gap-1">
                            <i class="fas fa-exclamation-circle"></i> Needs Action
                        </div>
                    </div>
                    <div class="icon-wrapper">
                        <i class="fas fa-bell"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Content Row -->
    <div class="row g-4">
        <!-- Main Table -->
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm rounded-4 overflow-hidden h-100">
                <div class="card-header bg-white border-0 py-4 px-4 d-flex justify-content-between align-items-center">
                    <div>
                        <h5 class="fw-bold text-dark mb-0">Recent Activity</h5>
                        <p class="text-muted small mb-0">Latest employees added to the system</p>
                    </div>
                    <a href="{{ route('hr.employees.index') }}" class="btn btn-light btn-sm fw-medium text-secondary">View All</a>
                </div>
                <div class="table-responsive">
                    <table class="table table-custom align-middle mb-0">
                        <thead>
                            <tr>
                                <th class="ps-4">Employee</th>
                                <th>Role</th>
                                <th>Status</th>
                                <th class="text-end pe-4">Joined</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($latestEmployees as $emp)
                            <tr class="hover-bg-light transition-all">
                                <td class="ps-4 py-3">
                                    <div class="d-flex align-items-center">
                                        @if($emp->profile_picture)
                                            <img src="{{ asset('storage/' . $emp->profile_picture) }}" class="avatar-circle me-3">
                                        @else
                                            <div class="avatar-initial me-3">{{ substr($emp->first_name, 0, 1) }}</div>
                                        @endif
                                        <div>
                                            <div class="fw-bold text-dark">{{ $emp->first_name }} {{ $emp->last_name }}</div>
                                            <div class="small text-muted">{{ $emp->email }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div class="text-dark fw-medium small">{{ $emp->position ?? 'N/A' }}</div>
                                    <div class="text-secondary small">{{ $emp->department ?? 'General' }}</div>
                                </td>
                                <td>
                                    @php
                                        $statusConfig = match($emp->status) {
                                            'Active' => ['bg' => '#d1fae5', 'text' => '#10b981'],
                                            'On Leave' => ['bg' => '#fef3c7', 'text' => '#f59e0b'],
                                            'Terminated' => ['bg' => '#ffe4e6', 'text' => '#f43f5e'],
                                            default => ['bg' => '#f1f5f9', 'text' => '#64748b']
                                        };
                                    @endphp
                                    <span class="badge rounded-pill fw-bold border-0 px-3 py-2" 
                                          style="background-color: {{ $statusConfig['bg'] }}; color: {{ $statusConfig['text'] }};">
                                        {{ $emp->status ?? 'Active' }}
                                    </span>
                                </td>
                                <td class="text-end pe-4 text-secondary small">
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

        <!-- Sidebar / Dept Stats -->
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm rounded-4 overflow-hidden h-100">
                <div class="card-header bg-white border-0 py-4 px-4">
                    <h5 class="fw-bold text-dark mb-0">Department Stats</h5>
                    <p class="text-muted small mb-0">Workforce distribution</p>
                </div>
                <div class="card-body px-4 pt-0">
                    @forelse($departmentStats as $dept)
                    <div class="mb-4">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <span class="fw-bold text-dark">{{ $dept->name }}</span>
                            <span class="text-secondary small fw-bold">{{ $dept->total }} Members</span>
                        </div>
                        <div class="progress" style="height: 6px; background: #f1f5f9; border-radius: 3px;">
                            <div class="progress-bar" role="progressbar" 
                                 style="width: {{ $employeeCount > 0 ? ($dept->total / $employeeCount) * 100 : 0 }}%; background-color: var(--color-brand); border-radius: 3px;">
                            </div>
                        </div>
                    </div>
                    @empty
                    <div class="text-center py-4 text-muted small">No data.</div>
                    @endforelse
                </div>
                <div class="card-footer bg-white border-0 p-4 pt-0">
                    <a href="{{ route('hr.employees.index') }}" class="btn btn-outline-dark w-100 py-2 rounded-3 fw-medium">
                        Manage Departments
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
