@extends('layouts.app')
@section('title', 'Modify Identity Archive')

@section('content')
<div class="row align-items-center mb-4 stagger-entrance">
    <div class="col">
        <h1 class="h3 mb-1 fw-800 text-erp-deep">Modify Identity Archive</h1>
        <p class="text-muted mb-0">Adjusting operational credentials and professional anchoring for: <span class="text-erp-deep fw-800">{{ $user->name }}</span></p>
    </div>
    <div class="col-auto">
        <a href="{{ route('admin.users.index') }}" class="btn btn-white rounded-pill px-4 shadow-sm border-0">
            <i class="bi bi-arrow-left me-2"></i>Return to Registry
        </a>
    </div>
</div>

<div class="row justify-content-center stagger-entrance">
    <div class="col-lg-10">
        <div class="card hardened-glass border-0 overflow-hidden shadow-lg">
            <div class="card-body p-4 p-md-5">
                <form method="POST" action="{{ route('admin.users.update', $user) }}" enctype="multipart/form-data">
                    @csrf 
                    @method('PUT')

                    <div class="row g-4 mb-5">
                        <div class="col-12 text-center mb-4">
                            <div class="position-relative d-inline-block">
                                <label for="profile_picture" class="cursor-pointer">
                                    <div class="avatar-preview-box rounded-circle bg-light-soft border border-2 d-flex align-items-center justify-content-center overflow-hidden position-relative shadow-sm" style="width: 140px; height: 140px;">
                                        @if(optional($user->employee)->profile_picture)
                                            <img id="avatar_preview" src="{{ asset('storage/' . $user->employee->profile_picture) }}" alt="Preview" class="w-100 h-100 object-fit-cover">
                                        @else
                                            <i class="bi bi-person-bounding-box fs-1 text-muted opacity-25" id="avatar_placeholder"></i>
                                            <img id="avatar_preview" src="#" alt="Preview" class="w-100 h-100 object-fit-cover d-none">
                                        @endif
                                    </div>
                                    <div class="badge bg-erp-deep rounded-pill position-absolute bottom-0 end-0 p-3 border border-3 border-white shadow-lg">
                                        <i class="bi bi-camera-fill text-white"></i>
                                    </div>
                                </label>
                                <input type="file" name="profile_picture" id="profile_picture" class="d-none" accept="image/*" onchange="previewAvatar(this)">
                            </div>
                            <div class="mt-3 fw-800 text-erp-deep text-uppercase small tracking-widest">Identify Revision</div>
                            @error('profile_picture') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                        </div>

                        <div class="col-12">
                            <h5 class="fw-800 text-erp-deep mb-4 d-flex align-items-center gap-2">
                                <i class="bi bi-shield-lock text-primary"></i>
                                Security & Authorization
                            </h5>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label small fw-800 text-muted text-uppercase">Legal Personnel Name</label>
                            <input name="name" value="{{ old('name', $user->name) }}" required
                                   class="form-control border-0 bg-light-soft rounded-4 py-3 px-4 shadow-sm @error('name') is-invalid @enderror"/>
                            @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label small fw-800 text-muted text-uppercase">Digital Signature (Email)</label>
                            <input type="email" name="email" value="{{ old('email', $user->email) }}" required
                                   class="form-control border-0 bg-light-soft rounded-4 py-3 px-4 shadow-sm @error('email') is-invalid @enderror"/>
                            @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label small fw-800 text-muted text-uppercase">Update Credential <span class="fw-normal text-muted lowercase">(Leave blank to retain)</span></label>
                            <input type="password" name="password" placeholder="••••••••"
                                   class="form-control border-0 bg-light-soft rounded-4 py-3 px-4 shadow-sm @error('password') is-invalid @enderror"/>
                            @error('password') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label small fw-800 text-muted text-uppercase">Authorization Tier (Role)</label>
                            <select name="role" required class="form-select border-0 bg-light-soft rounded-4 py-3 px-4 shadow-sm @error('role') is-invalid @enderror">
                                @foreach($roles as $role)
                                    <option value="{{ $role }}" @selected($user->role === $role)>{{ $role }}</option>
                                @endforeach
                            </select>
                            @error('role') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="col-12 mt-5">
                            <h5 class="fw-800 text-erp-deep mb-4 d-flex align-items-center gap-2">
                                <i class="bi bi-activity text-success"></i>
                                Professional Status & Metadata
                            </h5>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label small fw-800 text-muted text-uppercase">Telephonic Link</label>
                            <input name="phone_number" value="{{ old('phone_number', $user->phone_number) }}" 
                                   class="form-control border-0 bg-light-soft rounded-4 py-3 px-4 shadow-sm @error('phone_number') is-invalid @enderror"/>
                            @error('phone_number') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label small fw-800 text-muted text-uppercase">Account Vitality</label>
                            <select name="status" class="form-select border-0 bg-light-soft rounded-4 py-3 px-4 shadow-sm @error('status') is-invalid @enderror">
                                <option value="Active" @selected(old('status', $user->status) == 'Active')>Active Duty</option>
                                <option value="Inactive" @selected(old('status', $user->status) == 'Inactive')>Off-Registry</option>
                                <option value="Suspended" @selected(old('status', $user->status) == 'Suspended')>Access Terminated</option>
                            </select>
                            @error('status') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label small fw-800 text-muted text-uppercase">Assigned Designation</label>
                            <input name="position" value="{{ old('position', $user->position) }}" 
                                   class="form-control border-0 bg-light-soft rounded-4 py-3 px-4 shadow-sm @error('position') is-invalid @enderror"/>
                            @error('position') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label small fw-800 text-muted text-uppercase">Departmental Anchor</label>
                            <input name="department" value="{{ old('department', $user->department) }}" 
                                   class="form-control border-0 bg-light-soft rounded-4 py-3 px-4 shadow-sm @error('department') is-invalid @enderror"/>
                            @error('department') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                         <div class="col-12">
                            <label class="form-label small fw-800 text-muted text-uppercase">Professional Synopsis</label>
                            <textarea name="bio" rows="3" class="form-control border-0 bg-light-soft rounded-4 py-3 px-4 shadow-sm">{{ old('bio', $user->bio) }}</textarea>
                        </div>
                    </div>

                    <div class="text-end mt-5">
                        <button type="submit" class="btn btn-erp-deep rounded-pill px-5 py-3 fw-800 shadow-lg border-0">
                            Apply Archive Modifications
                        </button>
                    </div>
                </form>
            </div>
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
                if(placeholder) placeholder.classList.add('d-none');
            }
            reader.readAsDataURL(input.files[0]);
        }
    }
</script>
@endsection

