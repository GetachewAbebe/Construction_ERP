@extends('layouts.app')
@section('title', 'Corporate Directory')

@section('content')
<div class="row align-items-center mb-4 stagger-entrance">
    <div class="col">
        <h1 class="h3 mb-1 fw-800 text-erp-deep">Human Capital Identity</h1>
        <p class="text-muted mb-0">Strategic workforce overview and organizational directory.</p>
    </div>
    <div class="col-auto">
        @if(Auth::user()->hasAnyRole(['Administrator', 'Admin', 'Human Resource Manager', 'HumanResourceManager']))
        <a href="{{ route('hr.employees.create') }}" class="btn btn-erp-deep rounded-pill px-4 py-2 shadow-lg border-0 fw-700">
            <i class="bi bi-person-plus-fill me-2"></i>Onboard Professional
        </a>
        @endif
    </div>
</div>

{{-- Flash Messages --}}
@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show hardened-glass border-0 shadow-sm stagger-entrance mb-4" role="alert">
        <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif
@if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show hardened-glass border-0 shadow-sm stagger-entrance mb-4" role="alert">
        <i class="bi bi-exclamation-triangle-fill me-2"></i>{{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

{{-- Search & Filter --}}
<form action="{{ route('hr.employees.index') }}" method="GET" class="mb-4 stagger-entrance" style="animation-delay: 0.1s;">
    <div class="row g-2">
        <div class="col-md-9">
            <div class="position-relative">
                <i class="bi bi-search position-absolute top-50 start-0 translate-middle-y ms-3 text-muted"></i>
                <input type="text" name="q" class="form-control border-0 bg-white py-3 ps-5 rounded-pill shadow-sm" 
                       placeholder="Search by name, email, department, or position title..." 
                       value="{{ request('q') }}">
            </div>
        </div>
        <div class="col-md-3">
            <select name="status" class="form-select border-0 bg-white py-3 rounded-pill shadow-sm" onchange="this.form.submit()">
                <option value="">All Statuses</option>
                <option value="Active" {{ request('status') === 'Active' ? 'selected' : '' }}>Active Duty</option>
                <option value="On Leave" {{ request('status') === 'On Leave' ? 'selected' : '' }}>On Leave</option>
                <option value="Terminated" {{ request('status') === 'Terminated' ? 'selected' : '' }}>Terminated</option>
                <option value="Resigned" {{ request('status') === 'Resigned' ? 'selected' : '' }}>Resigned</option>
            </select>
        </div>
    </div>
</form>

{{-- Workforce Stats --}}
<div class="row g-4 mb-4 stagger-entrance" style="animation-delay: 0.2s;">
    <div class="col-md-3">
        <div class="card hardened-glass border-0 p-3">
            <div class="d-flex align-items-center gap-3">
                <div class="avatar-sm bg-primary-soft text-primary rounded-circle d-flex align-items-center justify-content-center">
                    <i class="bi bi-people-fill fs-5"></i>
                </div>
                <div>
                    <div class="small text-muted">Total Workforce</div>
                    <div class="fw-800 fs-5 text-erp-deep">{{ $employees->total() }}</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card hardened-glass border-0 p-3">
            <div class="d-flex align-items-center gap-3">
                <div class="avatar-sm bg-success-soft text-success rounded-circle d-flex align-items-center justify-content-center">
                    <i class="bi bi-patch-check-fill fs-5"></i>
                </div>
                <div>
                    <div class="small text-muted">Active Duty</div>
                    <div class="fw-800 fs-5 text-erp-deep">{{ \App\Models\Employee::where('status', 'Active')->count() }}</div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="card hardened-glass border-0 overflow-hidden stagger-entrance" style="animation-delay: 0.3s;">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="bg-light-soft text-erp-deep">
                <tr>
                    <th class="ps-4">Photo</th>
                    <th>Employee Name</th>
                    <th>Position & Department</th>
                    <th>Status</th>
                    <th>Contact</th>
                    <th class="text-end pe-4">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($employees as $e)
                    <tr>
                        <td class="ps-4">
                            @if($e->profile_picture_url)
                                <div class="avatar-md hardened-glass p-1 rounded-circle shadow-sm" style="width: 45px; height: 45px;">
                                    <img src="{{ $e->profile_picture_url }}" 
                                         class="rounded-circle w-100 h-100" 
                                         style="object-fit: cover; text-indent: -9999px;" 
                                         alt="{{ $e->first_name }}"
                                         onerror="this.onerror=null; this.src='data:image/svg+xml;base64,PHN2ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHZpZXdCb3g9IjAgMCAyNCAyNCI+PHBhdGggZmlsbD0iI2NjYyIgZD0iTTEyIDEyYzIuMjEgMCA0LTEuNzkgNC00cy0xLjc5LTQtNC00LTQgMS43OS00IDRzMS43OSA0IDQgNHptMCAyYy0yLjY3IDAtOCAxLjM0LTggNHYyaDE2di0yYzAtMi42Ni01LjMzLTQtOC00eiIvPjwvc3ZnPg==';">
                                </div>
                            @else
                                <div class="avatar-md bg-erp-deep text-white rounded-circle d-flex align-items-center justify-content-center fw-800 shadow-sm" 
                                     style="width: 45px; height: 45px;">
                                    {{ substr($e->first_name, 0, 1) }}
                                </div>
                            @endif
                        </td>
                        <td>
                            <div class="fw-800 text-erp-deep">{{ $e->first_name }} {{ $e->last_name }}</div>
                            <small class="text-muted fw-600">ID: #EMP-{{ str_pad($e->id, 4, '0', STR_PAD_LEFT) }}</small>
                        </td>
                        <td>
                            <div class="d-flex flex-column">
                                <span class="fw-700 text-dark">{{ $e->position ?? 'Unassigned Role' }}</span>
                                <small class="text-muted"><i class="bi bi-diagram-2 me-1"></i>{{ $e->department ?? 'General' }}</small>
                            </div>
                        </td>
                        <td>
                            @php
                                $statusContext = match($e->status) {
                                    'Active' => ['class' => 'bg-success-soft text-success', 'icon' => 'bi-check-circle-fill'],
                                    'On Leave' => ['class' => 'bg-warning-soft text-warning', 'icon' => 'bi-hourglass-split'],
                                    'Terminated' => ['class' => 'bg-danger-soft text-danger', 'icon' => 'bi-x-circle-fill'],
                                    'Resigned' => ['class' => 'bg-secondary-soft text-secondary', 'icon' => 'bi-door-closed-fill'],
                                    default => ['class' => 'bg-light text-muted', 'icon' => 'bi-question-circle']
                                };
                            @endphp
                            <span class="badge {{ $statusContext['class'] }} rounded-pill px-3 py-2 border-0 fw-600">
                                <i class="bi {{ $statusContext['icon'] }} me-1"></i>{{ $e->status ?? 'Unknown' }}
                            </span>
                        </td>
                        <td>
                            <div class="small text-dark fw-bold">{{ $e->email }}</div>
                            <div class="x-small text-muted">{{ $e->phone ?? 'No Direct Line' }}</div>
                        </td>
                        <td class="text-end pe-4">
                            @if(Auth::user()->hasAnyRole(['Administrator', 'Admin', 'Human Resource Manager', 'HumanResourceManager']))
                            <div class="btn-group shadow-sm" role="group">
                                <a href="{{ route('hr.employees.edit', $e) }}" 
                                   class="btn btn-sm btn-primary px-3 py-2 fw-600" 
                                   title="Edit Profile">
                                    <i class="bi bi-pencil-square me-1"></i>Edit
                                </a>
                                <button type="button" 
                                        class="btn btn-sm btn-danger px-3 py-2 fw-600" 
                                        onclick="if(confirm('Permanently remove {{ $e->first_name }} {{ $e->last_name }} from records?')) document.getElementById('del-emp-{{ $e->id }}').submit()">
                                    <i class="bi bi-trash3-fill me-1"></i>Delete
                                    <form id="del-emp-{{ $e->id }}" action="{{ route('hr.employees.destroy', $e) }}" method="POST" class="d-none">@csrf @method('DELETE')</form>
                                </button>
                            </div>
                            @else
                            <span class="badge bg-secondary text-white fw-normal rounded-pill px-3 py-2">View Only</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center py-5">
                            <i class="bi bi-person-video2 fs-1 text-muted opacity-25"></i>
                            <div class="text-muted italic mt-3">No professional records match your search criteria.</div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    
    @if($employees->hasPages())
        <div class="card-footer border-0 bg-white p-4">
            {{ $employees->links() }}
        </div>
    @endif
</div>
@endsection

