@extends('layouts.app')
@section('title', 'Edit Professional Profile')

@section('content')
<div class="row align-items-center mb-4 stagger-entrance">
    <div class="col">
        <h1 class="h3 mb-1 fw-800 text-erp-deep">Edit Professional Profile</h1>
        <p class="text-muted mb-0">Update organizational records for {{ $employee->first_name }} {{ $employee->last_name }}.</p>
    </div>
    <div class="col-auto">
        <a href="{{ route('hr.employees.index') }}" class="btn btn-white rounded-pill px-4 shadow-sm border-0">
            <i class="bi bi-arrow-left me-2"></i>Return to Directory
        </a>
    </div>
</div>

<div class="stagger-entrance">
    <div class="card border-0 bg-white shadow-sm">
        <div class="card-body p-4 p-md-5">
                <form action="{{ route('hr.employees.update', $employee) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    
                    <div class="text-center mb-5">
                        <div class="position-relative d-inline-block">
                            @if($employee->profile_picture)
                                <img src="{{ asset('storage/' . $employee->profile_picture) }}" 
                                     alt="Profile" 
                                     class="rounded-circle shadow-lg border border-4 border-white" 
                                     width="120" height="120" style="object-fit: cover;">
                            @else
                                <div class="rounded-circle bg-light-soft d-flex align-items-center justify-content-center shadow-lg border border-4 border-white" 
                                     style="width: 120px; height: 120px;">
                                    <i class="bi bi-person text-secondary fs-1"></i>
                                </div>
                            @endif
                            <label for="profile_picture_input" class="position-absolute bottom-0 end-0 bg-white rounded-circle shadow-sm p-2 cursor-pointer border user-select-none" style="transform: translate(10%, 10%);" title="Change Photo">
                                <i class="bi bi-camera-fill text-primary"></i>
                                <input type="file" id="profile_picture_input" name="profile_picture" class="d-none" accept="image/*">
                            </label>
                        </div>
                        <h4 class="fw-800 text-erp-deep mt-3">{{ $employee->first_name }} {{ $employee->last_name }}</h4>
                        <span class="badge bg-primary-soft text-primary rounded-pill px-3">{{ $employee->position ?? 'Unassigned' }}</span>
                    </div>

                    <div class="mb-5">
                        <h5 class="fw-800 text-erp-deep mb-4 d-flex align-items-center gap-2">
                            <i class="bi bi-person-vcard text-primary"></i>
                            Personal Identity
                        </h5>
                        
                        <div class="row g-4">
                            <div class="col-md-6">
                                <label class="form-label small fw-800 text-muted text-uppercase">Legal First Name</label>
                                <input type="text" name="first_name" 
                                       class="form-control border-0 bg-light-soft rounded-4 py-3 px-4 shadow-sm @error('first_name') is-invalid @enderror" 
                                       value="{{ old('first_name', $employee->first_name) }}" 
                                       required>
                                @error('first_name') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                            </div>
                            
                            <div class="col-md-6">
                                <label class="form-label small fw-800 text-muted text-uppercase">Family Name</label>
                                <input type="text" name="last_name" 
                                       class="form-control border-0 bg-light-soft rounded-4 py-3 px-4 shadow-sm @error('last_name') is-invalid @enderror" 
                                       value="{{ old('last_name', $employee->last_name) }}" 
                                       required>
                                @error('last_name') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label small fw-800 text-muted text-uppercase">Work Email Address</label>
                                <input type="email" name="email" 
                                       class="form-control border-0 bg-light-soft rounded-4 py-3 px-4 shadow-sm @error('email') is-invalid @enderror" 
                                       value="{{ old('email', $employee->email) }}" 
                                       required>
                                @error('email') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label small fw-800 text-muted text-uppercase">Contact Phone</label>
                                <input type="text" name="phone" 
                                       class="form-control border-0 bg-light-soft rounded-4 py-3 px-4 shadow-sm @error('phone') is-invalid @enderror" 
                                       value="{{ old('phone', $employee->phone) }}">
                                @error('phone') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                            </div>
                        </div>
                    </div>

                    <div class="mb-5">
                        <h5 class="fw-800 text-erp-deep mb-4 d-flex align-items-center gap-2">
                            <i class="bi bi-briefcase text-success"></i>
                            Organizational Assignment
                        </h5>

                        <div class="row g-4">
                            <div class="col-md-6">
                                <label class="form-label small fw-800 text-muted text-uppercase">Department Alignment</label>
                                <input type="text" name="department_name" list="departmentList" 
                                       class="form-control border-0 bg-light-soft rounded-4 py-3 px-4 shadow-sm @error('department_name') is-invalid @enderror" 
                                       value="{{ old('department_name', $employee->department) }}" 
                                       placeholder="Select or type department...">
                                <datalist id="departmentList">
                                    @foreach($departments as $dept)
                                        <option value="{{ $dept->name }}">
                                    @endforeach
                                </datalist>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label small fw-800 text-muted text-uppercase">Job Title / Designation</label>
                                <input type="text" name="position_title" list="positionList" 
                                       class="form-control border-0 bg-light-soft rounded-4 py-3 px-4 shadow-sm @error('position_title') is-invalid @enderror" 
                                       value="{{ old('position_title', $employee->position) }}" 
                                       placeholder="Select or type position...">
                                <datalist id="positionList">
                                    @foreach($positions as $pos)
                                        <option value="{{ $pos->title }}">
                                    @endforeach
                                </datalist>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label small fw-800 text-muted text-uppercase">Official Hire Date</label>
                                <input type="date" name="hire_date" 
                                       class="form-control border-0 bg-light-soft rounded-4 py-3 px-4 shadow-sm @error('hire_date') is-invalid @enderror" 
                                       value="{{ old('hire_date', optional($employee->hire_date)->format('Y-m-d')) }}">
                                @error('hire_date') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label small fw-800 text-muted text-uppercase">Monthly Gross Salary (ETB)</label>
                                <input type="number" step="0.01" name="salary" 
                                       class="form-control border-0 bg-light-soft rounded-4 py-3 px-4 shadow-sm @error('salary') is-invalid @enderror" 
                                       value="{{ old('salary', $employee->salary) }}">
                                @error('salary') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label small fw-800 text-muted text-uppercase">Employment Status</label>
                                <select name="status" class="form-select border-0 bg-light-soft rounded-4 py-3 px-4 shadow-sm @error('status') is-invalid @enderror">
                                    @foreach(['Active', 'On Leave', 'Terminated', 'Resigned'] as $status)
                                        <option value="{{ $status }}" {{ old('status', $employee->status) == $status ? 'selected' : '' }}>{{ $status }}</option>
                                    @endforeach
                                </select>
                                @error('status') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                            </div>
                        </div>
                    </div>

                    <div class="d-flex gap-3 justify-content-end pt-4 border-top border-light">
                        <a href="{{ route('hr.employees.index') }}" class="btn btn-white rounded-pill px-4 py-3 fw-700 shadow-sm border-0">
                            <i class="bi bi-x-circle me-2"></i>Discard Changes
                        </a>
                        <button type="submit" class="btn btn-erp-deep rounded-pill px-5 py-3 fw-800 shadow-lg border-0">
                            <i class="bi bi-check2-circle me-2"></i>Update Profile
                        </button>
                    </div>
                </form>
            </div>
        </div>
</div>
@endsection
