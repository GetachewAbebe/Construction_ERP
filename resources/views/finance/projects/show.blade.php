@extends('layouts.app')
@section('title', 'Site Intelligence | ' . $project->name)

@section('content')
@push('head')
<link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700;900&family=Outfit:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
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
    .official-table td { padding: 18px 15px; border-bottom: 1px solid var(--border-color); vertical-align: middle; }

    /* Refined Status Pill Architectures */
    .status-badge { font-size: 0.7rem; font-weight: 900; padding: 6px 16px; border-radius: 100px; text-transform: uppercase; letter-spacing: 0.05em; }
    .status-badge-operational { background-color: #10b981; color: #ffffff; }
    .status-badge-hold { background-color: #f59e0b; color: #ffffff; }
    .status-badge-terminated { background-color: #ef4444; color: #ffffff; }
    .status-badge-fallback { background-color: #64748b; color: #ffffff; }

    .stat-card-premium {
        background: var(--bg-soft);
        padding: 2rem;
        border-radius: 16px;
        border: 1px solid #eff6ff;
        transition: all 0.3s ease;
    }

    /* Decoupled Layout Classes replacing Inline Styles */
    .header-control-bar { max-width: 210mm; margin: 0 auto; }
    .section-counter-icon { width: 32px; height: 32px; font-size: 0.8rem; }
    .description-box-premium { font-size: 1.05rem; }
    .expense-description-truncate { max-width: 300px; }
    .signature-line-rule { width: 140px; border-width: 2px !important; }
    .auth-badge-rotated { top: -25px; left: 50%; transform: translateX(-50%) rotate(-4deg); font-weight: 900; font-size: 0.65rem; box-shadow: 0 4px 10px rgba(0,0,0,0.1); border: 2px solid white; }

    @media print {
        @page { size: A4; margin: 0; }
        .no-print { display: none !important; }
        .document-wrapper { padding: 0 !important; background: white !important; }
        .premium-document { box-shadow: none !important; width: 100%; padding: 15mm !important; border: none !important; }
    }
</style>
@endpush

<div class="document-wrapper">
    <!-- Action Controls (Excluded from Prints) -->
    <div class="container-fluid no-print mb-5">
        <div class="d-flex justify-content-between align-items-center bg-white p-3 rounded-pill shadow-lg border header-control-bar">
            <a href="{{ route('finance.projects.index') }}" class="btn btn-white rounded-pill px-4 fw-800 shadow-sm border border-slate-200 text-secondary">
                <i class="bi bi-arrow-left me-2"></i> Registry
            </a>
            <div class="d-flex gap-2">
                <a href="{{ route('finance.projects.edit', $project) }}" class="btn btn-white rounded-pill px-4 fw-800 shadow-sm border border-slate-200 text-secondary">
                    Refine Variables
                </a>
                <button onclick="window.print()" class="btn bg-gradient-premium text-white rounded-pill px-4 fw-800 shadow-sm border-0 transform-hover-premium">
                    <i class="bi bi-printer-fill me-2"></i>Generate Report
                </button>
            </div>
        </div>
    </div>

    <!-- Main Ledger Document Sheet -->
    <div class="premium-document">
        <div class="d-flex justify-content-between align-items-start mb-5">
            <div>
                <div class="logo-text mb-2">NATANEM</div>
                <div class="small fw-900 text-slate-400 uppercase tracking-widest">Engineering & Infrastructure</div>
            </div>
            <div class="text-end">
                <div class="voucher-header-title mb-2">SITE INTELLIGENCE</div>
                <span class="status-badge 
                    @if($project->status === 'operational' || $project->status === 'active') status-badge-operational
                    @elseif($project->status === 'on_hold') status-badge-hold
                    @elseif($project->status === 'cancelled') status-badge-terminated
                    @else status-badge-fallback @endif">
                    STATUS: {{ ($project->status === 'active') ? 'OPERATIONAL' : strtoupper($project->status) }}
                </span>
            </div>
        </div>

        <div class="info-section">
            <div class="row g-4">
                <div class="col-4">
                    <div class="info-item-label">Construction Site</div>
                    <div class="info-item-value fs-5 fw-900 text-erp-deep">{{ $project->name }}</div>
                </div>
                <div class="col-4 text-center">
                    <div class="info-item-label">Operational Area</div>
                    <div class="info-item-value fs-5 fw-900 text-truncate px-2 text-erp-deep">{{ $project->location ?? 'Global / Default' }}</div>
                </div>
                <div class="col-4 text-end">
                    <div class="info-item-label">Timeline Envelope</div>
                    <div class="info-item-value fw-900 text-erp-deep">
                        {{ optional($project->start_date)->format('M d, Y') ?? 'PENDING' }} <span class="mx-1 text-slate-400">→</span> {{ optional($project->end_date)->format('M d, Y') ?? 'TBA' }}
                    </div>
                </div>
            </div>
        </div>

        <!-- Balanced Financial Aggregations -->
        <div class="row g-4 mb-5">
            <div class="col-md-4">
                <div class="stat-card-premium border-start border-5 border-dark">
                    <div class="info-item-label text-dark opacity-50">Portfolio Value</div>
                    <div class="fw-900 text-slate-900 fs-2 mt-1">
                        <small class="fs-6 fw-700 text-muted">ETB</small> {{ number_format($project->budget, 0) }}
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="stat-card-premium border-start border-5 border-danger">
                    <div class="info-item-label text-danger opacity-75">Certified Spending</div>
                    <div class="fw-900 text-danger fs-2 mt-1">
                        <small class="fs-6 fw-700 opacity-50">ETB</small> {{ number_format($project->total_expenses, 0) }}
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="stat-card-premium border-start border-5 border-success">
                    <div class="info-item-label text-success opacity-75">Operating Liquidity</div>
                    <div class="fw-900 text-success fs-2 mt-1">
                        <small class="fs-6 fw-700 opacity-50">ETB</small> {{ number_format($project->budget - $project->total_expenses, 0) }}
                    </div>
                </div>
            </div>
        </div>

        <!-- Segment 01: Scope Metadata -->
        <div class="mb-5">
            <h5 class="fw-900 text-slate-900 mb-4 d-flex align-items-center gap-3">
                <span class="bg-dark text-white rounded-circle d-flex align-items-center justify-content-center section-counter-icon">01</span>
                Operational Scope & Narration
            </h5>
            <div class="p-4 bg-light border rounded-4 text-slate-600 fw-500 lh-lg shadow-sm description-box-premium">
                {{ $project->description ?: 'Operational scope parameters have not been defined for this site. This unit operates under standard infrastructure guidelines.' }}
            </div>
        </div>

        <!-- Segment 02: Recent Transactional Assertions -->
        <div class="mb-5">
            <h5 class="fw-900 text-slate-900 mb-4 d-flex align-items-center gap-3">
                <span class="bg-dark text-white rounded-circle d-flex align-items-center justify-content-center section-counter-icon">02</span>
                Recent Field Expenditures
            </h5>
            <div class="table-outer border rounded-4 overflow-hidden shadow-sm">
                <table class="official-table mb-0">
                    <thead>
                        <tr>
                            <th class="ps-4">Authority Ref</th>
                            <th>Expense Classification</th>
                            <th>Date</th>
                            <th class="text-end pe-4">Transaction Value</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($project->expenses->sortByDesc('expense_date')->take(12) as $expense)
                            <tr>
                                <td class="ps-4">
                                    <span class="badge bg-light text-dark fw-800 border-0 x-small">#{{ str_pad($expense->id, 4, '0', STR_PAD_LEFT) }}</span>
                                </td>
                                <td>
                                    <div class="fw-900 text-erp-deep small">{{ strtoupper($expense->category) }}</div>
                                    <div class="x-small text-muted text-truncate mt-1 expense-description-truncate">{{ $expense->description }}</div>
                                </td>
                                <td class="small fw-800 text-slate-600">{{ $expense->expense_date->format('M d, Y') }}</td>
                                <td class="text-end pe-4">
                                    <div class="fw-900 fs-6 text-erp-deep">
                                        <small class="text-muted fw-normal x-small me-1">ETB</small>{{ number_format($expense->amount, 0) }}
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="4" class="text-center py-5 text-muted fw-800 fs-6 opacity-50 italic">No reconciled transactions found in current site ledger.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- System Validation Matrix & Signatures -->
        <div class="row items-center mt-5 pt-5 border-top border-3 border-dark">
            <div class="col-4 text-center">
                <div class="border-top border-dark mx-auto mb-3 signature-line-rule"></div>
                <div class="x-small fw-900 text-slate-400 uppercase tracking-widest">Project Lead Authority</div>
            </div>
            <div class="col-4 text-center">
                <div class="border-top border-dark mx-auto mb-3 signature-line-rule"></div>
                <div class="x-small fw-900 text-slate-400 uppercase tracking-widest">Site Supervisor Oversight</div>
            </div>
            <div class="col-4 text-center">
                <div class="auth-stamp-box position-relative" style="height: 40px;">
                    <div class="badge bg-success text-white px-3 py-2 position-absolute auth-badge-rotated">VERIFIED & AUTHENTICATED</div>
                </div>
                <div class="border-top border-dark mx-auto mb-3 signature-line-rule"></div>
                <div class="x-small fw-900 text-slate-400 uppercase tracking-widest">Global Finance Controller</div>
            </div>
        </div>

        <!-- Corporate Branding Footer Banner -->
        <div class="text-center mt-5 pt-5 opacity-50">
            <div class="fw-900 fs-4 text-slate-900" style="letter-spacing: -1px;">NATANEM ENGINEERING GROUP</div>
            <div class="x-small fw-800 uppercase tracking-widest mt-2">© {{ date('Y') }} Enterprise Site Intelligence • Official Property</div>
        </div>
    </div>
</div>
@endsection