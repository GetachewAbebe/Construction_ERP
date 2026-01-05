@extends('layouts.app')

@section('title', 'Edit Expense | Natanem Engineering')

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
        font-size: 0.9rem;
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
        font-size: 0.8rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
</style>
@endpush

@section('content')
<div class="form-container container">
    <div class="row justify-content-center">
        <div class="col-lg-10">
            {{-- Header --}}
            <div class="d-flex align-items-center mb-4">
                <a href="{{ route('finance.expenses.index') }}" class="btn btn-white shadow-sm rounded-circle p-2 me-3">
                    <i class="bi bi-arrow-left fs-5"></i>
                </a>
                <div>
                    <h2 class="fw-800 text-erp-deep mb-1">Edit Expense Record</h2>
                    <p class="text-muted mb-0">Update financial entry details for auditing.</p>
                </div>
            </div>

            <div class="glass-form-card p-4 p-md-5">
                <form action="{{ route('finance.expenses.update', $expense) }}" method="POST">
                    @csrf
                    @method('PUT')
                    
                    <div class="row">
                        {{-- Project --}}
                        <div class="col-md-6 mb-4">
                            <label class="form-label-modern">Charge to Project</label>
                            <div class="input-group-modern d-flex align-items-center">
                                <span class="input-group-text-modern"><i class="bi bi-diagram-3"></i></span>
                                <select name="project_id" class="form-select @error('project_id') is-invalid @enderror" required>
                                    @foreach($projects as $p)
                                        <option value="{{ $p->id }}" {{ old('project_id', $expense->project_id) == $p->id ? 'selected' : '' }}>{{ $p->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            @error('project_id') <div class="text-danger small mt-1 px-2">{{ $message }}</div> @enderror
                        </div>

                        {{-- Category --}}
                        <div class="col-md-6 mb-4">
                            <label class="form-label-modern">Expense Category</label>
                            <div class="input-group-modern d-flex align-items-center">
                                <span class="input-group-text-modern"><i class="bi bi-tags"></i></span>
                                <select name="category" class="form-select @error('category') is-invalid @enderror" required>
                                    @foreach(['Material Purchase', 'Labor Cost', 'Equipment Rental', 'Transport', 'Administrative', 'Miscellaneous'] as $cat)
                                        <option value="{{ $cat }}" {{ old('category', $expense->category) == $cat ? 'selected' : '' }}>{{ $cat }}</option>
                                    @endforeach
                                </select>
                            </div>
                            @error('category') <div class="text-danger small mt-1 px-2">{{ $message }}</div> @enderror
                        </div>

                        {{-- Amount --}}
                        <div class="col-md-6 mb-4">
                            <label class="form-label-modern">Disbursement Amount (ETB)</label>
                            <div class="input-group-modern d-flex align-items-center">
                                <span class="input-group-text-modern"><i class="bi bi-cash-stack"></i></span>
                                <input type="number" step="0.01" name="amount" class="form-control @error('amount') is-invalid @enderror" value="{{ old('amount', $expense->amount) }}" required>
                            </div>
                            @error('amount') <div class="text-danger small mt-1 px-2">{{ $message }}</div> @enderror
                        </div>

                        {{-- Date --}}
                        <div class="col-md-6 mb-4">
                            <label class="form-label-modern">Expense Date</label>
                            <div class="input-group-modern d-flex align-items-center">
                                <span class="input-group-text-modern"><i class="bi bi-calendar-event"></i></span>
                                <input type="date" name="expense_date" class="form-control @error('expense_date') is-invalid @enderror" value="{{ old('expense_date', optional($expense->expense_date)->format('Y-m-d')) }}" required>
                            </div>
                            @error('expense_date') <div class="text-danger small mt-1 px-2">{{ $message }}</div> @enderror
                        </div>

                        {{-- Reference --}}
                        <div class="col-md-12 mb-4">
                            <label class="form-label-modern">Reference # (Receipt/Invoice)</label>
                            <div class="input-group-modern d-flex align-items-center">
                                <span class="input-group-text-modern"><i class="bi bi-receipt"></i></span>
                                <input type="text" name="reference_no" class="form-control" value="{{ old('reference_no', $expense->reference_no) }}" placeholder="e.g. INV-99901">
                            </div>
                        </div>

                        {{-- Description --}}
                        <div class="col-md-12 mb-4">
                            <label class="form-label-modern">Transaction Details</label>
                            <div class="input-group-modern d-flex align-items-start">
                                <span class="input-group-text-modern mt-2"><i class="bi bi-chat-left-text"></i></span>
                                <textarea name="description" rows="3" class="form-control" placeholder="What specifically was this for?">{{ old('description', $expense->description) }}</textarea>
                            </div>
                        </div>
                    </div>

                    <div class="mt-5 d-flex gap-3">
                        <button type="submit" class="btn btn-dark btn-lg rounded-pill px-5 flex-grow-1 fw-bold py-3 shadow-lg">
                            <i class="bi bi-check2-circle me-2"></i> Save Modifications
                        </button>
                        <a href="{{ route('finance.expenses.index') }}" class="btn btn-outline-secondary btn-lg rounded-pill px-5 py-3 fw-bold">
                            Cancel
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
