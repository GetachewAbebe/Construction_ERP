@extends('layouts.app')

@section('title', 'Loan Details – Inventory')

@section('content')
    <div class="container py-4">

        {{-- Breadcrumb / back --}}
        <div class="row mb-3">
            <div class="col d-flex justify-content-between align-items-center">
                <div>
                    <a href="{{ route('inventory.loans.index') }}" class="text-decoration-none small text-muted">
                        ← Back to Item Lending
                    </a>
                    <h1 class="h4 mb-1 mt-1">Loan Details</h1>
                    <p class="text-muted mb-0">
                        Detailed information about this item lending record.
                    </p>
                </div>

                <div class="text-end">
                    @php
                        $badge = match($loan->status) {
                            'approved' => 'success',
                            'returned' => 'secondary',
                            'rejected' => 'danger',
                            default    => 'warning',
                        };
                    @endphp
                    <span class="badge bg-{{ $badge }} px-3 py-2">
                        Status: {{ ucfirst($loan->status) }}
                    </span>
                </div>
            </div>
        </div>

        {{-- Flash messages --}}
        @if(session('status'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('status') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif
        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        {{-- Main card --}}
        <div class="card shadow-soft border-0 mb-4">
            <div class="card-body">
                <div class="row g-4">

                    {{-- Left column: Item & employee --}}
                    <div class="col-md-6 border-end-md">
                        <h2 class="h6 text-erp-deep mb-3">Item Information</h2>

                        <dl class="row mb-0">
                            <dt class="col-sm-4 small text-muted">Item</dt>
                            <dd class="col-sm-8">
                                <div class="fw-semibold">
                                    {{ $loan->item->name ?? '—' }}
                                </div>
                                <div class="small text-muted">
                                    Item No: {{ $loan->item->item_no ?? '—' }}
                                </div>
                            </dd>

                            <dt class="col-sm-4 small text-muted mt-3">Quantity</dt>
                            <dd class="col-sm-8 mt-3">
                                <span class="fw-semibold">{{ $loan->quantity }}</span>
                            </dd>

                            <dt class="col-sm-4 small text-muted mt-3">Store Location</dt>
                            <dd class="col-sm-8 mt-3">
                                {{ $loan->item->store_location ?? '—' }}
                            </dd>
                        </dl>

                        <hr class="my-4">

                        <h2 class="h6 text-erp-deep mb-3">Employee</h2>

                        <dl class="row mb-0">
                            <dt class="col-sm-4 small text-muted">Name</dt>
                            <dd class="col-sm-8">
                                {{ $loan->employee->full_name ?? '—' }}
                            </dd>

                            <dt class="col-sm-4 small text-muted mt-3">Position</dt>
                            <dd class="col-sm-8 mt-3">
                                {{ $loan->employee->position ?? '—' }}
                            </dd>

                            <dt class="col-sm-4 small text-muted mt-3">Project / Site</dt>
                            <dd class="col-sm-8 mt-3">
                                {{ $loan->employee->project_site ?? '—' }}
                            </dd>
                        </dl>
                    </div>

                    {{-- Right column: Dates & approvals --}}
                    <div class="col-md-6">
                        <h2 class="h6 text-erp-deep mb-3">Lending & Return</h2>

                        <dl class="row mb-0">
                            <dt class="col-sm-4 small text-muted">Requested at</dt>
                            <dd class="col-sm-8">
                                {{ optional($loan->created_at)->format('Y-m-d H:i') ?? '—' }}
                            </dd>

                            <dt class="col-sm-4 small text-muted mt-3">Lend date</dt>
                            <dd class="col-sm-8 mt-3">
                                {{ $loan->lend_date ? optional($loan->lend_date)->format('Y-m-d') : '—' }}
                            </dd>

                            <dt class="col-sm-4 small text-muted mt-3">Expected return</dt>
                            <dd class="col-sm-8 mt-3">
                                {{ $loan->expected_return_date ? optional($loan->expected_return_date)->format('Y-m-d') : '—' }}
                            </dd>

                            <dt class="col-sm-4 small text-muted mt-3">Actual return</dt>
                            <dd class="col-sm-8 mt-3">
                                {{ $loan->actual_return_date ? optional($loan->actual_return_date)->format('Y-m-d') : '—' }}
                            </dd>
                        </dl>

                        <hr class="my-4">

                        <h2 class="h6 text-erp-deep mb-3">Approval trail</h2>

                        <dl class="row mb-0">
                            <dt class="col-sm-4 small text-muted">Approved by</dt>
                            <dd class="col-sm-8">
                                {{ $loan->approvedBy->name ?? '—' }}
                                @if($loan->approved_at)
                                    <div class="small text-muted">
                                        {{ optional($loan->approved_at)->format('Y-m-d H:i') }}
                                    </div>
                                @endif
                            </dd>

                            <dt class="col-sm-4 small text-muted mt-3">Rejected by</dt>
                            <dd class="col-sm-8 mt-3">
                                {{ $loan->rejectedBy->name ?? '—' }}
                                @if($loan->rejected_at)
                                    <div class="small text-muted">
                                        {{ optional($loan->rejected_at)->format('Y-m-d H:i') }}
                                    </div>
                                @endif
                            </dd>
                        </dl>
                    </div>

                </div>
            </div>
        </div>

        {{-- Actions row --}}
        <div class="d-flex justify-content-between">
            <a href="{{ route('inventory.loans.index') }}" class="btn btn-outline-secondary">
                ← Back to Lending List
            </a>

            <div class="btn-group">
                @if($loan->status === 'pending')
                    <a href="{{ route('inventory.loans.edit', $loan) }}" class="btn btn-outline-primary">
                        Edit Request
                    </a>
                    <form action="{{ route('inventory.loans.destroy', $loan) }}"
                          method="POST"
                          onsubmit="return confirm('Delete this pending loan request?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-outline-danger">
                            Delete
                        </button>
                    </form>
                @elseif($loan->status === 'approved')
                    <form action="{{ route('inventory.loans.mark-returned', $loan) }}"
                          method="POST"
                          onsubmit="return confirm('Mark this loan as returned and update item quantity?');">
                        @csrf
                        <button type="submit" class="btn btn-success">
                            Mark as Returned
                        </button>
                    </form>
                @endif
            </div>
        </div>

    </div>
@endsection
