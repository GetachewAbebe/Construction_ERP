@extends('layouts.app')

@section('title', 'Expenses - Natanem Engineering')

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 fw-bold mb-0">Financial Expenses</h1>
            <p class="text-muted mb-0">Track all costs across projects and categories.</p>
        </div>
        <a href="{{ route('finance.expenses.create') }}" class="btn btn-primary rounded-pill px-4">
            <i class="bi bi-plus-lg me-2"></i>Record Expense
        </a>
    </div>

    @if(session('status'))
        <div class="alert alert-success alert-dismissible fade show rounded-4 border-0 shadow-sm mb-4" role="alert">
            {{ session('status') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    {{-- Filters --}}
    <div class="card border-0 shadow-sm rounded-4 mb-4">
        <div class="card-body">
            <form action="{{ route('finance.expenses.index') }}" method="GET" class="row g-3">
                <div class="col-md-4">
                    <label class="form-label small fw-bold text-muted">Project</label>
                    <select name="project_id" class="form-select border-light bg-light rounded-3">
                        <option value="">All Projects</option>
                        @foreach($projects as $p)
                            <option value="{{ $p->id }}" {{ request('project_id') == $p->id ? 'selected' : '' }}>{{ $p->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label small fw-bold text-muted">Category</label>
                    <select name="category" class="form-select border-light bg-light rounded-3">
                        <option value="">All Categories</option>
                        <option value="materials" {{ request('category') == 'materials' ? 'selected' : '' }}>Materials</option>
                        <option value="labor" {{ request('category') == 'labor' ? 'selected' : '' }}>Labor</option>
                        <option value="transport" {{ request('category') == 'transport' ? 'selected' : '' }}>Transport</option>
                        <option value="equipment" {{ request('category') == 'equipment' ? 'selected' : '' }}>Equipment</option>
                        <option value="other" {{ request('category') == 'other' ? 'selected' : '' }}>Other</option>
                    </select>
                </div>
                <div class="col-md-5 d-flex align-items-end gap-2">
                    <button type="submit" class="btn btn-dark rounded-3 px-4">Filter</button>
                    <a href="{{ route('finance.expenses.index') }}" class="btn btn-outline-secondary rounded-3 px-4">Reset</a>
                </div>
            </form>
        </div>
    </div>

    <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light">
                    <tr>
                        <th class="ps-4">Date</th>
                        <th>Project</th>
                        <th>Category</th>
                        <th>Status</th>
                        <th>Description</th>
                        <th>Amount</th>
                        <th class="text-end pe-4">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($expenses as $expense)
                        <tr>
                            <td class="ps-4">
                                <div class="fw-bold">{{ $expense->expense_date->format('M d, Y') }}</div>
                                <small class="text-muted">{{ $expense->user->name ?? 'System' }}</small>
                            </td>
                            <td>
                                <a href="{{ route('finance.projects.show', $expense->project) }}" class="text-decoration-none fw-semibold">
                                    {{ $expense->project->name }}
                                </a>
                            </td>
                            <td>
                                <span class="badge rounded-pill bg-light text-dark border px-3">
                                    {{ ucfirst($expense->category) }}
                                </span>
                            </td>
                            <td>
                                @if($expense->status === 'approved')
                                    <span class="badge rounded-pill bg-success-soft text-success px-3">
                                        <i class="bi bi-check2-circle me-1"></i>Approved
                                    </span>
                                @elseif($expense->status === 'rejected')
                                    <span class="badge rounded-pill bg-danger-soft text-danger px-3" title="Reason: {{ $expense->rejection_reason }}">
                                        <i class="bi bi-x-circle me-1"></i>Rejected
                                    </span>
                                @else
                                    <span class="badge rounded-pill bg-warning-soft text-warning px-3">
                                        <i class="bi bi-clock me-1"></i>Pending
                                    </span>
                                @endif
                            </td>
                            <td>
                                <div class="text-truncate" style="max-width: 250px;">
                                    {{ $expense->description ?? 'No description' }}
                                </div>
                                @if($expense->reference_no)
                                    <small class="text-muted mt-1 d-block"><i class="bi bi-receipt me-1"></i>{{ $expense->reference_no }}</small>
                                @endif
                            </td>
                            <td class="fw-bold text-dark">
                                ETB {{ number_format($expense->amount, 2) }}
                            </td>
                            <td class="text-end pe-4">
                                <div class="d-flex gap-2 justify-content-end align-items-center">
                                    @if($expense->status === 'pending')
                                        @if(Auth::user()->hasRole('Administrator'))
                                            <form method="POST" action="{{ route('admin.requests.finance.approve', $expense) }}" class="d-inline">
                                                @csrf
                                                <button type="submit" class="btn btn-sm btn-success rounded-pill px-3 fw-bold">Approve</button>
                                            </form>
                                            <button type="button" class="btn btn-sm btn-outline-danger rounded-pill px-3 fw-bold" 
                                                    data-bs-toggle="modal" data-bs-target="#rejectModal{{ $expense->id }}">Reject</button>
                                        @else
                                            <a href="{{ route('finance.expenses.edit', $expense) }}" class="btn btn-sm btn-outline-secondary rounded-pill px-3">Edit</a>
                                        @endif
                                    @endif

                                    <a href="{{ route('finance.expenses.show', $expense) }}" class="btn btn-sm btn-erp-deep rounded-pill px-3 shadow-sm">
                                        <i class="bi bi-file-earmark-text me-1"></i>Report
                                    </a>
                                </div>

                                @if($expense->status !== 'pending')
                                    <div class="text-muted mt-2" style="font-size: 0.7rem;">
                                        <i class="bi bi-person-check-fill me-1"></i>
                                        @if($expense->status === 'approved')
                                            Approved by {{ $expense->approvedBy->name ?? 'Admin' }}
                                        @else
                                            Rejected by {{ $expense->rejectedBy->name ?? 'Admin' }}
                                        @endif
                                    </div>
                                @endif

                                {{-- Reject Modal --}}
                                @if(Auth::user()->hasRole('Administrator') && $expense->status === 'pending')
                                <div class="modal fade text-start" id="rejectModal{{ $expense->id }}" tabindex="-1" aria-hidden="true">
                                    <div class="modal-dialog modal-dialog-centered">
                                        <div class="modal-content border-0 rounded-4 shadow-xl">
                                            <form method="POST" action="{{ route('admin.requests.finance.reject', $expense) }}">
                                                @csrf
                                                <div class="modal-header border-0 pb-0">
                                                    <h5 class="fw-800 text-erp-deep">Reject Expense</h5>
                                                    <button type="button" class="btn-close" data-bs-close="modal"></button>
                                                </div>
                                                <div class="modal-body py-4">
                                                    <p class="text-muted mb-4">Provide a reason for rejecting the ETB {{ number_format($expense->amount, 2) }} expense.</p>
                                                    <label class="form-label fw-bold small text-uppercase mb-2">Rejection Reason</label>
                                                    <textarea name="rejection_reason" class="form-control rounded-3" rows="3" required placeholder="e.g. Unclear description..."></textarea>
                                                </div>
                                                <div class="modal-footer border-0 pt-0">
                                                    <button type="button" class="btn btn-light rounded-pill px-4 fw-bold" data-bs-dismiss="modal">Cancel</button>
                                                    <button type="submit" class="btn btn-danger rounded-pill px-4 fw-bold">Confirm Reject</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-5">
                                <div class="text-muted">No expenses recorded matching your criteria.</div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($expenses->hasPages())
            <div class="card-footer bg-white border-0 py-3">
                {{ $expenses->links() }}
            </div>
        @endif
    </div>
</div>

<style>
    .bg-success-soft { background-color: rgba(25, 135, 84, 0.1); color: #198754; }
    .bg-warning-soft { background-color: rgba(255, 193, 7, 0.1); color: #ffc107; }
    .bg-danger-soft { background-color: rgba(220, 53, 69, 0.1); color: #dc3545; }
</style>
@endsection
