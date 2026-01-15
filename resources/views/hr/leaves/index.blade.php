@extends('layouts.app')

@section('title', 'Leave Requests')

@section('content')
    <div class="container py-4">

        {{-- HERO / HEADER --}}
        <div class="row mb-4">
            <div class="col">
                <div class="card shadow-soft border-0 bg-erp-soft">
                    <div class="card-body d-flex flex-column flex-md-row align-items-md-center justify-content-between">
                        <div>
                            <div class="small text-uppercase text-muted mb-1">
                                Human Resource Module
                            </div>
                            <h1 class="h4 mb-2 text-erp-deep">
                                Leave Requests
                            </h1>
                            <p class="mb-0 text-muted">
                                Track submitted leave requests, current leave status and approvals.
                            </p>
                        </div>
                        <div class="mt-3 mt-md-0 text-md-end">
                            <a href="{{ route('hr.leaves.create') }}"
                               class="btn btn-sm btn-success">
                                File New Leave
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- SUMMARY FILTERS / METRICS --}}
        <div class="row g-3 mb-4">
            <div class="col-md-4">
                <div class="card shadow-soft border-0 h-100">
                    <div class="card-body">
                        <h6 class="small text-uppercase text-muted mb-1">Pending Requests</h6>
                        <div class="d-flex align-items-baseline gap-2 mb-1">
                            <span class="fs-4 fw-semibold text-erp-deep">
                                {{ $pendingCount ?? '—' }}
                            </span>
                            <span class="text-muted small">awaiting action</span>
                        </div>
                        <p class="text-muted small mb-0">
                            Requests that still need review or approval.
                        </p>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card shadow-soft border-0 h-100">
                    <div class="card-body">
                        <h6 class="small text-uppercase text-muted mb-1">Approved</h6>
                        <div class="d-flex align-items-baseline gap-2 mb-1">
                            <span class="fs-4 fw-semibold text-erp-deep">
                                {{ $approvedCount ?? '—' }}
                            </span>
                            <span class="text-muted small">in total</span>
                        </div>
                        <p class="text-muted small mb-0">
                            Leave requests that have been approved.
                        </p>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card shadow-soft border-0 h-100">
                    <div class="card-body">
                        <h6 class="small text-uppercase text-muted mb-1">Rejected</h6>
                        <div class="d-flex align-items-baseline gap-2 mb-1">
                            <span class="fs-4 fw-semibold text-erp-deep">
                                {{ $rejectedCount ?? '—' }}
                            </span>
                            <span class="text-muted small">total</span>
                        </div>
                        <p class="text-muted small mb-0">
                            Leave requests that have been rejected or cancelled.
                        </p>
                    </div>
                </div>
            </div>
        </div>

        {{-- LEAVE REQUESTS TABLE --}}
        <div class="row">
            <div class="col">
                <div class="card shadow-soft border-0">
                    <div class="card-body">
                        <h5 class="card-title mb-3 text-erp-deep">All Leave Requests</h5>

                        {{-- Optional filter row --}}
                        <form class="row g-2 mb-3">
                            <div class="col-md-3">
                                <input type="text" class="form-control form-control-sm" placeholder="Employee name">
                            </div>
                            <div class="col-md-3">
                                <select class="form-select form-select-sm">
                                    <option value="">Status (Any)</option>
                                    <option>Pending</option>
                                    <option>Approved</option>
                                    <option>Rejected</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <input type="date" class="form-control form-control-sm">
                            </div>
                            <div class="col-md-3 text-md-end">
                                <button type="submit" class="btn btn-sm btn-outline-success">
                                    Filter
                                </button>
                            </div>
                        </form>

                        {{-- Placeholder table --}}
                        <div class="table-responsive">
                            <table class="table table-sm align-middle mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>Employee</th>
                                        <th>Type</th>
                                        <th>From</th>
                                        <th>To</th>
                                        <th>Status</th>
                                        <th class="text-end">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($requests as $leave)
                                    <tr>
                                        <td>{{ optional($leave->employee)->first_name }} {{ optional($leave->employee)->last_name }}</td>
                                        <td>Annual</td>
                                        <td>{{ \Illuminate\Support\Carbon::parse($leave->start_date)->format('Y-m-d') }}</td>
                                        <td>{{ \Illuminate\Support\Carbon::parse($leave->end_date)->format('Y-m-d') }}</td>
                                        <td>
                                            <span class="badge @if($leave->status=='Pending') bg-warning-subtle text-erp-deep @elseif($leave->status=='Approved') bg-success-subtle text-success @else bg-secondary-subtle @endif">
                                                {{ $leave->status }}
                                            </span>
                                        </td>
                                        <td class="text-end">
                                            @if($leave->status === 'Pending')
                                                @can('Administrator')
                                                    <a href="{{ route('admin.requests.leave') }}" class="btn btn-sm btn-outline-primary">Process</a>
                                                @else
                                                    <span class="text-muted small">Pending Review</span>
                                                @endcan
                                            @else
                                                <span class="text-muted small">{{ $leave->status }}</span>
                                            @endif
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                    </div>
                </div>
            </div>
        </div>

    </div>
@endsection
