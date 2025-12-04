@extends('layouts.app')

@section('title', 'Item Lending – Inventory')

@section('content')
    <div class="container py-4">

        {{-- Page header --}}
        <div class="row mb-3">
            <div class="col d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="h4 mb-1">Item Lending</h1>
                    <p class="text-muted mb-0">
                        Track which items are lent to which employees, and follow up on returns.
                    </p>
                </div>
                <div>
                    <a href="{{ route('inventory.loans.create') }}" class="btn btn-success">
                        Record / Request Loan
                    </a>
                </div>
            </div>
        </div>

        {{-- Status flash --}}
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

        {{-- Loans table --}}
        <div class="card shadow-soft border-0">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h2 class="h6 mb-0 text-erp-deep">Lending Records</h2>
                    <span class="badge bg-light text-muted">
                        Total: {{ $loans->total() }}
                    </span>
                </div>

                @if($loans->isEmpty())
                    <p class="text-muted mb-0">
                        No lending records yet. Use <strong>Record / Request Loan</strong> to start tracking issued items.
                    </p>
                @else
                    <div class="table-responsive">
                        <table class="table align-middle table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Item</th>
                                    <th>Employee</th>
                                    <th>Qty</th>
                                    <th>Status</th>
                                    <th>Requested</th>
                                    <th>Expected Return</th>
                                    <th>Approved By</th>
                                    <th>Returned</th>
                                    <th class="text-end">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($loans as $loan)
                                    @php
                                        $badge = match($loan->status) {
                                            'approved' => 'success',
                                            'returned' => 'secondary',
                                            'rejected' => 'danger',
                                            default    => 'warning',
                                        };
                                    @endphp
                                    <tr>
                                        <td>
                                            <div class="fw-semibold">
                                                {{ $loan->item->name ?? '—' }}
                                            </div>
                                            <div class="small text-muted">
                                                Item No: {{ $loan->item->item_no ?? '—' }}
                                            </div>
                                        </td>
                                        <td>
                                            <div class="fw-semibold">
                                                {{ $loan->employee->full_name ?? '—' }}
                                            </div>
                                            <div class="small text-muted">
                                                {{ $loan->employee->position ?? '' }}
                                            </div>
                                        </td>
                                        <td>
                                            <span class="fw-semibold">{{ $loan->quantity }}</span>
                                        </td>
                                        <td>
                                            <span class="badge bg-{{ $badge }}">
                                                {{ ucfirst($loan->status) }}
                                            </span>
                                        </td>
                                        <td class="small text-muted">
                                            {{-- Requested = when the loan record was created --}}
                                            {{ optional($loan->created_at)->format('Y-m-d H:i') ?? '—' }}
                                        </td>
                                        <td class="small text-muted">
                                            {{-- expected_return_date from DB (cast to date/datetime in model) --}}
                                            {{ $loan->expected_return_date?->format('Y-m-d') ?? '—' }}
                                        </td>
                                        <td class="small">
                                            {{ $loan->approvedBy->name ?? '—' }}
                                        </td>
                                        <td class="small text-muted">
                                            {{-- actual_return_date (not returned -> null) --}}
                                            {{ $loan->actual_return_date?->format('Y-m-d') ?? '—' }}
                                        </td>
                                        <td class="text-end">
                                            <div class="btn-group btn-group-sm" role="group">
                                                <a href="{{ route('inventory.loans.show', $loan) }}"
                                                   class="btn btn-outline-secondary">
                                                    View
                                                </a>

                                                @if($loan->status === 'pending')
                                                    <a href="{{ route('inventory.loans.edit', $loan) }}"
                                                       class="btn btn-outline-primary">
                                                        Edit
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
                                                          onsubmit="return confirm('Mark this loan as returned?');">
                                                        @csrf
                                                        <button type="submit" class="btn btn-outline-success">
                                                            Mark Returned
                                                        </button>
                                                    </form>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-3">
                        {{ $loans->links() }}
                    </div>
                @endif
            </div>
        </div>

    </div>
@endsection
