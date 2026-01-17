@extends('layouts.app')
@section('title', 'Leave Authorization Record')

@section('content')
@push('head')
<link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
<style>
    :root {
        --corporate-blue: #1e3a8a;
        --slate-900: #0f172a;
        --slate-600: #475569;
        --slate-400: #94a3b8;
        --border-color: #e2e8f0;
        --bg-soft: #f8fafc;
    }

    body { font-family: 'Outfit', sans-serif; }

    .premium-document {
        background: white;
        width: 210mm;
        min-height: 297mm;
        margin: 0 auto;
        padding: 15mm !important;
        position: relative;
        color: var(--slate-900);
        box-sizing: border-box;
    }

    @media screen {
        .document-wrapper { background: #f1f5f9; padding: 2.5rem 1rem; min-height: 100vh; }
        .premium-document { box-shadow: 0 20px 50px -12px rgba(0,0,0,0.1); border-radius: 4px; }
    }

    .logo-text { font-weight: 900; font-size: 1.75rem; letter-spacing: -1px; color: var(--corporate-blue); line-height: 1; }
    .voucher-header-title { font-weight: 800; font-size: 2.25rem; color: var(--slate-900); letter-spacing: -1.5px; }
    
    .info-section { border-top: 2px solid var(--slate-900); padding-top: 1.5rem; margin-bottom: 2rem; }
    .info-item-label { font-size: 0.7rem; font-weight: 800; color: var(--slate-400); text-transform: uppercase; letter-spacing: 0.1em; margin-bottom: 0.25rem; }
    .info-item-value { font-weight: 700; font-size: 1rem; color: var(--slate-900); }

    .auth-stamp {
        border: 3px solid #10b981;
        color: #10b981;
        padding: 5px 15px;
        font-weight: 950;
        font-size: 0.8rem;
        text-transform: uppercase;
        transform: rotate(-10deg);
        display: inline-block;
        border-radius: 4px;
        opacity: 0.8;
    }

    @media print {
        @page { size: A4; margin: 0; }
        .no-print { display: none !important; }
        .document-wrapper { padding: 0 !important; background: white !important; }
        .premium-document { box-shadow: none !important; width: 100%; padding: 12mm !important; }
    }
</style>
@endpush

<div class="document-wrapper">
    <div class="container-fluid no-print mb-4">
        <div class="d-flex justify-content-between align-items-center bg-white p-3 rounded-3 shadow-sm" style="max-width: 210mm; margin: 0 auto;">
            <a href="{{ route('hr.leaves.index') }}" class="btn btn-link text-decoration-none text-muted fw-bold">
                <i class="bi bi-chevron-left"></i> Back to HR Ledger
            </a>
            <button onclick="window.print()" class="btn btn-dark rounded-pill px-4 fw-bold">
                <i class="bi bi-printer-fill me-2"></i>Print Official Report
            </button>
        </div>
    </div>

    <div class="premium-document">
        <div class="d-flex justify-content-between align-items-start mb-5">
            <div>
                <div class="logo-text mb-1">NATANEM</div>
                <div class="small fw-800 text-slate-600 uppercase tracking-widest mb-2">Human Resource Management</div>
            </div>
            <div class="text-end">
                <div class="voucher-header-title mb-2">LEAVE AUTHORIZATION</div>
                <div class="badge bg-dark text-white rounded-px px-3 py-2 fw-800">FILING ID #LRQ-{{ str_pad($leave->id, 5, '0', STR_PAD_LEFT) }}</div>
            </div>
        </div>

        <div class="info-section">
            <div class="row g-4 mb-4">
                <div class="col-6">
                    <div class="info-item-label">Employee Identification</div>
                    <div class="info-item-value fs-5">{{ $leave->employee->name }}</div>
                    <div class="small text-slate-400 fw-700 mt-1">{{ $leave->employee->position ?? 'Operations associate' }} | {{ $leave->employee->department ?? 'General Operations' }}</div>
                </div>
                <div class="col-6 text-end">
                    <div class="info-item-label">Adjudicated Status</div>
                    <div class="fw-900 fs-3 text-{{ $leave->status === 'Approved' ? 'success' : ($leave->status === 'Rejected' ? 'danger' : 'warning') }}">
                        {{ strtoupper($leave->status) }}
                    </div>
                </div>
            </div>
        </div>

        <h5 class="fw-900 text-slate-900 mb-4 border-start border-4 border-dark ps-3">Temporal Metrics</h5>
        <div class="row g-4 mb-5">
            <div class="col-4">
                <div class="p-4 bg-light border-start border-4 border-dark h-100">
                    <div class="info-item-label">Commencement Date</div>
                    <div class="fw-900 text-slate-900 fs-4">{{ $leave->start_date->format('d M, Y') }}</div>
                </div>
            </div>
            <div class="col-4">
                <div class="p-4 bg-light border-start border-4 border-dark h-100">
                    <div class="info-item-label">Conclusion Date</div>
                    <div class="fw-900 text-slate-900 fs-4">{{ $leave->end_date->format('d M, Y') }}</div>
                </div>
            </div>
            <div class="col-4">
                <div class="p-4 bg-slate-900 text-white h-100 text-center rounded-3">
                    <div class="info-item-label text-slate-400">Total Duration</div>
                    <div class="fw-900 display-6 lh-1">{{ $leave->start_date->diffInDays($leave->end_date) + 1 }}</div>
                    <div class="small fw-800 uppercase text-slate-400">Working Days</div>
                </div>
            </div>
        </div>

        <div class="mb-5">
            <h5 class="fw-900 text-slate-900 mb-3 border-start border-4 border-dark ps-3">Narrative & Rationale</h5>
            <div class="p-4 bg-white border-2 border-slate-100 rounded-3 text-slate-600 fw-500 lh-lg shadow-sm font-italic">
                "{{ $leave->reason ?? 'Official personal respite request documented for employee leave management.' }}"
            </div>
        </div>

        <div class="row mt-auto pt-5 border-top border-4 border-dark">
            <div class="col-4">
                <div class="border-top border-dark mx-auto mb-2 text-center" style="width: 150px;"></div>
                <div class="text-center small fw-800 text-slate-400 uppercase">Employee Signature</div>
                <div class="text-center fw-900 small text-slate-900 mt-1">{{ $leave->employee->name }}</div>
            </div>
            <div class="col-4">
                <div class="border-top border-dark mx-auto mb-2 text-center" style="width: 150px;"></div>
                <div class="text-center small fw-800 text-slate-400 uppercase">Supervisor Approval</div>
            </div>
            <div class="col-4 text-center">
                @if($leave->status === 'Approved')
                    <div class="auth-stamp mb-2">VERIFIED BY HR</div>
                @endif
                <div class="border-top border-dark mx-auto mb-2" style="width: 150px;"></div>
                <div class="small fw-800 text-slate-400 uppercase">HR Director Signature</div>
                <div class="x-small text-slate-400 mt-1 d-block">Authorized: {{ $leave->updated_at->format('Y-m-d H:i') }}</div>
            </div>
        </div>

        <div class="text-center mt-5 pt-3 opacity-25">
            <div class="fw-900 fs-5 text-slate-900">NATANEM ENGINEERING PERSONNEL ARCHIVE</div>
            <div class="x-small fw-700 uppercase tracking-widest mt-1">HR Compliance Document | Generated via EPR Enterprise</div>
        </div>
    </div>
</div>
@endsection
