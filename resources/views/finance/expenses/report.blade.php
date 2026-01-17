@extends('layouts.app')
@section('title', 'Expense Report #' . $expense->id)

@section('content')
<div class="row justify-content-center stagger-entrance">
    <div class="col-lg-9">
        {{-- Report Controls --}}
        <div class="d-flex justify-content-between align-items-center mb-4 d-print-none">
            <a href="{{ route('finance.expenses.index') }}" class="btn btn-white rounded-pill px-4 shadow-sm border-0">
                <i class="bi bi-arrow-left me-2"></i>Back to Ledger
            </a>
            <button onclick="window.print()" class="btn btn-erp-deep rounded-pill px-4 shadow-sm border-0">
                <i class="bi bi-printer-fill me-2"></i>Print Report
            </button>
        </div>

        {{-- Printable Document --}}
        <div class="card border-0 shadow-lg print-card">
            <div class="card-body p-5">
                {{-- Header --}}
                <div class="d-flex justify-content-between align-items-start mb-5 pb-4 border-bottom">
                    <div>
                        <h2 class="fw-800 text-erp-deep mb-2">EXPENSE REPORT</h2>
                        <div class="text-muted fw-600">ID: EX-{{ str_pad($expense->id, 6, '0', STR_PAD_LEFT) }}</div>
                    </div>
                    <div class="text-end">
                        <h4 class="fw-800 text-erp-deep mb-1">Natanem Engineering</h4>
                        <div class="text-muted small fw-600">
                            Professional Construction Solutions<br>
                            Addis Ababa, Ethiopia<br>
                            Generated on {{ now()->format('d M, Y H:i') }}
                        </div>
                    </div>
                </div>

                <div class="row g-4 mb-5">
                    <div class="col-sm-6">
                        <div class="small fw-800 text-muted text-uppercase mb-2">Requester Information</div>
                        <div class="fw-700 text-dark fs-5">{{ optional($expense->user)->name ?? 'System' }}</div>
                        <div class="text-muted fw-600 small">{{ optional($expense->user)->email }}</div>
                    </div>
                    <div class="col-sm-6 text-sm-end">
                        <div class="small fw-800 text-muted text-uppercase mb-2">Project Assignment</div>
                        <div class="fw-700 text-erp-deep fs-5">{{ optional($expense->project)->name ?? 'General Expenditure' }}</div>
                        <div class="text-muted fw-600 small">ID: {{ optional($expense->project)->id ?? 'N/A' }}</div>
                    </div>
                </div>

                {{-- Financial Details --}}
                <div class="bg-light-soft rounded-4 p-4 mb-5">
                    <div class="row align-items-center">
                        <div class="col">
                            <div class="small fw-800 text-muted text-uppercase mb-1">Transaction Category</div>
                            <div class="fw-700 text-dark">{{ strtoupper($expense->category) }}</div>
                        </div>
                        <div class="col">
                            <div class="small fw-800 text-muted text-uppercase mb-1">Transaction Date</div>
                            <div class="fw-700 text-dark">{{ $expense->expense_date->format('d M, Y') }}</div>
                        </div>
                        <div class="col-auto text-end">
                            <div class="small fw-800 text-muted text-uppercase mb-1">Total Valuation</div>
                            <div class="fw-900 text-erp-deep fs-3">
                                <small class="fw-normal">ETB</small> {{ number_format($expense->amount, 2) }}
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Descriptive Content --}}
                <div class="mb-5">
                    <div class="small fw-800 text-muted text-uppercase mb-2">Description & Notes</div>
                    <div class="p-4 bg-white border rounded-4 fw-600 text-dark-50" style="min-height: 100px; line-height: 1.6;">
                        {{ $expense->description ?? 'No detailed description provided for this transaction.' }}
                    </div>
                </div>

                {{-- Status & Approvals --}}
                <div class="row g-4 mb-5">
                    <div class="col-md-6">
                        <div class="small fw-800 text-muted text-uppercase mb-2">Authorization Status</div>
                        <div class="d-flex align-items-center gap-2">
                            @if($expense->status === 'approved')
                                <div class="badge bg-success shadow-sm rounded-pill px-4 py-2 fw-800 text-uppercase">
                                    <i class="bi bi-check-circle-fill me-2"></i>Approved
                                </div>
                            @elseif($expense->status === 'rejected')
                                <div class="badge bg-danger shadow-sm rounded-pill px-4 py-2 fw-800 text-uppercase">
                                    <i class="bi bi-x-circle-fill me-2"></i>Rejected
                                </div>
                            @else
                                <div class="badge bg-warning shadow-sm rounded-pill px-4 py-2 fw-800 text-uppercase">
                                    <i class="bi bi-hourglass-split me-2"></i>Pending
                                </div>
                            @endif
                        </div>
                    </div>
                    @if($expense->status === 'approved' && $expense->approved_by)
                        <div class="col-md-6 text-md-end">
                            <div class="small fw-800 text-muted text-uppercase mb-2">Approved By</div>
                            <div class="fw-700 text-dark">{{ $expense->approvedBy->name ?? 'Administrator' }}</div>
                            <div class="text-muted small fw-600">{{ $expense->updated_at->format('d M, Y H:i') }}</div>
                        </div>
                    @endif
                </div>

                {{-- Signatures Header --}}
                <div class="row mt-5 pt-5 border-top d-none d-print-flex">
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

                {{-- Footer Text --}}
                <div class="text-center mt-5 pt-4 border-top border-light opacity-50 small fw-600 italic">
                    This is a computer-generated document and remains valid within the Natanem ERP framework.
                </div>
            </div>
        </div>
    </div>
</div>

<style>
@media print {
    .main-sidebar, .main-navbar, .sidebar, .navbar, .btn-group, .d-print-none {
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
    }
    body {
        background: white !important;
    }
    header, footer {
        display: none !important;
    }
}
</style>
@endsection
