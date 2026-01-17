@extends('layouts.app')
@section('title', 'Asset Lending Registry | Natanem Engineering')

@section('content')
@push('head')
<link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
<style>
    :root {
        --premium-green: #064e43;
        --soft-green-bg: #f2fdfb;
        --border-radius-xl: 40px;
        --border-radius-lg: 24px;
    }

    body { font-family: 'Outfit', sans-serif; background-color: #f7faf9; color: #1e293b; }

    .premium-header-title { font-weight: 900; color: #064e43; letter-spacing: -1.5px; }

    .premium-card {
        background: white;
        border-radius: var(--border-radius-xl);
        border: none;
        box-shadow: 0 50px 100px -20px rgba(6, 78, 67, 0.04);
        padding: 40px;
    }

    .section-label {
        font-weight: 800;
        font-size: 0.75rem;
        color: #064e43;
        text-transform: uppercase;
        letter-spacing: 0.1em;
        margin-bottom: 1rem;
        display: block;
    }

    .search-input-premium {
        border: 1px solid #e2e8f0;
        border-radius: 100px;
        padding: 18px 30px;
        transition: all 0.3s ease;
        background: white;
        font-weight: 500;
        color: #1e293b;
    }

    .search-input-premium::placeholder { color: #94a3b8; font-weight: 400; }

    .filter-select-premium {
        border: 1px solid #e2e8f0;
        border-radius: 100px;
        padding: 18px 30px;
        background-color: white;
        font-weight: 500;
        color: #1e293b;
        appearance: none;
        background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 16 16'%3e%3cpath fill='none' stroke='%23064e43' stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='m2 5 6 6 6-6'/%3e%3c/svg%3e");
        background-repeat: no-repeat;
        background-position: right 1.5rem center;
        background-size: 14px 10px;
    }

    .btn-premium-main {
        background: var(--premium-green);
        color: white;
        border-radius: 100px;
        font-weight: 700;
        padding: 14px 35px;
        border: none;
        transition: all 0.2s ease;
        font-size: 0.9rem;
    }

    .btn-premium-main:hover { background: #043831; color: white; transform: scale(1.02); }

    .btn-premium-return {
        background: white;
        border: 1px solid #e2e8f0;
        border-radius: 100px;
        color: #064e43;
        font-weight: 700;
        padding: 10px 30px;
        font-size: 0.85rem;
        transition: all 0.2s ease;
    }

    .btn-premium-return:hover { border-color: #064e43; background: var(--soft-green-bg); }

    .premium-table thead th {
        font-weight: 800;
        color: #1a202c;
        font-size: 0.85rem;
        padding: 20px 0;
        border-bottom: 2px solid #f1f5f9;
        background: white;
    }

    .premium-table tbody td {
        padding: 30px 0;
        border-bottom: 1px solid #f1f5f9;
        background: white;
    }

    .asset-title { font-weight: 700; color: #064e43; font-size: 1rem; margin-bottom: 2px; }
    .asset-meta { font-weight: 600; color: #718096; font-size: 0.8rem; }
    .unit-pill { background: #edf2f7; color: #2d3748; padding: 2px 10px; border-radius: 6px; font-size: 0.7rem; font-weight: 800; }
    
    .status-text { font-weight: 800; font-size: 0.8rem; }
    .status-active { color: #064e43; }
    .status-returned { color: #718096; }

    .timeline-label { color: #718096; font-size: 0.8rem; font-weight: 500; }
    .timeline-date { color: #1a202c; font-weight: 800; font-size: 0.85rem; }
</style>
@endpush

<div class="container py-5 stagger-entrance">
    {{-- High-End Header --}}
    <div class="row align-items-center mb-5 gx-4">
        <div class="col">
            <h1 class="premium-header-title mb-1 display-5">Asset Lending Registry</h1>
            <p class="text-slate-400 fw-500 fs-5 mb-0">Track tool assignments, material issuance, and return schedules.</p>
        </div>
        <div class="col-auto">
            <a href="{{ route('inventory.loans.create') }}" class="btn btn-premium-main shadow-lg">
                New Loan Request
            </a>
        </div>
    </div>

    {{-- Registry Hub --}}
    <div class="premium-card">
        {{-- Filters --}}
        <div class="mb-5 px-2">
            <form action="{{ route('inventory.loans.index') }}" method="GET">
                <div class="row g-4 align-items-end">
                    <div class="col-lg-5 col-md-6">
                        <label class="section-label">Search Records</label>
                        <input type="text" name="q" class="form-control search-input-premium shadow-sm" 
                               placeholder="Search employee or item name..." 
                               value="{{ request('q') }}">
                    </div>
                    <div class="col-lg-3 col-md-4">
                        <label class="section-label">Loan Status</label>
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
                            Filter
                        </button>
                        <a href="{{ route('inventory.loans.index') }}" class="btn btn-outline-light rounded-circle shadow-sm p-0 d-flex align-items-center justify-content-center border" style="width: 58px; height: 58px;">
                            <i class="bi bi-arrow-counterclockwise fs-5 text-muted"></i>
                        </a>
                    </div>
                </div>
            </form>
        </div>

        <div class="table-responsive">
            <table class="table premium-table align-middle">
                <thead>
                    <tr>
                        <th style="width: 35%;">Asset Details</th>
                        <th style="width: 25%;">Borrower</th>
                        <th style="width: 15%;">Status</th>
                        <th style="width: 15%;">Timeline</th>
                        <th class="text-end" style="width: 10%;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($loans as $loan)
                        <tr>
                            <td>
                                <div class="asset-title text-truncate" style="max-width: 300px;">{{ optional($loan->item)->name ?? 'Unknown Item' }}</div>
                                <div class="asset-meta">
                                    {{ optional($loan->item)->item_no ?? 'SN-XXX' }} 
                                    <span class="unit-pill ms-2">{{ $loan->quantity }} Units</span>
                                </div>
                            </td>
                            <td>
                                <div class="fw-800 text-slate-800 fs-6">{{ optional($loan->employee)->name ?? 'Haileeyesus Ketyibelu' }}</div>
                                <div class="x-small text-slate-400 fw-700">{{ strtoupper(optional(optional($loan->employee)->position_rel)->title ?? 'N/A') }}</div>
                            </td>
                            <td>
                                @if($loan->status === 'approved')
                                    <div class="status-text status-active">Active Loan</div>
                                @elseif($loan->status === 'returned')
                                    <div class="status-text status-returned">Returned</div>
                                @elseif($loan->status === 'pending')
                                    <div class="status-text text-warning">Pending Review</div>
                                @else
                                    <div class="status-text text-danger">{{ strtoupper($loan->status) }}</div>
                                @endif
                            </td>
                            <td>
                                <div class="d-flex flex-column">
                                    <div class="mb-1">
                                        <span class="timeline-label">Requested:</span> 
                                        <span class="timeline-date"> {{ optional($loan->requested_at)->format('M d') }}</span>
                                    </div>
                                    @if($loan->status === 'returned')
                                        <div>
                                            <span class="timeline-label text-success-soft">Returned:</span> 
                                            <span class="timeline-date text-success"> {{ optional($loan->returned_at)->format('M d') }}</span>
                                        </div>
                                    @elseif($loan->expected_return_date)
                                        <div>
                                            <span class="timeline-label">Due:</span> 
                                            <span class="timeline-date ps-1 {{ $loan->expected_return_date->isPast() && $loan->status == 'approved' ? 'text-danger' : '' }}">
                                                {{ $loan->expected_return_date->format('M d') }}
                                            </span>
                                        </div>
                                    @endif
                                </div>
                            </td>
                            <td class="text-end">
                                <div class="d-flex justify-content-end gap-2">
                                    @if($loan->status === 'approved')
                                        <button type="button" class="btn btn-premium-return" 
                                                onclick="if(confirm('Verify item return?')) document.getElementById('ret-{{ $loan->id }}').submit()">
                                            Return
                                            <form id="ret-{{ $loan->id }}" action="{{ route('inventory.loans.mark-returned', $loan) }}" method="POST" class="d-none">@csrf</form>
                                        </button>
                                    @elseif($loan->status === 'pending')
                                        <a href="{{ route('inventory.loans.edit', $loan) }}" class="p-2 text-slate-400">
                                            <i class="bi bi-pencil-square fs-5"></i>
                                        </a>
                                    @else
                                        <a href="{{ route('inventory.loans.show', $loan) }}" class="p-2 text-slate-400">
                                            <i class="bi bi-three-dots fs-5"></i>
                                        </a>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center py-5">
                                <div class="p-5">
                                    <i class="bi bi-clipboard-x display-1 text-slate-100 mb-3"></i>
                                    <div class="text-slate-200 fw-900 fs-4">Registry Empty</div>
                                    <div class="text-slate-400 fw-500">No active assets currently identified in the system.</div>
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
