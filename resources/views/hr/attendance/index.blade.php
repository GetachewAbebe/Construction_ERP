<x-layouts.app-shell title="Attendance">
    @php
        $sessionBadge = ['present' => 'badge-success', 'late' => 'badge-warning', 'leave' => 'badge-info', 'absent' => 'badge-error'];
    @endphp

    <div class="space-y-5">
        <div class="flex flex-wrap items-center justify-between gap-3">
            <div>
                <h2 class="text-xl font-semibold">Attendance Registry</h2>
                <p class="text-sm text-base-content/60">Daily presence and session tracking.</p>
            </div>
            <div class="flex gap-2">
                <a href="{{ route('hr.attendance.daily-sheet') }}" class="btn btn-primary btn-sm"><x-mary-icon name="o-clipboard-document-check" class="h-4 w-4" /> Daily sheet</a>
                <a href="{{ route('hr.attendance.weekly-salary') }}" class="btn btn-outline btn-sm"><x-mary-icon name="o-banknotes" class="h-4 w-4" /> Salary</a>
            </div>
        </div>

        @if (session('success'))
            <div role="alert" class="alert alert-success py-2 text-sm"><span>{{ session('success') }}</span></div>
        @endif
        @if (session('error'))
            <div role="alert" class="alert alert-error py-2 text-sm"><span>{{ session('error') }}</span></div>
        @endif

        {{-- KPIs --}}
        <div class="grid grid-cols-3 gap-4">
            <div class="rounded-lg border border-base-300 border-l-4 border-l-primary bg-base-100 px-4 py-3 shadow-sm">
                <div class="text-[11px] font-medium uppercase tracking-wide text-base-content/50">Morning present</div>
                <div class="mt-1 text-2xl font-bold text-primary">{{ $todayStats['morning_present'] ?? 0 }} <span class="text-sm font-normal text-base-content/40">/ {{ $employees->count() }}</span></div>
            </div>
            <div class="rounded-lg border border-base-300 border-l-4 border-l-info bg-base-100 px-4 py-3 shadow-sm">
                <div class="text-[11px] font-medium uppercase tracking-wide text-base-content/50">Afternoon present</div>
                <div class="mt-1 text-2xl font-bold text-info">{{ $todayStats['afternoon_present'] ?? 0 }} <span class="text-sm font-normal text-base-content/40">/ {{ $employees->count() }}</span></div>
            </div>
            <div class="rounded-lg border border-base-300 border-l-4 border-l-base-content/30 bg-base-100 px-4 py-3 shadow-sm">
                <div class="text-[11px] font-medium uppercase tracking-wide text-base-content/50">Workforce</div>
                <div class="mt-1 text-2xl font-bold">{{ $employees->count() }}</div>
            </div>
        </div>

        <div class="grid gap-4 lg:grid-cols-5">
            {{-- Check-in --}}
            <div class="rounded-xl border border-base-300 bg-base-100 p-5 shadow-sm lg:col-span-2">
                <div class="mb-4 flex items-center gap-3">
                    <div class="grid h-10 w-10 place-items-center rounded-lg bg-primary/10 text-primary"><x-mary-icon name="o-finger-print" class="h-5 w-5" /></div>
                    <div>
                        <h3 class="font-semibold">Access Portal</h3>
                        <p class="text-xs text-base-content/50">{{ now()->format('l, jS F Y') }}</p>
                    </div>
                </div>
                <form action="{{ route('hr.attendance.check-in') }}" method="POST" class="space-y-3">
                    @csrf
                    @can('manage-attendance')
                        <select name="employee_id" class="select select-bordered w-full">
                            <option value="">Identify associate…</option>
                            @foreach ($employees as $employee)
                                <option value="{{ $employee->id }}">{{ $employee->name }} — {{ $employee->position ?? 'Staff' }}</option>
                            @endforeach
                        </select>
                    @else
                        <div class="rounded-lg border border-base-200 bg-base-200/40 p-3 text-sm font-medium">{{ auth()->user()->employee->name ?? 'Your profile' }}</div>
                    @endcan
                    <button type="submit" class="btn btn-primary w-full">Confirm check-in</button>
                </form>
                @isset($myOpenAttendance)
                    <div class="mt-4 flex items-center justify-between rounded-lg border border-warning/30 bg-warning/5 p-3">
                        <div>
                            <div class="text-xs font-semibold uppercase text-warning">Active session</div>
                            <div class="text-sm font-medium">In at {{ $myOpenAttendance->clock_in->format('H:i') }}</div>
                        </div>
                        <form action="{{ route('hr.attendance.check-out', $myOpenAttendance->id) }}" method="POST">
                            @csrf <button type="submit" class="btn btn-warning btn-sm">Check out</button>
                        </form>
                    </div>
                @endisset
            </div>

            {{-- Filters --}}
            <div class="rounded-xl border border-base-300 bg-base-100 p-5 shadow-sm lg:col-span-3">
                <h3 class="mb-3 font-semibold">Filters</h3>
                <form method="GET" action="{{ route('hr.attendance.index') }}" class="grid gap-3 sm:grid-cols-2">
                    <div>
                        <label class="mb-1 block text-xs text-base-content/50">From</label>
                        <input type="date" name="date_from" value="{{ request('date_from') }}" class="input input-bordered input-sm w-full" />
                    </div>
                    <div>
                        <label class="mb-1 block text-xs text-base-content/50">To</label>
                        <input type="date" name="date_to" value="{{ request('date_to') }}" class="input input-bordered input-sm w-full" />
                    </div>
                    <div>
                        <label class="mb-1 block text-xs text-base-content/50">Associate</label>
                        <select name="employee_filter" class="select select-bordered select-sm w-full">
                            <option value="">All workforce</option>
                            @foreach ($employees as $employee)
                                <option value="{{ $employee->id }}" @selected(request('employee_filter') == $employee->id)>{{ $employee->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="mb-1 block text-xs text-base-content/50">Status</label>
                        <select name="status" class="select select-bordered select-sm w-full">
                            <option value="">Any</option>
                            <option value="present" @selected(request('status') == 'present')>Present</option>
                            <option value="late" @selected(request('status') == 'late')>Late</option>
                            <option value="absent" @selected(request('status') == 'absent')>Absent</option>
                        </select>
                    </div>
                    <div class="flex justify-end gap-2 sm:col-span-2">
                        <a href="{{ route('hr.attendance.index') }}" class="btn btn-ghost btn-sm">Reset</a>
                        <button type="submit" class="btn btn-primary btn-sm">Apply</button>
                    </div>
                </form>
            </div>
        </div>

        {{-- Ledger --}}
        <div class="rounded-lg border border-base-300 bg-base-100 shadow-sm">
            <div class="overflow-x-auto">
                <table class="table">
                    <thead>
                        <tr class="text-[11px] uppercase tracking-wide text-base-content/60">
                            <th>Date</th><th>Personnel</th><th class="text-center">Morning</th><th class="text-center">Afternoon</th><th class="text-center">Credit</th><th class="text-center">Clock</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($attendances as $at)
                            <tr class="hover:bg-base-200/40">
                                <td class="whitespace-nowrap font-medium">{{ $at->date->format('M d, Y') }}</td>
                                <td>
                                    <div class="text-sm font-medium">{{ $at->employee->name ?? '—' }}</div>
                                    <div class="text-xs text-base-content/50">{{ $at->employee->department ?? 'General' }}</div>
                                </td>
                                <td class="text-center"><span class="badge badge-sm {{ $sessionBadge[$at->morning_status] ?? 'badge-ghost' }}">{{ strtoupper($at->morning_status ?? '—') }}</span></td>
                                <td class="text-center"><span class="badge badge-sm {{ $sessionBadge[$at->afternoon_status] ?? 'badge-ghost' }}">{{ strtoupper($at->afternoon_status ?? '—') }}</span></td>
                                <td class="text-center font-semibold text-primary">{{ number_format($at->total_credit, 1) }}</td>
                                <td class="text-center text-xs text-base-content/60">
                                    <div>IN {{ $at->clock_in ? $at->clock_in->format('H:i') : '—' }}</div>
                                    <div>OUT {{ $at->clock_out ? $at->clock_out->format('H:i') : '—' }}</div>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="6" class="py-12 text-center text-base-content/40">No presence data for this range.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if ($attendances->hasPages())
                <div class="border-t border-base-200 p-4">{{ $attendances->withQueryString()->links() }}</div>
            @endif
        </div>
    </div>
</x-layouts.app-shell>
