@extends('layouts.app')
@section('title', 'Project Intelligence | ' . $project->name)

@section('content')
<div class="row align-items-center mb-4 stagger-entrance">
    <div class="col">
        <h1 class="h3 mb-1 fw-800 text-erp-deep">Project Intelligence</h1>
        <p class="text-muted mb-0">Detailed analytics and operational status for: <span class="text-erp-deep fw-800">{{ $project->name }}</span></p>
    </div>
    <div class="col-auto d-flex gap-2">
        <a href="{{ route('finance.projects.edit', $project) }}" class="btn btn-white rounded-pill px-4 shadow-sm border-0">
            <i class="bi bi-pencil-square me-2"></i>Refine Project
        </a>
        <a href="{{ route('finance.projects.index') }}" class="btn btn-white rounded-pill px-4 shadow-sm border-0">
            <i class="bi bi-arrow-left me-2"></i>Back to Dashboard
        </a>
    </div>
</div>

<div class="row g-4 mb-4 stagger-entrance">
    {{-- Key Project Metrics --}}
    <div class="col-lg-3 col-md-6">
        <div class="card hardened-glass border-0 p-4 shadow-sm h-100">
            <div class="small fw-800 text-muted text-uppercase mb-1">Project Budget</div>
            <div class="fw-800 fs-4 text-erp-deep">ETB {{ number_format($project->budget, 2) }}</div>
            <div class="small text-muted fw-600 mt-1">Total Allocation</div>
        </div>
    </div>
    <div class="col-lg-3 col-md-6">
        <div class="card hardened-glass border-0 p-4 shadow-sm h-100">
            <div class="small fw-800 text-muted text-uppercase mb-1">Current Expenditure</div>
            <div class="fw-800 fs-4 text-dark">ETB {{ number_format($project->total_expenses, 2) }}</div>
            <div class="small text-muted fw-600 mt-1">
                <span class="{{ $project->budget_usage_percentage > 90 ? 'text-danger' : 'text-success' }}">
                    <i class="bi bi-pie-chart-fill me-1"></i>{{ $project->budget_usage_percentage }}%
                </span>
                 Utilized
            </div>
        </div>
    </div>
    <div class="col-lg-3 col-md-6">
        <div class="card hardened-glass border-0 p-4 shadow-sm h-100">
            <div class="small fw-800 text-muted text-uppercase mb-1">Operational Status</div>
            @php
                $statusClass = match($project->status) {
                    'active' => 'text-success',
                    'completed' => 'text-info',
                    'on_hold' => 'text-warning',
                    default => 'text-secondary'
                };
                $statusIcon = match($project->status) {
                    'active' => 'activity',
                    'completed' => 'check-circle-fill',
                    'on_hold' => 'pause-circle-fill',
                    default => 'x-circle-fill'
                };
            @endphp
            <div class="fw-800 fs-4 {{ $statusClass }}">
                <i class="bi bi-{{ $statusIcon }} me-2"></i>{{ ucfirst($project->status) }}
            </div>
            <div class="small text-muted fw-600 mt-1">Lifecycle Stage</div>
        </div>
    </div>
    <div class="col-lg-3 col-md-6">
        <div class="card hardened-glass border-0 p-4 shadow-sm h-100">
            <div class="small fw-800 text-muted text-uppercase mb-1">Timeline</div>
            <div class="fw-700 text-dark">{{ optional($project->start_date)->format('M d, Y') ?? 'TBD' }}</div>
            <div class="small text-muted fw-600">to {{ optional($project->end_date)->format('M d, Y') ?? 'TBD' }}</div>
        </div>
    </div>
</div>

<div class="row g-4">
    {{-- Project Details & Scope --}}
    <div class="col-lg-4 stagger-entrance">
        <div class="card hardened-glass border-0 overflow-hidden shadow-sm h-100">
            <div class="card-header bg-light-soft border-0 p-4">
                <h5 class="fw-800 text-erp-deep mb-0 d-flex align-items-center gap-2">
                    <i class="bi bi-building text-primary"></i>
                    Project Profile
                </h5>
            </div>
            <div class="card-body p-4">
                <div class="mb-4">
                    <div class="small fw-800 text-muted text-uppercase mb-1">Site Location</div>
                    <div class="d-flex align-items-center gap-2">
                        <i class="bi bi-geo-alt-fill text-danger"></i>
                        <span class="fw-700 text-dark">{{ $project->location ?? 'Location not specified' }}</span>
                    </div>
                </div>

                <div class="mb-4">
                    <div class="small fw-800 text-muted text-uppercase mb-1">Scope & Description</div>
                    <div class="p-3 bg-light-soft rounded-4 text-muted small fw-600" style="min-height: 100px;">
                        {{ $project->description ?? 'No detailed scope provided for this project.' }}
                    </div>
                </div>

                <div>
                    <div class="small fw-800 text-muted text-uppercase mb-1">Timeline Progress</div>
                    @php
                        $start = $project->start_date ? \Carbon\Carbon::parse($project->start_date) : null;
                        $end = $project->end_date ? \Carbon\Carbon::parse($project->end_date) : null;
                        $now = now();
                        $progress = 0;
                        
                        if ($start && $end) {
                            $totalDuration = $end->diffInDays($start);
                            $daysElapsed = $now->diffInDays($start);
                            if ($totalDuration > 0) {
                                $progress = min(100, max(0, ($daysElapsed / $totalDuration) * 100));
                            }
                        }
                    @endphp
                    <div class="progress hardened-glass bg-white mt-2" style="height: 10px;">
                        <div class="progress-bar bg-erp-deep rounded-pill" role="progressbar" style="width: {{ $progress }}%"></div>
                    </div>
                    <div class="d-flex justify-content-between mt-1">
                        <small class="text-muted fw-bold">{{ number_format($progress, 0) }}% Elapsed</small>
                        <small class="text-muted fw-bold">{{ $end ? $end->diffForHumans() : 'Date pending' }}</small>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Expense Ledger --}}
    <div class="col-lg-8 stagger-entrance">
        <div class="card hardened-glass border-0 overflow-hidden shadow-sm h-100">
            <div class="card-header bg-light-soft border-0 p-4 d-flex align-items-center justify-content-between">
                <h5 class="fw-800 text-erp-deep mb-0 d-flex align-items-center gap-2">
                    <i class="bi bi-receipt-cutoff text-success"></i>
                    Financial Ledger
                </h5>
                {{-- If we had a create expense route linked to project id, we'd add it here --}}
            </div>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light-soft text-erp-deep">
                        <tr>
                            <th class="ps-4 py-3">Reference</th>
                            <th class="py-3">Requester</th>
                            <th class="py-3">Date</th>
                            <th class="py-3">Amount</th>
                            <th class="pe-4 py-3 text-end">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($project->expenses as $expense)
                            <tr>
                                <td class="ps-4">
                                    <div class="fw-700 text-dark">{{ $expense->title ?? 'Expense Request' }}</div>
                                    <small class="text-muted">#{{ $expense->id }}</small>
                                </td>
                                <td>
                                    @if($expense->user)
                                        <div class="d-flex align-items-center gap-2">
                                            <div class="bg-primary-soft text-primary rounded-circle d-flex align-items-center justify-content-center fw-bold" style="width: 30px; height: 30px; font-size: 0.75rem;">
                                                {{ substr($expense->user->first_name, 0, 1) }}{{ substr($expense->user->last_name, 0, 1) }}
                                            </div>
                                            <span class="fw-600 small">{{ $expense->user->name }}</span>
                                        </div>
                                    @else
                                        <span class="text-muted italic">Unknown</span>
                                    @endif
                                </td>
                                <td>
                                    <span class="text-muted fw-600 small">{{ $expense->created_at->format('M d, Y') }}</span>
                                </td>
                                <td>
                                    <div class="fw-800 text-dark">ETB {{ number_format($expense->amount, 2) }}</div>
                                </td>
                                <td class="pe-4 text-end">
                                    @php
                                        $statusClass = match($expense->status) {
                                            'approved' => 'bg-success-soft text-success',
                                            'pending' => 'bg-warning-soft text-warning',
                                            'rejected' => 'bg-danger-soft text-danger',
                                            default => 'bg-secondary-soft text-secondary'
                                        };
                                    @endphp
                                    <span class="badge {{ $statusClass }} rounded-pill px-3 py-1 border-0 fw-700">
                                        {{ ucfirst($expense->status) }}
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center py-5">
                                    <i class="bi bi-wallet2 fs-1 text-muted opacity-25"></i>
                                    <div class="text-muted italic mt-3">No financial records recorded for this site.</div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
