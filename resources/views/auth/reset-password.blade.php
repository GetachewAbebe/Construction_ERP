<x-layouts.auth title="Reset password · Natanem Engineering ERP">
    <div class="min-h-screen flex items-center justify-center p-6">
        <div class="w-full max-w-md auth-rise">
            <div class="rounded-3xl border border-base-300/70 bg-base-100 p-8 sm:p-10 shadow-sm">
                <div class="mb-6 flex items-center gap-2.5">
                    <div class="grid h-9 w-9 place-items-center rounded-lg bg-primary text-primary-content text-xs font-bold">NE</div>
                    <span class="text-sm font-semibold">Natanem Engineering</span>
                </div>

                <div class="mb-6">
                    <h1 class="text-xl font-bold">Create a new password</h1>
                    <p class="mt-1 text-sm text-base-content/60">Choose a strong password you don’t reuse.</p>
                </div>

                @if ($errors->any())
                    <div role="alert" class="alert alert-error mb-4 py-2 text-sm">
                        <x-mary-icon name="o-exclamation-triangle" class="w-5 h-5" />
                        <span>
                            @if ($errors->first() === 'This password reset token is invalid.')
                                This reset link has expired. Please request a new one.
                            @else
                                {{ $errors->first() }}
                            @endif
                        </span>
                    </div>
                @endif

                <form method="POST" action="{{ route('password.update') }}" class="space-y-4">
                    @csrf
                    <input type="hidden" name="token" value="{{ $request->route('token') }}">

                    {{-- Email (readonly) --}}
                    <div>
                        <label for="email" class="mb-1.5 block text-sm font-medium">Email address</label>
                        <label class="input input-bordered flex items-center gap-2 bg-base-200/60">
                            <x-mary-icon name="o-envelope" class="w-4 h-4 opacity-50" />
                            <input id="email" type="email" name="email" value="{{ old('email', $request->email) }}" readonly
                                   class="grow text-base-content/70" />
                        </label>
                    </div>

                    {{-- New password --}}
                    <div>
                        <label for="password" class="mb-1.5 block text-sm font-medium">New password</label>
                        <label class="input input-bordered flex items-center gap-2 {{ $errors->has('password') ? 'input-error' : '' }}">
                            <x-mary-icon name="o-lock-closed" class="w-4 h-4 opacity-50" />
                            <input id="password" type="password" name="password" required autofocus autocomplete="new-password"
                                   placeholder="Min. 8 characters" class="grow"
                                   oninput="checkStrength(this.value); checkMatch();" />
                            <button type="button" onclick="togglePw('password','pw1')" class="text-xs font-medium text-base-content/50 hover:text-base-content">
                                <span id="pw1">Show</span>
                            </button>
                        </label>

                        {{-- Strength meter --}}
                        <div id="strength-wrap" class="mt-2 hidden">
                            <div class="h-1.5 w-full overflow-hidden rounded-full bg-base-300">
                                <div id="strength-bar" class="h-full w-0 rounded-full transition-all duration-300"></div>
                            </div>
                            <div class="mt-1 flex items-center justify-between text-[11px]">
                                <span class="text-base-content/50">Password strength</span>
                                <span id="strength-text" class="font-semibold">Weak</span>
                            </div>
                        </div>
                    </div>

                    {{-- Confirm password --}}
                    <div>
                        <label for="password_confirmation" class="mb-1.5 block text-sm font-medium">Confirm password</label>
                        <label class="input input-bordered flex items-center gap-2">
                            <x-mary-icon name="o-lock-closed" class="w-4 h-4 opacity-50" />
                            <input id="password_confirmation" type="password" name="password_confirmation" required autocomplete="new-password"
                                   placeholder="Re-enter new password" class="grow" oninput="checkMatch()" />
                            <button type="button" onclick="togglePw('password_confirmation','pw2')" class="text-xs font-medium text-base-content/50 hover:text-base-content">
                                <span id="pw2">Show</span>
                            </button>
                        </label>
                        <p id="match-msg" class="mt-1.5 hidden items-center gap-1 text-[11px] font-medium"></p>
                    </div>

                    <button type="submit" class="btn btn-primary w-full">
                        <x-mary-icon name="o-check" class="w-5 h-5" />
                        Reset password
                    </button>
                </form>

                <div class="mt-6 text-center">
                    <a href="{{ route('home') }}" class="inline-flex items-center gap-1 text-sm font-medium text-primary hover:underline">
                        <x-mary-icon name="o-arrow-left" class="w-4 h-4" />
                        Back to sign in
                    </a>
                </div>
            </div>
        </div>
    </div>

    <script>
        function togglePw(inputId, labelId) {
            const input = document.getElementById(inputId);
            const label = document.getElementById(labelId);
            const show = input.type === 'password';
            input.type = show ? 'text' : 'password';
            label.textContent = show ? 'Hide' : 'Show';
        }

        function checkStrength(pw) {
            const wrap = document.getElementById('strength-wrap');
            const bar = document.getElementById('strength-bar');
            const text = document.getElementById('strength-text');
            if (!pw.length) { wrap.classList.add('hidden'); return; }
            wrap.classList.remove('hidden');

            let score = 0;
            if (pw.length >= 8) score++;
            if (/[A-Z]/.test(pw)) score++;
            if (/[0-9]/.test(pw)) score++;
            if (/[^a-zA-Z0-9]/.test(pw)) score++;
            if (pw.length < 8) score = Math.min(score, 1);

            const levels = [
                { w: '33%', bar: 'bg-error',   text: 'Weak',   cls: 'text-error' },
                { w: '66%', bar: 'bg-warning', text: 'Medium', cls: 'text-warning' },
                { w: '100%',bar: 'bg-success', text: 'Strong', cls: 'text-success' },
            ];
            const lvl = score <= 1 ? levels[0] : (score <= 3 ? levels[1] : levels[2]);
            bar.style.width = lvl.w;
            bar.className = 'h-full rounded-full transition-all duration-300 ' + lvl.bar;
            text.textContent = lvl.text;
            text.className = 'font-semibold ' + lvl.cls;
        }

        function checkMatch() {
            const p1 = document.getElementById('password').value;
            const p2 = document.getElementById('password_confirmation').value;
            const msg = document.getElementById('match-msg');
            if (!p2.length) { msg.classList.add('hidden'); return; }
            msg.classList.remove('hidden');
            msg.classList.add('flex');
            if (p1 === p2) {
                msg.textContent = '✓ Passwords match';
                msg.className = 'mt-1.5 flex items-center gap-1 text-[11px] font-medium text-success';
            } else {
                msg.textContent = '✕ Passwords do not match';
                msg.className = 'mt-1.5 flex items-center gap-1 text-[11px] font-medium text-error';
            }
        }
    </script>
</x-layouts.auth>
