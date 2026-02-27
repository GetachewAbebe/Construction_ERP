@extends('layouts.app')
@section('title', 'Inventory Intel')

@section('content')
<div class="page-header-premium">
    <div class="row align-items-center">
        <div class="col">
            <h1>Stock Inventory</h1>
            <p>Unified management system for materials, tools, and site equipment.</p>
        </div>
        <div class="col-auto">
            @unless(Auth::user()->hasRole('Administrator') || Auth::user()->hasRole('Admin'))
            <a href="{{ route('inventory.items.create') }}" class="btn btn-erp-deep rounded-pill px-4 shadow-sm border-0">
                <i class="bi bi-box-seam me-2"></i>Add New Item
            </a>
            @endunless
        </div>
    </div>
</div>

{{-- Flash Messages --}}
@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show erp-card border-0 mb-4" role="alert">
        <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif
@if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show erp-card border-0 mb-4" role="alert">
        <i class="bi bi-exclamation-triangle-fill me-2"></i>{{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

{{-- Stock Quick Stats --}}
<div class="row g-4 mb-5">
    <div class="col-md-3">
        <div class="erp-card border-start border-4 border-success">
            <div class="erp-label text-uppercase mb-2">All Items</div>
            <div class="d-flex align-items-baseline gap-2">
                <div class="display-6 fw-900 text-erp-deep">{{ number_format($totals['total_items']) }}</div>
                <small class="text-muted fw-bold">MATERIALS</small>
            </div>
            <div class="mt-3">
                <span class="badge bg-success-soft text-success rounded-pill px-3 py-1 fw-700" style="font-size: 0.65rem;">
                    <i class="bi bi-check-circle-fill me-1"></i>LIVE ASSETS
                </span>
            </div>
        </div>
    </div>
    
    <div class="col-md-3">
        <div class="erp-card border-start border-4 border-danger">
            <div class="erp-label text-uppercase mb-2">Low Stock</div>
            <div class="d-flex align-items-baseline gap-2">
                <div class="display-6 fw-900 text-danger">{{ $totals['low_stock_count'] }}</div>
                <small class="text-danger fw-bold">ALERTS</small>
            </div>
            <div class="mt-3">
                <span class="badge bg-danger text-white rounded-pill px-3 py-1 fw-700" style="font-size: 0.65rem;">
                    <i class="bi bi-exclamation-triangle-fill me-1"></i>UNDER THRESHOLD
                </span>
            </div>
        </div>
    </div>
</div>

<div class="erp-card mb-4">
    <form action="{{ route('inventory.items.index') }}" method="GET" class="row g-3 align-items-end">
        <div class="col-md-3">
            <label class="erp-label">Category</label>
            <select name="classification_id" class="erp-input" style="appearance: auto;">
                <option value="">All Categories</option>
                @foreach($classifications as $cl)
                    <option value="{{ $cl->id }}" @selected(request('classification_id') == $cl->id)>{{ $cl->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-3">
            <label class="erp-label">Store Location</label>
            <select name="store_location" class="erp-input" style="appearance: auto;">
                <option value="">All Store Sites</option>
                @foreach($storeLocations as $location)
                    <option value="{{ $location }}" @selected(request('store_location') === $location)>{{ $location }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-2">
            <label class="erp-label">Stock Status</label>
            <select name="status" class="erp-input" style="appearance: auto;">
                <option value="">All Status</option>
                <option value="in_stock" @selected(request('status') === 'in_stock')>Stable Stock</option>
                <option value="low_stock" @selected(request('status') === 'low_stock')>Low Stock</option>
                <option value="out_of_stock" @selected(request('status') === 'out_of_stock')>Out of Stock</option>
            </select>
        </div>
        <div class="col-md-4">
            <label class="erp-label">Search Registry</label>
            <div class="input-group bg-light rounded-pill overflow-hidden px-3 border-0">
                <span class="input-group-text bg-transparent border-0 text-muted"><i class="bi bi-search"></i></span>
                <input type="text" name="q" class="form-control border-0 bg-transparent py-2" placeholder="Item name, ID or specs..." value="{{ request('q') }}">
            </div>
        </div>
        <div class="col-md-2">
            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-erp-deep rounded-pill px-4 flex-grow-1 border-0 shadow-sm fw-800">
                    Filter
                </button>
                <a href="{{ route('inventory.items.index') }}" 
                   class="btn btn-white border text-dark rounded-pill px-3 d-flex align-items-center justify-content-center shadow-sm" 
                   title="Reset Filters">
                    <i class="bi bi-arrow-counterclockwise"></i>
                </a>
            </div>
        </div>
    </form>
</div>

<div class="table-responsive">
    <table class="table-premium">
        <thead>
            <tr>
                <th class="ps-4">Item Catalog</th>
                <th>Store Location</th>
                <th class="text-center">Stock Level</th>
                <th>Status</th>
                <th class="text-end pe-4">Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($items as $item)
                <tr>
                    <td class="ps-4">
                        <div class="d-flex align-items-center gap-3">
                            <div class="bg-light shadow-sm rounded-3 p-2 text-primary">
                                <i class="bi bi-box-fill fs-5"></i>
                            </div>
                            <div>
                                <div class="fw-800 text-erp-deep">{{ $item->name }}</div>
                                <div class="d-flex align-items-center gap-2">
                                    <small class="text-muted fw-bold">REF: {{ $item->item_no }}</small>
                                    @if($item->classification)
                                        <span class="badge bg-erp-deep text-white border-0 rounded-pill px-2 py-0 fw-800" style="font-size: 0.65rem; background: linear-gradient(90deg, #064e3b, #059669);">
                                            <i class="bi bi-diagram-3-fill me-1"></i>{{ $item->classification->name }}
                                        </span>
                                    @endif
                                    @if($item->vendor)
                                        <span class="badge bg-light text-muted border-0 rounded-pill px-2 py-0 fw-800" style="font-size: 0.65rem;">
                                            <i class="bi bi-truck me-1"></i>{{ $item->vendor->name }}
                                        </span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </td>
                    <td>
                        <div class="d-flex align-items-center gap-2">
                            <i class="bi bi-geo-alt text-primary"></i>
                            <span class="fw-600 text-dark">{{ $item->store_location ?? 'Undefined' }}</span>
                        </div>
                    </td>
                    <td class="text-center">
                        <div class="fw-800 fs-5 text-erp-deep">{{ $item->quantity }}</div>
                        <small class="text-muted text-uppercase">{{ $item->unit_of_measurement }}</small>
                    </td>
                    <td>
                        @php
                            $qty = $item->quantity;
                            $statusClass = $qty > 5 ? 'bg-success-soft text-success' : ($qty > 0 ? 'bg-warning-soft text-warning' : 'bg-danger-soft text-danger');
                            $statusLabel = $qty > 5 ? 'Stable Stock' : ($qty > 0 ? 'Low Stock' : 'Out of Stock');
                        @endphp
                        <span class="badge {{ $statusClass }} border-0 rounded-pill px-3 py-2 fw-600">
                            {{ $statusLabel }}
                        </span>
                    </td>
                    <td class="text-end pe-4">
                        @if($item->quantity <= 0)
                            <span class="badge bg-light text-muted fw-normal rounded-pill px-3 border border-light">
                                <i class="bi bi-lock-fill me-1"></i>Historical Record
                            </span>
                        @else
                            <div class="d-flex justify-content-end gap-2">
                                <a href="{{ route('inventory.items.edit', $item) }}" 
                                   class="btn btn-sm btn-white rounded-pill px-3 py-2 fw-bold">
                                    Edit
                                </a>
                                
                                <button type="button" 
                                        class="btn btn-sm btn-outline-danger rounded-pill px-3 py-2 fw-bold"
                                        onclick="if(confirm('Are you sure you want to archive this item?')) document.getElementById('del-{{ $item->id }}').submit()">
                                    Delete
                                    <form id="del-{{ $item->id }}" action="{{ route('inventory.items.destroy', $item) }}" method="POST" class="d-none">@csrf @method('DELETE')</form>
                                </button>
                            </div>
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" class="text-center py-5">
                        <div class="opacity-25 fs-1 mb-2"><i class="bi bi-box2"></i></div>
                        <div class="text-muted">The inventory catalog is currently empty.</div>
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

@if($items->hasPages())
    <div class="mt-4">
        {{ $items->links() }}
    </div>
@endif
@endsection

