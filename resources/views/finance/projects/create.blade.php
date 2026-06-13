@extends('layouts.app')
@section('title', 'Add Construction Site')

@section('content')
<div class="mb-5">
    <div class="row align-items-center g-3">
        <div class="col-md">
            <span class="text-uppercase tracking-wider text-muted font-bold small d-block mb-1" style="font-size: 0.75rem; letter-spacing: 0.1em;">System Initialization</span>
            <h1 class="display-4 fw-900 text-erp-deep mb-0 tracking-tight" style="letter-spacing: -0.03em;">New Construction Site</h1>
            <p class="text-muted small mb-0 mt-1">Initialize a new production site with budget metrics and operational constraints.</p>
        </div>
        <div class="col-md-auto">
            <a href="{{ route('finance.projects.index') }}" class="btn btn-white rounded-pill px-4 py-2.5 fw-800 shadow-sm border border-slate-200 text-secondary">
                <i class="bi bi-arrow-left me-2"></i> Back to Registry
            </a>
        </div>
    </div>
</div>

<div class="project-card-premium p-4 p-md-5 shadow-sm stagger-entrance">
    <form action="{{ route('finance.projects.store') }}" method="POST">
        @csrf
        
        <div class="mb-5">
            <div class="d-flex align-items-center gap-3 mb-4">
                <div class="bg-gradient-premium text-white rounded-4 d-flex align-items-center justify-content-center flex-shrink-0 shadow-sm" style="width: 48px; height: 48px;">
                    <i class="bi bi-building fs-5"></i>
                </div>
                <div>
                    <h5 class="fw-800 text-erp-deep mb-0">Project Identity</h5>
                    <small class="text-muted text-xs">Core operational tags and accounting identifiers.</small>
                </div>
            </div>
            
            <div class="row g-4">
                <div class="col-12">
                    <label class="form-label-premium">Project Site Name</label>
                    <input type="text" name="name" 
                           class="erp-input @error('name') is-invalid @enderror" 
                           value="{{ old('name') }}" 
                           placeholder="e.g. Skyline Residencies - Block A"
                           required>
                    @error('name') <div class="invalid-feedback d-block fw-bold ms-2 mt-1">{{ $message }}</div> @enderror
                </div>

                <div class="col-md-6">
                    <label class="form-label-premium">Geographical Area (Location)</label>
                    <input type="text" name="location" 
                           class="erp-input @error('location') is-invalid @enderror" 
                           value="{{ old('location') }}" 
                           placeholder="e.g. Bole, Addis Ababa"
                           required>
                    @error('location') <div class="invalid-feedback d-block fw-bold ms-2 mt-1">{{ $message }}</div> @enderror
                </div>

                <div class="col-md-6">
                    <label class="form-label-premium">Total Budget Allocation (ETB)</label>
                    <input type="number" step="0.01" name="budget" 
                           class="erp-input @error('budget') is-invalid @enderror" 
                           value="{{ old('budget') }}" 
                           placeholder="0.00"
                           required>
                    @error('budget') <div class="invalid-feedback d-block fw-bold ms-2 mt-1">{{ $message }}</div> @enderror
                </div>
            </div>
        </div>

        <div class="mb-5">
             <div class="d-flex align-items-center gap-3 mb-4">
                <div class="bg-gradient-dark text-white rounded-4 d-flex align-items-center justify-content-center flex-shrink-0 shadow-sm" style="width: 48px; height: 48px;">
                    <i class="bi bi-calendar-range fs-5"></i>
                </div>
                <div>
                    <h5 class="fw-800 text-erp-deep mb-0">Timeline & Track Status</h5>
                    <small class="text-muted text-xs">Define deployment duration thresholds.</small>
                </div>
            </div>
            
            <div class="row g-4">
                <div class="col-md-6">
                    <label class="form-label-premium">Initialization Date</label>
                    <input type="date" name="start_date" 
                           class="erp-input @error('start_date') is-invalid @enderror" 
                           value="{{ old('start_date') }}">
                    @error('start_date') <div class="invalid-feedback d-block fw-bold ms-2 mt-1">{{ $message }}</div> @enderror
                </div>

                <div class="col-md-6">
                    <label class="form-label-premium">Projected Completion</label>
                    <input type="date" name="end_date" 
                           class="erp-input @error('end_date') is-invalid @enderror" 
                           value="{{ old('end_date') }}">
                    @error('end_date') <div class="invalid-feedback d-block fw-bold ms-2 mt-1">{{ $message }}</div> @enderror
                </div>

                <div class="col-md-6">
                    <label class="form-label-premium">Current Alignment (Status)</label>
                    <select name="status" class="form-select erp-input @error('status') is-invalid @enderror">
                        <option value="operational" @selected(old('status') == 'operational')>Live / Operational</option>
                        <option value="on_hold" @selected(old('status') == 'on_hold')>On Hold</option>
                        <option value="completed" @selected(old('status') == 'completed')>Completed</option>
                        <option value="cancelled" @selected(old('status') == 'cancelled')>Terminated</option>
                    </select>
                    @error('status') <div class="invalid-feedback d-block fw-bold ms-2 mt-1">{{ $message }}</div> @enderror
                </div>
            </div>
        </div>

        <div class="mb-5">
             <div class="d-flex align-items-center gap-3 mb-4">
                <div class="search-glass text-emerald-800 rounded-4 d-flex align-items-center justify-content-center flex-shrink-0 shadow-sm border border-slate-200" style="width: 48px; height: 48px;">
                    <i class="bi bi-file-text fs-5"></i>
                </div>
                <div>
                    <h5 class="fw-800 text-erp-deep mb-0">Operational Scope</h5>
                    <small class="text-muted text-xs">Detailed task breakdowns or site expectations.</small>
                </div>
            </div>
            
            <div class="row g-4">
                <div class="col-12">
                    <label class="form-label-premium">Project Description / Scope</label>
                    <textarea name="description" rows="4" 
                              class="erp-input @error('description') is-invalid @enderror" 
                              placeholder="Describe the main architectural objectives and engineering scope of this project site...">{{ old('description') }}</textarea>
                    @error('description') <div class="invalid-feedback d-block fw-bold ms-2 mt-1">{{ $message }}</div> @enderror
                </div>
            </div>
        </div>

        <div class="d-flex gap-3 justify-content-end pt-4 border-top border-slate-100">
            <a href="{{ route('finance.projects.index') }}" class="btn btn-light rounded-pill px-4 py-2.5 fw-700 text-secondary border border-slate-200 shadow-2xs">
                Cancel
            </a>
            <button type="submit" class="btn bg-gradient-premium text-white rounded-pill px-5 py-2.5 fw-800 shadow-lg border-0 transform-hover-premium">
                Register Project Site
            </button>
        </div>
    </form>
</div>
@endsection