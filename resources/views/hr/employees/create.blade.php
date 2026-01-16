@extends('layouts.app')
@section('title', 'Onboard Professional')

@section('content')
<div class="row align-items-center mb-4 stagger-entrance">
    <div class="col">
        <h1 class="h3 mb-1 fw-800 text-erp-deep">Onboard Professional</h1>
        <p class="text-muted mb-0">Integrate a new member into the organizational structure.</p>
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
                <form action="{{ route('hr.employees.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    
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
                                       value="{{ old('first_name') }}" 
                                       placeholder="e.g. John" required>
                                @error('first_name') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                            </div>
                            
                            <div class="col-md-6">
                                <label class="form-label small fw-800 text-muted text-uppercase">Family Name</label>
                                <input type="text" name="last_name" 
                                       class="form-control border-0 bg-light-soft rounded-4 py-3 px-4 shadow-sm @error('last_name') is-invalid @enderror" 
                                       value="{{ old('last_name') }}" 
                                       placeholder="e.g. Doe" required>
                                @error('last_name') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label small fw-800 text-muted text-uppercase">Work Email Address</label>
                                <input type="email" name="email" 
                                       class="form-control border-0 bg-light-soft rounded-4 py-3 px-4 shadow-sm @error('email') is-invalid @enderror" 
                                       value="{{ old('email') }}" 
                                       placeholder="john.doe@natanem.com" required>
                                @error('email') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label small fw-800 text-muted text-uppercase">Contact Phone</label>
                                <input type="text" name="phone" 
                                       class="form-control border-0 bg-light-soft rounded-4 py-3 px-4 shadow-sm @error('phone') is-invalid @enderror" 
                                       value="{{ old('phone') }}" 
                                       placeholder="+251 9...">
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
                                       value="{{ old('department_name') }}" 
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
                                       value="{{ old('position_title') }}" 
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
                                       value="{{ old('hire_date') }}">
                                @error('hire_date') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label small fw-800 text-muted text-uppercase">Monthly Gross Salary (ETB)</label>
                                <input type="number" step="0.01" name="salary" 
                                       class="form-control border-0 bg-light-soft rounded-4 py-3 px-4 shadow-sm @error('salary') is-invalid @enderror" 
                                       value="{{ old('salary') }}" 
                                       placeholder="0.00">
                                @error('salary') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label small fw-800 text-muted text-uppercase">Initial Employment Status</label>
                                <select name="status" class="form-select border-0 bg-light-soft rounded-4 py-3 px-4 shadow-sm @error('status') is-invalid @enderror">
                                    <option value="Active" {{ old('status') == 'Active' ? 'selected' : '' }}>Active - Reporting</option>
                                    <option value="On Leave" {{ old('status') == 'On Leave' ? 'selected' : '' }}>On Leave</option>
                                    <option value="Terminated" {{ old('status') == 'Terminated' ? 'selected' : '' }}>Terminated</option>
                                    <option value="Resigned" {{ old('status') == 'Resigned' ? 'selected' : '' }}>Resigned</option>
                                </select>
                                @error('status') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label small fw-800 text-muted text-uppercase">Profile Portrait</label>
                                <input type="file" name="profile_picture" 
                                       class="form-control border-0 bg-light-soft rounded-4 py-3 px-4 shadow-sm @error('profile_picture') is-invalid @enderror" 
                                       accept="image/*">
                                <small class="text-muted d-block mt-1">Professional headshot (JPG/PNG)</small>
                                @error('profile_picture') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                            </div>
                        </div>
                    </div>

                    <div class="d-flex gap-3 justify-content-end pt-4 border-top border-light">
                        <a href="{{ route('hr.employees.index') }}" class="btn btn-white rounded-pill px-4 py-3 fw-700 shadow-sm border-0">
                            <i class="bi bi-x-circle me-2"></i>Cancel Process
                        </a>
                        <button type="submit" class="btn btn-erp-deep rounded-pill px-5 py-3 fw-800 shadow-lg border-0">
                            <i class="bi bi-person-check me-2"></i>Complete Onboarding
                        </button>
                    </div>
                </form>
            </div>
        </div>
</div>
@endsection
