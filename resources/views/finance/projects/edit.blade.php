@extends('layouts.app')

@section('title', 'Edit Project | Natanem Engineering')

@push('head')
<style>
    .form-container {
        padding: 3rem 0;
    }
    .glass-form-card {
        background: rgba(255, 255, 255, 0.95);
        backdrop-filter: blur(15px);
        border: 1px solid rgba(255, 255, 255, 0.4);
        border-radius: 24px;
        box-shadow: 0 15px 35px rgba(0,0,0,0.05);
    }
    .input-group-modern {
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        border-radius: 12px;
        transition: all 0.3s ease;
    }
    .input-group-modern:focus-within {
        border-color: #0d6efd;
        background: white;
        box-shadow: 0 0 0 4px rgba(13, 110, 253, 0.1);
    }
    .input-group-modern .form-control, 
    .input-group-modern .form-select {
        border: none;
        background: transparent;
        padding: 0.75rem 1rem;
        font-weight: 500;
        font-size: 0.9rem;
    }
    .input-group-modern .form-control:focus, 
    .input-group-modern .form-select:focus {
        box-shadow: none;
    }
    .input-group-text-modern {
        background: transparent;
        border: none;
        color: #64748b;
        padding-left: 1.25rem;
    }
    .form-label-modern {
        font-weight: 700;
        color: #334155;
        margin-bottom: 0.5rem;
        display: block;
        font-size: 0.8rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
</style>
@endpush

@section('content')
<div class="form-container container">
    <div class="row justify-content-center">
        <div class="col-lg-10">
            {{-- Header --}}
            <div class="d-flex align-items-center mb-4">
                <a href="{{ route('finance.projects.index') }}" class="btn btn-white shadow-sm rounded-circle p-2 me-3">
                    <i class="bi bi-arrow-left fs-5"></i>
                </a>
                <div>
                    <h2 class="fw-800 text-erp-deep mb-1">Edit Construction Project</h2>
                    <p class="text-muted mb-0">Update budget, timeline, and status for: {{ $project->name }}.</p>
                </div>
            </div>

            <div class="glass-form-card p-4 p-md-5">
                <form action="{{ route('finance.projects.update', $project) }}" method="POST">
                    @csrf
                    @method('PUT')
                    
                    <div class="row">
                        <div class="col-md-12 mb-4">
                            <label class="form-label-modern">Project Name</label>
                            <div class="input-group-modern d-flex align-items-center">
                                <span class="input-group-text-modern"><i class="bi bi-building"></i></span>
                                <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', $project->name) }}" required>
                            </div>
                            @error('name') <div class="text-danger small mt-1 px-2">{{ $message }}</div> @enderror
                        </div>

                        <div class="col-md-12 mb-4">
                            <label class="form-label-modern">Site Location</label>
                            <div class="input-group-modern d-flex align-items-center">
                                <span class="input-group-text-modern"><i class="bi bi-geo-alt"></i></span>
                                <input type="text" name="location" class="form-control @error('location') is-invalid @enderror" value="{{ old('location', $project->location) }}">
                            </div>
                        </div>

                        <div class="col-md-6 mb-4">
                            <label class="form-label-modern">Allocation Budget (ETB)</label>
                            <div class="input-group-modern d-flex align-items-center">
                                <span class="input-group-text-modern"><i class="bi bi-currency-exchange"></i></span>
                                <input type="number" step="0.01" name="budget" class="form-control @error('budget') is-invalid @enderror" value="{{ old('budget', $project->budget) }}" required>
                            </div>
                        </div>

                        <div class="col-md-6 mb-4">
                            <label class="form-label-modern">Execution Status</label>
                            <div class="input-group-modern d-flex align-items-center">
                                <span class="input-group-text-modern"><i class="bi bi-flag"></i></span>
                                <select name="status" class="form-select @error('status') is-invalid @enderror">
                                    <option value="active" {{ old('status', $project->status) == 'active' ? 'selected' : '' }}>Active Phase</option>
                                    <option value="completed" {{ old('status', $project->status) == 'completed' ? 'selected' : '' }}>Completed</option>
                                    <option value="on_hold" {{ old('status', $project->status) == 'on_hold' ? 'selected' : '' }}>On Hold</option>
                                    <option value="cancelled" {{ old('status', $project->status) == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                                </select>
                            </div>
                        </div>

                        <div class="col-md-6 mb-4">
                            <label class="form-label-modern">Kick-off Date</label>
                            <div class="input-group-modern d-flex align-items-center">
                                <span class="input-group-text-modern"><i class="bi bi-calendar-check"></i></span>
                                <input type="date" name="start_date" class="form-control" value="{{ old('start_date', optional($project->start_date)->format('Y-m-d')) }}">
                            </div>
                        </div>

                        <div class="col-md-6 mb-4">
                            <label class="form-label-modern">Expected Handover</label>
                            <div class="input-group-modern d-flex align-items-center">
                                <span class="input-group-text-modern"><i class="bi bi-calendar-x"></i></span>
                                <input type="date" name="end_date" class="form-control" value="{{ old('end_date', optional($project->end_date)->format('Y-m-d')) }}">
                            </div>
                        </div>

                        <div class="col-md-12 mb-4">
                            <label class="form-label-modern">Scope / Description</label>
                            <div class="input-group-modern d-flex align-items-start">
                                <span class="input-group-text-modern mt-2"><i class="bi bi-card-text"></i></span>
                                <textarea name="description" rows="3" class="form-control">{{ old('description', $project->description) }}</textarea>
                            </div>
                        </div>
                    </div>

                    <div class="mt-5 d-flex gap-3">
                        <button type="submit" class="btn btn-erp-deep btn-lg rounded-pill px-5 flex-grow-1 fw-bold py-3 shadow-lg">
                            <i class="bi bi-check2-circle me-2"></i> Update Project
                        </button>
                        <a href="{{ route('finance.projects.index') }}" class="btn btn-outline-secondary btn-lg rounded-pill px-5 py-3 fw-bold">
                            Discard
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
