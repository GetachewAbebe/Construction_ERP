@extends('layouts.app')

@section('title', 'Edit Item | Natanem Engineering')

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
        <h1 class="h3 mb-1 fw-800 text-erp-deep">Edit Item</h1>
        <p class="text-muted mb-0">Edit details for item: <span class="fw-bold text-primary">{{ $item->item_no }}</span>.</p>
    </div>
    <div class="col-auto">
        <a href="{{ route('inventory.items.index') }}" class="btn btn-white rounded-pill px-4 py-2 shadow-sm border border-light fw-700">
            <i class="bi bi-arrow-left me-2"></i>Back to Inventory
        </a>
    </div>
</div>

<div class="stagger-entrance">
    <div class="card bg-white border-0 overflow-hidden shadow-sm rounded-4">
            <div class="card-body p-4 p-md-5">
                <form action="{{ route('inventory.items.update', $item) }}" method="POST">
                    @csrf
                    @method('PUT')
                    
                    {{-- Section 1: Item Details --}}
                    <div class="mb-5">
                        <div class="d-flex align-items-center gap-3 mb-4">
                            <div class="section-icon-box">
                                <i class="bi bi-pen"></i>
                            </div>
                            <div>
                                <h5 class="fw-800 text-erp-deep mb-0">Item Details</h5>
                                <p class="text-muted small mb-0">Basic information about the item.</p>
                            </div>
                        </div>

                        <div class="row g-4">
                            {{-- Item No --}}
                            <div class="col-md-4">
                                <label class="input-label-premium text-uppercase">SKU / Code</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border border-end-0 text-muted"><i class="bi bi-hash"></i></span>
                                    <input type="text" name="item_no" 
                                           class="form-control py-3 px-4 shadow-sm rounded-end-4 form-premium-input @error('item_no') is-invalid @enderror" 
                                           value="{{ old('item_no', $item->item_no) }}" required>
                                </div>
                                <div class="form-text x-small ps-1 text-muted">Unique code for the item.</div>
                                @error('item_no') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                            </div>

                            {{-- Name --}}
                            <div class="col-md-8">
                                <label class="input-label-premium text-uppercase">Item Name</label>
                                <input type="text" name="name" 
                                       class="form-control rounded-4 py-3 px-4 shadow-sm form-premium-input @error('name') is-invalid @enderror" 
                                       value="{{ old('name', $item->name) }}" required>
                                <div class="form-text x-small ps-1 text-muted">Full name of the item.</div>
                                @error('name') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                            </div>

                            {{-- Description --}}
                            <div class="col-12">
                                <label class="input-label-premium text-uppercase">Description</label>
                                <textarea name="description" rows="3" 
                                          class="form-control rounded-4 py-3 px-4 shadow-sm form-premium-input @error('description') is-invalid @enderror" 
                                          placeholder="Item description, specs, or notes...">{{ old('description', $item->description) }}</textarea>
                                @error('description') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                            </div>
                        </div>
                    </div>

                    {{-- Section 2: Stock & Valuation --}}
                    <div class="mb-5 py-4 border-top border-light">
                        <div class="d-flex align-items-center gap-3 mb-4">
                            <div class="section-icon-box" style="background: var(--erp-primary);">
                                <i class="bi bi-graph-up-arrow"></i>
                            </div>
                            <div>
                                <h5 class="fw-800 text-erp-deep mb-0">Stock & Value</h5>
                                <p class="text-muted small mb-0">Inventory levels and pricing information.</p>
                            </div>
                        </div>

                        <div class="row g-4">
                            <div class="col-md-6">
                                <label class="input-label-premium text-uppercase">Item Category</label>
                                <select name="classification_id" class="form-select rounded-4 py-3 px-4 shadow-sm form-premium-input @error('classification_id') is-invalid @enderror">
                                    <option value="">-- Select Category --</option>
                                    @foreach($classifications as $cl)
                                        <option value="{{ $cl->id }}" @selected(old('classification_id', $item->classification_id) == $cl->id)>
                                            {{ $cl->name }} ({{ $cl->code }})
                                        </option>
                                    @endforeach
                                </select>
                                @error('classification_id') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                            </div>

                            <div class="col-md-6 col-lg-3">
                                <label class="input-label-premium text-uppercase">Unit of Measure</label>
                                <input type="text" name="unit_of_measurement" 
                                       class="form-control rounded-4 py-3 px-4 shadow-sm form-premium-input @error('unit_of_measurement') is-invalid @enderror" 
                                       value="{{ old('unit_of_measurement', $item->unit_of_measurement) }}" placeholder="e.g. Pcs, Kg, Liters" required>
                                @error('unit_of_measurement') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                            </div>

                            <div class="col-md-6 col-lg-3">
                                <label class="input-label-premium text-uppercase">Quantity</label>
                                <input type="number" name="quantity" 
                                       class="form-control rounded-4 py-3 px-4 shadow-sm form-premium-input @error('quantity') is-invalid @enderror" 
                                       value="{{ old('quantity', $item->quantity) }}" min="0" required>
                                <div class="form-text x-small ps-1 text-muted">Current stock level.</div>
                                @error('quantity') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                            </div>
                            
                            <div class="col-md-6 col-lg-3">
                                <label class="input-label-premium text-uppercase">Supplier / Vendor</label>
                                <select name="vendor_id" class="form-select rounded-4 py-3 px-4 shadow-sm form-premium-input @error('vendor_id') is-invalid @enderror">
                                    <option value="">-- No Specific Vendor --</option>
                                    @foreach($vendors as $cl)
                                        <option value="{{ $cl->id }}" @selected(old('vendor_id', $item->vendor_id) == $cl->id)>
                                            {{ $cl->name }} ({{ $cl->code }})
                                        </option>
                                    @endforeach
                                </select>
                                @error('vendor_id') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                            </div>
                            
                            <div class="col-md-6 col-lg-3">
                                <label class="input-label-premium text-uppercase">Unit Price (ETB)</label>
                                <div class="input-group">
                                    <input type="number" step="0.01" name="unit_price" 
                                           class="form-control py-3 px-4 shadow-sm rounded-start-4 form-premium-input @error('unit_price') is-invalid @enderror" 
                                           value="{{ old('unit_price', $item->unit_price) }}" placeholder="0.00">
                                    <span class="input-group-text bg-light fw-bold text-muted border border-start-0">ETB</span>
                                </div>
                                @error('unit_price') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                            </div>

                            <div class="col-12">
                                <div class="p-4 rounded-4 bg-light-soft border border-light">
                                    <div class="row align-items-center">
                                        <div class="col-md-8">
                                            <label class="fw-800 text-erp-deep mb-1">Low Stock Alert Level</label>
                                            <p class="small text-muted mb-md-0">Set a minimum quantity to trigger reorder notifications.</p>
                                        </div>
                                        <div class="col-md-4">
                                            <input type="number" name="reorder_point" 
                                                   class="form-control rounded-4 py-3 px-4 shadow-sm form-premium-input text-center fw-bold @error('reorder_point') is-invalid @enderror" 
                                                   value="{{ old('reorder_point', $item->reorder_point) }}" min="0">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="d-flex gap-3 justify-content-end pt-4 border-top border-light">
                        <a href="{{ route('inventory.items.index') }}" class="btn btn-light rounded-pill px-4 py-3 fw-bold text-muted">Cancel</a>
                        <button type="submit" class="btn btn-erp-deep rounded-pill px-5 py-3 fw-800 shadow-lg">
                            <i class="bi bi-save-fill me-2"></i>Save Changes
                        </button>
                    </div>
                </form>
            </div>
    </div>
</div>
@endsection
