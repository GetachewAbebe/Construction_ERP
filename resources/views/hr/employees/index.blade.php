<x-layouts.app-shell title="Employees">
    @php
        $canManage = Auth::user()->hasAnyRole(['Administrator', 'Admin', 'Human Resource Manager', 'HumanResourceManager']);
        $isAdmin = Auth::user()->hasAnyRole(['Administrator', 'Admin']);
        $statusBadge = [
            'Active' => 'badge-success', 'On Leave' => 'badge-warning',
            'Terminated' => 'badge-error', 'Resigned' => 'badge-ghost',
        ];
    @endphp

    <div class="space-y-5">
        <div class="flex flex-wrap items-center justify-between gap-3">
            <div>
                <h2 class="text-xl font-semibold">Employee Directory</h2>
                <p class="text-sm text-base-content/60">Workforce records and onboarding.</p>
            </div>
            @if ($canManage)
                <a href="{{ route('hr.employees.create') }}" class="btn btn-primary btn-sm">
                    <x-mary-icon name="o-user-plus" class="h-4 w-4" /> Add employee
                </a>
            @endif
        </div>

        @if (session('success'))
            <div role="alert" class="alert alert-success py-2 text-sm"><span>{{ session('success') }}</span></div>
        @endif
        @if (session('error'))
            <div role="alert" class="alert alert-error py-2 text-sm"><span>{{ session('error') }}</span></div>
        @endif

        <div class="rounded-lg border border-base-300 bg-base-100 shadow-sm">
            <form action="{{ route('hr.employees.index') }}" method="GET" class="flex flex-col gap-3 border-b border-base-200 p-4 sm:flex-row sm:items-center">
                <label class="input input-bordered input-sm flex flex-1 items-center gap-2">
                    <x-mary-icon name="o-magnifying-glass" class="h-4 w-4 opacity-40" />
                    <input type="text" name="q" value="{{ request('q') }}" placeholder="Name, email or unit…" class="grow" />
                </label>
                <select name="status" class="select select-bordered select-sm w-full sm:w-48" onchange="this.form.submit()">
                    <option value="">All personnel</option>
                    @foreach (['Active', 'On Leave', 'Terminated', 'Resigned'] as $s)
                        <option value="{{ $s }}" @selected(request('status') === $s)>{{ $s }}</option>
                    @endforeach
                </select>
                <button type="submit" class="btn btn-primary btn-sm">Filter</button>
            </form>

            <div class="overflow-x-auto">
                <table class="table">
                    <thead>
                        <tr class="text-[11px] uppercase tracking-wide text-base-content/60">
                            <th>Employee</th><th>Position</th><th>Status</th><th>Contact</th><th class="text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($employees as $e)
                            <tr class="hover:bg-base-200/40">
                                <td>
                                    <div class="flex items-center gap-3">
                                        <div class="grid h-9 w-9 shrink-0 place-items-center overflow-hidden rounded-full bg-primary/15 text-xs font-semibold text-primary">
                                            @if ($e->profile_picture_url)
                                                <img src="{{ $e->profile_picture_url }}" class="h-full w-full object-cover" alt="">
                                            @else
                                                {{ strtoupper(mb_substr($e->first_name, 0, 1) . mb_substr($e->last_name, 0, 1)) }}
                                            @endif
                                        </div>
                                        <div>
                                            <div class="font-medium leading-tight">{{ $e->first_name }} {{ $e->last_name }}</div>
                                            <div class="text-xs text-base-content/50">#EMP-{{ str_pad((string) $e->id, 4, '0', STR_PAD_LEFT) }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div class="text-sm">{{ $e->position ?? 'Unassigned' }}</div>
                                    <div class="text-xs text-base-content/50">{{ $e->department ?? 'General' }}</div>
                                </td>
                                <td><span class="badge badge-sm {{ $statusBadge[$e->status] ?? 'badge-ghost' }}">{{ $e->status ?? 'Unknown' }}</span></td>
                                <td>
                                    <div class="text-sm">{{ $e->email }}</div>
                                    <div class="text-xs text-base-content/50">{{ $e->phone ?? '—' }}</div>
                                </td>
                                <td>
                                    <div class="flex justify-end gap-2">
                                        @if ($isAdmin)
                                            @if (! $e->user_id)
                                                <a href="{{ route('admin.users.create', ['employee_id' => $e->id]) }}" class="btn btn-outline btn-xs">Grant access</a>
                                            @else
                                                <span class="badge badge-ghost badge-sm">Has access</span>
                                            @endif
                                        @endif
                                        @if ($canManage)
                                            <a href="{{ route('hr.employees.edit', $e) }}" class="btn btn-ghost btn-xs">Edit</a>
                                            <form action="{{ route('hr.employees.destroy', $e) }}" method="POST" onsubmit="return confirm('Remove {{ $e->first_name }} {{ $e->last_name }}?')">
                                                @csrf @method('DELETE')
                                                <button type="submit" class="btn btn-ghost btn-xs text-error">Delete</button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="5" class="py-10 text-center text-base-content/40">No employees found.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if ($employees->hasPages())
                <div class="border-t border-base-200 p-4">{{ $employees->withQueryString()->links() }}</div>
            @endif
        </div>
    </div>
</x-layouts.app-shell>
