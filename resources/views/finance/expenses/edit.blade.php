@extends('layouts.app')
@section('title', 'Edit Expense')

@section('content')
<div class="row align-items-center mb-4 stagger-entrance">
    <div class="col">
        <h1 class="h3 mb-1 fw-800 text-erp-deep">Edit Expense</h1>
        <p class="text-muted mb-0">Edit details of the expense.</p>
    </div>
    <div class="col-auto">
        <a href="{{ route('finance.expenses.index') }}" class="btn btn-white rounded-pill px-4 shadow-sm border-0">
            <i class="bi bi-arrow-left me-2"></i>Back to Expenses
        </a>
    </div>
</div>

<div class="stagger-entrance">
    <div class="card border-0 bg-white shadow-sm">
        <div class="card-body p-4 p-md-5">
                <form action="{{ route('finance.expenses.update', $expense) }}" method="POST">
                    @csrf
                    @method('PUT')
                    
                    <div class="mb-5">
                        <h5 class="fw-800 text-erp-deep mb-4 d-flex align-items-center gap-2">
                            <i class="bi bi-building text-primary"></i>
                            Project
                        </h5>
                        
                        <div class="row g-4">
                            <div class="col-12">
                                <label class="form-label small fw-800 text-muted text-uppercase">Project</label>
                                <select name="project_id" class="form-select border-0 bg-light-soft rounded-4 py-3 px-4 shadow-sm @error('project_id') is-invalid @enderror" required>
                                    @foreach($projects as $p)
                                        <option value="{{ $p->id }}" @selected(old('project_id', $expense->project_id) == $p->id)>{{ $p->name }}</option>
                                    @endforeach
                                </select>
                                @error('project_id') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                            </div>
                        </div>
                    </div>

                    <div class="mb-5">
                        <h5 class="fw-800 text-erp-deep mb-4 d-flex align-items-center gap-2">
                            <i class="bi bi-receipt-cutoff text-success"></i>
                            Expense Details
                        </h5>
                        
                        <div class="row g-4">
                            <div class="col-md-6">
                                <label class="form-label small fw-800 text-muted text-uppercase">Category</label>
                                <select name="category" class="form-select border-0 bg-light-soft rounded-4 py-3 px-4 shadow-sm @error('category') is-invalid @enderror" required>
                                    <option value="materials" @selected(old('category', $expense->category) == 'materials')>Materials & Supplies</option>
                                    <option value="labor" @selected(old('category', $expense->category) == 'labor')>Labor & Wages</option>
                                    <option value="transport" @selected(old('category', $expense->category) == 'transport')>Logistics & Transport</option>
                                    <option value="equipment" @selected(old('category', $expense->category) == 'equipment')>Tools & Equipment</option>
                                    <option value="utility" @selected(old('category', $expense->category) == 'utility')>Utilities</option>
                                    <option value="other" @selected(old('category', $expense->category) == 'other')>Other</option>
                                </select>
                                @error('category') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label small fw-800 text-muted text-uppercase">Amount (ETB)</label>
                                <input type="number" step="0.01" name="amount" 
                                       class="form-control border-0 bg-light-soft rounded-4 py-3 px-4 shadow-sm @error('amount') is-invalid @enderror" 
                                       value="{{ old('amount', $expense->amount) }}" 
                                       required>
                                @error('amount') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label small fw-800 text-muted text-uppercase">Date</label>
                                <input type="date" name="expense_date" 
                                       class="form-control border-0 bg-light-soft rounded-4 py-3 px-4 shadow-sm @error('expense_date') is-invalid @enderror" 
                                       value="{{ old('expense_date', optional($expense->expense_date)->format('Y-m-d')) }}" 
                                       required>
                                @error('expense_date') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label small fw-800 text-muted text-uppercase">Reference No.</label>
                                <input type="text" name="reference_no" 
                                       class="form-control border-0 bg-light-soft rounded-4 py-3 px-4 shadow-sm" 
                                       value="{{ old('reference_no', $expense->reference_no) }}" 
                                       placeholder="e.g. INV-99901">
                            </div>
                        </div>
                    </div>

                    <div class="mb-5">
                        <h5 class="fw-800 text-erp-deep mb-4 d-flex align-items-center gap-2">
                            <i class="bi bi-file-text text-info"></i>
                            Description
                        </h5>
                        
                        <div class="row g-4">
                            <div class="col-12">
                                <label class="form-label small fw-800 text-muted text-uppercase">Description</label>
                                <textarea name="description" rows="4" 
                                          class="form-control border-0 bg-light-soft rounded-4 py-3 px-4 shadow-sm @error('description') is-invalid @enderror" 
                                          placeholder="Description of the expense...">{{ old('description', $expense->description) }}</textarea>
                                @error('description') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                            </div>
                        </div>
                    </div>

                    <div class="d-flex gap-3 justify-content-end pt-4 border-top border-light">
                        <a href="{{ route('finance.expenses.index') }}" class="btn btn-white rounded-pill px-4 py-3 fw-700 shadow-sm border-0">
                            <i class="bi bi-x-circle me-2"></i>Cancel
                        </a>
                        <button type="submit" class="btn btn-erp-deep rounded-pill px-5 py-3 fw-800 shadow-lg border-0">
                            <i class="bi bi-check2-circle me-2"></i>Save Changes
                        </button>
                    </div>
                </form>
        </div>
    </div>
</div>
@endsection
