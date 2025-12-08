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
                        <form method="POST" action="{{ route('hr.employees.update', $employee) }}">
                            @csrf
                            @method('PUT')

                            <div class="row g-3">
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
                                    <select name="department_id" class="form-select form-select-sm">
                                        <option value="">Select Department...</option>
                                        @foreach($departments as $dept)
                                            <option value="{{ $dept->id }}"
                                                {{ old('department_id', $employee->department_id) == $dept->id ? 'selected' : '' }}>
                                                {{ $dept->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label small text-muted">Position</label>
                                    <select name="position_id" class="form-select form-select-sm">
                                        <option value="">Select Position...</option>
                                        @foreach($positions as $pos)
                                            <option value="{{ $pos->id }}"
                                                {{ old('position_id', $employee->position_id) == $pos->id ? 'selected' : '' }}>
                                                {{ $pos->title }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="col-12">
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
