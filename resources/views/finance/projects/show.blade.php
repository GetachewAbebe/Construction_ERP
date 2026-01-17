@extends('layouts.app')
@section('title', 'Project Intelligence Report | ' . $project->name)

@section('content')
@push('head')
<link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700;900&family=Outfit:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
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
    .official-table td { padding: 15px; border-bottom: 1px solid var(--border-color); vertical-align: middle; }

    .status-badge { font-size: 0.65rem; font-weight: 900; padding: 4px 12px; border-radius: 100px; text-transform: uppercase; }

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
            <a href="{{ route('finance.projects.index') }}" class="btn btn-link text-decoration-none text-muted fw-bold">
                <i class="bi bi-chevron-left"></i> Back to Dashboard
            </a>
            <div class="d-flex gap-2">
                <a href="{{ route('finance.projects.edit', $project) }}" class="btn btn-outline-dark rounded-pill px-4 fw-bold">Refine Project</a>
                <button onclick="window.print()" class="btn btn-dark rounded-pill px-4 fw-bold">
                    <i class="bi bi-printer-fill me-2"></i>Print Intelligence Report
                </button>
            </div>
        </div>
    </div>

    <div class="premium-document">
        <div class="d-flex justify-content-between align-items-start mb-5">
            <div>
                <div class="logo-text mb-1">NATANEM</div>
                <div class="small fw-800 text-slate-600 uppercase tracking-widest mb-2">Engineering & Civil Solutions</div>
            </div>
            <div class="text-end">
                <div class="voucher-header-title mb-2">PROJECT PROFILE</div>
                <div class="badge bg-dark text-white rounded-px px-3 py-2 fw-800">STATUS: {{ strtoupper($project->status) }}</div>
            </div>
        </div>

        <div class="info-section">
            <div class="row g-4">
                <div class="col-4">
                    <div class="info-item-label">Project Identity</div>
                    <div class="info-item-value fs-5">{{ $project->name }}</div>
                </div>
                <div class="col-4">
                    <div class="info-item-label">Site Location</div>
                    <div class="info-item-value fs-5">{{ $project->location ?? 'Not Specified' }}</div>
                </div>
                <div class="col-4 text-end">
                    <div class="info-item-label">Implementation Timeline</div>
                    <div class="info-item-value small">
                        {{ optional($project->start_date)->format('M d, Y') ?? 'TBD' }} — {{ optional($project->end_date)->format('M d, Y') ?? 'TBD' }}
                    </div>
                </div>
            </div>
        </div>

        <div class="row g-4 mb-5">
            <div class="col-md-4">
                <div class="p-4 bg-light border-start border-4 border-dark h-100">
                    <div class="info-item-label">Authorized Budget</div>
                    <div class="fw-900 text-slate-900 fs-3">ETB {{ number_format($project->budget, 2) }}</div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="p-4 bg-light border-start border-4 border-rose-600 h-100">
                    <div class="info-item-label">Total Expenditure</div>
                    <div class="fw-900 text-rose-600 fs-3">ETB {{ number_format($project->total_expenses, 2) }}</div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="p-4 bg-light border-start border-4 border-emerald-600 h-100">
                    <div class="info-item-label">Remaining Liquidity</div>
                    <div class="fw-900 text-emerald-600 fs-3">ETB {{ number_format($project->budget - $project->total_expenses, 2) }}</div>
                </div>
            </div>
        </div>

        <div class="mb-5">
            <h5 class="fw-900 text-slate-900 mb-3 border-start border-4 border-dark ps-3">Scope of Works</h5>
            <div class="p-4 bg-white border-2 border-slate-100 rounded-3 text-slate-600 fw-500 lh-lg shadow-sm">
                {{ $project->description ?? 'No detailed scope of work has been defined for this operational unit.' }}
            </div>
        </div>

        <div class="mb-5">
            <h5 class="fw-900 text-slate-900 mb-3 border-start border-4 border-dark ps-3">Financial Ledger</h5>
            <div class="table-outer border rounded-3 overflow-hidden">
                <table class="official-table mb-0">
                    <thead>
                        <tr>
                            <th class="ps-4">Reference</th>
                            <th>Particulars</th>
                            <th>Date</th>
                            <th class="text-end pe-4">Amount (ETB)</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($project->expenses->sortByDesc('created_at')->take(10) as $expense)
                            <tr>
                                <td class="ps-4 small fw-800 text-slate-400">#{{ str_pad($expense->id, 5, '0', STR_PAD_LEFT) }}</td>
                                <td>
                                    <div class="fw-800 text-dark small">{{ strtoupper($expense->category) }}</div>
                                    <div class="x-small text-slate-400 text-truncate" style="max-width: 250px;">{{ $expense->description }}</div>
                                </td>
                                <td class="small fw-700 text-slate-600">{{ $expense->expense_date->format('d M, Y') }}</td>
                                <td class="text-end pe-4 fw-900 fs-6">{{ number_format($expense->amount, 2) }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="4" class="text-center py-5 text-muted italic small">No transaction records found.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="row mt-auto pt-5 border-top border-4 border-dark">
            <div class="col-4 text-center">
                <div class="border-top border-dark mx-auto mb-2" style="width: 150px;"></div>
                <div class="small fw-800 text-slate-400 uppercase">Project Manager</div>
            </div>
            <div class="col-4 text-center">
                <div class="border-top border-dark mx-auto mb-2" style="width: 150px;"></div>
                <div class="small fw-800 text-slate-400 uppercase">Site Supervisor</div>
            </div>
            <div class="col-4 text-center">
                <div class="auth-stamp-box position-relative" style="height: 30px;">
                    <div class="badge bg-success-soft text-success border border-success px-3 py-1 position-absolute" style="top: -20px; left: 50%; transform: translateX(-50%) rotate(-5deg); font-weight: 900; font-size: 0.6rem;">VALIDATED REPORT</div>
                </div>
                <div class="border-top border-dark mx-auto mb-2" style="width: 150px;"></div>
                <div class="small fw-800 text-slate-400 uppercase">Operations Director</div>
            </div>
        </div>

        <div class="text-center mt-5 pt-3 opacity-25">
            <div class="fw-900 fs-5 text-slate-900">NATANEM ENGINEERING PROJECT ANALYTICS</div>
            <div class="x-small fw-700 uppercase tracking-widest mt-1">Certified Corporate Document - {{ date('Y') }}</div>
        </div>
    </div>
</div>
@endsection
