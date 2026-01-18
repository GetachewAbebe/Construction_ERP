@extends('layouts.app')
@section('title', 'System Identities Control')

@section('content')
@push('head')
<style>
    .btn-action-group {
        display: inline-flex;
        border-radius: 12px;
        overflow: hidden;
        box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
    }

    .btn-action-group:hover {
        transform: translateY(-3px) scale(1.02);
        box-shadow: 0 12px 25px rgba(0,0,0,0.15);
    }

    .btn-action {
        width: 110px; /* Exact uniform sizing */
        padding: 11px 0;
        font-size: 0.75rem;
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: 0.08em;
        text-decoration: none;
        color: white !important;
        border: none;
        display: flex;
        align-items: center;
        justify-content: center; /* Center text perfectly */
        gap: 8px;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }

    .btn-action-edit {
        background: linear-gradient(135deg, #6366f1 0%, #4338ca 100%); /* Premium Indigo Gradient */
        border-right: 1px solid rgba(255,255,255,0.2);
    }

    .btn-action-delete {
        background: linear-gradient(135deg, #f43f5e 0%, #e11d48 100%); /* Premium Rose Gradient */
    }

    .btn-action:hover {
        filter: saturate(1.2) brightness(1.1);
    }

    .btn-action:active {
        transform: scale(0.95);
    }
</style>
@endpush
<div class="row align-items-center mb-4 stagger-entrance">
    <div class="col">
        <h1 class="h3 mb-1 fw-800 text-erp-deep">User Management</h1>
        <p class="text-muted mb-0">Manage system users and access credentials.</p>
    </div>
    <div class="col-auto">
        <a href="{{ route('admin.users.create') }}" class="btn btn-erp-deep rounded-pill px-4 shadow-sm border-0">
            <i class="bi bi-person-plus-fill me-2"></i>Add New User
        </a>
    </div>
</div>

{{-- Identity Search & Triage --}}
<div class="card hardened-glass border-0 mb-4 stagger-entrance shadow-sm">
    <div class="card-body p-4">
        <form action="{{ route('admin.users.index') }}" method="GET" class="row g-3 align-items-center">
            <div class="col-lg-8">
                <div class="input-group bg-light-soft rounded-pill overflow-hidden shadow-sm px-3 border-0">
                    <span class="input-group-text bg-transparent border-0 text-muted">
                        <i class="bi bi-search"></i>
                    </span>
                    <input type="text" name="q" value="{{ $q ?? '' }}" 
                           class="form-control border-0 bg-transparent py-3" 
                           placeholder="Search by name or email...">
                </div>
            </div>
            <div class="col-lg-4 text-lg-end">
                @if($q)
                    <a href="{{ route('admin.users.index') }}" class="btn btn-white rounded-pill px-4 me-2 border-0 shadow-sm">Reset</a>
                @endif
                <button type="submit" class="btn btn-erp-deep rounded-pill px-5 border-0 shadow-sm">Search</button>
            </div>
        </form>
    </div>
</div>

{{-- Identity Grid --}}
<div class="stagger-entrance">
    <div class="card border-0 bg-white shadow-sm overflow-hidden">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light-soft text-erp-deep">
                    <tr>
                        <th class="ps-4">User / Employee</th>
                        <th>Email</th>
                        <th>Role</th>
                        <th>Status</th>
                        <th class="text-end pe-4">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($users as $user)
                        <tr>
                            <td class="ps-4">
                                <div class="d-flex align-items-center gap-3">
                                    @php 
                                        $avatarUrl = optional($user->employee)->profile_picture_url;
                                        $initials = strtoupper(substr($user->first_name, 0, 1) . substr($user->last_name, 0, 1));
                                    @endphp
                                    <div class="avatar-sm bg-erp-deep text-white rounded-pill d-flex align-items-center justify-content-center fw-800 overflow-hidden" 
                                         style="width: 45px; height: 45px; background: linear-gradient(135deg, #064e3b 0%, #059669 100%); border: 2px solid #fff; shadow: 0 4px 10px rgba(0,0,0,0.1);">
                                         @if($avatarUrl)
                                             <img src="{{ $avatarUrl }}" class="w-100 h-100 object-fit-cover" 
                                                  onerror="this.style.display='none'; this.parentNode.querySelector('.avatar-initial').style.display='flex';">
                                         @endif
                                         <span class="avatar-initial" style="display: {{ $avatarUrl ? 'none' : 'flex' }};">{{ $initials }}</span>
                                     </div>
                                    <div>
                                        <div class="fw-800 text-erp-deep">{{ $user->name }}</div>
                                        <small class="text-muted fw-bold">{{ $user->position ?? 'Unassigned' }}</small>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <span class="text-muted fw-600">{{ $user->email }}</span>
                            </td>
                            <td>
                                @php
                                    $roleClass = match($user->role) {
                                        'Administrator' => 'bg-danger-soft text-danger',
                                        'HumanResourceManager' => 'bg-primary-soft text-primary',
                                        'InventoryManager' => 'bg-success-soft text-success',
                                        'FinancialManager' => 'bg-warning-soft text-warning',
                                        default => 'bg-light text-muted'
                                    };
                                @endphp
                                <span class="badge {{ $roleClass }} rounded-pill px-3 py-2 border-0 fw-800">
                                    <i class="bi bi-shield-lock me-1"></i>{{ $user->role ?? 'Standard' }}
                                </span>
                            </td>
                            <td>
                                @php
                                    $statusClass = match($user->status) {
                                        'Active' => 'text-success',
                                        'Inactive' => 'text-muted',
                                        'Suspended' => 'text-danger',
                                        default => 'text-info'
                                    };
                                @endphp
                                <span class="d-flex align-items-center gap-2 fw-700 {{ $statusClass }}">
                                    <i class="bi bi-circle-fill" style="font-size: 8px;"></i>
                                    {{ $user->status ?? 'Active' }}
                                </span>
                            </td>
                            <td class="text-end pe-4">
                                <div class="d-flex justify-content-end">
                                    <div class="btn-action-group">
                                        <a href="{{ route('admin.users.show', $user) }}" 
                                           class="btn-action btn-action-edit" 
                                           title="View Profile">
                                            Profile
                                        </a>
                                        
                                        @if($user->id !== auth()->id())
                                            <form action="{{ route('admin.users.destroy', $user) }}" 
                                                  method="POST" class="d-inline" id="delete-form-{{ $user->id }}">
                                                @csrf
                                                @method('DELETE')
                                                <button type="button" class="btn-action btn-action-delete" 
                                                        onclick="premiumConfirm('Delete User', 'Are you sure you want to permanently delete this user account? This will revoke all system access immediately.', 'delete-form-{{ $user->id }}', '{{ $user->name }}', '{{ optional($user->employee)->profile_picture_url }}')">
                                                    Delete
                                                </button>
                                            </form>
                                        @else
                                            <button type="button" class="btn-action btn-action-delete" 
                                                    onclick="showToast('As a security integrity measure, active Administrator identities cannot be deleted via the console.', 'danger', 'System Protection')">
                                                Delete
                                            </button>
                                        @endif
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center py-5">
                                <div class="text-muted italic">No users found matching your search.</div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    @if($users->hasPages())
        <div class="card-footer border-0 p-4">
            {{ $users->links() }}
        </div>
    @endif
</div>
</div>
@endsection

