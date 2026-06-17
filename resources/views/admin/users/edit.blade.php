<x-layouts.app-shell title="Edit User">
    <div class="mx-auto max-w-3xl space-y-5">
        <div class="flex items-center justify-between gap-3">
            <div>
                <h2 class="text-xl font-semibold">Edit User</h2>
                <p class="text-sm text-base-content/60">Updating credentials for <span class="font-medium text-base-content">{{ $user->name }}</span>.</p>
            </div>
            <a href="{{ route('admin.users.index') }}" class="btn btn-ghost btn-sm">
                <x-mary-icon name="o-arrow-left" class="h-4 w-4" /> Back
            </a>
        </div>

        @if ($errors->any())
            <div role="alert" class="alert alert-error py-2 text-sm"><span>{{ $errors->first() }}</span></div>
        @endif

        <form method="POST" action="{{ route('admin.users.update', $user) }}" enctype="multipart/form-data"
              class="rounded-xl border border-base-300 bg-base-100 p-6 shadow-sm sm:p-8">
            @csrf @method('PUT')

            {{-- Avatar --}}
            <div class="mb-8 flex flex-col items-center gap-2">
                <label for="profile_picture" class="cursor-pointer">
                    <div class="relative grid h-28 w-28 place-items-center overflow-hidden rounded-full border-2 border-base-300 bg-base-200">
                        @if (optional($user->employee)->profile_picture_url)
                            <img id="avatar_preview" src="{{ $user->employee->profile_picture_url }}" alt="" class="h-full w-full object-cover">
                            <x-mary-icon name="o-camera" id="avatar_placeholder" class="hidden h-8 w-8 text-base-content/30" />
                        @else
                            <x-mary-icon name="o-camera" id="avatar_placeholder" class="h-8 w-8 text-base-content/30" />
                            <img id="avatar_preview" src="#" alt="" class="hidden h-full w-full object-cover">
                        @endif
                        <span class="absolute bottom-0 right-0 grid h-7 w-7 place-items-center rounded-full bg-primary text-primary-content ring-2 ring-base-100">
                            <x-mary-icon name="o-camera" class="h-4 w-4" />
                        </span>
                    </div>
                </label>
                <input type="file" name="profile_picture" id="profile_picture" class="hidden" accept="image/*" onchange="previewAvatar(this)">
                <span class="text-xs uppercase tracking-wide text-base-content/50">Profile picture</span>
                @error('profile_picture') <span class="text-xs text-error">{{ $message }}</span> @enderror
            </div>

            <h3 class="mb-4 flex items-center gap-2 font-semibold">
                <x-mary-icon name="o-shield-check" class="h-5 w-5 text-primary" /> Account &amp; Security
            </h3>
            <div class="grid gap-4 sm:grid-cols-2">
                <div>
                    <label class="mb-1.5 block text-sm font-medium">Full name</label>
                    <input name="name" value="{{ old('name', $user->name) }}" required class="input input-bordered w-full {{ $errors->has('name') ? 'input-error' : '' }}" />
                    @error('name') <span class="mt-1 block text-xs text-error">{{ $message }}</span> @enderror
                </div>
                <div>
                    <label class="mb-1.5 block text-sm font-medium">Email address</label>
                    <input type="email" name="email" value="{{ old('email', $user->email) }}" required class="input input-bordered w-full {{ $errors->has('email') ? 'input-error' : '' }}" />
                    @error('email') <span class="mt-1 block text-xs text-error">{{ $message }}</span> @enderror
                </div>
                <div>
                    <label class="mb-1.5 block text-sm font-medium">Change password</label>
                    <input type="password" name="password" placeholder="Leave blank to keep current" class="input input-bordered w-full {{ $errors->has('password') ? 'input-error' : '' }}" />
                    @error('password') <span class="mt-1 block text-xs text-error">{{ $message }}</span> @enderror
                </div>
                <div>
                    <label class="mb-1.5 block text-sm font-medium">System role</label>
                    <select name="role" required class="select select-bordered w-full">
                        @foreach ($roles as $role)
                            <option value="{{ $role }}" @selected(old('role', $user->role) === $role)>{{ $role }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <h3 class="mb-4 mt-8 flex items-center gap-2 font-semibold">
                <x-mary-icon name="o-identification" class="h-5 w-5 text-success" /> Professional Details
            </h3>
            <div class="grid gap-4 sm:grid-cols-2">
                <div>
                    <label class="mb-1.5 block text-sm font-medium">Phone number</label>
                    <input name="phone_number" value="{{ old('phone_number', $user->phone_number) }}" placeholder="+251 …" class="input input-bordered w-full" />
                </div>
                <div>
                    <label class="mb-1.5 block text-sm font-medium">Status</label>
                    <select name="status" class="select select-bordered w-full">
                        @foreach (['Active', 'Inactive', 'Suspended'] as $s)
                            <option value="{{ $s }}" @selected(old('status', $user->status) === $s)>{{ $s }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="mb-1.5 block text-sm font-medium">Job title / position</label>
                    <input name="position" value="{{ old('position', $user->position) }}" placeholder="e.g. Civil Engineer" class="input input-bordered w-full" />
                </div>
                <div>
                    <label class="mb-1.5 block text-sm font-medium">Department</label>
                    <input name="department" value="{{ old('department', $user->department) }}" placeholder="e.g. Engineering" class="input input-bordered w-full" />
                </div>
            </div>

            <div class="mt-8 flex justify-end gap-2">
                <a href="{{ route('admin.users.index') }}" class="btn btn-ghost">Cancel</a>
                <button type="submit" class="btn btn-primary">Update user</button>
            </div>
        </form>
    </div>

    @push('scripts')
    <script>
        function previewAvatar(input) {
            if (input.files && input.files[0]) {
                const reader = new FileReader();
                reader.onload = e => {
                    const img = document.getElementById('avatar_preview');
                    img.src = e.target.result; img.classList.remove('hidden');
                    const p = document.getElementById('avatar_placeholder'); if (p) p.classList.add('hidden');
                };
                reader.readAsDataURL(input.files[0]);
            }
        }
    </script>
    @endpush
</x-layouts.app-shell>
