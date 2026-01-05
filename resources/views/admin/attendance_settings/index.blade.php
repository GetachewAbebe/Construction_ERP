@extends('layouts.app')

@section('title', 'Attendance Settings | Admin')

@push('head')
<style>
    .settings-container {
        padding: 3rem 0;
    }
    .glass-settings-card {
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
    .input-group-modern .form-control {
        border: none;
        background: transparent;
        padding: 0.75rem 1rem;
        font-weight: 500;
    }
    .input-group-modern .form-control:focus {
        box-shadow: none;
    }
    .input-group-text-modern {
        background: transparent;
        border: none;
        color: #64748b;
        padding-left: 1.25rem;
    }
</style>
@endpush

@section('content')
<div class="settings-container container">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            {{-- Header --}}
            <div class="mb-4">
                <h2 class="fw-800 text-erp-deep mb-1">Attendance Settings</h2>
                <p class="text-muted">Configure shift times and grace periods for the entire organization.</p>
            </div>

            @if(session('status'))
                <div class="alert alert-success alert-dismissible fade show rounded-4 mb-4" role="alert">
                    <i class="bi bi-check-circle-fill me-2"></i> {{ session('status') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            <div class="glass-settings-card p-4 p-md-5">
                <form action="{{ route('admin.attendance-settings.update') }}" method="POST">
                    @csrf
                    
                    <div class="row">
                        <div class="col-md-6 mb-4">
                            <label class="form-label fw-bold text-dark small text-uppercase mb-2">Shift Start Time</label>
                            <div class="input-group-modern d-flex align-items-center">
                                <span class="input-group-text-modern"><i class="bi bi-clock"></i></span>
                                <input type="time" name="shift_start_time" class="form-control" value="{{ $settings['shift_start_time'] }}" required>
                            </div>
                            <small class="text-muted d-block mt-2">The official time employees should check in.</small>
                        </div>

                        <div class="col-md-6 mb-4">
                            <label class="form-label fw-bold text-dark small text-uppercase mb-2">Shift End Time</label>
                            <div class="input-group-modern d-flex align-items-center">
                                <span class="input-group-text-modern"><i class="bi bi-clock-history"></i></span>
                                <input type="time" name="shift_end_time" class="form-control" value="{{ $settings['shift_end_time'] }}" required>
                            </div>
                            <small class="text-muted d-block mt-2">The time for automatic check-out (if enabled).</small>
                        </div>

                        <div class="col-md-12 mb-4">
                            <label class="form-label fw-bold text-dark small text-uppercase mb-2">Grace Period (Minutes)</label>
                            <div class="input-group-modern d-flex align-items-center">
                                <span class="input-group-text-modern"><i class="bi bi-hourglass-split"></i></span>
                                <input type="number" name="grace_period_minutes" class="form-control" value="{{ $settings['grace_period_minutes'] }}" min="0" max="120" required>
                            </div>
                            <small class="text-muted d-block mt-2">Allow extra time before marking an employee as "Late".</small>
                        </div>
                    </div>

                    <div class="mt-4">
                        <button type="submit" class="btn btn-erp-deep btn-lg rounded-pill px-5 fw-bold py-3 shadow-lg">
                            <i class="bi bi-save me-2"></i> Save Configurations
                        </button>
                    </div>
                </form>
            </div>
            
            <div class="mt-4 text-center">
                <a href="{{ route('admin.dashboard') }}" class="text-decoration-none text-muted fw-500">
                    <i class="bi bi-arrow-left me-1"></i> Back to Dashboard
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
