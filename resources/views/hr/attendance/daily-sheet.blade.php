<x-layouts.app-shell title="Daily Attendance">
    <div class="space-y-5">
        <div class="flex flex-wrap items-center justify-between gap-3">
            <div>
                <h2 class="text-xl font-semibold">Daily Attendance Sheet</h2>
                <p class="text-sm text-base-content/60">Register morning check-ins and evening check-outs.</p>
            </div>
            <form action="{{ route('hr.attendance.daily-sheet') }}" method="GET">
                <input type="date" name="date" value="{{ $date->toDateString() }}" onchange="this.form.submit()" class="input input-bordered input-sm" />
            </form>
        </div>

        @if (session('success'))
            <div role="alert" class="alert alert-success py-2 text-sm"><span>{{ session('success') }}</span></div>
        @endif

        <div class="rounded-lg border border-base-300 bg-base-100 shadow-sm">
            <div class="overflow-x-auto">
                <table class="table">
                    <thead>
                        <tr class="text-[11px] uppercase tracking-wide text-base-content/60">
                            <th>Employee</th><th class="text-center">Morning (check-in)</th><th class="text-center">Evening (check-out)</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php $isToday = $date->isToday(); @endphp
                        @foreach ($employees as $employee)
                            @php
                                $att = $attendances->get($employee->id);
                                $morning = $att ? $att->morning_status : 'absent';
                                $afternoon = $att ? $att->afternoon_status : 'absent';
                                $mClock = $att && $att->clock_in ? $att->clock_in->format('H:i') : null;
                                $eClock = $att && $att->clock_out ? $att->clock_out->format('H:i') : null;
                            @endphp
                            <tr data-employee="{{ $employee->id }}" data-date="{{ $date->toDateString() }}" class="{{ $isToday ? '' : 'opacity-70' }}">
                                <td>
                                    <div class="flex items-center gap-3">
                                        <div class="grid h-8 w-8 place-items-center rounded-full bg-primary/15 text-xs font-semibold text-primary">{{ strtoupper(mb_substr($employee->first_name, 0, 1)) }}</div>
                                        <div>
                                            <div class="text-sm font-medium">{{ $employee->full_name ?? $employee->name }}</div>
                                            <div class="text-xs text-base-content/50">{{ $employee->department }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="text-center">
                                    <div class="session-container flex flex-col items-center gap-1">
                                        <span class="clock-display font-mono text-xs text-primary">{{ $mClock ?? '--:--' }}</span>
                                        <button type="button" {{ $isToday ? '' : 'disabled' }}
                                            class="btn btn-sm w-28 {{ in_array($morning, ['present', 'late']) ? 'btn-success' : 'btn-outline' }}"
                                            onclick="toggleAttendance(this, 'morning', 'check-in')">
                                            {{ $morning == 'late' ? 'LATE' : 'CHECK IN' }}
                                        </button>
                                    </div>
                                </td>
                                <td class="text-center">
                                    <div class="session-container flex flex-col items-center gap-1">
                                        <span class="clock-display font-mono text-xs text-primary">{{ $eClock ?? '--:--' }}</span>
                                        <button type="button" {{ $isToday ? '' : 'disabled' }}
                                            class="btn btn-sm w-28 {{ $afternoon == 'present' ? 'btn-success' : 'btn-outline' }}"
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

    <div id="toast-container" class="fixed bottom-4 right-4 z-50 flex flex-col gap-2"></div>

    @push('scripts')
    <script>
        async function toggleAttendance(btn, session, action) {
            const row = btn.closest('tr');
            const container = btn.closest('.session-container');
            const clock = container.querySelector('.clock-display');
            const original = btn.innerText;
            btn.disabled = true;
            btn.innerHTML = '<span class="loading loading-spinner loading-xs"></span>';
            try {
                const res = await fetch('{{ route('hr.attendance.toggle') }}', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                    body: JSON.stringify({ employee_id: row.dataset.employee, date: row.dataset.date, session, action }),
                });
                const data = await res.json();
                if (data.success) {
                    const on = session === 'morning' ? ['present', 'late'].includes(data.status) : data.status === 'present';
                    btn.className = 'btn btn-sm w-28 ' + (on ? 'btn-success' : 'btn-outline');
                    btn.innerText = session === 'morning' ? (data.status === 'late' ? 'LATE' : 'CHECK IN') : 'CHECK OUT';
                    clock.innerText = data.time || '--:--';
                } else {
                    btn.innerText = original; showToast(data.message || 'Update failed', 'alert-error');
                }
            } catch (e) {
                btn.innerText = original; showToast('Network error', 'alert-error');
            } finally {
                btn.disabled = false;
            }
        }
        function showToast(message, cls) {
            const c = document.getElementById('toast-container');
            const t = document.createElement('div');
            t.className = 'alert ' + cls + ' py-2 text-sm shadow-lg';
            t.innerHTML = '<span>' + message + '</span>';
            c.appendChild(t);
            setTimeout(() => t.remove(), 4000);
        }
    </script>
    @endpush
</x-layouts.app-shell>
