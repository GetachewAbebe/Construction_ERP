@extends('layouts.app')
@section('title', 'Inventory Intel')

@section('content')
<div class="row align-items-center mb-4 stagger-entrance">
    <div class="col">
        <h1 class="h3 mb-1 fw-800 text-erp-deep">Stock Inventory</h1>
        <p class="text-muted mb-0">Unified management system for materials, tools, and site equipment.</p>
    </div>
    <div class="col-auto">
        @unless(Auth::user()->hasRole('Administrator'))
        <a href="{{ route('inventory.items.create') }}" class="btn btn-erp-deep rounded-pill px-4 shadow-sm border-0">
            <i class="bi bi-box-seam me-2"></i>Provision New Item
        </a>
        @endunless
    </div>
</div>

{{-- Flash Messages --}}
@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show hardened-glass border-0 shadow-sm stagger-entrance mb-4" role="alert">
        <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif
@if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show hardened-glass border-0 shadow-sm stagger-entrance mb-4" role="alert">
        <i class="bi bi-exclamation-triangle-fill me-2"></i>{{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

{{-- Stock Quick Stats --}}
<style>
    .analytic-card-premium {
        background: #ffffff;
        border: 1px solid #edf2f7;
        border-left: 4px solid var(--erp-deep);
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }
    .analytic-card-premium:hover {
        box-shadow: 0 10px 20px -10px rgba(0,0,0,0.1);
        transform: translateY(-2px);
    }
    .analytic-label {
        font-family: 'Outfit', sans-serif;
        font-size: 0.7rem;
        font-weight: 700;
        letter-spacing: 0.08em;
        color: #718096;
    }
    .analytic-value {
        font-family: 'Outfit', sans-serif;
        font-size: 1.75rem;
        font-weight: 800;
        color: var(--erp-deep);
        line-height: 1;
    }
</style>

<div class="row g-4 mb-5">
    <div class="col-md-3">
        <div class="card analytic-card-premium shadow-sm rounded-3 h-100 border-0">
            <div class="card-body p-4 border-start border-4 border-success">
                <div class="analytic-label text-uppercase mb-2">All Items</div>
                <div class="d-flex align-items-baseline gap-2">
                    <div class="analytic-value">{{ number_format($totals['total_items']) }}</div>
                    <small class="text-muted fw-bold">MATERIALS</small>
                </div>
                <div class="mt-3">
                    <span class="badge bg-success-soft text-success rounded-pill px-3 py-1 fw-700" style="font-size: 0.65rem;">
                        <i class="bi bi-check-circle-fill me-1"></i>LIVE ASSETS
                    </span>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-3">
        <div class="card analytic-card-premium shadow-sm rounded-3 h-100 border-0">
            <div class="card-body p-4 border-start border-4 border-danger">
                <div class="analytic-label text-uppercase mb-2">Low Stock</div>
                <div class="d-flex align-items-baseline gap-2">
                    <div class="analytic-value text-danger">{{ $totals['low_stock_count'] }}</div>
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
</div>

<div class="card bg-white border-0 shadow-sm rounded-4 mb-4">
    <div class="card-body p-0">
        <form action="{{ route('inventory.items.index') }}" method="GET" class="p-4 bg-light-soft border-bottom">
            <div class="row g-3 align-items-end">
                <div class="col-md-4">
                    <label class="form-label small fw-800 text-erp-deep text-uppercase">Search Registry</label>
                    <div class="input-group bg-white rounded-pill overflow-hidden shadow-sm px-3 border border-light">
                        <span class="input-group-text bg-transparent border-0 text-muted"><i class="bi bi-search"></i></span>
                        <input type="text" name="q" class="form-control border-0 bg-transparent py-2" placeholder="Item name, ID or specs..." value="{{ request('q') }}">
                    </div>
                </div>
                <div class="col-md-3">
                    <label class="form-label small fw-800 text-erp-deep text-uppercase">Store Location</label>
                    <select name="store_location" class="form-select border border-light bg-white rounded-pill shadow-sm px-4 py-2 text-dark fw-600">
                        <option value="">All Store Sites</option>
                        @foreach($storeLocations as $location)
                            <option value="{{ $location }}" @selected(request('store_location') === $location)>{{ $location }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label small fw-800 text-erp-deep text-uppercase">Stock Status</label>
                    <select name="status" class="form-select border border-light bg-white rounded-pill shadow-sm px-4 py-2 text-dark fw-600">
                        <option value="">All Status</option>
                        <option value="in_stock" @selected(request('status') === 'in_stock')>Stable Stock</option>
                        <option value="low_stock" @selected(request('status') === 'low_stock')>Low Stock</option>
                        <option value="out_of_stock" @selected(request('status') === 'out_of_stock')>Deleted</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-erp-deep rounded-pill px-0 flex-grow-1 border-0 shadow-sm fw-800">
                            Filter
                        </button>
                        <a href="{{ route('inventory.items.index') }}" 
                           class="btn btn-white border-secondary border-opacity-50 text-dark rounded-pill px-3 d-flex align-items-center justify-content-center shadow-sm" 
                           title="Reset Filters">
                            <i class="bi bi-arrow-counterclockwise me-1"></i>
                            <span class="small fw-800">Reset</span>
                        </a>
                    </div>
                </div>
            </div>
        </form>

        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light-soft text-erp-deep">
                    <tr>
                        <th class="ps-4">Item Catalog</th>
                        <th>Store Location</th>
                        <th class="text-center">Stock Level</th>
                        <th>Status</th>
                        <th class="text-end pe-4">Management</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($items as $item)
                        <tr>
                            <td class="ps-4">
                                <div class="d-flex align-items-center gap-3">
                                    <div class="item-icon-box bg-white shadow-sm rounded-3 p-2 text-primary">
                                        <i class="bi bi-box-fill fs-5"></i>
                                    </div>
                                    <div>
                                        <div class="fw-800 text-erp-deep">{{ $item->name }}</div>
                                        <small class="text-muted fw-bold">REF: {{ $item->item_no }}</small>
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
                                    $statusLabel = $qty > 5 ? 'Stable Stock' : ($qty > 0 ? 'Low Stock' : 'Deleted');
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
                                           class="btn btn-sm btn-light border border-secondary shadow-sm px-3 rounded-pill d-flex align-items-center gap-1"
                                           title="Modify Item">
                                            <i class="bi bi-pencil-square text-primary fw-bold"></i>
                                            <span class="small fw-700 text-dark">Edit</span>
                                        </a>
                                        
                                        <button type="button" 
                                                class="btn btn-sm btn-white border border-danger shadow-sm px-3 rounded-pill d-flex align-items-center gap-1"
                                                onclick="if(confirm('Are you sure you want to archive this item?')) document.getElementById('del-{{ $item->id }}').submit()">
                                            <i class="bi bi-trash3 text-danger fw-bold"></i>
                                            <span class="small fw-700 text-danger text-uppercase" style="font-size: 0.65rem;">Delete</span>
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
                                <div class="text-muted italic">The inventory catalog is currently empty.</div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($items->hasPages())
        <div class="card-footer border-0 p-4">
            {{ $items->links() }}
        </div>
    @endif
</div>
@endsection

