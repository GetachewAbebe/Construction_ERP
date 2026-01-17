@extends('layouts.app')

@section('title', 'Add Inventory Item | Natanem Engineering')

@section('content')
<style>
    .form-premium-input {
        transition: all 0.2s ease;
        border: 1.5px solid #e2e8f0 !important; /* Clearly defined border */
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
        <h1 class="h3 mb-1 fw-800 text-erp-deep">Add Inventory Item</h1>
        <p class="text-muted mb-0">Centralized material registration portal for industrial assets.</p>
    </div>
    <div class="col-auto">
        <a href="{{ route('inventory.items.index') }}" class="btn btn-white rounded-pill px-4 py-2 shadow-sm border border-light fw-700">
            <i class="bi bi-arrow-left me-2"></i>Back to Registry
        </a>
    </div>
</div>

<div class="stagger-entrance">
    <div class="card bg-white border-0 overflow-hidden shadow-sm rounded-4">
            <div class="card-body p-4 p-md-5">
                <form action="{{ route('inventory.items.store') }}" method="POST">
                    @csrf
                    
                    {{-- Section 1: Specifications --}}
                    <div class="mb-5">
                        <div class="d-flex align-items-center gap-3 mb-4">
                            <div class="section-icon-box">
                                <i class="bi bi-box-seam"></i>
                            </div>
                            <div>
                                <h5 class="fw-800 text-erp-deep mb-0">Item Specifications</h5>
                                <p class="text-muted small mb-0">Define the core identity and technical parameters of the asset.</p>
                            </div>
                        </div>

                        <div class="row g-4">
                            {{-- Item No --}}
                            <div class="col-md-4">
                                <label class="input-label-premium text-uppercase">Catalog Number (SKU)</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border border-end-0 text-muted"><i class="bi bi-hash"></i></span>
                                    <input type="text" name="item_no" 
                                           class="form-control py-3 px-4 shadow-sm rounded-end-4 form-premium-input @error('item_no') is-invalid @enderror" 
                                           value="{{ old('item_no') }}" placeholder="e.g. CAT-001" required>
                                </div>
                                <div class="form-text x-small ps-1 text-muted">Unique identifier for stock tracking.</div>
                                @error('item_no') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                            </div>

                            {{-- Name --}}
                            <div class="col-md-8">
                                <label class="input-label-premium text-uppercase">Item Name</label>
                                <input type="text" name="name" 
                                       class="form-control rounded-4 py-3 px-4 shadow-sm form-premium-input @error('name') is-invalid @enderror" 
                                       value="{{ old('name') }}" placeholder="e.g. Electrical Mixer 750 liter" required>
                                <div class="form-text x-small ps-1 text-muted">Primary name used in logs and reports.</div>
                                @error('name') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                            </div>

                            {{-- Description --}}
                            <div class="col-12">
                                <label class="input-label-premium text-uppercase">Technical Description</label>
                                <textarea name="description" rows="3" 
                                          class="form-control rounded-4 py-3 px-4 shadow-sm form-premium-input @error('description') is-invalid @enderror" 
                                          placeholder="Enter detailed specifications, model numbers, or usage guidelines...">{{ old('description') }}</textarea>
                                @error('description') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                            </div>
                        </div>
                    </div>

                    {{-- Section 2: Logistics --}}
                    <div class="mb-5 py-4 border-top border-light">
                        <div class="d-flex align-items-center gap-3 mb-4">
                            <div class="section-icon-box" style="background: var(--erp-primary);">
                                <i class="bi bi-layers"></i>
                            </div>
                            <div>
                                <h5 class="fw-800 text-erp-deep mb-0">Stock Logistics</h5>
                                <p class="text-muted small mb-0">Manage quantity, placement, and intake scheduling.</p>
                            </div>
                        </div>

                        <div class="row g-4">
                            {{-- Unit of Measurement --}}
                            <div class="col-md-3">
                                <label class="input-label-premium text-uppercase">Unit (UoM)</label>
                                <select name="unit_of_measurement" class="form-select rounded-4 py-3 px-4 shadow-sm form-premium-input @error('unit_of_measurement') is-invalid @enderror" required>
                                    <option value="" disabled {{ old('unit_of_measurement') ? '' : 'selected' }}>Select Unit...</option>
                                    <option value="pcs" {{ old('unit_of_measurement') === 'pcs' ? 'selected' : '' }}>Pieces (pcs)</option>
                                    <option value="kg" {{ old('unit_of_measurement') === 'kg' ? 'selected' : '' }}>Kilograms (kg)</option>
                                    <option value="m" {{ old('unit_of_measurement') === 'm' ? 'selected' : '' }}>Meters (m)</option>
                                    <option value="box" {{ old('unit_of_measurement') === 'box' ? 'selected' : '' }}>Boxes</option>
                                    <option value="liters" {{ old('unit_of_measurement') === 'liters' ? 'selected' : '' }}>Litres (L)</option>
                                </select>
                                @error('unit_of_measurement') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                            </div>

                            {{-- Quantity --}}
                            <div class="col-md-3">
                                <label class="input-label-premium text-uppercase">Initial Quantity</label>
                                <div class="input-group">
                                    <input type="number" min="0" name="quantity" 
                                           class="form-control rounded-start-4 py-3 px-4 shadow-sm form-premium-input @error('quantity') is-invalid @enderror" 
                                           value="{{ old('quantity') }}" placeholder="0" required>
                                    <span class="input-group-text bg-light border border-start-0 text-muted small fw-bold"><i class="bi bi-plus-slash-minus"></i></span>
                                </div>
                                @error('quantity') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                            </div>

                            {{-- Store Location --}}
                            <div class="col-md-3">
                                <label class="input-label-premium text-uppercase">Store Location</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border border-end-0 text-muted"><i class="bi bi-geo-alt"></i></span>
                                    <input type="text" name="store_location" 
                                           class="form-control py-3 px-4 shadow-sm rounded-end-4 form-premium-input @error('store_location') is-invalid @enderror" 
                                           value="{{ old('store_location') }}" placeholder="e.g. Site B" required>
                                </div>
                                @error('store_location') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                            </div>

                            {{-- In Date --}}
                            <div class="col-md-3">
                                <label class="input-label-premium text-uppercase">Arrival Date</label>
                                <input type="date" name="in_date" 
                                       class="form-control rounded-4 py-3 px-4 shadow-sm form-premium-input @error('in_date') is-invalid @enderror" 
                                       value="{{ old('in_date', date('Y-m-d')) }}" required>
                                @error('in_date') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                            </div>
                        </div>
                    </div>

                    <div class="d-flex gap-3 justify-content-end pt-4 border-top border-light">
                        <a href="{{ route('inventory.items.index') }}" class="btn btn-white rounded-pill px-4 py-3 fw-700 shadow-sm border border-light">
                            <i class="bi bi-x-circle me-2"></i>Cancel
                        </a>
                        <button type="submit" class="btn btn-erp-deep rounded-pill px-5 py-3 fw-800 shadow-sm border-0"
                                onclick="this.disabled=true; this.innerText='Processing...'; this.form.submit();">
                            <i class="bi bi-save-fill me-2"></i>Register Item
                        </button>
                    </div>
                </form>
            </div>
    </div>
</div>
@endsection
