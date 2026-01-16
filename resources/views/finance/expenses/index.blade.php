@extends('layouts.app')
@section('title', 'Financial Requisitions - Natanem Engineering')

@section('content')
<div class="row align-items-center mb-4 stagger-entrance">
    <div class="col">
        <h1 class="h3 mb-1 fw-800 text-erp-deep">Financial Requisitions</h1>
        <p class="text-muted mb-0">Monitor and track project expenditures in real-time.</p>
    </div>
    <div class="col-auto">
        <a href="{{ route('finance.expenses.create') }}" class="btn btn-erp-deep rounded-pill px-4 shadow-sm border-0">
            <i class="bi bi-plus-lg me-2"></i>New Requisition
        </a>
    </div>
</div>

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show hardened-glass border-0 shadow-sm stagger-entrance" role="alert">
        <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

@if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show hardened-glass border-0 shadow-sm stagger-entrance" role="alert">
        <i class="bi bi-exclamation-triangle-fill me-2"></i>{{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

<div class="card hardened-glass border-0 mb-4 stagger-entrance shadow-sm">
    <div class="card-body p-4">
        <form action="{{ route('finance.expenses.index') }}" method="GET" class="row g-3">
            <div class="col-lg-3 col-md-6">
                <label class="form-label small fw-800 text-muted text-uppercase">Search</label>
                <div class="input-group bg-light-soft rounded-pill overflow-hidden shadow-sm border-0">
                    <span class="input-group-text bg-transparent border-0 text-muted ps-3"><i class="bi bi-search"></i></span>
                    <input type="text" name="q" value="{{ request('q') }}" class="form-control border-0 bg-transparent py-2" placeholder="Desc, ID, or User...">
                </div>
            </div>
            <div class="col-lg-3 col-md-6">
                <label class="form-label small fw-800 text-muted text-uppercase">Project Alignment</label>
                <select name="project_id" class="form-select border-0 bg-light-soft rounded-pill shadow-sm py-2">
                    <option value="">All Projects</option>
                    @foreach($projects as $p)
                        <option value="{{ $p->id }}" @selected(request('project_id') == $p->id)>{{ $p->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-lg-2 col-md-6">
                <label class="form-label small fw-800 text-muted text-uppercase">Status</label>
                <select name="status" class="form-select border-0 bg-light-soft rounded-pill shadow-sm py-2">
                    <option value="">All Statuses</option>
                    <option value="pending" @selected(request('status') == 'pending')>Pending</option>
                    <option value="approved" @selected(request('status') == 'approved')>Approved</option>
                    <option value="rejected" @selected(request('status') == 'rejected')>Rejected</option>
                </select>
            </div>
            <div class="col-lg-2 col-md-6">
                <label class="form-label small fw-800 text-muted text-uppercase">Category</label>
                <select name="category" class="form-select border-0 bg-light-soft rounded-pill shadow-sm py-2">
                    <option value="">All Categories</option>
                    <option value="materials" @selected(request('category') == 'materials')>Materials</option>
                    <option value="labor" @selected(request('category') == 'labor')>Labor</option>
                    <option value="transport" @selected(request('category') == 'transport')>Transport</option>
                    <option value="equipment" @selected(request('category') == 'equipment')>Equipment</option>
                    <option value="other" @selected(request('category') == 'other')>Other</option>
                </select>
            </div>
            <div class="col-lg-2 col-md-12 d-flex align-items-end gap-2">
                <button type="submit" class="btn btn-erp-deep rounded-pill px-4 flex-grow-1 border-0 shadow-sm fw-bold">Filter</button>
                @if(request()->anyFilled(['q', 'project_id', 'category', 'status']))
                    <a href="{{ route('finance.expenses.index') }}" class="btn btn-white rounded-circle shadow-sm border-0" title="Reset Filters">
                        <i class="bi bi-x-lg"></i>
                    </a>
                @endif
            </div>
        </form>
    </div>
</div>

<div class="card hardened-glass border-0 overflow-hidden stagger-entrance shadow-lg">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="bg-light-soft text-erp-deep">
                <tr>
                    <th class="ps-4 py-3">Transaction Info</th>
                    <th class="py-3">Project & Description</th>
                    <th class="py-3">Category</th>
                    <th class="py-3 text-center">Authorization</th>
                    <th class="py-3">Valuation</th>
                    <th class="text-end pe-4 py-3">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($expenses as $expense)
                    <tr>
                        <td class="ps-4">
                            <div class="fw-800 text-erp-deep fs-6">{{ $expense->expense_date->format('d M, Y') }}</div>
                            <small class="text-muted d-flex align-items-center gap-1">
                                <i class="bi bi-person-circle opacity-50"></i>
                                {{ optional($expense->user)->name ?? 'System' }}
                            </small>
                        </td>
                        <td>
                            <div class="fw-600 text-dark">{{ optional($expense->project)->name ?? 'Unknown Project' }}</div>
                            <small class="text-muted text-truncate d-block" style="max-width: 200px;">
                                {{ Str::limit($expense->description ?? 'No details provided', 50) }}
                            </small>
                        </td>
                        <td>
                            <span class="badge bg-white text-erp-deep border shadow-sm rounded-pill px-3 py-1 fw-600 x-small font-monospace">
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
                                    'approved' => 'bi-check-circle-fill',
                                    'rejected' => 'bi-x-circle-fill',
                                    default => 'bi-hourglass-split'
                                };
                            @endphp
                            <span class="badge {{ $statusClass }} rounded-pill px-3 py-2 border-0 fw-700">
                                <i class="bi {{ $statusIcon }} me-1"></i>
                                {{ ucfirst($expense->status ?? 'pending') }}
                            </span>
                        </td>
                        <td>
                            <div class="fw-800 fs-6 text-erp-deep">
                                <small class="text-muted fw-normal me-1">ETB</small>{{ number_format($expense->amount, 2) }}
                            </div>
                        </td>
                        <td class="text-end pe-4">
                            <div class="btn-group hardened-glass rounded-pill p-1 shadow-sm">
                                <a href="{{ route('finance.expenses.show', $expense) }}" class="btn btn-sm btn-white rounded-pill px-3 border-0" title="View Ledger">
                                    <i class="bi bi-eye-fill"></i>
                                </a>
                                @if($expense->status === 'pending')
                                    <a href="{{ route('finance.expenses.edit', $expense) }}" class="btn btn-sm btn-white rounded-pill px-3 border-0 text-primary" title="Modify">
                                        <i class="bi bi-pencil-fill"></i>
                                    </a>
                                @endif
                                
                                @if(Auth::user()->hasAnyRole(['Administrator', 'FinancialManager']) && $expense->status === 'pending')
                                    <button type="button" class="btn btn-sm btn-white rounded-pill px-3 border-0 text-success" 
                                            onclick="if(confirm('Authorize this expense?')) document.getElementById('approve-{{ $expense->id }}').submit()" title="Approve">
                                        <i class="bi bi-check-lg"></i>
                                        <form id="approve-{{ $expense->id }}" action="{{ route('finance.expenses.approve', $expense) }}" method="POST" class="d-none">@csrf</form>
                                    </button>
                                    <button type="button" class="btn btn-sm btn-white rounded-pill px-3 border-0 text-danger" 
                                            onclick="if(confirm('Reject this transaction?')) document.getElementById('reject-{{ $expense->id }}').submit()" title="Reject">
                                        <i class="bi bi-x-lg"></i>
                                        <form id="reject-{{ $expense->id }}" action="{{ route('finance.expenses.reject', $expense) }}" method="POST" class="d-none">@csrf</form>
                                    </button>
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center py-5">
                            <i class="bi bi-receipt fs-1 text-muted opacity-25"></i>
                            <p class="text-muted italic mt-3 mb-0">No financial requisitions match your criteria.</p>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    
    <div class="px-4 py-3 border-top border-light">
        {{ $expenses->withQueryString()->links() }}
    </div>
</div>
@endsection

