<x-layouts.app-shell title="Add User">
    <div class="mx-auto max-w-3xl space-y-5">
        {{-- Heading --}}
        <div class="flex items-center justify-between gap-3">
            <div>
                <h2 class="text-xl font-semibold">Add New User</h2>
                <p class="text-sm text-base-content/60">Create administrative credentials and professional details.</p>
            </div>
            <a href="{{ route('admin.users.index') }}" class="btn btn-ghost btn-sm">
                <x-mary-icon name="o-arrow-left" class="h-4 w-4" /> Back
            </a>
        </div>

        @if ($errors->any())
            <div role="alert" class="alert alert-error py-2 text-sm"><span>{{ $errors->first() }}</span></div>
        @endif

        <form method="POST" action="{{ route('admin.users.store') }}" enctype="multipart/form-data"
              class="rounded-xl border border-base-300 bg-base-100 p-6 shadow-sm sm:p-8">
            @csrf

            {{-- Avatar --}}
            <div class="mb-8 flex flex-col items-center gap-2">
                <label for="profile_picture" class="cursor-pointer">
                    <div class="relative grid h-28 w-28 place-items-center overflow-hidden rounded-full border-2 border-base-300 bg-base-200">
                        <x-mary-icon name="o-camera" id="avatar_placeholder" class="h-8 w-8 text-base-content/30" />
                        <img id="avatar_preview" src="#" alt="" class="hidden h-full w-full object-cover">
                        <span class="absolute bottom-0 right-0 grid h-7 w-7 place-items-center rounded-full bg-primary text-primary-content ring-2 ring-base-100">
                            <x-mary-icon name="o-plus" class="h-4 w-4" />
                        </span>
                    </div>
                </label>
                <input type="file" name="profile_picture" id="profile_picture" class="hidden" accept="image/*" onchange="previewAvatar(this)">
                <span class="text-xs uppercase tracking-wide text-base-content/50">Profile picture</span>
                @error('profile_picture') <span class="text-xs text-error">{{ $message }}</span> @enderror
            </div>

            {{-- Account & Security --}}
            <h3 class="mb-4 flex items-center gap-2 font-semibold">
                <x-mary-icon name="o-shield-check" class="h-5 w-5 text-primary" /> Account &amp; Security
            </h3>
            <div class="grid gap-4 sm:grid-cols-2">
                <div>
                    <label class="mb-1.5 block text-sm font-medium">Full name</label>
                    <input name="name" value="{{ old('name', $employee ? $employee->first_name.' '.$employee->last_name : '') }}" required
                           placeholder="e.g. Getachew Abebe" class="input input-bordered w-full {{ $errors->has('name') ? 'input-error' : '' }}" />
                    @error('name') <span class="mt-1 block text-xs text-error">{{ $message }}</span> @enderror
                </div>
                <div>
                    <label class="mb-1.5 block text-sm font-medium">Email address</label>
                    <input type="email" name="email" value="{{ old('email', $employee->email ?? '') }}" required
                           placeholder="user@natanem.com" class="input input-bordered w-full {{ $errors->has('email') ? 'input-error' : '' }}" />
                    @error('email') <span class="mt-1 block text-xs text-error">{{ $message }}</span> @enderror
                </div>
                <div>
                    <label class="mb-1.5 block text-sm font-medium">Password</label>
                    <label class="input input-bordered flex w-full items-center gap-2 {{ $errors->has('password') ? 'input-error' : '' }}">
                        <input id="password" type="password" name="password" required placeholder="Min. 8 characters" class="grow" oninput="checkStrength(this.value)" />
                        <button type="button" onclick="togglePw()" class="text-xs font-medium text-base-content/50 hover:text-base-content"><span id="pw-lbl">Show</span></button>
                    </label>
                    <div id="strength-wrap" class="mt-2 hidden">
                        <div class="h-1.5 w-full overflow-hidden rounded-full bg-base-300"><div id="strength-bar" class="h-full w-0 rounded-full transition-all"></div></div>
                        <div class="mt-1 flex justify-between text-[11px]"><span class="text-base-content/50">Strength</span><span id="strength-text" class="font-semibold"></span></div>
                    </div>
                    @error('password') <span class="mt-1 block text-xs text-error">{{ $message }}</span> @enderror
                </div>
                <div>
                    <label class="mb-1.5 block text-sm font-medium">System role</label>
                    <select name="role" required class="select select-bordered w-full {{ $errors->has('role') ? 'select-error' : '' }}">
                        <option value="">Select role…</option>
                        @foreach ($roles as $role)
                            <option value="{{ $role }}" @selected(old('role') === $role)>{{ $role }}</option>
                        @endforeach
                    </select>
                    @error('role') <span class="mt-1 block text-xs text-error">{{ $message }}</span> @enderror
                </div>
            </div>

            {{-- Professional details --}}
            <h3 class="mb-4 mt-8 flex items-center gap-2 font-semibold">
                <x-mary-icon name="o-identification" class="h-5 w-5 text-success" /> Professional Details
            </h3>
            <div class="grid gap-4 sm:grid-cols-2">
                <div>
                    <label class="mb-1.5 block text-sm font-medium">Phone number</label>
                    <input name="phone_number" value="{{ old('phone_number', $employee->phone ?? '') }}" placeholder="+251 …" class="input input-bordered w-full" />
                </div>
                <div>
                    <label class="mb-1.5 block text-sm font-medium">Status</label>
                    <select name="status" class="select select-bordered w-full">
                        @foreach (['Active', 'Inactive', 'Suspended'] as $s)
                            <option value="{{ $s }}" @selected(old('status') === $s)>{{ $s }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="mb-1.5 block text-sm font-medium">Job title / position</label>
                    <input name="position" value="{{ old('position', $employee->position ?? '') }}" placeholder="e.g. Civil Engineer" class="input input-bordered w-full" />
                </div>
                <div>
                    <label class="mb-1.5 block text-sm font-medium">Department</label>
                    <input name="department" value="{{ old('department', $employee->department ?? '') }}" placeholder="e.g. Engineering" class="input input-bordered w-full" />
                </div>
                <div>
                    <label class="mb-1.5 block text-sm font-medium">Hire date</label>
                    <input type="date" name="hire_date" value="{{ old('hire_date', optional($employee->hire_date ?? null)->format('Y-m-d') ?? date('Y-m-d')) }}" class="input input-bordered w-full" />
                </div>
                <div>
                    <label class="mb-1.5 block text-sm font-medium">Initial salary (ETB)</label>
                    <input type="number" step="0.01" name="salary" value="{{ old('salary', $employee->salary ?? '') }}" placeholder="0.00" class="input input-bordered w-full" />
                </div>
            </div>

            <div class="mt-8 flex justify-end gap-2">
                <a href="{{ route('admin.users.index') }}" class="btn btn-ghost">Cancel</a>
                <button type="submit" class="btn btn-primary">Create user</button>
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
                    document.getElementById('avatar_placeholder').classList.add('hidden');
                };
                reader.readAsDataURL(input.files[0]);
            }
        }
        function togglePw() {
            const i = document.getElementById('password'), l = document.getElementById('pw-lbl');
            const show = i.type === 'password'; i.type = show ? 'text' : 'password'; l.textContent = show ? 'Hide' : 'Show';
        }
        function checkStrength(pw) {
            const wrap = document.getElementById('strength-wrap'), bar = document.getElementById('strength-bar'), text = document.getElementById('strength-text');
            if (!pw.length) { wrap.classList.add('hidden'); return; }
            wrap.classList.remove('hidden');
            let s = 0; if (pw.length >= 8) s++; if (/[A-Z]/.test(pw)) s++; if (/[0-9]/.test(pw)) s++; if (/[^a-zA-Z0-9]/.test(pw)) s++;
            if (pw.length < 8) s = Math.min(s, 1);
            const lv = s <= 1 ? ['33%','bg-error','Weak','text-error'] : (s <= 3 ? ['66%','bg-warning','Medium','text-warning'] : ['100%','bg-success','Strong','text-success']);
            bar.style.width = lv[0]; bar.className = 'h-full rounded-full transition-all ' + lv[1];
            text.textContent = lv[2]; text.className = 'font-semibold ' + lv[3];
        }
    </script>
    @endpush
</x-layouts.app-shell>
