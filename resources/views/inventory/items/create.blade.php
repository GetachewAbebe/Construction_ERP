@extends('layouts.app')

@section('title', 'Add Inventory Item | Natanem Engineering')

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
        border-color: #0d9488;
        background: white;
        box-shadow: 0 0 0 4px rgba(13, 148, 136, 0.1);
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
            <div class="d-flex align-items-center justify-content-between mb-4">
                <div class="d-flex align-items-center">
                    <a href="{{ route('inventory.items.index') }}" class="btn btn-white shadow-sm rounded-circle p-2 me-3">
                        <i class="bi bi-arrow-left fs-5"></i>
                    </a>
                    <div>
                        <h2 class="fw-800 text-erp-deep mb-1">Add Inventory Item</h2>
                        <p class="text-muted mb-0">Register industrial materials or tools into the central store.</p>
                    </div>
                </div>
            </div>

            <div class="glass-form-card p-4 p-md-5">
                <form action="{{ route('inventory.items.store') }}" method="POST">
                    @csrf
                    
                    <div class="row">
                        {{-- Item No --}}
                        <div class="col-md-4 mb-4">
                            <label class="form-label-modern">Item Catalog #</label>
                            <div class="input-group-modern d-flex align-items-center">
                                <span class="input-group-text-modern"><i class="bi bi-hash"></i></span>
                                <input type="text" name="item_no" class="form-control @error('item_no') is-invalid @enderror" value="{{ old('item_no') }}" placeholder="e.g. CAT-001" required>
                            </div>
                            @error('item_no') <div class="text-danger small mt-1 px-2">{{ $message }}</div> @enderror
                        </div>

                        {{-- Name --}}
                        <div class="col-md-8 mb-4">
                            <label class="form-label-modern">Item Name</label>
                            <div class="input-group-modern d-flex align-items-center">
                                <span class="input-group-text-modern"><i class="bi bi-box-seam"></i></span>
                                <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name') }}" placeholder="e.g. Electrical Mixer 750 liter" required>
                            </div>
                            @error('name') <div class="text-danger small mt-1 px-2">{{ $message }}</div> @enderror
                        </div>
                    </div>

                    {{-- Description --}}
                    <div class="mb-4">
                        <label class="form-label-modern">Technical Specifications / Description</label>
                        <div class="input-group-modern d-flex align-items-start">
                            <span class="input-group-text-modern mt-2"><i class="bi bi-card-text"></i></span>
                            <textarea name="description" rows="2" class="form-control @error('description') is-invalid @enderror" placeholder="Optional details about the item...">{{ old('description') }}</textarea>
                        </div>
                        @error('description') <div class="text-danger small mt-1 px-2">{{ $message }}</div> @enderror
                    </div>

                    <div class="row">
                        {{-- Unit of Measurement --}}
                        <div class="col-md-4 mb-4">
                            <label class="form-label-modern">Unit of Measure</label>
                            <div class="input-group-modern d-flex align-items-center">
                                <span class="input-group-text-modern"><i class="bi bi-rulers"></i></span>
                                <input type="text" name="unit_of_measurement" class="form-control @error('unit_of_measurement') is-invalid @enderror" value="{{ old('unit_of_measurement') }}" placeholder="e.g. pcs / kg / box" required>
                            </div>
                            @error('unit_of_measurement') <div class="text-danger small mt-1 px-2">{{ $message }}</div> @enderror
                        </div>

                        {{-- Quantity --}}
                        <div class="col-md-4 mb-4">
                            <label class="form-label-modern">Initial Quantity</label>
                            <div class="input-group-modern d-flex align-items-center">
                                <span class="input-group-text-modern"><i class="bi bi-bar-chart-steps"></i></span>
                                <input type="number" min="0" name="quantity" class="form-control @error('quantity') is-invalid @enderror" value="{{ old('quantity') }}" required>
                            </div>
                            @error('quantity') <div class="text-danger small mt-1 px-2">{{ $message }}</div> @enderror
                        </div>

                        {{-- Store Location --}}
                        <div class="col-md-4 mb-4">
                            <label class="form-label-modern">Storage Location</label>
                            <div class="input-group-modern d-flex align-items-center">
                                <span class="input-group-text-modern"><i class="bi bi-crosshair"></i></span>
                                <input type="text" name="store_location" class="form-control @error('store_location') is-invalid @enderror" value="{{ old('store_location') }}" placeholder="e.g. Waserbi Site B" required>
                            </div>
                            @error('store_location') <div class="text-danger small mt-1 px-2">{{ $message }}</div> @enderror
                        </div>
                    </div>

                    <div class="row">
                        {{-- In Date --}}
                        <div class="col-md-4 mb-4">
                            <label class="form-label-modern">Arrival / In Date</label>
                            <div class="input-group-modern d-flex align-items-center">
                                <span class="input-group-text-modern"><i class="bi bi-calendar-check"></i></span>
                                <input type="date" name="in_date" class="form-control @error('in_date') is-invalid @enderror" value="{{ old('in_date', date('Y-m-d')) }}" required>
                            </div>
                            @error('in_date') <div class="text-danger small mt-1 px-2">{{ $message }}</div> @enderror
                        </div>
                    </div>

                    <div class="mt-5 d-flex gap-3">
                        <button type="submit" class="btn btn-erp-deep btn-lg rounded-pill px-5 flex-grow-1 fw-bold py-3 shadow-lg"
                                onclick="this.disabled=true; this.innerText='Saving Record...'; this.form.submit();">
                            <i class="bi bi-save me-2"></i> Register Item
                        </button>
                        <a href="{{ route('inventory.items.index') }}" class="btn btn-outline-secondary btn-lg rounded-pill px-5 py-3 fw-bold">
                            Cancel
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
