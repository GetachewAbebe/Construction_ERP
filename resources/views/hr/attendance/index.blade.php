@extends('layouts.app')
@section('title', 'Attendance Control')

@section('content')
<div class="page-header-premium mb-5">
    <div class="row align-items-center">
        <div class="col">
            <h1 class="display-3 fw-900 text-erp-deep mb-0 tracking-tight">Attendance Registry</h1>
        </div>
        <div class="col-auto">
            @can('manage-attendance')
            <a href="{{ route('hr.attendance.monthly-summary') }}" class="btn btn-erp-deep rounded-pill px-5 py-3 fw-900 shadow-xl border-0 transform-hover">
                <i class="bi bi-file-earmark-bar-graph me-2 fs-5"></i>VIEW MONTHLY SUMMARY
            </a>
            @endcan
        </div>
    </div>
</div>

{{-- Real-time Operational Metrics --}}
<div class="row g-4 mb-4 stagger-entrance">
    <div class="col-md-3">
        <div class="card hardened-glass border-0 p-4 shadow-sm">
            <div class="small text-muted fw-bold text-uppercase mb-2">Present Today</div>
            <div class="d-flex align-items-end gap-2">
                <div class="fw-800 fs-2 text-success">{{ $todayStats['present'] ?? 0 }}</div>
                <div class="text-muted small fw-600 mb-2">/ {{ $employees->count() }} Total</div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card hardened-glass border-0 p-4 shadow-sm">
            <div class="small text-muted fw-bold text-uppercase mb-2">Late Arrivals</div>
            <div class="d-flex align-items-end gap-2">
                <div class="fw-800 fs-2 text-warning">{{ $todayStats['late'] ?? 0 }}</div>
                <div class="text-muted small fw-600 mb-2">Incidents</div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card hardened-glass border-0 p-4 shadow-sm">
            <div class="small text-muted fw-bold text-uppercase mb-2">Absent Today</div>
            <div class="d-flex align-items-end gap-2">
                <div class="fw-800 fs-2 text-danger">{{ $todayStats['absent'] ?? 0 }}</div>
                <div class="text-muted small fw-600 mb-2">Unaccounted</div>
            </div>
        </div>
    </div>
</div>

<div class="row g-4 mb-4 stagger-entrance">
    {{-- Check-in / Biometric Style Action --}}
    <div class="col-lg-5">
        <div class="card hardened-glass border-0 h-100 overflow-hidden shadow-sm">
            <div class="card-body p-4">
                <div class="d-flex align-items-center gap-3 mb-4">
                    <div class="access-icon-box bg-primary text-white p-3 rounded-4 shadow-lg">
                        <i class="bi bi-fingerprint fs-2"></i>
                    </div>
                    <div>
                        <h4 class="fw-800 text-erp-deep mb-0">Access Portal</h4>
                        <small class="text-muted fw-bold">{{ now()->format('l, jS F Y') }}</small>
                    </div>
                </div>

                <form action="{{ route('hr.attendance.check-in') }}" method="POST">
                    @csrf
                    <div class="mb-4">
                        @can('manage-attendance')
                            <label class="form-label small fw-800 text-muted text-uppercase">Identification</label>
                            <select name="employee_id" class="form-select border-0 bg-light-soft rounded-4 py-3 px-4 shadow-sm select2" data-placeholder="Select associate for check-in">
                                <option value="">Identify Associate...</option>
                                @foreach($employees as $employee)
                                    <option value="{{ $employee->id }}">
                                        {{ $employee->name }} — {{ $employee->position ?? 'Professional' }}
                                    </option>
                                @endforeach
                            </select>
                        @else
                            <div class="p-4 bg-light-soft rounded-4 border">
                                <div class="d-flex align-items-center gap-3">
                                    <div class="fw-800 text-erp-deep">
                                        {{ auth()->user()->employee->name ?? 'User Profile' }}
                                    </div>
                                    <span class="badge bg-primary-soft text-primary rounded-pill px-3">Standard Access</span>
                                </div>
                            </div>
                        @endcan
                    </div>

                    <button type="submit" class="btn btn-primary w-100 rounded-pill py-3 fw-800 fs-5 shadow-lg border-0">
                        Confirm Access Recording
                    </button>
                </form>

                @isset($myOpenAttendance)
                    <div class="mt-4 p-3 rounded-4 bg-warning-soft border-warning border border-opacity-10">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <small class="text-warning fw-bold d-block text-uppercase mb-1">Active Duty Session</small>
                                <span class="fw-800 text-erp-deep">Locked in at {{ $myOpenAttendance->clock_in->format('H:i') }}</span>
                            </div>
                            <form action="{{ route('hr.attendance.check-out', $myOpenAttendance->id) }}" method="POST">
                                @csrf
                                <button type="submit" class="btn btn-warning rounded-pill px-4 fw-800">
                                    Depart Site
                                </button>
                            </form>
                        </div>
                    </div>
                @endisset
            </div>
        </div>
    </div>

    {{-- Temporal Search & Filtering --}}
    <div class="col-lg-7">
        <div class="card hardened-glass border-0 h-100 shadow-sm">
            <div class="card-body p-4">
                <h5 class="fw-800 text-erp-deep mb-4"><i class="bi bi-funnel-fill me-2"></i>Analytical Filters</h5>
                <form method="GET" action="{{ route('hr.attendance.index') }}" class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label small fw-bold text-muted">Temporal Range: Start</label>
                        <div class="input-group bg-light-soft rounded-pill overflow-hidden shadow-sm px-3 border-0">
                            <span class="input-group-text bg-transparent border-0"><i class="bi bi-calendar-date"></i></span>
                            <input type="date" name="date_from" value="{{ request('date_from') }}" class="form-control border-0 bg-transparent">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label small fw-bold text-muted">Temporal Range: End</label>
                        <div class="input-group bg-light-soft rounded-pill overflow-hidden shadow-sm px-3 border-0">
                            <span class="input-group-text bg-transparent border-0"><i class="bi bi-calendar-check"></i></span>
                            <input type="date" name="date_to" value="{{ request('date_to') }}" class="form-control border-0 bg-transparent">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label small fw-bold text-muted">Associate Filter</label>
                        <select name="employee_filter" class="form-select border-0 bg-light-soft rounded-pill px-4 shadow-sm select2">
                            <option value="">Consolidated Workforce</option>
                            @foreach($employees as $employee)
                                <option value="{{ $employee->id }}" @selected(request('employee_filter') == $employee->id)>
                                    {{ $employee->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label small fw-bold text-muted">Presence Status</label>
                        <select name="status" class="form-select border-0 bg-light-soft rounded-pill px-4 shadow-sm">
                            <option value="">Any Operational State</option>
                            <option value="present" @selected(request('status') == 'present')>Standard Presence</option>
                            <option value="late" @selected(request('status') == 'late')>Tardy (Delayed)</option>
                            <option value="absent" @selected(request('status') == 'absent')>Null Presence</option>
                        </select>
                    </div>
                    <div class="col-12 text-end mt-4">
                        <a href="{{ route('hr.attendance.index') }}" class="btn btn-white rounded-pill px-4 me-2 border-0 shadow-sm">Reset</a>
                        <button type="submit" class="btn btn-erp-deep rounded-pill px-5 border-0 shadow-sm">Apply Analysis</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

{{-- Presence Transaction Ledger --}}
<div class="card hardened-glass border-0 overflow-hidden stagger-entrance">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="bg-light-soft text-erp-deep">
                <tr>
                    <th class="ps-4">Operational Date</th>
                    <th>Personnel Identity</th>
                    <th class="text-center">Entry</th>
                    <th class="text-center">Exit</th>
                    <th>Status Context</th>
                    <th class="text-end pe-4">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($attendances as $at)
                    <tr>
                        <td class="ps-4 fw-800 text-erp-deep">{{ $at->date->format('M d, Y') }}</td>
                        <td>
                            <div class="fw-800 text-dark">{{ $at->employee->name ?? 'Legacy Identity' }}</div>
                            <small class="text-muted fw-bold">{{ $at->employee->department ?? 'General Operations' }}</small>
                        </td>
                        <td class="text-center fw-600">{{ $at->clock_in ? $at->clock_in->format('H:i') : '—' }}</td>
                        <td class="text-center fw-600">{{ $at->clock_out ? $at->clock_out->format('H:i') : '—' }}</td>
                        <td>
                            @php
                                $statusIcon = match($at->status) {
                                    'present' => 'bi-circle-fill text-success',
                                    'late' => 'bi-dash-circle-fill text-warning',
                                    'absent' => 'bi-x-circle-fill text-danger',
                                    default => 'bi-question-circle text-muted'
                                };
                            @endphp
                            <span class="d-flex align-items-center gap-2 fw-700 text-erp-deep">
                                <i class="bi {{ $statusIcon }}" style="font-size: 8px;"></i>
                                {{ ucfirst($at->status) }}
                            </span>
                        </td>
                        <td class="text-end pe-4">
                            @if(!$at->clock_out)
                                <form action="{{ route('hr.attendance.check-out', $at->id) }}" method="POST" onsubmit="return confirm('Authorize site departure?');">
                                    @csrf
                                    <button type="submit" class="btn btn-sm btn-outline-danger rounded-pill px-3 fw-800">
                                        Force Depart
                                    </button>
                                </form>
                            @else
                                <span class="badge bg-light text-muted fw-normal rounded-pill px-3">Duty Concluded</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center py-5">
                            <i class="bi bi-calendar2-x fs-1 text-muted opacity-25"></i>
                            <div class="text-muted italic mt-3">No presence data detected for this temporal scope.</div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($attendances->hasPages())
        <div class="card-footer border-0 p-4">
            {{ $attendances->links() }}
        </div>
    @endif
</div>
@endsection

