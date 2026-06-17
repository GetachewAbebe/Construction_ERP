<x-layouts.app-shell title="Roles">
    @php($protected = ['Administrator', 'HumanResourceManager', 'InventoryManager', 'FinancialManager'])

    <div class="space-y-5">
        <div class="flex flex-wrap items-center justify-between gap-3">
            <div>
                <h2 class="text-xl font-semibold">Roles &amp; Access</h2>
                <p class="text-sm text-base-content/60">Authorization tiers and their permission sets.</p>
            </div>
            <div class="flex gap-2">
                <a href="{{ route('admin.roles.permissions') }}" class="btn btn-ghost btn-sm">
                    <x-mary-icon name="o-key" class="h-4 w-4" /> Permissions
                </a>
                <a href="{{ route('admin.roles.create') }}" class="btn btn-primary btn-sm">
                    <x-mary-icon name="o-plus" class="h-4 w-4" /> New role
                </a>
            </div>
        </div>

        @if (session('success'))
            <div role="alert" class="alert alert-success py-2 text-sm"><span>{{ session('success') }}</span></div>
        @endif
        @if (session('error'))
            <div role="alert" class="alert alert-error py-2 text-sm"><span>{{ session('error') }}</span></div>
        @endif

        <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
            @foreach ($roles as $role)
                <div class="flex flex-col rounded-xl border border-base-300 bg-base-100 p-5 shadow-sm">
                    <div class="flex items-start justify-between">
                        <div class="grid h-10 w-10 place-items-center rounded-lg bg-primary/10 text-primary">
                            <x-mary-icon name="o-shield-check" class="h-5 w-5" />
                        </div>
                        @if (in_array($role->name, $protected))
                            <span class="badge badge-ghost badge-sm">Core</span>
                        @endif
                    </div>
                    <h3 class="mt-3 font-semibold">{{ $role->name }}</h3>
                    <p class="mt-1 text-sm text-base-content/60">{{ $role->permissions->count() }} permission{{ $role->permissions->count() === 1 ? '' : 's' }}</p>

                    <div class="mt-4 flex gap-2">
                        <a href="{{ route('admin.roles.edit', $role) }}" class="btn btn-outline btn-xs">Edit</a>
                        @unless (in_array($role->name, $protected))
                            <form method="POST" action="{{ route('admin.roles.destroy', $role) }}" onsubmit="return confirm('Delete role {{ $role->name }}?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn btn-ghost btn-xs text-error">Delete</button>
                            </form>
                        @endunless
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</x-layouts.app-shell>
