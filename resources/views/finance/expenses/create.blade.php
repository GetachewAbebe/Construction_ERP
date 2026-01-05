@extends('layouts.app')

@section('title', 'Record Expense | Natanem Engineering')

@push('head')
<style>
    .form-container {
        padding: 3rem 0;
    }
    .glass-form-card {
        background: rgba(255, 255, 255, 0.95);
        backdrop-filter: blur(15px);
        border: 1px solid rgba(255, 255, 255, 0.4);
        border-radius: 24px;
        box-shadow: 0 15px 35px rgba(0,0,0,0.05);
    }
    .input-group-modern {
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        border-radius: 12px;
        transition: all 0.3s ease;
    }
    .input-group-modern:focus-within {
        border-color: #f43f5e;
        background: white;
        box-shadow: 0 0 0 4px rgba(244, 63, 94, 0.1);
    }
    .input-group-modern .form-control, 
    .input-group-modern .form-select {
        border: none;
        background: transparent;
        padding: 0.75rem 1rem;
        font-weight: 500;
    }
    .input-group-modern .form-control:focus, 
    .input-group-modern .form-select:focus {
        box-shadow: none;
    }
    .input-group-text-modern {
        background: transparent;
        border: none;
        color: #64748b;
        padding-left: 1.25rem;
    }
    .form-label-modern {
        font-weight: 700;
        color: #334155;
        margin-bottom: 0.5rem;
        display: block;
        font-size: 0.85rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
</style>
@endpush

@section('content')
<div class="form-container container">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            {{-- Header --}}
            <div class="d-flex align-items-center mb-4">
                <a href="{{ route('finance.expenses.index') }}" class="btn btn-white shadow-sm rounded-circle p-2 me-3">
                    <i class="bi bi-arrow-left fs-5"></i>
                </a>
                <div>
                    <h2 class="fw-800 text-erp-deep mb-1">Record Expense</h2>
                    <p class="text-muted mb-0">Account for project-level spending with precision.</p>
                </div>
            </div>

            <div class="glass-form-card p-4 p-md-5">
                <form action="{{ route('finance.expenses.store') }}" method="POST">
                    @csrf
                    
                    {{-- Project Selection --}}
                    <div class="mb-4">
                        <label class="form-label-modern">Target Project</label>
                        <div class="input-group-modern d-flex align-items-center">
                            <span class="input-group-text-modern"><i class="bi bi-diagram-3"></i></span>
                            <select name="project_id" class="form-select @error('project_id') is-invalid @enderror" required>
                                <option value="" disabled {{ old('project_id') ? '' : 'selected' }}>Select project site...</option>
                                @foreach($projects as $p)
                                    <option value="{{ $p->id }}" {{ old('project_id') == $p->id ? 'selected' : '' }}>{{ $p->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        @error('project_id') <div class="text-danger small mt-1 px-2">{{ $message }}</div> @enderror
                    </div>

                    <div class="row">
                        {{-- Category --}}
                        <div class="col-md-6 mb-4">
                            <label class="form-label-modern">Expense Category</label>
                            <div class="input-group-modern d-flex align-items-center">
                                <span class="input-group-text-modern"><i class="bi bi-tags"></i></span>
                                <select name="category" class="form-select @error('category') is-invalid @enderror" required>
                                    <option value="materials" {{ old('category') == 'materials' ? 'selected' : '' }}>Materials</option>
                                    <option value="labor" {{ old('category') == 'labor' ? 'selected' : '' }}>Labor & Wages</option>
                                    <option value="transport" {{ old('category') == 'transport' ? 'selected' : '' }}>Logistics / Transport</option>
                                    <option value="equipment" {{ old('category') == 'equipment' ? 'selected' : '' }}>Tool Rentals / Purchase</option>
                                    <option value="utility" {{ old('category') == 'utility' ? 'selected' : '' }}>Utilities (Power/Water)</option>
                                    <option value="other" {{ old('category') == 'other' ? 'selected' : '' }}>Miscellaneous</option>
                                </select>
                            </div>
                            @error('category') <div class="text-danger small mt-1 px-2">{{ $message }}</div> @enderror
                        </div>

                        {{-- Amount --}}
                        <div class="col-md-6 mb-4">
                            <label class="form-label-modern">Spent Amount (ETB)</label>
                            <div class="input-group-modern d-flex align-items-center">
                                <span class="input-group-text-modern"><i class="bi bi-cash-stack"></i></span>
                                <input type="number" step="0.01" name="amount" class="form-control @error('amount') is-invalid @enderror" value="{{ old('amount') }}" placeholder="0.00" required>
                            </div>
                            @error('amount') <div class="text-danger small mt-1 px-2">{{ $message }}</div> @enderror
                        </div>
                    </div>

                    <div class="row">
                        {{-- Date --}}
                        <div class="col-md-6 mb-4">
                            <label class="form-label-modern">Transaction Date</label>
                            <div class="input-group-modern d-flex align-items-center">
                                <span class="input-group-text-modern"><i class="bi bi-calendar-event"></i></span>
                                <input type="date" name="expense_date" class="form-control @error('expense_date') is-invalid @enderror" value="{{ old('expense_date', date('Y-m-d')) }}" required>
                            </div>
                            @error('expense_date') <div class="text-danger small mt-1 px-2">{{ $message }}</div> @enderror
                        </div>

                        {{-- Reference --}}
                        <div class="col-md-6 mb-4">
                            <label class="form-label-modern">Invoice / Reference #</label>
                            <div class="input-group-modern d-flex align-items-center">
                                <span class="input-group-text-modern"><i class="bi bi-hash"></i></span>
                                <input type="text" name="reference_no" class="form-control @error('reference_no') is-invalid @enderror" value="{{ old('reference_no') }}" placeholder="e.g. INV-1029">
                            </div>
                            @error('reference_no') <div class="text-danger small mt-1 px-2">{{ $message }}</div> @enderror
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="form-label-modern">Justification / Remarks</label>
                        <div class="input-group-modern d-flex align-items-start">
                            <span class="input-group-text-modern mt-2"><i class="bi bi-chat-left-text"></i></span>
                            <textarea name="description" rows="3" class="form-control @error('description') is-invalid @enderror" placeholder="Provide a brief explanation for this expenditure...">{{ old('description') }}</textarea>
                        </div>
                        @error('description') <div class="text-danger small mt-1 px-2">{{ $message }}</div> @enderror
                    </div>

                    <div class="mt-5">
                        <button type="submit" class="btn btn-dark btn-lg rounded-pill px-5 w-100 fw-bold py-3 shadow-lg" style="background: #1e293b;">
                            <i class="bi bi-check2-circle me-2"></i> Commit Expenditure
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
