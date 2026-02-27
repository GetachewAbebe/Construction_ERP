@extends('layouts.app')

@section('title', 'Request Asset Loan | Natanem Engineering')

@section('content')
<style>
    .form-premium-input {
        transition: all 0.2s ease;
        border: 1.5px solid #e2e8f0 !important;
        background-color: #ffffff !important;
    }
    .form-premium-input:focus {
        background: #fff !important;
        border-color: var(--erp-primary) !important;
        box-shadow: 0 0 0 4px rgba(5, 150, 105, 0.1) !important;
    }
    .section-icon-box {
        width: 42px;
        height: 42px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 12px;
        background: var(--erp-deep);
        color: white;
        font-size: 1.2rem;
    }
    .input-label-premium {
        font-family: 'Outfit', sans-serif;
        font-weight: 700;
        color: var(--erp-deep);
        font-size: 0.75rem;
        letter-spacing: 0.05em;
        margin-bottom: 0.6rem;
    }
</style>

<div class="row align-items-center mb-5">
    <div class="col">
        <h1 class="h3 mb-1 fw-800 text-erp-deep">Request Asset Loan</h1>
        <p class="text-muted mb-0">Record a tool or material loan request for personnel assignment.</p>
    </div>
    <div class="col-auto">
        <a href="{{ route('inventory.loans.index') }}" class="btn btn-white rounded-pill px-4 py-2 shadow-sm border border-light fw-700">
            <i class="bi bi-arrow-left me-2"></i>Back to Registry
        </a>
    </div>
</div>

<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="card bg-white border-0 overflow-hidden shadow-sm rounded-4">
            <div class="card-body p-4 p-md-5">
                <form action="{{ route('inventory.loans.store') }}" method="POST">
                    @csrf
                    
                    {{-- Section 1: Assignment --}}
                    <div class="mb-5">
                        <div class="d-flex align-items-center gap-3 mb-4">
                            <div class="section-icon-box">
                                <i class="bi bi-box-seam"></i>
                            </div>
                            <div>
                                <h5 class="fw-800 text-erp-deep mb-0">Asset Assignment</h5>
                                <p class="text-muted small mb-0">Identify the item and the personnel responsible for the loan.</p>
                            </div>
                        </div>

                        <div class="row g-4">
                            {{-- Item Selection --}}
                            <div class="col-12">
                                <label class="input-label-premium text-uppercase">Select Asset / Item</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border border-end-0 text-muted"><i class="bi bi-box"></i></span>
                                    <select name="inventory_item_id" class="form-select rounded-end-4 py-3 px-4 shadow-sm form-premium-input @error('inventory_item_id') is-invalid @enderror" required>
                                        <option value="" disabled {{ old('inventory_item_id') ? '' : 'selected' }}>Choose an item from available stock...</option>
                                        @foreach($items as $item)
                                            <option value="{{ $item->id }}" {{ old('inventory_item_id') == $item->id ? 'selected' : '' }}>
                                                {{ $item->item_no }} – {{ $item->name }} (In Stock: {{ $item->quantity }})
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-text x-small ps-1 text-muted">Only items currently in stock are available for assignment.</div>
                                @error('inventory_item_id') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                            </div>

                            {{-- Employee Selection --}}
                            <div class="col-12">
                                <label class="input-label-premium text-uppercase">Assign to Personnel</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border border-end-0 text-muted"><i class="bi bi-person-badge"></i></span>
                                    <select name="employee_id" class="form-select rounded-end-4 py-3 px-4 shadow-sm form-premium-input @error('employee_id') is-invalid @enderror" required>
                                        <option value="" disabled {{ old('employee_id') ? '' : 'selected' }}>Select employee...</option>
                                        @foreach($employees as $employee)
                                            <option value="{{ $employee->id }}" {{ old('employee_id') == $employee->id ? 'selected' : '' }}>
                                                {{ $employee->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                @error('employee_id') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                            </div>
                        </div>
                    </div>

                    {{-- Section 2: Terms --}}
                    <div class="mb-5 py-4 border-top border-light">
                        <div class="d-flex align-items-center gap-3 mb-4">
                            <div class="section-icon-box" style="background: var(--erp-primary);">
                                <i class="bi bi-calendar-event"></i>
                            </div>
                            <div>
                                <h5 class="fw-800 text-erp-deep mb-0">Loan Terms</h5>
                                <p class="text-muted small mb-0">Specify quantity and expected recovery timeline.</p>
                            </div>
                        </div>

                        <div class="row g-4">
                            {{-- Quantity --}}
                            <div class="col-md-6">
                                <label class="input-label-premium text-uppercase">Units to Issue</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border border-end-0 text-muted"><i class="bi bi-123"></i></span>
                                    <input type="number" name="quantity" min="1" 
                                           class="form-control rounded-end-4 py-3 px-4 shadow-sm form-premium-input @error('quantity') is-invalid @enderror" 
                                           value="{{ old('quantity', 1) }}" required>
                                </div>
                                @error('quantity') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                            </div>

                            {{-- Due Date --}}
                            <div class="col-md-6">
                                <label class="input-label-premium text-uppercase">Expected Return by</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border border-end-0 text-muted"><i class="bi bi-calendar-date"></i></span>
                                    <input type="date" name="due_date" 
                                           class="form-control rounded-end-4 py-3 px-4 shadow-sm form-premium-input @error('due_date') is-invalid @enderror" 
                                           value="{{ old('due_date') }}">
                                </div>
                                <div class="form-text x-small ps-1 text-muted">Leave blank if return date is indefinite.</div>
                                @error('due_date') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                            </div>

                            {{-- Notes --}}
                            <div class="col-12 mt-4">
                                <label class="input-label-premium text-uppercase">Purpose / Location Remarks</label>
                                <textarea name="notes" rows="3" 
                                          class="form-control rounded-4 py-3 px-4 shadow-sm form-premium-input @error('notes') is-invalid @enderror" 
                                          placeholder="Specify the site location or project usage...">{{ old('notes') }}</textarea>
                                @error('notes') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                            </div>
                        </div>
                    </div>

                    <div class="d-flex gap-3 justify-content-end pt-4 border-top border-light">
                        <a href="{{ route('inventory.loans.index') }}" class="btn btn-white rounded-pill px-4 py-3 fw-700 shadow-sm border border-light">
                            <i class="bi bi-x-circle me-2"></i>Cancel
                        </a>
                        <button type="submit" class="btn btn-erp-deep rounded-pill px-5 py-3 fw-800 shadow-sm border-0"
                                onclick="this.disabled=true; this.innerText='Processing...'; this.form.submit();">
                            <i class="bi bi-send-fill me-2"></i>Submit Request
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
