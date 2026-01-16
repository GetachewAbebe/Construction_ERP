@extends('layouts.app')
@section('title', 'Authorization Matrix Control')

@section('content')
<div class="row align-items-center mb-4 stagger-entrance">
    <div class="col">
        <h1 class="h3 mb-1 fw-800 text-erp-deep">Authorization Matrix Control</h1>
        <p class="text-muted mb-0">Define and manage role-based access control tiers and granular permission assignments.</p>
    </div>
    <div class="col-auto">
        <a href="{{ route('admin.roles.create') }}" class="btn btn-erp-deep rounded-pill px-4 shadow-sm border-0">
            <i class="bi bi-plus-circle me-2"></i>Provision New Role
        </a>
    </div>
</div>

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show hardened-glass border-0 shadow-sm stagger-entrance" role="alert">
        <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

@if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show hardened-glass border-0 shadow-sm stagger-entrance" role="alert">
        <i class="bi bi-exclamation-triangle-fill me-2"></i>{{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

<div class="row g-4">
    @forelse($roles as $role)
        <div class="col-lg-6 stagger-entrance">
            <div class="card hardened-glass border-0 overflow-hidden shadow-sm h-100">
                <div class="card-body p-4">
                    <div class="d-flex align-items-start justify-content-between mb-3">
                        <div class="flex-grow-1">
                            <h5 class="fw-800 text-erp-deep mb-1">{{ $role->name }}</h5>
                            <p class="text-muted small mb-0 fw-600">
                                @php
                                    $userCount = \App\Models\User::role($role->name)->count();
                                @endphp
                                <i class="bi bi-people-fill me-1"></i>{{ $userCount }} {{ Str::plural('user', $userCount) }} assigned
                            </p>
                        </div>
                        <div class="dropdown">
                            <button class="btn btn-white btn-sm rounded-pill px-3 shadow-sm border-0" type="button" data-bs-toggle="dropdown">
                                <i class="bi bi-three-dots-vertical"></i>
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end shadow-lg border-0">
                                <li>
                                    <a class="dropdown-item" href="{{ route('admin.roles.edit', $role) }}">
                                        <i class="bi bi-pencil-square me-2"></i>Modify Role
                                    </a>
                                </li>
                                @if(!in_array($role->name, ['Administrator', 'HumanResourceManager', 'InventoryManager', 'FinancialManager']))
                                    <li><hr class="dropdown-divider"></li>
                                    <li>
                                        <form action="{{ route('admin.roles.destroy', $role) }}" method="POST" 
                                              onsubmit="return confirm('Authorize deletion of this role tier?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="dropdown-item text-danger">
                                                <i class="bi bi-trash3-fill me-2"></i>Expunge Role
                                            </button>
                                        </form>
                                    </li>
                                @else
                                    <li><hr class="dropdown-divider"></li>
                                    <li>
                                        <span class="dropdown-item text-muted disabled">
                                            <i class="bi bi-lock-fill me-2"></i>Core Role (Protected)
                                        </span>
                                    </li>
                                @endif
                            </ul>
                        </div>
                    </div>

                    <div class="mb-3">
                        <h6 class="small fw-800 text-muted text-uppercase mb-2">Assigned Privileges</h6>
                        @if($role->permissions->count() > 0)
                            <div class="d-flex flex-wrap gap-2">
                                @foreach($role->permissions->take(8) as $permission)
                                    <span class="badge bg-primary-soft text-primary rounded-pill px-3 py-2 border-0 fw-700 small">
                                        {{ $permission->name }}
                                    </span>
                                @endforeach
                                @if($role->permissions->count() > 8)
                                    <span class="badge bg-secondary-soft text-secondary rounded-pill px-3 py-2 border-0 fw-700 small">
                                        +{{ $role->permissions->count() - 8 }} more
                                    </span>
                                @endif
                            </div>
                        @else
                            <p class="text-muted italic small mb-0">No specific permissions assigned. Role has default access only.</p>
                        @endif
                    </div>

                    <div class="d-flex gap-2 pt-3 border-top border-light">
                        <a href="{{ route('admin.roles.edit', $role) }}" class="btn btn-white btn-sm rounded-pill px-3 flex-grow-1 shadow-sm border-0 fw-700">
                            <i class="bi bi-gear-fill me-1"></i>Configure
                        </a>
                        <span class="badge bg-light-soft text-dark rounded-pill px-3 py-2 d-flex align-items-center fw-700">
                            <i class="bi bi-shield-lock-fill me-2"></i>{{ $role->permissions->count() }} privileges
                        </span>
                    </div>
                </div>
            </div>
        </div>
    @empty
        <div class="col-12 stagger-entrance">
            <div class="card hardened-glass border-0 overflow-hidden shadow-sm">
                <div class="card-body text-center py-5">
                    <i class="bi bi-shield-x fs-1 text-muted opacity-25"></i>
                    <div class="text-muted italic mt-3">No authorization roles have been configured in the system.</div>
                    <a href="{{ route('admin.roles.create') }}" class="btn btn-erp-deep rounded-pill px-4 mt-3">
                        <i class="bi bi-plus-circle me-2"></i>Create First Role
                    </a>
                </div>
            </div>
        </div>
    @endforelse
</div>

<div class="row mt-4">
    <div class="col-12 stagger-entrance">
        <div class="card hardened-glass border-0 overflow-hidden shadow-sm">
            <div class="card-body p-4">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <h6 class="fw-800 text-erp-deep mb-1">Permission Registry Management</h6>
                        <p class="text-muted small mb-0 fw-600">View and manage granular access privileges available for role assignment.</p>
                    </div>
                    <a href="{{ route('admin.roles.permissions') }}" class="btn btn-white rounded-pill px-4 shadow-sm border-0 fw-700">
                        <i class="bi bi-key-fill me-2"></i>Manage Permissions
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
