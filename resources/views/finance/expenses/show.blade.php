@extends('layouts.app')
@section('title', 'Financial Voucher | Natanem Engineering')

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

    body {
        font-family: 'Outfit', sans-serif;
    }

    /* Print Precision */
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
        .document-wrapper {
            background: #f1f5f9;
            padding: 2.5rem 1rem;
            min-height: 100vh;
        }
        .premium-document {
            box-shadow: 0 0 0 1px rgba(0,0,0,0.05), 0 20px 50px -12px rgba(0,0,0,0.1);
            border-radius: 2px;
        }
    }

    /* Professional Elements */
    .logo-text {
        font-weight: 900;
        font-size: 1.75rem;
        letter-spacing: -1px;
        color: var(--corporate-blue);
        line-height: 1;
    }

    .document-badge {
        font-size: 0.65rem;
        font-weight: 800;
        padding: 4px 10px;
        border-radius: 4px;
        text-transform: uppercase;
        letter-spacing: 0.05em;
    }

    .voucher-header-title {
        font-weight: 800;
        font-size: 2.25rem;
        color: var(--slate-900);
        letter-spacing: -1.5px;
    }

    /* Grid Layout */
    .info-section {
        border-top: 2px solid var(--slate-900);
        padding-top: 1.5rem;
        margin-bottom: 2rem;
    }

    .info-item-label {
        font-size: 0.7rem;
        font-weight: 800;
        color: var(--slate-400);
        text-transform: uppercase;
        letter-spacing: 0.1em;
        margin-bottom: 0.25rem;
    }

    .info-item-value {
        font-weight: 700;
        font-size: 1rem;
        color: var(--slate-900);
    }

    /* Table Design */
    .official-table {
        width: 100%;
        border-collapse: collapse;
        margin-bottom: 2rem;
    }

    .official-table th {
        text-align: left;
        font-size: 0.75rem;
        font-weight: 800;
        color: var(--slate-600);
        text-transform: uppercase;
        letter-spacing: 0.05em;
        padding: 12px 15px;
        border-bottom: 2px solid var(--slate-900);
        background: var(--bg-soft);
    }

    .official-table td {
        padding: 20px 15px;
        border-bottom: 1px solid var(--border-color);
        vertical-align: top;
    }

    .amount-column {
        text-align: right;
        font-family: 'Outfit', sans-serif;
        font-weight: 800;
        font-size: 1.25rem;
    }

    .total-row {
        background: var(--bg-soft);
    }

    .total-row td {
        border-bottom: 3px double var(--slate-900);
        font-weight: 900;
        padding: 15px;
    }

    /* Signatures */
    .signature-grid {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 40px;
        margin-top: 5rem;
    }

    .signature-box {
        position: relative;
        padding-top: 40px;
    }

    .signature-line {
        border-top: 1.5px solid var(--slate-900);
        margin-bottom: 8px;
    }

    .signature-label {
        font-size: 0.65rem;
        font-weight: 800;
        color: var(--slate-400);
        text-transform: uppercase;
        letter-spacing: 0.05em;
    }

    /* Security Stamp */
    .auth-stamp {
        position: absolute;
        top: -10px;
        left: 20px;
        border: 3px solid #10b981;
        color: #10b981;
        padding: 4px 12px;
        font-weight: 950;
        font-size: 0.75rem;
        text-transform: uppercase;
        transform: rotate(-12deg);
        border-radius: 4px;
        background: rgba(255, 255, 255, 0.9);
        z-index: 10;
        pointer-events: none;
        box-shadow: 0 0 10px rgba(16, 185, 129, 0.1);
    }

    @media print {
        @page { size: A4; margin: 0; }
        .no-print { display: none !important; }
        .document-wrapper { padding: 0 !important; background: white !important; }
        .premium-document { box-shadow: none !important; width: 100%; padding: 12mm !important; }
        body { background: white !important; }
    }
</style>
@endpush

<div class="document-wrapper">
    {{-- Control Toolbar --}}
    <div class="container-fluid no-print mb-4">
        <div class="d-flex justify-content-between align-items-center bg-white p-3 rounded-3 shadow-sm" style="max-width: 210mm; margin: 0 auto;">
            <a href="{{ route('finance.expenses.index') }}" class="btn btn-link text-decoration-none text-muted fw-bold">
                <i class="bi bi-chevron-left"></i> Back to Finance Ledger
            </a>
            <div class="d-flex gap-2">
                @if(Auth::user()->hasAnyRole(['Administrator', 'FinancialManager', 'Financial Manager']) && $expense->status === 'pending')
                    <button class="btn btn-success rounded-pill px-4 fw-bold shadow-sm" onclick="document.getElementById('approve-form').submit()">Approve Voucher</button>
                    <form id="approve-form" action="{{ route('finance.expenses.approve', $expense) }}" method="POST" class="d-none">@csrf</form>
                @endif
                <button onclick="window.print()" class="btn btn-dark rounded-pill px-4 fw-bold">
                    <i class="bi bi-printer-fill me-2"></i>Print Official Report
                </button>
            </div>
        </div>
    </div>

    {{-- A4 Content --}}
    <div class="premium-document">
        {{-- Header Section --}}
        <div class="d-flex justify-content-between align-items-start mb-5">
            <div>
                <div class="logo-text mb-1">NATANEM</div>
                <div class="small fw-800 text-slate-600 uppercase tracking-widest mb-2">Engineering & Civil Solutions</div>
                <div class="small text-slate-400">
                    Bole West, House #492, Addis Ababa, Ethiopia<br>
                    finance@natanemeng.com | +251 116 123 456
                </div>
            </div>
            <div class="text-end">
                <div class="voucher-header-title mb-2">PAYMENT VOUCHER</div>
                <div class="d-flex justify-content-end gap-2">
                    <div class="document-badge bg-light border text-dark">VOUCHER ID: #{{ str_pad($expense->id, 6, '0', STR_PAD_LEFT) }}</div>
                    <div class="document-badge bg-dark text-white">REF: {{ $expense->reference_no ?? 'VCH-'.$expense->id }}</div>
                </div>
            </div>
        </div>

        {{-- Primary Info Block --}}
        <div class="info-section">
            <div class="row g-4 text-center">
                <div class="col-3 text-start">
                    <div class="info-item-label">Date of Issue</div>
                    <div class="info-item-value">{{ $expense->expense_date->format('d M, Y') }}</div>
                </div>
                <div class="col-3 text-start">
                    <div class="info-item-label">Authorized By</div>
                    <div class="info-item-value">{{ optional($expense->approvedBy)->name ?? '---' }}</div>
                </div>
                <div class="col-6 text-end">
                    <div class="info-item-label">Total Amount Payable</div>
                    <div class="fw-900 text-slate-900 display-6 lh-1">
                        <span class="fs-5 fw-600">ETB</span> {{ number_format($expense->amount, 2) }}
                    </div>
                </div>
            </div>
        </div>

        {{-- Context Grid --}}
        <div class="row g-4 mb-5">
            <div class="col-6">
                <div class="p-3 bg-light border-start border-4 border-dark h-100">
                    <div class="info-item-label">Project Allocation</div>
                    <div class="info-item-value fs-5">{{ optional($expense->project)->name ?? 'General Administrative' }}</div>
                    <div class="small text-slate-400 mt-1">{{ optional($expense->project)->location }}</div>
                </div>
            </div>
            <div class="col-6">
                <div class="p-3 bg-light border-start border-4 border-dark h-100">
                    <div class="info-item-label">Beneficiary Particulars</div>
                    <div class="info-item-value fs-5">{{ optional($expense->user)->name ?? 'Natanem Staff' }}</div>
                    <div class="small text-slate-400 mt-1">{{ optional($expense->user)->position ?? 'Operations Department' }}</div>
                </div>
            </div>
        </div>

        {{-- Particulars Table --}}
        <table class="official-table">
            <thead>
                <tr>
                    <th>Expenditure Description / Particulars</th>
                    <th class="text-end">Amount (ETB)</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td class="py-5">
                        <div class="fw-800 text-dark fs-5 mb-2">{{ strtoupper($expense->category) }} SERVICES</div>
                        <p class="text-slate-600 fw-500 lh-lg m-0" style="max-width: 85%;">
                            {{ $expense->description ?? 'Official expenditure recorded for project-related operations and service requirements as per internal requisition.' }}
                        </p>
                    </td>
                    <td class="amount-column py-5">{{ number_format($expense->amount, 2) }}</td>
                </tr>
                <tr class="total-row">
                    <td class="text-end text-slate-600 fs-6">GRAND TOTAL PAYABLE</td>
                    <td class="amount-column fs-3">{{ number_format($expense->amount, 2) }}</td>
                </tr>
            </tbody>
        </table>

        {{-- Budget Tracking (Subtle) --}}
        <div class="row mb-5">
            <div class="col-7">
                <div class="p-3 bg-light rounded-2 border">
                    <div class="row align-items-center">
                        <div class="col-4 border-end">
                            <div class="info-item-label">Total Budget</div>
                            <div class="fw-700 small">ETB {{ number_format($expense->project->budget, 2) }}</div>
                        </div>
                        <div class="col-4 border-end px-3">
                            <div class="info-item-label">Budget Used</div>
                            <div class="fw-700 small text-danger">ETB {{ number_format($expense->project->total_expenses, 2) }}</div>
                        </div>
                        <div class="col-4 px-3 text-end">
                            <div class="info-item-label">Net Balance</div>
                            <div class="fw-700 small text-success">ETB {{ number_format($expense->project->budget - $expense->project->total_expenses, 2) }}</div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-5">
                <div class="text-end">
                    <div class="info-item-label">Authorization Status</div>
                    <span class="badge {{ $expense->status === 'approved' ? 'bg-success' : 'bg-warning' }} rounded-1 px-3 py-2 fw-900">
                        {{ strtoupper($expense->status ?? 'PENDING') }}
                    </span>
                </div>
            </div>
        </div>

        {{-- Signature Section --}}
        <div class="signature-grid">
            <div class="signature-box">
                <div class="signature-line"></div>
                <div class="fw-800 text-slate-800 small">{{ optional($expense->user)->name }}</div>
                <div class="signature-label">Prepared By (Requester)</div>
            </div>
            
            <div class="signature-box">
                <div class="signature-line"></div>
                <div class="fw-800 text-slate-300 small italic">Pending Site Stamp</div>
                <div class="signature-label">Verified By (Project Manager)</div>
            </div>

            <div class="signature-box">
                @if($expense->status === 'approved')
                    <div class="auth-stamp">ELECTRONICALLY VERIFIED</div>
                @endif
                <div class="signature-line"></div>
                <div class="fw-800 text-slate-800 small">{{ optional($expense->approvedBy)->name ?? 'Financial Officer' }}</div>
                <div class="signature-label">Authorized By (Finance)</div>
            </div>
        </div>

        {{-- Final Footer --}}
        <div class="text-center mt-5 pt-5 opacity-25">
            <div class="fw-900 fs-5 text-slate-900 lh-1">NATANEM ENGINEERING SOLUTIONS</div>
            <div class="small fw-700 uppercase tracking-widest mt-1">Certified Financial Record | Generated via ERP Enterprise v2.0</div>
            <div class="mt-2 font-monospace x-small" style="font-size: 0.6rem;">
                DOC_HASH: {{ strtoupper(hash('sha256', $expense->id . now())) }}
            </div>
        </div>
    </div>
</div>
@endsection
