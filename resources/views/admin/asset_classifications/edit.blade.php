@extends('layouts.app')
@section('title', 'Edit Category')

@section('content')
<div class="page-header-premium mb-4">
    <div class="row align-items-center">
        <div class="col">
            <h1 class="display-6">Edit Category: {{ $classification->name }}</h1>
            <p>Update the details and organization for this category.</p>
        </div>
        <div class="col-auto">
            <a href="{{ route('inventory.asset-classifications.index') }}" class="btn btn-white rounded-pill px-4 shadow-sm border-0">
                <i class="bi bi-arrow-left me-2"></i>Back to Categories
            </a>
        </div>
    </div>
</div>

<div class="row justify-content-center stagger-entrance">
    <div class="col-lg-8">
        <div class="erp-card shadow-lg border-0">
            <form action="{{ route('inventory.asset-classifications.update', $classification) }}" method="POST">
                @csrf
                @method('PUT')
                
                <div class="row g-4">
                    <div class="col-md-8">
                        <label for="name" class="erp-label">Category Name</label>
                        <input type="text" name="name" id="name" value="{{ old('name', $classification->name) }}" 
                               class="erp-input @error('name') is-invalid @enderror" 
                               placeholder="e.g., Raw Materials, Tools, Equipment" required>
                        @error('name')
                            <div class="invalid-feedback fw-bold">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-4">
                        <label for="code" class="erp-label">Category Code</label>
                        <input type="text" name="code" id="code" value="{{ old('code', $classification->code) }}" 
                               class="erp-input @error('code') is-invalid @enderror" 
                               placeholder="e.g. MTRL-RW" style="text-transform: uppercase;" required>
                        @error('code')
                            <div class="invalid-feedback fw-bold">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6">
                        <label for="parent_id" class="erp-label">Parent Category</label>
                        <select name="parent_id" id="parent_id" class="btn btn-white w-100 text-start border py-3 shadow-none @error('parent_id') is-invalid @enderror" style="border-radius: 12px;">
                            <option value="">No Parent (Root Category)</option>
                            @foreach($parents as $p)
                                <option value="{{ $p->id }}" {{ old('parent_id', $classification->parent_id) == $p->id ? 'selected' : '' }}>
                                    @for($i=0; $i<$p->depth; $i++) &nbsp;&nbsp; @endfor
                                    {{ $p->name }} ({{ $p->code }})
                                </option>
                            @endforeach
                        </select>
                        @error('parent_id')
                            <div class="invalid-feedback fw-bold">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6">
                        <label for="icon_identifier" class="erp-label">Category Icon</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-0 px-4" style="border-radius: 12px 0 0 12px;">
                                <i class="bi {{ $classification->icon_identifier ?: 'bi-tag-fill' }}"></i>
                            </span>
                            <input type="text" name="icon_identifier" id="icon_identifier" value="{{ old('icon_identifier', $classification->icon_identifier) }}" 
                                   class="erp-input @error('icon_identifier') is-invalid @enderror" 
                                   placeholder="e.g. bi-tag, bi-tools, bi-hammer" style="border-radius: 0 12px 12px 0;">
                        </div>
                        @error('icon_identifier')
                            <div class="invalid-feedback fw-bold">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-12">
                        <label for="description" class="erp-label">Description</label>
                        <textarea name="description" id="description" rows="4" 
                                  class="erp-input @error('description') is-invalid @enderror" 
                                  placeholder="Describe what kind of items belong in this category...">{{ old('description', $classification->description) }}</textarea>
                        @error('description')
                            <div class="invalid-feedback fw-bold">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="mt-5 border-top pt-4 text-end">
                    <button type="submit" class="btn btn-erp-deep rounded-pill px-5 py-3 shadow-sm border-0 fw-800">
                        <i class="bi bi-check-circle-fill me-2"></i>Save Changes
                    </button>
                </div>
            </form>
        </div>

        @php $assetCount = $classification->assets()->count(); @endphp
        @if($assetCount > 0)
            <div class="alert alert-info mt-4 border-0 shadow-sm rounded-4" style="background: rgba(13, 110, 253, 0.05); color: #084298;">
                <div class="d-flex align-items-center gap-3 p-2">
                    <i class="bi bi-info-circle-fill display-6 opacity-50"></i>
                    <div>
                        <h6 class="fw-800 mb-1">Active Item Notice</h6>
                        <p class="mb-0 small fw-bold">This category is currently linked to <strong>{{ $assetCount }}</strong> active items. Changes will be updated across all linked records.</p>
                    </div>
                </div>
            </div>
        @endif
    </div>
</div>
@endsection
