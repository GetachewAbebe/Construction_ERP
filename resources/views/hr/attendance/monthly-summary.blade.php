<x-layouts.app-shell title="Monthly Summary">
    @php($totalPayable = $perEmployee->sum('payable_amount'))

    <div class="space-y-5">
        <div class="flex flex-wrap items-center justify-between gap-3">
            <div>
                <h2 class="text-xl font-semibold">Monthly Attendance Summary</h2>
                <p class="text-sm text-base-content/60">
                    {{ $startOfMonth->format('F Y') }}@if ($departmentFilter) · {{ $departmentFilter }}@endif
                </p>
            </div>
            <div class="flex gap-2">
                <a href="{{ route('hr.attendance.index') }}" class="btn btn-ghost btn-sm">Daily attendance</a>
                <a href="{{ route('hr.attendance.monthly-summary.export', request()->query()) }}" class="btn btn-primary btn-sm">
                    <x-mary-icon name="o-arrow-down-tray" class="h-4 w-4" /> Export CSV
                </a>
            </div>
        </div>

        <div class="grid gap-4 lg:grid-cols-3">
            {{-- Filters --}}
            <div class="rounded-xl border border-base-300 bg-base-100 p-5 shadow-sm">
                <h3 class="mb-3 font-semibold">Filters</h3>
                <form method="GET" action="{{ route('hr.attendance.monthly-summary') }}" class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="mb-1 block text-xs text-base-content/50">Year</label>
                        <select name="year" class="select select-bordered select-sm w-full">
                            @for ($y = now()->year - 3; $y <= now()->year + 1; $y++)
                                <option value="{{ $y }}" @selected((int) $year === $y)>{{ $y }}</option>
                            @endfor
                        </select>
                    </div>
                    <div>
                        <label class="mb-1 block text-xs text-base-content/50">Month</label>
                        <select name="month" class="select select-bordered select-sm w-full">
                            @for ($m = 1; $m <= 12; $m++)
                                <option value="{{ $m }}" @selected((int) $month === $m)>{{ \Carbon\Carbon::createFromDate($year, $m, 1)->format('F') }}</option>
                            @endfor
                        </select>
                    </div>
                    <div class="col-span-2">
                        <label class="mb-1 block text-xs text-base-content/50">Department</label>
                        <select name="department" class="select select-bordered select-sm w-full">
                            <option value="">All departments</option>
                            @foreach ($departments as $dept)
                                <option value="{{ $dept }}" @selected($departmentFilter === $dept)>{{ $dept }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-span-2 flex justify-between">
                        <button type="submit" class="btn btn-primary btn-sm">Apply</button>
                        <a href="{{ route('hr.attendance.monthly-summary') }}" class="btn btn-ghost btn-sm">Reset</a>
                    </div>
                </form>
            </div>

            {{-- KPIs --}}
            <div class="grid grid-cols-1 gap-4 sm:grid-cols-3 lg:col-span-2">
                @foreach ([
                    ['Employees', $totalEmployeesInScope, 'text-base-content'],
                    ['Total credits', number_format($totalCreditsInScope, 1), 'text-primary'],
                    ['Total payable (ETB)', number_format($totalPayable, 0), 'text-success'],
                ] as [$label, $value, $color])
                    <div class="rounded-lg border border-base-300 bg-base-100 px-4 py-5 shadow-sm">
                        <div class="text-[11px] font-medium uppercase tracking-wide text-base-content/50">{{ $label }}</div>
                        <div class="mt-1 text-2xl font-bold {{ $color }}">{{ $value }}</div>
                    </div>
                @endforeach
            </div>
        </div>

        {{-- Per employee --}}
        <div class="rounded-lg border border-base-300 bg-base-100 shadow-sm">
            <div class="border-b border-base-200 px-5 py-3"><h3 class="font-semibold">Details by employee</h3></div>
            <div class="overflow-x-auto">
                <table class="table table-sm">
                    <thead>
                        <tr class="text-[11px] uppercase tracking-wide text-base-content/60">
                            <th>Employee</th><th>Department</th><th class="text-center">Records</th><th class="text-center">Credits</th><th class="text-right">Payable (ETB)</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($perEmployee as $s)
                            @php($employee = $s['employee'])
                            <tr>
                                <td>
                                    <div class="font-medium">{{ $employee->first_name }} {{ $employee->last_name }}</div>
                                    <div class="text-xs text-base-content/50">#{{ $employee->id }}</div>
                                </td>
                                <td>{{ $employee->department ?? 'Unassigned' }}</td>
                                <td class="text-center">{{ $s['records'] }}</td>
                                <td class="text-center font-medium text-primary">{{ number_format($s['total_credits'], 1) }}</td>
                                <td class="text-right font-semibold">{{ number_format($s['payable_amount'], 0) }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="5" class="py-10 text-center text-base-content/40">No records for this period.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-layouts.app-shell>
