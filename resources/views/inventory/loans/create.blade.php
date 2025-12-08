@extends('layouts.app')

@section('title', 'New Inventory Loan – Natanem ERP')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-lg-7 col-md-8">

            <div class="card shadow-soft border-0">
                <div class="card-body">

                    <h1 class="h5 mb-1">New Item Loan</h1>
                    <p class="text-muted small mb-3">
                        Select an inventory item and the employee who will borrow it.
                        The request will be sent to the Administrator for approval.
                    </p>

                    @if ($errors->any())
                        <div class="alert alert-danger alert-dismissible fade show d-flex align-items-start" role="alert">
                            <div class="me-2" style="font-size: 1.5rem;">⚠️</div>
                            <div class="flex-grow-1">
                                <strong>Oops!</strong> {{ $errors->first() }}
                            </div>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    <form method="POST" action="{{ route('inventory.loans.store') }}">
                        @csrf

                        <div class="mb-3">
                            <label class="form-label">Item</label>
                            <select name="inventory_item_id" class="form-select" required>
                                <option value="">Choose item...</option>
                                @foreach ($items as $item)
                                    <option value="{{ $item->id }}"
                                        {{ old('inventory_item_id') == $item->id ? 'selected' : '' }}>
                                        {{ $item->item_no }} – {{ $item->name ?? $item->description }}
                                        (Qty: {{ $item->quantity }})
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Employee (borrower)</label>
                            <select name="employee_id" class="form-select" required>
                                <option value="">Choose employee...</option>
                                @foreach ($employees as $employee)
                                    <option value="{{ $employee->id }}"
                                        {{ old('employee_id') == $employee->id ? 'selected' : '' }}>
                                        {{ $employee->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Quantity</label>
                            <input type="number" name="quantity" min="1"
                                   class="form-control @error('quantity') is-invalid @enderror"
                                   value="{{ old('quantity', 1) }}" required>
                            @error('quantity')
                                <div class="invalid-feedback d-block">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Due date (optional)</label>
                            <input type="date" name="due_date" class="form-control"
                                   value="{{ old('due_date') }}">
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Notes (optional)</label>
                            <textarea name="notes" class="form-control" rows="3"
                                      placeholder="Purpose, site, special notes...">{{ old('notes') }}</textarea>
                        </div>

                        <div class="d-flex justify-content-between align-items-center">
                            <a href="{{ route('inventory.loans.index') }}" class="btn btn-outline-secondary">
                                Cancel
                            </a>

                            <button type="submit" class="btn btn-success">
                                Submit Loan Request
                            </button>
                        </div>
                    </form>

                </div>
            </div>

        </div>
    </div>
</div>
@endsection
