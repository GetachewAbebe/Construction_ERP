@extends('layouts.app')

@section('title', 'Finance Dashboard - Natanem Engineering')

@section('content')
<div class="container py-3 py-md-4">
    {{-- Page header --}}
    <div class="row mb-3">
        <div class="col">
            <h1 class="h4 fw-bold mb-1">Finance Dashboard</h1>
            <p class="text-muted mb-0">
                Monitor invoices, payments and financial reports for Natanem Engineering.
            </p>
        </div>
    </div>

    {{-- Finance menu (role-specific) --}}
    <ul class="nav nav-pills mb-4">
        <li class="nav-item">
            <a href="{{ route('finance.dashboard') }}"
               class="nav-link {{ request()->routeIs('finance.dashboard') ? 'active' : '' }}">
                Overview
            </a>
        </li>
        {{-- Add real routes as you build the module --}}
        <li class="nav-item">
            <a href="#" class="nav-link disabled" aria-disabled="true">Invoices</a>
        </li>
        <li class="nav-item">
            <a href="#" class="nav-link disabled" aria-disabled="true">Payments</a>
        </li>
        <li class="nav-item">
            <a href="#" class="nav-link disabled" aria-disabled="true">Reports</a>
        </li>
    </ul>

    {{-- Project finance menus --}}
    <div class="d-flex align-items-center mb-2">
        <span class="text-uppercase small text-muted fw-semibold">Project Finance</span>
    </div>
    <ul class="nav nav-pills flex-wrap mb-4">
        {{-- Add real project routes as you build the module --}}
        <li class="nav-item">
            <a href="#" class="nav-link disabled" aria-disabled="true">Project 01</a>
        </li>
        <li class="nav-item">
            <a href="#" class="nav-link disabled" aria-disabled="true">Project 02</a>
        </li>
        <li class="nav-item">
            <a href="#" class="nav-link disabled" aria-disabled="true">Project 03</a>
        </li>
        <li class="nav-item">
            <a href="#" class="nav-link disabled" aria-disabled="true">Project 04</a>
        </li>
    </ul>

    {{-- Placeholder cards --}}
    <div class="row g-3 mb-4">
        <div class="col-md-4">
            <div class="card border-0 shadow-sm rounded-4 h-100">
                <div class="card-body">
                    <h6 class="text-muted text-uppercase small mb-1">Billing</h6>
                    <p class="small text-muted mb-0">
                        Centralize project invoices and client billing details.
                    </p>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm rounded-4 h-100">
                <div class="card-body">
                    <h6 class="text-muted text-uppercase small mb-1">Payments</h6>
                    <p class="small text-muted mb-0">
                        Track incoming and outgoing payments across sites.
                    </p>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm rounded-4 h-100">
                <div class="card-body">
                    <h6 class="text-muted text-uppercase small mb-1">Reporting</h6>
                    <p class="small text-muted mb-0">
                        Build financial summaries for management and project owners.
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
