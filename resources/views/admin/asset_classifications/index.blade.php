@extends('layouts.app')
@section('title', 'Asset Classifications')

@section('content')
<div class="page-header-premium mb-5">
    <div class="row align-items-center">
        <div class="col">
            <h1 class="display-3 fw-900 text-erp-deep mb-2 tracking-tight">Resource Classification Registry</h1>
            <p class="text-muted fs-5 mb-0">Organizational taxonomy for physical assets and material inventory categories.</p>
        </div>
        <div class="col-auto">
            <a href="{{ route('inventory.asset-classifications.create') }}" class="btn btn-erp-deep rounded-pill px-4 shadow-sm border-0">
                <i class="bi bi-plus-circle-fill me-2"></i>Add Category
            </a>
        </div>
    </div>
</div>

<div class="table-responsive stagger-entrance">
    <table class="table-premium">
        <thead>
            <tr>
                <th class="ps-4">Category Name</th>
                <th>Level</th>
                <th>Full Path</th>
                <th class="text-center">Items</th>
                <th class="text-end pe-4">Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($classifications as $item)
                <tr>
                    <td class="ps-4">
                        <div class="d-flex align-items-center gap-3">
                            <div class="bg-erp-deep text-white rounded-4 d-flex align-items-center justify-content-center flex-shrink-0 shadow-sm" 
                                 style="width: 48px; height: 48px; background: linear-gradient(45deg, #064e3b, #059669);">
                                 <i class="bi {{ $item->icon_identifier ?: 'bi-layers-half' }} fs-5"></i>
                             </div>
                            <div>
                                <div class="fw-800 text-erp-deep fs-5 mb-0">{{ $item->name }}</div>
                                <span class="badge bg-light text-muted border-0 rounded-pill px-2 py-1 fw-800 tracking-wider" style="font-size: 0.65rem;">
                                    CODE: {{ $item->code }}
                                </span>
                            </div>
                        </div>
                    </td>
                    <td>
                        <div class="d-flex align-items-center gap-2">
                            @for($i = 0; $i < $item->depth; $i++)
                                <div style="width: 12px; height: 2px; background: #e2e8f0; border-radius: 99px;"></div>
                            @endfor
                            <span class="badge {{ $item->depth == 0 ? 'bg-primary-soft text-primary' : 'bg-light text-muted' }} rounded-pill px-3 py-2 border-0 fw-700">
                                <i class="bi {{ $item->depth == 0 ? 'bi-star-fill' : 'bi-node-plus' }} me-1"></i>
                                Level {{ $item->depth + 1 }}
                            </span>
                        </div>
                    </td>
                    <td>
                        <small class="text-muted fw-bold d-block" style="letter-spacing: 0.02em;">
                            {{ $item->full_nomenclature }}
                        </small>
                    </td>
                    <td class="text-center">
                        <div class="fw-900 fs-5 text-erp-deep">{{ number_format($item->recursive_asset_count) }}</div>
                        <small class="text-muted text-uppercase fw-800 x-small">Active Assets</small>
                    </td>
                    <td class="text-end pe-4">
                        <div class="d-flex justify-content-end gap-2">
                            <a href="{{ route('inventory.asset-classifications.edit', $item) }}" 
                               class="btn btn-sm btn-white rounded-pill px-3 py-2 fw-bold shadow-sm" 
                               title="Edit Category">
                                <i class="bi bi-pencil-square"></i>
                            </a>
                            
                            <form action="{{ route('inventory.asset-classifications.destroy', $item) }}" 
                                  method="POST" class="d-inline" id="delete-form-{{ $item->id }}">
                                @csrf
                                @method('DELETE')
                                <button type="button" class="btn btn-sm btn-outline-danger rounded-pill px-3 py-2 fw-bold" 
                                        onclick="premiumConfirm('Delete Category', 'Are you sure you want to delete this category?', 'delete-form-{{ $item->id }}', '{{ $item->name }}')">
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
                            <i class="bi bi-diagram-3 display-1 mb-3 d-block opacity-10"></i>
                            No enterprise classifications found in the registry.
                        </div>
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

@if($classifications->hasPages())
    <div class="mt-4">
        {{ $classifications->links() }}
    </div>
@endif
@endsection
