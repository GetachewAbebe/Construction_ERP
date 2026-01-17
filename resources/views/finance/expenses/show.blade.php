@extends('layouts.app')
@section('title', 'Expense Report #' . $expense->id)

@section('content')
<div class="row justify-content-center stagger-entrance">
    <div class="col-lg-9">
        {{-- Report Controls (Screen Only) --}}
        <div class="d-flex justify-content-between align-items-center mb-4 d-print-none">
            <a href="{{ route('finance.expenses.index') }}" class="btn btn-white rounded-pill px-4 shadow-sm border-0 fw-bold">
                <i class="bi bi-arrow-left me-2"></i>Back to Ledger
            </a>
            
            <div class="d-flex gap-2">
                @if(Auth::user()->hasAnyRole(['Administrator', 'FinancialManager', 'Financial Manager']) && $expense->status === 'pending')
                    <button type="button" class="btn btn-white text-success rounded-pill px-4 shadow-sm border-0 fw-bold hover-lift" 
                            onclick="if(confirm('Authorize this expense?')) document.getElementById('approve-form').submit()">
                        <i class="bi bi-patch-check-fill me-2"></i>Approve
                    </button>
                    <form id="approve-form" action="{{ route('finance.expenses.approve', $expense) }}" method="POST" class="d-none">@csrf</form>

                    <button type="button" class="btn btn-white text-danger rounded-pill px-4 shadow-sm border-0 fw-bold hover-lift" 
                            onclick="if(confirm('Reject this transaction?')) document.getElementById('reject-form').submit()">
                        <i class="bi bi-x-circle-fill me-2"></i>Reject
                    </button>
                    <form id="reject-form" action="{{ route('finance.expenses.reject', $expense) }}" method="POST" class="d-none">@csrf</form>
                @endif

                <button onclick="window.print()" class="btn btn-erp-deep rounded-pill px-4 shadow-sm border-0 fw-bold">
                    <i class="bi bi-printer-fill me-2"></i>Print Report
                </button>
            </div>
        </div>

        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show hardened-glass border-0 shadow-sm mb-4 d-print-none" role="alert">
                <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        {{-- Printable Document --}}
        <div class="card border-0 shadow-lg print-card overflow-hidden">
            <div class="card-body p-4 p-md-5">
                {{-- Header --}}
                <div class="d-flex justify-content-between align-items-start mb-5 pb-4 border-bottom">
                    <div>
                        <h1 class="fw-900 text-erp-deep mb-2" style="letter-spacing: -2px;">EXPENSE REPORT</h1>
                        <div class="text-muted fw-800 fs-5">ID: EX-{{ str_pad($expense->id, 6, '0', STR_PAD_LEFT) }}</div>
                    </div>
                    <div class="text-end">
                        <h4 class="fw-800 text-erp-deep mb-1">Natanem Engineering</h4>
                        <div class="text-muted small fw-700">
                            Industrial & Civil Construction Solutions<br>
                            Addis Ababa, Ethiopia<br>
                            Generated on {{ now()->format('d M, Y H:i') }}
                        </div>
                    </div>
                </div>

                {{-- Parties Info --}}
                <div class="row g-4 mb-5">
                    <div class="col-sm-6">
                        <div class="small fw-800 text-muted text-uppercase mb-2" style="letter-spacing: 0.1em;">Requester Information</div>
                        <div class="p-4 bg-light-soft rounded-4 border">
                            <div class="fw-800 text-dark fs-5">{{ optional($expense->user)->name ?? 'System' }}</div>
                            <div class="text-muted fw-700 small">{{ optional($expense->user)->email ?? 'no-email@natanem.com' }}</div>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="small fw-800 text-muted text-uppercase mb-2 text-sm-end" style="letter-spacing: 0.1em;">Project Assignment</div>
                        <div class="p-4 bg-light-soft rounded-4 border text-sm-end">
                            <div class="fw-800 text-erp-deep fs-5 text-truncate">{{ optional($expense->project)->name ?? 'General Expenditure' }}</div>
                            <div class="text-muted fw-700 small">{{ optional($expense->project)->location ?? 'Addis Ababa' }}</div>
                        </div>
                    </div>
                </div>

                {{-- Financial Details --}}
                <div class="bg-erp-deep rounded-4 p-4 mb-5 text-white shadow-sm">
                    <div class="row align-items-center">
                        <div class="col-md-4 mb-3 mb-md-0">
                            <div class="small fw-800 text-white-50 text-uppercase mb-1">Category</div>
                            <div class="fw-800 fs-5">{{ strtoupper($expense->category) }}</div>
                        </div>
                        <div class="col-md-4 mb-3 mb-md-0 border-start border-white-10 ps-md-4">
                            <div class="small fw-800 text-white-50 text-uppercase mb-1">Date of Record</div>
                            <div class="fw-800 fs-5">{{ $expense->expense_date->format('d M, Y') }}</div>
                        </div>
                        <div class="col-md-4 text-md-end border-start border-white-10 ps-md-4">
                            <div class="small fw-800 text-white-50 text-uppercase mb-1">Total Amount</div>
                            <div class="fw-900 fs-2">
                                <span class="fs-6 fw-400 opacity-75">ETB</span> {{ number_format($expense->amount, 2) }}
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Descriptive Content --}}
                <div class="mb-5">
                    <div class="small fw-800 text-muted text-uppercase mb-2" style="letter-spacing: 0.1em;">Narrative & Description</div>
                    <div class="p-4 bg-white border rounded-4 fw-700 text-dark" style="min-height: 120px; line-height: 1.8; font-size: 1.05rem;">
                        {{ $expense->description ?? 'No detailed description provided for this transaction.' }}
                    </div>
                </div>

                {{-- Status & Approvals --}}
                <div class="row g-4 mb-5">
                    <div class="col-md-12">
                        <div class="p-4 rounded-4 border {{ $expense->status === 'approved' ? 'bg-success-soft border-success-subtle' : ($expense->status === 'rejected' ? 'bg-danger-soft border-danger-subtle' : 'bg-warning-soft border-warning-subtle') }}">
                            <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
                                <div>
                                    <div class="small fw-800 text-muted text-uppercase mb-1">Current Status</div>
                                    <div class="fw-900 fs-4 text-{{ $expense->status === 'approved' ? 'success' : ($expense->status === 'rejected' ? 'danger' : 'warning') }}">
                                        {{ strtoupper($expense->status ?? 'pending') }}
                                    </div>
                                </div>
                                @if($expense->status === 'approved' && $expense->approved_by)
                                    <div class="text-md-end">
                                        <div class="small fw-800 text-muted text-uppercase mb-1">Digital Authorization</div>
                                        <div class="fw-800 text-dark">{{ $expense->approvedBy->name ?? 'Administrator' }}</div>
                                        <div class="text-muted small fw-700">{{ $expense->updated_at->format('d M, Y H:i') }}</div>
                                    </div>
                                @endif
                                @if($expense->status === 'pending')
                                    <div class="text-muted italic fw-700 small">Awaiting Review</div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Signatures Header --}}
                <div class="row mt-5 pt-5 border-top signature-section">
                    <div class="col-4 text-center">
                        <div class="border-top border-dark mx-auto mb-2" style="width: 150px;"></div>
                        <div class="small fw-800 text-muted text-uppercase">Prepared By</div>
                    </div>
                    <div class="col-4 text-center">
                        <div class="border-top border-dark mx-auto mb-2" style="width: 150px;"></div>
                        <div class="small fw-800 text-muted text-uppercase">Checked By</div>
                    </div>
                    <div class="col-4 text-center">
                        <div class="border-top border-dark mx-auto mb-2" style="width: 150px;"></div>
                        <div class="small fw-800 text-muted text-uppercase">Authorized By</div>
                    </div>
                </div>

                {{-- Footer Summary --}}
                <div class="text-center mt-5 pt-4 border-top border-light opacity-50 small fw-800 italic">
                    NATANEM ENGINEERING SOLUTIONS - OFFICIAL ENTERPRISE RESOURCE DOCUMENT
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.bg-light-soft { background-color: #f8fafc; }
.bg-success-soft { background-color: #f0fdf4; }
.bg-danger-soft { background-color: #fef2f2; }
.bg-warning-soft { background-color: #fffbeb; }

.print-card {
    border-radius: 20px;
    background: white;
}

@media print {
    .main-sidebar, .main-navbar, .sidebar, .navbar, .btn-group, .d-print-none, .alert {
        display: none !important;
    }
    .main-content {
        margin: 0 !important;
        padding: 0 !important;
        width: 100% !important;
        background: white !important;
    }
    .print-card {
        box-shadow: none !important;
        border: none !important;
        padding: 0 !important;
    }
    body {
        background: white !important;
    }
    .container, .container-fluid {
        max-width: 100% !important;
        width: 100% !important;
        padding: 0 !important;
        margin: 0 !important;
    }
    .row {
        margin: 0 !important;
    }
    .col-lg-9 {
        width: 100% !important;
        max-width: 100% !important;
        flex: 0 0 100% !important;
    }
    .signature-section {
        display: flex !important;
        margin-top: 100px !important;
    }
}
</style>
@endsection
