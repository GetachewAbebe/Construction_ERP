@extends('layouts.app')
@section('title', 'Modify ' . ($user->role ?? 'User') . ' Profile')

@section('content')
<div class="row align-items-center mb-4 stagger-entrance">
    <div class="col">
        <h1 class="h3 mb-1 fw-800 text-erp-deep">Modify {{ $user->role ?? 'User' }} Profile</h1>
        <p class="text-muted mb-0">Adjusting operational credentials and personal identity anchoring.</p>
    </div>
    <div class="col-auto">
        <a href="{{ route($user->getProfileRouteName('show')) }}" class="btn btn-white rounded-pill px-4 shadow-sm border-0">
            <i class="bi bi-arrow-left me-2"></i>Return to Profile
        </a>
    </div>
</div>

<div class="stagger-entrance">
    <div class="card border-0 bg-white shadow-sm overflow-hidden" style="border-radius: 30px;">
        <div class="card-body p-4 p-md-5">
            <form method="POST" action="{{ route($user->getProfileRouteName('update')) }}" enctype="multipart/form-data">
                @csrf 
                @method('PUT')

                <div class="row g-4">
                    {{-- Profile Picture Section --}}
                    <div class="col-12 text-center mb-5">
                        <div class="position-relative d-inline-block">
                            <label for="profile_picture" class="cursor-pointer">
                                <div class="avatar-preview-box rounded-circle bg-light d-flex align-items-center justify-content-center overflow-hidden position-relative shadow-lg border border-5 border-white" style="width: 160px; height: 160px;">
                                    @if(optional($user->employee)->profile_picture_url)
                                        <img id="avatar_preview" src="{{ $user->employee->profile_picture_url }}" alt="Preview" class="w-100 h-100 object-fit-cover">
                                    @else
                                        <div id="avatar_placeholder" class="w-100 h-100 d-flex align-items-center justify-content-center bg-erp-deep text-white fs-1 fw-900">
                                            {{ substr($user->first_name, 0, 1) }}
                                        </div>
                                        <img id="avatar_preview" src="#" alt="Preview" class="w-100 h-100 object-fit-cover d-none">
                                    @endif
                                </div>
                                <div class="badge bg-primary rounded-circle position-absolute bottom-0 end-0 p-3 border border-4 border-white shadow-lg translate-middle-x mb-2">
                                    <i class="bi bi-camera-fill text-white fs-5"></i>
                                </div>
                            </label>
                            <input type="file" name="profile_picture" id="profile_picture" class="d-none" accept="image/*" onchange="previewAvatar(this)">
                            <div class="mt-4 fw-800 text-erp-deep text-uppercase small tracking-widest">Identify Photo</div>
                        </div>
                        @error('profile_picture') <div class="text-danger small mt-2 fw-bold">{{ $message }}</div> @enderror
                    </div>

                    {{-- Identity Section --}}
                    <div class="col-12">
                        <h5 class="fw-900 text-erp-deep mb-4 d-flex align-items-center gap-3">
                            <span class="bg-primary-soft p-2 rounded-3 text-primary"><i class="bi bi-person-badge"></i></span>
                            Personal Information
                        </h5>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label small fw-bold text-muted text-uppercase tracking-wider">Display Name</label>
                        <input name="name" value="{{ old('name', $user->name) }}" required 
                               class="form-control border-0 bg-light rounded-4 py-3 px-4 @error('name') is-invalid @enderror"/>
                        @error('name') <div class="invalid-feedback fw-bold">{{ $message }}</div> @enderror
                    </div>

                    <div class="col-md-6">
                        <label class="form-label small fw-bold text-muted text-uppercase tracking-wider">Email Address</label>
                        <input type="email" name="email" value="{{ old('email', $user->email) }}" required
                               class="form-control border-0 bg-light rounded-4 py-3 px-4 @error('email') is-invalid @enderror"/>
                        @error('email') <div class="invalid-feedback fw-bold">{{ $message }}</div> @enderror
                    </div>

                    {{-- Security Section --}}
                    <div class="col-12 mt-4">
                        <h5 class="fw-900 text-erp-deep mb-4 d-flex align-items-center gap-3">
                            <span class="bg-warning-soft p-2 rounded-3 text-warning"><i class="bi bi-shield-lock"></i></span>
                            Security & Access
                        </h5>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label small fw-bold text-muted text-uppercase tracking-wider">Change Password</label>
                        <input type="password" name="password" placeholder="Leave blank to keep current"
                               class="form-control border-0 bg-light rounded-4 py-3 px-4 @error('password') is-invalid @enderror"/>
                        <p class="small text-muted mt-2">Update this only if you want to rotate your credentials.</p>
                        @error('password') <div class="invalid-feedback fw-bold">{{ $message }}</div> @enderror
                    </div>

                    <div class="col-md-6">
                        <label class="form-label small fw-bold text-muted text-uppercase tracking-wider">Confirm New Password</label>
                        <input type="password" name="password_confirmation" placeholder="Re-type new password"
                               class="form-control border-0 bg-light rounded-4 py-3 px-4"/>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label small fw-bold text-muted text-uppercase tracking-wider">Contact Phone</label>
                        <input name="phone_number" value="{{ old('phone_number', $user->phone_number) }}" 
                               class="form-control border-0 bg-light rounded-4 py-3 px-4 @error('phone_number') is-invalid @enderror"/>
                        @error('phone_number') <div class="invalid-feedback fw-bold">{{ $message }}</div> @enderror
                    </div>

                    <div class="col-12 mt-4">
                        <label class="form-label small fw-bold text-muted text-uppercase tracking-wider">Professional Bio</label>
                        <textarea name="bio" rows="4" class="form-control border-0 bg-light rounded-4 py-3 px-4 shadow-sm" 
                                  placeholder="Brief description of your expertise and professional background...">{{ old('bio', $user->bio) }}</textarea>
                    </div>

                    <div class="col-12">
                        <div class="bg-light-soft p-4 rounded-4 border-start border-4 border-primary mt-4">
                            <div class="d-flex gap-3 align-items-center">
                                <i class="bi bi-info-circle-fill text-primary fs-4"></i>
                                <div>
                                    <h6 class="fw-bold text-erp-deep mb-1">Administrative Controlled Fields</h6>
                                    <p class="mb-0 text-muted small">Your Role ({{ $user->role }}), Department, and Job Position are managed by the System Administrator or HR. To update these, please contact the Support team.</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-12 text-end mt-5">
                        <button type="submit" class="btn btn-erp-deep rounded-pill px-5 py-3 fw-bold shadow-lg border-0">
                            Apply Identity Updates
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    function previewAvatar(input) {
        if (input.files && input.files[0]) {
            const reader = new FileReader();
            reader.onload = function(e) {
                const img = document.getElementById('avatar_preview');
                const placeholder = document.getElementById('avatar_placeholder');
                img.src = e.target.result;
                img.classList.remove('d-none');
                if(placeholder) placeholder.classList.add('d-none');
            }
            reader.readAsDataURL(input.files[0]);
        }
    }
</script>
@endsection
