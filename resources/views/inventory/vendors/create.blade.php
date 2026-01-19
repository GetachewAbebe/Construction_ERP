@extends('layouts.app')
@section('title', 'Onboard New Vendor')

@section('content')
<div class="page-header-premium mb-4">
    <div class="row align-items-center">
        <div class="col">
            <h1 class="display-6">Onboard Partner</h1>
            <p>Register a new supplier or service provider in the enterprise registry.</p>
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
            <form action="{{ route('inventory.vendors.store') }}" method="POST">
                @csrf
                
                <h5 class="fw-800 text-erp-deep mb-4 border-bottom pb-2">
                    <i class="bi bi-info-circle me-2"></i>Basic Information
                </h5>
                <div class="row g-4 mb-5">
                    <div class="col-md-8">
                        <label for="name" class="erp-label">Legal Name / Enterprise Entity</label>
                        <input type="text" name="name" id="name" value="{{ old('name') }}" 
                               class="erp-input @error('name') is-invalid @enderror" 
                               placeholder="e.g. Acme Construction Supplies Plc" required>
                        @error('name')
                            <div class="invalid-feedback fw-bold">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-4">
                        <label for="category" class="erp-label">Partner Category</label>
                        <select name="category" id="category" class="erp-input">
                            <option value="">General Supplier</option>
                            <option value="Raw Materials">Raw Materials</option>
                            <option value="Machinery & Tools">Machinery & Tools</option>
                            <option value="Safety Gear">Safety Gear</option>
                            <option value="Services">Services (Sub-contractor)</option>
                            <option value="Logistics">Logistics & Transport</option>
                        </select>
                    </div>

                    <div class="col-md-6">
                        <label for="tax_id" class="erp-label">Tax Identification Number (TIN)</label>
                        <input type="text" name="tax_id" id="tax_id" value="{{ old('tax_id') }}" 
                               class="erp-input" placeholder="e.g. 0012345678">
                    </div>

                    <div class="col-md-6">
                        <label for="payment_terms" class="erp-label">Default Payment Terms</label>
                        <select name="payment_terms" id="payment_terms" class="erp-input">
                            <option value="Immediate">Immediate / COD</option>
                            <option value="Net 15">Net 15 Days</option>
                            <option value="Net 30">Net 30 Days</option>
                            <option value="Net 60">Net 60 Days</option>
                            <option value="Milestone">Milestone Based</option>
                        </select>
                    </div>
                </div>

                <h5 class="fw-800 text-erp-deep mb-4 border-bottom pb-2">
                    <i class="bi bi-person-lines-fill me-2"></i>Communication & Logistics
                </h5>
                <div class="row g-4 mb-4">
                    <div class="col-md-6">
                        <label for="contact_person" class="erp-label">Primary Contact Person</label>
                        <input type="text" name="contact_person" id="contact_person" value="{{ old('contact_person') }}" 
                               class="erp-input" placeholder="e.g. John Doe">
                    </div>

                    <div class="col-md-6">
                        <label for="email" class="erp-label">Corporate Email Address</label>
                        <input type="email" name="email" id="email" value="{{ old('email') }}" 
                               class="erp-input" placeholder="e.g. sales@vendor.com">
                    </div>

                    <div class="col-md-6">
                        <label for="phone" class="erp-label">Primary Phone / Mobile</label>
                        <input type="text" name="phone" id="phone" value="{{ old('phone') }}" 
                               class="erp-input" placeholder="e.g. +251 911 ...">
                    </div>

                    <div class="col-12">
                        <label for="address" class="erp-label">Registered Site Address</label>
                        <textarea name="address" id="address" rows="3" 
                                  class="erp-input" placeholder="Full physical location details..."></textarea>
                    </div>
                </div>

                <div class="mt-5 border-top pt-4 text-end">
                    <button type="submit" class="btn btn-erp-deep rounded-pill px-5 py-3 shadow-sm border-0 fw-800">
                        <i class="bi bi-check-circle-fill me-2"></i>Official Registration
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
