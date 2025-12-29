@extends('layouts.app')

@section('title', 'Leave Approvals - Admin')

@section('content')
<div class="container py-4">
    {{-- Header --}}
    <div class="row mb-3">
        <div class="col">
            <h1 class="h4 mb-1">Leave Approvals</h1>
            <p class="text-muted mb-0">
                Review pending leave requests and track approved leaves.
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

    {{-- Pending requests --}}
    <div class="card shadow-soft border-0 mb-4">
        <div class="card-body">
            <h6 class="text-uppercase text-muted small mb-3">Pending Requests</h6>

            @if($pending->isEmpty())
                <p class="text-muted mb-0">
                    No pending leave requests at the moment.
                </p>
            @else
                <div class="table-responsive">
                    <table class="table align-middle mb-0">
                        <thead>
                            <tr>
                                <th>Requested At</th>
                                <th>Employee</th>
                                <th>Dates</th>
                                <th>Reason</th>
                                <th class="text-end">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                        @foreach($pending as $leave)
                            <tr>
                                <td>
                                    <div class="small text-muted">
                                        {{ $leave->created_at?->format('Y-m-d H:i') ?? '-' }}
                                    </div>
                                </td>
                                <td>
                                    <div class="fw-semibold">
                                        {{ $leave->employee->full_name ?? 'Unknown' }}
                                    </div>
                                    <div class="small text-muted">
                                        ID: {{ $leave->employee->id ?? '-' }}
                                    </div>
                                </td>
                                <td>
                                    <div class="small">
                                        {{ $leave->start_date?->format('Y-m-d') ?? '-' }}
                                        to
                                        {{ $leave->end_date?->format('Y-m-d') ?? '-' }}
                                    </div>
                                </td>
                                <td>
                                    <span class="small text-muted">
                                        {{ $leave->reason ?? '-' }}
                                    </span>
                                </td>
                                <td class="text-end">
                                    <form method="POST"
                                          action="{{ route('admin.requests.leave.approve', $leave) }}"
                                          class="d-inline">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-success">
                                            Approve
                                        </button>
                                    </form>

                                    <form method="POST"
                                          action="{{ route('admin.requests.leave.reject', $leave) }}"
                                          class="d-inline ms-1">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-outline-danger">
                                            Reject
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="mt-3">
                    {{ $pending->links() }}
                </div>
            @endif
        </div>
    </div>

    {{-- Approved requests --}}
    <div class="card shadow-soft border-0">
        <div class="card-body">
            <h6 class="text-uppercase text-muted small mb-3">Approved Leaves</h6>

            @if($approved->isEmpty())
                <p class="text-muted mb-0">
                    No approved leaves recorded yet.
                </p>
            @else
                <div class="table-responsive">
                    <table class="table align-middle mb-0">
                        <thead>
                            <tr>
                                <th>Approved At</th>
                                <th>Employee</th>
                                <th>Dates</th>
                                <th>Reason</th>
                                <th>Approved By</th>
                            </tr>
                        </thead>
                        <tbody>
                        @foreach($approved as $record)
                            <tr>
                                <td>
                                    <div class="small text-muted">
                                        {{ $record->approved_at?->format('Y-m-d H:i') ?? '-' }}
                                    </div>
                                </td>
                                <td>
                                    <div class="fw-semibold">
                                        {{ $record->employee->full_name ?? 'Unknown' }}
                                    </div>
                                    <div class="small text-muted">
                                        ID: {{ $record->employee->id ?? '-' }}
                                    </div>
                                </td>
                                <td>
                                    <div class="small">
                                        {{ $record->start_date?->format('Y-m-d') ?? '-' }}
                                        to
                                        {{ $record->end_date?->format('Y-m-d') ?? '-' }}
                                    </div>
                                </td>
                                <td>
                                    <span class="small text-muted">
                                        {{ $record->reason ?? '-' }}
                                    </span>
                                </td>
                                <td>
                                    <span class="small text-muted">
                                        {{ $record->approver->name ?? '-' }}
                                    </span>
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="mt-3">
                    {{ $approved->links() }}
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
