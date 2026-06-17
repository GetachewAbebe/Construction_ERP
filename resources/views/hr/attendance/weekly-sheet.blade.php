<x-layouts.app-shell title="Weekly Attendance">
    @php $days = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday']; @endphp

    <div class="space-y-5">
        <div class="flex flex-wrap items-center justify-between gap-3">
            <div>
                <h2 class="text-xl font-semibold">Weekly Workforce Presence</h2>
                <p class="text-sm text-base-content/60">Check-in/out management, Monday–Saturday.</p>
            </div>
            <form action="{{ route('hr.attendance.weekly-sheet') }}" method="GET">
                <input type="date" name="date" value="{{ $date->toDateString() }}" onchange="this.form.submit()" class="input input-bordered input-sm" />
            </form>
        </div>

        <div class="rounded-lg border border-base-300 bg-base-100 shadow-sm">
            <div class="overflow-x-auto">
                <table class="table border-separate border-spacing-0">
                    <thead>
                        <tr class="text-[11px] uppercase tracking-wide text-base-content/60">
                            <th class="min-w-44">Employee</th>
                            @foreach ($days as $index => $day)
                                @php $currentDay = (clone $monday)->addDays($index); @endphp
                                <th class="min-w-36 text-center">
                                    <div class="font-semibold">{{ $day }}</div>
                                    <div class="text-base-content/40">{{ $currentDay->format('M d') }}</div>
                                </th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($employees as $employee)
                            <tr>
                                <td class="align-top">
                                    <div class="text-sm font-medium">{{ $employee->full_name ?? $employee->name }}</div>
                                    <div class="text-xs text-base-content/50">{{ $employee->department }}</div>
                                </td>
                                @foreach ($days as $index => $day)
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
                                    <td class="align-top {{ $isToday ? '' : 'opacity-60' }}" data-employee="{{ $employee->id }}" data-date="{{ $currentDayStr }}">
                                        <div class="flex flex-col gap-2">
                                            <div class="session-box">
                                                <div class="mb-1 flex justify-between px-0.5 text-[10px] font-semibold"><span class="text-base-content/40">IN</span><span class="clock-display font-mono text-primary">{{ $mClock ?? '--:--' }}</span></div>
                                                <button type="button" {{ $isToday ? '' : 'disabled' }}
                                                    class="btn btn-xs w-full {{ in_array($morning, ['present', 'late']) ? 'btn-success' : 'btn-outline' }}"
                                                    onclick="toggleAttendance(this, 'morning', 'check-in')">{{ $morning == 'late' ? 'LATE' : 'IN' }}</button>
                                            </div>
                                            <div class="session-box">
                                                <div class="mb-1 flex justify-between px-0.5 text-[10px] font-semibold"><span class="text-base-content/40">OUT</span><span class="clock-display font-mono text-primary">{{ $eClock ?? '--:--' }}</span></div>
                                                <button type="button" {{ $isToday ? '' : 'disabled' }}
                                                    class="btn btn-xs w-full {{ $evening == 'present' ? 'btn-success' : 'btn-outline' }}"
                                                    onclick="toggleAttendance(this, 'afternoon', 'check-out')">OUT</button>
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

    <div id="toast-container" class="fixed bottom-4 right-4 z-50 flex flex-col gap-2"></div>

    @push('scripts')
    <script>
        async function toggleAttendance(btn, session, action) {
            const cell = btn.closest('td');
            const box = btn.closest('.session-box');
            const clock = box.querySelector('.clock-display');
            const original = btn.innerText;
            btn.disabled = true;
            btn.innerHTML = '<span class="loading loading-spinner loading-xs"></span>';
            try {
                const res = await fetch('{{ route('hr.attendance.toggle') }}', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                    body: JSON.stringify({ employee_id: cell.dataset.employee, date: cell.dataset.date, session, action }),
                });
                const data = await res.json();
                if (data.success) {
                    const on = session === 'morning' ? ['present', 'late'].includes(data.status) : data.status === 'present';
                    btn.className = 'btn btn-xs w-full ' + (on ? 'btn-success' : 'btn-outline');
                    btn.innerText = session === 'morning' ? (data.status === 'late' ? 'LATE' : 'IN') : 'OUT';
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
