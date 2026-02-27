@extends('layouts.app')
@section('title', 'Provision Operational Identity')

@section('content')
<div class="page-header-premium">
    <div class="row align-items-center">
        <div class="col">
            <h1>Provision Operational Identity</h1>
            <p>Establish new administrative credentials and professional anchoring for workforce associates.</p>
        </div>
        <div class="col-auto">
            <a href="{{ route('admin.users.index') }}" class="btn btn-white rounded-pill px-4 shadow-sm border-0">
                <i class="bi bi-arrow-left me-2"></i>Abort Provisioning
            </a>
        </div>
    </div>
</div>

<div class="stagger-entrance">
    <div class="erp-card">
        <form method="POST" action="{{ route('admin.users.store') }}" enctype="multipart/form-data">
            @csrf

            <div class="row g-4 mb-5">
                <div class="col-12 text-center mb-4">
                    <div class="position-relative d-inline-block">
                        <label for="profile_picture" class="cursor-pointer">
                            <div class="avatar-preview-box rounded-circle bg-light border border-2 d-flex align-items-center justify-content-center overflow-hidden position-relative shadow-sm" style="width: 140px; height: 140px;">
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
                    <label class="erp-label">Full Name</label>
                    <input name="name" value="{{ old('name', $employee ? $employee->first_name . ' ' . $employee->last_name : '') }}" required placeholder="e.g. Getachew Abebe"
                           class="erp-input @error('name') is-invalid @enderror"/>
                    @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="col-md-6">
                    <label class="erp-label">Email Address</label>
                    <input type="email" name="email" value="{{ old('email', $employee ? $employee->email : '') }}" required placeholder="corporate@natanem.com"
                           class="erp-input @error('email') is-invalid @enderror"/>
                    @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="col-md-6">
                    <label class="erp-label">Password</label>
                    <div class="input-group">
                        <input type="password" name="password" id="password" required placeholder="Min. 8 characters" onkeyup="checkStrength(this.value)"
                            class="erp-input form-control border-end-0 rounded-end-0 @error('password') is-invalid @enderror"/>
                        <button class="btn btn-secondary border-start-0" type="button" onclick="togglePasswordVis('password', 'admin-eye-text')" style="min-width: 80px;">
                            <span id="admin-eye-text">Show</span> <i class="bi bi-eye ms-1"></i>
                        </button>
                    </div>
                    {{-- Strength Meter --}}
                    <div class="mt-2 d-none" id="strength-container">
                        <div class="progress" style="height: 4px;">
                            <div class="progress-bar transition-all" role="progressbar" id="strength-bar" style="width: 0%"></div>
                        </div>
                        <div class="d-flex justify-content-between align-items-center mt-1">
                            <small class="text-muted" style="font-size: 0.75rem;">Strength</small>
                            <small class="fw-bold" id="strength-text" style="font-size: 0.75rem;">Weak</small>
                        </div>
                    </div>
                    @error('password') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                </div>

                <div class="col-md-6">
                    <label class="erp-label">System Role</label>
                    <select name="role" required class="erp-input @error('role') is-invalid @enderror" style="appearance: auto;">
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
                    <label class="erp-label">Phone Number</label>
                    <input name="phone_number" value="{{ old('phone_number', $employee ? $employee->phone : '') }}" placeholder="+251 ..."
                           class="erp-input @error('phone_number') is-invalid @enderror"/>
                    @error('phone_number') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="col-md-6">
                    <label class="erp-label">Status</label>
                    <select name="status" class="erp-input @error('status') is-invalid @enderror" style="appearance: auto;">
                        <option value="Active" @selected(old('status') == 'Active')>Active</option>
                        <option value="Inactive" @selected(old('status') == 'Inactive')>Inactive</option>
                        <option value="Suspended" @selected(old('status') == 'Suspended')>Suspended</option>
                    </select>
                    @error('status') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="col-md-6">
                    <label class="erp-label">Job Title / Position</label>
                    <input name="position" value="{{ old('position', $employee ? $employee->position : '') }}" placeholder="e.g. Civil Engineer"
                           class="erp-input @error('position') is-invalid @enderror"/>
                    @error('position') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="col-md-6">
                    <label class="erp-label">Department</label>
                    <input name="department" value="{{ old('department', $employee ? $employee->department : '') }}" placeholder="e.g. Engineering"
                           class="erp-input @error('department') is-invalid @enderror"/>
                    @error('department') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="col-md-6">
                    <label class="erp-label">Hire Date</label>
                    <input type="date" name="hire_date" value="{{ old('hire_date', $employee ? ($employee->hire_date ? $employee->hire_date->format('Y-m-d') : '') : date('Y-m-d')) }}"
                           class="erp-input @error('hire_date') is-invalid @enderror"/>
                    @error('hire_date') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="col-md-6">
                    <label class="erp-label">Initial Salary (ETB)</label>
                    <input type="number" step="0.01" name="salary" value="{{ old('salary', $employee ? $employee->salary : '') }}" placeholder="0.00"
                           class="erp-input @error('salary') is-invalid @enderror"/>
                    @error('salary') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

            </div>

            <div class="text-end mt-5">
                <button type="submit" class="btn btn-erp-deep rounded-pill px-5 py-3 fw-bold shadow-lg border-0">
                    Create Operational Identity
                </button>
            </div>
        </form>
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

    function togglePasswordVis(inputId, iconId) {
        const input = document.getElementById(inputId);
        const icon = document.getElementById(iconId);
        if (input.type === "password") {
            input.type = "text";
            icon.classList.remove('bi-eye-slash');
            icon.classList.add('bi-eye');
        } else {
            input.type = "password";
            icon.classList.remove('bi-eye');
            icon.classList.add('bi-eye-slash');
        }
    }

    function checkStrength(password) {
        const container = document.getElementById('strength-container');
        const bar = document.getElementById('strength-bar');
        const text = document.getElementById('strength-text');
        
        if (password.length > 0) {
            container.classList.remove('d-none');
        } else {
            container.classList.add('d-none');
            return;
        }

        let strength = 0;
        if (password.length >= 8) strength += 1;
        if (password.match(/[A-Z]/)) strength += 1;
        if (password.match(/[0-9]/)) strength += 1;
        if (password.match(/[^a-zA-Z0-9]/)) strength += 1;

        if (password.length < 8) {
            strength = 0; 
        }

        switch(strength) {
            case 0:
            case 1:
                bar.style.width = "33%";
                bar.className = "progress-bar bg-danger";
                text.innerHTML = "Weak";
                text.className = "fw-bold text-danger";
                break;
            case 2:
            case 3:
                bar.style.width = "66%";
                bar.className = "progress-bar bg-warning";
                text.innerHTML = "Medium";
                text.className = "fw-bold text-warning";
                break;
            case 4:
                bar.style.width = "100%";
                bar.className = "progress-bar bg-success";
                text.innerHTML = "Strong";
                text.className = "fw-bold text-success";
                break;
        }
    }
</script>
@endsection
