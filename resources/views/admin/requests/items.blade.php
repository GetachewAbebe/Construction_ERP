@extends('layouts.app')

@section('title', 'Item Lending Requests – Admin')

@section('content')
<div class="container py-4">
    {{-- Header --}}
    <div class="row mb-3">
        <div class="col">
            <h1 class="h4 mb-1">Item Lending Requests</h1>
            <p class="text-muted mb-0">
                Approve or reject inventory items requested by employees.
            </p>
        </div>
    </div>

    {{-- Flash messages --}}
    @if(session('status'))
        <div class="alert alert-success py-2">
            {{ session('status') }}
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger py-2">
            {{ session('error') }}
        </div>
    @endif

    {{-- Table of loans --}}
    <div class="card shadow-soft border-0">
        <div class="card-body">
            @if($loans->isEmpty())
                <p class="text-muted mb-0">
                    No item lending requests yet. When inventory managers submit lending records,
                    they will appear here for review.
                </p>
            @else
                <div class="table-responsive">
                    <table class="table align-middle mb-0">
                        <thead>
                            <tr>
                                <th>Requested At</th>
                                <th>Item</th>
                                <th>Employee</th>
                                <th class="text-center">Quantity</th>
                                <th>Status</th>
                                <th class="text-end">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                        @foreach($loans as $loan)
                            @php
                                $status = $loan->status ?? 'pending';
                                $badgeClass = match($status) {
                                    'approved' => 'bg-success',
                                    'rejected' => 'bg-danger',
                                    default     => 'bg-warning text-dark',
                                };
                            @endphp
                            <tr>
                                <td>
                                    <div class="small text-muted">
                                        {{ $loan->created_at?->format('Y-m-d H:i') ?? '—' }}
                                    </div>
                                </td>
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
                                        ID: {{ $loan->employee->id ?? '—' }}
                                    </div>
                                </td>
                                <td class="text-center">
                                    {{ $loan->quantity }}
                                </td>
                                <td>
                                    <span class="badge {{ $badgeClass }}">
                                        {{ ucfirst($status) }}
                                    </span>
                                </td>
                                <td class="text-end">
                                    @if($status === 'pending')
                                        <form method="POST"
                                              action="{{ route('admin.requests.items.approve', $loan) }}"
                                              class="d-inline">
                                            @csrf
                                            <button type="submit"
                                                    class="btn btn-sm btn-success">
                                                Approve
                                            </button>
                                        </form>

                                        <form method="POST"
                                              action="{{ route('admin.requests.items.reject', $loan) }}"
                                              class="d-inline ms-1">
                                            @csrf
                                            <button type="submit"
                                                    class="btn btn-sm btn-outline-danger">
                                                Reject
                                            </button>
                                        </form>
                                    @else
                                        <span class="small text-muted">
                                            No actions
                                        </span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>

                {{-- Pagination --}}
                <div class="mt-3">
                    {{ $loans->links() }}
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
