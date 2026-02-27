@extends('layouts.app')

@section('title','Inventory Loan Approvals – Admin')

@section('content')
<div class="container py-4">

    @if (session('status'))
        <div class="alert alert-success shadow-sm">
            {{ session('status') }}
        </div>
    @endif

    @if ($errors->any())
        <div class="alert alert-danger shadow-sm">
            {{ $errors->first() }}
        </div>
    @endif

    <div class="mb-3">
        <h1 class="h4 mb-1">Inventory Loan Approvals</h1>
        <p class="text-muted small mb-0">
            Review and approve item lending requests submitted by the Inventory team.
        </p>
    </div>

    {{-- Pending requests --}}
    <div class="card shadow-soft border-0 mb-4">
        <div class="card-header bg-white">
            <strong>Pending Requests</strong>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-striped mb-0 align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>Item</th>
                            <th>Employee</th>
                            <th>Qty</th>
                            <th>Requested By</th>
                            <th>Requested At</th>
                            <th>Due Date</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                    @forelse($pending as $loan)
                        <tr>
                            <td>
                                <strong>{{ $loan->item->name ?? '—' }}</strong><br>
                                <span class="small text-muted">
                                    Item No: {{ $loan->item->item_no ?? '—' }}
                                </span>
                            </td>
                            <td>{{ $loan->employee->full_name ?? '—' }}</td>
                            <td>{{ $loan->quantity }}</td>
                            <td>{{ $loan->requestedBy->name ?? '—' }}</td>
                            <td class="small text-muted">
                                {{ $loan->requested_at?->format('Y-m-d H:i') ?? '—' }}
                            </td>
                            <td class="small text-muted">
                                {{ $loan->due_date?->format('Y-m-d') ?? '—' }}
                            </td>
                            <td class="text-end">
                                <div class="d-flex justify-content-end gap-2">
                                    <form method="POST" action="{{ route('admin.inventory-loans.approve', $loan) }}">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-success">
                                            Approve
                                        </button>
                                    </form>
                                    <form method="POST" action="{{ route('admin.inventory-loans.reject', $loan) }}">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-outline-danger">
                                            Reject
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-3 text-muted">
                                No pending loan requests.
                            </td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- Recent activity --}}
    <div class="card shadow-soft border-0">
        <div class="card-header bg-white">
            <strong>Recent Activity</strong>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0 align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>Item</th>
                            <th>Employee</th>
                            <th>Qty</th>
                            <th>Status</th>
                            <th>Approved By</th>
                            <th>Updated At</th>
                        </tr>
                    </thead>
                    <tbody>
                    @forelse($recent as $loan)
                        <tr>
                            <td>{{ $loan->item->name ?? '—' }}</td>
                            <td>{{ $loan->employee->full_name ?? '—' }}</td>
                            <td>{{ $loan->quantity }}</td>
                            <td>
                                <span class="badge bg-{{ $loan->status === 'approved' ? 'success' : ($loan->status === 'rejected' ? 'danger' : 'secondary') }}">
                                    {{ ucfirst($loan->status) }}
                                </span>
                            </td>
                            <td>{{ $loan->approvedBy->name ?? '—' }}</td>
                            <td class="small text-muted">
                                {{ $loan->updated_at->format('Y-m-d H:i') }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-3 text-muted">
                                No recent loan activity.
                            </td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</div>
@endsection
