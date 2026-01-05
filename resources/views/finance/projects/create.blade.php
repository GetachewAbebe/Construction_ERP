@extends('layouts.app')

@section('title', 'New Project | Natanem Engineering')

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
        border-color: #11998e;
        background: white;
        box-shadow: 0 0 0 4px rgba(17, 153, 142, 0.1);
    }
    .input-group-modern .form-control, 
    .input-group-modern .form-select {
        border: none;
        background: transparent;
        padding: 0.75rem 1rem;
        font-weight: 500;
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
        font-size: 0.85rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
</style>
@endpush

@section('content')
<div class="form-container container">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            {{-- Header --}}
            <div class="d-flex align-items-center mb-4">
                <a href="{{ route('finance.projects.index') }}" class="btn btn-white shadow-sm rounded-circle p-2 me-3">
                    <i class="bi bi-arrow-left fs-5"></i>
                </a>
                <div>
                    <h2 class="fw-800 text-erp-deep mb-1">Initialize Project</h2>
                    <p class="text-muted mb-0">Define the core parameters for a new construction site.</p>
                </div>
            </div>

            <div class="glass-form-card p-4 p-md-5">
                <form action="{{ route('finance.projects.store') }}" method="POST">
                    @csrf
                    
                    {{-- Project Name --}}
                    <div class="mb-4">
                        <label class="form-label-modern">Project Name</label>
                        <div class="input-group-modern d-flex align-items-center">
                            <span class="input-group-text-modern"><i class="bi bi-building"></i></span>
                            <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name') }}" placeholder="e.g. Skyline Residency" required>
                        </div>
                        @error('name') <div class="text-danger small mt-1 px-2">{{ $message }}</div> @enderror
                    </div>

                    <div class="row">
                        {{-- Location --}}
                        <div class="col-md-6 mb-4">
                            <label class="form-label-modern">Site Location</label>
                            <div class="input-group-modern d-flex align-items-center">
                                <span class="input-group-text-modern"><i class="bi bi-geo-alt"></i></span>
                                <input type="text" name="location" class="form-control @error('location') is-invalid @enderror" value="{{ old('location') }}" placeholder="e.g. Addis Ababa">
                            </div>
                            @error('location') <div class="text-danger small mt-1 px-2">{{ $message }}</div> @enderror
                        </div>

                        {{-- Budget --}}
                        <div class="col-md-6 mb-4">
                            <label class="form-label-modern">Portfolio Budget (ETB)</label>
                            <div class="input-group-modern d-flex align-items-center">
                                <span class="input-group-text-modern"><i class="bi bi-currency-exchange"></i></span>
                                <input type="number" step="0.01" name="budget" class="form-control @error('budget') is-invalid @enderror" value="{{ old('budget') }}" placeholder="0.00" required>
                            </div>
                            @error('budget') <div class="text-danger small mt-1 px-2">{{ $message }}</div> @enderror
                        </div>
                    </div>

                    <div class="row">
                        {{-- Start Date --}}
                        <div class="col-md-6 mb-4">
                            <label class="form-label-modern">Start Date</label>
                            <div class="input-group-modern d-flex align-items-center">
                                <span class="input-group-text-modern"><i class="bi bi-calendar-check"></i></span>
                                <input type="date" name="start_date" class="form-control @error('start_date') is-invalid @enderror" value="{{ old('start_date') }}">
                            </div>
                            @error('start_date') <div class="text-danger small mt-1 px-2">{{ $message }}</div> @enderror
                        </div>

                        {{-- End Date --}}
                        <div class="col-md-6 mb-4">
                            <label class="form-label-modern">Estimated Completion</label>
                            <div class="input-group-modern d-flex align-items-center">
                                <span class="input-group-text-modern"><i class="bi bi-calendar-event"></i></span>
                                <input type="date" name="end_date" class="form-control @error('end_date') is-invalid @enderror" value="{{ old('end_date') }}">
                            </div>
                            @error('end_date') <div class="text-danger small mt-1 px-2">{{ $message }}</div> @enderror
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="form-label-modern">Operational Status</label>
                        <div class="input-group-modern d-flex align-items-center">
                            <span class="input-group-text-modern"><i class="bi bi-toggle-on"></i></span>
                            <select name="status" class="form-select @error('status') is-invalid @enderror">
                                <option value="active" {{ old('status') == 'active' ? 'selected' : '' }}>Active - In Construction</option>
                                <option value="on_hold" {{ old('status') == 'on_hold' ? 'selected' : '' }}>On Hold - Temporary Pause</option>
                                <option value="completed" {{ old('status') == 'completed' ? 'selected' : '' }}>Completed - Site Handover</option>
                                <option value="cancelled" {{ old('status') == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                            </select>
                        </div>
                        @error('status') <div class="text-danger small mt-1 px-2">{{ $message }}</div> @enderror
                    </div>

                    <div class="mb-4">
                        <label class="form-label-modern">Project Scope & Notes</label>
                        <div class="input-group-modern d-flex align-items-start">
                            <span class="input-group-text-modern mt-2"><i class="bi bi-pencil-square"></i></span>
                            <textarea name="description" rows="4" class="form-control @error('description') is-invalid @enderror" placeholder="Outline goals, scope, and initial requirements...">{{ old('description') }}</textarea>
                        </div>
                        @error('description') <div class="text-danger small mt-1 px-2">{{ $message }}</div> @enderror
                    </div>

                    <div class="mt-5">
                        <button type="submit" class="btn btn-erp-deep btn-lg rounded-pill px-5 w-100 fw-bold py-3 shadow-lg">
                            <i class="bi bi-plus-circle me-2"></i> Create Project Site
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
