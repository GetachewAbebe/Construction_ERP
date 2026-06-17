<x-layouts.auth title="Sign in · Natanem Engineering ERP">
    <div class="min-h-screen flex items-center justify-center p-4 sm:p-6">
        <div class="w-full max-w-4xl auth-rise">
            <div class="overflow-hidden rounded-3xl bg-base-100 shadow-2xl">
            <div class="grid md:grid-cols-2">

                {{-- LEFT: brand welcome panel --}}
                <div class="relative hidden flex-col overflow-hidden p-10 text-white md:flex"
                     style="background: linear-gradient(160deg,#1a3a5c 0%,#0f2438 55%,#0a1a2b 100%);">
                    {{-- subtle accents --}}
                    <div class="pointer-events-none absolute inset-x-0 top-0 h-px bg-gradient-to-r from-transparent via-amber-400/60 to-transparent"></div>
                    <div class="pointer-events-none absolute -bottom-28 -right-24 h-80 w-80 rounded-full bg-amber-500/10 blur-3xl"></div>
                    <div class="pointer-events-none absolute inset-0 opacity-[0.05]"
                         style="background-image: radial-gradient(circle at 1px 1px,#fff 1px,transparent 0); background-size: 24px 24px;"></div>

                    {{-- brand + value props, vertically centered as one block --}}
                    <div class="relative my-auto w-full">
                        <div class="text-center">
                            <div class="text-2xl font-bold tracking-wide">NATANEM <span class="text-amber-400">ENGINEERING</span></div>
                            <div class="mt-1.5 text-[11px] font-medium uppercase tracking-[0.3em] text-white/40">Enterprise Resource Planning</div>
                        </div>

                        <ul class="mx-auto mt-10 w-fit space-y-4">
                            @foreach ([
                                'Real-time inventory & stock control',
                                'Employee, attendance & leave management',
                                'Project budgeting & expense approvals',
                            ] as $feature)
                                <li class="flex items-center gap-3">
                                    <x-mary-icon name="o-check-circle" class="w-5 h-5 shrink-0 text-amber-400" />
                                    <span class="text-sm text-white/85">{{ $feature }}</span>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                </div>

                {{-- RIGHT: sign-in form --}}
                <div class="p-8 sm:p-10">
                    {{-- mobile brand --}}
                    <div class="mb-7 md:hidden">
                        <span class="text-lg font-bold tracking-wide">NATANEM <span class="text-primary">ENGINEERING</span></span>
                    </div>

                    <h1 class="text-2xl font-bold">Sign in</h1>
                    <p class="mt-1 mb-6 text-sm text-base-content/60">Enter your credentials to continue.</p>

                    @if (session('status'))
                        <div role="alert" class="alert alert-success mb-4 py-2 text-sm"><span>{{ session('status') }}</span></div>
                    @endif
                    @if ($errors->any())
                        <div role="alert" class="alert alert-error mb-4 py-2 text-sm"><span>{{ $errors->first() }}</span></div>
                    @endif

                    <form method="POST" action="{{ route('login', absolute: false) }}" class="space-y-4">
                        @csrf

                        <div>
                            <label for="email" class="mb-1.5 block text-sm font-medium">Email address</label>
                            <label class="input input-bordered flex items-center gap-2 w-full {{ $errors->has('email') ? 'input-error' : '' }}">
                                <x-mary-icon name="o-envelope" class="w-4 h-4 opacity-40" />
                                <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus
                                       autocomplete="username" placeholder="you@company.com" class="grow" />
                            </label>
                        </div>

                        <div>
                            <label for="password" class="mb-1.5 block text-sm font-medium">Password</label>
                            <label class="input input-bordered flex items-center gap-2 w-full {{ $errors->has('password') ? 'input-error' : '' }}">
                                <x-mary-icon name="o-lock-closed" class="w-4 h-4 opacity-40" />
                                <input id="password" type="password" name="password" required autocomplete="current-password"
                                       placeholder="••••••••" class="grow" />
                                <button type="button" onclick="togglePw()" class="text-base-content/40 hover:text-base-content/70">
                                    <span id="pw-eye"><x-mary-icon name="o-eye" class="w-5 h-5" /></span>
                                    <span id="pw-eye-off" class="hidden"><x-mary-icon name="o-eye-slash" class="w-5 h-5" /></span>
                                </button>
                            </label>
                        </div>

                        <div class="flex items-center justify-between pt-1">
                            <label class="flex cursor-pointer items-center gap-2 text-sm text-base-content/70">
                                <input type="checkbox" name="remember" class="checkbox checkbox-sm checkbox-primary" />
                                Remember me
                            </label>
                            <a href="{{ route('password.request') }}" class="text-sm font-medium text-amber-700 hover:underline">Forgot password?</a>
                        </div>

                        <button type="submit" class="btn btn-primary w-full mt-2">Sign in</button>
                    </form>
                </div>
            </div>
            </div>
            <p class="mt-5 text-center text-xs text-base-content/50">© {{ date('Y') }} Natanem Engineering</p>
        </div>
    </div>

    <script>
        function togglePw() {
            const input = document.getElementById('password');
            const show = input.type === 'password';
            input.type = show ? 'text' : 'password';
            document.getElementById('pw-eye').classList.toggle('hidden', show);
            document.getElementById('pw-eye-off').classList.toggle('hidden', !show);
        }
    </script>
</x-layouts.auth>
