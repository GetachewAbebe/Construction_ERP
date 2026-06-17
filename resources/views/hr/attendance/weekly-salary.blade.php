<x-layouts.app-shell title="Weekly Salary">
    <div class="space-y-5">
        <div class="flex flex-wrap items-center justify-between gap-3">
            <div>
                <h2 class="text-xl font-semibold">Weekly Salary Analysis</h2>
                <p class="text-sm text-base-content/60">Attendance credits and pay projections.</p>
            </div>
            <form action="{{ route('hr.attendance.weekly-salary') }}" method="GET" class="flex items-center gap-2">
                <span class="text-xs text-base-content/50">Week start</span>
                <input type="date" name="week_start" value="{{ $weekStart->toDateString() }}" onchange="this.form.submit()" class="input input-bordered input-sm" />
            </form>
        </div>

        <div class="flex items-center gap-2 rounded-lg border border-info/30 bg-info/5 p-3 text-sm text-base-content/70">
            <x-mary-icon name="o-information-circle" class="h-5 w-5 shrink-0 text-info" />
            <span>Cycle: {{ $weekStart->format('M d, Y') }} — {{ $weekEnd->format('M d, Y') }} · Daily rate = monthly salary ÷ 22 working days.</span>
        </div>

        <div class="rounded-lg border border-base-300 bg-base-100 shadow-sm">
            <div class="overflow-x-auto">
                <table class="table">
                    <thead>
                        <tr class="text-[11px] uppercase tracking-wide text-base-content/60">
                            <th>Associate</th><th class="text-center">Credits</th><th class="text-center">Daily yield</th><th class="text-center">Projected pay</th><th class="text-right">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($analysis as $row)
                            @php($employee = $row['employee'])
                            <tr class="hover:bg-base-200/40">
                                <td>
                                    <div class="flex items-center gap-3">
                                        <div class="grid h-9 w-9 place-items-center rounded-lg bg-primary/15 text-xs font-semibold text-primary">{{ strtoupper(mb_substr($employee->first_name, 0, 1)) }}</div>
                                        <div>
                                            <div class="text-sm font-medium">{{ $employee->full_name ?? $employee->name }}</div>
                                            <div class="text-xs text-base-content/50">{{ $employee->position }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="text-center">
                                    <div class="text-lg font-bold text-primary">{{ number_format($row['credits'], 1) }}</div>
                                    <div class="text-xs text-base-content/40 uppercase">sessions</div>
                                </td>
                                <td class="text-center text-sm">{{ number_format($row['daily_rate'], 2) }} ETB</td>
                                <td class="text-center"><span class="badge badge-ghost badge-lg">{{ number_format($row['payable_amount'], 2) }} ETB</span></td>
                                <td class="text-right">
                                    @if ($row['credits'] >= 5)
                                        <span class="badge badge-success badge-sm">Full</span>
                                    @elseif ($row['credits'] > 0)
                                        <span class="badge badge-warning badge-sm">Partial</span>
                                    @else
                                        <span class="badge badge-error badge-sm">Inactive</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-layouts.app-shell>
