@extends('layouts.app')
@section('title', 'Leave Request Detail')

@section('content')
<div class="row align-items-center mb-4 d-print-none stagger-entrance">
    <div class="col">
        <h1 class="h3 mb-1 fw-800 text-erp-deep">Leave Specification</h1>
        <p class="text-muted mb-0">Detailed analysis and formal documentation of leave filing #LRQ-{{ str_pad($leave->id, 5, '0', STR_PAD_LEFT) }}</p>
    </div>
    <div class="col-auto">
        <button onclick="window.print()" class="btn btn-white rounded-pill px-4 shadow-sm border-0">
            <i class="bi bi-printer me-2"></i>Print Official Report
        </button>
        <a href="{{ route('hr.leaves.index') }}" class="btn btn-erp-deep rounded-pill px-4 shadow-sm border-0 ms-2">
            <i class="bi bi-arrow-left me-2"></i>Return to Ledger
        </a>
    </div>
</div>

<div class="card hardened-glass border-0 shadow-sm overflow-hidden p-0 stagger-entrance" style="animation-delay: 0.1s;">
    {{-- Header / Brand Section --}}
    <div class="p-5 text-white" style="background: linear-gradient(135deg, var(--erp-deep), var(--erp-glass-dark));">
        <div class="row align-items-center">
            <div class="col">
                <div class="text-uppercase small fw-bold mb-2 opacity-75">Formal Personnel Respite Filing</div>
                <h2 class="fw-800 mb-0">LEAVE AUTHORIZATION DOCUMENT</h2>
            </div>
            <div class="col-auto text-end">
                <div class="fw-800 fs-4 mb-0">#LRQ-{{ str_pad($leave->id, 5, '0', STR_PAD_LEFT) }}</div>
                <div class="small fw-bold opacity-75">Filed on: {{ $leave->created_at->format('M d, Y') }}</div>
            </div>
        </div>
    </div>

    <div class="card-body p-5">
        <div class="row g-5">
            {{-- Employee Section --}}
            <div class="col-md-6 border-end">
                <h5 class="fw-800 text-erp-deep mb-4"><i class="bi bi-person-badge me-2 text-primary"></i>Associate Credentials</h5>
                <div class="mb-4">
                    <label class="small fw-bold text-muted text-uppercase d-block mb-1">Full Identity</label>
                    <div class="fw-800 text-dark fs-5">{{ $leave->employee->name }}</div>
                </div>
                <div class="row">
                    <div class="col-6">
                        <label class="small fw-bold text-muted text-uppercase d-block mb-1">Functional Unit</label>
                        <div class="fw-700 text-erp-deep">{{ $leave->employee->department ?? 'General Operations' }}</div>
                    </div>
                    <div class="col-6">
                        <label class="small fw-bold text-muted text-uppercase d-block mb-1">Professional Rank</label>
                        <div class="fw-700 text-erp-deep">{{ $leave->employee->position ?? 'Professional associate' }}</div>
                    </div>
                </div>
            </div>

            {{-- Period Section --}}
            <div class="col-md-6">
                <h5 class="fw-800 text-erp-deep mb-4"><i class="bi bi-calendar-check me-2 text-primary"></i>Temporal Parameters</h5>
                <div class="d-flex align-items-center gap-4 mb-4 p-3 bg-light-soft rounded-4 border">
                    <div class="text-center px-3 border-end">
                        <label class="small fw-bold text-muted text-uppercase d-block">Start</label>
                        <div class="fw-800 text-erp-deep fs-5">{{ $leave->start_date->format('M d') }}</div>
                        <small class="text-muted fw-bold">{{ $leave->start_date->format('Y') }}</small>
                    </div>
                    <div class="flex-grow-1 text-center py-2">
                        <i class="bi bi-arrow-right fs-4 text-muted"></i>
                        <div class="badge bg-primary rounded-pill px-3">{{ $leave->start_date->diffInDays($leave->end_date) + 1 }} Working Days</div>
                    </div>
                    <div class="text-center px-3 border-start">
                        <label class="small fw-bold text-muted text-uppercase d-block">End</label>
                        <div class="fw-800 text-erp-deep fs-5">{{ $leave->end_date->format('M d') }}</div>
                        <small class="text-muted fw-bold">{{ $leave->end_date->format('Y') }}</small>
                    </div>
                </div>
            </div>
        </div>

        <hr class="my-5 opacity-10">

        {{-- Reason Section --}}
        <div class="mb-5">
            <h5 class="fw-800 text-erp-deep mb-4"><i class="bi bi-chat-left-dots me-2 text-primary"></i>Contextual Rationale</h5>
            <div class="p-4 bg-light-soft rounded-4 border-start border-primary border-4 shadow-sm">
                <p class="mb-0 text-dark fw-600 italic">"{{ $leave->reason }}"</p>
            </div>
        </div>

        <div class="row align-items-center">
            <div class="col">
                <h5 class="fw-800 text-erp-deep mb-3"><i class="bi bi-shield-check me-2 text-primary"></i>Adjudication Status</h5>
                @php
                    $statusColor = match($leave->status) {
                        'Approved' => 'success',
                        'Rejected' => 'danger',
                        default   => 'warning'
                    };
                @endphp
                <div class="d-flex align-items-center gap-3">
                    <span class="badge bg-{{ $statusColor }} fs-6 rounded-pill px-4 py-2 shadow-sm border-0">
                        {{ strtoupper($leave->status) }}
                    </span>
                    @if($leave->status === 'Approved')
                        <small class="text-success fw-bold"><i class="bi bi-check-all me-1"></i>Execution Authorized</small>
                    @elseif($leave->status === 'Pending')
                        <small class="text-warning fw-bold"><i class="bi bi-hourglass-split me-1"></i>Awaiting Operational Review</small>
                    @endif
                </div>
            </div>
            <div class="col-md-5 text-end">
                <div class="p-4 border rounded-4 d-inline-block text-start bg-white shadow-sm" style="min-width: 250px;">
                    <div class="mb-4">
                        <label class="small fw-bold text-muted text-uppercase d-block mb-3">Operational Authentication</label>
                        <div class="border-bottom border-dark border-2 mb-2" style="height: 40px; border-style: dotted !important;"></div>
                        <div class="small text-muted fw-bold">Executive Signature / Stamp</div>
                    </div>
                    <div class="small fw-bold text-erp-deep mt-2">DATED: <span class="ms-1 border-bottom border-dark">__________________</span></div>
                </div>
            </div>
        </div>
    </div>

    <div class="card-footer bg-light p-4 text-center border-0 opacity-75">
        <small class="fw-bold text-muted">Generated by NATANEM ERP SYSTEM - Core HR Module • Operational Compliance Verified</small>
    </div>
</div>

<style>
@media print {
    body { background: white !important; padding: 0 !important; }
    .stagger-entrance { animation: none !important; opacity: 1 !important; transform: none !important; }
    .hardened-glass { background: white !important; box-shadow: none !important; border: 1px solid #eee !important; }
    .card { border: none !important; }
    .navbar, .sidebar { display: none !important; }
    .main-content { margin: 0 !important; padding: 0 !important; }
    .card-footer { border-top: 1px solid #eee !important; }
    @page { margin: 2cm; }
}
</style>
@endsection
