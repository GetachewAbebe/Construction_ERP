@extends('layouts.app')
@section('title','Attendance Management')

@section('content')
<div class="container py-4">

    {{-- PAGE HEADER + SUMMARY --}}
    <div class="row mb-3">
        <div class="col d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
            <div>
                <h1 class="h4 mb-1 text-erp-deep">Attendance Management</h1>
                <p class="text-muted small mb-0">
                    Track daily check-in and check-out, monitor lateness and overall presence.
                </p>
            </div>

            <div class="d-flex flex-wrap gap-2 align-items-center justify-content-md-end">
                @if(isset($todayStats))
                    <div class="d-flex flex-wrap gap-2 me-md-2">
                        <div class="card border-0 shadow-sm py-1 px-2">
                            <div class="small text-muted mb-0">Present</div>
                            <div class="fw-bold">{{ $todayStats['present'] ?? 0 }}</div>
                        </div>
                        <div class="card border-0 shadow-sm py-1 px-2">
                            <div class="small text-muted mb-0">Late</div>
                            <div class="fw-bold text-warning">{{ $todayStats['late'] ?? 0 }}</div>
                        </div>
                        <div class="card border-0 shadow-sm py-1 px-2">
                            <div class="small text-muted mb-0">Absent</div>
                            <div class="fw-bold text-danger">{{ $todayStats['absent'] ?? 0 }}</div>
                        </div>
                    </div>
                @endif

                {{-- Link to monthly summary --}}
                @can('manage-attendance')
                    <a href="{{ route('hr.attendance.monthly-summary') }}" class="btn btn-outline-primary btn-sm">
                        Monthly Summary
                    </a>
                @endcan
            </div>
        </div>
    </div>

    {{-- FLASH MESSAGES --}}
    <div class="row mb-3">
        <div class="col">
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            @if($errors->any())
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <ul class="mb-0 small">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif
        </div>
    </div>

    {{-- QUICK ACTIONS + FILTERS ROW --}}
    <div class="row mb-4 g-3">
        <div class="col-lg-6">
            <div class="card shadow-soft border-0 h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <h5 class="card-title mb-0">Quick Actions</h5>
                        <span class="badge bg-light text-muted small">
                            {{ now()->format('Y-m-d') }}
                        </span>
                    </div>
                    <p class="small text-muted mb-3">
                        Quickly check in employees for today’s attendance.
                    </p>

                    <form action="{{ route('hr.attendance.check-in') }}" method="POST" class="row gy-2 gx-2 align-items-end">
                        @csrf

                        {{-- If HR/admin: choose any employee; otherwise default to logged-in user --}}
                        <div class="col-12 @can('manage-attendance') col-md-8 @else col-md-12 @endcan">
                            <label for="employee_id" class="form-label small fw-semibold">Employee</label>
                            @can('manage-attendance')
                                <select name="employee_id" id="employee_id"
                                        class="form-select form-select-sm select2"
                                        data-placeholder="Select employee">
                                    <option value="">-- Select Employee --</option>
                                    @foreach($employees as $employee)
                                        <option value="{{ $employee->id }}"
                                            {{ old('employee_id') == $employee->id ? 'selected' : '' }}>
                                            {{ $employee->first_name }} {{ $employee->last_name }}
                                            ({{ $employee->department }})
                                        </option>
                                    @endforeach
                                </select>
                            @else
                                <input type="hidden" name="employee_id" value="{{ auth()->user()->employee_id }}">
                                <div class="form-control form-control-sm bg-light">
                                    {{ auth()->user()->employee->first_name }}
                                    {{ auth()->user()->employee->last_name }}
                                    ({{ auth()->user()->employee->department }})
                                </div>
                            @endcan
                        </div>

                        <div class="col-6 col-md-4 text-md-end">
                            <button type="submit"
                                    class="btn btn-primary btn-sm w-100"
                                    onclick="this.disabled = true; this.form.submit();">
                                Check In Now
                            </button>
                        </div>
                    </form>

                    {{-- Quick Check-out for own open session --}}
                    @isset($myOpenAttendance)
                        <hr class="my-3">
                        <form action="{{ route('hr.attendance.check-out', $myOpenAttendance->id) }}" method="POST"
                              onsubmit="return confirm('Confirm check out now?');">
                            @csrf
                            <div class="d-flex justify-content-between align-items-center">
                                <div class="small text-muted">
                                    You are checked in since
                                    <strong>{{ $myOpenAttendance->clock_in->format('H:i') }}</strong>
                                </div>
                                <button type="submit" class="btn btn-outline-danger btn-sm">
                                    Check Out
                                </button>
                            </div>
                        </form>
                    @endisset
                </div>
            </div>
        </div>

        {{-- FILTERS CARD --}}
        <div class="col-lg-6">
            <div class="card shadow-soft border-0 h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <h5 class="card-title mb-0">Filters</h5>
                        @if(request()->anyFilled(['date_from', 'date_to', 'employee_filter', 'status']))
                            <a href="{{ route('hr.attendance.index') }}" class="small text-decoration-none">
                                Clear
                            </a>
                        @endif
                    </div>

                    <button class="btn btn-outline-secondary btn-sm d-md-none mb-2"
                            type="button"
                            data-bs-toggle="collapse"
                            data-bs-target="#attendanceFilters">
                        Filters
                    </button>

                    <div class="collapse d-md-block" id="attendanceFilters">
                        <form method="GET" action="{{ route('hr.attendance.index') }}" class="row g-2">
                            <div class="col-sm-6 col-md-3">
                                <label class="form-label small">From</label>
                                <input type="date" name="date_from" value="{{ request('date_from') }}"
                                       class="form-control form-control-sm">
                            </div>
                            <div class="col-sm-6 col-md-3">
                                <label class="form-label small">To</label>
                                <input type="date" name="date_to" value="{{ request('date_to') }}"
                                       class="form-control form-control-sm">
                            </div>
                            <div class="col-sm-6 col-md-3">
                                <label class="form-label small">Employee</label>
                                <select name="employee_filter"
                                        class="form-select form-select-sm select2"
                                        data-placeholder="All employees">
                                    <option value="">All</option>
                                    @foreach($employees as $employee)
                                        <option value="{{ $employee->id }}"
                                            {{ request('employee_filter') == $employee->id ? 'selected' : '' }}>
                                            {{ $employee->first_name }} {{ $employee->last_name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-sm-6 col-md-3">
                                <label class="form-label small">Status</label>
                                <select name="status" class="form-select form-select-sm">
                                    <option value="">All</option>
                                    <option value="present" {{ request('status') == 'present' ? 'selected' : '' }}>Present</option>
                                    <option value="late" {{ request('status') == 'late' ? 'selected' : '' }}>Late</option>
                                    <option value="absent" {{ request('status') == 'absent' ? 'selected' : '' }}>Absent</option>
                                </select>
                            </div>

                            <div class="col-12 d-flex justify-content-end mt-2">
                                <button type="submit" class="btn btn-outline-primary btn-sm">
                                    Apply Filters
                                </button>
                            </div>
                        </form>
                    </div>

                </div>
            </div>
        </div>
    </div>

    {{-- TABLE CARD --}}
    <div class="row">
        <div class="col">
            <div class="card shadow-soft border-0">
                <div class="card-body">

                    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-3 gap-2">
                        <h5 class="card-title mb-0">Attendance Records</h5>
                        <div class="small text-muted">
                            Showing {{ $attendances->firstItem() ?? 0 }}–{{ $attendances->lastItem() ?? 0 }}
                            of {{ $attendances->total() }} records
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-sm align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th scope="col">Date</th>
                                    <th scope="col">Employee</th>
                                    <th scope="col">Check In</th>
                                    <th scope="col">Check Out</th>
                                    <th scope="col">Status</th>
                                    <th scope="col" class="text-center">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($attendances as $attendance)
                                    @php
                                        $rowClass = match ($attendance->status) {
                                            'present' => 'table-success-subtle',
                                            'late'    => 'table-warning-subtle',
                                            'absent'  => 'table-secondary-subtle',
                                            default   => ''
                                        };
                                    @endphp
                                    <tr class="{{ $rowClass }}">
                                        <td>{{ $attendance->date->format('Y-m-d') }}</td>
                                        <td>
                                            <div class="fw-semibold">
                                                {{ $attendance->employee->first_name }} {{ $attendance->employee->last_name }}
                                            </div>
                                            <div class="small text-muted">
                                                {{ $attendance->employee->department }}
                                            </div>
                                        </td>
                                        <td>{{ $attendance->clock_in ? $attendance->clock_in->format('H:i') : '-' }}</td>
                                        <td>{{ $attendance->clock_out ? $attendance->clock_out->format('H:i') : '-' }}</td>
                                        <td>
                                            <span class="badge
                                                @if($attendance->status === 'present') bg-success
                                                @elseif($attendance->status === 'late') bg-warning text-dark
                                                @elseif($attendance->status === 'absent') bg-secondary
                                                @else bg-light text-muted
                                                @endif">
                                                {{ ucfirst($attendance->status) }}
                                            </span>
                                        </td>
                                        <td class="text-center">
                                            @if(!$attendance->clock_out)
                                                <form action="{{ route('hr.attendance.check-out', $attendance->id) }}"
                                                      method="POST"
                                                      class="d-inline"
                                                      onsubmit="return confirm('Check out this employee now?');">
                                                    @csrf
                                                    <button type="submit" class="btn btn-sm btn-outline-danger">
                                                        Check Out
                                                    </button>
                                                </form>
                                            @else
                                                <span class="text-muted small">Completed</span>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center text-muted py-4">
                                            No attendance records found for the selected filters.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    {{-- PAGINATION --}}
                    <div class="mt-3 d-flex justify-content-end">
                        {{ $attendances->withQueryString()->links() }}
                    </div>

                </div>
            </div>
        </div>
    </div>

</div>
@endsection

@push('styles')
    <style>
        .table-success-subtle { background-color: rgba(25,135,84,.04); }
        .table-warning-subtle { background-color: rgba(255,193,7,.04); }
        .table-secondary-subtle { background-color: rgba(108,117,125,.04); }
    </style>
@endpush

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            if (window.jQuery && $.fn.select2) {
                $('.select2').select2({
                    width: '100%',
                    allowClear: true,
                    placeholder: function(){
                        return $(this).data('placeholder') || '';
                    }
                });
            }
        });
    </script>
@endpush
