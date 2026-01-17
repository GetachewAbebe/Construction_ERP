@extends('layouts.app')
@section('title', 'Record Expense')

@section('content')
<div class="row align-items-center mb-4 stagger-entrance">
    <div class="col">
        <h1 class="h3 mb-1 fw-800 text-erp-deep">Record Expense</h1>
        <p class="text-muted mb-0">Account for project-level spending with precision.</p>
    </div>
    <div class="col-auto">
        <a href="{{ route('finance.expenses.index') }}" class="btn btn-white rounded-pill px-4 shadow-sm border-0">
            <i class="bi bi-arrow-left me-2"></i>Back to Ledger
        </a>
    </div>
</div>

<div class="stagger-entrance">
    <div class="card border-0 bg-white shadow-sm">
        <div class="card-body p-4 p-md-5">
                <form action="{{ route('finance.expenses.store') }}" method="POST">
                    @csrf
                    
                    <div class="mb-5">
                        <h5 class="fw-800 text-erp-deep mb-4 d-flex align-items-center gap-2">
                            <i class="bi bi-building text-primary"></i>
                            Project Context
                        </h5>
                        
                        <div class="row g-4">
                            <div class="col-12">
                                <label class="form-label small fw-800 text-muted text-uppercase">Target Project</label>
                                <select name="project_id" class="form-select border-0 bg-light-soft rounded-4 py-3 px-4 shadow-sm @error('project_id') is-invalid @enderror" required>
                                    <option value="" disabled {{ old('project_id') ? '' : 'selected' }}>Select project site...</option>
                                    @foreach($projects as $p)
                                        <option value="{{ $p->id }}" {{ old('project_id') == $p->id ? 'selected' : '' }}>{{ $p->name }}</option>
                                    @endforeach
                                </select>
                                <small class="text-muted fw-bold mt-2 d-block">Cost center for this expenditure.</small>
                                @error('project_id') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                            </div>
                        </div>
                    </div>

                    <div class="mb-5">
                        <h5 class="fw-800 text-erp-deep mb-4 d-flex align-items-center gap-2">
                            <i class="bi bi-receipt-cutoff text-success"></i>
                            Transaction Details
                        </h5>
                        
                        <div class="row g-4">
                            <div class="col-md-6">
                                <label class="form-label small fw-800 text-muted text-uppercase">Expense Category</label>
                                <select name="category" class="form-select border-0 bg-light-soft rounded-4 py-3 px-4 shadow-sm @error('category') is-invalid @enderror" required>
                                    <option value="materials" @selected(old('category') == 'materials')>Materials & Supplies</option>
                                    <option value="labor" @selected(old('category') == 'labor')>Labor & Wages</option>
                                    <option value="transport" @selected(old('category') == 'transport')>Logistics & Transport</option>
                                    <option value="equipment" @selected(old('category') == 'equipment')>Tools & Equipment</option>
                                    <option value="utility" @selected(old('category') == 'utility')>Utilities</option>
                                    <option value="other" @selected(old('category') == 'other')>Other</option>
                                </select>
                                @error('category') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label small fw-800 text-muted text-uppercase">Spent Amount (ETB)</label>
                                <input type="number" step="0.01" name="amount" 
                                       class="form-control border-0 bg-light-soft rounded-4 py-3 px-4 shadow-sm @error('amount') is-invalid @enderror" 
                                       value="{{ old('amount') }}" 
                                       placeholder="0.00" 
                                       required>
                                @error('amount') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label small fw-800 text-muted text-uppercase">Transaction Date</label>
                                <input type="date" name="expense_date" 
                                       class="form-control border-0 bg-light-soft rounded-4 py-3 px-4 shadow-sm @error('expense_date') is-invalid @enderror" 
                                       value="{{ old('expense_date', date('Y-m-d')) }}" 
                                       required>
                                @error('expense_date') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label small fw-800 text-muted text-uppercase">Invoice / Reference #</label>
                                <input type="text" name="reference_no" 
                                       class="form-control border-0 bg-light-soft rounded-4 py-3 px-4 shadow-sm @error('reference_no') is-invalid @enderror" 
                                       value="{{ old('reference_no') }}" 
                                       placeholder="e.g. INV-1029">
                                @error('reference_no') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                            </div>
                        </div>
                    </div>

                    <div class="mb-5">
                        <h5 class="fw-800 text-erp-deep mb-4 d-flex align-items-center gap-2">
                            <i class="bi bi-file-text text-info"></i>
                            Justification
                        </h5>
                        
                        <div class="row g-4">
                            <div class="col-12">
                                <label class="form-label small fw-800 text-muted text-uppercase">Detailed Remarks</label>
                                <textarea name="description" rows="4" 
                                          class="form-control border-0 bg-light-soft rounded-4 py-3 px-4 shadow-sm @error('description') is-invalid @enderror" 
                                          placeholder="Provide a brief explanation for this expenditure...">{{ old('description') }}</textarea>
                                @error('description') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                            </div>
                        </div>
                    </div>

                    <div class="d-flex gap-3 justify-content-end pt-4 border-top border-light">
                        <a href="{{ route('finance.expenses.index') }}" class="btn btn-white rounded-pill px-4 py-3 fw-700 shadow-sm border-0">
                            <i class="bi bi-x-circle me-2"></i>Cancel
                        </a>
                        <button type="submit" class="btn btn-erp-deep rounded-pill px-5 py-3 fw-800 shadow-lg border-0">
                            <i class="bi bi-check2-circle me-2"></i>Commit Expenditure
                        </button>
                    </div>
                </form>
            </div>
        </div>
</div>
@endsection
