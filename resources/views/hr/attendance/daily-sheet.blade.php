@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-800 text-erp-deep mb-1">Standard Daily Attendance</h2>
            <p class="text-muted">Register morning check-ins and evening check-outs with a single tap.</p>
        </div>
        <div>
            <form action="{{ route('hr.attendance.daily-sheet') }}" method="GET" class="d-flex gap-2">
                <input type="date" name="date" class="form-control glass-input" value="{{ $date->toDateString() }}" onchange="this.form.submit()">
            </form>
        </div>
    </div>

    <div class="card glass-card border-0 shadow-sm overflow-hidden">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light">
                    <tr>
                        <th class="ps-4" style="width: 300px;">Employee</th>
                        <th class="text-center">Morning (Check-In)</th>
                        <th class="text-center">Evening (Check-Out)</th>
                    </tr>
                </thead>
                <tbody>
                    @php $isToday = $date->isToday(); @endphp
                    @foreach($employees as $employee)
                        @php
                            $att = $attendances->get($employee->id);
                            $morning = $att ? $att->morning_status : 'absent';
                            $afternoon = $att ? $att->afternoon_status : 'absent';
                            $mClock = $att && $att->clock_in ? $att->clock_in->format('H:i') : null;
                            $eClock = $att && $att->clock_out ? $att->clock_out->format('H:i') : null;
                        @endphp
                        <tr data-employee="{{ $employee->id }}" data-date="{{ $date->toDateString() }}" class="{{ $isToday ? '' : 'opacity-75 bg-light' }}">
                            <td class="ps-4">
                                <div class="d-flex align-items-center">
                                    <div class="avatar avatar-sm rounded-circle me-3 bg-primary-soft text-primary d-flex align-items-center justify-content-center fw-bold">
                                        {{ substr($employee->first_name, 0, 1) }}
                                    </div>
                                    <div>
                                        <div class="fw-bold text-erp-deep">{{ $employee->full_name }}</div>
                                        <div class="small text-muted">{{ $employee->department }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="text-center">
                                <div class="session-container morning-session d-flex flex-column align-items-center gap-1">
                                    <span class="clock-display x-small fw-bold text-primary">{{ $mClock ?? '--:--' }}</span>
                                    <button type="button" 
                                        class="btn btn-action w-75 fw-bold {{ in_array($morning, ['present', 'late']) ? 'btn-success' : 'btn-outline-secondary' }}"
                                        style="font-size: 0.8rem; height: 40px;"
                                        {{ $isToday ? '' : 'disabled' }}
                                        onclick="toggleAttendance(this, 'morning', 'check-in')">
                                        {{ $morning == 'late' ? 'LATE' : 'CHECK IN' }}
                                    </button>
                                </div>
                            </td>
                            <td class="text-center">
                                <div class="session-container afternoon-session d-flex flex-column align-items-center gap-1">
                                    <span class="clock-display x-small fw-bold text-primary">{{ $eClock ?? '--:--' }}</span>
                                    <button type="button" 
                                        class="btn btn-action w-75 fw-bold {{ $afternoon == 'present' ? 'btn-success' : 'btn-outline-secondary' }}"
                                        style="font-size: 0.8rem; height: 40px;"
                                        {{ $isToday ? '' : 'disabled' }}
                                        onclick="toggleAttendance(this, 'afternoon', 'check-out')">
                                        CHECK OUT
                                    </button>
                                </div>
                            </td>
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
    const row = btn.closest('tr');
    const employeeId = row.dataset.employee;
    const date = row.dataset.date;
    const container = btn.closest('.session-container');
    const clockDisplay = container.querySelector('.clock-display');

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
                btn.className = (data.status === 'present' || data.status === 'late') ? 'btn btn-action w-75 fw-bold btn-success' : 'btn btn-action w-75 fw-bold btn-outline-secondary';
            } else {
                btn.innerText = 'CHECK OUT';
                btn.className = data.status === 'present' ? 'btn btn-action w-75 fw-bold btn-success' : 'btn btn-action w-75 fw-bold btn-outline-secondary';
            }
            clockDisplay.innerText = data.time || '--:--';
        } else {
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
    .x-small { font-size: 0.75rem; }
    .glass-input { background: rgba(255,255,255,0.7); backdrop-filter: blur(5px); border: 1px solid rgba(0,0,0,0.05); }
    .clock-display { font-family: 'Courier New', Courier, monospace; }
</style>
@endsection
