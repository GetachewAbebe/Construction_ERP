@extends('layouts.app')
@section('title', 'Financial Authorization Record | Natanem Engineering')

@section('content')
{{-- High-End Typography and Assets --}}
@push('head')
<link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,700;0,900;1,700&family=Outfit:wght@300;400;600;700;800;900&display=swap" rel="stylesheet">
@endpush

<div class="row justify-content-center stagger-entrance">
    <div class="col-lg-10">
        {{-- Elite Control Panel (Screen only) --}}
        <div class="d-flex justify-content-between align-items-center mb-5 d-print-none shadow-sm p-4 bg-white rounded-4 border-0">
            <div>
                <a href="{{ route('finance.expenses.index') }}" class="btn btn-outline-secondary border-0 rounded-pill px-4 fw-800">
                    <i class="bi bi-arrow-left me-2"></i>Back to Ledger
                </a>
            </div>
            
            <div class="d-flex gap-3">
                @if(Auth::user()->hasAnyRole(['Administrator', 'FinancialManager', 'Financial Manager']) && $expense->status === 'pending')
                    <button type="button" class="btn btn-emerald rounded-pill px-4 fw-800 shadow-sm hover-lift" 
                            onclick="if(confirm('Authorize this expenditure?')) document.getElementById('approve-form').submit()">
                        <i class="bi bi-patch-check-fill me-2"></i>Authorize
                    </button>
                    <form id="approve-form" action="{{ route('finance.expenses.approve', $expense) }}" method="POST" class="d-none">@csrf</form>

                    <button type="button" class="btn btn-rose rounded-pill px-4 fw-800 shadow-sm hover-lift" 
                            onclick="if(confirm('Reject this transaction?')) document.getElementById('reject-form').submit()">
                        <i class="bi bi-x-circle-fill me-2"></i>Reject
                    </button>
                    <form id="reject-form" action="{{ route('finance.expenses.reject', $expense) }}" method="POST" class="d-none">@csrf</form>
                @endif

                <button onclick="window.print()" class="btn btn-erp-deep rounded-pill px-5 fw-900 shadow-lg border-0 hover-lift ripple">
                    <i class="bi bi-printer-fill me-2"></i>PRINT OFFICIAL RECORD
                </button>
            </div>
        </div>

        @if(session('success'))
            <div class="alert alert-emerald alert-dismissible fade show border-0 shadow-sm mb-5 d-print-none rounded-4 p-3" role="alert">
                <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        {{-- The Premium Report Document --}}
        <div class="premium-document-container mx-auto">
            <div class="premium-document shadow-2xl border-0 overflow-hidden" id="reportDocument">
                {{-- Background Aesthetic (Structural Lines) --}}
                <div class="structural-accent-lines"></div>

                <div class="document-content p-5">
                    {{-- Royal Header --}}
                    <div class="d-flex justify-content-between align-items-center mb-5 pb-5 border-bottom border-2 border-slate-100 position-relative">
                        <div class="brand-identity">
                            <div class="text-erp-deep fw-900 fs-1 lh-1 mb-1" style="font-family: 'Playfair Display', serif;">NATANEM</div>
                            <div class="text-slate-400 fw-800 small tracking-widest uppercase">Engineering & Civil Solutions</div>
                        </div>
                        
                        <div class="document-descriptor text-end">
                            <h2 class="fw-900 text-slate-800 mb-0 tracking-tighter" style="font-size: 2.5rem;">FINANCIAL VOUCHER</h2>
                            <div class="d-flex justify-content-end gap-3 mt-2">
                                <span class="badge bg-slate-100 text-slate-600 rounded-px px-3 py-2 fw-800 border-0">SERIAL: {{ str_pad($expense->id, 8, '0', STR_PAD_LEFT) }}</span>
                                <span class="badge bg-slate-800 text-white rounded-px px-3 py-2 fw-800 border-0">REF: {{ $expense->reference_no ?? 'VCH-'.$expense->id }}</span>
                            </div>
                        </div>
                    </div>

                    {{-- Transaction Summary Bar --}}
                    <div class="summary-master-bar bg-slate-900 rounded-xl p-4 mb-5 shadow-inner d-flex justify-content-between align-items-center">
                        <div class="px-3 border-end border-slate-700">
                            <div class="text-slate-500 small fw-800 uppercase tracking-wider mb-1">Transaction Category</div>
                            <div class="text-white fw-900 fs-4">{{ strtoupper($expense->category) }}</div>
                        </div>
                        <div class="px-3 border-end border-slate-700">
                            <div class="text-slate-500 small fw-800 uppercase tracking-wider mb-1">Execution Date</div>
                            <div class="text-white fw-900 fs-4">{{ $expense->expense_date->format('M d, Y') }}</div>
                        </div>
                        <div class="px-4 text-end">
                            <div class="text-slate-500 small fw-800 uppercase tracking-wider mb-1">Final Disbursed Amount</div>
                            <div class="text-white fw-900 display-6 lh-1">
                                <span class="fs-6 fw-400 opacity-50">ETB</span> {{ number_format($expense->amount, 2) }}
                            </div>
                        </div>
                    </div>

                    {{-- Data Matrix --}}
                    <div class="row g-5 mb-5">
                        <div class="col-md-6">
                            <h5 class="fw-900 text-slate-900 mb-3 border-start border-4 border-erp-deep ps-3">Project Alignment</h5>
                            <div class="card bg-slate-50 border-0 rounded-4 h-100">
                                <div class="card-body p-4">
                                    <div class="mb-3">
                                        <div class="text-slate-400 small fw-800 uppercase tracking-widest mb-1">Project Site</div>
                                        <div class="fw-900 text-slate-800 fs-5">{{ optional($expense->project)->name ?? 'GENERAL OPERATIONS' }}</div>
                                    </div>
                                    <div class="mb-3">
                                        <div class="text-slate-400 small fw-800 uppercase tracking-widest mb-1">Project Location</div>
                                        <div class="fw-700 text-slate-600">{{ optional($expense->project)->location ?? 'Addis Ababa Headquarters' }}</div>
                                    </div>
                                    <div class="pt-3 border-top border-slate-200">
                                        <div class="d-flex justify-content-between mb-1">
                                            <span class="text-slate-400 fw-700">Completion Target:</span>
                                            <span class="text-slate-700 fw-900">{{ optional($expense->project)->end_date ? $expense->project->end_date->format('M Y') : 'Ongoing' }}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <h5 class="fw-900 text-slate-900 mb-3 border-start border-4 border-erp-deep ps-3">Requester Details</h5>
                            <div class="card bg-slate-50 border-0 rounded-4 h-100">
                                <div class="card-body p-4">
                                    <div class="mb-3">
                                        <div class="text-slate-400 small fw-800 uppercase tracking-widest mb-1">Employee Name</div>
                                        <div class="fw-900 text-slate-800 fs-5">{{ optional($expense->user)->name ?? 'SYSTEM DISBURSEMENT' }}</div>
                                    </div>
                                    <div class="mb-3">
                                        <div class="text-slate-400 small fw-800 uppercase tracking-widest mb-1">Position / Department</div>
                                        <div class="fw-700 text-slate-600">{{ optional($expense->user)->position ?? 'Operations Staff' }}</div>
                                    </div>
                                    <div class="pt-3 border-top border-slate-200">
                                        <div class="d-flex justify-content-between">
                                            <span class="text-slate-400 fw-700">Digital ID:</span>
                                            <span class="text-slate-700 fw-900">EMP-{{ str_pad($expense->user_id, 5, '0', STR_PAD_LEFT) }}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Transaction Particulars (Detailed) --}}
                    <div class="particulars-section mb-5">
                        <h5 class="fw-900 text-slate-900 mb-3 border-start border-4 border-erp-deep ps-3">Expenditure Particulars</h5>
                        <div class="table-outer border border-slate-200 rounded-4 overflow-hidden">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="bg-slate-900 text-white fw-800 small tracking-wider uppercase">
                                    <tr>
                                        <th class="py-4 ps-4">Narrative Description of Works / Procurement</th>
                                        <th class="py-4 text-end pe-4" style="width: 200px;">Subtotal (ETB)</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td class="py-5 ps-4">
                                            <div class="fw-900 text-slate-900 fs-5 mb-2">{{ strtoupper($expense->category) }} SERVICES</div>
                                            <p class="text-slate-600 fw-600 mb-0 lh-lg" style="max-width: 600px;">
                                                {{ $expense->description ?? 'No detailed description provided. This transaction was logged as a standard operational expenditure.' }}
                                            </p>
                                        </td>
                                        <td class="py-5 text-end pe-4 fw-1000 fs-3 text-slate-900">
                                            {{ number_format($expense->amount, 2) }}
                                        </td>
                                    </tr>
                                </tbody>
                                <tfoot class="bg-slate-50 border-top border-2">
                                    <tr>
                                        <td class="text-end fw-900 py-4 fs-5 text-slate-500">TOTAL PAYABLE AMOUNT</td>
                                        <td class="text-end fw-1000 py-4 pe-4 fs-2 text-erp-deep">
                                            {{ number_format($expense->amount, 2) }}
                                        </td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>

                    {{-- Financial Compliance --}}
                    <div class="financial-compliance-matrix row g-4 mb-5">
                        <div class="col-md-7">
                            <h5 class="fw-900 text-slate-900 mb-3 border-start border-4 border-erp-deep ps-3">Project Compliance Matrix</h5>
                            <div class="compliance-box p-4 rounded-4 border-2 border-slate-100">
                                <div class="row text-center g-3">
                                    <div class="col-4 border-end">
                                        <div class="text-slate-400 small fw-800 uppercase mb-1">Total Budget</div>
                                        <div class="fw-900 text-slate-800">ETB {{ number_format($expense->project->budget, 2) }}</div>
                                    </div>
                                    <div class="col-4 border-end px-3">
                                        <div class="text-slate-400 small fw-800 uppercase mb-1">Approved Exp.</div>
                                        <div class="fw-900 text-rose-600">ETB {{ number_format($expense->project->total_expenses, 2) }}</div>
                                    </div>
                                    <div class="col-4">
                                        <div class="text-slate-400 small fw-800 uppercase mb-1">Net Balance</div>
                                        <div class="fw-900 text-emerald-600">ETB {{ number_format($expense->project->budget - $expense->project->total_expenses, 2) }}</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-5">
                            <h5 class="fw-900 text-slate-900 mb-3 border-start border-4 border-erp-deep ps-3">Authorization Status</h5>
                            <div class="status-box p-4 rounded-4 text-center d-flex flex-column justify-content-center h-100 shadow-sm {{ $expense->status === 'approved' ? 'bg-emerald text-white' : ($expense->status === 'rejected' ? 'bg-rose text-white' : 'bg-amber text-slate-800') }}">
                                <div class="fw-1000 fs-2 uppercase tracking-tighter">{{ $expense->status ?? 'PENDING' }}</div>
                                <div class="fw-700 small opacity-75">{{ $expense->status === 'approved' ? 'Digital Seal Verified' : ($expense->status === 'rejected' ? 'Verification Denied' : 'Pending Verification') }}</div>
                            </div>
                        </div>
                    </div>

                    {{-- Elite Multi-Signature Verification --}}
                    <div class="signature-vault mt-5 pt-5 border-top border-4 border-slate-900">
                        <div class="row text-center g-5">
                            <div class="col-4">
                                <div class="signature-box mb-3 mx-auto shadow-inner rounded-3" style="width: 200px; height: 100px; background: #fafafa; border: 1px dashed #ddd;">
                                    {{-- Prepared User Name (Background) --}}
                                    <div class="h-100 d-flex align-items-center justify-content-center opacity-10 fw-900 fs-1">{{ optional($expense->user)->name }}</div>
                                </div>
                                <div class="fw-900 text-slate-900 fs-5 mb-0">{{ optional($expense->user)->name }}</div>
                                <div class="text-slate-400 small fw-800 uppercase">Preparer / Employee</div>
                                <div class="text-slate-400 x-small mt-1">{{ $expense->created_at->format('Y-m-d H:i') }}</div>
                            </div>
                            
                            <div class="col-4">
                                <div class="signature-box mb-3 mx-auto shadow-inner rounded-3" style="width: 200px; height: 100px; background: #fafafa; border: 1px dashed #ddd;">
                                    <div class="h-100 d-flex align-items-center justify-content-center">
                                        <i class="bi bi-clock-history fs-1 text-slate-200"></i>
                                    </div>
                                </div>
                                <div class="fw-900 text-slate-900 fs-5 mb-0">- NIL -</div>
                                <div class="text-slate-400 small fw-800 uppercase">Technical Verifier (PM)</div>
                            </div>

                            <div class="col-4">
                                <div class="signature-box mb-3 mx-auto shadow-inner rounded-3 position-relative overflow-hidden" style="width: 200px; height: 100px; background: #fafafa; border: 1px dashed #ddd;">
                                    @if($expense->status === 'approved')
                                        {{-- Verification Stamp Effect --}}
                                        <div class="h-100 d-flex align-items-center justify-content-center">
                                            <div class="auth-seal-svg" style="transform: rotate(-15deg); opacity: 0.8;">
                                                <div class="border border-4 border-emerald-600 rounded-circle d-flex align-items-center justify-content-center" style="width: 80px; height: 80px;">
                                                    <div class="text-emerald-600 fw-900 x-small text-center lh-1">
                                                        VERIFIED<br>DIGITAL<br>OFFICE
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endif
                                </div>
                                <div class="fw-900 text-slate-800 fs-5 mb-0">{{ optional($expense->approvedBy)->name ?? 'Authorized Official' }}</div>
                                <div class="text-slate-400 small fw-800 uppercase">Financial Approval</div>
                                <div class="text-slate-400 x-small mt-1">{{ $expense->status === 'approved' ? $expense->updated_at->format('Y-m-d H:i') : 'AWAITING AUTH' }}</div>
                            </div>
                        </div>
                    </div>

                    {{-- Legal Footer --}}
                    <div class="text-center mt-5 pt-5 opacity-25">
                        <div class="fw-900 fs-3 text-slate-900 lh-1">NATANEM ENGINEERING</div>
                        <div class="fw-700 x-small uppercase tracking-widest mt-1">Certified Enterprise Resource Management Record - 2026</div>
                        <div class="mt-2 font-monospace x-small">HASHCODE: {{ strtoupper(hash('crc32', $expense->id . $expense->created_at)) }}-ERP-{{ date('Y') }}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
/* Elite Palette */
:root {
    --erp-deep: #0f172a;
    --emerald-primary: #10b981;
    --rose-primary: #f43f5e;
    --amber-primary: #f59e0b;
}

.text-emerald-600 { color: #059669; }
.text-rose-600 { color: #e11d48; }
.bg-emerald { background-color: var(--emerald-primary); }
.bg-rose { background-color: var(--rose-primary); }
.bg-amber { background-color: var(--amber-primary); }
 
.btn-emerald { background: var(--emerald-primary); color: white; border: 0; }
.btn-rose { background: var(--rose-primary); color: white; border: 0; }

.x-small { font-size: 0.65rem; }
.fw-900 { font-weight: 900; }
.fw-1000 { font-weight: 1000; }

.shadow-2xl {
    box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
}

.premium-document-container {
    max-width: 900px;
    background: #f1f5f9;
    padding: 2rem;
    border-radius: 32px;
}

.premium-document {
    background: white;
    min-height: 1000px;
    position: relative;
    border-radius: 4px;
    font-family: 'Outfit', sans-serif;
    color: #1e293b;
}

.structural-accent-lines {
    position: absolute;
    top: 0; right: 0; bottom: 0; left: 0;
    pointer-events: none;
    background-image: 
        radial-gradient(circle at 100% 100%, #f8fafc 0%, transparent 20%),
        radial-gradient(circle at 0% 0%, #f8fafc 0%, transparent 20%);
    opacity: 0.5;
    z-index: 0;
}

.document-content {
    position: relative;
    z-index: 1;
}

/* Printing Optimization */
@media print {
    body { background: white !important; margin: 0 !important; }
    .main-sidebar, .main-navbar, .sidebar, .navbar, .btn-group, .d-print-none, .alert, .premium-document-container {
        display: none !important;
    }
    .premium-document-container {
        padding: 0 !important;
        margin: 0 !important;
        background: transparent !important;
        max-width: 100% !important;
    }
    .premium-document {
        box-shadow: none !important;
        border: none !important;
        border-radius: 0 !important;
        width: 100% !important;
        min-height: auto !important;
        margin: 0 !important;
        padding: 0 !important;
    }
    .signature-vault {
        margin-top: 100px !important;
    }
}

/* High Contrast Adjustments */
.text-erp-deep { color: var(--erp-deep); }
.bg-slate-900 { background-color: #0f172a; }
.bg-slate-50 { background-color: #f8fafc; }
.border-slate-100 { border-color: #f1f5f9; }
.border-slate-700 { border-color: #334155; }
</style>
@endsection
