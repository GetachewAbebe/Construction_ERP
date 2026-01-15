@extends('layouts.app')

@section('title', 'Expense Approvals | Admin')

@push('head')
<style>
    .approvals-container {
        padding: 3rem 0;
    }
    .glass-card-approval {
        background: rgba(255, 255, 255, 0.95);
        backdrop-filter: blur(15px);
        border: 1px solid rgba(255, 255, 255, 0.4);
        border-radius: 24px;
        box-shadow: 0 15px 35px rgba(0,0,0,0.05);
        margin-bottom: 2rem;
    }
    .table-modern thead th {
        background: rgba(0,0,0,0.02);
        color: #64748b;
        font-weight: 700;
        text-transform: uppercase;
        font-size: 0.75rem;
        letter-spacing: 0.05em;
        padding: 1.25rem 1rem;
        border: none;
    }
    .status-badge-pending {
        background: rgba(245, 158, 11, 0.1);
        color: #f59e0b;
        font-weight: 700;
    }
    .btn-approve {
        background: #10b981;
        color: white;
        border: none;
        transition: all 0.2s;
    }
    .btn-approve:hover {
        background: #059669;
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(16, 185, 129, 0.3);
        color: white;
    }
</style>
@endpush

@section('content')
<div class="approvals-container container">
    {{-- Header --}}
    <div class="row align-items-center mb-5">
        <div class="col-lg-8">
            <h1 class="display-6 fw-800 text-erp-deep mb-2">Finance Administration</h1>
            <p class="text-muted fs-5 mb-0">Review and authorize project expenditures from departments.</p>
        </div>
        <div class="col-lg-4 text-lg-end mt-4 mt-lg-0">
            <a href="{{ route('admin.dashboard') }}" class="btn btn-white shadow-sm rounded-pill px-4 fw-bold">
                <i class="bi bi-arrow-left me-2"></i> Dashboard
            </a>
        </div>
    </div>

    @if(session('status'))
        <div class="alert alert-success alert-dismissible fade show rounded-4 mb-4" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i> {{ session('status') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    {{-- Pending Section --}}
    <div class="glass-card-approval p-4 p-md-5">
        <div class="d-flex align-items-center justify-content-between mb-4">
            <h5 class="fw-800 text-erp-deep mb-0">Pending Expenditures</h5>
            <span class="badge status-badge-pending rounded-pill px-3 py-2">
                {{ $pendingExpenses->total() }} Requires Auth
            </span>
        </div>

        @if($pendingExpenses->isEmpty())
            <div class="text-center py-5">
                <div class="text-muted mb-3"><i class="bi bi-receipt fs-1 opacity-25"></i></div>
                <p class="text-muted fw-500">No pending expenses to review.</p>
            </div>
        @else
            <div class="table-responsive">
                <table class="table table-modern align-middle mb-0">
                    <thead>
                        <tr>
                            <th>Project & Category</th>
                            <th>Amount</th>
                            <th>Recorded By</th>
                            <th>Date</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($pendingExpenses as $expense)
                        <tr>
                            <td>
                                <div class="fw-bold text-dark">{{ $expense->project->name }}</div>
                                <div class="badge bg-light text-muted fw-normal border">{{ ucfirst($expense->category) }}</div>
                                <div class="text-muted small mt-1">{{ Str::limit($expense->description, 40) }}</div>
                            </td>
                            <td>
                                <div class="fw-800 text-erp-deep">ETB {{ number_format($expense->amount, 2) }}</div>
                                <small class="text-muted">Ref: {{ $expense->reference_no ?? 'N/A' }}</small>
                            </td>
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    <div class="avatar-pill bg-soft-primary d-flex align-items-center justify-content-center fw-bold" style="width: 32px; height: 32px; border-radius: 8px; font-size: 0.8rem;">
                                        {{ substr($expense->user->first_name ?? 'U', 0, 1) }}
                                    </div>
                                    <span class="small fw-600 text-slate-700">{{ $expense->user->name ?? 'System' }}</span>
                                </div>
                            </td>
                            <td class="text-muted small">
                                {{ $expense->expense_date->format('M d, Y') }}
                            </td>
                            <td class="text-end">
                                <div class="d-flex gap-2 justify-content-end">
                                    <form method="POST" action="{{ route('admin.requests.finance.approve', $expense) }}">
                                        @csrf
                                        <button type="submit" class="btn btn-approve btn-sm rounded-pill px-3 fw-bold">
                                            Allow
                                        </button>
                                    </form>
                                    <button type="button" class="btn btn-outline-danger btn-sm rounded-pill px-3 fw-bold" 
                                            data-bs-toggle="modal" data-bs-target="#rejectModal{{ $expense->id }}">
                                        Deny
                                    </button>
                                </div>

                                {{-- Reject Modal --}}
                                <div class="modal fade" id="rejectModal{{ $expense->id }}" tabindex="-1" aria-hidden="true">
                                    <div class="modal-dialog modal-dialog-centered">
                                        <div class="modal-content border-0 rounded-4 shadow-xl">
                                            <form method="POST" action="{{ route('admin.requests.finance.reject', $expense) }}">
                                                @csrf
                                                <div class="modal-header border-0 pb-0">
                                                    <h5 class="fw-800 text-erp-deep">Deny Expenditure</h5>
                                                    <button type="button" class="btn-close" data-bs-close="modal"></button>
                                                </div>
                                                <div class="modal-body py-4">
                                                    <p class="text-muted mb-4 text-start">Please provide a reason for denying this ETB {{ number_format($expense->amount, 2) }} expense for <strong>{{ $expense->project->name }}</strong>.</p>
                                                    <div class="text-start">
                                                        <label class="form-label fw-bold small text-uppercase mb-2">Rejection Reason</label>
                                                        <textarea name="rejection_reason" class="form-control rounded-3" rows="3" placeholder="e.g. Insufficient documentation or incorrect category..." required></textarea>
                                                    </div>
                                                </div>
                                                <div class="modal-footer border-0 pt-0">
                                                    <button type="button" class="btn btn-light rounded-pill px-4 fw-bold" data-bs-dismiss="modal">Cancel</button>
                                                    <button type="submit" class="btn btn-danger rounded-pill px-4 fw-bold">Confirm Denial</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="mt-4">
                {{ $pendingExpenses->links() }}
            </div>
        @endif
    </div>
</div>

<style>
    .bg-soft-primary { background: rgba(59, 130, 246, 0.1); color: #2563eb; }
</style>
@endsection
