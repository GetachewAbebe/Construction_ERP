@extends('layouts.app')
@section('title', 'Item Lending Registry | Natanem Engineering')

@section('content')
<div class="row align-items-center mb-4 stagger-entrance">
    <div class="col">
        <h1 class="h3 mb-1 fw-800 text-erp-deep">Asset Lending Registry</h1>
        <p class="text-muted mb-0">Track tool assignments, material issuance, and return schedules.</p>
    </div>
    <div class="col-auto">
        <a href="{{ route('inventory.loans.create') }}" class="btn btn-erp-deep rounded-pill px-4 shadow-sm border-0">
            <i class="bi bi-box-arrow-right me-2"></i>New Loan Request
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

<div class="card hardened-glass border-0 overflow-hidden shadow-lg stagger-entrance">
    <div class="card-body p-0">
        {{-- Filter Header --}}
        <form action="{{ route('inventory.loans.index') }}" method="GET" class="p-4 bg-light-soft border-bottom">
            <div class="row g-3 align-items-end">
                <div class="col-md-6">
                    <label class="form-label small fw-bold text-erp-deep text-uppercase">Search Records</label>
                    <div class="input-group bg-white rounded-pill overflow-hidden shadow-sm px-3 border border-light">
                        <span class="input-group-text bg-transparent border-0 ps-2"><i class="bi bi-search text-muted"></i></span>
                        <input type="text" name="q" class="form-control border-0 py-3 bg-transparent" 
                               placeholder="Search employee or item name..." 
                               value="{{ request('q') }}">
                    </div>
                </div>
                <div class="col-md-3">
                    <label class="form-label small fw-bold text-erp-deep text-uppercase">Loan Status</label>
                    <select name="status" class="form-select border border-light bg-white rounded-pill shadow-sm py-3 px-4 text-dark">
                        <option value="">All Status</option>
                        <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending Approval</option>
                        <option value="approved" {{ request('status') === 'approved' ? 'selected' : '' }}>Active (Issued)</option>
                        <option value="returned" {{ request('status') === 'returned' ? 'selected' : '' }}>Returned / Closed</option>
                        <option value="rejected" {{ request('status') === 'rejected' ? 'selected' : '' }}>Rejected</option>
                    </select>
                </div>
                <div class="col-md-3 d-flex gap-2">
                    <button type="submit" class="btn btn-erp-deep rounded-pill px-4 flex-grow-1 border-0 shadow-sm fw-bold">
                        Filter
                    </button>
                    <a href="{{ route('inventory.loans.index') }}" class="btn btn-white rounded-pill px-3 border border-light shadow-sm" title="Reset Filters">
                        <i class="bi bi-arrow-counterclockwise"></i>
                    </a>
                </div>
            </div>
        </form>

        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light-soft text-erp-deep">
                    <tr>
                        <th class="ps-4">Asset Details</th>
                        <th>Borrower</th>
                        <th>Status</th>
                        <th>Timeline</th>
                        <th class="text-end pe-4">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($loans as $loan)
                        <tr>
                            <td class="ps-4">
                                <div class="d-flex align-items-center gap-3">
                                    <div class="avatar-sm bg-white shadow-sm rounded-3 text-primary d-flex align-items-center justify-content-center">
                                        <i class="bi bi-box-seam fs-5"></i>
                                    </div>
                                    <div>
                                        <div class="fw-800 text-erp-deep">{{ optional($loan->item)->name ?? 'Unknown Item' }}</div>
                                        <div class="small text-muted fw-bold">
                                            {{ optional($loan->item)->item_no ?? '---' }} 
                                            <span class="badge bg-light text-dark ms-1 border">{{ $loan->quantity }} Units</span>
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <div class="fw-700 text-dark">{{ optional($loan->employee)->name ?? 'Unknown' }}</div>
                                <div class="x-small text-muted">{{ optional(optional($loan->employee)->position_rel)->title ?? 'Staff' }}</div>
                            </td>
                            <td>
                                @php
                                    $statusMeta = match($loan->status) {
                                        'approved' => ['class' => 'bg-success-soft text-success', 'icon' => 'bi-check-circle-fill', 'label' => 'Active Loan'],
                                        'pending' => ['class' => 'bg-warning-soft text-warning', 'icon' => 'bi-hourglass-split', 'label' => 'Pending'],
                                        'returned' => ['class' => 'bg-secondary-soft text-secondary', 'icon' => 'bi-arrow-return-left', 'label' => 'Returned'],
                                        'rejected' => ['class' => 'bg-danger-soft text-danger', 'icon' => 'bi-x-circle-fill', 'label' => 'Rejected'],
                                        default => ['class' => 'bg-light text-muted', 'icon' => 'bi-question', 'label' => $loan->status],
                                    };
                                @endphp
                                <span class="badge {{ $statusMeta['class'] }} rounded-pill px-3 py-2 border-0 fw-bold">
                                    <i class="bi {{ $statusMeta['icon'] }} me-1"></i>{{ $statusMeta['label'] }}
                                </span>
                            </td>
                            <td>
                                <div class="d-flex flex-column small">
                                    <span class="text-muted">Requested: <span class="fw-bold text-dark">{{ optional($loan->requested_at)->format('M d') }}</span></span>
                                    @if($loan->status === 'returned')
                                        <span class="text-success">Returned: <span class="fw-bold">{{ optional($loan->returned_at)->format('M d') }}</span></span>
                                    @elseif($loan->expected_return_date)
                                        <span class="{{ $loan->expected_return_date->isPast() && $loan->status == 'approved' ? 'text-danger fw-bold' : 'text-muted' }}">
                                            Due: {{ $loan->expected_return_date->format('M d') }}
                                        </span>
                                    @endif
                                </div>
                            </td>
                            <td class="text-end pe-4">
                                <div class="btn-group shadow-sm rounded-pill p-1 border border-light bg-white">
                                    <a href="{{ route('inventory.loans.show', $loan) }}" class="btn btn-sm btn-white rounded-pill px-3" title="View Details">
                                        <i class="bi bi-eye-fill text-primary"></i>
                                    </a>

                                    @if($loan->status === 'pending')
                                        <a href="{{ route('inventory.loans.edit', $loan) }}" class="btn btn-sm btn-white rounded-pill px-3" title="Edit Request">
                                            <i class="bi bi-pencil-fill text-dark"></i>
                                        </a>
                                        <button type="button" class="btn btn-sm btn-white text-danger rounded-pill px-3" 
                                                onclick="if(confirm('Cancel this loan request?')) document.getElementById('del-{{ $loan->id }}').submit()" title="Delete Request">
                                            <i class="bi bi-trash-fill"></i>
                                            <form id="del-{{ $loan->id }}" action="{{ route('inventory.loans.destroy', $loan) }}" method="POST" class="d-none">@csrf @method('DELETE')</form>
                                        </button>
                                    @elseif($loan->status === 'approved')
                                        <button type="button" class="btn btn-sm btn-white text-success rounded-pill px-3" 
                                                onclick="if(confirm('Mark items as returned?')) document.getElementById('ret-{{ $loan->id }}').submit()" title="Mark Returned">
                                            <i class="bi bi-box-arrow-in-left me-1"></i>Return
                                            <form id="ret-{{ $loan->id }}" action="{{ route('inventory.loans.mark-returned', $loan) }}" method="POST" class="d-none">@csrf</form>
                                        </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center py-5">
                                <i class="bi bi-inbox fs-1 text-muted opacity-25"></i>
                                <div class="text-muted italic mt-3">No lending records found matching your criteria.</div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($loans->hasPages())
            <div class="card-footer border-0 bg-white p-4">
                {{ $loans->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
