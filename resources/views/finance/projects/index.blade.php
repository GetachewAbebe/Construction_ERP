@extends('layouts.app')

@section('title', 'Projects - Natanem Engineering')

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 fw-bold mb-0">Construction Projects</h1>
            <p class="text-muted mb-0">Manage active sites and track budget usage.</p>
        </div>
        <a href="{{ route('finance.projects.create') }}" class="btn btn-primary rounded-pill px-4">
            <i class="bi bi-plus-lg me-2"></i>New Project
        </a>
    </div>

    @if(session('status'))
        <div class="alert alert-success alert-dismissible fade show rounded-4 border-0 shadow-sm mb-4" role="alert">
            {{ session('status') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light">
                    <tr>
                        <th class="ps-4">Project Name</th>
                        <th>Location</th>
                        <th>Status</th>
                        <th>Budget</th>
                        <th>Usage</th>
                        <th class="text-end pe-4">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($projects as $project)
                        <tr>
                            <td class="ps-4">
                                <div class="fw-bold">{{ $project->name }}</div>
                                <small class="text-muted">{{ $project->start_date?->format('M d, Y') ?? 'N/A' }}</small>
                            </td>
                            <td>{{ $project->location ?? 'N/A' }}</td>
                            <td>
                                <span class="badge rounded-pill bg-{{ $project->status === 'active' ? 'success' : ($project->status === 'on_hold' ? 'warning' : 'secondary') }}-soft px-3">
                                    {{ ucfirst($project->status) }}
                                </span>
                            </td>
                            <td class="fw-semibold text-dark">
                                ETB {{ number_format($project->budget, 2) }}
                            </td>
                            <td style="width: 200px;">
                                <div class="d-flex align-items-center">
                                    <div class="progress flex-grow-1 rounded-pill me-2" style="height: 6px;">
                                        <div class="progress-bar bg-primary" role="progressbar" 
                                             style="width: {{ $project->budget_usage_percentage }}%" 
                                             aria-valuenow="{{ $project->budget_usage_percentage }}" aria-valuemin="0" aria-valuemax="100"></div>
                                    </div>
                                    <small class="fw-bold">{{ $project->budget_usage_percentage }}%</small>
                                </div>
                            </td>
                            <td class="text-end pe-4">
                                <div class="dropdown">
                                    <button class="btn btn-icon btn-light rounded-circle shadow-none" type="button" data-bs-toggle="dropdown">
                                        <i class="bi bi-three-dots-vertical"></i>
                                    </button>
                                    <ul class="dropdown-menu dropdown-menu-end border-0 shadow-lg rounded-3">
                                        <li><a class="dropdown-item" href="{{ route('finance.projects.show', $project) }}">View Details</a></li>
                                        <li><a class="dropdown-item" href="{{ route('finance.projects.edit', $project) }}">Edit Project</a></li>
                                        <li><hr class="dropdown-divider"></li>
                                        <li>
                                            <form action="{{ route('finance.projects.destroy', $project) }}" method="POST" onsubmit="return confirm('Archive this project?');">
                                                @csrf @method('DELETE')
                                                <button type="submit" class="dropdown-item text-danger">Delete</button>
                                            </form>
                                        </li>
                                    </ul>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-5">
                                <div class="text-muted">No projects found. Create your first construction project to get started.</div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($projects->hasPages())
            <div class="card-footer bg-white border-0 py-3">
                {{ $projects->links() }}
            </div>
        @endif
    </div>
</div>

<style>
    .bg-success-soft { background-color: rgba(25, 135, 84, 0.1); color: #198754; }
    .bg-warning-soft { background-color: rgba(255, 193, 7, 0.1); color: #ffc107; }
    .bg-secondary-soft { background-color: rgba(108, 117, 125, 0.1); color: #6c757d; }
    .btn-icon { width: 32px; height: 32px; padding: 0; display: inline-flex; align-items: center; justify-content: center; }
</style>
@endsection
