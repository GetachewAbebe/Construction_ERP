<x-layouts.app-shell title="User Profile">
    @php
        $avatarUrl = optional($user->employee)->profile_picture_url;
        $initials = strtoupper(mb_substr($user->first_name ?? '', 0, 1) . mb_substr($user->last_name ?? '', 0, 1));
        $statusBadge = ['Active' => 'badge-success', 'Inactive' => 'badge-warning', 'Suspended' => 'badge-error'];
    @endphp

    <div class="mx-auto max-w-4xl space-y-5">
        <div class="flex items-center justify-between gap-3">
            <h2 class="text-xl font-semibold">{{ $user->role ?? 'User' }} Profile</h2>
            <a href="{{ route('admin.users.index') }}" class="btn btn-ghost btn-sm">
                <x-mary-icon name="o-arrow-left" class="h-4 w-4" /> Back
            </a>
        </div>

        <div class="overflow-hidden rounded-2xl border border-base-300 bg-base-100 shadow-sm">
            {{-- Header --}}
            <div class="flex flex-col items-center gap-3 border-b border-base-200 p-8 text-center"
                 style="background: linear-gradient(160deg,#13263b 0%,#0d1b2a 100%);">
                <div class="grid h-28 w-28 place-items-center overflow-hidden rounded-full bg-white/10 text-3xl font-bold text-white ring-4 ring-white/10">
                    @if ($avatarUrl)
                        <img src="{{ $avatarUrl }}" class="h-full w-full object-cover" alt="">
                    @else
                        {{ $initials ?: 'U' }}
                    @endif
                </div>
                <div>
                    <h3 class="text-2xl font-bold text-white">{{ $user->name }}</h3>
                    <div class="mt-2 flex flex-wrap items-center justify-center gap-2">
                        <span class="badge badge-primary">{{ $user->role }}</span>
                        <span class="badge badge-ghost text-white/80">ID #{{ str_pad((string) $user->id, 5, '0', STR_PAD_LEFT) }}</span>
                    </div>
                </div>
            </div>

            {{-- Details --}}
            <div class="grid gap-6 p-6 sm:p-8 md:grid-cols-2">
                <div class="rounded-xl border border-base-200 bg-base-200/30 p-5">
                    <h4 class="mb-4 flex items-center gap-2 font-semibold">
                        <x-mary-icon name="o-user" class="h-5 w-5 text-primary" /> Personal Credentials
                    </h4>
                    <dl class="space-y-4 text-sm">
                        <div>
                            <dt class="text-xs uppercase tracking-wide text-base-content/50">Full name</dt>
                            <dd class="mt-0.5 font-medium">{{ $user->name }}</dd>
                        </div>
                        <div>
                            <dt class="text-xs uppercase tracking-wide text-base-content/50">Email</dt>
                            <dd class="mt-0.5 font-medium">{{ $user->email }}</dd>
                        </div>
                        <div>
                            <dt class="text-xs uppercase tracking-wide text-base-content/50">Phone</dt>
                            <dd class="mt-0.5 font-medium">{{ $user->phone_number ?: '—' }}</dd>
                        </div>
                    </dl>
                </div>

                <div class="rounded-xl border border-base-200 bg-base-200/30 p-5">
                    <h4 class="mb-4 flex items-center gap-2 font-semibold">
                        <x-mary-icon name="o-building-office-2" class="h-5 w-5 text-success" /> Organization
                    </h4>
                    <dl class="space-y-4 text-sm">
                        <div>
                            <dt class="text-xs uppercase tracking-wide text-base-content/50">Department</dt>
                            <dd class="mt-0.5 font-medium">{{ $user->department ?: 'General Operations' }}</dd>
                        </div>
                        <div>
                            <dt class="text-xs uppercase tracking-wide text-base-content/50">Position</dt>
                            <dd class="mt-0.5 font-medium">{{ $user->position ?: 'Authorized System User' }}</dd>
                        </div>
                        <div>
                            <dt class="text-xs uppercase tracking-wide text-base-content/50">Status</dt>
                            <dd class="mt-1"><span class="badge badge-sm {{ $statusBadge[$user->status] ?? 'badge-ghost' }}">{{ $user->status ?? 'Active' }}</span></dd>
                        </div>
                    </dl>
                </div>
            </div>

            {{-- Footer --}}
            <div class="flex flex-col items-center justify-between gap-3 border-t border-base-200 p-6 sm:flex-row">
                <span class="text-xs text-base-content/50">
                    Last updated {{ $user->updated_at ? $user->updated_at->format('M d, Y') : 'original record' }}
                </span>
                <a href="{{ route('admin.users.edit', $user) }}" class="btn btn-primary btn-sm">
                    <x-mary-icon name="o-pencil-square" class="h-4 w-4" /> Edit user
                </a>
            </div>
        </div>
    </div>
</x-layouts.app-shell>
