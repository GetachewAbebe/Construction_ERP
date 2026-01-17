@extends('layouts.app')
@section('title', 'Item Lending Authorization | Natanem')

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
        display: flex;
        flex-direction: column;
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

    .official-table { width: 100%; border-collapse: collapse; margin-bottom: 2rem; }
    .official-table th { text-align: left; font-size: 0.75rem; font-weight: 800; color: var(--slate-600); text-transform: uppercase; padding: 12px 15px; border-bottom: 2px solid var(--slate-900); background: var(--bg-soft); }
    .official-table td { padding: 20px 15px; border-bottom: 1px solid var(--border-color); vertical-align: top; }

    .auth-stamp {
        border: 3px solid #10b981;
        color: #10b981;
        padding: 5px 15px;
        font-weight: 950;
        font-size: 0.8rem;
        text-transform: uppercase;
        transform: rotate(-8deg);
        display: inline-block;
        border-radius: 4px;
        opacity: 0.8;
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
    <div class="container-fluid no-print mb-4">
        <div class="d-flex justify-content-between align-items-center bg-white p-3 rounded-3 shadow-sm" style="max-width: 210mm; margin: 0 auto;">
            <a href="{{ route('inventory.loans.index') }}" class="btn btn-link text-decoration-none text-muted fw-bold">
                <i class="bi bi-chevron-left"></i> Back to Inventory Ledger
            </a>
            <div class="d-flex gap-2">
                @if($loan->status === 'approved' && !$loan->returned_at)
                    <form action="{{ route('inventory.loans.mark-returned', $loan) }}" method="POST">
                        @csrf
                        <button type="submit" class="btn btn-success rounded-pill px-4 fw-bold shadow-sm">Mark Returned</button>
                    </form>
                @endif
                <button onclick="window.print()" class="btn btn-dark rounded-pill px-4 fw-bold">
                    <i class="bi bi-printer-fill me-2"></i>Print Official Voucher
                </button>
            </div>
        </div>
    </div>

    <div class="premium-document">
        <div class="d-flex justify-content-between align-items-start mb-5">
            <div>
                <div class="logo-text mb-1">NATANEM</div>
                <div class="small fw-800 text-slate-600 uppercase tracking-widest mb-2">Inventory & Assets Management</div>
            </div>
            <div class="text-end">
                <div class="voucher-header-title mb-2">LENDING AUTHORIZATION</div>
                <div class="badge bg-dark text-white rounded-px px-3 py-2 fw-800">RECORD ID #LND-{{ str_pad($loan->id, 5, '0', STR_PAD_LEFT) }}</div>
            </div>
        </div>

        <div class="info-section">
            <div class="row g-4">
                <div class="col-6">
                    <div class="info-item-label">Accountable Custodian</div>
                    <div class="info-item-value fs-5">{{ $loan->employee->name }}</div>
                    <div class="small text-slate-400 fw-700 mt-1">{{ strtoupper($loan->employee->position ?? 'STAFF') }} — {{ strtoupper($loan->employee->department ?? 'OPERATIONS') }}</div>
                </div>
                <div class="col-6 text-end">
                    <div class="info-item-label">Lending Status</div>
                    <div class="fw-900 fs-3 text-{{ $loan->status === 'returned' ? 'info' : ($loan->status === 'approved' ? 'success' : 'warning') }}">
                        {{ strtoupper($loan->status) }}
                    </div>
                    @if($loan->returned_at)
                        <div class="x-small fw-800 text-info uppercase">Returned on: {{ $loan->returned_at->format('d M, Y') }}</div>
                    @endif
                </div>
            </div>
        </div>

        <h5 class="fw-900 text-slate-900 mb-4 border-start border-4 border-dark ps-3">Asset Distribution Particulars</h5>
        <table class="official-table mb-5">
            <thead>
                <tr>
                    <th>Item Specification & Serial</th>
                    <th class="text-center">Quantity</th>
                    <th class="text-end">Due Date</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td class="py-4">
                        <div class="fw-900 text-dark fs-5">{{ $loan->item->name }}</div>
                        <div class="small text-slate-400 fw-800">CATEGORY: {{ strtoupper($loan->item->category ?? 'General Asset') }} | NO: {{ $loan->item->item_no ?? 'N/A' }}</div>
                        <div class="x-small text-slate-400 mt-2">STORE LOCATION: {{ $loan->item->store_location ?? 'Main Warehouse' }}</div>
                    </td>
                    <td class="text-center py-4">
                        <div class="fw-900 fs-4">{{ $loan->quantity }}</div>
                        <div class="x-small fw-800 text-slate-400 uppercase">{{ $loan->item->unit_of_measurement ?? 'Units' }}</div>
                    </td>
                    <td class="text-end py-4">
                        <div class="fw-900 text-slate-900 fs-5">{{ optional($loan->due_date)->format('d M, Y') ?? 'PERMANENT' }}</div>
                        <div class="x-small fw-800 text-slate-400 uppercase">Authorized Return</div>
                    </td>
                </tr>
            </tbody>
        </table>

        @if($loan->notes)
        <div class="mb-5">
            <h5 class="fw-900 text-slate-900 mb-3 border-start border-4 border-dark ps-3">Notes & Operational Context</h5>
            <div class="p-4 bg-light border-start border-4 border-dark rounded-end shadow-sm">
                <p class="mb-0 text-slate-700 fw-600 small">{{ $loan->notes }}</p>
            </div>
        </div>
        @endif

        <div class="row mt-auto pt-5 border-top border-4 border-dark">
            <div class="col-4">
                <div class="border-top border-dark mx-auto mb-2 text-center" style="width: 150px;"></div>
                <div class="text-center small fw-800 text-slate-400 uppercase">Custodian Acknowledgement</div>
                <div class="text-center fw-900 small text-slate-900 mt-1">{{ $loan->employee->name }}</div>
            </div>
            <div class="col-4">
                <div class="border-top border-dark mx-auto mb-2 text-center" style="width: 150px;"></div>
                <div class="text-center small fw-800 text-slate-400 uppercase">Departmental Verification</div>
            </div>
            <div class="col-4 text-center">
                @if($loan->status === 'approved' || $loan->status === 'returned')
                    <div class="auth-stamp mb-2">OFFICIALLY DISBURSED</div>
                @endif
                <div class="border-top border-dark mx-auto mb-2" style="width: 150px;"></div>
                <div class="small fw-800 text-slate-400 uppercase">Inventory Manager</div>
                <div class="fw-900 small text-slate-900 mt-1">{{ optional($loan->approvedBy)->name ?? 'Authorized Official' }}</div>
            </div>
        </div>

        <div class="text-center mt-5 pt-3 opacity-25">
            <div class="fw-900 fs-5 text-slate-900">NATANEM ENGINEERING ASSET LOGISTICS</div>
            <div class="x-small fw-700 uppercase tracking-widest mt-1">Official Asset Disbursement Voucher - {{ date('Y') }}</div>
        </div>
    </div>
</div>
@endsection
