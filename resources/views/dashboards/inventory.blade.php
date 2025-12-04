@extends('layouts.app')

@section('title', 'Inventory Dashboard')

@section('content')
    <div class="container py-4">

        {{-- HERO / MODULE HEADER --}}
        <div class="row mb-3">
            <div class="col">
                <div class="card shadow-soft border-0 bg-erp-soft">
                    <div class="card-body d-flex flex-column flex-md-row align-items-md-center justify-content-between">
                        <div>
                            <div class="small text-uppercase text-muted mb-1">
                                Inventory Module
                            </div>
                            <h1 class="h4 mb-2 text-erp-deep">
                                Inventory Dashboard
                            </h1>
                            <p class="mb-0 text-muted">
                                High-level view of materials, equipment and stock levels for Natanem Engineering.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- INVENTORY HEADER MENU (OVERVIEW / ITEMS / LENDING) --}}
        <div class="row mb-4">
            <div class="col">
                <ul class="nav nav-pills small">
                    <li class="nav-item">
                        <a href="{{ route('inventory.dashboard') }}"
                           class="nav-link {{ request()->routeIs('inventory.dashboard') ? 'active' : '' }}">
                            Overview
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('inventory.items.index') }}"
                           class="nav-link {{ request()->routeIs('inventory.items.*') ? 'active' : '' }}">
                            Browse Items
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('inventory.items.create') }}"
                           class="nav-link">
                            Add Item
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('inventory.loans.create') }}"
                           class="nav-link {{ request()->routeIs('inventory.loans.create') ? 'active' : '' }}">
                            Lend Item
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('inventory.loans.index') }}"
                           class="nav-link {{ request()->routeIs('inventory.loans.index') ? 'active' : '' }}">
                            Loan History
                        </a>
                    </li>
                </ul>
            </div>
        </div>

        {{-- SUMMARY METRICS (DB data) --}}
        <div class="row g-3 mb-4">
            <div class="col-md-4">
                <div class="card shadow-soft border-0 h-100">
                    <div class="card-body">
                        <div class="small text-uppercase text-muted mb-1">Total Items</div>
                        <div class="d-flex align-items-baseline gap-2 mb-1">
                            <span class="fs-3 fw-semibold text-erp-deep">
                                {{ $totalItems ?? '—' }}
                            </span>
                            <span class="text-muted small">records</span>
                        </div>
                        <p class="text-muted small mb-0">
                            Count of all inventory items registered in the system.
                        </p>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card shadow-soft border-0 h-100">
                    <div class="card-body">
                        <div class="small text-uppercase text-muted mb-1">Low Stock Items</div>
                        <div class="d-flex align-items-baseline gap-2 mb-1">
                            <span class="fs-3 fw-semibold text-erp-deep">
                                {{ $lowStockCount ?? '—' }}
                            </span>
                            <span class="text-muted small">items</span>
                        </div>
                        <p class="text-muted small mb-0">
                            Items that are below the configured minimum quantity.
                        </p>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card shadow-soft border-0 h-100">
                    <div class="card-body">
                        <div class="small text-uppercase text-muted mb-1">Out of Stock</div>
                        <div class="d-flex align-items-baseline gap-2 mb-1">
                            <span class="fs-3 fw-semibold text-erp-deep">
                                {{ $outOfStockCount ?? '—' }}
                            </span>
                            <span class="text-muted small">items</span>
                        </div>
                        <p class="text-muted small mb-0">
                            Items currently at zero available quantity.
                        </p>
                    </div>
                </div>
            </div>
        </div>

        {{-- LOANS & QUICK ACTIONS --}}
        <div class="row g-3">
            <div class="col-lg-6">
                <div class="card shadow-soft border-0 h-100">
                    <div class="card-body">
                        <h5 class="card-title mb-2 text-erp-deep">Item Lending</h5>
                        <p class="text-muted small mb-3">
                            Register which employee is using which equipment, and keep track of returns.
                            Loan requests will go to the Administrator for approval.
                        </p>

                        <div class="d-flex flex-wrap gap-2">
                            <a href="{{ route('inventory.loans.create') }}"
                               class="btn btn-success btn-sm">
                                Lend Item to Employee
                            </a>

                            <a href="{{ route('inventory.loans.index') }}"
                               class="btn btn-outline-success btn-sm">
                                View Active & Past Loans
                            </a>
                        </div>

                        <hr class="my-3">

                        <ul class="small text-muted mb-0">
                            <li>Employees must be registered in the HR module before they can receive items.</li>
                            <li>Loan status updates (approved / rejected / returned) are visible here.</li>
                        </ul>
                    </div>
                </div>
            </div>

            {{-- Inventory actions --}}
            <div class="col-lg-6">
                <div class="card shadow-soft border-0 h-100">
                    <div class="card-body">
                        <h5 class="card-title mb-2 text-erp-deep">Inventory Actions</h5>
                        <p class="text-muted small mb-3">
                            Use these shortcuts to keep your inventory up to date.
                        </p>

                        <div class="d-flex flex-wrap gap-2 mb-3">
                            <a href="{{ route('inventory.items.create') }}" class="btn btn-outline-primary btn-sm">
                                Add New Item
                            </a>
                            <a href="{{ route('inventory.items.index') }}" class="btn btn-outline-secondary btn-sm">
                                Browse Items
                            </a>
                        </div>

                        <p class="small text-muted mb-0">
                            This section can later show recent items, charts or alerts
                            (for example: “3 loans overdue” or “5 items below minimum stock”).
                        </p>
                    </div>
                </div>
            </div>
        </div>

    </div>
@endsection
