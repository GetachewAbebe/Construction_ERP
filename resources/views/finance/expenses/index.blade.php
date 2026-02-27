@extends('layouts.app')
@section('title', 'Financial Requisitions')

@section('content')
<div class="page-header-premium mb-4">
    <div class="row align-items-center">
        <div class="col">
            <h1 class="display-3 fw-900 text-erp-deep mb-0 tracking-tight">Expenses</h1>
        </div>
        <div class="col-auto">
            <a href="{{ route('finance.expenses.create') }}" class="btn btn-erp-deep rounded-pill px-5 py-3 fw-900 shadow-xl border-0 transform-hover">
                <i class="bi bi-plus-lg me-2 fs-5"></i>NEW EXPENSE
            </a>
        </div>
    </div>
</div>

<div class="erp-card p-4 mb-4 stagger-entrance">
    <form action="{{ route('finance.expenses.index') }}" method="GET" class="row g-3">
        <div class="col-lg-3 col-md-6">
            <label class="form-label-premium">Search Registry</label>
            <div class="input-group bg-light rounded-pill overflow-hidden border-0">
                <span class="input-group-text bg-transparent border-0 text-muted ps-3"><i class="bi bi-search"></i></span>
                <input type="text" name="q" value="{{ request('q') }}" class="form-control border-0 bg-transparent py-2" placeholder="ID, Description or User...">
            </div>
        </div>
        <div class="col-lg-3 col-md-6">
            <label class="form-label-premium">Site Alignment</label>
            <select name="project_id" class="form-select border-0 bg-light rounded-pill py-2">
                <option value="">All Projects</option>
                @foreach($projects as $p)
                    <option value="{{ $p->id }}" @selected(request('project_id') == $p->id)>{{ $p->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-lg-2 col-md-6">
            <label class="form-label-premium">Auth Status</label>
            <select name="status" class="form-select border-0 bg-light rounded-pill py-2">
                <option value="">All Statuses</option>
                <option value="pending" @selected(request('status') == 'pending')>Pending</option>
                <option value="approved" @selected(request('status') == 'approved')>Approved</option>
                <option value="rejected" @selected(request('status') == 'rejected')>Rejected</option>
            </select>
        </div>
        <div class="col-lg-2 col-md-6">
            <label class="form-label-premium">Category</label>
            <select name="category" class="form-select border-0 bg-light rounded-pill py-2">
                <option value="">All Categories</option>
                <option value="materials" @selected(request('category') == 'materials')>Materials</option>
                <option value="labor" @selected(request('category') == 'labor')>Labor</option>
                <option value="transport" @selected(request('category') == 'transport')>Transport</option>
                <option value="equipment" @selected(request('category') == 'equipment')>Equipment</option>
                <option value="other" @selected(request('category') == 'other')>Other</option>
            </select>
        </div>
        <div class="col-lg-2 col-md-12 d-flex align-items-end gap-2">
            <button type="submit" class="btn btn-erp-deep rounded-pill fw-800 px-4 flex-grow-1 shadow-sm">Filter</button>
            @if(request()->anyFilled(['q', 'project_id', 'category', 'status']))
                <a href="{{ route('finance.expenses.index') }}" class="btn btn-white rounded-circle shadow-sm" title="Clear All">
                    <i class="bi bi-arrow-counterclockwise"></i>
                </a>
            @endif
        </div>
    </form>
</div>

<div class="table-responsive stagger-entrance">
    <table class="table-premium">
        <thead>
            <tr>
                <th class="ps-4">Transaction Identity</th>
                <th>Construction Site</th>
                <th>Category</th>
                <th class="text-center">Auth Status</th>
                <th>Transaction Value</th>
                <th class="text-end pe-4">Management</th>
            </tr>
        </thead>
        <tbody>
            @forelse($expenses as $expense)
                <tr>
                    <td class="ps-4">
                        <div class="fw-800 text-erp-deep fs-6">{{ $expense->expense_date->format('d M, Y') }}</div>
                        <small class="text-muted fw-bold">
                            <i class="bi bi-person-fill opacity-50 me-1"></i>{{ optional($expense->user)->name ?? 'Site Staff' }}
                        </small>
                    </td>
                    <td>
                        <div class="fw-700 text-dark">{{ optional($expense->project)->name ?? 'Global Site' }}</div>
                        <small class="text-muted text-truncate d-block" style="max-width: 220px;">
                            {{ $expense->description ?? 'No specific details recorded' }}
                        </small>
                    </td>
                    <td>
                        <span class="badge bg-light text-erp-deep border-0 rounded-pill px-3 py-1 fw-800 x-small">
                            {{ strtoupper($expense->category) }}
                        </span>
                    </td>
                    <td class="text-center">
                        @php
                            $statusClass = match($expense->status) {
                                'approved' => 'bg-success-soft text-success',
                                'rejected' => 'bg-danger-soft text-danger',
                                default => 'bg-warning-soft text-warning'
                            };
                            $statusIcon = match($expense->status) {
                                'approved' => 'bi-patch-check-fill',
                                'rejected' => 'bi-x-circle-fill',
                                default => 'bi-hourglass-split'
                            };
                        @endphp
                        <span class="badge {{ $statusClass }} rounded-pill px-3 py-2 border-0 fw-800" style="font-size: 0.65rem;">
                            <i class="bi {{ $statusIcon }} me-1"></i>
                            {{ strtoupper($expense->status ?? 'pending') }}
                        </span>
                    </td>
                    <td>
                        <div class="fw-900 text-erp-deep fs-5">
                            <small class="text-muted fw-normal x-small me-1">ETB</small>{{ number_format($expense->amount, 0) }}
                        </div>
                    </td>
                    <td class="text-end pe-4">
                        @if($expense->status === 'approved')
                            <a href="{{ route('finance.expenses.show', $expense) }}" class="btn btn-white rounded-pill px-4 py-2 fw-700 shadow-sm border-0 d-inline-flex align-items-center gap-2" style="font-size: 0.85rem;">
                                <i class="bi bi-printer-fill text-erp-deep"></i> <span>Voucher</span>
                            </a>
                        @else
                            <div class="d-flex justify-content-end gap-2">
                                <a href="{{ route('finance.expenses.show', $expense) }}" class="btn btn-white rounded-pill px-4 py-2 fw-700 shadow-sm border-0 d-inline-flex align-items-center gap-2" style="font-size: 0.85rem;">
                                    <i class="bi bi-eye-fill"></i> <span>View</span>
                                </a>
                                @if($expense->status === 'pending')
                                    <a href="{{ route('finance.expenses.edit', $expense) }}" class="btn btn-primary rounded-pill px-4 py-2 fw-700 shadow-sm border-0 d-inline-flex align-items-center gap-2" style="font-size: 0.85rem;">
                                        <i class="bi bi-pencil-square"></i> <span>Edit</span>
                                    </a>
                                @endif
                                
                                @if(Auth::user()->hasAnyRole(['Administrator', 'FinancialManager', 'Financial Manager', 'Admin']) && $expense->status === 'pending')
                                    <form action="{{ route('finance.expenses.approve', $expense) }}" method="POST" class="d-inline" id="app-exp-{{ $expense->id }}">
                                        @csrf
                                        <button type="submit" class="btn btn-success rounded-pill px-4 py-2 fw-700 shadow-sm border-0 d-inline-flex align-items-center gap-2" style="font-size: 0.85rem;">
                                            <i class="bi bi-check-lg"></i> <span>Approve</span>
                                        </button>
                                    </form>
                                @endif
                            </div>
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="text-center py-5">
                        <div class="text-muted fw-800 py-5">
                            <i class="bi bi-receipt display-1 mb-3 d-block opacity-10"></i>
                            No financial requisitions found in history.
                        </div>
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="mt-4">
    {{ $expenses->withQueryString()->links() }}
</div>
@endsection


