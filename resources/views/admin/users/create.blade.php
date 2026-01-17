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
                            <div class="mt-3 fw-800 text-erp-deep text-uppercase small tracking-widest">Digital Snapshot</div>
                            @error('profile_picture') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                        </div>

                        <div class="col-12">
                            <h5 class="fw-800 text-erp-deep mb-4 d-flex align-items-center gap-2">
                                <i class="bi bi-shield-check text-primary"></i>
                                Authentication Matrix
                            </h5>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label small fw-800 text-muted text-uppercase">Legal Personnel Name</label>
                            <input name="name" value="{{ old('name') }}" required placeholder="e.g. Getachew Abebe"
                                   class="form-control border-0 bg-light-soft rounded-4 py-3 px-4 shadow-sm @error('name') is-invalid @enderror"/>
                            @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label small fw-800 text-muted text-uppercase">Digital Signature (Email)</label>
                            <input type="email" name="email" value="{{ old('email') }}" required placeholder="corporate@natanem.com"
                                   class="form-control border-0 bg-light-soft rounded-4 py-3 px-4 shadow-sm @error('email') is-invalid @enderror"/>
                            @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label small fw-800 text-muted text-uppercase">Access Credential (Password)</label>
                            <input type="password" name="password" required placeholder="Min. 8 Entropy Units"
                                   class="form-control border-0 bg-light-soft rounded-4 py-3 px-4 shadow-sm @error('password') is-invalid @enderror"/>
                            @error('password') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label small fw-800 text-muted text-uppercase">Authorization Tier (Role)</label>
                            <select name="role" required class="form-select border-0 bg-light-soft rounded-4 py-3 px-4 shadow-sm @error('role') is-invalid @enderror">
                                <option value="">Select Command Level...</option>
                                @foreach($roles as $role)
                                    <option value="{{ $role }}" @selected(old('role')===$role)>{{ $role }}</option>
                                @endforeach
                            </select>
                            @error('role') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="col-12 mt-5">
                            <h5 class="fw-800 text-erp-deep mb-4 d-flex align-items-center gap-2">
                                <i class="bi bi-briefcase-fill text-success"></i>
                                Professional Placement
                            </h5>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label small fw-800 text-muted text-uppercase">Telephonic Link</label>
                            <input name="phone_number" value="{{ old('phone_number') }}" placeholder="+251 ..."
                                   class="form-control border-0 bg-light-soft rounded-4 py-3 px-4 shadow-sm @error('phone_number') is-invalid @enderror"/>
                            @error('phone_number') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label small fw-800 text-muted text-uppercase">Operational Status</label>
                            <select name="status" class="form-select border-0 bg-light-soft rounded-4 py-3 px-4 shadow-sm @error('status') is-invalid @enderror">
                                <option value="Active" @selected(old('status') == 'Active')>Active Duty</option>
                                <option value="Inactive" @selected(old('status') == 'Inactive')>Off-Registry</option>
                                <option value="Suspended" @selected(old('status') == 'Suspended')>Access Terminated</option>
                            </select>
                            @error('status') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label small fw-800 text-muted text-uppercase">Assigned Designation</label>
                            <input name="position" value="{{ old('position') }}" placeholder="e.g. Lead Technologist"
                                   class="form-control border-0 bg-light-soft rounded-4 py-3 px-4 shadow-sm @error('position') is-invalid @enderror"/>
                            @error('position') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label small fw-800 text-muted text-uppercase">Departmental Anchor</label>
                            <input name="department" value="{{ old('department') }}" placeholder="e.g. Infrastructure"
                                   class="form-control border-0 bg-light-soft rounded-4 py-3 px-4 shadow-sm @error('department') is-invalid @enderror"/>
                            @error('department') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                         <div class="col-12">
                            <label class="form-label small fw-800 text-muted text-uppercase">Professional Synopsis</label>
                            <textarea name="bio" rows="3" class="form-control border-0 bg-light-soft rounded-4 py-3 px-4 shadow-sm" placeholder="Summarize associate background..."></textarea>
                        </div>
                    </div>

                    <div class="text-end mt-5">
                        <button type="submit" class="btn btn-erp-deep rounded-pill px-5 py-3 fw-800 shadow-lg border-0">
                            Confirm Identity Provisioning
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

