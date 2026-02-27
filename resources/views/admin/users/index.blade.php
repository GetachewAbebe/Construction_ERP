@extends('layouts.app')
@section('title', 'System Identities Control')

@section('content')
<div class="page-header-premium">
    <div class="row align-items-center">
        <div class="col">
            <h1 class="display-6">User Management</h1>
            <p>Manage system users, access credentials, and identity permissions.</p>
        </div>
        <div class="col-auto">
            <a href="{{ route('admin.users.create') }}" class="btn btn-erp-deep rounded-pill px-4 shadow-sm border-0">
                <i class="bi bi-person-plus-fill me-2"></i>Add New User
            </a>
        </div>
    </div>
</div>

{{-- Identity Search & Triage --}}
<div class="erp-card mb-4 stagger-entrance">
    <form action="{{ route('admin.users.index') }}" method="GET" class="row g-3 align-items-center">
        <div class="col-lg-8">
            <div class="input-group bg-light rounded-pill overflow-hidden px-3 border-0">
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

<div class="table-responsive">
    <table class="table-premium">
        <thead>
            <tr>
                <th class="ps-4">Users</th>
                <th>Email Address</th>
                <th>System Role</th>
                <th>Account Status</th>
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
                            <div class="bg-erp-deep text-white rounded-circle d-flex align-items-center justify-content-center fw-800 overflow-hidden" 
                                 style="width: 45px; height: 45px; background: linear-gradient(135deg, #064e3b 0%, #059669 100%); border: 2px solid #fff; box-shadow: 0 4px 10px rgba(0,0,0,0.1); flex-shrink: 0;">
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
                            <div class="d-flex justify-content-end gap-2">
                                <a href="{{ route('admin.users.show', $user) }}" 
                                   class="btn btn-sm btn-white rounded-pill px-3 py-2 fw-bold" 
                                   title="View Profile">
                                    Profile
                                </a>
                                
                                @if($user->id !== auth()->id())
                                    <form action="{{ route('admin.users.destroy', $user) }}" 
                                          method="POST" class="d-inline" id="delete-form-{{ $user->id }}">
                                        @csrf
                                        @method('DELETE')
                                        <button type="button" class="btn btn-sm btn-outline-danger rounded-pill px-3 py-2 fw-bold" 
                                                onclick="premiumConfirm('Delete User', 'Are you sure you want to permanently delete this user account? This will revoke all system access immediately.', 'delete-form-{{ $user->id }}', '{{ $user->name }}', '{{ optional($user->employee)->profile_picture_url }}')">
                                            Delete
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="text-center py-5">
                            <div class="text-muted">No users found matching your search.</div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($users->hasPages())
        <div class="mt-4">
            {{ $users->links() }}
        </div>
    @endif
</div>
@endsection
