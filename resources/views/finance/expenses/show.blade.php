@extends('layouts.app')
@section('title', 'Financial Voucher | ' . ($expense->reference_no ?? 'REF-' . $expense->id))

@section('content')
@push('head')
<link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
<style>
    :root {
        --corporate-blue: #0f172a;
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
        padding: 20mm !important;
        position: relative;
        color: var(--slate-900);
        border: 1px solid #eee;
    }

    @media screen {
        .document-wrapper { background: #f1f5f9; padding: 4rem 1rem; min-height: 100vh; }
        .premium-document { box-shadow: 0 40px 100px -20px rgba(0,0,0,0.15); border-radius: 8px; }
    }

    .logo-text { font-weight: 900; font-size: 2rem; letter-spacing: -1.5px; color: var(--corporate-blue); line-height: 1; }
    .voucher-header-title { font-weight: 900; font-size: 2.5rem; color: var(--slate-900); letter-spacing: -2px; }
    
    .info-section { border-top: 3px solid var(--slate-900); padding-top: 2rem; margin-bottom: 2.5rem; }
    .info-item-label { font-size: 0.75rem; font-weight: 800; color: var(--slate-400); text-transform: uppercase; letter-spacing: 0.15em; margin-bottom: 0.5rem; }
    .info-item-value { font-weight: 700; font-size: 1.15rem; color: var(--slate-900); }

    .official-table { width: 100%; border-collapse: collapse; margin-bottom: 2.5rem; }
    .official-table th { text-align: left; font-size: 0.75rem; font-weight: 800; color: var(--slate-600); text-transform: uppercase; padding: 15px; border-bottom: 2px solid var(--slate-900); background: var(--bg-soft); }
    .official-table td { padding: 25px 15px; border-bottom: 1px solid var(--border-color); vertical-align: top; }

    .amount-display { font-weight: 900; font-size: 2.5rem; color: var(--slate-900); letter-spacing: -1px; }

    .signature-box { border-top: 2px solid var(--slate-900); padding-top: 10px; text-align: center; }

    .watermark-status {
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%) rotate(-30deg);
        font-size: 8rem;
        font-weight: 900;
        opacity: 0.03;
        color: var(--slate-900);
        pointer-events: none;
        z-index: 0;
        text-transform: uppercase;
        white-space: nowrap;
    }

    @media print {
        @page { size: A4; margin: 0; }
        .no-print { display: none !important; }
        .document-wrapper { padding: 0 !important; background: white !important; }
        .premium-document { box-shadow: none !important; width: 100%; padding: 15mm !important; border: none !important; }
    }
</style>
@endpush

<div class="document-wrapper">
    {{-- Control Toolbar --}}
    <div class="container-fluid no-print mb-5">
        <div class="d-flex justify-content-between align-items-center bg-white p-3 rounded-pill shadow-lg border" style="max-width: 210mm; margin: 0 auto;">
            <a href="{{ route('finance.expenses.index') }}" class="btn btn-white rounded-pill px-4 fw-800 shadow-sm">
                <i class="bi bi-arrow-left me-2"></i> Requisitions
            </a>
            <div class="d-flex gap-2">
                @if(Auth::user()->hasAnyRole(['Administrator', 'FinancialManager', 'Financial Manager']) && $expense->status === 'pending')
                    <button class="btn btn-success rounded-pill px-4 fw-800 shadow-sm" onclick="document.getElementById('approve-form').submit()">Authorize Requisition</button>
                    <form id="approve-form" action="{{ route('finance.expenses.approve', $expense) }}" method="POST" class="d-none">@csrf</form>
                @endif
                <button onclick="window.print()" class="btn btn-erp-deep rounded-pill px-4 fw-800 shadow-sm">
                    <i class="bi bi-printer-fill me-2"></i>Print Voucher
                </button>
            </div>
        </div>
    </div>

    <div class="premium-document">
        <div class="watermark-status">{{ $expense->status }}</div>

        {{-- Header --}}
        <div class="d-flex justify-content-between align-items-start mb-5 position-relative" style="z-index: 1;">
            <div>
                <div class="logo-text mb-2">NATANEM</div>
                <div class="small fw-900 text-slate-400 uppercase tracking-widest">Engineering & infrastructure</div>
                <div class="small text-muted mt-3 fw-500" style="max-width: 250px;">
                    Headquarters: Bole, Addis Ababa<br>
                    finance@natanemeng.com<br>
                    Internal Corporate Voucher
                </div>
            </div>
            <div class="text-end">
                <div class="voucher-header-title mb-2">EXPENSE VOUCHER</div>
                <div class="d-flex justify-content-end gap-2">
                    <span class="badge bg-light text-dark border fw-800 rounded-px px-2 py-1 x-small">ID: #{{ str_pad($expense->id, 5, '0', STR_PAD_LEFT) }}</span>
                    <span class="badge bg-dark text-white fw-800 rounded-px px-2 py-1 x-small">REF: {{ $expense->reference_no ?: 'GEN-'.$expense->id }}</span>
                </div>
            </div>
        </div>

        {{-- Core Summary --}}
        <div class="info-section position-relative" style="z-index: 1;">
            <div class="row g-4 align-items-end">
                <div class="col-4">
                    <div class="info-item-label">Issuance Date</div>
                    <div class="info-item-value fw-900">{{ $expense->expense_date->format('M d, Y') }}</div>
                </div>
                <div class="col-4 text-center">
                    <div class="info-item-label">Authorization Status</div>
                    <div class="badge {{ $expense->status === 'approved' ? 'bg-success' : 'bg-warning' }} text-white text-uppercase fw-900 px-3 py-2 rounded-pill shadow-sm" style="font-size: 0.7rem;">
                        {{ $expense->status }}
                    </div>
                </div>
                <div class="col-4 text-end">
                    <div class="info-item-label">Total Requisition Value</div>
                    <div class="amount-display">
                        <small class="fs-4 fw-700 text-muted">ETB</small> {{ number_format($expense->amount, 0) }}
                    </div>
                </div>
            </div>
        </div>

        {{-- Contextual Details --}}
        <div class="row g-4 mb-5 position-relative" style="z-index: 1;">
            <div class="col-6">
                <div class="p-4 bg-light border-start border-5 border-dark h-100 rounded-end-4 shadow-sm">
                    <div class="info-item-label">Applicable Project Site</div>
                    <div class="info-item-value fs-5 fw-900">{{ $expense->project->name ?? 'Global Operations' }}</div>
                    <div class="small text-muted mt-2 fw-700">{{ $expense->project->location ?? 'Head Office' }}</div>
                </div>
            </div>
            <div class="col-6">
                <div class="p-4 bg-light border-start border-5 border-dark h-100 rounded-end-4 shadow-sm">
                    <div class="info-item-label">Originating Requester</div>
                    <div class="info-item-value fs-5 fw-900">{{ $expense->user->name ?? 'System User' }}</div>
                    <div class="small text-muted mt-2 fw-700">Digital Identity: {{ $expense->user->id ?? '---' }}</div>
                </div>
            </div>
        </div>

        {{-- Description Table --}}
        <table class="official-table position-relative" style="z-index: 1;">
            <thead>
                <tr>
                    <th class="ps-3">Expenditure Classification & Narration</th>
                    <th class="text-end pe-3">Subtotal</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td class="ps-3">
                        <div class="fw-900 text-erp-deep fs-5 mb-2">{{ strtoupper($expense->category) }} SERVICES/SUPPLIES</div>
                        <p class="text-muted fw-500 lh-lg" style="max-width: 90%; font-size: 1.1rem;">
                            {{ $expense->description ?: 'Operational expenditure for field activities as per site requirements.' }}
                        </p>
                    </td>
                    <td class="text-end pe-3">
                        <div class="fw-900 fs-4 text-erp-deep">
                            <small class="fs-6 opacity-50 fw-700">ETB</small> {{ number_format($expense->amount, 0) }}
                        </div>
                    </td>
                </tr>
                <tr style="background: var(--bg-soft);">
                    <td class="text-end py-3 fw-900 text-muted ps-3">TOTAL REQUISITION AMOUNT</td>
                    <td class="text-end py-3 pe-3">
                        <div class="fw-900 fs-2 text-erp-deep" style="border-bottom: 4px double #000; display: inline-block;">
                            <small class="fs-5 opacity-50 fw-700">ETB</small> {{ number_format($expense->amount, 0) }}
                        </div>
                    </td>
                </tr>
            </tbody>
        </table>

        {{-- Authentication --}}
        <div class="row g-5 mt-auto pt-5 position-relative" style="z-index: 1;">
            <div class="col-4">
                <div class="signature-box">
                    <div class="fw-900 text-dark small mb-1">{{ $expense->user->name ?? 'Staff' }}</div>
                    <div class="info-item-label" style="font-size: 0.6rem;">Prepared & Requested By</div>
                </div>
            </div>
            <div class="col-4">
                <div class="signature-box">
                    <div class="fw-900 text-muted opacity-50 small mb-1">STAMP & SIGN REQUIRED</div>
                    <div class="info-item-label" style="font-size: 0.6rem;">Verified By (Site Lead)</div>
                </div>
            </div>
            <div class="col-4">
                <div class="signature-box position-relative">
                    @if($expense->status === 'approved')
                        <div class="badge bg-success text-white position-absolute" style="top: -45px; left: 50%; transform: translateX(-50%) rotate(-5deg); font-weight: 900; box-shadow: 0 4px 10px rgba(16, 185, 129, 0.3); border: 2px solid white;">SECURELY AUTHORIZED</div>
                    @endif
                    <div class="fw-900 text-dark small mb-1">{{ $expense->approvedBy->name ?? '---' }}</div>
                    <div class="info-item-label" style="font-size: 0.6rem;">Global Finance Authorization</div>
                </div>
            </div>
        </div>

        <div class="text-center mt-5 pt-5 opacity-50 position-relative" style="z-index: 1;">
            <div class="fw-900 fs-5 text-slate-900" style="letter-spacing: -1px;">NATANEM ENGINEERING GROUP</div>
            <div class="x-small fw-800 uppercase tracking-widest mt-1">Certified Corporate Voucher • Proprietary Document</div>
            <div class="mt-3 x-small fw-bold text-muted">Checksum: {{ strtoupper(substr(md5($expense->id . $expense->amount), 0, 16)) }}</div>
        </div>
    </div>
</div>
@endsection
