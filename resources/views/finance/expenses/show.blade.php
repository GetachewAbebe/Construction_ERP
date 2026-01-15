@extends('layouts.app')

@section('title', 'Financial Report | Natanem Engineering')

@push('head')
<link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:wght@600&family=Outfit:wght@300;400;600;800&display=swap" rel="stylesheet">
<style>
    :root {
        --report-navy: #0f172a;
        --report-accent: #1e40af;
        --report-border: #e2e8f0;
    }

    .report-body {
        font-family: 'Outfit', sans-serif;
        color: #334155;
        background: #f8fafc;
        min-height: 100vh;
    }

    .premium-document {
        background: white;
        width: 210mm; /* A4 Width */
        min-height: 297mm; /* A4 Height */
        margin: 0 auto;
        padding: 15mm !important;
        position: relative;
        overflow: hidden;
        display: flex;
        flex-direction: column;
    }

    /* Visual guide for screen only */
    @media screen {
        .premium-document {
            border: 1px solid var(--report-border);
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.1);
            margin-top: 2rem;
            margin-bottom: 2rem;
        }
    }

    .premium-document::before {
        content: "";
        position: absolute;
        top: 0; left: 0; right: 0; height: 4px;
        background: var(--report-navy);
    }

    .brand-title {
        font-weight: 800;
        letter-spacing: -0.02em;
        color: var(--report-navy);
        font-size: 1.5rem;
    }

    .document-type {
        font-family: 'Cormorant Garamond', serif;
        font-size: 2rem;
        color: #64748b;
        font-style: italic;
        line-height: 1;
    }

    .info-label {
        font-size: 0.65rem;
        font-weight: 800;
        text-transform: uppercase;
        color: #94a3b8;
        letter-spacing: 0.05em;
        margin-bottom: 0.2rem;
    }

    .info-value {
        color: var(--report-navy);
        font-weight: 600;
        font-size: 1rem;
    }

    .amount-display {
        font-size: 2.5rem;
        font-weight: 800;
        letter-spacing: -0.04em;
        color: var(--report-navy);
        line-height: 1;
    }

    .status-stamp {
        border: 3px double;
        padding: 0.4rem 1rem;
        border-radius: 4px;
        font-weight: 900;
        text-transform: uppercase;
        transform: rotate(-10deg);
        position: absolute;
        top: 80mm;
        right: 15mm;
        opacity: 0.6;
        font-size: 1.25rem;
        z-index: 10;
    }

    .stamp-approved { color: #10b981; border-color: #10b981; }
    .stamp-rejected { color: #ef4444; border-color: #ef4444; }
    .stamp-pending { color: #f59e0b; border-color: #f59e0b; }

    @media screen {
        .premium-document {
            border: 1px solid var(--report-border);
            box-shadow: 0 10px 30px rgba(0,0,0,0.05);
            margin-top: 0;
            margin-bottom: 2rem;
        }
    }

    .premium-document {
        background: white;
        width: 210mm; 
        min-height: 297mm;
        margin: 0 auto;
        padding: 15mm !important;
        position: relative;
        overflow: hidden;
        display: flex;
        flex-direction: column;
    }

    .signature-line {
        border-bottom: 1px solid #cbd5e1;
        width: 100%;
        height: 40px;
        margin-bottom: 0.3rem;
    }

    .summary-card {
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        border-radius: 12px;
        padding: 1rem;
    }

    .description-box {
        border: 1px solid #e2e8f0;
        padding: 1rem;
        border-radius: 8px;
        min-height: 80px;
        font-size: 0.95rem;
        line-height: 1.5;
        background: #fdfdfd;
    }

    @media print {
        @page {
            size: A4;
            margin: 0;
        }
        .no-print { display: none !important; }
        body { background: white !important; margin: 0 !important; padding: 0 !important; }
        .report-viewer-mode { background: white !important; padding: 0 !important; display: block !important; }
        .premium-document {
            width: 210mm;
            height: 297mm;
            padding: 15mm !important;
            border: none;
            box-shadow: none;
            margin: 0 !important;
        }
    }
</style>
@endpush

@section('content')
<div class="no-print pt-2 pb-4">
    <div class="d-flex justify-content-between align-items-center">
        <a href="{{ route('finance.expenses.index') }}" class="btn btn-link text-decoration-none text-muted p-0">
            <i class="bi bi-arrow-left me-2"></i>Exit to Ledger
        </a>
        <button onclick="window.print()" class="btn btn-erp-deep text-white rounded-pill px-4 shadow-sm fw-bold">
            <i class="bi bi-printer me-2"></i>Print Report
        </button>
    </div>
</div>

<div class="premium-document">
    
    {{-- Status Stamp --}}
    @if($expense->status === 'approved')
        <div class="status-stamp stamp-approved">VERIFIED</div>
    @elseif($expense->status === 'rejected')
        <div class="status-stamp stamp-rejected">VOID</div>
    @else
        <div class="status-stamp stamp-pending">PENDING</div>
    @endif

    {{-- Header Branding --}}
    <div class="row align-items-center mb-4 pb-4 border-bottom">
        <div class="col-7">
            <div class="brand-title">NATANEM ENGINEERING</div>
            <p class="text-muted" style="font-size: 0.75rem; line-height: 1.4; margin-bottom: 0;">
                Bole West, Addis Ababa, Ethiopia<br>
                information@natanemengineering.com | +251 911 223 344
            </p>
        </div>
        <div class="col-5 text-end">
            <div class="document-type mb-2">Expenditure Report</div>
            <span class="badge bg-light text-dark border px-3 py-2">
                REF: {{ $expense->reference_no ?? 'EXP-REF-'.$expense->id }}
            </span>
        </div>
    </div>

    {{-- Details Grid --}}
    <div class="row g-4 mb-4">
        <div class="col-6">
            <div class="info-label">Project Site Name</div>
            <div class="info-value">{{ $expense->project->name }}</div>
            <div class="text-muted" style="font-size: 0.8rem;">{{ $expense->project->location }}</div>
        </div>
        <div class="col-3">
            <div class="info-label">Category</div>
            <div class="info-value">{{ ucfirst($expense->category) }}</div>
        </div>
        <div class="col-3 text-end">
            <div class="info-label">Transaction Date</div>
            <div class="info-value">{{ $expense->expense_date->format('d M, Y') }}</div>
        </div>
    </div>

    {{-- Scope/Description --}}
    <div class="mb-4">
        <div class="info-label">Narrative / Scope of Work</div>
        <div class="description-box">
            {{ $expense->description ?: 'No narrative provided.' }}
        </div>
    </div>

    {{-- Amount & Financial Impact --}}
    <div class="row align-items-center mb-5">
        <div class="col-6">
            <div class="info-label">Total Expenditure Amount</div>
            <div class="amount-display">
                <span style="font-size: 1.2rem; color: #94a3b8; font-weight: 400;">ETB</span> {{ number_format($expense->amount, 2) }}
            </div>
        </div>
        <div class="col-6">
            <div class="summary-card">
                <div class="info-label mb-2">Project Financial Summary</div>
                <div class="d-flex justify-content-between small mb-1">
                    <span class="text-muted">Total Budget:</span>
                    <span class="fw-bold">ETB {{ number_format($expense->project->budget, 2) }}</span>
                </div>
                <div class="d-flex justify-content-between small mb-1 text-danger">
                    <span>Approved Spending:</span>
                    <span class="fw-bold">ETB {{ number_format($expense->project->total_expenses, 2) }}</span>
                </div>
                <div class="d-flex justify-content-between small border-top pt-1 mt-1 text-success">
                    <span class="fw-bold">Remaining:</span>
                    <span class="fw-800">ETB {{ number_format($expense->project->budget - $expense->project->total_expenses, 2) }}</span>
                </div>
            </div>
        </div>
    </div>

    {{-- Authorized Signatures --}}
    <div class="row g-4 mt-auto">
        <div class="col-4">
            <div class="info-label">Prepared By</div>
            <div class="signature-line"></div>
            <div style="font-size: 0.85rem; font-weight: 700;">{{ $expense->user->name ?? 'System User' }}</div>
            <div class="text-muted" style="font-size: 0.65rem;">Recorded On: {{ $expense->created_at->format('d M, Y') }}</div>
        </div>
        <div class="col-4">
            <div class="info-label">Checked By (PM)</div>
            <div class="signature-line"></div>
            <div class="text-muted small italic" style="font-size: 0.75rem;">Signature & Stamp</div>
        </div>
        <div class="col-4">
            <div class="info-label">Authorized By</div>
            @if($expense->status === 'approved')
                <div class="signature-line border-0 d-flex align-items-end">
                    <div class="text-success fw-bold small"><i class="bi bi-patch-check-fill me-1"></i>SECURED DIGITAL APPROVAL</div>
                </div>
                <div style="font-size: 0.85rem; font-weight: 700;">{{ $expense->approvedBy->name ?? 'Administrator' }}</div>
            @elseif($expense->status === 'rejected')
                <div class="signature-line border-0 d-flex align-items-end">
                    <div class="text-danger fw-bold small"><i class="bi bi-x-circle-fill me-1"></i>VOID / REJECTED</div>
                </div>
                <div style="font-size: 0.85rem; font-weight: 700;">{{ $expense->rejectedBy->name ?? 'Administrator' }}</div>
            @else
                <div class="signature-line text-muted small pt-4 italic">Pending Approval</div>
                <div style="font-size: 0.85rem;" class="text-muted">-</div>
            @endif
            <div class="text-muted" style="font-size: 0.65rem;">Auth Date: {{ $expense->status === 'pending' ? '---' : $expense->updated_at->format('d M, Y') }}</div>
        </div>
    </div>

    {{-- Rejection Memorandum if applicable --}}
    @if($expense->status === 'rejected')
    <div class="mt-4 p-3 border border-danger rounded-3 bg-light">
        <div class="info-label text-danger">Rejection Memorandum</div>
        <p class="small text-danger mb-0">{{ $expense->rejection_reason }}</p>
    </div>
    @endif

    {{-- Document Footer --}}
    <div class="mt-5 pb-2 text-center border-top pt-3">
        <div class="text-muted" style="font-size: 0.6rem; letter-spacing: 0.05em; line-height: 1.8;">
            OFFICIAL FINANCIAL RECORD | GENERATED BY NATANEM ENGINEERING ERP<br>
            DIGITAL FINGERPRINT: {{ strtoupper(substr(hash('sha256', $expense->id . $expense->created_at), 0, 16)) }} | PAGE 1 OF 1<br>
            &copy; {{ date('Y') }} NATANEM ENGINEERING PLC.
        </div>
    </div>

</div>
@endsection
