@extends('layouts.app')
@section('title', 'Add New Category')

@section('content')
<div class="page-header-premium mb-4">
    <div class="row align-items-center">
        <div class="col">
            <h1 class="display-6">Add New Category</h1>
            <p>Create a new classification to organize your inventory assets.</p>
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
            <form action="{{ route('inventory.asset-classifications.store') }}" method="POST">
                @csrf
                
                <div class="row g-4">
                    <div class="col-md-8">
                        <label for="name" class="erp-label">Category Name</label>
                        <input type="text" name="name" id="name" value="{{ old('name') }}" 
                               class="erp-input @error('name') is-invalid @enderror" 
                               placeholder="e.g., Raw Materials, Tools, Equipment" required>
                        @error('name')
                            <div class="invalid-feedback fw-bold">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-4">
                        <label for="code" class="erp-label">Category Code</label>
                        <input type="text" name="code" id="code" value="{{ old('code') }}" 
                               class="erp-input @error('code') is-invalid @enderror" 
                               placeholder="e.g. MTRL-RW" style="text-transform: uppercase;">
                        <small class="text-muted x-small">Leave blank for auto-generation.</small>
                        @error('code')
                            <div class="invalid-feedback fw-bold">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6">
                        <label for="parent_id" class="erp-label">Parent Category</label>
                        <select name="parent_id" id="parent_id" class="btn btn-white w-100 text-start border py-3 shadow-none @error('parent_id') is-invalid @enderror" style="border-radius: 12px;">
                            <option value="">No Parent (Root Category)</option>
                            @foreach($parents as $p)
                                <option value="{{ $p->id }}" {{ old('parent_id') == $p->id ? 'selected' : '' }}>
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
                                <i class="bi bi-tag-fill"></i>
                            </span>
                            <input type="text" name="icon_identifier" id="icon_identifier" value="{{ old('icon_identifier', 'bi-layers') }}" 
                                   class="erp-input @error('icon_identifier') is-invalid @enderror" 
                                   placeholder="e.g. bi-tag, bi-hammer" style="border-radius: 0 12px 12px 0;">
                        </div>
                        @error('icon_identifier')
                            <div class="invalid-feedback fw-bold">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-12">
                        <label for="description" class="erp-label">Description</label>
                        <textarea name="description" id="description" rows="4" 
                                  class="erp-input @error('description') is-invalid @enderror" 
                                  placeholder="Describe what kind of items belong in this category..."></textarea>
                        @error('description')
                            <div class="invalid-feedback fw-bold">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="mt-5 border-top pt-4 text-end">
                    <button type="submit" class="btn btn-erp-deep rounded-pill px-5 py-3 shadow-sm border-0 fw-800">
                        <i class="bi bi-check-circle-fill me-2"></i>Save Category
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
