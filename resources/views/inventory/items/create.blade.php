@extends('layouts.app')

@section('title', 'Register New Item | Natanem Engineering')

@push('head')
<style>
    /* Scoped minimalist styles for this form */
    .form-section-title {
        font-family: 'Outfit', sans-serif;
        font-weight: 800;
        color: var(--erp-deep);
        margin-bottom: 1.5rem;
        padding-bottom: 1rem;
        border-bottom: 2px solid var(--erp-border);
    }
</style>
@endpush

@section('content')
<div class="page-header-premium mb-5">
    <div class="row align-items-center">
        <div class="col">
            <h1 class="display-3 fw-900 text-erp-deep mb-0 tracking-tight">Register New Item</h1>
        </div>
        <div class="col-auto">
            <a href="{{ route('inventory.items.index') }}" class="btn btn-white rounded-pill px-4 py-3 fw-700 shadow-sm border-0">
                <i class="bi bi-arrow-left me-2"></i>Back to Stock Registry
            </a>
        </div>
    </div>
</div>

<div class="row justify-content-center">
    <div class="col-lg-10">
        <form action="{{ route('inventory.items.store') }}" method="POST">
            @csrf
            
            {{-- Technical Specifications Card --}}
            <div class="erp-card mb-4">
                <h5 class="form-section-title">
                    <i class="bi bi-cpu text-primary me-2"></i>
                    Technical Specifications
                </h5>

                <div class="row g-4">
                    {{-- SKU / Code --}}
                    <div class="col-md-4">
                        <label class="erp-label">SKU / Identification Code</label>
                        <input type="text" name="item_no" 
                               class="erp-input @error('item_no') is-invalid @enderror" 
                               value="{{ old('item_no') }}" placeholder="e.g. CAT-202X-001" required>
                        @error('item_no') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                    </div>

                    {{-- Item Name --}}
                    <div class="col-md-8">
                        <label class="erp-label">Item Designation</label>
                        <input type="text" name="name" 
                               class="erp-input @error('name') is-invalid @enderror" 
                               value="{{ old('name') }}" placeholder="e.g. Industrial Mixer Model X" required>
                        @error('name') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                    </div>

                    {{-- Description --}}
                    <div class="col-12">
                        <label class="erp-label">Technical Description</label>
                        <textarea name="description" rows="4" 
                                  class="erp-input @error('description') is-invalid @enderror" 
                                  placeholder="Detailed specifications, dimensions, and operational notes..." style="resize: vertical; min-height: 100px;">{{ old('description') }}</textarea>
                        @error('description') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                    </div>
                </div>
            </div>

            {{-- Logistics & Valuation Card --}}
            <div class="erp-card mb-4">
                <h5 class="form-section-title">
                    <i class="bi bi-bar-chart-steps text-success me-2"></i>
                    Logistics & Valuation
                </h5>

                <div class="row g-4">
                     {{-- Category --}}
                     <div class="col-md-6">
                        <label class="erp-label">Resource Classification</label>
                        <select name="classification_id" class="erp-input @error('classification_id') is-invalid @enderror">
                            <option value="">Select classification...</option>
                            @foreach($classifications as $item)
                                <option value="{{ $item->id }}" @selected(old('classification_id') == $item->id)>
                                    {{ $item->name }} ({{ $item->code }})
                                </option>
                            @endforeach
                        </select>
                        @error('classification_id') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                    </div>

                    {{-- Vendor --}}
                    <div class="col-md-6">
                        <label class="erp-label">Primary Vendor</label>
                        <select name="vendor_id" class="erp-input @error('vendor_id') is-invalid @enderror">
                            <option value="">Select vendor...</option>
                            @foreach($vendors as $vendor)
                                <option value="{{ $vendor->id }}" @selected(old('vendor_id') == $vendor->id)>
                                    {{ $vendor->name }} ({{ $vendor->code }})
                                </option>
                            @endforeach
                        </select>
                        @error('vendor_id') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                    </div>

                    {{-- Initial Quantity --}}
                    <div class="col-md-4">
                        <label class="erp-label">Initial Quantity</label>
                        <input type="number" name="quantity" 
                               class="erp-input @error('quantity') is-invalid @enderror" 
                               value="{{ old('quantity', 0) }}" min="0" required>
                        @error('quantity') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                    </div>

                    {{-- UoM --}}
                    <div class="col-md-4">
                        <label class="erp-label">Unit of Measure</label>
                        <input type="text" name="unit_of_measurement" 
                               class="erp-input @error('unit_of_measurement') is-invalid @enderror" 
                               value="{{ old('unit_of_measurement') }}" placeholder="e.g. PCS, KG, LTR" required>
                        @error('unit_of_measurement') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                    </div>

                    {{-- Unit Price --}}
                    <div class="col-md-4">
                        <label class="erp-label">Unit Value (ETB)</label>
                        <input type="number" step="0.01" name="unit_price" 
                               class="erp-input @error('unit_price') is-invalid @enderror" 
                               value="{{ old('unit_price') }}" placeholder="0.00">
                        @error('unit_price') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                    </div>

                    {{-- Reorder Point --}}
                    <div class="col-12">
                        <div class="p-4 rounded-4 bg-gradient-to-r from-gray-50 to-gray-100 border border-light d-flex align-items-center justify-content-between" style="background: rgba(243, 244, 246, 0.5);">
                            <div>
                                <label class="fw-800 text-erp-deep mb-1 d-block">Low Stock Threshold</label>
                                <span class="small text-muted">Minimum quantity to trigger replenishment alerts.</span>
                            </div>
                            <div style="width: 120px;">
                                <input type="number" name="reorder_point" 
                                       class="erp-input text-center fw-bold @error('reorder_point') is-invalid @enderror" 
                                       value="{{ old('reorder_point', 10) }}" min="0">
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="d-flex gap-3 justify-content-end">
                <a href="{{ route('inventory.items.index') }}" class="btn btn-white rounded-pill px-5 py-3 fw-700 shadow-sm border-0">
                    <i class="bi bi-x-circle me-2"></i>Cancel
                </a>
                <button type="submit" class="btn btn-erp-deep rounded-pill px-5 py-3 fw-900 shadow-xl border-0">
                    <i class="bi bi-plus-lg me-2 fs-5"></i>REGISTER ITEM
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
