@extends('layouts.app')
@section('title', 'Monthly Attendance Summary')

@section('content')
<div class="container py-4">

    {{-- HEADER --}}
    <div class="row mb-3">
        <div class="col d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-2">
            <div>
                <h1 class="h4 mb-1 text-erp-deep">Monthly Attendance Summary</h1>
                <p class="text-muted small mb-0">
                    Overview of attendance for {{ $startOfMonth->format('F Y') }}
                    @if($departmentFilter)
                        – Department: <strong>{{ $departmentFilter }}</strong>
                    @endif
                </p>
            </div>

            <div class="d-flex flex-wrap gap-2">
                <a href="{{ route('hr.attendance.index') }}" class="btn btn-outline-secondary btn-sm">
                    Back to Daily Attendance
                </a>

                {{-- Export CSV keeps current filters --}}
                <a href="{{ route('hr.attendance.monthly-summary.export', request()->query()) }}"
                   class="btn btn-primary btn-sm">
                    Export CSV
                </a>
            </div>
        </div>
    </div>

    {{-- FILTERS + SUMMARY CARDS --}}
    <div class="row g-3 mb-4">
        {{-- Filters --}}
        <div class="col-lg-4">
            <div class="card shadow-soft border-0 h-100">
                <div class="card-body">
                    <h6 class="card-title mb-3">Filters</h6>

                    <form method="GET" action="{{ route('hr.attendance.monthly-summary') }}" class="row g-2">
                        <div class="col-6">
                            <label class="form-label small">Year</label>
                            <select name="year" class="form-select form-select-sm">
                                @for($y = now()->year - 3; $y <= now()->year + 1; $y++)
                                    <option value="{{ $y }}" {{ (int)$year === $y ? 'selected' : '' }}>
                                        {{ $y }}
                                    </option>
                                @endfor
                            </select>
                        </div>
                        <div class="col-6">
                            <label class="form-label small">Month</label>
                            <select name="month" class="form-select form-select-sm">
                                @for($m = 1; $m <= 12; $m++)
                                    @php
                                        $monthName = \Carbon\Carbon::createFromDate($year, $m, 1)->format('F');
                                    @endphp
                                    <option value="{{ $m }}" {{ (int)$month === $m ? 'selected' : '' }}>
                                        {{ $monthName }}
                                    </option>
                                @endfor
                            </select>
                        </div>

                        <div class="col-12">
                            <label class="form-label small">Department</label>
                            <select name="department" class="form-select form-select-sm">
                                <option value="">All Departments</option>
                                @foreach($departments as $dept)
                                    <option value="{{ $dept }}"
                                        {{ $departmentFilter === $dept ? 'selected' : '' }}>
                                        {{ $dept }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-12 d-flex justify-content-between mt-2">
                            <button type="submit" class="btn btn-primary btn-sm">
                                Apply
                            </button>
                            @if(request()->has('year') || request()->has('month') || request()->has('department'))
                                <a href="{{ route('hr.attendance.monthly-summary') }}"
                                   class="btn btn-outline-secondary btn-sm">
                                    Reset
                                </a>
                            @endif
                        </div>
                    </form>
                </div>
            </div>
        </div>

        {{-- Summary cards + chart --}}
        <div class="col-lg-8">
            <div class="row g-3">
                <div class="col-md-3 col-6">
                    <div class="card shadow-soft border-0 h-100">
                        <div class="card-body py-3">
                            <div class="small text-muted">Employees</div>
                            <div class="fs-5 fw-semibold">{{ $totalEmployeesInScope }}</div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 col-6">
                    <div class="card shadow-soft border-0 h-100">
                        <div class="card-body py-3">
                            <div class="small text-muted">Present Days</div>
                            <div class="fs-5 fw-semibold text-success">{{ $totalPresentDays }}</div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 col-6">
                    <div class="card shadow-soft border-0 h-100">
                        <div class="card-body py-3">
                            <div class="small text-muted">Late Days</div>
                            <div class="fs-5 fw-semibold text-warning">{{ $totalLateDays }}</div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 col-6">
                    <div class="card shadow-soft border-0 h-100">
                        <div class="card-body py-3">
                            <div class="small text-muted">Total Hours</div>
                            <div class="fs-5 fw-semibold">{{ $totalHoursInScope }}</div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Chart --}}
            <div class="card shadow-soft border-0 mt-3">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <h6 class="card-title mb-0">Presence vs Lateness by Department</h6>
                        <span class="small text-muted">
                            {{ $startOfMonth->format('M d') }} – {{ $endOfMonth->format('M d, Y') }}
                        </span>
                    </div>
                    <div style="height: 220px;">
                        <canvas id="deptAttendanceChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- PER-DEPARTMENT SUMMARY TABLE --}}
    <div class="row mb-4">
        <div class="col">
            <div class="card shadow-soft border-0">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h5 class="card-title mb-0">Summary by Department</h5>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-sm align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Department</th>
                                    <th class="text-center">Employees</th>
                                    <th class="text-center">Present Days</th>
                                    <th class="text-center">Late Days</th>
                                    <th class="text-center">Total Hours</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($perDepartment as $deptSummary)
                                    <tr>
                                        <td>{{ $deptSummary['department'] ?: 'Unassigned' }}</td>
                                        <td class="text-center">{{ $deptSummary['employees_count'] }}</td>
                                        <td class="text-center text-success">{{ $deptSummary['present_days'] }}</td>
                                        <td class="text-center text-warning">{{ $deptSummary['late_days'] }}</td>
                                        <td class="text-center">{{ $deptSummary['total_hours'] }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center text-muted py-3">
                                            No data available for this month/filters.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                </div>
            </div>
        </div>
    </div>

    {{-- PER-EMPLOYEE DETAIL TABLE --}}
    <div class="row">
        <div class="col">
            <div class="card shadow-soft border-0">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h5 class="card-title mb-0">Details by Employee</h5>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-sm align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Employee</th>
                                    <th>Department</th>
                                    <th class="text-center">Records</th>
                                    <th class="text-center">Present</th>
                                    <th class="text-center">Late</th>
                                    <th class="text-center">Total Hours</th>
                                    <th class="text-center">Avg. Hours / Day</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($perEmployee as $summary)
                                    @php
                                        $employee = $summary['employee'];
                                    @endphp
                                    <tr>
                                        <td>
                                            <div class="fw-semibold">
                                                {{ $employee->first_name }} {{ $employee->last_name }}
                                            </div>
                                            <div class="small text-muted">
                                                #{{ $employee->id }}
                                            </div>
                                        </td>
                                        <td>{{ $employee->department ?? 'Unassigned' }}</td>
                                        <td class="text-center">{{ $summary['records'] }}</td>
                                        <td class="text-center text-success">{{ $summary['present_days'] }}</td>
                                        <td class="text-center text-warning">{{ $summary['late_days'] }}</td>
                                        <td class="text-center">{{ $summary['total_hours'] ?? 0 }}</td>
                                        <td class="text-center">
                                            @if(!is_null($summary['avg_hours']))
                                                {{ $summary['avg_hours'] }}
                                            @else
                                                <span class="text-muted small">-</span>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center text-muted py-3">
                                            No employee attendance records for this month/filters.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                </div>
            </div>
        </div>
    </div>

</div>
@endsection

@push('scripts')
    {{-- Chart.js CDN --}}
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const ctx = document.getElementById('deptAttendanceChart');
            if (!ctx) return;

            const deptLabels = @json($perDepartment->pluck('department'));
            const presentData = @json($perDepartment->pluck('present_days'));
            const lateData = @json($perDepartment->pluck('late_days'));

            new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: deptLabels,
                    datasets: [
                        {
                            label: 'Present Days',
                            data: presentData,
                        },
                        {
                            label: 'Late Days',
                            data: lateData,
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                precision: 0
                            }
                        }
                    }
                }
            });
        });
    </script>
@endpush
