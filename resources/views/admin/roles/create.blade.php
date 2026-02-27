@extends('layouts.app')
@section('title', 'Create New Role')

@section('content')
<div class="row align-items-center mb-4 stagger-entrance">
    <div class="col">
        <h1 class="h3 mb-1 fw-800 text-erp-deep">Create New Role</h1>
        <p class="text-muted mb-0">Create a new user role and assign permissions.</p>
    </div>
    <div class="col-auto">
        <a href="{{ route('admin.roles.index') }}" class="btn btn-white rounded-pill px-4 shadow-sm border-0">
            <i class="bi bi-arrow-left me-2"></i>Back to Roles
        </a>
    </div>
</div>

<div class="stagger-entrance">
    <div class="card border-0 bg-white shadow-sm">
        <div class="card-body p-4 p-md-5">
                <form action="{{ route('admin.roles.store') }}" method="POST">
                    @csrf
                    
                    <div class="mb-5">
                        <h5 class="fw-800 text-erp-deep mb-4 d-flex align-items-center gap-2">
                            <i class="bi bi-shield-fill-check text-primary"></i>
                            Role Details
                        </h5>
                        
                        <div class="row g-4">
                            <div class="col-md-6">
                                <label class="form-label small fw-800 text-muted text-uppercase">Role Name</label>
                                <input type="text" name="name" 
                                       class="form-control border-0 bg-light-soft rounded-4 py-3 px-4 shadow-sm @error('name') is-invalid @enderror" 
                                       value="{{ old('name') }}" 
                                       placeholder="e.g., ProjectManager"
                                       required>
                                <small class="text-muted fw-bold mt-2 d-block">Unique role name (e.g. ProjectManager).</small>
                                @error('name') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label small fw-800 text-muted text-uppercase">Display Name (Optional)</label>
                                <input type="text" name="display_name" 
                                       class="form-control border-0 bg-light-soft rounded-4 py-3 px-4 shadow-sm @error('display_name') is-invalid @enderror" 
                                       value="{{ old('display_name') }}" 
                                       placeholder="e.g., Project Manager">
                                <small class="text-muted fw-bold mt-2 d-block">Human-readable name.</small>
                                @error('display_name') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                            </div>

                            <div class="col-12">
                                <label class="form-label small fw-800 text-muted text-uppercase">Description</label>
                                <textarea name="description" rows="3" 
                                          class="form-control border-0 bg-light-soft rounded-4 py-3 px-4 shadow-sm @error('description') is-invalid @enderror" 
                                          placeholder="Description of the role...">{{ old('description') }}</textarea>
                                @error('description') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                            </div>
                        </div>
                    </div>

                    <div class="mb-5">
                        <h5 class="fw-800 text-erp-deep mb-4 d-flex align-items-center gap-2">
                            <i class="bi bi-key-fill text-success"></i>
                            Permissions
                        </h5>
                        
                        @if($permissions->count() > 0)
                            @foreach($permissions as $module => $modulePermissions)
                                <div class="mb-4 p-4 bg-light-soft rounded-4">
                                    <div class="form-check mb-3">
                                        <input class="form-check-input" type="checkbox" id="select_all_{{ $module }}" 
                                               onchange="toggleModulePermissions('{{ $module }}', this.checked)">
                                        <label class="form-check-label fw-800 text-erp-deep text-uppercase small" for="select_all_{{ $module }}">
                                            <i class="bi bi-folder-fill me-2"></i>{{ ucfirst($module) }} Module
                                        </label>
                                    </div>
                                    
                                    <div class="row g-3 ms-4">
                                        @foreach($modulePermissions as $permission)
                                            <div class="col-md-6">
                                                <div class="form-check">
                                                    <input class="form-check-input module-{{ $module }}" type="checkbox" 
                                                           name="permissions[]" value="{{ $permission->id }}" 
                                                           id="perm_{{ $permission->id }}"
                                                           @checked(in_array($permission->id, old('permissions', [])))>
                                                    <label class="form-check-label fw-600 text-dark small" for="perm_{{ $permission->id }}">
                                                        {{ $permission->name }}
                                                    </label>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endforeach
                        @else
                            <div class="alert alert-info border-0 rounded-4">
                                <i class="bi bi-info-circle-fill me-2"></i>
                                No permissions found.
                            </div>
                        @endif
                    </div>

                    <div class="d-flex gap-3 justify-content-end pt-4 border-top border-light">
                        <a href="{{ route('admin.roles.index') }}" class="btn btn-white rounded-pill px-4 py-3 fw-700 shadow-sm border-0">
                            <i class="bi bi-x-circle me-2"></i>Cancel
                        </a>
                        <button type="submit" class="btn btn-erp-deep rounded-pill px-5 py-3 fw-800 shadow-lg border-0">
                            <i class="bi bi-check2-circle me-2"></i>Create Role
                        </button>
                    </div>
                </form>
            </div>
    </div>
</div>

<script>
function toggleModulePermissions(module, checked) {
    document.querySelectorAll('.module-' + module).forEach(checkbox => {
        checkbox.checked = checked;
    });
}
</script>
@endsection
