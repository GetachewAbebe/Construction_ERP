@extends('layouts.app')
@section('title', 'Provision Asset Classification')

@section('content')
<div class="page-header-premium mb-4">
    <div class="row align-items-center">
        <div class="col">
            <h1 class="display-6">Register Item Category</h1>
            <p>Define a new classification tier for inventory asset management.</p>
        </div>
        <div class="col-auto">
            <a href="{{ route('admin.item-categories.index') }}" class="btn btn-white rounded-pill px-4 shadow-sm border-0">
                <i class="bi bi-arrow-left me-2"></i>Back to Classification Registry
            </a>
        </div>
    </div>
</div>

<div class="row justify-content-center stagger-entrance">
    <div class="col-lg-8">
        <div class="erp-card shadow-lg border-0">
            <form action="{{ route('admin.item-categories.store') }}" method="POST">
                @csrf
                
                <div class="row g-4">
                    <div class="col-md-12">
                        <label for="name" class="erp-label">Category Nomenclature</label>
                        <input type="text" name="name" id="name" value="{{ old('name') }}" 
                               class="erp-input @error('name') is-invalid @enderror" 
                               placeholder="e.g., Construction Materials, Hand Tools, etc." required>
                        @error('name')
                            <div class="invalid-feedback fw-bold">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6">
                        <label for="parent_id" class="erp-label">Structural Parent</label>
                        <select name="parent_id" id="parent_id" class="btn btn-white w-100 text-start border py-3 shadow-none @error('parent_id') is-invalid @enderror" style="border-radius: 12px;">
                            <option value="">Root Level (No Parent)</option>
                            @foreach($parentCategories as $parent)
                                <option value="{{ $parent->id }}" {{ old('parent_id') == $parent->id ? 'selected' : '' }}>
                                    {{ $parent->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('parent_id')
                            <div class="invalid-feedback fw-bold">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6">
                        <label for="icon" class="erp-label">Visual Identifier (Icon)</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-0 px-4" style="border-radius: 12px 0 0 12px;">
                                <i class="bi bi-box-seam"></i>
                            </span>
                            <input type="text" name="icon" id="icon" value="{{ old('icon', 'bi-box-seam') }}" 
                                   class="erp-input @error('icon') is-invalid @enderror" 
                                   placeholder="bi-tag, bi-tools, bi-hammer..." style="border-radius: 0 12px 12px 0;">
                        </div>
                        <small class="text-muted mt-1 px-1 d-block">Supports Bootstrap Icons (e.g., bi-hammer).</small>
                        @error('icon')
                            <div class="invalid-feedback fw-bold">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-12">
                        <label for="description" class="erp-label">Category Definition / Scope</label>
                        <textarea name="description" id="description" rows="4" 
                                  class="erp-input @error('description') is-invalid @enderror" 
                                  placeholder="Describe the scope and purpose of this classification tier...">{{ old('description') }}</textarea>
                        @error('description')
                            <div class="invalid-feedback fw-bold">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="mt-5 border-top pt-4 text-end">
                    <button type="submit" class="btn btn-erp-deep rounded-pill px-5 py-3 shadow-sm border-0 fw-800">
                        <i class="bi bi-shield-check me-2"></i>Provision Category
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
