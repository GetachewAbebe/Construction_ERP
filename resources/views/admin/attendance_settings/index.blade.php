@extends('layouts.app')
@section('title', 'Temporal Configuration Control')

@section('content')
<div class="row align-items-center mb-4 stagger-entrance">
    <div class="col">
        <h1 class="h3 mb-1 fw-800 text-erp-deep">Temporal Configuration Control</h1>
        <p class="text-muted mb-0">Establish organizational shift parameters and attendance monitoring protocols for workforce synchronization.</p>
    </div>
    <div class="col-auto">
        <a href="{{ route('admin.dashboard') }}" class="btn btn-white rounded-pill px-4 shadow-sm border-0">
            <i class="bi bi-arrow-left me-2"></i>Return to Command
        </a>
    </div>
</div>

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show hardened-glass border-0 shadow-sm stagger-entrance" role="alert">
        <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

@if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show hardened-glass border-0 shadow-sm stagger-entrance" role="alert">
        <i class="bi bi-exclamation-triangle-fill me-2"></i>{{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

<div class="row justify-content-center stagger-entrance">
    <div class="col-lg-8">
        <div class="card hardened-glass border-0 overflow-hidden shadow-lg">
            <div class="card-body p-4 p-md-5">
                <form action="{{ route('admin.attendance-settings.update') }}" method="POST">
                    @csrf
                    
                    <div class="mb-5">
                        <h5 class="fw-800 text-erp-deep mb-4 d-flex align-items-center gap-2">
                            <i class="bi bi-clock-history text-primary"></i>
                            Shift Temporal Boundaries
                        </h5>
                        
                        <div class="row g-4">
                            <div class="col-md-6">
                                <label class="form-label small fw-800 text-muted text-uppercase">Shift Commencement Time</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light-soft border-0">
                                        <i class="bi bi-sunrise text-primary"></i>
                                    </span>
                                    <input type="time" name="shift_start_time" 
                                           class="form-control border-0 bg-light-soft rounded-end-4 py-3 px-4 shadow-sm @error('shift_start_time') is-invalid @enderror" 
                                           value="{{ old('shift_start_time', $settings['shift_start_time']) }}" 
                                           required>
                                </div>
                                <small class="text-muted fw-bold mt-2 d-block">Official workforce activation timestamp for daily operations.</small>
                                @error('shift_start_time') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label small fw-800 text-muted text-uppercase">Shift Termination Time</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light-soft border-0">
                                        <i class="bi bi-sunset text-warning"></i>
                                    </span>
                                    <input type="time" name="shift_end_time" 
                                           class="form-control border-0 bg-light-soft rounded-end-4 py-3 px-4 shadow-sm @error('shift_end_time') is-invalid @enderror" 
                                           value="{{ old('shift_end_time', $settings['shift_end_time']) }}" 
                                           required>
                                </div>
                                <small class="text-muted fw-bold mt-2 d-block">Designated conclusion marker for standard operational cycles.</small>
                                @error('shift_end_time') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                            </div>
                        </div>
                    </div>

                    <div class="mb-5">
                        <h5 class="fw-800 text-erp-deep mb-4 d-flex align-items-center gap-2">
                            <i class="bi bi-hourglass-split text-success"></i>
                            Tolerance Parameters
                        </h5>
                        
                        <div class="row g-4">
                            <div class="col-md-12">
                                <label class="form-label small fw-800 text-muted text-uppercase">Grace Period Allocation (Minutes)</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light-soft border-0">
                                        <i class="bi bi-stopwatch text-info"></i>
                                    </span>
                                    <input type="number" name="grace_period_minutes" 
                                           class="form-control border-0 bg-light-soft rounded-end-4 py-3 px-4 shadow-sm @error('grace_period_minutes') is-invalid @enderror" 
                                           value="{{ old('grace_period_minutes', $settings['grace_period_minutes']) }}" 
                                           min="0" max="120" 
                                           required>
                                    <span class="input-group-text bg-light-soft border-0 text-muted fw-bold">
                                        minutes
                                    </span>
                                </div>
                                <small class="text-muted fw-bold mt-2 d-block">Permissible temporal variance before tardiness classification is triggered.</small>
                                @error('grace_period_minutes') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                            </div>
                        </div>
                    </div>

                    <div class="d-flex gap-3 justify-content-end pt-4 border-top border-light">
                        <a href="{{ route('admin.dashboard') }}" class="btn btn-white rounded-pill px-4 py-3 fw-700 shadow-sm border-0">
                            <i class="bi bi-x-circle me-2"></i>Discard Changes
                        </a>
                        <button type="submit" class="btn btn-erp-deep rounded-pill px-5 py-3 fw-800 shadow-lg border-0">
                            <i class="bi bi-check2-circle me-2"></i>Apply Configuration
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <div class="mt-4 p-4 bg-light-soft rounded-4 stagger-entrance">
            <div class="d-flex align-items-start gap-3">
                <i class="bi bi-info-circle-fill text-primary fs-4"></i>
                <div>
                    <h6 class="fw-800 text-erp-deep mb-2">Configuration Impact Notice</h6>
                    <p class="text-muted mb-0 small fw-600">
                        Modifications to these temporal parameters will immediately affect all active workforce monitoring systems. 
                        Ensure shift times align with organizational policies before applying changes. Grace period adjustments 
                        influence tardiness calculations across all attendance records.
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

