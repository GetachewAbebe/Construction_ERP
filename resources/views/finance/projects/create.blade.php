@extends('layouts.app')
@section('title', 'Initialize Project')

@section('content')
<div class="row align-items-center mb-4 stagger-entrance">
    <div class="col">
        <h1 class="h3 mb-1 fw-800 text-erp-deep">Initialize Project</h1>
        <p class="text-muted mb-0">Define the core parameters for a new construction site.</p>
    </div>
    <div class="col-auto">
        <a href="{{ route('finance.projects.index') }}" class="btn btn-white rounded-pill px-4 shadow-sm border-0">
            <i class="bi bi-arrow-left me-2"></i>Return to Projects
        </a>
    </div>
</div>

<div class="stagger-entrance">
    <div class="card border-0 bg-white shadow-sm">
        <div class="card-body p-4 p-md-5">
                <form action="{{ route('finance.projects.store') }}" method="POST">
                    @csrf
                    
                    <div class="mb-5">
                        <h5 class="fw-800 text-erp-deep mb-4 d-flex align-items-center gap-2">
                            <i class="bi bi-building text-primary"></i>
                            Site Identity
                        </h5>
                        
                        <div class="row g-4">
                            <div class="col-12">
                                <label class="form-label small fw-800 text-muted text-uppercase">Project Name</label>
                                <input type="text" name="name" 
                                       class="form-control border-0 bg-light-soft rounded-4 py-3 px-4 shadow-sm @error('name') is-invalid @enderror" 
                                       value="{{ old('name') }}" 
                                       placeholder="e.g. Skyline Residency"
                                       required>
                                <small class="text-muted fw-bold mt-2 d-block">Official designation for the construction initiative.</small>
                                @error('name') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label small fw-800 text-muted text-uppercase">Site Location</label>
                                <input type="text" name="location" 
                                       class="form-control border-0 bg-light-soft rounded-4 py-3 px-4 shadow-sm @error('location') is-invalid @enderror" 
                                       value="{{ old('location') }}" 
                                       placeholder="e.g. Addis Ababa">
                                <small class="text-muted fw-bold mt-2 d-block">Geographical coordinates or area name.</small>
                                @error('location') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label small fw-800 text-muted text-uppercase">Portfolio Budget (ETB)</label>
                                <input type="number" step="0.01" name="budget" 
                                       class="form-control border-0 bg-light-soft rounded-4 py-3 px-4 shadow-sm @error('budget') is-invalid @enderror" 
                                       value="{{ old('budget') }}" 
                                       placeholder="0.00"
                                       required>
                                <small class="text-muted fw-bold mt-2 d-block">Total financial allocation for this project.</small>
                                @error('budget') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                            </div>
                        </div>
                    </div>

                    <div class="mb-5">
                        <h5 class="fw-800 text-erp-deep mb-4 d-flex align-items-center gap-2">
                            <i class="bi bi-calendar-range text-success"></i>
                            Timeline & Status
                        </h5>
                        
                        <div class="row g-4">
                            <div class="col-md-6">
                                <label class="form-label small fw-800 text-muted text-uppercase">Start Date</label>
                                <input type="date" name="start_date" 
                                       class="form-control border-0 bg-light-soft rounded-4 py-3 px-4 shadow-sm @error('start_date') is-invalid @enderror" 
                                       value="{{ old('start_date') }}">
                                @error('start_date') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label small fw-800 text-muted text-uppercase">Estimated Completion</label>
                                <input type="date" name="end_date" 
                                       class="form-control border-0 bg-light-soft rounded-4 py-3 px-4 shadow-sm @error('end_date') is-invalid @enderror" 
                                       value="{{ old('end_date') }}">
                                @error('end_date') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label small fw-800 text-muted text-uppercase">Operational Status</label>
                                <select name="status" class="form-select border-0 bg-light-soft rounded-4 py-3 px-4 shadow-sm @error('status') is-invalid @enderror">
                                    <option value="active" @selected(old('status') == 'active')>Active - In Construction</option>
                                    <option value="on_hold" @selected(old('status') == 'on_hold')>On Hold - Temporary Pause</option>
                                    <option value="completed" @selected(old('status') == 'completed')>Completed - Site Handover</option>
                                    <option value="cancelled" @selected(old('status') == 'cancelled')>Cancelled</option>
                                </select>
                                <small class="text-muted fw-bold mt-2 d-block">Current lifecycle stage of the project.</small>
                                @error('status') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                            </div>
                        </div>
                    </div>

                    <div class="mb-5">
                        <h5 class="fw-800 text-erp-deep mb-4 d-flex align-items-center gap-2">
                            <i class="bi bi-file-text text-info"></i>
                            Project Scope
                        </h5>
                        
                        <div class="row g-4">
                            <div class="col-12">
                                <label class="form-label small fw-800 text-muted text-uppercase">Scope & Notes</label>
                                <textarea name="description" rows="4" 
                                          class="form-control border-0 bg-light-soft rounded-4 py-3 px-4 shadow-sm @error('description') is-invalid @enderror" 
                                          placeholder="Outline goals, scope, and initial requirements...">{{ old('description') }}</textarea>
                                @error('description') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                            </div>
                        </div>
                    </div>

                    <div class="d-flex gap-3 justify-content-end pt-4 border-top border-light">
                        <a href="{{ route('finance.projects.index') }}" class="btn btn-white rounded-pill px-4 py-3 fw-700 shadow-sm border-0">
                            <i class="bi bi-x-circle me-2"></i>Cancel Initialization
                        </a>
                        <button type="submit" class="btn btn-erp-deep rounded-pill px-5 py-3 fw-800 shadow-lg border-0">
                            <i class="bi bi-check2-circle me-2"></i>Commission Project
                        </button>
                    </div>
                </form>
            </div>
        </div>
</div>
@endsection
