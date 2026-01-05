@extends('layouts.app')

@section('title', 'Edit Employee | Natanem Engineering')

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
        border-color: #4f46e5;
        background: white;
        box-shadow: 0 0 0 4px rgba(79, 70, 229, 0.1);
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
    .profile-preview-wrapper {
        position: relative;
        display: inline-block;
        padding: 5px;
        background: white;
        border-radius: 50%;
        box-shadow: 0 10px 25px rgba(0,0,0,0.1);
        margin-bottom: 2rem;
    }
</style>
@endpush

@section('content')
<div class="form-container container">
    <div class="row justify-content-center">
        <div class="col-lg-10">
            {{-- Header --}}
            <div class="d-flex align-items-center justify-content-between mb-4">
                <div class="d-flex align-items-center">
                    <a href="{{ route('hr.employees.index') }}" class="btn btn-white shadow-sm rounded-circle p-2 me-3">
                        <i class="bi bi-arrow-left fs-5"></i>
                    </a>
                    <div>
                        <h2 class="fw-800 text-erp-deep mb-1">Edit Employee Profile</h2>
                        <p class="text-muted mb-0">Update information for {{ $employee->first_name }} {{ $employee->last_name }}.</p>
                    </div>
                </div>
            </div>

            <div class="glass-form-card p-4 p-md-5">
                <form action="{{ route('hr.employees.update', $employee) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    
                    <div class="text-center">
                        <div class="profile-preview-wrapper">
                            @if($employee->profile_picture)
                                <img src="{{ asset('storage/' . $employee->profile_picture) }}" alt="Profile" class="rounded-circle" width="120" height="120" style="object-fit: cover;">
                            @else
                                <div class="rounded-circle bg-light d-flex align-items-center justify-content-center" width="120" height="120" style="width: 120px; height: 120px;">
                                    <i class="bi bi-person text-secondary fs-1"></i>
                                </div>
                            @endif
                        </div>
                    </div>

                    <div class="row">
                        {{-- First Name --}}
                        <div class="col-md-6 mb-4">
                            <label class="form-label-modern">Legal First Name</label>
                            <div class="input-group-modern d-flex align-items-center">
                                <span class="input-group-text-modern"><i class="bi bi-person"></i></span>
                                <input type="text" name="first_name" class="form-control @error('first_name') is-invalid @enderror" value="{{ old('first_name', $employee->first_name) }}" required>
                            </div>
                            @error('first_name') <div class="text-danger small mt-1 px-2">{{ $message }}</div> @enderror
                        </div>

                        {{-- Last Name --}}
                        <div class="col-md-6 mb-4">
                            <label class="form-label-modern">Family Name</label>
                            <div class="input-group-modern d-flex align-items-center">
                                <span class="input-group-text-modern"><i class="bi bi-person"></i></span>
                                <input type="text" name="last_name" class="form-control @error('last_name') is-invalid @enderror" value="{{ old('last_name', $employee->last_name) }}" required>
                            </div>
                            @error('last_name') <div class="text-danger small mt-1 px-2">{{ $message }}</div> @enderror
                        </div>
                    </div>

                    <div class="row">
                        {{-- Department --}}
                        <div class="col-md-6 mb-4">
                            <label class="form-label-modern">Department Alignment</label>
                            <div class="input-group-modern d-flex align-items-center">
                                <span class="input-group-text-modern"><i class="bi bi-diagram-3"></i></span>
                                <input type="text" name="department_name" list="departmentList" class="form-control" placeholder="Select or type..." value="{{ old('department_name', $employee->department) }}">
                                <datalist id="departmentList">
                                    @foreach($departments as $dept) <option value="{{ $dept->name }}"> @endforeach
                                </datalist>
                            </div>
                        </div>

                        {{-- Position --}}
                        <div class="col-md-6 mb-4">
                            <label class="form-label-modern">Job Title / Position</label>
                            <div class="input-group-modern d-flex align-items-center">
                                <span class="input-group-text-modern"><i class="bi bi-briefcase"></i></span>
                                <input type="text" name="position_title" list="positionList" class="form-control" placeholder="Select or type..." value="{{ old('position_title', $employee->position) }}">
                                <datalist id="positionList">
                                    @foreach($positions as $pos) <option value="{{ $pos->title }}"> @endforeach
                                </datalist>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        {{-- Phone --}}
                        <div class="col-md-6 mb-4">
                            <label class="form-label-modern">Contact Primary Phone</label>
                            <div class="input-group-modern d-flex align-items-center">
                                <span class="input-group-text-modern"><i class="bi bi-telephone"></i></span>
                                <input type="text" name="phone" class="form-control @error('phone') is-invalid @enderror" value="{{ old('phone', $employee->phone) }}">
                            </div>
                            @error('phone') <div class="text-danger small mt-1 px-2">{{ $message }}</div> @enderror
                        </div>

                        {{-- Email --}}
                        <div class="col-md-6 mb-4">
                            <label class="form-label-modern">Work Email Address</label>
                            <div class="input-group-modern d-flex align-items-center">
                                <span class="input-group-text-modern"><i class="bi bi-envelope"></i></span>
                                <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email', $employee->email) }}" required>
                            </div>
                            @error('email') <div class="text-danger small mt-1 px-2">{{ $message }}</div> @enderror
                        </div>
                    </div>

                    <div class="row">
                        {{-- Hire Date --}}
                        <div class="col-md-6 mb-4">
                            <label class="form-label-modern">Official Hire Date</label>
                            <div class="input-group-modern d-flex align-items-center">
                                <span class="input-group-text-modern"><i class="bi bi-calendar-check"></i></span>
                                <input type="date" name="hire_date" class="form-control @error('hire_date') is-invalid @enderror" value="{{ old('hire_date', optional($employee->hire_date)->format('Y-m-d')) }}">
                            </div>
                        </div>

                        {{-- Salary --}}
                        <div class="col-md-6 mb-4">
                            <label class="form-label-modern">Contractual Salary (ETB)</label>
                            <div class="input-group-modern d-flex align-items-center">
                                <span class="input-group-text-modern"><i class="bi bi-wallet2"></i></span>
                                <input type="number" step="0.01" name="salary" class="form-control @error('salary') is-invalid @enderror" value="{{ old('salary', $employee->salary) }}">
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        {{-- Profile Picture --}}
                        <div class="col-md-6 mb-4">
                            <label class="form-label-modern">Update Profile Picture</label>
                            <div class="input-group-modern d-flex align-items-center">
                                <span class="input-group-text-modern"><i class="bi bi-image"></i></span>
                                <input type="file" name="profile_picture" class="form-control" accept="image/*">
                            </div>
                        </div>

                        {{-- Status --}}
                        <div class="col-md-6 mb-4">
                            <label class="form-label-modern">Employment Status</label>
                            <div class="input-group-modern d-flex align-items-center">
                                <span class="input-group-text-modern"><i class="bi bi-info-circle"></i></span>
                                <select name="status" class="form-select @error('status') is-invalid @enderror">
                                    @foreach(['Active', 'On Leave', 'Terminated', 'Resigned'] as $status)
                                        <option value="{{ $status }}" {{ old('status', $employee->status) == $status ? 'selected' : '' }}>{{ $status }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="mt-5 d-flex gap-3">
                        <button type="submit" class="btn btn-erp-deep btn-lg rounded-pill px-5 flex-grow-1 fw-bold py-3 shadow-lg">
                            <i class="bi bi-check2-circle me-2"></i> Save Changes
                        </button>
                        <a href="{{ route('hr.employees.index') }}" class="btn btn-outline-secondary btn-lg rounded-pill px-5 py-3 fw-bold">
                            Discard
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
