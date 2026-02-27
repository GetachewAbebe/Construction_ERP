@extends('layouts.app')

@section('title', 'Finance Authorization')

@section('content')
<div class="page-header-premium mb-5">
    <div class="row align-items-center">
        <div class="col">
            <h1 class="display-6">Finance Administration</h1>
            <p>Review and authorize field expenditure requests from production sites.</p>
        </div>
        <div class="col-auto">
            <a href="{{ route('admin.dashboard') }}" class="btn btn-white shadow-sm rounded-pill px-4 fw-800">
                <i class="bi bi-arrow-left me-2"></i> Dashboard
            </a>
        </div>
    </div>
</div>

{{-- Pending Authorization --}}
<div class="erp-card p-4 p-md-5 stagger-entrance">
    <div class="d-flex align-items-center justify-content-between mb-4 border-bottom pb-3">
        <h5 class="fw-800 text-erp-deep mb-0">Pending Expenditures</h5>
        <span class="badge bg-warning text-dark rounded-pill px-3 py-2 fw-800">
            {{ $pendingExpenses->total() }} ACTION REQUIRED
        </span>
    </div>

    @if($pendingExpenses->isEmpty())
        <div class="text-center py-5">
            <div class="text-muted mb-3"><i class="bi bi-patch-check display-4 opacity-25"></i></div>
            <p class="text-muted fw-800 fs-5">All financial requisitions have been processed.</p>
        </div>
    @else
        <div class="table-responsive">
            <table class="table-premium align-middle mb-0">
                <thead>
                    <tr>
                        <th class="ps-0">Site & Category</th>
                        <th>Transaction Value</th>
                        <th>Requester</th>
                        <th>Date</th>
                        <th class="text-end pe-0">Management</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($pendingExpenses as $expense)
                    <tr>
                        <td class="ps-0">
                            <div class="fw-800 text-erp-deep fs-6">{{ $expense->project->name ?? 'Global Site' }}</div>
                            <div class="badge bg-light text-muted fw-800 border-0 rounded-pill x-small px-2">{{ strtoupper($expense->category) }}</div>
                            <div class="text-muted small mt-1 italic">{{ Str::limit($expense->description, 50) }}</div>
                        </td>
                        <td>
                            <div class="fw-900 text-erp-deep fs-5">
                                <small class="text-muted fw-normal x-small me-1">ETB</small>{{ number_format($expense->amount, 0) }}
                            </div>
                            <small class="text-muted fw-bold">REF: {{ $expense->reference_no ?? 'N/A' }}</small>
                        </td>
                        <td>
                            <div class="d-flex align-items-center gap-2">
                                <div class="bg-light-soft text-erp-deep d-flex align-items-center justify-content-center fw-900 border" style="width: 32px; height: 32px; border-radius: 10px; font-size: 0.75rem;">
                                    {{ substr($expense->user->first_name ?? 'U', 0, 1) }}
                                </div>
                                <span class="small fw-800 text-slate-700">{{ $expense->user->name ?? 'System' }}</span>
                            </div>
                        </td>
                        <td class="text-muted fs-7 fw-bold">
                            {{ $expense->expense_date->format('M d, Y') }}
                        </td>
                        <td class="text-end pe-0">
                            <div class="d-flex gap-2 justify-content-end">
                                <form method="POST" action="{{ route('admin.requests.finance.approve', $expense) }}" id="app-exp-{{ $expense->id }}">
                                    @csrf
                                    <button type="submit" class="btn btn-erp-deep btn-sm rounded-pill px-4 fw-800 shadow-sm">
                                        Authorize
                                    </button>
                                </form>
                                <button type="button" class="btn btn-outline-danger btn-sm rounded-pill px-4 fw-800 shadow-sm" 
                                        onclick="document.getElementById('reject-form-{{ $expense->id }}').classList.toggle('d-none')">
                                    Decline
                                </button>
                            </div>
                            
                            {{-- Inline Rejection Form (Cleaner than Modals for quick auth) --}}
                            <div id="reject-form-{{ $expense->id }}" class="d-none mt-3 p-3 bg-light rounded-4 text-start border shadow-sm stagger-entrance">
                                <form method="POST" action="{{ route('admin.requests.finance.reject', $expense) }}">
                                    @csrf
                                    <label class="form-label-premium mb-2">Specify Rejection Reason</label>
                                    <textarea name="rejection_reason" class="form-control rounded-3 mb-2" rows="2" placeholder="e.g. Missing receipt or incorrect category..." required></textarea>
                                    <div class="d-flex gap-2">
                                        <button type="submit" class="btn btn-danger btn-sm rounded-pill px-3 fw-800">Confirm Rejection</button>
                                        <button type="button" class="btn btn-white btn-sm rounded-pill px-3 fw-800" onclick="document.getElementById('reject-form-{{ $expense->id }}').classList.add('d-none')">Cancel</button>
                                    </div>
                                </form>
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
@endsection
