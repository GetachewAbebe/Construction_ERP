@extends('layouts.app')
@section('title','Edit Employee')

@section('content')
    <div class="container py-4">

        {{-- HEADER --}}
        <div class="row mb-3">
            <div class="col d-flex align-items-center justify-content-between">
                <div>
                    <h1 class="h4 mb-1 text-erp-deep">Edit Employee</h1>
                    <p class="text-muted small mb-0">
                        Update basic details for this employee.
                    </p>
                </div>
                <a href="{{ route('hr.employees.index') }}" class="btn btn-sm btn-outline-secondary">
                    Cancel &amp; Back
                </a>
            </div>
        </div>

        {{-- FULL-WIDTH FORM CARD --}}
        <div class="row">
            <div class="col-12">
                <div class="card shadow-soft border-0">
                    <div class="card-body">
                        <form method="POST" action="{{ route('hr.employees.update', $employee) }}" enctype="multipart/form-data">
                            @csrf
                            @method('PUT')

                            <div class="row g-3">
                                {{-- Current Profile Picture Preview --}}
                                @if($employee->profile_picture)
                                    <div class="col-12 text-center mb-2">
                                        <img src="{{ asset('storage/' . $employee->profile_picture) }}" alt="Profile" class="rounded-circle" width="100" height="100" style="object-fit: cover;">
                                    </div>
                                @endif

                                <div class="col-md-6">
                                    <label class="form-label small text-muted">First name</label>
                                    <input
                                        name="first_name"
                                        required
                                        value="{{ old('first_name', $employee->first_name) }}"
                                        class="form-control form-control-sm @error('first_name') is-invalid @enderror"
                                    >
                                    @error('first_name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label small text-muted">Last name</label>
                                    <input
                                        name="last_name"
                                        required
                                        value="{{ old('last_name', $employee->last_name) }}"
                                        class="form-control form-control-sm @error('last_name') is-invalid @enderror"
                                    >
                                    @error('last_name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label small text-muted">Department</label>
                                    <input 
                                        type="text" 
                                        name="department_name" 
                                        list="departmentList" 
                                        class="form-control form-control-sm" 
                                        placeholder="Type or select department..."
                                        value="{{ old('department_name', $employee->department) }}"
                                    >
                                    <datalist id="departmentList">
                                        @foreach($departments as $dept)
                                            <option value="{{ $dept->name }}">
                                        @endforeach
                                    </datalist>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label small text-muted">Position</label>
                                    <input 
                                        type="text" 
                                        name="position_title" 
                                        list="positionList" 
                                        class="form-control form-control-sm" 
                                        placeholder="Type or select position..."
                                        value="{{ old('position_title', $employee->position) }}"
                                    >
                                    <datalist id="positionList">
                                        @foreach($positions as $pos)
                                            <option value="{{ $pos->title }}">
                                        @endforeach
                                    </datalist>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label small text-muted">Phone Number</label>
                                    <input 
                                        type="text" 
                                        name="phone" 
                                        class="form-control form-control-sm @error('phone') is-invalid @enderror"
                                        value="{{ old('phone', $employee->phone) }}"
                                    >
                                    @error('phone')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>



                                <div class="col-md-6">
                                    <label class="form-label small text-muted">Email</label>
                                    <input
                                        type="email"
                                        name="email"
                                        required
                                        value="{{ old('email', $employee->email) }}"
                                        class="form-control form-control-sm @error('email') is-invalid @enderror"
                                    >
                                    @error('email')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label small text-muted">Hire date</label>
                                    <input
                                        type="date"
                                        name="hire_date"
                                        value="{{ old('hire_date', optional($employee->hire_date)->format('Y-m-d')) }}"
                                        class="form-control form-control-sm @error('hire_date') is-invalid @enderror"
                                    >
                                    @error('hire_date')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label small text-muted">Salary</label>
                                    <input
                                        type="number"
                                        step="0.01"
                                        name="salary"
                                        value="{{ old('salary', $employee->salary) }}"
                                        class="form-control form-control-sm @error('salary') is-invalid @enderror"
                                    >
                                    @error('salary')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label small text-muted">Profile Picture</label>
                                    <input 
                                        type="file" 
                                        name="profile_picture" 
                                        class="form-control form-control-sm @error('profile_picture') is-invalid @enderror"
                                        accept="image/*"
                                    >
                                    @error('profile_picture')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label small text-muted">Status</label>
                                    <select 
                                        name="status" 
                                        class="form-select form-select-sm @error('status') is-invalid @enderror"
                                    >
                                        <option value="Active" {{ old('status', $employee->status) == 'Active' ? 'selected' : '' }}>Active</option>
                                        <option value="On Leave" {{ old('status', $employee->status) == 'On Leave' ? 'selected' : '' }}>On Leave</option>
                                        <option value="Terminated" {{ old('status', $employee->status) == 'Terminated' ? 'selected' : '' }}>Terminated</option>
                                        <option value="Resigned" {{ old('status', $employee->status) == 'Resigned' ? 'selected' : '' }}>Resigned</option>
                                    </select>
                                    @error('status')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            @if ($errors->any())
                                <div class="mt-3 text-danger small">
                                    {{ $errors->first() }}
                                </div>
                            @endif

                            <div class="mt-4 d-flex gap-2">
                                <button class="btn btn-sm btn-success">
                                    Update
                                </button>
                                <a href="{{ route('hr.employees.index') }}" class="btn btn-sm btn-outline-secondary">
                                    Cancel
                                </a>
                            </div>

                        </form>
                    </div>
                </div>
            </div>
        </div>

    </div>
@endsection
