@extends('layouts.app')
@section('title', ($user->role ?? 'User') . ' Profile')

@section('content')
<div class="row align-items-center mb-4 stagger-entrance">
    <div class="col">
        <h1 class="h3 mb-1 fw-800 text-erp-deep">{{ $user->role ?? 'User' }} Profile</h1>
        <p class="text-muted mb-0">Overview of your professional anchoring and system identity.</p>
    </div>
</div>

<div class="stagger-entrance">
    <div class="card border-0 bg-white shadow-sm overflow-hidden" style="border-radius: 35px;">
        <div class="card-body p-0">
            {{-- Professional Header with Mesh Gradient Background --}}
            <div class="p-5 text-center position-relative" style="background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);">
                {{-- Subtle Mesh Gradient Overlay --}}
                <div class="position-absolute top-0 start-0 w-100 h-100 opacity-25" 
                     style="background-image: radial-gradient(at 0% 0%, rgba(59, 130, 246, 0.15) 0px, transparent 50%), 
                                            radial-gradient(at 100% 0%, rgba(16, 185, 129, 0.15) 0px, transparent 50%);"></div>
                
                <div class="position-relative z-index-1">
                    <div class="mb-4">
                        <div class="avatar-preview-box rounded-circle bg-white d-flex align-items-center justify-content-center overflow-hidden position-relative shadow-lg border border-5 border-white mx-auto" 
                             style="width: 180px; height: 180px; transition: transform 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);">
                            @php 
                                $avatarUrl = optional($user->employee)->profile_picture_url;
                                $initial = substr($user->first_name, 0, 1);
                            @endphp
                            @if($avatarUrl)
                                <img src="{{ $avatarUrl }}" class="w-100 h-100 object-fit-cover image-premium-zoom"
                                     onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                            @endif
                            <div class="w-100 h-100 d-flex align-items-center justify-content-center bg-erp-deep text-white fw-900" 
                                 style="display: {{ $avatarUrl ? 'none' : 'flex' }}; font-size: 5rem; background: linear-gradient(135deg, #0f172a 0%, #334155 100%);">
                                {{ $initial }}
                            </div>
                        </div>
                    </div>
                    <h1 class="fw-900 text-erp-deep mb-2 display-6">{{ $user->name }}</h1>
                    <div class="d-flex align-items-center justify-content-center gap-2 mb-3">
                        <span class="badge bg-primary rounded-pill px-4 py-2 border-0 fw-800 text-uppercase tracking-widest shadow-sm">
                            <i class="bi bi-shield-check me-2"></i>{{ $user->role }}
                        </span>
                        <span class="badge bg-white text-erp-deep rounded-pill px-4 py-2 border border-light shadow-sm fw-800 text-uppercase tracking-widest">
                             ID #{{ str_pad($user->id, 5, '0', STR_PAD_LEFT) }}
                        </span>
                    </div>
                </div>
            </div>

            <div class="p-5 bg-white">
                <div class="row g-5">
                    {{-- Left Column: Identity Info --}}
                    <div class="col-lg-6">
                        <div class="p-4 rounded-4 bg-light-soft border border-light h-100 shadow-sm-hover transition-all">
                            <h5 class="fw-900 text-erp-deep mb-4 d-flex align-items-center gap-3">
                                <span class="bg-primary text-white p-2 rounded-3 d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                                    <i class="bi bi-person-lines-fill"></i>
                                </span>
                                Personal Credentials
                            </h5>
                            <div class="vstack gap-4">
                                <div class="profile-info-item">
                                    <label class="small text-muted text-uppercase fw-bold tracking-wider mb-1 d-block">Official Full Name</label>
                                    <div class="fw-800 text-erp-deep fs-5 d-flex align-items-center gap-2">
                                        {{ $user->name }}
                                        <i class="bi bi-patch-check-fill text-primary small"></i>
                                    </div>
                                </div>
                                <div class="profile-info-item">
                                    <label class="small text-muted text-uppercase fw-bold tracking-wider mb-1 d-block">Operational Email</label>
                                    <div class="fw-800 text-erp-deep fs-5 text-lowercase">{{ $user->email }}</div>
                                </div>
                                <div class="profile-info-item">
                                    <label class="small text-muted text-uppercase fw-bold tracking-wider mb-1 d-block">Contact Frequency</label>
                                    <div class="fw-800 text-erp-deep fs-5">{{ $user->phone_number ?: 'Restricted / Not Provided' }}</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Right Column: Professional Details --}}
                    <div class="col-lg-6">
                        <div class="p-4 rounded-4 bg-light-soft border border-light h-100 shadow-sm-hover transition-all">
                            <h5 class="fw-900 text-erp-deep mb-4 d-flex align-items-center gap-3">
                                <span class="bg-success text-white p-2 rounded-3 d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                                    <i class="bi bi-building-gear"></i>
                                </span>
                                Organization Anchoring
                            </h5>
                            <div class="vstack gap-4">
                                <div class="profile-info-item">
                                    <label class="small text-muted text-uppercase fw-bold tracking-wider mb-1 d-block">Assigned Department</label>
                                    <div class="fw-800 text-erp-deep fs-5 text-uppercase tracking-wide">{{ $user->department ?: 'General Operations' }}</div>
                                </div>
                                <div class="profile-info-item">
                                    <label class="small text-muted text-uppercase fw-bold tracking-wider mb-1 d-block">Functional Role</label>
                                    <div class="fw-800 text-erp-deep fs-5 text-uppercase tracking-wide">{{ $user->position ?: 'Authorized System User' }}</div>
                                </div>
                                <div class="profile-info-item">
                                    <label class="small text-muted text-uppercase fw-bold tracking-wider mb-1 d-block">Operational Status</label>
                                    <div class="d-flex align-items-center gap-3">
                                        @php
                                            $statusColor = match($user->status) {
                                                'Active' => 'success',
                                                'Inactive' => 'warning',
                                                'Suspended' => 'danger',
                                                default => 'primary'
                                            };
                                        @endphp
                                        <div class="d-flex align-items-center gap-2 bg-{{ $statusColor }}-soft px-3 py-1 rounded-pill">
                                            <span class="bg-{{ $statusColor }} p-1 rounded-circle shadow-sm pulse-{{ $statusColor }}"></span>
                                            <span class="fw-800 text-{{ $statusColor }} text-uppercase small tracking-widest">{{ $user->status ?? 'Active' }}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Full Width: Bio Section --}}
                    <div class="col-12">
                        <div class="p-4 rounded-4 bg-light-soft border border-light shadow-sm-hover transition-all">
                            <h5 class="fw-900 text-erp-deep mb-3 d-flex align-items-center gap-3">
                                <span class="bg-erp-deep text-white p-2 rounded-3 d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                                    <i class="bi bi-quote"></i>
                                </span>
                                Professional Biography & Notes
                            </h5>
                            <div class="text-muted fs-5 ps-2" style="line-height: 1.8; border-left: 3px solid #e2e8f0;">
                                {{ $user->bio ?: 'The administrative biography for this identity has not been established. This section serves as a professional record of qualifications and system notes.' }}
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Unified Action Footer --}}
                <div class="mt-5 pt-5 border-top border-light d-flex justify-content-between align-items-center">
                    <div class="text-muted small fw-bold text-uppercase tracking-widest">
                        <i class="bi bi-clock-history me-2"></i>Last Identity Update: {{ $user->updated_at ? $user->updated_at->format('M d, Y') : 'Original Record' }}
                    </div>
                    <a href="{{ route($user->getProfileRouteName('edit')) }}" 
                       class="btn btn-erp-deep rounded-pill px-5 py-3 fw-900 shadow-xl border-0 transform-hover">
                        <i class="bi bi-pencil-square me-3 fs-5"></i>MODIFY IDENTITY CREDENTIALS
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .bg-light-soft { background-color: #f8fafc; }
    .bg-primary-soft { background-color: rgba(59, 130, 246, 0.1); }
    .bg-success-soft { background-color: rgba(16, 185, 129, 0.1); }
    .bg-warning-soft { background-color: rgba(245, 158, 11, 0.1); }
    .bg-danger-soft { background-color: rgba(239, 68, 68, 0.1); }
    
    .shadow-sm-hover:hover {
        shadow: 0 10px 30px rgba(0,0,0,0.05);
        transform: translateY(-5px);
    }
    .transition-all { transition: all 0.3s ease; }
    
    .pulse-success {
        animation: pulse-success 2s infinite;
    }
    @keyframes pulse-success {
        0% { box-shadow: 0 0 0 0 rgba(16, 185, 129, 0.4); }
        70% { box-shadow: 0 0 0 10px rgba(16, 185, 129, 0); }
        100% { box-shadow: 0 0 0 0 rgba(16, 185, 129, 0); }
    }

    .transform-hover:hover {
        transform: translateY(-3px) scale(1.02);
    }
    
    .image-premium-zoom:hover {
        transform: scale(1.1);
    }

    .z-index-1 { z-index: 1; }
</style>
@endsection
