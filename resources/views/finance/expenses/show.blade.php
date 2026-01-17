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
                            <div class="small fw-800 text-white-50 text-uppercase mb-1">Transaction Category</div>
                            <div class="fw-800 fs-5">{{ strtoupper($expense->category) }}</div>
                        </div>
                        <div class="col-md-4 mb-3 mb-md-0 border-start border-white-10 ps-md-4">
                            <div class="small fw-800 text-white-50 text-uppercase mb-1">Transaction Date</div>
                            <div class="fw-800 fs-5">{{ $expense->expense_date->format('d M, Y') }}</div>
                        </div>
                        <div class="col-md-4 text-md-end border-start border-white-10 ps-md-4">
                            <div class="small fw-800 text-white-50 text-uppercase mb-1">Invoice / Reference</div>
                            <div class="fw-800 fs-5">{{ $expense->reference_no ?? 'N/A' }}</div>
                        </div>
                    </div>
                </div>

                {{-- Transaction Particulars Table --}}
                <div class="mb-5">
                    <div class="small fw-800 text-muted text-uppercase mb-2" style="letter-spacing: 0.1em;">Transaction Particulars</div>
                    <div class="table-responsive">
                        <table class="table table-bordered align-middle">
                            <thead class="bg-light-soft text-muted small fw-800 text-uppercase">
                                <tr>
                                    <th class="py-3 ps-4">Description / Details</th>
                                    <th class="py-3 text-end pe-4" style="width: 200px;">Amount (ETB)</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td class="py-4 ps-4">
                                        <div class="fw-800 text-dark mb-1">{{ strtoupper($expense->category) }} REQUISITION</div>
                                        <div class="text-muted fw-600 small" style="line-height: 1.6;">{{ $expense->description }}</div>
                                    </td>
                                    <td class="py-4 text-end pe-4 fw-900 fs-5">
                                        {{ number_format($expense->amount, 2) }}
                                    </td>
                                </tr>
                            </tbody>
                            <tfoot class="bg-light-soft">
                                <tr>
                                    <td class="text-end fw-800 py-3">GRAND TOTAL</td>
                                    <td class="text-end fw-900 py-3 pe-4 fs-4 text-erp-deep">
                                        {{ number_format($expense->amount, 2) }}
                                    </td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>

                {{-- Financial & Status Overview --}}
                <div class="row g-4 mb-5">
                    <div class="col-md-6">
                        <div class="small fw-800 text-muted text-uppercase mb-2" style="letter-spacing: 0.1em;">Project Financial Context</div>
                        <div class="p-4 bg-light-soft rounded-4 border h-100">
                            <div class="d-flex justify-content-between mb-2">
                                <span class="text-muted fw-700">Project Budget:</span>
                                <span class="fw-800 text-dark">ETB {{ number_format($expense->project->budget, 2) }}</span>
                            </div>
                            <div class="d-flex justify-content-between mb-3">
                                <span class="text-muted fw-700">Accumulated Costs:</span>
                                <span class="fw-800 text-danger">ETB {{ number_format($expense->project->total_expenses, 2) }}</span>
                            </div>
                            <div class="d-flex justify-content-between pt-3 border-top border-2 border-white">
                                <span class="fw-800 text-erp-deep uppercase small">Remaining Balance:</span>
                                <span class="fw-900 text-success fs-5">ETB {{ number_format($expense->project->budget - $expense->project->total_expenses, 2) }}</span>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="small fw-800 text-muted text-uppercase mb-2" style="letter-spacing: 0.1em;">Current Transaction Status</div>
                        <div class="p-4 rounded-4 border h-100 {{ $expense->status === 'approved' ? 'bg-success-soft border-success-subtle' : ($expense->status === 'rejected' ? 'bg-danger-soft border-danger-subtle' : 'bg-warning-soft border-warning-subtle') }}">
                            <div class="mb-4">
                                <div class="small fw-800 text-muted text-uppercase mb-1">Status</div>
                                <div class="fw-900 fs-3 text-{{ $expense->status === 'approved' ? 'success' : ($expense->status === 'rejected' ? 'danger' : 'warning') }}">
                                    {{ strtoupper($expense->status ?? 'pending') }}
                                </div>
                            </div>
                            @if($expense->status === 'approved' && $expense->approved_by)
                                <div>
                                    <div class="small fw-800 text-muted text-uppercase mb-1">Secure Digital Auth</div>
                                    <div class="fw-800 text-dark">{{ $expense->approvedBy->name ?? 'Administrator' }}</div>
                                    <div class="text-muted small fw-700">{{ $expense->updated_at->format('d M, Y H:i') }}</div>
                                </div>
                            @elseif($expense->status === 'rejected' && $expense->rejected_by)
                                <div>
                                    <div class="small fw-800 text-muted text-uppercase mb-1">Rejected By</div>
                                    <div class="fw-800 text-danger">{{ $expense->rejectedBy->name ?? 'Administrator' }}</div>
                                    <div class="text-muted small fw-700">{{ $expense->updated_at->format('d M, Y H:i') }}</div>
                                </div>
                            @else
                                <div class="text-muted italic fw-700 small pt-2">Awaiting formal review by the Finance Department.</div>
                            @endif
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
