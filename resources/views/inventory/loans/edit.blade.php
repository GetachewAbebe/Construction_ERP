@extends('layouts.app')

@section('title', 'Edit Loan Request | Natanem Engineering')

@section('content')
<div class="row align-items-center mb-4 stagger-entrance">
    <div class="col">
        <h1 class="h3 mb-1 fw-800 text-erp-deep">Edit Loan Request</h1>
        <p class="text-muted mb-0">Modify parameters for pending asset assignment.</p>
    </div>
    <div class="col-auto">
        <a href="{{ route('inventory.loans.index') }}" class="btn btn-white rounded-pill px-4 shadow-sm border-0">
            <i class="bi bi-arrow-left me-2"></i>Return to Registry
        </a>
    </div>
</div>

<div class="row justify-content-center stagger-entrance">
    <div class="col-lg-8">
        <div class="card hardened-glass border-0 overflow-hidden shadow-lg">
            <div class="card-body p-4 p-md-5">
                <form action="{{ route('inventory.loans.update', $loan) }}" method="POST">
                    @csrf
                    @method('PUT')
                    
                    <div class="mb-5">
                        <h5 class="fw-800 text-erp-deep mb-4 d-flex align-items-center gap-2">
                            <i class="bi bi-box-seam text-primary"></i>
                            Asset Assignment
                        </h5>

                        <div class="row g-4">
                            {{-- Item Selection --}}
                            <div class="col-12">
                                <label class="form-label small fw-800 text-muted text-uppercase">Select Asset / Item</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light-soft border-0"><i class="bi bi-box"></i></span>
                                    <select name="inventory_item_id" class="form-select border-0 bg-light-soft py-3 px-4 shadow-sm rounded-end-4 @error('inventory_item_id') is-invalid @enderror" required>
                                        <option value="" disabled>Choose an item...</option>
                                        @foreach($items as $item)
                                            <option value="{{ $item->id }}" {{ old('inventory_item_id', $loan->inventory_item_id) == $item->id ? 'selected' : '' }}>
                                                {{ $item->item_no }} – {{ $item->name }} (In Stock: {{ $item->quantity }})
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-text small text-warning"><i class="bi bi-exclamation-triangle me-1"></i>Changing the item will validate stock availability again.</div>
                                @error('inventory_item_id') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                            </div>

                            {{-- Employee Selection --}}
                            <div class="col-12">
                                <label class="form-label small fw-800 text-muted text-uppercase">Assign to Personnel</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light-soft border-0"><i class="bi bi-person-badge"></i></span>
                                    <select name="employee_id" class="form-select border-0 bg-light-soft py-3 px-4 shadow-sm rounded-end-4 @error('employee_id') is-invalid @enderror" required>
                                        <option value="" disabled>Select employee...</option>
                                        @foreach($employees as $employee)
                                            <option value="{{ $employee->id }}" {{ old('employee_id', $loan->employee_id) == $employee->id ? 'selected' : '' }}>
                                                {{ $employee->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                @error('employee_id') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                            </div>
                        </div>
                    </div>

                    <div class="mb-5">
                        <h5 class="fw-800 text-erp-deep mb-4 d-flex align-items-center gap-2">
                            <i class="bi bi-calendar-event text-success"></i>
                            Loan Terms
                        </h5>

                        <div class="row g-4">
                            {{-- Quantity --}}
                            <div class="col-md-6">
                                <label class="form-label small fw-800 text-muted text-uppercase">Units to Issue</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light-soft border-0"><i class="bi bi-123"></i></span>
                                    <input type="number" name="quantity" min="1" 
                                           class="form-control border-0 bg-light-soft py-3 px-4 shadow-sm rounded-end-4 @error('quantity') is-invalid @enderror" 
                                           value="{{ old('quantity', $loan->quantity) }}" required>
                                </div>
                                @error('quantity') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                            </div>

                            {{-- Due Date --}}
                            <div class="col-md-6">
                                <label class="form-label small fw-800 text-muted text-uppercase">Expected Return by</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light-soft border-0"><i class="bi bi-calendar-date"></i></span>
                                    <input type="date" name="due_date" 
                                           class="form-control border-0 bg-light-soft py-3 px-4 shadow-sm rounded-end-4 @error('due_date') is-invalid @enderror" 
                                           value="{{ old('due_date', optional($loan->due_date)->format('Y-m-d')) }}">
                                </div>
                                @error('due_date') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                            </div>

                            {{-- Notes --}}
                            <div class="col-12 mt-4">
                                <label class="form-label small fw-800 text-muted text-uppercase">Purpose / Location Remarks</label>
                                <textarea name="notes" rows="3" 
                                          class="form-control border-0 bg-light-soft rounded-4 py-3 px-4 shadow-sm @error('notes') is-invalid @enderror" 
                                          placeholder="Specify the site location or project usage...">{{ old('notes', $loan->notes) }}</textarea>
                                @error('notes') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                            </div>
                        </div>
                    </div>

                    <div class="d-flex gap-3 justify-content-end pt-4 border-top border-light">
                        <a href="{{ route('inventory.loans.index') }}" class="btn btn-white rounded-pill px-4 py-3 fw-700 shadow-sm border-0">
                            <i class="bi bi-x-circle me-2"></i>Discard Changes
                        </a>
                        <button type="submit" class="btn btn-erp-deep rounded-pill px-5 py-3 fw-800 shadow-lg border-0">
                            <i class="bi bi-check2-circle me-2"></i>Save Updates
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
