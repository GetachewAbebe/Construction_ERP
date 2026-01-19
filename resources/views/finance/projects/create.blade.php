@extends('layouts.app')
@section('title', 'Add Construction Site')

@section('content')
<div class="page-header-premium mb-4">
    <div class="row align-items-center">
        <div class="col">
            <h1 class="display-6">New Construction Site</h1>
            <p>Initialize a new production site with budget and timeline allocation.</p>
        </div>
        <div class="col-auto">
            <a href="{{ route('finance.projects.index') }}" class="btn btn-white rounded-pill px-4 shadow-sm fw-800">
                <i class="bi bi-arrow-left me-2"></i> Registry
            </a>
        </div>
    </div>
</div>

<div class="erp-card p-4 p-md-5 stagger-entrance">
    <form action="{{ route('finance.projects.store') }}" method="POST">
        @csrf
        
        <div class="mb-5">
            <div class="d-flex align-items-center gap-3 mb-4">
                <div class="bg-erp-deep text-white rounded-4 d-flex align-items-center justify-content-center" style="width: 48px; height: 48px;">
                    <i class="bi bi-building fs-5"></i>
                </div>
                <h5 class="fw-800 text-erp-deep mb-0">Project Identity</h5>
            </div>
            
            <div class="row g-4">
                <div class="col-12">
                    <label class="form-label-premium">Project Site Name</label>
                    <input type="text" name="name" 
                           class="erp-input @error('name') is-invalid @enderror" 
                           value="{{ old('name') }}" 
                           placeholder="e.g. Skyline Residencies - Block A"
                           required>
                    @error('name') <div class="invalid-feedback d-block fw-bold ms-2">{{ $message }}</div> @enderror
                </div>

                <div class="col-md-6">
                    <label class="form-label-premium">Geographical Area (Location)</label>
                    <input type="text" name="location" 
                           class="erp-input @error('location') is-invalid @enderror" 
                           value="{{ old('location') }}" 
                           placeholder="e.g. Bole, Addis Ababa">
                    @error('location') <div class="invalid-feedback d-block fw-bold ms-2">{{ $message }}</div> @enderror
                </div>

                <div class="col-md-6">
                    <label class="form-label-premium">Total Budget (ETB)</label>
                    <input type="number" step="0.01" name="budget" 
                           class="erp-input @error('budget') is-invalid @enderror" 
                           value="{{ old('budget') }}" 
                           placeholder="0.00"
                           required>
                    @error('budget') <div class="invalid-feedback d-block fw-bold ms-2">{{ $message }}</div> @enderror
                </div>
            </div>
        </div>

        <div class="mb-5">
             <div class="d-flex align-items-center gap-3 mb-4">
                <div class="bg-primary text-white rounded-4 d-flex align-items-center justify-content-center" style="width: 48px; height: 48px;">
                    <i class="bi bi-calendar-range fs-5"></i>
                </div>
                <h5 class="fw-800 text-erp-deep mb-0">Timeline & Status</h5>
            </div>
            
            <div class="row g-4">
                <div class="col-md-6">
                    <label class="form-label-premium">Initialization Date</label>
                    <input type="date" name="start_date" 
                           class="erp-input @error('start_date') is-invalid @enderror" 
                           value="{{ old('start_date') }}">
                    @error('start_date') <div class="invalid-feedback d-block fw-bold ms-2">{{ $message }}</div> @enderror
                </div>

                <div class="col-md-6">
                    <label class="form-label-premium">Projected Completion</label>
                    <input type="date" name="end_date" 
                           class="erp-input @error('end_date') is-invalid @enderror" 
                           value="{{ old('end_date') }}">
                    @error('end_date') <div class="invalid-feedback d-block fw-bold ms-2">{{ $message }}</div> @enderror
                </div>

                <div class="col-md-6">
                    <label class="form-label-premium">Current Alignment (Status)</label>
                    <select name="status" class="form-select erp-input @error('status') is-invalid @enderror">
                        <option value="active" @selected(old('status') == 'active')>Live / Active</option>
                        <option value="on_hold" @selected(old('status') == 'on_hold')>On Hold</option>
                        <option value="completed" @selected(old('status') == 'completed')>Completed</option>
                        <option value="cancelled" @selected(old('status') == 'cancelled')>Terminated</option>
                    </select>
                    @error('status') <div class="invalid-feedback d-block fw-bold ms-2">{{ $message }}</div> @enderror
                </div>
            </div>
        </div>

        <div class="mb-5">
             <div class="d-flex align-items-center gap-3 mb-4">
                <div class="bg-warning text-white rounded-4 d-flex align-items-center justify-content-center" style="width: 48px; height: 48px;">
                    <i class="bi bi-file-text fs-5"></i>
                </div>
                <h5 class="fw-800 text-erp-deep mb-0">Operational Scope</h5>
            </div>
            
            <div class="row g-4">
                <div class="col-12">
                    <label class="form-label-premium">Project Description / Scope</label>
                    <textarea name="description" rows="4" 
                              class="erp-input @error('description') is-invalid @enderror" 
                              placeholder="Describe the main objectives and scope of this construction project...">{{ old('description') }}</textarea>
                    @error('description') <div class="invalid-feedback d-block fw-bold ms-2">{{ $message }}</div> @enderror
                </div>
            </div>
        </div>

        <div class="d-flex gap-3 justify-content-end pt-4 border-top">
            <a href="{{ route('finance.projects.index') }}" class="btn btn-white rounded-pill px-4 py-2 fw-800 shadow-sm">
                Cancel
            </a>
            <button type="submit" class="btn btn-erp-deep rounded-pill px-5 py-2 fw-900 shadow-lg border-0">
                Add Project Site
            </button>
        </div>
    </form>
</div>
@endsection
