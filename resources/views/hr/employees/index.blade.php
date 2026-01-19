@extends('layouts.app')
@section('title', 'Corporate Directory')

@section('content')
<div class="page-header-premium mb-5">
    <div class="row align-items-center">
        <div class="col">
            <h1 class="display-3 fw-900 text-erp-deep mb-0 tracking-tight">Employee Directory</h1>
        </div>
        <div class="col-auto">
            @if(Auth::user()->hasAnyRole(['Administrator', 'Admin', 'Human Resource Manager', 'HumanResourceManager']))
            <a href="{{ route('hr.employees.create') }}" class="btn btn-erp-deep rounded-pill px-5 py-3 fw-900 shadow-xl border-0 transform-hover">
                <i class="bi bi-person-plus-fill me-3 fs-5"></i>EXECUTE ONBOARDING PROTOCOL
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
<div class="hardened-glass p-2 mb-5 stagger-entrance shadow-lg">
    <div class="card bg-transparent border-0">
        <div class="card-body p-4">
            <form action="{{ route('hr.employees.index') }}" method="GET" class="row g-4 align-items-end">
                <div class="col-lg-8">
                    <label class="erp-label text-uppercase tracking-widest small mb-3 d-block opacity-75 fw-800">Global Personnel Search</label>
                    <div class="input-group bg-white rounded-pill px-4 py-2 shadow-sm border transaction-all">
                        <span class="input-group-text bg-transparent border-0 text-primary"><i class="bi bi-search fs-5"></i></span>
                        <input type="text" name="q" class="form-control border-0 bg-transparent py-2 fw-600" 
                               placeholder="Scan registry by name, identity credentials, or organizational unit..." 
                               value="{{ request('q') }}">
                    </div>
                </div>
                <div class="col-lg-3">
                    <label class="erp-label text-uppercase tracking-widest small mb-3 d-block opacity-75 fw-800">Filter by Status</label>
                    <select name="status" class="form-select border-pill bg-white rounded-pill py-3 px-4 shadow-sm fw-700 text-erp-deep border" onchange="this.form.submit()">
                        <option value="">Status: All Personnel</option>
                        <option value="Active" {{ request('status') === 'Active' ? 'selected' : '' }}>Operational (Active)</option>
                        <option value="On Leave" {{ request('status') === 'On Leave' ? 'selected' : '' }}>On Leave Portfolio</option>
                        <option value="Terminated" {{ request('status') === 'Terminated' ? 'selected' : '' }}>Deactivated / Terminated</option>
                        <option value="Resigned" {{ request('status') === 'Resigned' ? 'selected' : '' }}>Voluntary Departure</option>
                    </select>
                </div>
                <div class="col-lg-1">
                    <button type="submit" class="btn btn-erp-deep rounded-circle p-0 d-flex align-items-center justify-content-center shadow-lg transform-hover" style="width: 58px; height: 58px;">
                        <i class="bi bi-funnel-fill fs-5"></i>
                    </button>
                </div>
            </form>
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
