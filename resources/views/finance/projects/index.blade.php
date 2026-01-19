@section('title', 'Project Registry')

@section('content')
<div class="page-header-premium mb-4">
    <div class="row align-items-center">
        <div class="col">
            <h1 class="display-6">Project Registry</h1>
            <p>Operational oversight and budget tracking for all active construction sites.</p>
        </div>
        <div class="col-auto">
            <a href="{{ route('finance.projects.create') }}" class="btn btn-erp-deep rounded-pill px-4 shadow-sm border-0">
                <i class="bi bi-building-plus me-2"></i>New Project Site
            </a>
        </div>
    </div>
</div>

{{-- Search & Regional Stats --}}
<div class="row g-4 mb-4 stagger-entrance">
    <div class="col-lg-8">
        <div class="erp-card p-4 shadow-sm h-100">
            <form action="{{ route('finance.projects.index') }}" method="GET" class="row g-3 align-items-center">
                <div class="col-lg-8">
                    <div class="input-group bg-light rounded-pill overflow-hidden px-3 border-0">
                        <span class="input-group-text bg-transparent border-0 text-muted ps-3">
                            <i class="bi bi-search"></i>
                        </span>
                        <input type="text" name="q" value="{{ request('q') }}" 
                               class="form-control border-0 bg-transparent py-3" 
                               placeholder="Search projects by name, site location, or code...">
                    </div>
                </div>
                <div class="col-lg-4 text-lg-end">
                    @if(request('q') || request('status'))
                        <a href="{{ route('finance.projects.index') }}" class="btn btn-white rounded-pill px-4 me-2 border-0 shadow-sm fw-bold">Reset</a>
                    @endif
                    <button type="submit" class="btn btn-erp-deep rounded-pill px-4 py-3 border-0 shadow-sm fw-800">Inquire</button>
                </div>
            </form>
        </div>
    </div>
    <div class="col-lg-2 col-md-6">
        <div class="erp-card p-4 shadow-sm h-100 text-center">
            <div class="small text-muted fw-800 text-uppercase mb-1" style="font-size: 0.7rem;">Active Sites</div>
            <div class="fw-900 fs-1 text-erp-deep mb-0">{{ $projects->total() }}</div>
            <div class="text-success x-small fw-800"><i class="bi bi-activity"></i> LIVE</div>
        </div>
    </div>
    <div class="col-lg-2 col-md-6">
        <div class="erp-card p-4 shadow-sm h-100 text-center">
            <div class="small text-muted fw-800 text-uppercase mb-1" style="font-size: 0.7rem;">Portfolio Value</div>
            <div class="fw-900 fs-4 text-erp-deep mt-2">
                <small class="fw-bold opacity-50">ETB</small> {{ number_format($projects->sum('budget') / 1000000, 1) }}M
            </div>
        </div>
    </div>
</div>

<div class="table-responsive stagger-entrance">
    <table class="table-premium">
        <thead>
            <tr>
                <th class="ps-4">Project Identity</th>
                <th>Site Location</th>
                <th>Status</th>
                <th>Total Budget</th>
                <th>Utilization</th>
                <th class="text-end pe-4">Management</th>
            </tr>
        </thead>
        <tbody>
            @forelse($projects as $project)
                <tr>
                    <td class="ps-4">
                        <div class="d-flex align-items-center gap-3">
                             <div class="bg-erp-deep text-white rounded-4 d-flex align-items-center justify-content-center flex-shrink-0 shadow-sm" 
                                 style="width: 48px; height: 48px; background: linear-gradient(45deg, #064e3b, #059669);">
                                 <i class="bi bi-building fs-5"></i>
                             </div>
                             <div>
                                <div class="fw-800 text-erp-deep fs-5 mb-0">{{ $project->name }}</div>
                                <small class="text-muted fw-bold">Since {{ $project->created_at->format('M Y') }}</small>
                             </div>
                        </div>
                    </td>
                    <td>
                        <div class="d-flex align-items-center gap-2">
                            <i class="bi bi-geo-alt-fill text-primary"></i>
                            <span class="fw-700 text-dark">{{ $project->location ?? 'Site Undisclosed' }}</span>
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
                        <span class="badge {{ $statusClass }} rounded-pill px-3 py-2 border-0 fw-800 text-uppercase" style="font-size: 0.65rem;">
                            {{ $project->status ?? 'Draft' }}
                        </span>
                    </td>
                    <td>
                        <div class="fw-800 text-erp-deep">
                            <small class="text-muted fw-normal x-small">ETB</small> {{ number_format($project->budget, 0) }}
                        </div>
                    </td>
                    <td style="min-width: 180px;">
                        <div class="d-flex align-items-center gap-3">
                            <div class="progress flex-grow-1 bg-light rounded-pill" style="height: 8px;">
                                @php
                                    $usage = $project->budget_usage_percentage ?? 0;
                                    $barColor = $usage > 90 ? 'bg-danger' : ($usage > 70 ? 'bg-warning' : 'bg-primary');
                                @endphp
                                <div class="progress-bar {{ $barColor }} rounded-pill" role="progressbar" style="width: {{ $usage }}%"></div>
                            </div>
                            <span class="fw-900 text-erp-deep small">{{ round($usage) }}%</span>
                        </div>
                    </td>
                    <td class="text-end pe-4">
                        <div class="d-flex justify-content-end gap-2">
                            <a href="{{ route('finance.projects.show', $project) }}" class="btn btn-sm btn-white rounded-pill px-3 py-2 fw-bold shadow-sm" title="Intelligence">
                                <i class="bi bi-bar-chart-fill"></i>
                            </a>
                            <a href="{{ route('finance.projects.edit', $project) }}" class="btn btn-sm btn-white rounded-pill px-3 py-2 fw-bold shadow-sm" title="Settings">
                                <i class="bi bi-gear-fill"></i>
                            </a>
                            <form action="{{ route('finance.projects.destroy', $project) }}" method="POST" class="d-inline" id="del-proj-{{ $project->id }}">
                                @csrf @method('DELETE')
                                <button type="button" class="btn btn-sm btn-outline-danger rounded-pill px-3 py-2 fw-bold" 
                                        onclick="premiumConfirm('Archive Site', 'Deactivate and archive this production site?', 'del-proj-{{ $project->id }}', '{{ $project->name }}')">
                                    <i class="bi bi-archive-fill"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="text-center py-5">
                        <div class="text-muted fw-800 py-5">
                            <i class="bi bi-building display-1 mb-3 d-block opacity-10"></i>
                            No construction sites registered in the registry.
                        </div>
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="mt-4">
    {{ $projects->links() }}
</div>
@endsection


