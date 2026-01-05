@extends('layouts.app')

@section('title', 'Leave Approvals | Admin')

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
</style>
@endpush

@section('content')
<div class="approvals-container container">
    {{-- Header --}}
    <div class="row align-items-center mb-5">
        <div class="col-lg-8">
            <h1 class="display-6 fw-800 text-erp-deep mb-2">Leave Administration</h1>
            <p class="text-muted fs-5 mb-0">Review requests and manage employee absence schedules.</p>
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
            <h5 class="fw-800 text-erp-deep mb-0">Pending Requests</h5>
            <span class="badge status-badge-pending rounded-pill px-3 py-2">
                {{ $pending->total() }} Needs Review
            </span>
        </div>

        @if($pending->isEmpty())
            <div class="text-center py-5">
                <div class="text-muted mb-3"><i class="bi bi-inbox fs-1 opacity-25"></i></div>
                <p class="text-muted fw-500">No pending leave requests at the moment.</p>
            </div>
        @else
            <div class="table-responsive">
                <table class="table table-modern align-middle mb-0">
                    <thead>
                        <tr>
                            <th>Employee</th>
                            <th>Period</th>
                            <th>Requested On</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($pending as $leave)
                        <tr>
                            <td>
                                <div class="d-flex align-items-center gap-3">
                                    <div class="avatar-pill bg-light text-erp-deep d-flex align-items-center justify-content-center fw-bold" style="width: 40px; height: 40px; border-radius: 12px;">
                                        {{ substr($leave->employee->first_name ?? 'E', 0, 1) }}
                                    </div>
                                    <div>
                                        <div class="fw-bold text-dark">{{ $leave->employee->full_name ?? 'Unknown' }}</div>
                                        <div class="text-muted small">ID: {{ $leave->employee->id }}</div>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <div class="fw-600 text-dark">
                                    {{ $leave->start_date?->format('M d') }} - {{ $leave->end_date?->format('M d, Y') }}
                                </div>
                                <div class="text-muted small">{{ $leave->reason }}</div>
                            </td>
                            <td class="text-muted small">
                                {{ $leave->created_at?->diffForHumans() }}
                            </td>
                            <td class="text-end">
                                <form method="POST" action="{{ route('admin.requests.leave.approve', $leave) }}" class="d-inline">
                                    @csrf
                                    <button type="submit" class="btn btn-success btn-sm rounded-pill px-3 fw-bold">
                                        Approve
                                    </button>
                                </form>
                                <form method="POST" action="{{ route('admin.requests.leave.reject', $leave) }}" class="d-inline ms-1">
                                    @csrf
                                    <button type="submit" class="btn btn-outline-danger btn-sm rounded-pill px-3 fw-bold">
                                        Reject
                                    </button>
                                </form>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="mt-4">
                {{ $pending->links() }}
            </div>
        @endif
    </div>

    {{-- History Section --}}
    <div class="glass-card-approval p-4 p-md-5">
        <h5 class="fw-800 text-erp-deep mb-4">Approval History</h5>

        @if($approved->isEmpty())
            <p class="text-muted py-3">No approved leaves recorded yet.</p>
        @else
            <div class="table-responsive">
                <table class="table table-modern align-middle mb-0">
                    <thead>
                        <tr>
                            <th>Employee</th>
                            <th>Leave Period</th>
                            <th>Authorized By</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($approved as $record)
                        <tr>
                            <td>
                                <div class="fw-bold text-dark">{{ $record->employee->full_name ?? 'Unknown' }}</div>
                                <div class="text-muted small">{{ $record->reason }}</div>
                            </td>
                            <td class="small fw-600">
                                {{ $record->start_date?->format('M d') }} - {{ $record->end_date?->format('M d, Y') }}
                            </td>
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    <span class="badge bg-light text-dark border fw-bold">{{ $record->approver->name ?? '-' }}</span>
                                    <div class="text-muted" style="font-size: 0.7rem;">{{ $record->approved_at?->format('M d, H:i') }}</div>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="mt-4">
                {{ $approved->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
