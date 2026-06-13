@extends('layouts.app')
@section('title', 'Project Registry')

@section('content')
<div class="mb-5">
    <div class="row align-items-center g-3">
        <div class="col-md">
            <span class="text-uppercase tracking-wider text-muted font-bold small d-block mb-1" style="font-size: 0.75rem; letter-spacing: 0.1em;">Portfolio Overview</span>
            <h1 class="display-4 fw-900 text-erp-deep mb-0 tracking-tight" style="letter-spacing: -0.03em;">Project Registry</h1>
        </div>
        <div class="col-md-auto">
            <a href="{{ route('finance.projects.create') }}" class="btn bg-gradient-premium text-white rounded-pill px-5 py-3 fw-800 shadow-lg border-0 transform-hover-premium d-inline-flex align-items-center gap-2">
                <i class="bi bi-building-plus fs-5"></i>
                <span style="letter-spacing: 0.05em;">NEW PROJECT</span>
            </a>
        </div>
    </div>
</div>

{{-- Analytics Hub & Filter Bar --}}
<div class="row g-4 mb-5">
    <div class="col-xl-6">
        <div class="search-glass p-3 h-100 d-flex align-items-center shadow-sm">
            <form action="{{ route('finance.projects.index') }}" method="GET" class="w-full row g-2 align-items-center m-0">
                <div class="col-sm-9">
                    <div class="input-group bg-light rounded-pill px-3 py-1 border-0">
                        <span class="input-group-text bg-transparent border-0 text-muted">
                            <i class="bi bi-search fs-5"></i>
                        </span>
                        <input type="text" name="q" value="{{ request('q') }}" 
                               class="form-control border-0 bg-transparent py-2 text-dark font-medium" 
                               placeholder="Search site layout, code name, or location...">
                    </div>
                </div>
                <div class="col-sm-3 text-end d-flex gap-2 justify-content-end">
                    @if(request('q') || request('status'))
                        <a href="{{ route('finance.projects.index') }}" class="btn btn-light rounded-pill px-3 border-0 fw-bold shadow-sm text-secondary">Reset</a>
                    @endif
                    <button type="submit" class="btn bg-gradient-dark text-white rounded-pill px-4 border-0 shadow-sm fw-700">Inquire</button>
                </div>
            </form>
        </div>
    </div>

    <div class="col-sm-6 col-xl-3">
        <div class="project-card-premium p-4 shadow-sm h-100 d-flex align-items-center justify-content-between relative overflow-hidden" style="border-left: 5px solid #059669;">
            <div>
                <div class="text-muted fw-800 text-uppercase tracking-wider mb-1" style="font-size: 0.65rem;">Active Footprint</div>
                <div class="fw-900 display-5 text-slate-900 tracking-tight">{{ $projects->total() }}</div>
                <div class="text-success small font-bold d-flex align-items-center gap-1 mt-1">
                    <span class="d-inline-block rounded-circle bg-success animate-pulse" style="width: 7px; height: 7px;"></span>
                    <span>OPERATIONAL SITES</span>
                </div>
            </div>
            <div class="bg-light rounded-circle p-3 text-emerald-700">
                <i class="bi bi-activity fs-2"></i>
            </div>
        </div>
    </div>

    <div class="col-sm-6 col-xl-3">
        <div class="project-card-premium p-4 shadow-sm h-100 d-flex align-items-center justify-content-between relative overflow-hidden" style="border-left: 5px solid #0f172a;">
            <div>
                <div class="text-muted fw-800 text-uppercase tracking-wider mb-1" style="font-size: 0.65rem;">Portfolio Scale</div>
                <div class="fw-900 fs-2 text-slate-900 tracking-tight mt-1">
                    <span class="fs-6 font-bold text-muted">ETB</span> {{ number_format($projects->sum('budget') / 1000000, 1) }}M
                </div>
                <div class="text-muted font-medium small mt-1">Aggregate allocation</div>
            </div>
            <div class="bg-light rounded-circle p-3 text-slate-700">
                <i class="bi bi-cash-coin fs-2"></i>
            </div>
        </div>
    </div>
</div>

{{-- Premium Cards Grid Layout --}}
<div class="row g-4">
    @forelse($projects as $project)
        @php
            // Dynamic metrics from our updated ProjectController integrations
            $spent = $project->total_approved_spending ?? 0;
            $usagePercentage = $project->budget > 0 ? min(($spent / $project->budget) * 100, 100) : 0;
            $remainingWallet = $project->budget - $spent;

            // Status badge class mapping
            $statusStyle = match($project->status) {
                'active', 'operational' => 'bg-success text-white',
                'completed' => 'bg-info text-dark',
                'on_hold' => 'bg-warning text-dark',
                default => 'bg-secondary text-white'
            };

            // Budget depletion indicator alerts
            $barColor = $usagePercentage > 90 ? 'bg-danger' : ($usagePercentage > 75 ? 'bg-warning' : 'bg-success-gradient');
        @endphp
        
        <div class="col-lg-6 col-xl-4">
            <div class="project-card-premium p-4 shadow-sm flex-column d-flex h-100 justify-content-between">
                <div>
                    <div class="d-flex align-items-start justify-content-between gap-3 mb-4">
                        <div class="d-flex align-items-center gap-3">
                            <div class="bg-gradient-premium text-white rounded-4 d-flex align-items-center justify-content-center flex-shrink-0 shadow-md" style="width: 52px; height: 52px;">
                                <i class="bi bi-building fs-4"></i>
                            </div>
                            <div>
                                <h3 class="fw-800 text-slate-900 fs-5 mb-0 tracking-tight">{{ $project->name }}</h3>
                                <small class="text-muted font-medium d-flex align-items-center gap-1 mt-0.5">
                                    <i class="bi bi-geo-alt-fill text-danger"></i> {{ $project->location ?? 'Undisclosed Location' }}
                                </small>
                            </div>
                        </div>
                        <span class="badge metric-pill-badge {{ $statusStyle }} shadow-sm text-uppercase">
                            {{ $project->status ?? 'Operational' }}
                        </span>
                    </div>

                    <div class="bg-light rounded-4 p-3 mb-4">
                        <div class="row g-2 text-center">
                            <div class="col-6 border-end border-slate-200">
                                <span class="text-muted d-block small mb-1" style="font-size: 11px; font-weight: 700;">TOTAL BUDGET</span>
                                <span class="fw-800 text-dark font-mono text-sm">ETB {{ number_format($project->budget, 0) }}</span>
                            </div>
                            <div class="col-6">
                                <span class="text-muted d-block small mb-1" style="font-size: 11px; font-weight: 700;">APPROVED SPENT</span>
                                <span class="fw-800 text-rose-600 font-mono text-sm">ETB {{ number_format($spent, 0) }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                <div>
                    <div class="d-flex align-items-center justify-content-between mb-1">
                        <span class="text-muted small font-bold" style="font-size: 11px;">BUDGET EXHAUSTION</span>
                        <span class="small font-black {{ $usagePercentage > 90 ? 'text-danger animate-pulse' : 'text-emerald-800' }}">{{ round($usagePercentage) }}%</span>
                    </div>
                    
                    <div class="progress-track-premium mb-3">
                        <div class="h-100 rounded-pill {{ $barColor }}" style="width: {{ $usagePercentage }}%; background: {{ $usagePercentage <= 75 ? 'linear-gradient(90deg, #10b981, #059669)' : '' }};"></div>
                    </div>

                    <div class="d-flex align-items-center justify-content-between pb-3 border-b border-light mb-3" style="font-size: 11px;">
                        <span class="text-muted font-medium"><i class="bi bi-receipt-cutoff me-1"></i>{{ $project->expenses_count }} Logged Invoices</span>
                        <span class="font-bold {{ $remainingWallet < 0 ? 'text-danger' : 'text-slate-600' }}">
                            Wallet Left: ETB {{ number_format($remainingWallet, 0) }}
                        </span>
                    </div>

                    <div class="d-flex gap-2 justify-content-end">
                        <a href="{{ route('finance.projects.show', $project) }}" class="btn btn-light rounded-pill px-3 py-2 fw-700 d-inline-flex align-items-center gap-1 border border-slate-200 text-secondary shadow-2xs" style="font-size: 0.78rem;">
                            <i class="bi bi-eye-fill"></i> View Details
                        </a>
                        <a href="{{ route('finance.projects.edit', $project) }}" class="btn bg-gradient-dark text-white rounded-pill px-3 py-2 fw-700 d-inline-flex align-items-center gap-1 border-0 shadow-sm" style="font-size: 0.78rem;">
                            <i class="bi bi-pencil-square"></i> Edit
                        </a>
                        <form action="{{ route('finance.projects.destroy', $project) }}" method="POST" class="d-inline" id="del-proj-{{ $project->id }}">
                            @csrf @method('DELETE')
                            <button type="button" class="btn btn-outline-danger rounded-pill px-3 py-2 fw-700 d-inline-flex align-items-center gap-1 shadow-2xs" 
                                    onclick="premiumConfirm('Archive Site', 'Deactivate and archive this production site?', 'del-proj-{{ $project->id }}', '{{ $project->name }}')"
                                    style="font-size: 0.78rem;">
                                <i class="bi bi-archive-fill"></i>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    @empty
        <div class="col-12">
            <div class="project-card-premium text-center py-5 border-dashed bg-light/50">
                <div class="text-muted fw-800 py-5">
                    <i class="bi bi-building display-2 mb-3 d-block opacity-20 text-emerald-800"></i>
                    <h4 class="fw-bold text-slate-700">No Projects Registered</h4>
                    <p class="text-muted small max-w-md mx-auto mt-1">There are currently no active construction or engineering developments recorded in the portfolio database.</p>
                </div>
            </div>
        </div>
    @endforelse
</div>

<div class="mt-5 d-flex justify-content-center">
    {{ $projects->links() }}
</div>
@endsection