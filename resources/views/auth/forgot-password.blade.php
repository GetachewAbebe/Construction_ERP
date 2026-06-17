<x-layouts.auth title="Reset password · Natanem Engineering ERP">
    <div class="min-h-screen flex items-center justify-center p-6">
        <div class="w-full max-w-md auth-rise">
            <div class="rounded-3xl border border-base-300/70 bg-base-100 p-8 sm:p-10 shadow-sm">
                <div class="mb-6 flex items-center gap-2.5">
                    <div class="grid h-9 w-9 place-items-center rounded-lg bg-primary text-primary-content text-xs font-bold">NE</div>
                    <span class="text-sm font-semibold">Natanem Engineering</span>
                </div>

                <h1 class="text-xl font-bold">Reset password</h1>
                <p class="mt-1 mb-6 text-sm text-base-content/60">We’ll email you a reset link.</p>

                @if (session('status'))
                    <div role="alert" class="alert alert-success mb-4 py-2 text-sm"><span>{{ session('status') }}</span></div>
                @endif
                @if ($errors->any())
                    <div role="alert" class="alert alert-error mb-4 py-2 text-sm"><span>{{ $errors->first() }}</span></div>
                @endif

                <form method="POST" action="{{ route('password.email') }}" class="space-y-4">
                    @csrf
                    <label class="input input-bordered flex items-center gap-2 w-full {{ $errors->has('email') ? 'input-error' : '' }}">
                        <x-mary-icon name="o-envelope" class="w-4 h-4 opacity-40" />
                        <input type="email" name="email" value="{{ old('email') }}" required autofocus
                               autocomplete="username" placeholder="Email address" class="grow" />
                    </label>

                    <button type="submit" class="btn btn-primary w-full shadow-lg shadow-primary/30">Send reset link</button>
                </form>

                <a href="{{ route('home') }}" class="mt-6 inline-flex items-center gap-1 text-sm text-base-content/60 hover:text-base-content">
                    <x-mary-icon name="o-arrow-left" class="w-4 h-4" />
                    Back to sign in
                </a>
            </div>
        </div>
    </div>
</x-layouts.auth>
