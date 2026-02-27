@extends('layouts.app')
@section('title', 'Asset Lending Registry | Natanem Engineering')

@section('content')
@push('head')
<link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
<style>
    :root {
        --premium-green: #064e43;
        --accent-teal: #0d9488;
        --soft-green-bg: #f2fdfb;
        --border-radius-xl: 44px;
        --glass-bg: rgba(255, 255, 255, 0.85);
    }

    /* Supreme Atmosphere */
    body { 
        font-family: 'Outfit', sans-serif; 
        background: radial-gradient(at top right, #f2fdfb, #ffffff), radial-gradient(at bottom left, #f1f5f9, #ffffff);
        background-attachment: fixed;
        color: #1a202c;
    }

    .premium-header-title { 
        font-weight: 900; 
        background: linear-gradient(135deg, #064e43 0%, #0d9488 100%);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        letter-spacing: -2.5px; 
    }

    /* Layered Float Effect */
    .premium-card {
        background: var(--glass-bg);
        backdrop-filter: blur(20px);
        border-radius: var(--border-radius-xl);
        border: 1px solid rgba(255,255,255,0.6);
        box-shadow: 
            0 10px 15px -3px rgba(6, 78, 67, 0.02),
            0 25px 50px -12px rgba(6, 78, 67, 0.05),
            inset 0 2px 4px 0 rgba(255, 255, 255, 0.05);
        padding: 50px;
    }

    .section-label {
        font-weight: 800;
        font-size: 0.7rem;
        color: #64748b;
        text-transform: uppercase;
        letter-spacing: 0.15em;
        margin-bottom: 1.25rem;
        display: block;
        opacity: 0.8;
    }

    /* Command Bar Hub */
    .search-input-premium {
        border: 1.5px solid #edf2f7;
        border-radius: 100px;
        padding: 16px 28px;
        transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        background: #ffffff;
        font-weight: 500;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.02);
    }

    .search-input-premium:focus {
        border-color: var(--premium-green);
        box-shadow: 0 0 0 5px rgba(6, 78, 67, 0.08);
        outline: none;
    }

    .btn-premium-main {
        background: linear-gradient(135deg, #064e43 0%, #043831 100%);
        color: white;
        border-radius: 100px;
        font-weight: 700;
        padding: 14px 40px;
        border: none;
        transition: all 0.3s cubic-bezier(0.34, 1.56, 0.64, 1);
        box-shadow: 0 10px 25px -5px rgba(6, 78, 67, 0.3);
    }

    .btn-premium-main:hover { 
        transform: translateY(-3px) scale(1.03); 
        box-shadow: 0 15px 30px -5px rgba(6, 78, 67, 0.4);
        color: white;
    }

    /* Ultra-Refined Table */
    .premium-table tbody tr {
        transition: all 0.3s ease;
        position: relative;
    }

    .premium-table tbody tr:hover {
        background-color: #f8fafc;
        transform: scale(1.005);
        z-index: 10;
        box-shadow: 0 10px 30px -15px rgba(0,0,0,0.1);
    }

    .asset-title { 
        font-weight: 800; 
        color: #0f172a; 
        font-size: 1.05rem; 
        letter-spacing: -0.3px;
    }

    .unit-pill { 
        background: #f1f5f9; 
        color: #475569; 
        padding: 3px 12px; 
        border-radius: 8px; 
        font-size: 0.65rem; 
        font-weight: 900;
        border: 1px solid #e2e8f0;
    }

    /* Glow Status */
    .status-text {
        font-weight: 800;
        font-size: 0.75rem;
        padding: 6px 14px;
        border-radius: 100px;
        display: inline-flex;
        align-items: center;
    }

    .status-active { 
        background: #ecfdf5; 
        color: #065f46; 
        box-shadow: 0 0 15px rgba(16, 185, 129, 0.1);
    }

    .status-returned { 
        background: #f1f5f9; 
        color: #64748b; 
    }

    .loan-progress-bar {
        height: 6px;
        background: #f1f5f9;
        border-radius: 100px;
        margin-top: 8px;
        overflow: hidden;
        width: 120px;
    }

    .loan-progress-fill {
        height: 100%;
        background: linear-gradient(90deg, #064e43, #0d9488);
        border-radius: 100px;
    }
    .btn-premium-return {
        background: white;
        border: 1.5px solid #edf2f7;
        border-radius: 100px;
        color: var(--premium-green);
        font-weight: 700;
        padding: 10px 24px;
        font-size: 0.85rem;
        transition: all 0.3s ease;
        box-shadow: 0 2px 5px rgba(0,0,0,0.02);
    }

    .btn-premium-return:hover {
        border-color: var(--premium-green);
        background: var(--soft-green-bg);
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(6, 78, 67, 0.05);
    }

    .btn-reset-circle {
        width: 58px;
        height: 58px;
        border: 2px solid #e2e8f0;
        border-radius: 50%;
        background: white;
        color: #064e43;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        box-shadow: 0 4px 12px rgba(0,0,0,0.08);
        text-decoration: none;
    }

    .btn-reset-circle:hover {
        border-color: #064e43;
        background: #064e43;
        color: white;
        transform: rotate(180deg) scale(1.1);
        box-shadow: 0 12px 20px rgba(6, 78, 67, 0.2);
    }
</style>
@endpush

<div class="container py-5 stagger-entrance">
    {{-- High-End Header --}}
    <div class="row align-items-center mb-5 gx-4">
        <div class="col">
            <h1 class="premium-header-title mb-1 display-5">Tool & Equipment Loans</h1>
            <p class="text-slate-400 fw-600 fs-5 mb-0">Manage item assignments and site equipment tracking.</p>
        </div>
        <div class="col-auto">
            <a href="{{ route('inventory.loans.create') }}" class="btn btn-premium-main">
                <i class="bi bi-plus-circle me-2"></i>New Loan
            </a>
        </div>
    </div>

    {{-- Registry Hub --}}
    <div class="premium-card">
        {{-- Filters Hub --}}
        <div class="mb-5 px-3">
            <form action="{{ route('inventory.loans.index') }}" method="GET">
                <div class="row g-4 align-items-end">
                    <div class="col-lg-5 col-md-6">
                        <label class="section-label ps-2">Search Records</label>
                        <div class="position-relative">
                            <i class="bi bi-search position-absolute top-50 start-0 translate-middle-y ms-4 text-muted opacity-50"></i>
                            <input type="text" name="q" class="form-control search-input-premium ps-5" 
                                   placeholder="Search employee or item name..." 
                                   value="{{ request('q') }}">
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-4">
                        <label class="section-label ps-2">Loan Status</label>
                        <select name="status" class="form-select filter-select-premium shadow-sm text-muted">
                            <option value="">All Status</option>
                            <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending Approval</option>
                            <option value="approved" {{ request('status') === 'approved' ? 'selected' : '' }}>Active Loan</option>
                            <option value="returned" {{ request('status') === 'returned' ? 'selected' : '' }}>Returned / Closed</option>
                            <option value="rejected" {{ request('status') === 'rejected' ? 'selected' : '' }}>Rejected</option>
                        </select>
                    </div>
                    <div class="col-lg-4 col-md-2 d-flex gap-3">
                        <button type="submit" class="btn btn-premium-main flex-grow-1 shadow-lg py-3">
                            Search
                        </button>
                        <a href="{{ route('inventory.loans.index') }}" class="btn-reset-circle" title="Reset Filters">
                            <i class="bi bi-arrow-repeat fs-3"></i>
                        </a>
                    </div>
                </div>
            </form>
        </div>

        <div class="table-responsive">
            <table class="table premium-table align-middle">
                <thead>
                    <tr>
                        <th style="width: 35%;" class="ps-4">Item Details</th>
                        <th style="width: 25%;">Borrowed By</th>
                        <th style="width: 15%;">Status</th>
                        <th style="width: 15%;">Loan Period</th>
                        <th class="text-end ps-4" style="width: 10%;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($loans as $loan)
                        <tr>
                            <td class="ps-4">
                                <div class="asset-title text-truncate" style="max-width: 320px;">{{ optional($loan->item)->name ?? 'Unknown Item' }}</div>
                                <div class="asset-meta d-flex align-items-center gap-2">
                                    <span class="text-slate-400 font-monospace">{{ optional($loan->item)->item_no ?? 'SN-XXX' }}</span>
                                    <span class="unit-pill">{{ $loan->quantity }} Units</span>
                                </div>
                            </td>
                            <td>
                                <div class="fw-800 text-slate-800 fs-6">{{ optional($loan->employee)->name ?? 'Employee' }}</div>
                                <div class="x-small text-slate-400 fw-700 uppercase tracking-wider">{{ strtoupper(optional(optional($loan->employee)->position_rel)->title ?? 'STAFF') }}</div>
                            </td>
                            <td>
                                @if($loan->status === 'approved')
                                    <div class="status-text status-active">
                                        <i class="bi bi-lightning-charge-fill me-2"></i>Active Loan
                                    </div>
                                    @php
                                        $requested = $loan->requested_at ?? now();
                                        $due = $loan->expected_return_date ?? now()->addDays(7);
                                        $total = $due->diffInDays($requested);
                                        $elapsed = now()->diffInDays($requested);
                                        $percent = $total > 0 ? min(100, ($elapsed / $total) * 100) : 0;
                                    @endphp
                                    <div class="loan-progress-bar">
                                        <div class="loan-progress-fill" style="width: {{ $percent }}%"></div>
                                    </div>
                                @elseif($loan->status === 'returned')
                                    <div class="status-text status-returned">
                                        <i class="bi bi-check-circle-fill me-2"></i>Returned
                                    </div>
                                @elseif($loan->status === 'pending')
                                    <div class="status-text text-warning bg-warning-soft px-3">
                                        <i class="bi bi-hourglass-split me-2"></i>Pending Review
                                    </div>
                                @else
                                    <div class="status-text text-danger bg-danger-soft px-3">
                                        {{ strtoupper($loan->status) }}
                                    </div>
                                @endif
                            </td>
                            <td>
                                <div class="d-flex flex-column gap-1">
                                    <div>
                                        <span class="timeline-label">Issued:</span> 
                                        <span class="timeline-date"> {{ optional($loan->requested_at)->format('d M') }}</span>
                                    </div>
                                    @if($loan->status === 'returned')
                                        <div>
                                            <span class="timeline-label text-success-soft">Closed:</span> 
                                            <span class="timeline-date text-success"> {{ optional($loan->returned_at)->format('d M') }}</span>
                                        </div>
                                    @elseif($loan->expected_return_date)
                                        <div>
                                            <span class="timeline-label">Due:</span> 
                                            <span class="timeline-date {{ $loan->expected_return_date->isPast() && $loan->status == 'approved' ? 'text-danger' : '' }}">
                                                {{ $loan->expected_return_date->format('d M') }}
                                            </span>
                                        </div>
                                    @endif
                                </div>
                            </td>
                            <td class="text-end">
                                <div class="d-flex justify-content-end gap-2 pe-3">
                                    @if($loan->status === 'approved')
                                        <button type="button" class="btn btn-premium-return px-4" 
                                                onclick="if(confirm('Acknowledge item return?')) document.getElementById('ret-{{ $loan->id }}').submit()">
                                            Acknowledge
                                            <form id="ret-{{ $loan->id }}" action="{{ route('inventory.loans.mark-returned', $loan) }}" method="POST" class="d-none">@csrf</form>
                                        </button>
                                    @elseif($loan->status === 'pending')
                                        <a href="{{ route('inventory.loans.edit', $loan) }}" class="p-2 text-slate-300 hover-teal">
                                            <i class="bi bi-pencil-square fs-5"></i>
                                        </a>
                                    @else
                                        <a href="{{ route('inventory.loans.show', $loan) }}" class="p-2 text-slate-300">
                                            <i class="bi bi-shield-lock fs-5"></i>
                                        </a>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center py-5">
                                <div class="p-5">
                                    <div class="display-1 text-slate-100 mb-4 fw-900 opacity-25">NULL</div>
                                    <div class="text-slate-400 fw-800 fs-4">Registry Repository Empty</div>
                                    <div class="text-slate-300 fw-500 x-small tracking-widest uppercase mt-2">No active logistics assets detected</div>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($loans->hasPages())
            <div class="pt-5 pb-2">
                {{ $loans->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
