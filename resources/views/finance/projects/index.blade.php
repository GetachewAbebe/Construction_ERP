@extends('layouts.app')

@section('title', 'Projects - Natanem Engineering')

@section('content')
<div class="row align-items-center mb-4 stagger-entrance">
    <div class="col">
        <h1 class="h3 mb-1 fw-800 text-erp-deep">Construction Initiatives</h1>
        <p class="text-muted mb-0">Strategic project oversight and high-level budget management.</p>
    </div>
    <div class="col-auto">
        <a href="{{ route('finance.projects.create') }}" class="btn btn-erp-deep rounded-pill px-4 shadow-sm border-0">
            <i class="bi bi-building-plus me-2"></i>Initialize Project
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

{{-- Search & Stats --}}
<div class="row g-4 mb-4 stagger-entrance">
    <div class="col-lg-8">
        <div class="card hardened-glass border-0 p-4 shadow-sm h-100">
            <form action="{{ route('finance.projects.index') }}" method="GET" class="row g-3 align-items-center">
                <div class="col-lg-8">
                    <div class="input-group bg-light-soft rounded-pill overflow-hidden shadow-sm px-3 border-0">
                        <span class="input-group-text bg-transparent border-0 text-muted">
                            <i class="bi bi-search"></i>
                        </span>
                        <input type="text" name="q" value="{{ request('q') }}" 
                               class="form-control border-0 bg-transparent py-3" 
                               placeholder="Search initiatives by name, location, or code...">
                    </div>
                </div>
                <div class="col-lg-4 text-lg-end">
                    @if(request('q') || request('status'))
                        <a href="{{ route('finance.projects.index') }}" class="btn btn-white rounded-pill px-4 me-2 border-0 shadow-sm">Reset</a>
                    @endif
                    <button type="submit" class="btn btn-erp-deep rounded-pill px-5 border-0 shadow-sm">Search</button>
                </div>
            </form>
        </div>
    </div>
    <div class="col-lg-2 col-md-6">
        <div class="card hardened-glass border-0 p-4 shadow-sm h-100 text-center text-lg-start">
            <div class="small text-muted fw-bold text-uppercase mb-1">Active Sites</div>
            <div class="d-flex align-items-center justify-content-center justify-content-lg-start gap-2">
                <div class="fw-800 fs-2 text-erp-deep">{{ $projects->total() }}</div>
                <div class="text-success small fw-700 mb-1"><i class="bi bi-activity"></i> Live</div>
            </div>
        </div>
    </div>
    <div class="col-lg-2 col-md-6">
        <div class="card hardened-glass border-0 p-4 shadow-sm h-100 text-center text-lg-start">
            <div class="small text-muted fw-bold text-uppercase mb-1">Total Budget</div>
            <div class="fw-800 fs-5 text-erp-deep text-truncate" title="ETB {{ number_format($projects->sum('budget'), 0) }}">
                ETB {{ number_format($projects->sum('budget') / 1000000, 1) }}M
            </div>
        </div>
    </div>
</div>

<div class="card hardened-glass border-0 overflow-hidden stagger-entrance shadow-lg">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="bg-light-soft text-erp-deep">
                <tr>
                    <th class="ps-4 py-3">Site Identity</th>
                    <th class="py-3">Geographical Area</th>
                    <th class="py-3">Current Status</th>
                    <th class="py-3">Financial Allocation</th>
                    <th class="py-3">Budget Utilization</th>
                    <th class="text-end pe-4 py-3">Management</th>
                </tr>
            </thead>
            <tbody>
                @forelse($projects as $project)
                    <tr>
                        <td class="ps-4">
                            <div class="fw-800 text-erp-deep fs-6">{{ $project->name }}</div>
                            <small class="text-muted d-flex align-items-center gap-1">
                                <i class="bi bi-calendar-event"></i>
                                Created {{ $project->created_at->format('M Y') }}
                            </small>
                        </td>
                        <td>
                            <div class="d-flex align-items-center gap-2">
                                <i class="bi bi-pin-map text-primary"></i>
                                <span class="fw-600 text-dark">{{ $project->location ?? 'Site Undisclosed' }}</span>
                            </div>
                        </td>
                        <td>
                            @php
                                $statusClass = match($project->status) {
                                    'active' => 'bg-success-soft text-success',
                                    'completed' => 'bg-info-soft text-info',
                                    'on_hold' => 'bg-warning-soft text-warning',
                                    default => 'bg-secondary-soft text-secondary'
                                };
                            @endphp
                            <span class="badge {{ $statusClass }} border-0 rounded-pill px-3 py-2 fw-600">
                                {{ ucfirst($project->status) }}
                            </span>
                        </td>
                        <td>
                            <div class="fw-800 text-erp-deep">
                                <small class="text-muted fw-normal">ETB</small> {{ number_format($project->budget, 2) }}
                            </div>
                        </td>
                        <td style="min-width: 180px;">
                            <div class="d-flex align-items-center gap-3">
                                <div class="progress flex-grow-1 hardened-glass bg-white" style="height: 8px;">
                                    @php
                                        $usage = $project->budget_usage_percentage ?? 0;
                                        $barColor = $usage > 90 ? 'bg-danger' : ($usage > 70 ? 'bg-warning' : 'bg-primary');
                                    @endphp
                                    <div class="progress-bar {{ $barColor }} rounded-pill" role="progressbar" style="width: {{ $usage }}%"></div>
                                </div>
                                <span class="fw-800 text-erp-deep small">{{ $usage }}%</span>
                            </div>
                        </td>
                        <td class="text-end pe-4">
                            <div class="btn-group hardened-glass rounded-pill p-1 shadow-sm">
                                <a href="{{ route('finance.projects.show', $project) }}" class="btn btn-sm btn-white rounded-pill px-3" title="Project Intelligence">
                                    <i class="bi bi-kanban"></i>
                                </a>
                                <a href="{{ route('finance.projects.edit', $project) }}" class="btn btn-sm btn-white rounded-pill px-3" title="Refine Project">
                                    <i class="bi bi-gear-fill"></i>
                                </a>
                                <button type="button" class="btn btn-sm btn-white text-danger rounded-pill px-3" 
                                        onclick="if(confirm('Deactivate and archive this site?')) document.getElementById('del-proj-{{ $project->id }}').submit()">
                                    <i class="bi bi-x-square-fill"></i>
                                    <form id="del-proj-{{ $project->id }}" action="{{ route('finance.projects.destroy', $project) }}" method="POST" class="d-none">@csrf @method('DELETE')</form>
                                </button>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center py-5">
                            <i class="bi bi-building fs-1 text-muted opacity-25"></i>
                            <div class="text-muted italic mt-3">No construction sites are currently under surveillance.</div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection

