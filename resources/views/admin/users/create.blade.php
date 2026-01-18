@extends('layouts.app')
@section('title', 'Provision Operational Identity')

@section('content')
<div class="row align-items-center mb-4 stagger-entrance">
    <div class="col">
        <h1 class="h3 mb-1 fw-800 text-erp-deep">Provision Operational Identity</h1>
        <p class="text-muted mb-0">Establish new administrative credentials and professional anchoring for workforce associates.</p>
    </div>
    <div class="col-auto">
        <a href="{{ route('admin.users.index') }}" class="btn btn-white rounded-pill px-4 shadow-sm border-0">
            <i class="bi bi-arrow-left me-2"></i>Abort Provisioning
        </a>
    </div>
</div>

<div class="stagger-entrance">
    <div class="card border-0 bg-white shadow-sm">
        <div class="card-body p-4 p-md-5">
                <form method="POST" action="{{ route('admin.users.store') }}" enctype="multipart/form-data">
                    @csrf

                    <div class="row g-4 mb-5">
                        <div class="col-12 text-center mb-4">
                            <div class="position-relative d-inline-block">
                                <label for="profile_picture" class="cursor-pointer">
                                    <div class="avatar-preview-box rounded-circle bg-light-soft border border-2 d-flex align-items-center justify-content-center overflow-hidden position-relative shadow-sm" style="width: 140px; height: 140px;">
                                        <i class="bi bi-camera-fill fs-1 text-muted opacity-25" id="avatar_placeholder"></i>
                                        <img id="avatar_preview" src="#" alt="Preview" class="w-100 h-100 object-fit-cover d-none">
                                    </div>
                                    <div class="badge bg-erp-deep rounded-pill position-absolute bottom-0 end-0 p-3 border border-3 border-white shadow-lg">
                                        <i class="bi bi-plus-lg text-white"></i>
                                    </div>
                                </label>
                                <input type="file" name="profile_picture" id="profile_picture" class="d-none" accept="image/*" onchange="previewAvatar(this)">
                            </div>
                            <div class="mt-3 fw-bold text-erp-deep text-uppercase small tracking-wide">Profile Picture</div>
                            @error('profile_picture') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                        </div>

                        <div class="col-12">
                            <h5 class="fw-bold text-erp-deep mb-4 d-flex align-items-center gap-2">
                                <i class="bi bi-shield-lock text-primary"></i>
                                Account & Security
                            </h5>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-muted text-uppercase">Full Name</label>
                            <input name="name" value="{{ old('name', $employee ? $employee->first_name . ' ' . $employee->last_name : '') }}" required placeholder="e.g. Getachew Abebe"
                                   class="form-control border-0 bg-light-soft rounded-4 py-3 px-4 shadow-sm @error('name') is-invalid @enderror"/>
                            @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-muted text-uppercase">Email Address</label>
                            <input type="email" name="email" value="{{ old('email', $employee ? $employee->email : '') }}" required placeholder="corporate@natanem.com"
                                   class="form-control border-0 bg-light-soft rounded-4 py-3 px-4 shadow-sm @error('email') is-invalid @enderror"/>
                            @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-muted text-uppercase">Password</label>
                            <input type="password" name="password" required placeholder="Min. 8 characters"
                                   class="form-control border-0 bg-light-soft rounded-4 py-3 px-4 shadow-sm @error('password') is-invalid @enderror"/>
                            @error('password') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-muted text-uppercase">System Role</label>
                            <select name="role" required class="form-select border-0 bg-light-soft rounded-4 py-3 px-4 shadow-sm @error('role') is-invalid @enderror">
                                <option value="">Select Role...</option>
                                @foreach($roles as $role)
                                    <option value="{{ $role }}" @selected(old('role')===$role)>{{ $role }}</option>
                                @endforeach
                            </select>
                            @error('role') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="col-12 mt-4">
                            <h5 class="fw-bold text-erp-deep mb-4 d-flex align-items-center gap-2">
                                <i class="bi bi-person-badge text-success"></i>
                                Professional Details
                            </h5>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-muted text-uppercase">Phone Number</label>
                            <input name="phone_number" value="{{ old('phone_number', $employee ? $employee->phone : '') }}" placeholder="+251 ..."
                                   class="form-control border-0 bg-light-soft rounded-4 py-3 px-4 shadow-sm @error('phone_number') is-invalid @enderror"/>
                            @error('phone_number') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-muted text-uppercase">Status</label>
                            <select name="status" class="form-select border-0 bg-light-soft rounded-4 py-3 px-4 shadow-sm @error('status') is-invalid @enderror">
                                <option value="Active" @selected(old('status') == 'Active')>Active</option>
                                <option value="Inactive" @selected(old('status') == 'Inactive')>Inactive</option>
                                <option value="Suspended" @selected(old('status') == 'Suspended')>Suspended</option>
                            </select>
                            @error('status') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-muted text-uppercase">Job Title / Position</label>
                            <input name="position" value="{{ old('position', $employee ? $employee->position : '') }}" placeholder="e.g. Civil Engineer"
                                   class="form-control border-0 bg-light-soft rounded-4 py-3 px-4 shadow-sm @error('position') is-invalid @enderror"/>
                            @error('position') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-muted text-uppercase">Department</label>
                            <input name="department" value="{{ old('department', $employee ? $employee->department : '') }}" placeholder="e.g. Engineering"
                                   class="form-control border-0 bg-light-soft rounded-4 py-3 px-4 shadow-sm @error('department') is-invalid @enderror"/>
                            @error('department') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-muted text-uppercase">Hire Date</label>
                            <input type="date" name="hire_date" value="{{ old('hire_date', $employee ? ($employee->hire_date ? $employee->hire_date->format('Y-m-d') : '') : date('Y-m-d')) }}"
                                   class="form-control border-0 bg-light-soft rounded-4 py-3 px-4 shadow-sm @error('hire_date') is-invalid @enderror"/>
                            @error('hire_date') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-muted text-uppercase">Initial Salary (ETB)</label>
                            <input type="number" step="0.01" name="salary" value="{{ old('salary', $employee ? $employee->salary : '') }}" placeholder="0.00"
                                   class="form-control border-0 bg-light-soft rounded-4 py-3 px-4 shadow-sm @error('salary') is-invalid @enderror"/>
                            @error('salary') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                    </div>

                    <div class="text-end mt-5">
                        <button type="submit" class="btn btn-erp-deep rounded-pill px-5 py-3 fw-bold shadow-lg border-0">
                            Create User
                        </button>
                    </div>
                </form>
            </div>
        </div>
</div>

<script>
    function previewAvatar(input) {
        if (input.files && input.files[0]) {
            var reader = new FileReader();
            reader.onload = function(e) {
                var img = document.getElementById('avatar_preview');
                var placeholder = document.getElementById('avatar_placeholder');
                img.src = e.target.result;
                img.classList.remove('d-none');
                placeholder.classList.add('d-none');
            }
            reader.readAsDataURL(input.files[0]);
        }
    }
</script>
@endsection

