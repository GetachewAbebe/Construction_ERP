@extends('layouts.app')

@section('title', 'Edit User | Admin')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-lg-10">
            {{-- Header --}}
            <div class="d-flex align-items-center mb-4">
                <h1 class="display-5 fw-bold text-erp-deep mb-0 tracking-tight">Edit Profile: {{ $user->name }}</h1>
            </div>

            <div class="glass-card-global p-4 p-md-5">
                <form method="POST" action="{{ route('admin.users.update', $user) }}">
                    @csrf 
                    @method('PUT')

                    <h5 class="fw-bold text-erp-primary mb-4 pb-2 border-bottom border-light">Account Essentials</h5>
                    <div class="row g-4 mb-5">
                        {{-- Full Name --}}
                        <div class="col-md-6">
                            <label class="form-label text-muted x-small fw-bold text-uppercase tracking-wider">Full Name</label>
                            <div class="input-group">
                                <span class="input-group-text bg-white border-end-0"><i class="bi bi-person text-secondary"></i></span>
                                <input name="name" value="{{ old('name', $user->name) }}" required placeholder="e.g. Abebe Kebede Tesfaye"
                                       class="form-control border-start-0 ps-0 shadow-none @error('name') is-invalid @enderror"/>
                            </div>
                            @error('name') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                        </div>

                        {{-- Email --}}
                        <div class="col-md-6">
                            <label class="form-label text-muted x-small fw-bold text-uppercase tracking-wider">Email Address</label>
                            <div class="input-group">
                                <span class="input-group-text bg-white border-end-0"><i class="bi bi-envelope text-secondary"></i></span>
                                <input type="email" name="email" value="{{ old('email', $user->email) }}" required placeholder="email@natanem.com"
                                       class="form-control border-start-0 ps-0 shadow-none @error('email') is-invalid @enderror"/>
                            </div>
                            @error('email') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                        </div>

                        {{-- Password --}}
                        <div class="col-md-6">
                            <label class="form-label text-muted x-small fw-bold text-uppercase tracking-wider">
                                New Password <span class="fw-normal text-muted ms-1">(Leave blank to keep current)</span>
                            </label>
                            <div class="input-group">
                                <span class="input-group-text bg-white border-end-0"><i class="bi bi-shield-lock text-secondary"></i></span>
                                <input type="password" name="password" placeholder="••••••••"
                                       class="form-control border-start-0 ps-0 shadow-none @error('password') is-invalid @enderror"/>
                            </div>
                            @error('password') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                        </div>

                        {{-- Role --}}
                        <div class="col-md-6">
                            <label class="form-label text-muted x-small fw-bold text-uppercase tracking-wider">System Role</label>
                            <div class="input-group">
                                <span class="input-group-text bg-white border-end-0"><i class="bi bi-shield-shaded text-secondary"></i></span>
                                <select name="role" required class="form-select border-start-0 ps-0 shadow-none cursor-pointer @error('role') is-invalid @enderror">
                                    @foreach($roles as $role)
                                        <option value="{{ $role }}" @selected($currentRole === $role)>
                                            {{ $role }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            @error('role') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                        </div>
                    </div>

                    <h5 class="fw-bold text-erp-primary mb-4 pb-2 border-bottom border-light">Professional Details</h5>
                    <div class="row g-4 mb-4">
                        {{-- Phone Number --}}
                        <div class="col-md-6">
                            <label class="form-label text-muted x-small fw-bold text-uppercase tracking-wider">Contact Number</label>
                            <div class="input-group">
                                <span class="input-group-text bg-white border-end-0"><i class="bi bi-telephone text-secondary"></i></span>
                                <input name="phone_number" value="{{ old('phone_number', $user->phone_number) }}" placeholder="+251 9..."
                                       class="form-control border-start-0 ps-0 shadow-none @error('phone_number') is-invalid @enderror"/>
                            </div>
                            @error('phone_number') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                        </div>

                         {{-- Status --}}
                         <div class="col-md-6">
                            <label class="form-label text-muted x-small fw-bold text-uppercase tracking-wider">Account Status</label>
                            <div class="input-group">
                                <span class="input-group-text bg-white border-end-0"><i class="bi bi-toggle-on text-secondary"></i></span>
                                <select name="status" class="form-select border-start-0 ps-0 shadow-none cursor-pointer @error('status') is-invalid @enderror">
                                    <option value="Active" @selected(old('status', $user->status) == 'Active')>Active</option>
                                    <option value="Inactive" @selected(old('status', $user->status) == 'Inactive')>Inactive</option>
                                    <option value="Suspended" @selected(old('status', $user->status) == 'Suspended')>Suspended</option>
                                </select>
                            </div>
                            @error('status') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                        </div>

                        {{-- Position --}}
                        <div class="col-md-6">
                            <label class="form-label text-muted x-small fw-bold text-uppercase tracking-wider">Job Position</label>
                            <div class="input-group">
                                <span class="input-group-text bg-white border-end-0"><i class="bi bi-briefcase text-secondary"></i></span>
                                <input name="position" value="{{ old('position', $user->position) }}" placeholder="e.g. Senior Engineer"
                                       class="form-control border-start-0 ps-0 shadow-none @error('position') is-invalid @enderror"/>
                            </div>
                            @error('position') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                        </div>

                        {{-- Department --}}
                        <div class="col-md-6">
                            <label class="form-label text-muted x-small fw-bold text-uppercase tracking-wider">Department</label>
                            <div class="input-group">
                                <span class="input-group-text bg-white border-end-0"><i class="bi bi-building text-secondary"></i></span>
                                <input name="department" value="{{ old('department', $user->department) }}" placeholder="e.g. Operations"
                                       class="form-control border-start-0 ps-0 shadow-none @error('department') is-invalid @enderror"/>
                            </div>
                            @error('department') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                        </div>

                        {{-- Bio --}}
                        <div class="col-12">
                            <label class="form-label text-muted x-small fw-bold text-uppercase tracking-wider">Bio / Notes</label>
                            <textarea name="bio" rows="3" class="form-control shadow-none @error('bio') is-invalid @enderror" placeholder="Additional notes about this user...">{{ old('bio', $user->bio) }}</textarea>
                            @error('bio') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                        </div>
                    </div>

                    <div class="mt-5 pt-3 border-top border-light">
                        <div class="d-flex gap-3">
                            <a href="{{ route('admin.users.index') }}" class="btn btn-outline-danger flex-grow-1 py-3 rounded-3 fw-bold shadow-sm d-flex align-items-center justify-content-center">
                                <span>Cancel</span>
                            </a>
                            <button type="submit" class="btn btn-success flex-grow-1 py-3 rounded-3 fw-bold shadow-sm d-flex align-items-center justify-content-center gap-2">
                                <i class="bi bi-check2-circle"></i>
                                <span>Save Account Modifications</span>
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
