@extends('layouts.app')
@section('title', 'Leave Approvals | Admin')

@section('content')
<div class="row align-items-center mb-4 stagger-entrance">
    <div class="col">
        <h1 class="h3 mb-1 fw-800 text-erp-deep">Leave Administration</h1>
        <p class="text-muted mb-0">Review requests and adjudicate employee absence schedules.</p>
    </div>
    <div class="col-auto">
        <a href="{{ route('admin.dashboard') }}" class="btn btn-white rounded-pill px-4 shadow-sm border-0">
            <i class="bi bi-arrow-left me-2"></i>Return to Dashboard
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

<div class="row stagger-entrance">
    <div class="col-12">
        {{-- Pending Requests --}}
        <div class="card hardened-glass border-0 overflow-hidden shadow-lg mb-5">
            <div class="card-header bg-white border-0 p-4">
                <div class="d-flex align-items-center justify-content-between">
                    <div class="d-flex align-items-center gap-3">
                        <div class="avatar-sm bg-warning-soft text-warning rounded-circle d-flex align-items-center justify-content-center">
                            <i class="bi bi-hourglass-split fs-5"></i>
                        </div>
                        <h5 class="fw-800 text-erp-deep mb-0">Pending Adjudication</h5>
                    </div>
                    <span class="badge bg-warning-soft text-warning rounded-pill px-3 py-2 fw-800">
                        {{ $pending->total() }} Active Cases
                    </span>
                </div>
            </div>
            
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light-soft text-erp-deep">
                        <tr>
                            <th class="ps-4">Personnel</th>
                            <th>Absence Period</th>
                            <th>Filing Date</th>
                            <th class="text-end pe-4">Adjudication</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($pending as $leave)
                            <tr>
                                <td class="ps-4">
                                    <div class="d-flex align-items-center gap-3">
                                        @if(optional($leave->employee)->profile_picture)
                                            <div class="avatar-sm rounded-circle d-flex align-items-center justify-content-center overflow-hidden border border-2 border-white shadow-sm" style="width: 40px; height: 40px;">
                                                 <img src="{{ asset('storage/' . $leave->employee->profile_picture) }}" class="w-100 h-100 object-fit-cover">
                                            </div>
                                        @else
                                            <div class="avatar-sm bg-light-soft text-erp-deep rounded-circle d-flex align-items-center justify-content-center fw-800 border border-2 border-white shadow-sm" style="width: 40px; height: 40px;">
                                                {{ substr(optional($leave->employee)->first_name ?? 'U', 0, 1) }}
                                            </div>
                                        @endif
                                        <div>
                                            <div class="fw-800 text-erp-deep">{{ optional($leave->employee)->name ?? 'Unknown' }}</div>
                                            <div class="x-small text-muted fw-bold">ID: #{{ optional($leave->employee)->id ?? '---' }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div class="fw-700 text-dark">
                                        {{ $leave->start_date?->format('M d') }} — {{ $leave->end_date?->format('M d, Y') }}
                                    </div>
                                    <div class="small text-muted">{{ Str::limit($leave->reason, 40) }}</div>
                                </td>
                                <td>
                                    <div class="text-dark small fw-bold">{{ $leave->created_at->format('M d, Y') }}</div>
                                    <div class="x-small text-muted">{{ $leave->created_at->diffForHumans() }}</div>
                                </td>
                                <td class="text-end pe-4">
                                    <form method="POST" action="{{ route('admin.requests.leave.approve', $leave) }}" class="d-inline">
                                        @csrf
                                        <button type="submit" class="btn btn-success btn-sm rounded-pill px-3 fw-bold shadow-sm border-0" onclick="return confirm('Authorize this leave request?')">
                                            <i class="bi bi-check-lg me-1"></i>Approve
                                        </button>
                                    </form>
                                    <form method="POST" action="{{ route('admin.requests.leave.reject', $leave) }}" class="d-inline ms-1">
                                        @csrf
                                        <button type="submit" class="btn btn-white text-danger btn-sm rounded-pill px-3 fw-bold border-0" onclick="return confirm('Decline this leave request?')">
                                            <i class="bi bi-x-lg me-1"></i>Reject
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center py-5">
                                    <i class="bi bi-check-circle fs-1 text-muted opacity-25"></i>
                                    <div class="text-muted italic mt-3">All leave requests have been adjudicated.</div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            @if($pending->hasPages())
                <div class="card-footer border-0 bg-white p-4">
                    {{ $pending->links() }}
                </div>
            @endif
        </div>

        {{-- History Section --}}
        <div class="card hardened-glass border-0 overflow-hidden shadow-lg">
            <div class="card-header bg-white border-0 p-4">
                <div class="d-flex align-items-center gap-3">
                    <div class="avatar-sm bg-success-soft text-success rounded-circle d-flex align-items-center justify-content-center">
                        <i class="bi bi-clock-history fs-5"></i>
                    </div>
                    <h5 class="fw-800 text-erp-deep mb-0">Authorization Archive</h5>
                </div>
            </div>

            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light-soft text-erp-deep">
                        <tr>
                            <th class="ps-4">Personnel</th>
                            <th>Authorized Absence</th>
                            <th class="text-end pe-4">Authorization Meta</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($approved as $record)
                            <tr>
                                <td class="ps-4">
                                    <div class="fw-800 text-erp-deep">{{ optional($record->employee)->name ?? 'Unknown' }}</div>
                                    <div class="small text-muted">{{ Str::limit($record->reason, 50) }}</div>
                                </td>
                                <td>
                                    <div class="badge bg-light text-dark border rounded-pill px-3 py-2 fw-600">
                                        {{ $record->start_date?->format('M d') }} — {{ $record->end_date?->format('M d, Y') }}
                                    </div>
                                </td>
                                <td class="text-end pe-4">
                                    <div class="d-flex flex-column align-items-end">
                                        <div class="small fw-bold text-success">
                                            <i class="bi bi-patch-check-fill me-1"></i>{{ optional($record->approver)->name ?? 'System' }}
                                        </div>
                                        <div class="x-small text-muted">{{ $record->approved_at?->format('M d, Y H:i') }}</div>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="text-center py-5">
                                    <div class="text-muted italic">No historical approvals found.</div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($approved->hasPages())
                <div class="card-footer border-0 bg-white p-4">
                    {{ $approved->links() }}
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
