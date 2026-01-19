@extends('layouts.app')
@section('title', 'Update Vendor Metadata')

@section('content')
<div class="page-header-premium mb-4">
    <div class="row align-items-center">
        <div class="col">
            <h1 class="display-6">Update Partner: {{ $vendor->name }}</h1>
            <p>Reconfigure technical and financial metadata for this enterprise partner.</p>
        </div>
        <div class="col-auto">
            <a href="{{ route('inventory.vendors.index') }}" class="btn btn-white rounded-pill px-4 shadow-sm border-0">
                <i class="bi bi-arrow-left me-2"></i>Back to Registry
            </a>
        </div>
    </div>
</div>

<div class="row justify-content-center stagger-entrance">
    <div class="col-lg-10">
        <div class="erp-card shadow-lg border-0">
            <form action="{{ route('inventory.vendors.update', $vendor) }}" method="POST">
                @csrf
                @method('PUT')
                
                <h5 class="fw-800 text-erp-deep mb-4 border-bottom pb-2">
                    <i class="bi bi-info-circle me-2"></i>Basic Information
                </h5>
                <div class="row g-4 mb-5">
                    <div class="col-md-8">
                        <label for="name" class="erp-label">Legal Name / Enterprise Entity</label>
                        <input type="text" name="name" id="name" value="{{ old('name', $vendor->name) }}" 
                               class="erp-input @error('name') is-invalid @enderror" required>
                        @error('name')
                            <div class="invalid-feedback fw-bold">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-4">
                        <label class="erp-label">Record Status</label>
                        <div class="form-check form-switch p-3 bg-light rounded-4 ms-0 d-flex align-items-center justify-content-between px-4" style="height: 56px;">
                            <label class="form-check-label fw-800 text-erp-deep mb-0" for="is_active">Partner Active</label>
                            <input class="form-check-input" type="checkbox" name="is_active" id="is_active" {{ $vendor->is_active ? 'checked' : '' }} value="1" style="width: 50px; height: 25px;">
                        </div>
                    </div>

                    <div class="col-md-4">
                        <label class="erp-label">Partner Category</label>
                        <select name="category" id="category" class="erp-input">
                            <option value="" @selected(!$vendor->category)>General Supplier</option>
                            <option value="Raw Materials" @selected($vendor->category == 'Raw Materials')>Raw Materials</option>
                            <option value="Machinery & Tools" @selected($vendor->category == 'Machinery & Tools')>Machinery & Tools</option>
                            <option value="Safety Gear" @selected($vendor->category == 'Safety Gear')>Safety Gear</option>
                            <option value="Services" @selected($vendor->category == 'Services')>Services (Sub-contractor)</option>
                            <option value="Logistics" @selected($vendor->category == 'Logistics')>Logistics & Transport</option>
                        </select>
                    </div>

                    <div class="col-md-4">
                        <label for="tax_id" class="erp-label">Tax ID (TIN)</label>
                        <input type="text" name="tax_id" id="tax_id" value="{{ old('tax_id', $vendor->tax_id) }}" 
                               class="erp-input" placeholder="e.g. 0012345678">
                    </div>

                    <div class="col-md-4">
                        <label for="payment_terms" class="erp-label">Payment Terms</label>
                        <select name="payment_terms" id="payment_terms" class="erp-input">
                            <option value="Immediate" @selected($vendor->payment_terms == 'Immediate')>Immediate / COD</option>
                            <option value="Net 15" @selected($vendor->payment_terms == 'Net 15')>Net 15 Days</option>
                            <option value="Net 30" @selected($vendor->payment_terms == 'Net 30')>Net 30 Days</option>
                            <option value="Net 60" @selected($vendor->payment_terms == 'Net 60')>Net 60 Days</option>
                            <option value="Milestone" @selected($vendor->payment_terms == 'Milestone')>Milestone Based</option>
                        </select>
                    </div>
                </div>

                <h5 class="fw-800 text-erp-deep mb-4 border-bottom pb-2">
                    <i class="bi bi-person-lines-fill me-2"></i>Communication & Logistics
                </h5>
                <div class="row g-4 mb-4">
                    <div class="col-md-6">
                        <label for="contact_person" class="erp-label">Primary Contact Person</label>
                        <input type="text" name="contact_person" id="contact_person" value="{{ old('contact_person', $vendor->contact_person) }}" 
                               class="erp-input">
                    </div>

                    <div class="col-md-6">
                        <label for="email" class="erp-label">Corporate Email Address</label>
                        <input type="email" name="email" id="email" value="{{ old('email', $vendor->email) }}" 
                               class="erp-input">
                    </div>

                    <div class="col-md-6">
                        <label for="phone" class="erp-label">Primary Phone / Mobile</label>
                        <input type="text" name="phone" id="phone" value="{{ old('phone', $vendor->phone) }}" 
                               class="erp-input">
                    </div>

                    <div class="col-12">
                        <label for="address" class="erp-label">Registered Site Address</label>
                        <textarea name="address" id="address" rows="3" 
                                  class="erp-input">{{ old('address', $vendor->address) }}</textarea>
                    </div>
                </div>

                <div class="mt-5 border-top pt-4 text-end">
                    <button type="submit" class="btn btn-erp-deep rounded-pill px-5 py-3 shadow-sm border-0 fw-800">
                        <i class="bi bi-save2-fill me-2"></i>Persist Configuration
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
