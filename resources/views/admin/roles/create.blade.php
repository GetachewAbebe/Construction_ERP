<x-layouts.app-shell title="New Role">
    <div class="mx-auto max-w-3xl space-y-5">
        <div class="flex items-center justify-between gap-3">
            <div>
                <h2 class="text-xl font-semibold">Create New Role</h2>
                <p class="text-sm text-base-content/60">Define a role and assign its permissions.</p>
            </div>
            <a href="{{ route('admin.roles.index') }}" class="btn btn-ghost btn-sm">
                <x-mary-icon name="o-arrow-left" class="h-4 w-4" /> Back
            </a>
        </div>

        @if ($errors->any())
            <div role="alert" class="alert alert-error py-2 text-sm"><span>{{ $errors->first() }}</span></div>
        @endif

        <form method="POST" action="{{ route('admin.roles.store') }}"
              class="space-y-6 rounded-xl border border-base-300 bg-base-100 p-6 shadow-sm sm:p-8">
            @csrf

            <div>
                <h3 class="mb-4 flex items-center gap-2 font-semibold"><x-mary-icon name="o-shield-check" class="h-5 w-5 text-primary" /> Role details</h3>
                <div class="grid gap-4 sm:grid-cols-2">
                    <div>
                        <label class="mb-1.5 block text-sm font-medium">Role name</label>
                        <input name="name" value="{{ old('name') }}" required placeholder="e.g. ProjectManager" class="input input-bordered w-full {{ $errors->has('name') ? 'input-error' : '' }}" />
                        @error('name') <span class="mt-1 block text-xs text-error">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="mb-1.5 block text-sm font-medium">Display name <span class="text-base-content/40">(optional)</span></label>
                        <input name="display_name" value="{{ old('display_name') }}" placeholder="e.g. Project Manager" class="input input-bordered w-full" />
                    </div>
                    <div class="sm:col-span-2">
                        <label class="mb-1.5 block text-sm font-medium">Description</label>
                        <textarea name="description" rows="2" placeholder="Role description…" class="textarea textarea-bordered w-full">{{ old('description') }}</textarea>
                    </div>
                </div>
            </div>

            <div>
                <h3 class="mb-4 flex items-center gap-2 font-semibold"><x-mary-icon name="o-key" class="h-5 w-5 text-success" /> Permissions</h3>
                @forelse ($permissions as $module => $modulePermissions)
                    <div class="mb-3 rounded-lg border border-base-200 bg-base-200/30 p-4">
                        <label class="mb-3 flex items-center gap-2 text-sm font-semibold uppercase tracking-wide">
                            <input type="checkbox" class="checkbox checkbox-sm checkbox-primary" onchange="toggleModule('{{ $module }}', this.checked)">
                            {{ ucfirst($module) }}
                        </label>
                        <div class="grid gap-2 sm:grid-cols-2">
                            @foreach ($modulePermissions as $permission)
                                <label class="flex items-center gap-2 text-sm">
                                    <input type="checkbox" name="permissions[]" value="{{ $permission->id }}" class="checkbox checkbox-sm mod-{{ $module }}"
                                           @checked(in_array($permission->id, old('permissions', [])))>
                                    {{ $permission->name }}
                                </label>
                            @endforeach
                        </div>
                    </div>
                @empty
                    <div class="alert alert-info py-2 text-sm"><span>No permissions found.</span></div>
                @endforelse
            </div>

            <div class="flex justify-end gap-2 border-t border-base-200 pt-5">
                <a href="{{ route('admin.roles.index') }}" class="btn btn-ghost">Cancel</a>
                <button type="submit" class="btn btn-primary">Create role</button>
            </div>
        </form>
    </div>

    @push('scripts')
    <script>
        function toggleModule(mod, checked) {
            document.querySelectorAll('.mod-' + mod).forEach(c => c.checked = checked);
        }
    </script>
    @endpush
</x-layouts.app-shell>
