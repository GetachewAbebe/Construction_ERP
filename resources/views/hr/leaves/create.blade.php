@extends('layouts.app')

@section('title', 'File Leave | Natanem Engineering')

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
        border-color: #f59e0b;
        background: white;
        box-shadow: 0 0 0 4px rgba(245, 158, 11, 0.1);
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
        <div class="col-lg-8">
            {{-- Header --}}
            <div class="d-flex align-items-center mb-4">
                <a href="{{ route('hr.leaves.index') }}" class="btn btn-white shadow-sm rounded-circle p-2 me-3">
                    <i class="bi bi-arrow-left fs-5"></i>
                </a>
                <div>
                    <h2 class="fw-800 text-erp-deep mb-1">File Leave Request</h2>
                    <p class="text-muted mb-0">Submit a formal time-off request for review.</p>
                </div>
            </div>

            <div class="row">
                <div class="col-lg-12">
                    <div class="glass-form-card p-4 p-md-5">
                        <form action="{{ route('hr.leaves.store') }}" method="POST">
                            @csrf
                            
                            {{-- Employee Selection --}}
                            <div class="mb-4">
                                <label class="form-label-modern">Selected Employee</label>
                                <div class="input-group-modern d-flex align-items-center">
                                    <span class="input-group-text-modern"><i class="bi bi-person-badge"></i></span>
                                    <select name="employee_id" class="form-select @error('employee_id') is-invalid @enderror" required>
                                        <option value="" disabled {{ old('employee_id') ? '' : 'selected' }}>Who is requesting leave?</option>
                                        @foreach($employees as $e)
                                            <option value="{{ $e->id }}" {{ old('employee_id') == $e->id ? 'selected' : '' }}>
                                                {{ $e->first_name }} {{ $e->last_name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                @error('employee_id') <div class="text-danger small mt-1 px-2">{{ $message }}</div> @enderror
                            </div>

                            <div class="row">
                                {{-- Start Date --}}
                                <div class="col-md-6 mb-4">
                                    <label class="form-label-modern">Start Date</label>
                                    <div class="input-group-modern d-flex align-items-center">
                                        <span class="input-group-text-modern"><i class="bi bi-calendar-minus"></i></span>
                                        <input type="date" name="start_date" class="form-control @error('start_date') is-invalid @enderror" value="{{ old('start_date') }}" required>
                                    </div>
                                    @error('start_date') <div class="text-danger small mt-1 px-2">{{ $message }}</div> @enderror
                                </div>

                                {{-- End Date --}}
                                <div class="col-md-6 mb-4">
                                    <label class="form-label-modern">End Date</label>
                                    <div class="input-group-modern d-flex align-items-center">
                                        <span class="input-group-text-modern"><i class="bi bi-calendar-plus"></i></span>
                                        <input type="date" name="end_date" class="form-control @error('end_date') is-invalid @enderror" value="{{ old('end_date') }}" required>
                                    </div>
                                    @error('end_date') <div class="text-danger small mt-1 px-2">{{ $message }}</div> @enderror
                                </div>
                            </div>

                            {{-- Reason --}}
                            <div class="mb-4">
                                <label class="form-label-modern">Reason for Absence</label>
                                <div class="input-group-modern d-flex align-items-start">
                                    <span class="input-group-text-modern mt-2"><i class="bi bi-chat-dots"></i></span>
                                    <textarea name="reason" rows="4" class="form-control @error('reason') is-invalid @enderror" placeholder="Provide a brief explanation for this leave request...">{{ old('reason') }}</textarea>
                                </div>
                                @error('reason') <div class="text-danger small mt-1 px-2">{{ $message }}</div> @enderror
                            </div>

                            <div class="mt-5">
                                <button type="submit" class="btn btn-warning btn-lg rounded-pill px-5 w-100 fw-bold py-3 shadow-lg" style="background: #f59e0b; color: #fff;">
                                    <i class="bi bi-send me-2"></i> Submit for Approval
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                {{-- Policy Note --}}
                <div class="col-12 mt-4">
                    <div class="alert bg-white shadow-sm rounded-4 border-0 d-flex align-items-center p-3">
                        <i class="bi bi-info-circle-fill text-warning fs-4 me-3"></i>
                        <span class="small text-muted">Leave requests are subject to approval by department heads and final confirmation from the HR administrator. Please ensure all dates are correct before submitting.</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
