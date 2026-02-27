@extends('layouts.app')
@section('title', 'Vendor Registry')

@section('content')
<div class="page-header-premium">
    <div class="row align-items-center">
        <div class="col">
            <h1 class="display-6">Vendor Registry</h1>
            <p>Onboard and manage strategic suppliers and service providers.</p>
        </div>
        <div class="col-auto">
            <a href="{{ route('inventory.vendors.create') }}" class="btn btn-erp-deep rounded-pill px-4 shadow-sm border-0">
                <i class="bi bi-person-plus-fill me-2"></i>Onboard New Vendor
            </a>
        </div>
    </div>
</div>

<div class="erp-card mb-4">
    <form action="{{ route('inventory.vendors.index') }}" method="GET" class="row g-3 align-items-end">
        <div class="col-md-10">
            <label class="erp-label">Search Partners</label>
            <div class="input-group bg-light rounded-pill overflow-hidden px-3 border-0">
                <span class="input-group-text bg-transparent border-0 text-muted"><i class="bi bi-search"></i></span>
                <input type="text" name="q" class="form-control border-0 bg-transparent py-2" placeholder="Vendor name, code, contact or email..." value="{{ $q }}">
            </div>
        </div>
        <div class="col-md-2">
            <button type="submit" class="btn btn-erp-deep rounded-pill w-100 py-2 border-0 shadow-sm fw-800">
                Search
            </button>
        </div>
    </form>
</div>

<div class="table-responsive stagger-entrance">
    <table class="table-premium">
        <thead>
            <tr>
                <th class="ps-4">Vendor Details</th>
                <th>Contact Info</th>
                <th>Financials</th>
                <th>Performance</th>
                <th class="text-end pe-4">Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($vendors as $vendor)
                <tr>
                    <td class="ps-4">
                        <div class="d-flex align-items-center gap-3">
                            <div class="bg-erp-deep text-white rounded-4 d-flex align-items-center justify-content-center flex-shrink-0 shadow-sm" 
                                 style="width: 48px; height: 48px; background: linear-gradient(45deg, #064e3b, #059669);">
                                 <i class="bi bi-building fs-5"></i>
                             </div>
                            <div>
                                <div class="fw-800 text-erp-deep fs-5 mb-0">{{ $vendor->name }}</div>
                                <div class="d-flex align-items-center gap-2">
                                    <span class="badge bg-light text-muted border-0 rounded-pill px-2 py-1 fw-800 tracking-wider" style="font-size: 0.65rem;">
                                        ID: {{ $vendor->code }}
                                    </span>
                                    @if($vendor->is_active)
                                        <span class="badge bg-success-soft text-success border-0 rounded-pill px-2 py-1 fw-800" style="font-size: 0.6rem;">ACTIVE</span>
                                    @else
                                        <span class="badge bg-danger-soft text-danger border-0 rounded-pill px-2 py-1 fw-800" style="font-size: 0.6rem;">INACTIVE</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </td>
                    <td>
                        <div class="fw-bold text-dark">{{ $vendor->contact_person ?: 'No Primary Contact' }}</div>
                        <div class="text-muted small"><i class="bi bi-envelope-at me-1"></i> {{ $vendor->email ?: 'N/A' }}</div>
                        <div class="text-muted small"><i class="bi bi-telephone me-1"></i> {{ $vendor->phone ?: 'N/A' }}</div>
                    </td>
                    <td>
                        <div class="text-muted x-small text-uppercase fw-800 mb-1">TIN / VAT</div>
                        <div class="fw-bold">{{ $vendor->tax_id ?: 'Not Registered' }}</div>
                        <div class="text-muted small text-uppercase fw-800 mt-1">Terms: {{ $vendor->payment_terms ?: 'Universal' }}</div>
                    </td>
                    <td>
                        <div class="d-flex align-items-center gap-1 text-warning">
                            @for($i=1; $i<=5; $i++)
                                <i class="bi {{ $i <= floor($vendor->rating) ? 'bi-star-fill' : 'bi-star' }}"></i>
                            @endfor
                        </div>
                        <div class="text-muted x-small fw-800 mt-1 text-uppercase">{{ $vendor->category ?: 'General Supplier' }}</div>
                    </td>
                    <td class="text-end pe-4">
                        <div class="d-flex justify-content-end gap-2">
                            <a href="{{ route('inventory.vendors.edit', $vendor) }}" 
                               class="btn btn-sm btn-white rounded-pill px-3 py-2 fw-bold shadow-sm" 
                               title="Edit Metadata">
                                <i class="bi bi-pencil-square"></i>
                            </a>
                            
                            <form action="{{ route('inventory.vendors.destroy', $vendor) }}" 
                                  method="POST" class="d-inline" id="delete-form-{{ $vendor->id }}">
                                @csrf
                                @method('DELETE')
                                <button type="button" class="btn btn-sm btn-outline-danger rounded-pill px-3 py-2 fw-bold" 
                                        onclick="premiumConfirm('Remove Vendor', 'Are you sure you want to remove {{ $vendor->name }} from the registry?', 'delete-form-{{ $vendor->id }}', '{{ $vendor->name }}')">
                                    <i class="bi bi-trash3"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" class="text-center py-5">
                        <div class="text-muted fw-800 py-5">
                            <i class="bi bi-truck display-1 mb-3 d-block opacity-10"></i>
                            No vendors registered in the procurement system.
                        </div>
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

@if($vendors->hasPages())
    <div class="mt-4">
        {{ $vendors->links() }}
    </div>
@endif
@endsection
