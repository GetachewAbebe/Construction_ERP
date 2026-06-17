<x-layouts.app-shell title="Edit Profile">
    <div class="mx-auto max-w-2xl space-y-5">
        <div class="flex items-center justify-between gap-3">
            <div>
                <h2 class="text-xl font-semibold">Edit Profile</h2>
                <p class="text-sm text-base-content/60">Update your personal details and password.</p>
            </div>
            <a href="{{ route($user->getProfileRouteName('show')) }}" class="btn btn-ghost btn-sm">
                <x-mary-icon name="o-arrow-left" class="h-4 w-4" /> Back
            </a>
        </div>

        @if ($errors->any())
            <div role="alert" class="alert alert-error py-2 text-sm"><span>{{ $errors->first() }}</span></div>
        @endif

        <form method="POST" action="{{ route($user->getProfileRouteName('update')) }}" enctype="multipart/form-data"
              class="space-y-6 rounded-xl border border-base-300 bg-base-100 p-6 shadow-sm sm:p-8">
            @csrf @method('PUT')

            <div class="flex flex-col items-center gap-2">
                <label for="profile_picture" class="cursor-pointer">
                    <div class="relative grid h-28 w-28 place-items-center overflow-hidden rounded-full border-2 border-base-300 bg-base-200 text-2xl font-bold text-base-content/40">
                        @if (optional($user->employee)->profile_picture_url)
                            <img id="avatar_preview" src="{{ $user->employee->profile_picture_url }}" class="h-full w-full object-cover" alt="">
                        @else
                            <span id="avatar_placeholder">{{ strtoupper(mb_substr($user->first_name ?? 'U', 0, 1)) }}</span>
                            <img id="avatar_preview" src="#" alt="" class="hidden h-full w-full object-cover">
                        @endif
                        <span class="absolute bottom-0 right-0 grid h-7 w-7 place-items-center rounded-full bg-primary text-primary-content ring-2 ring-base-100"><x-mary-icon name="o-camera" class="h-4 w-4" /></span>
                    </div>
                </label>
                <input type="file" name="profile_picture" id="profile_picture" class="hidden" accept="image/*" onchange="previewAvatar(this)">
                <span class="text-xs uppercase tracking-wide text-base-content/50">Profile photo</span>
                @error('profile_picture') <span class="text-xs text-error">{{ $message }}</span> @enderror
            </div>

            <div>
                <h3 class="mb-4 flex items-center gap-2 font-semibold"><x-mary-icon name="o-user" class="h-5 w-5 text-primary" /> Personal information</h3>
                <div class="grid gap-4 sm:grid-cols-2">
                    <div>
                        <label class="mb-1.5 block text-sm font-medium">Display name</label>
                        <input name="name" value="{{ old('name', $user->name) }}" required class="input input-bordered w-full {{ $errors->has('name') ? 'input-error' : '' }}" />
                        @error('name') <span class="mt-1 block text-xs text-error">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="mb-1.5 block text-sm font-medium">Email</label>
                        <input type="email" name="email" value="{{ old('email', $user->email) }}" required class="input input-bordered w-full {{ $errors->has('email') ? 'input-error' : '' }}" />
                        @error('email') <span class="mt-1 block text-xs text-error">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="mb-1.5 block text-sm font-medium">Phone</label>
                        <input name="phone_number" value="{{ old('phone_number', $user->phone_number) }}" class="input input-bordered w-full" />
                    </div>
                </div>
            </div>

            <div>
                <h3 class="mb-4 flex items-center gap-2 font-semibold"><x-mary-icon name="o-shield-check" class="h-5 w-5 text-warning" /> Security</h3>
                <div class="grid gap-4 sm:grid-cols-2">
                    <div>
                        <label class="mb-1.5 block text-sm font-medium">New password</label>
                        <input type="password" name="password" placeholder="Leave blank to keep current" class="input input-bordered w-full {{ $errors->has('password') ? 'input-error' : '' }}" />
                        @error('password') <span class="mt-1 block text-xs text-error">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="mb-1.5 block text-sm font-medium">Confirm password</label>
                        <input type="password" name="password_confirmation" placeholder="Re-type new password" class="input input-bordered w-full" />
                    </div>
                </div>
            </div>

            <div class="flex items-start gap-2 rounded-lg border border-info/30 bg-info/5 p-3 text-xs text-base-content/70">
                <x-mary-icon name="o-information-circle" class="h-5 w-5 shrink-0 text-info" />
                <span>Your role ({{ $user->role }}), department and position are managed by an administrator.</span>
            </div>

            <div class="flex justify-end gap-2 border-t border-base-200 pt-5">
                <a href="{{ route($user->getProfileRouteName('show')) }}" class="btn btn-ghost">Cancel</a>
                <button type="submit" class="btn btn-primary">Save changes</button>
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
