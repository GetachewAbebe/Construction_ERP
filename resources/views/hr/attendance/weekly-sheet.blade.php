@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-800 text-erp-deep mb-1">Weekly Workforce Presence</h2>
            <p class="text-muted">Direct Check-In/Out management for Monday through Saturday.</p>
        </div>
        <div>
            <form action="{{ route('hr.attendance.weekly-sheet') }}" method="GET" class="d-flex gap-2">
                <input type="date" name="date" class="form-control glass-input" value="{{ $date->toDateString() }}" onchange="this.form.submit()">
            </form>
        </div>
    </div>

    <div class="card glass-card border-0 shadow-sm overflow-hidden">
        <div class="table-responsive">
            <table class="table table-bordered align-middle mb-0 text-center">
                <thead class="bg-light">
                    <tr>
                        <th class="text-start ps-4" style="min-width: 200px;">Employee</th>
                        @php
                            $days = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];
                        @endphp
                        @foreach($days as $index => $day)
                            @php $currentDay = (clone $monday)->addDays($index); @endphp
                            <th style="min-width: 140px;">
                                <div class="fw-bold">{{ $day }}</div>
                                <div class="small text-muted">{{ $currentDay->format('M d') }}</div>
                            </th>
                        @endforeach
                    </tr>
                </thead>
                <tbody>
                    @foreach($employees as $employee)
                        <tr>
                            <td class="text-start ps-4">
                                <div class="fw-bold text-erp-deep small">{{ $employee->full_name }}</div>
                                <div class="x-small text-muted">{{ $employee->department }}</div>
                            </td>
                            @foreach($days as $index => $day)
                                @php 
                                    $currentDayStr = (clone $monday)->addDays($index)->toDateString();
                                    $dayAttColl = $attendances->get($employee->id)?->get($currentDayStr);
                                    $att = $dayAttColl ? $dayAttColl->first() : null;
                                    
                                    $morning = $att ? $att->morning_status : 'absent';
                                    $evening = $att ? $att->afternoon_status : 'absent';
                                    $mClock = $att && $att->clock_in ? $att->clock_in->format('H:i') : null;
                                    $eClock = $att && $att->clock_out ? $att->clock_out->format('H:i') : null;
                                    $isToday = $currentDayStr === \Carbon\Carbon::today()->toDateString();
                                @endphp
                                <td class="p-2 {{ $isToday ? 'bg-white' : 'bg-light bg-opacity-50' }}" data-employee="{{ $employee->id }}" data-date="{{ $currentDayStr }}">
                                    <div class="d-flex flex-column gap-2 opacity-{{ $isToday ? '100' : '75' }}">
                                        <!-- Morning Button -->
                                        <div class="session-box">
                                            <div class="d-flex justify-content-between px-1 mb-1">
                                                <span class="x-small fw-bold text-muted">IN</span>
                                                <span class="clock-display x-small fw-bold text-primary">{{ $mClock ?? '--:--' }}</span>
                                            </div>
                                            <button type="button" 
                                                class="btn btn-sm w-100 fw-bold {{ in_array($morning, ['present', 'late']) ? 'btn-success' : 'btn-outline-secondary' }}"
                                                style="font-size: 0.7rem;"
                                                {{ $isToday ? '' : 'disabled' }}
                                                onclick="toggleAttendance(this, 'morning', 'check-in')">
                                                {{ $morning == 'late' ? 'LATE' : 'CHECK IN' }}
                                            </button>
                                        </div>

                                        <!-- Evening Button -->
                                        <div class="session-box">
                                            <div class="d-flex justify-content-between px-1 mb-1">
                                                <span class="x-small fw-bold text-muted">OUT</span>
                                                <span class="clock-display x-small fw-bold text-primary">{{ $eClock ?? '--:--' }}</span>
                                            </div>
                                            <button type="button" 
                                                class="btn btn-sm w-100 fw-bold {{ $evening == 'present' ? 'btn-success' : 'btn-outline-secondary' }}"
                                                style="font-size: 0.7rem;"
                                                {{ $isToday ? '' : 'disabled' }}
                                                onclick="toggleAttendance(this, 'afternoon', 'check-out')">
                                                CHECK OUT
                                            </button>
                                        </div>
                                    </div>
                                </td>
                            @endforeach
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="toast-container position-fixed bottom-0 end-0 p-3" id="toast-container"></div>

<script>
async function toggleAttendance(btn, session, action) {
    const cell = btn.closest('td');
    const employeeId = cell.dataset.employee;
    const date = cell.dataset.date;
    const box = btn.closest('.session-box');
    const clockDisplay = box.querySelector('.clock-display');

    btn.disabled = true;
    const originalText = btn.innerText;
    btn.innerHTML = '<span class="spinner-border spinner-border-sm" role="status"></span>';

    try {
        const response = await fetch('{{ route('hr.attendance.toggle') }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({
                employee_id: employeeId,
                date: date,
                session: session,
                action: action
            })
        });

        const data = await response.json();

        if (data.success) {
            if (session === 'morning') {
                btn.innerText = data.status === 'late' ? 'LATE' : 'CHECK IN';
                btn.className = (data.status === 'present' || data.status === 'late') ? 'btn btn-sm w-100 fw-bold btn-success' : 'btn btn-sm w-100 fw-bold btn-outline-secondary';
            } else {
                btn.innerText = 'CHECK OUT';
                btn.className = data.status === 'present' ? 'btn btn-sm w-100 fw-bold btn-success' : 'btn btn-sm w-100 fw-bold btn-outline-secondary';
            }
            clockDisplay.innerText = data.time || '--:--';
        } else {
            // Show server-side error message (e.g., date lock)
            showToast(data.message || "Update Failed", "bg-danger");
        }
    } catch (err) {
        showToast("Network Error", "bg-danger");
    } finally {
        btn.disabled = false;
        if (btn.innerHTML.includes('spinner-border')) {
             btn.innerText = originalText;
        }
    }
}

function showToast(message, bgColor) {
    const container = document.getElementById('toast-container');
    const toastDiv = document.createElement('div');
    toastDiv.className = `toast align-items-center text-white ${bgColor} border-0 shadow-lg mb-2`;
    toastDiv.setAttribute('role', 'alert');
    toastDiv.setAttribute('aria-live', 'assertive');
    toastDiv.setAttribute('aria-atomic', 'true');
    
    toastDiv.innerHTML = `
        <div class="d-flex">
            <div class="toast-body fw-bold">${message}</div>
            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
        </div>
    `;
    
    container.appendChild(toastDiv);
    const toast = new bootstrap.Toast(toastDiv, { delay: 4000 });
    toast.show();
    
    toastDiv.addEventListener('hidden.bs.toast', () => toastDiv.remove());
}
</script>

<style>
    .x-small { font-size: 0.6rem; }
    .glass-input { background: rgba(255,255,255,0.7); backdrop-filter: blur(5px); border: 1px solid rgba(0,0,0,0.05); }
    th { vertical-align: middle !important; background: #f8f9fa !important; border-bottom: 2px solid #ddd !important; }
    .clock-display { font-family: 'Courier New', Courier, monospace; }
</style>
@endsection
