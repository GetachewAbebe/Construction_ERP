@extends('layouts.app')

@section('title', 'Admin Dashboard')

@section('content')
    <div class="container py-4">

        {{-- Page header --}}
        <div class="row mb-4">
            <div class="col">
                <h1 class="h3 mb-1">Admin Dashboard</h1>
                <p class="text-muted mb-0">
                    Central overview of users, HR, inventory and finance modules.
                </p>
            </div>
        </div>

        {{-- Top navigation pills inside the dashboard --}}
        <div class="row mb-4">
            <div class="col">
                <ul class="nav nav-pills small">
                    <li class="nav-item">
                        <a class="nav-link active" href="#">
                            Overview
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link"
                           href="{{ route('admin.users.index') }}">
                            Users
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link"
                           href="{{ route('hr.dashboard') }}">
                            HR Section
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link"
                           href="{{ route('inventory.dashboard') }}">
                            Inventory Section
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link"
                           href="{{ route('finance.dashboard') }}">
                            Finance Section
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link"
                           href="{{ route('admin.requests.items') }}">
                            Approvals
                            @if(isset($pendingLoanCount) && $pendingLoanCount > 0)
                                <span class="badge rounded-pill bg-light text-success ms-1">
                                    {{ $pendingLoanCount }}
                                </span>
                            @endif
                        </a>
                    </li>
                </ul>
            </div>
        </div>

        {{-- Overview cards --}}
        <div class="row g-3">

            {{-- Users --}}
            <div class="col-md-6 col-xl-4">
                <div class="card shadow-soft h-100">
                    <div class="card-body">
                        <h5 class="card-title mb-2">Users</h5>
                        <p class="card-text text-muted mb-3">
                            Manage system accounts and access levels.
                        </p>
                        <a href="{{ route('admin.users.index') }}" class="btn btn-sm btn-success">
                            Go to Users
                        </a>
                    </div>
                </div>
            </div>

            {{-- HR --}}
            <div class="col-md-6 col-xl-4">
                <div class="card shadow-soft h-100">
                    <div class="card-body">
                        <h5 class="card-title mb-2">HR</h5>
                        <p class="card-text text-muted mb-3">
                            Monitor employees, positions and leave approvals.
                        </p>
                        <a href="{{ route('hr.dashboard') }}" class="btn btn-sm btn-success">
                            Open HR Section
                        </a>
                    </div>
                </div>
            </div>

            {{-- Inventory --}}
            <div class="col-md-6 col-xl-4">
                <div class="card shadow-soft h-100">
                    <div class="card-body">
                        <h5 class="card-title mb-2">Inventory</h5>
                        <p class="card-text text-muted mb-3">
                            High-level view of site materials and equipment.
                        </p>
                        <a href="{{ route('inventory.dashboard') }}" class="btn btn-sm btn-success">
                            Open Inventory
                        </a>
                    </div>
                </div>
            </div>

            {{-- Finance --}}
            <div class="col-md-6 col-xl-4">
                <div class="card shadow-soft h-100">
                    <div class="card-body">
                        <h5 class="card-title mb-2">Finance</h5>
                        <p class="card-text text-muted mb-3">
                            Track billing, payments and project-level costs.
                        </p>
                        <a href="{{ route('finance.dashboard') }}" class="btn btn-sm btn-success">
                            Open Finance
                        </a>
                    </div>
                </div>
            </div>

            {{-- Approvals (with item lending info) --}}
            <div class="col-md-6 col-xl-4">
                <div class="card shadow-soft h-100">
                    <div class="card-body">
                        <h5 class="card-title mb-2">Approvals</h5>
                        <p class="card-text text-muted mb-2">
                            Review and approve requests across HR, inventory and finance.
                        </p>

                        @if(isset($pendingLoanCount))
                            <p class="small text-muted mb-3">
                                Pending item lending requests:
                                <span class="badge rounded-pill bg-warning text-dark">
                                    {{ $pendingLoanCount }}
                                </span>
                            </p>
                        @endif

                        <a href="{{ route('admin.requests.items') }}" class="btn btn-sm btn-success">
                            Go to Approvals
                        </a>
                    </div>
                </div>
            </div>

        </div>
    </div>
@endsection
