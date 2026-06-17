<x-layouts.app-shell title="Permissions">
    <div class="mx-auto max-w-4xl space-y-5">
        <div class="flex items-center justify-between gap-3">
            <div>
                <h2 class="text-xl font-semibold">Permission Registry</h2>
                <p class="text-sm text-base-content/60">Access privileges available to roles.</p>
            </div>
            <a href="{{ route('admin.roles.index') }}" class="btn btn-ghost btn-sm">
                <x-mary-icon name="o-arrow-left" class="h-4 w-4" /> Roles
            </a>
        </div>

        @if (session('success'))
            <div role="alert" class="alert alert-success py-2 text-sm"><span>{{ session('success') }}</span></div>
        @endif
        @if ($errors->any())
            <div role="alert" class="alert alert-error py-2 text-sm"><span>{{ $errors->first() }}</span></div>
        @endif

        {{-- Add permission --}}
        <form method="POST" action="{{ route('admin.roles.permissions.store') }}"
              class="flex flex-col gap-3 rounded-xl border border-base-300 bg-base-100 p-4 shadow-sm sm:flex-row sm:items-end">
            @csrf
            <div class="flex-1">
                <label class="mb-1.5 block text-sm font-medium">New permission</label>
                <input name="name" value="{{ old('name') }}" required placeholder="e.g. project.create" class="input input-bordered w-full" />
            </div>
            <button type="submit" class="btn btn-primary">Add permission</button>
        </form>

        {{-- Grouped permissions --}}
        <div class="space-y-4">
            @forelse ($permissions as $module => $modulePermissions)
                <div class="rounded-xl border border-base-300 bg-base-100 shadow-sm">
                    <div class="border-b border-base-200 px-5 py-3 text-sm font-semibold uppercase tracking-wide text-base-content/70">{{ ucfirst($module) }}</div>
                    <div class="divide-y divide-base-200">
                        @foreach ($modulePermissions as $permission)
                            <div class="flex items-center justify-between px-5 py-2.5">
                                <span class="text-sm">{{ $permission->name }}</span>
                                <form method="POST" action="{{ route('admin.roles.permissions.destroy', $permission) }}" onsubmit="return confirm('Delete permission {{ $permission->name }}?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn btn-ghost btn-xs text-error">Delete</button>
                                </form>
                            </div>
                        @endforeach
                    </div>
                </div>
            @empty
                <div class="alert alert-info py-2 text-sm"><span>No permissions registered yet.</span></div>
            @endforelse
        </div>
    </div>
</x-layouts.app-shell>
