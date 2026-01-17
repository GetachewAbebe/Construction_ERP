@extends('layouts.app')
@section('title', 'Financial Voucher | Natanem')

@section('content')
@push('head')
<link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700;900&family=Outfit:wght@300;400;600;700;800;900&display=swap" rel="stylesheet">
<style>
    :root {
        --erp-deep: #0f172a;
        --emerald-primary: #10b981;
        --rose-primary: #f43f5e;
    }

    /* Professional A4 Constraints */
    .premium-document {
        background: white;
        width: 210mm;
        min-height: 297mm;
        margin: 0 auto;
        padding: 12mm !important;
        position: relative;
        font-family: 'Outfit', sans-serif;
        color: #1e293b;
        display: flex;
        flex-direction: column;
    }

    @media screen {
        .premium-document-wrapper {
            background: #f1f5f9;
            padding: 2rem;
            border-radius: 20px;
        }
        .premium-document {
            box-shadow: 0 20px 50px rgba(0,0,0,0.1);
            border-radius: 8px;
        }
    }

    .brand-accent {
        font-family: 'Playfair Display', serif;
        color: var(--erp-deep);
        font-weight: 900;
        letter-spacing: -1px;
    }

    .voucher-title {
        font-weight: 900;
        font-size: 1.8rem;
        letter-spacing: -1px;
        color: #0f172a;
        line-height: 1;
    }

    .compact-table th {
        background: #0f172a !important;
        color: white !important;
        text-transform: uppercase;
        font-size: 0.75rem;
        letter-spacing: 0.05em;
        padding: 10px 15px !important;
    }

    .compact-table td {
        padding: 15px !important;
        border-color: #f1f5f9 !important;
    }

    .signature-area {
        margin-top: auto;
        border-top: 2px solid #0f172a;
        padding-top: 20px;
    }

    .sig-box {
        border: 1px dashed #cbd5e1;
        background: #f8fafc;
        height: 60px;
        margin-bottom: 8px;
        position: relative;
    }

    .stamp-overlay {
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%) rotate(-10deg);
        border: 2px solid var(--emerald-primary);
        color: var(--emerald-primary);
        font-weight: 900;
        padding: 2px 8px;
        font-size: 0.7rem;
        text-transform: uppercase;
        border-radius: 4px;
        opacity: 0.7;
    }

    @media print {
        @page { size: A4; margin: 0; }
        body { background: white !important; }
        .no-print, .main-sidebar, .main-navbar, .sidebar, .navbar { display: none !important; }
        .premium-document-wrapper { padding: 0 !important; }
        .premium-document {
            width: 100%;
            height: 297mm;
            padding: 10mm !important;
            box-shadow: none !important;
            margin: 0 !important;
        }
    }
</style>
@endpush

<div class="container-fluid py-4 stagger-entrance">
    {{-- Control Panel --}}
    <div class="d-flex justify-content-between align-items-center mb-4 d-print-none bg-white p-3 rounded-4 shadow-sm">
        <a href="{{ route('finance.expenses.index') }}" class="btn btn-outline-secondary border-0 fw-bold">
            <i class="bi bi-arrow-left me-2"></i>Back to Ledger
        </a>
        <div class="d-flex gap-2">
            @if(Auth::user()->hasAnyRole(['Administrator', 'FinancialManager', 'Financial Manager']) && $expense->status === 'pending')
                <button type="button" class="btn btn-emerald rounded-pill px-4 fw-800" onclick="if(confirm('Approve?')) document.getElementById('approve-form').submit()">Approve</button>
                <form id="approve-form" action="{{ route('finance.expenses.approve', $expense) }}" method="POST" class="d-none">@csrf</form>
            @endif
            <button onclick="window.print()" class="btn btn-erp-deep rounded-pill px-4 fw-900 shadow-lg border-0">
                <i class="bi bi-printer-fill me-2"></i>PRINT VOUCHER
            </button>
        </div>
    </div>

    <div class="premium-document-wrapper">
        <div class="premium-document">
            {{-- Header --}}
            <div class="row align-items-center mb-4 pb-3 border-bottom border-2">
                <div class="col-6">
                    <div class="brand-accent fs-2 lh-1 text-erp-deep">NATANEM</div>
                    <div class="small fw-800 text-slate-400 uppercase tracking-widest">Engineering & Civil Solutions</div>
                    <div class="x-small text-slate-500 mt-1">Bole Road, Addis Ababa | +251 911 000 000</div>
                </div>
                <div class="col-6 text-end">
                    <div class="voucher-title mb-1">FINANCIAL VOUCHER</div>
                    <div class="d-flex justify-content-end gap-2">
                        <span class="badge bg-slate-100 text-slate-600 rounded-px px-2 py-1 fw-800 border-0">REQ: #{{ str_pad($expense->id, 5, '0', STR_PAD_LEFT) }}</span>
                        <span class="badge bg-slate-900 text-white rounded-px px-2 py-1 fw-800 border-0">REF: {{ $expense->reference_no ?? 'EXP-'.$expense->id }}</span>
                    </div>
                </div>
            </div>

            {{-- Summary Bar --}}
            <div class="row g-0 bg-slate-900 rounded-3 mb-4 text-white overflow-hidden shadow-sm">
                <div class="col-3 p-3 border-end border-slate-700">
                    <div class="x-small fw-700 opacity-50 uppercase mb-1">Transaction Date</div>
                    <div class="fw-800">{{ $expense->expense_date->format('d M, Y') }}</div>
                </div>
                <div class="col-3 p-3 border-end border-slate-700">
                    <div class="x-small fw-700 opacity-50 uppercase mb-1">Payment Category</div>
                    <div class="fw-800 text-truncate">{{ strtoupper($expense->category) }}</div>
                </div>
                <div class="col-6 p-3 text-end bg-slate-800">
                    <div class="x-small fw-700 opacity-50 uppercase mb-1">Total Authorized Amount</div>
                    <div class="fw-900 fs-3 lh-1">
                        <span class="fs-6 fw-400 opacity-50">ETB</span> {{ number_format($expense->amount, 2) }}
                    </div>
                </div>
            </div>

            {{-- Participant Grid --}}
            <div class="row g-3 mb-4">
                <div class="col-6">
                    <div class="p-3 bg-slate-50 rounded-3 border h-100">
                        <div class="x-small fw-800 text-slate-400 uppercase tracking-wider mb-2">Project Assignment</div>
                        <div class="fw-900 text-slate-800">{{ optional($expense->project)->name ?? 'GENERAL OPERATIONS' }}</div>
                        <div class="small text-slate-500">{{ optional($expense->project)->location ?? 'Addis Ababa' }}</div>
                    </div>
                </div>
                <div class="col-6">
                    <div class="p-3 bg-slate-50 rounded-3 border h-100">
                        <div class="x-small fw-800 text-slate-400 uppercase tracking-wider mb-2">Beneficiary / Requester</div>
                        <div class="fw-900 text-slate-800">{{ optional($expense->user)->name ?? 'System' }}</div>
                        <div class="small text-slate-500">{{ optional($expense->user)->position ?? 'Operations' }}</div>
                    </div>
                </div>
            </div>

            {{-- Particulars Table --}}
            <div class="mb-4">
                <div class="table-responsive border rounded-3">
                    <table class="table table-hover compact-table mb-0">
                        <thead>
                            <tr>
                                <th>Description & Narrative of Expenditure</th>
                                <th class="text-end" style="width: 150px;">Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td class="py-4">
                                    <div class="fw-800 text-slate-900 mb-1">Payment for {{ $expense->category }}</div>
                                    <p class="small text-slate-600 mb-0 lh-base">{{ $expense->description ?? 'No specific narrative provided.' }}</p>
                                </td>
                                <td class="text-end fw-900 fs-5 text-slate-800">{{ number_format($expense->amount, 2) }}</td>
                            </tr>
                        </tbody>
                        <tfoot class="bg-slate-50 border-top">
                            <tr class="fw-900 text-erp-deep">
                                <td class="text-end py-3">GRAND TOTAL (ETB)</td>
                                <td class="text-end py-3 pe-3 fs-4">{{ number_format($expense->amount, 2) }}</td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>

            {{-- Compliance Section --}}
            <div class="row g-3 mb-4">
                <div class="col-8">
                    <div class="p-3 rounded-3 border h-100">
                        <div class="x-small fw-800 text-slate-400 uppercase mb-2">Project Budget Compliance</div>
                        <div class="row text-center x-small fw-800">
                            <div class="col-4 border-end">BUDGET: ETB {{ number_format($expense->project->budget, 2) }}</div>
                            <div class="col-4 border-end">USED: ETB {{ number_format($expense->project->total_expenses, 2) }}</div>
                            <div class="col-4 text-emerald-600">BAL: ETB {{ number_format($expense->project->budget - $expense->project->total_expenses, 2) }}</div>
                        </div>
                    </div>
                </div>
                <div class="col-4">
                    <div class="p-3 rounded-3 border h-100 text-center d-flex flex-column justify-content-center {{ $expense->status === 'approved' ? 'bg-emerald text-white' : 'bg-slate-100' }}">
                        <div class="fw-900 x-small uppercase mb-1">Verification Status</div>
                        <div class="fw-900 fs-5 tracking-tighter">{{ strtoupper($expense->status ?? 'pending') }}</div>
                    </div>
                </div>
            </div>

            {{-- Signatures --}}
            <div class="signature-area mt-auto">
                <div class="row g-4 text-center">
                    <div class="col-4">
                        <div class="sig-box"></div>
                        <div class="fw-900 text-erp-deep small">{{ optional($expense->user)->name }}</div>
                        <div class="x-small fw-800 text-slate-400 uppercase">Prepared By</div>
                    </div>
                    <div class="col-4">
                        <div class="sig-box"></div>
                        <div class="fw-900 text-erp-deep small">- Site Office -</div>
                        <div class="x-small fw-800 text-slate-400 uppercase">Verified By (PM)</div>
                    </div>
                    <div class="col-4">
                        <div class="sig-box">
                            @if($expense->status === 'approved')
                                <div class="stamp-overlay">APPROVED DIGITAL</div>
                            @endif
                        </div>
                        <div class="fw-900 text-erp-deep small">{{ optional($expense->approvedBy)->name ?? 'Financial Officer' }}</div>
                        <div class="x-small fw-800 text-slate-400 uppercase">Authorized Official</div>
                    </div>
                </div>
            </div>

            {{-- Footer --}}
            <div class="text-center mt-5 pt-3 opacity-50 x-small">
                <div class="fw-900 text-erp-deep">NATANEM ENGINEERING PLC - ERP SYSTEM VOUCHER</div>
                <div class="mt-1 font-monospace">VERIFICATION CODE: {{ strtoupper(substr(hash('sha256', $expense->id), 0, 12)) }} | PAGE 01/01</div>
            </div>
        </div>
    </div>
</div>

<style>
.bg-slate-50 { background-color: #f8fafc; }
.bg-slate-900 { background-color: #0f172a; }
.bg-slate-800 { background-color: #1e293b; }
.bg-slate-700 { background-color: #334155; }
.bg-emerald { background-color: #10b981; }
.text-emerald-600 { color: #059669; }
.x-small { font-size: 0.65rem; }
.rounded-px { border-radius: 4px; }
.lh-1 { line-height: 1; }
.fw-1000 { font-weight: 1000; }
</style>
@endsection
