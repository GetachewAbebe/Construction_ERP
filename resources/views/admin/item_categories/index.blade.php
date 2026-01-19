@extends('layouts.app')
@section('title', 'Strategic Asset Classifications')

@section('content')
<div class="page-header-premium">
    <div class="row align-items-center">
        <div class="col">
            <h1 class="display-6">Item Categories</h1>
            <p>Manage inventory asset classifications, taxonomies, and hierarchical mapping.</p>
        </div>
        <div class="col-auto">
            <a href="{{ route('admin.item-categories.create') }}" class="btn btn-erp-deep rounded-pill px-4 shadow-sm border-0">
                <i class="bi bi-tag-fill me-2"></i>Provision New Category
            </a>
        </div>
    </div>
</div>

<div class="table-responsive stagger-entrance">
    <table class="table-premium">
        <thead>
            <tr>
                <th class="ps-4">Classification</th>
                <th>Hierarchical Parent</th>
                <th>Description</th>
                <th class="text-center">Active Assets</th>
                <th class="text-end pe-4">Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($categories as $category)
                <tr>
                    <td class="ps-4">
                        <div class="d-flex align-items-center gap-3">
                            <div class="bg-erp-deep text-white rounded-pill d-flex align-items-center justify-content-center flex-shrink-0" 
                                 style="width: 40px; height: 40px; background: linear-gradient(135deg, #064e3b 0%, #059669 100%); box-shadow: 0 4px 10px rgba(0,0,0,0.1);">
                                 <i class="bi {{ $category->icon ?: 'bi-box-seam' }}"></i>
                             </div>
                            <div>
                                <div class="fw-800 text-erp-deep">{{ $category->name }}</div>
                                <small class="text-muted fw-bold">{{ $category->slug }}</small>
                            </div>
                        </div>
                    </td>
                    <td>
                        @if($category->parent)
                            <span class="badge bg-light text-muted rounded-pill px-3 py-2 border-0 fw-700">
                                <i class="bi bi-diagram-2 me-1"></i>{{ $category->parent->name }}
                            </span>
                        @else
                            <span class="text-muted fw-800">Root Classification</span>
                        @endif
                    </td>
                    <td>
                        <span class="text-muted fw-600">{{ Str::limit($category->description, 50) ?: 'No description provided.' }}</span>
                    </td>
                    <td class="text-center">
                        <span class="badge bg-erp-deep text-white rounded-pill px-3 py-1 fw-800">
                            {{ $category->items_count }}
                        </span>
                    </td>
                    <td class="text-end pe-4">
                        <div class="d-flex justify-content-end gap-2">
                            <a href="{{ route('admin.item-categories.edit', $category) }}" 
                               class="btn btn-sm btn-white rounded-pill px-3 py-2 fw-bold" 
                               title="Reconfigure Metadata">
                                <i class="bi bi-pencil-square"></i> Edit
                            </a>
                            
                            <form action="{{ route('admin.item-categories.destroy', $category) }}" 
                                  method="POST" class="d-inline" id="delete-form-{{ $category->id }}">
                                @csrf
                                @method('DELETE')
                                <button type="button" class="btn btn-sm btn-outline-danger rounded-pill px-3 py-2 fw-bold" 
                                        onclick="premiumConfirm('Remove Classification', 'Are you sure you want to expunge this asset classification? This will remove the logical mapping for all linked metadata.', 'delete-form-{{ $category->id }}', '{{ $category->name }}')">
                                    <i class="bi bi-trash3"></i> Delete
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" class="text-center py-5">
                        <div class="text-muted fw-800 py-4">
                            <i class="bi bi-inboxes display-1 mb-3 d-block opacity-25"></i>
                            No asset classifications have been provisioned in the registry.
                        </div>
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

@if($categories->hasPages())
    <div class="mt-4">
        {{ $categories->links() }}
    </div>
@endif
@endsection
