<x-layouts.app-shell title="Users">
    @php
        $roleBadge = [
            'Administrator' => 'badge-error',
            'HumanResourceManager' => 'badge-info',
            'InventoryManager' => 'badge-success',
            'FinancialManager' => 'badge-warning',
        ];
        $statusDot = ['Active' => 'bg-success', 'Inactive' => 'bg-base-content/30', 'Suspended' => 'bg-error'];
    @endphp

    <div class="space-y-5">
        {{-- Heading --}}
        <div class="flex flex-wrap items-center justify-between gap-3">
            <div>
                <h2 class="text-xl font-semibold">User Management</h2>
                <p class="text-sm text-base-content/60">Manage system users, credentials and roles.</p>
            </div>
            <a href="{{ route('admin.users.create') }}" class="btn btn-primary btn-sm">
                <x-mary-icon name="o-plus" class="h-4 w-4" /> Add user
            </a>
        </div>

        @if (session('success'))
            <div role="alert" class="alert alert-success py-2 text-sm"><span>{{ session('success') }}</span></div>
        @endif
        @if (session('error'))
            <div role="alert" class="alert alert-error py-2 text-sm"><span>{{ session('error') }}</span></div>
        @endif

        <div class="rounded-lg border border-base-300 bg-base-100 shadow-sm">
            {{-- Search --}}
            <form action="{{ route('admin.users.index') }}" method="GET"
                  class="flex flex-col gap-3 border-b border-base-200 p-4 sm:flex-row sm:items-center sm:justify-between">
                <label class="input input-bordered input-sm flex w-full items-center gap-2 sm:w-80">
                    <x-mary-icon name="o-magnifying-glass" class="h-4 w-4 opacity-40" />
                    <input type="text" name="q" value="{{ $q ?? '' }}" placeholder="Search name or email…" class="grow" />
                </label>
                <div class="flex items-center gap-2">
                    @if ($q ?? false)
                        <a href="{{ route('admin.users.index') }}" class="btn btn-ghost btn-sm">Reset</a>
                    @endif
                    <button type="submit" class="btn btn-primary btn-sm">Search</button>
                </div>
            </form>

            {{-- Table --}}
            <div class="overflow-x-auto">
                <table class="table">
                    <thead>
                        <tr class="text-[11px] uppercase tracking-wide text-base-content/60">
                            <th>User</th><th>Email</th><th>Role</th><th>Status</th><th class="text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($users as $user)
                            @php($initials = strtoupper(mb_substr($user->first_name ?? '', 0, 1) . mb_substr($user->last_name ?? '', 0, 1)))
                            <tr class="hover:bg-base-200/40">
                                <td>
                                    <div class="flex items-center gap-3">
                                        <div class="grid h-9 w-9 shrink-0 place-items-center overflow-hidden rounded-full bg-primary/15 text-xs font-semibold text-primary">
                                            @if (optional($user->employee)->profile_picture_url)
                                                <img src="{{ $user->employee->profile_picture_url }}" class="h-full w-full object-cover" alt="">
                                            @else
                                                {{ $initials ?: '—' }}
                                            @endif
                                        </div>
                                        <div>
                                            <div class="font-medium leading-tight">{{ $user->name }}</div>
                                            <div class="text-xs text-base-content/50">{{ $user->position ?? 'Unassigned' }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="text-sm text-base-content/70">{{ $user->email }}</td>
                                <td><span class="badge badge-sm {{ $roleBadge[$user->role] ?? 'badge-ghost' }}">{{ $user->role ?? 'Standard' }}</span></td>
                                <td>
                                    <span class="inline-flex items-center gap-1.5 text-sm">
                                        <span class="h-1.5 w-1.5 rounded-full {{ $statusDot[$user->status] ?? 'bg-info' }}"></span>
                                        {{ $user->status ?? 'Active' }}
                                    </span>
                                </td>
                                <td>
                                    <div class="flex justify-end gap-2">
                                        <a href="{{ route('admin.users.show', $user) }}" class="btn btn-ghost btn-xs">Profile</a>
                                        @if ($user->id !== auth()->id())
                                            <form action="{{ route('admin.users.destroy', $user) }}" method="POST"
                                                  onsubmit="return confirm('Delete {{ $user->name }}? This revokes all access immediately.')">
                                                @csrf @method('DELETE')
                                                <button type="submit" class="btn btn-ghost btn-xs text-error">Delete</button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="5" class="py-10 text-center text-base-content/40">No users found.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if ($users->hasPages())
                <div class="border-t border-base-200 p-4">{{ $users->withQueryString()->links() }}</div>
            @endif
        </div>
    </div>
</x-layouts.app-shell>
