@extends('layouts.app')
@section('title', 'Corporate Directory')

@section('content')
<div class="page-header-premium">
    <div class="row align-items-center">
        <div class="col">
            <h1>Employee Directory</h1>
            <p>Workforce overview and organizational directory.</p>
        </div>
        <div class="col-auto">
            @if(Auth::user()->hasAnyRole(['Administrator', 'Admin', 'Human Resource Manager', 'HumanResourceManager']))
            <a href="{{ route('hr.employees.create') }}" class="btn btn-erp-deep rounded-pill px-4 shadow-sm border-0">
                <i class="bi bi-person-plus-fill me-2"></i>Add Employee
            </a>
            @endif
        </div>
    </div>
</div>

{{-- Flash Messages --}}
@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show erp-card border-0 mb-4" role="alert">
        <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif
@if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show erp-card border-0 mb-4" role="alert">
        <i class="bi bi-exclamation-triangle-fill me-2"></i>{{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

{{-- Search & Filter --}}
<div class="erp-card mb-4">
    <form action="{{ route('hr.employees.index') }}" method="GET" class="row g-3 align-items-end">
        <div class="col-md-9">
            <label class="erp-label">Search Employees</label>
            <div class="input-group bg-light rounded-pill overflow-hidden px-3 border-0">
                <span class="input-group-text bg-transparent border-0 text-muted"><i class="bi bi-search"></i></span>
                <input type="text" name="q" class="form-control border-0 bg-transparent py-2" 
                       placeholder="Search by name, email, department, or position..." 
                       value="{{ request('q') }}">
            </div>
        </div>
        <div class="col-md-3">
            <label class="erp-label">Status Filter</label>
            <select name="status" class="erp-input" style="appearance: auto;" onchange="this.form.submit()">
                <option value="">All Statuses</option>
                <option value="Active" {{ request('status') === 'Active' ? 'selected' : '' }}>Active</option>
                <option value="On Leave" {{ request('status') === 'On Leave' ? 'selected' : '' }}>On Leave</option>
                <option value="Terminated" {{ request('status') === 'Terminated' ? 'selected' : '' }}>Terminated</option>
                <option value="Resigned" {{ request('status') === 'Resigned' ? 'selected' : '' }}>Resigned</option>
            </select>
        </div>
    </form>
</div>

{{-- Workforce Stats --}}
<div class="row g-4 mb-4">
    <div class="col-md-3">
        <div class="erp-card border-start border-4 border-primary">
            <div class="d-flex align-items-center gap-3">
                <div class="bg-primary-soft text-primary rounded-circle d-flex align-items-center justify-content-center" style="width: 48px; height: 48px;">
                    <i class="bi bi-people-fill fs-5"></i>
                </div>
                <div>
                    <div class="erp-label mb-1">Total Employees</div>
                    <div class="display-6 fw-900 text-erp-deep">{{ $employees->total() }}</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="erp-card border-start border-4 border-success">
            <div class="d-flex align-items-center gap-3">
                <div class="bg-success-soft text-success rounded-circle d-flex align-items-center justify-content-center" style="width: 48px; height: 48px;">
                    <i class="bi bi-patch-check-fill fs-5"></i>
                </div>
                <div>
                    <div class="erp-label mb-1">Active</div>
                    <div class="display-6 fw-900 text-erp-deep">{{ \App\Models\Employee::where('status', 'Active')->count() }}</div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="table-responsive">
    <table class="table-premium">
        <thead>
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
                        @php
                            $initialRef = strtoupper(substr($e->first_name, 0, 1));
                        @endphp
                        @if($e->profile_picture_url)
                            <div class="rounded-circle overflow-hidden shadow-sm border border-2 border-white position-relative" style="width: 45px; height: 45px;">
                                <img src="{{ $e->profile_picture_url }}" 
                                     class="w-100 h-100 object-fit-cover" 
                                     alt="{{ $e->first_name }}"
                                     onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                                <div class="w-100 h-100 d-flex align-items-center justify-content-center bg-erp-deep text-white fw-900" 
                                     style="display: none; font-size: 1.2rem;">
                                    {{ $initialRef }}
                                </div>
                            </div>
                        @else
                            <div class="bg-erp-deep text-white rounded-circle d-flex align-items-center justify-content-center fw-800 shadow-sm" 
                                 style="width: 45px; height: 45px; font-size: 1.2rem;">
                                {{ $initialRef }}
                            </div>
                        @endif
                    </td>
                    <td>
                        <div class="fw-800 text-erp-deep">{{ $e->first_name }} {{ $e->last_name }}</div>
                        <small class="text-muted fw-600">ID: #EMP-{{ str_pad($e->id, 4, '0', STR_PAD_LEFT) }}</small>
                    </td>
                    <td>
                        <div class="d-flex flex-column">
                            <span class="fw-700 text-dark">{{ $e->position ?? 'Unassigned' }}</span>
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
                        <div class="x-small text-muted">{{ $e->phone ?? 'No Phone' }}</div>
                    </td>
                    <td class="text-end pe-4">
                        <div class="d-flex justify-content-end gap-2">
                            @if(Auth::user()->hasAnyRole(['Administrator', 'Admin']))
                                @if(!$e->user_id)
                                    <a href="{{ route('admin.users.create', ['employee_id' => $e->id]) }}" 
                                       class="btn btn-sm btn-outline-primary rounded-pill px-3 py-2 fw-bold" 
                                       title="Grant Login Access">
                                        <i class="bi bi-shield-plus me-1"></i>Grant Access
                                    </a>
                                @else
                                    <span class="badge bg-primary-soft text-primary rounded-pill px-3 py-2 fw-800">
                                        <i class="bi bi-person-check-fill me-1"></i>Has Access
                                    </span>
                                @endif
                            @endif

                            @if(Auth::user()->hasAnyRole(['Administrator', 'Admin', 'Human Resource Manager', 'HumanResourceManager']))
                                <a href="{{ route('hr.employees.edit', $e) }}" 
                                   class="btn btn-sm btn-white rounded-pill px-3 py-2 fw-bold">
                                    Edit
                                </a>
                                <button type="button" 
                                        class="btn btn-sm btn-outline-danger rounded-pill px-3 py-2 fw-bold" 
                                        onclick="if(confirm('Permanently remove {{ $e->first_name }} {{ $e->last_name }} from records?')) document.getElementById('del-emp-{{ $e->id }}').submit()">
                                    Delete
                                    <form id="del-emp-{{ $e->id }}" action="{{ route('hr.employees.destroy', $e) }}" method="POST" class="d-none">@csrf @method('DELETE')</form>
                                </button>
                            @else
                                <span class="badge bg-secondary text-white fw-normal rounded-pill px-3 py-2">View Only</span>
                            @endif
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="text-center py-5">
                        <i class="bi bi-person-video2 fs-1 text-muted opacity-25"></i>
                        <div class="text-muted mt-3">No employees match your search criteria.</div>
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

@if($employees->hasPages())
    <div class="mt-4">
        {{ $employees->links() }}
    </div>
@endif
@endsection
