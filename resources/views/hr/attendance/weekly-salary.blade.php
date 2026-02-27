@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-800 text-erp-deep mb-1">Weekly Salary Analysis</h2>
            <p class="text-muted">Attendance credits and financial projections for the current cycle.</p>
        </div>
        <div class="d-flex gap-3">
             <form action="{{ route('hr.attendance.weekly-salary') }}" method="GET" class="d-flex gap-2">
                <div class="input-group">
                    <span class="input-group-text bg-white border-end-0 x-small fw-bold text-muted">WEEK START</span>
                    <input type="date" name="week_start" class="form-control border-start-0 glass-input" value="{{ $weekStart->toDateString() }}" onchange="this.form.submit()">
                </div>
            </form>
            <a href="#" class="btn btn-outline-erp px-4 rounded-pill">
                <i class="bi bi-printer me-2"></i> Print Report
            </a>
        </div>
    </div>

    <div class="alert alert-info border-0 shadow-sm mb-4 bg-info-soft text-info fw-bold">
        <i class="bi bi-info-circle-fill me-2"></i> Cycle: {{ $weekStart->format('M d, Y') }} — {{ $weekEnd->format('M d, Y') }} | Standard Daily Rate: (Monthly Salary / 22 Working Days)
    </div>

    <div class="card glass-card border-0 shadow-sm overflow-hidden">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light">
                    <tr>
                        <th class="ps-4">Associate</th>
                        <th class="text-center">Active Credits</th>
                        <th class="text-center">Daily Yield</th>
                        <th class="text-center">Projected Batch Pay</th>
                        <th class="text-end pe-4">Status</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($analysis as $row)
                        @php $employee = $row['employee']; @endphp
                        <tr>
                            <td class="ps-4">
                                <div class="d-flex align-items-center">
                                    <div class="avatar-sq rounded bg-primary-soft text-primary d-flex align-items-center justify-content-center fw-bold me-3 shadow-sm" style="width: 40px; height: 40px;">
                                        {{ substr($employee->first_name, 0, 1) }}
                                    </div>
                                    <div>
                                        <div class="fw-bold text-erp-deep">{{ $employee->full_name }}</div>
                                        <div class="small text-muted text-uppercase tracking-wider" style="font-size: 0.65rem;">{{ $employee->position }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="text-center">
                                <div class="fw-900 fs-5 text-erp-primary">{{ number_format($row['credits'], 1) }}</div>
                                <div class="x-small text-muted">SESSIONS</div>
                            </td>
                            <td class="text-center">
                                <div class="fw-bold text-dark">{{ number_format($row['daily_rate'], 2) }} ETB</div>
                                <div class="x-small text-muted">PER CREDIT</div>
                            </td>
                            <td class="text-center">
                                <div class="badge bg-dark-soft text-dark px-3 py-2 fs-6 border">
                                    {{ number_format($row['payable_amount'], 2) }} ETB
                                </div>
                            </td>
                            <td class="text-end pe-4">
                                @if($row['credits'] >= 5)
                                    <span class="badge bg-success-soft text-success rounded-pill px-3 py-1 border border-success-subtle">FULL CAPACITY</span>
                                @elseif($row['credits'] > 0)
                                    <span class="badge bg-warning-soft text-warning rounded-pill px-3 py-1 border border-warning-subtle">PARTUALLY ACTIVE</span>
                                @else
                                    <span class="badge bg-danger-soft text-danger rounded-pill px-3 py-1 border border-danger-subtle">INACTIVE CYCLE</span>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

<style>
    .bg-info-soft { background-color: rgba(13, 202, 240, 0.1); }
    .bg-dark-soft { background-color: rgba(33, 37, 41, 0.05); }
    .avatar-sq { border: 1px solid rgba(0,0,0,0.05); }
</style>
@endsection
