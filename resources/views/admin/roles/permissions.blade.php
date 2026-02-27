@extends('layouts.app')
@section('title', 'Permission Registry')

@section('content')
<div class="row align-items-center mb-4 stagger-entrance">
    <div class="col">
        <h1 class="h3 mb-1 fw-800 text-erp-deep">Permission Registry</h1>
        <p class="text-muted mb-0">Manage granular access privileges available for role assignment across the system.</p>
    </div>
    <div class="col-auto d-flex gap-2">
        <a href="{{ route('admin.roles.index') }}" class="btn btn-white rounded-pill px-4 shadow-sm border-0">
            <i class="bi bi-arrow-left me-2"></i>Back to Roles
        </a>
        <button type="button" class="btn btn-erp-deep rounded-pill px-4 shadow-sm border-0" data-bs-toggle="modal" data-bs-target="#createPermissionModal">
            <i class="bi bi-plus-circle me-2"></i>Register Permission
        </button>
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
    @forelse($permissions as $module => $modulePermissions)
        <div class="col-lg-6 stagger-entrance">
            <div class="card hardened-glass border-0 overflow-hidden shadow-sm h-100">
                <div class="card-header bg-light-soft border-0 p-4">
                    <h5 class="fw-800 text-erp-deep mb-0 d-flex align-items-center gap-2">
                        <i class="bi bi-folder-fill text-primary"></i>
                        {{ ucfirst($module) }} Module
                    </h5>
                    <p class="text-muted small mb-0 mt-1 fw-600">{{ $modulePermissions->count() }} {{ Str::plural('permission', $modulePermissions->count()) }} registered</p>
                </div>
                <div class="card-body p-4">
                    <div class="list-group list-group-flush">
                        @foreach($modulePermissions as $permission)
                            <div class="list-group-item bg-transparent border-0 px-0 py-2 d-flex align-items-center justify-content-between">
                                <div class="flex-grow-1">
                                    <div class="fw-700 text-dark small">{{ $permission->name }}</div>
                                    @if($permission->description)
                                        <div class="text-muted x-small fw-600 mt-1">{{ $permission->description }}</div>
                                    @endif
                                </div>
                                <form action="{{ route('admin.roles.permissions.destroy', $permission) }}" method="POST" 
                                      onsubmit="return confirm('Remove this permission from the registry?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-white rounded-pill px-3 shadow-sm border-0 text-danger">
                                        <i class="bi bi-trash3-fill"></i>
                                    </button>
                                </form>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    @empty
        <div class="col-12 stagger-entrance">
            <div class="card hardened-glass border-0 overflow-hidden shadow-sm">
                <div class="card-body text-center py-5">
                    <i class="bi bi-key-fill fs-1 text-muted opacity-25"></i>
                    <div class="text-muted italic mt-3">No permissions have been registered in the system.</div>
                    <button type="button" class="btn btn-erp-deep rounded-pill px-4 mt-3" data-bs-toggle="modal" data-bs-target="#createPermissionModal">
                        <i class="bi bi-plus-circle me-2"></i>Register First Permission
                    </button>
                </div>
            </div>
        </div>
    @endforelse
</div>

<!-- Create Permission Modal -->
<div class="modal fade" id="createPermissionModal" tabindex="-1" aria-labelledby="createPermissionModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content hardened-glass border-0 shadow-lg">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title fw-800 text-erp-deep" id="createPermissionModalLabel">Register New Permission</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('admin.roles.permissions.store') }}" method="POST">
                @csrf
                <div class="modal-body p-4">
                    <div class="mb-4">
                        <label class="form-label small fw-800 text-muted text-uppercase">Permission Name</label>
                        <input type="text" name="name" 
                               class="form-control border-0 bg-light-soft rounded-4 py-3 px-4 shadow-sm @error('name') is-invalid @enderror" 
                               placeholder="e.g., user.create, project.delete"
                               required>
                        <small class="text-muted fw-bold mt-2 d-block">Use dot notation: module.action (e.g., user.create, report.view)</small>
                        @error('name') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                    </div>

                    <div class="mb-4">
                        <label class="form-label small fw-800 text-muted text-uppercase">Display Name (Optional)</label>
                        <input type="text" name="display_name" 
                               class="form-control border-0 bg-light-soft rounded-4 py-3 px-4 shadow-sm @error('display_name') is-invalid @enderror" 
                               placeholder="e.g., Create Users">
                        <small class="text-muted fw-bold mt-2 d-block">Human-readable name for UI display.</small>
                        @error('display_name') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-800 text-muted text-uppercase">Description</label>
                        <textarea name="description" rows="2" 
                                  class="form-control border-0 bg-light-soft rounded-4 py-3 px-4 shadow-sm @error('description') is-invalid @enderror" 
                                  placeholder="Describe what this permission grants access to..."></textarea>
                        @error('description') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                    </div>
                </div>
                <div class="modal-footer border-0 pt-0">
                    <button type="button" class="btn btn-white rounded-pill px-4 shadow-sm border-0" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-erp-deep rounded-pill px-4 shadow-sm border-0">
                        <i class="bi bi-check2-circle me-2"></i>Register
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
