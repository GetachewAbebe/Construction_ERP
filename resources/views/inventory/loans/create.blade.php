@extends('layouts.app')

@section('title', 'Request Loan | Natanem Engineering')

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
        border-color: #10b981;
        background: white;
        box-shadow: 0 0 0 4px rgba(16, 185, 129, 0.1);
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
        <div class="col-lg-8">
            {{-- Header --}}
            <div class="d-flex align-items-center mb-4">
                <a href="{{ route('inventory.loans.index') }}" class="btn btn-white shadow-sm rounded-circle p-2 me-3">
                    <i class="bi bi-arrow-left fs-5"></i>
                </a>
                <div>
                    <h2 class="fw-800 text-erp-deep mb-1">New Item Loan</h2>
                    <p class="text-muted mb-0">Record a tool or material loan request for an employee.</p>
                </div>
            </div>

            <div class="glass-form-card p-4 p-md-5">
                <form action="{{ route('inventory.loans.store') }}" method="POST">
                    @csrf
                    
                    {{-- Item Selection --}}
                    <div class="mb-4">
                        <label class="form-label-modern">Inventory Item</label>
                        <div class="input-group-modern d-flex align-items-center">
                            <span class="input-group-text-modern"><i class="bi bi-box"></i></span>
                            <select name="inventory_item_id" class="form-select @error('inventory_item_id') is-invalid @enderror" required>
                                <option value="" disabled {{ old('inventory_item_id') ? '' : 'selected' }}>Select item to loan...</option>
                                @foreach($items as $item)
                                    <option value="{{ $item->id }}" {{ old('inventory_item_id') == $item->id ? 'selected' : '' }}>
                                        {{ $item->item_no }} – {{ $item->name ?? $item->description }} (Available: {{ $item->quantity }})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        @error('inventory_item_id') <div class="text-danger small mt-1 px-2">{{ $message }}</div> @enderror
                    </div>

                    {{-- Employee Selection --}}
                    <div class="mb-4">
                        <label class="form-label-modern">Borrowing Employee</label>
                        <div class="input-group-modern d-flex align-items-center">
                            <span class="input-group-text-modern"><i class="bi bi-person-check"></i></span>
                            <select name="employee_id" class="form-select @error('employee_id') is-invalid @enderror" required>
                                <option value="" disabled {{ old('employee_id') ? '' : 'selected' }}>Who is borrowing the item?</option>
                                @foreach($employees as $employee)
                                    <option value="{{ $employee->id }}" {{ old('employee_id') == $employee->id ? 'selected' : '' }}>
                                        {{ $employee->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        @error('employee_id') <div class="text-danger small mt-1 px-2">{{ $message }}</div> @enderror
                    </div>

                    <div class="row">
                        {{-- Quantity --}}
                        <div class="col-md-6 mb-4">
                            <label class="form-label-modern">Loan Quantity</label>
                            <div class="input-group-modern d-flex align-items-center">
                                <span class="input-group-text-modern"><i class="bi bi-bar-chart-steps"></i></span>
                                <input type="number" name="quantity" min="1" class="form-control @error('quantity') is-invalid @enderror" value="{{ old('quantity', 1) }}" required>
                            </div>
                            @error('quantity') <div class="text-danger small mt-1 px-2">{{ $message }}</div> @enderror
                        </div>

                        {{-- Due Date --}}
                        <div class="col-md-6 mb-4">
                            <label class="form-label-modern">Expected Return Date</label>
                            <div class="input-group-modern d-flex align-items-center">
                                <span class="input-group-text-modern"><i class="bi bi-calendar-event"></i></span>
                                <input type="date" name="due_date" class="form-control @error('due_date') is-invalid @enderror" value="{{ old('due_date') }}">
                            </div>
                            @error('due_date') <div class="text-danger small mt-1 px-2">{{ $message }}</div> @enderror
                        </div>
                    </div>

                    {{-- Notes --}}
                    <div class="mb-4">
                        <label class="form-label-modern">Purpose & Remarks</label>
                        <div class="input-group-modern d-flex align-items-start">
                            <span class="input-group-text-modern mt-2"><i class="bi bi-pencil-square"></i></span>
                            <textarea name="notes" rows="3" class="form-control @error('notes') is-invalid @enderror" placeholder="State the purpose or site location...">{{ old('notes') }}</textarea>
                        </div>
                        @error('notes') <div class="text-danger small mt-1 px-2">{{ $message }}</div> @enderror
                    </div>

                    <div class="mt-5">
                        <button type="submit" class="btn btn-success btn-lg rounded-pill px-5 w-100 fw-bold py-3 shadow-lg">
                            <i class="bi bi-clipboard-check me-2"></i> Submit Loan Request
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
