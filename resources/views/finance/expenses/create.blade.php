@extends('layouts.app')
@section('title', 'New Requisition')

@section('content')
<div class="page-header-premium mb-4">
    <div class="row align-items-center">
        <div class="col">
            <h1 class="display-6">New Financial Requisition</h1>
            <p>Log a new field expenditure for site operations and project audit.</p>
        </div>
        <div class="col-auto">
            <a href="{{ route('finance.expenses.index') }}" class="btn btn-white rounded-pill px-4 shadow-sm fw-800">
                <i class="bi bi-arrow-left me-2"></i> Requisitions
            </a>
        </div>
    </div>
</div>

<div class="erp-card p-4 p-md-5 stagger-entrance">
    <form action="{{ route('finance.expenses.store') }}" method="POST">
        @csrf
        
        <div class="mb-5">
            <div class="d-flex align-items-center gap-3 mb-4">
                <div class="bg-erp-deep text-white rounded-4 d-flex align-items-center justify-content-center" style="width: 48px; height: 48px;">
                    <i class="bi bi-building fs-5"></i>
                </div>
                <h5 class="fw-800 text-erp-deep mb-0">Project Alignment</h5>
            </div>
            
            <div class="row g-4">
                <div class="col-12">
                    <label class="form-label-premium">Target Construction Site</label>
                    <select name="project_id" class="form-select erp-input @error('project_id') is-invalid @enderror" required>
                        <option value="" disabled {{ old('project_id') ? '' : 'selected' }}>Select from active registry...</option>
                        @foreach($projects as $p)
                            <option value="{{ $p->id }}" {{ old('project_id') == $p->id ? 'selected' : '' }}>{{ $p->name }}</option>
                        @endforeach
                    </select>
                    @error('project_id') <div class="invalid-feedback d-block fw-bold ms-2">{{ $message }}</div> @enderror
                </div>
            </div>
        </div>

        <div class="mb-5">
            <div class="d-flex align-items-center gap-3 mb-4">
                <div class="bg-success text-white rounded-4 d-flex align-items-center justify-content-center" style="width: 48px; height: 48px;">
                    <i class="bi bi-receipt-cutoff fs-5"></i>
                </div>
                <h5 class="fw-800 text-erp-deep mb-0">Transaction Parameters</h5>
            </div>
            
            <div class="row g-4">
                <div class="col-md-6">
                    <label class="form-label-premium">Expense Category</label>
                    <select name="category" class="form-select erp-input @error('category') is-invalid @enderror" required>
                        <option value="materials" @selected(old('category') == 'materials')>Materials & Supplies</option>
                        <option value="labor" @selected(old('category') == 'labor')>Labor & Wages</option>
                        <option value="transport" @selected(old('category') == 'transport')>Logistics & Transport</option>
                        <option value="equipment" @selected(old('category') == 'equipment')>Tools & Equipment</option>
                        <option value="utility" @selected(old('category') == 'utility')>Utilities</option>
                        <option value="other" @selected(old('category') == 'other')>Other</option>
                    </select>
                    @error('category') <div class="invalid-feedback d-block fw-bold ms-2">{{ $message }}</div> @enderror
                </div>

                <div class="col-md-6">
                    <label class="form-label-premium">Expenditure Amount (ETB)</label>
                    <input type="number" step="0.01" name="amount" 
                           class="erp-input @error('amount') is-invalid @enderror" 
                           value="{{ old('amount') }}" 
                           placeholder="0.00" 
                           required>
                    @error('amount') <div class="invalid-feedback d-block fw-bold ms-2">{{ $message }}</div> @enderror
                </div>

                <div class="col-md-6">
                    <label class="form-label-premium">Transaction Date</label>
                    <input type="date" name="expense_date" 
                           class="erp-input @error('expense_date') is-invalid @enderror" 
                           value="{{ old('expense_date', date('Y-m-d')) }}" 
                           required>
                    @error('expense_date') <div class="invalid-feedback d-block fw-bold ms-2">{{ $message }}</div> @enderror
                </div>

                <div class="col-md-6">
                    <label class="form-label-premium">Official Reference No.</label>
                    <input type="text" name="reference_no" 
                           class="erp-input @error('reference_no') is-invalid @enderror" 
                           value="{{ old('reference_no') }}" 
                           placeholder="e.g. VOUCH-A093">
                    @error('reference_no') <div class="invalid-feedback d-block fw-bold ms-2">{{ $message }}</div> @enderror
                </div>
            </div>
        </div>

        <div class="mb-5">
            <div class="d-flex align-items-center gap-3 mb-4">
                <div class="bg-warning text-white rounded-4 d-flex align-items-center justify-content-center" style="width: 48px; height: 48px;">
                    <i class="bi bi-file-text fs-5"></i>
                </div>
                <h5 class="fw-800 text-erp-deep mb-0">Operational Details</h5>
            </div>
            
            <div class="row g-4">
                 <div class="col-12">
                    <label class="form-label-premium">Expenditure Narration / Purpose</label>
                    <textarea name="description" rows="3" 
                              class="erp-input @error('description') is-invalid @enderror" 
                              placeholder="Describe the purpose of this transaction for audit clarity...">{{ old('description') }}</textarea>
                    @error('description') <div class="invalid-feedback d-block fw-bold ms-2">{{ $message }}</div> @enderror
                </div>
            </div>
        </div>

        <div class="d-flex gap-3 justify-content-end pt-4 border-top">
            <a href="{{ route('finance.expenses.index') }}" class="btn btn-white rounded-pill px-4 py-2 fw-800 shadow-sm">
                Cancel
            </a>
            <button type="submit" class="btn btn-erp-deep rounded-pill px-5 py-2 fw-900 shadow-lg border-0">
                Log Requisition
            </button>
        </div>
    </form>
</div>
@endsection
