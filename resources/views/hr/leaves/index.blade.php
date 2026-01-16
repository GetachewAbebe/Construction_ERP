@extends('layouts.app')
@section('title', 'Leave Management Portfolio')

@section('content')
<div class="row align-items-center mb-4 stagger-entrance">
    <div class="col">
        <h1 class="h3 mb-1 fw-800 text-erp-deep">Leave Portfolio</h1>
        <p class="text-muted mb-0">Lifecycle management of personnel absence and respite requests.</p>
    </div>
    <div class="col-auto">
        <a href="{{ route('hr.leaves.create') }}" class="btn btn-primary rounded-pill px-4 shadow-sm hardened-glass border-0">
            <i class="bi bi-calendar-plus me-2"></i>Draft New Request
        </a>
    </div>
</div>

{{-- Flash Messages --}}
@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show hardened-glass border-0 shadow-sm stagger-entrance mb-4" role="alert">
        <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif
@if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show hardened-glass border-0 shadow-sm stagger-entrance mb-4" role="alert">
        <i class="bi bi-exclamation-triangle-fill me-2"></i>{{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

{{-- Search & Filter --}}
<form action="{{ route('hr.leaves.index') }}" method="GET" class="mb-4 stagger-entrance" style="animation-delay: 0.1s;">
    <div class="row g-2">
        <div class="col-md-9">
            <div class="position-relative">
                <i class="bi bi-search position-absolute top-50 start-0 translate-middle-y ms-3 text-muted"></i>
                <input type="text" name="q" class="form-control border-0 bg-white py-3 ps-5 rounded-pill shadow-sm" 
                       placeholder="Search by employee name..." 
                       value="{{ request('q') }}">
            </div>
        </div>
        <div class="col-md-3">
            <select name="status" class="form-select border-0 bg-white py-3 rounded-pill shadow-sm" onchange="this.form.submit()">
                <option value="">All Statuses</option>
                <option value="Pending" {{ request('status') === 'Pending' ? 'selected' : '' }}>Pending Review</option>
                <option value="Approved" {{ request('status') === 'Approved' ? 'selected' : '' }}>Authorized</option>
                <option value="Rejected" {{ request('status') === 'Rejected' ? 'selected' : '' }}>Declined</option>
            </select>
        </div>
    </div>
</form>

{{-- Consolidated Navigation Tabs --}}
<div class="mb-4 stagger-entrance">
    <ul class="nav nav-pills hardened-glass p-1 rounded-pill d-inline-flex shadow-sm border">
        <li class="nav-item">
            <a class="nav-link rounded-pill px-4 fw-800 {{ !request('view') || request('view') === 'active' ? 'active btn-erp-deep text-white shadow' : 'text-muted' }}" 
               href="{{ route('hr.leaves.index', ['view' => 'active']) }}">
                <i class="bi bi-stack me-2"></i>Active Portfolio
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link rounded-pill px-4 fw-800 {{ request('view') === 'logs' ? 'active btn-erp-deep text-white shadow' : 'text-muted' }}" 
               href="{{ route('hr.leaves.index', ['view' => 'logs']) }}">
                <i class="bi bi-archive-fill me-2"></i>Execution Logs
            </a>
        </li>
    </ul>
</div>

@if(!request('view') || request('view') === 'active')
    {{-- Strategic Absence Metrics --}}
    <div class="row g-4 mb-4 stagger-entrance">
        <div class="col-md-4">
            <div class="card hardened-glass border-0 p-4 shadow-sm">
                <div class="small text-muted fw-bold text-uppercase mb-2">Awaiting Adjudication</div>
                <div class="d-flex align-items-end gap-2">
                    <div class="fw-800 fs-2 text-warning">{{ $pendingCount }}</div>
                    <div class="text-muted small fw-600 mb-2">Active Filings</div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card hardened-glass border-0 p-4 shadow-sm">
                <div class="small text-muted fw-bold text-uppercase mb-2">Authorized Respite</div>
                <div class="d-flex align-items-end gap-2">
                    <div class="fw-800 fs-2 text-success">{{ $approvedCount }}</div>
                    <div class="text-muted small fw-600 mb-2">Total Executed</div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card hardened-glass border-0 p-4 shadow-sm">
                <div class="small text-muted fw-bold text-uppercase mb-2">Declined Filings</div>
                <div class="d-flex align-items-end gap-2">
                    <div class="fw-800 fs-2 text-danger">{{ $rejectedCount }}</div>
                    <div class="text-muted small fw-600 mb-2">Record Set</div>
                </div>
            </div>
        </div>
    </div>
@endif

<div class="card hardened-glass border-0 overflow-hidden stagger-entrance">
    <div class="table-responsive">
        @if($view === 'active')
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light-soft text-erp-deep">
                    <tr>
                        <th class="ps-4">Personnel</th>
                        <th>Absence Period</th>
                        <th>Classification</th>
                        <th>Status Context</th>
                        <th class="text-end pe-4">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($requests as $leave)
                        <tr>
                            <td class="ps-4">
                                <div class="fw-800 text-erp-deep">{{ optional($leave->employee)->name ?? 'Legacy Identity' }}</div>
                                <small class="text-muted fw-bold">{{ optional($leave->employee)->position ?? 'General Operations' }}</small>
                            </td>
                            <td>
                                <div class="d-flex flex-column">
                                    <span class="fw-700 text-dark">
                                        {{ \Carbon\Carbon::parse($leave->start_date)->format('M d') }} — 
                                        {{ \Carbon\Carbon::parse($leave->end_date)->format('M d, Y') }}
                                    </span>
                                    <small class="text-muted">
                                        <i class="bi bi-clock-history me-1"></i>
                                        {{ \Carbon\Carbon::parse($leave->start_date)->diffInDays(\Carbon\Carbon::parse($leave->end_date)) + 1 }} Days
                                    </small>
                                </div>
                            </td>
                            <td>
                                <span class="badge bg-light-soft text-erp-deep border rounded-pill px-3 fw-600">
                                    <i class="bi bi-briefcase-fill me-1"></i>Annual Leave
                                </span>
                            </td>
                            <td>
                                @php
                                    $statusMeta = match($leave->status) {
                                        'Pending' => ['class' => 'bg-warning-soft text-warning', 'icon' => 'bi-hourglass-split'],
                                        'Approved' => ['class' => 'bg-success-soft text-success', 'icon' => 'bi-check-circle-fill'],
                                        'Rejected' => ['class' => 'bg-danger-soft text-danger', 'icon' => 'bi-x-circle-fill'],
                                        default => ['class' => 'bg-light text-muted', 'icon' => 'bi-question-circle']
                                    };
                                @endphp
                                <span class="badge {{ $statusMeta['class'] }} rounded-pill px-3 py-2 border-0 fw-600">
                                    <i class="bi {{ $statusMeta['icon'] }} me-1"></i>{{ $leave->status }}
                                </span>
                            </td>
                            <td class="text-end pe-4">
                                @if($leave->status === 'Pending')
                                    <div class="d-flex justify-content-end gap-1">
                                        @if(auth()->user()->hasRole('Administrator'))
                                            {{-- Direct Actions for Clarity --}}
                                            <form method="POST" action="{{ route('admin.requests.leave.approve', $leave) }}" class="d-inline">
                                                @csrf
                                                <button type="button" class="btn btn-sm btn-success-soft rounded-circle shadow-sm border-0 d-flex align-items-center justify-content-center" 
                                                        style="width: 32px; height: 32px;"
                                                        onclick="if(confirm('Approve this request?')) this.form.submit();" 
                                                        title="Quick Approve">
                                                    <i class="bi bi-check-lg"></i>
                                                </button>
                                            </form>
                                            <form method="POST" action="{{ route('admin.requests.leave.reject', $leave) }}" class="d-inline">
                                                @csrf
                                                <button type="button" class="btn btn-sm btn-danger-soft rounded-circle shadow-sm border-0 d-flex align-items-center justify-content-center" 
                                                        style="width: 32px; height: 32px;"
                                                        onclick="if(confirm('Reject this request?')) this.form.submit();"
                                                        title="Quick Reject">
                                                    <i class="bi bi-x-lg"></i>
                                                </button>
                                            </form>
                                        @endif
                                        <a href="{{ route('hr.leaves.show', $leave) }}" class="btn btn-sm btn-light rounded-pill px-3 fw-bold ms-2" title="View Specification">
                                            Details
                                        </a>
                                    </div>
                                @else
                                    <div class="d-flex justify-content-end gap-1 px-3">
                                        <a href="{{ route('hr.leaves.show', $leave) }}" class="btn btn-sm btn-outline-erp-deep rounded-pill px-3 fw-bold" title="Print/View Report">
                                            <i class="bi bi-printer me-1"></i>Report
                                        </a>
                                    </div>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center py-5">
                                <i class="bi bi-calendar2-range fs-1 text-muted opacity-25"></i>
                                <div class="text-muted italic mt-3">The leave ledger is currently vacant of any active filings.</div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        @else
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light-soft text-erp-deep border-0">
                    <tr>
                        <th class="ps-4 py-3">Personnel Identity</th>
                        <th class="py-3">Absence Span</th>
                        <th class="py-3">Validator</th>
                        <th class="py-3">Validation Date</th>
                        <th class="text-end pe-4 py-3">Outcome Status</th>
                    </tr>
                </thead>
                <tbody class="border-0">
                    @forelse($approved as $row)
                        <tr>
                            <td class="ps-4">
                                <div class="fw-800 text-erp-deep">{{ $row->employee->name ?? 'Legacy Identity' }}</div>
                                <small class="text-muted fw-bold">ID: #LV-{{ $row->id }}</small>
                            </td>
                            <td>
                                <div class="d-flex flex-column font-monospace">
                                    <span class="fw-700 text-dark">
                                        {{ \Carbon\Carbon::parse($row->start_date)->format('Y-m-d') }}
                                    </span>
                                    <span class="text-muted small">to {{ \Carbon\Carbon::parse($row->end_date)->format('Y-m-d') }}</span>
                                </div>
                            </td>
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    <div class="avatar-xs bg-light rounded-circle p-1" style="width: 24px; height: 24px;">
                                        <i class="bi bi-person-check text-success"></i>
                                    </div>
                                    <span class="fw-700 text-dark small">{{ $row->approver->name ?? 'System Process' }}</span>
                                </div>
                            </td>
                            <td>
                                <div class="small fw-700 text-muted">
                                    {{ $row->approved_at ? $row->approved_at->format('M d, Y') : 'N/A' }}
                                    <div class="x-small fw-normal">at {{ $row->approved_at ? $row->approved_at->format('H:i') : '—' }}</div>
                                </div>
                            </td>
                            <td class="text-end pe-4">
                                <span class="badge bg-success-soft text-success rounded-pill px-3 py-2 border-0 fw-800">
                                    <i class="bi bi-shield-check me-1"></i>VERIFIED
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center py-5">
                                <i class="bi bi-archive fs-1 text-muted opacity-25"></i>
                                <div class="text-muted italic mt-3">The authorized absence archive is currently vacant.</div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        @endif
    </div>
    
    @php
        $paginationData = $view === 'active' ? $requests : $approved;
    @endphp

    @if($paginationData->hasPages())
        <div class="card-footer border-0 p-4">
            {{ $paginationData->links() }}
        </div>
    @endif
</div>
@endsection

