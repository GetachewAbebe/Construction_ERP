<x-layouts.auth title="Sign in · Natanem Engineering ERP">
    <div class="min-h-screen flex flex-col justify-between items-center p-4 sm:p-6 lg:p-8 bg-slate-50 dark:bg-slate-950 text-slate-800 dark:text-slate-100 selection:bg-blue-900 selection:text-white"
         x-data="{
             submitting: false,
             showPassword: false,
             darkMode: localStorage.getItem('erp-theme') === 'natanem-dark' || (!localStorage.getItem('erp-theme') && window.matchMedia('(prefers-color-scheme: dark)').matches),
             toggleTheme() {
                 this.darkMode = !this.darkMode;
                 var theme = this.darkMode ? 'natanem-dark' : 'natanem';
                 document.getElementById('html-root').setAttribute('data-theme', theme);
                 localStorage.setItem('erp-theme', theme);
                 if (this.darkMode) {
                     document.documentElement.classList.add('dark');
                 } else {
                     document.documentElement.classList.remove('dark');
                 }
             }
         }">

        {{-- Top Bar: Theme Switcher --}}
        <header class="w-full max-w-md flex items-center justify-end py-2">
            <button @click="toggleTheme()" 
                    type="button" 
                    title="Toggle theme" 
                    class="p-2 rounded-xl text-slate-400 hover:text-slate-700 dark:hover:text-white hover:bg-slate-200/60 dark:hover:bg-slate-800/80 transition-colors border border-slate-200 dark:border-slate-800 cursor-pointer">
                <x-mary-icon name="o-moon" class="w-4 h-4" x-show="!darkMode" />
                <x-mary-icon name="o-sun" class="w-4 h-4 text-amber-500" x-show="darkMode" style="display:none" />
            </button>
        </header>

        {{-- Centered Auth Card --}}
        <main class="w-full max-w-md my-auto">
            
            {{-- Brand Header --}}
            <div class="text-center mb-6">
                <h2 class="text-2xl font-black tracking-tight text-slate-900 dark:text-white">
                    Natanem Engineering
                </h2>
            </div>

            {{-- Form Card --}}
            <div class="rounded-2xl border border-slate-200/90 dark:border-slate-800 bg-white dark:bg-slate-900 p-7 sm:p-9 shadow-xl shadow-slate-900/5 dark:shadow-black/40">
                
                <div class="mb-6">
                    <h1 class="text-xl font-bold tracking-tight text-slate-900 dark:text-white">
                        Sign in to your account
                    </h1>
                    <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">
                        Enter your official company credentials to access your workspace.
                    </p>
                </div>

                {{-- Status Alert --}}
                @if (session('status'))
                    <div role="status" class="mb-5 p-3 rounded-xl bg-emerald-50 dark:bg-emerald-950/40 text-emerald-800 dark:text-emerald-300 text-xs font-semibold border border-emerald-200 dark:border-emerald-800/50 flex items-center gap-2">
                        <x-mary-icon name="o-check-circle" class="w-4 h-4 shrink-0 text-emerald-600 dark:text-emerald-400" />
                        <span>{{ session('status') }}</span>
                    </div>
                @endif

                {{-- Validation Error Alert --}}
                @if ($errors->any())
                    <div role="alert" class="mb-5 p-3.5 rounded-xl bg-rose-50 dark:bg-rose-950/40 text-rose-800 dark:text-rose-300 text-xs font-semibold border border-rose-200 dark:border-rose-800/50 flex items-start gap-2.5">
                        <x-mary-icon name="o-exclamation-triangle" class="w-4 h-4 shrink-0 text-rose-600 dark:text-rose-400 mt-0.5" />
                        <div>
                            <span class="block font-bold">Authentication failed</span>
                            <span class="text-[11px] font-normal text-rose-700 dark:text-rose-300/90">{{ $errors->first() }}</span>
                        </div>
                    </div>
                @endif

                {{-- Login Form --}}
                <form method="POST" action="{{ route('login', absolute: false) }}" @submit="submitting = true" class="space-y-4" novalidate>
                    @csrf

                    {{-- Corporate Email --}}
                    <div>
                        <label for="email" class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5 uppercase tracking-wider">
                            Corporate Email
                        </label>
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                                <x-mary-icon name="o-envelope" class="w-4 h-4" />
                            </span>
                            <input id="email" 
                                   type="email" 
                                   name="email" 
                                   value="{{ old('email') }}"
                                   required 
                                   autofocus
                                   autocomplete="username"
                                   placeholder="name@natanemengineering.com"
                                   class="w-full pl-10 pr-4 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50/70 dark:bg-slate-800/70 text-slate-900 dark:text-white text-sm focus:outline-none focus:ring-2 focus:ring-blue-900/20 focus:border-blue-900 dark:focus:border-blue-500 transition-all placeholder-slate-400" />
                        </div>
                    </div>

                    {{-- Password --}}
                    <div>
                        <div class="flex items-center justify-between mb-1.5">
                            <label for="password" class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider">
                                Password
                            </label>
                            <a href="{{ route('password.request') }}" class="text-xs font-medium text-blue-900 dark:text-blue-400 hover:underline">
                                Forgot password?
                            </a>
                        </div>
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                                <x-mary-icon name="o-lock-closed" class="w-4 h-4" />
                            </span>
                            <input id="password" 
                                   :type="showPassword ? 'text' : 'password'" 
                                   name="password" 
                                   required 
                                   autocomplete="current-password"
                                   placeholder="••••••••••••"
                                   class="w-full pl-10 pr-10 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50/70 dark:bg-slate-800/70 text-slate-900 dark:text-white text-sm focus:outline-none focus:ring-2 focus:ring-blue-900/20 focus:border-blue-900 dark:focus:border-blue-500 transition-all placeholder-slate-400" />
                            <button type="button" 
                                    @click="showPassword = !showPassword" 
                                    :aria-label="showPassword ? 'Hide password' : 'Show password'"
                                    class="absolute inset-y-0 right-0 pr-3.5 flex items-center text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 cursor-pointer">
                                <x-mary-icon name="o-eye" class="w-4 h-4" x-show="!showPassword" />
                                <x-mary-icon name="o-eye-slash" class="w-4 h-4" x-show="showPassword" style="display:none" />
                            </button>
                        </div>
                    </div>

                    {{-- Remember Me --}}
                    <div class="flex items-center pt-0.5">
                        <label class="flex cursor-pointer items-center gap-2 text-xs text-slate-600 dark:text-slate-400 select-none">
                            <input type="checkbox" name="remember" class="checkbox checkbox-xs rounded border-slate-300 dark:border-slate-700" />
                            <span>Remember this workstation</span>
                        </label>
                    </div>

                    {{-- Submit Button --}}
                    <button type="submit" 
                            :disabled="submitting"
                            class="w-full py-3 px-4 rounded-xl bg-blue-900 hover:bg-blue-950 active:bg-blue-900 text-white font-bold text-sm shadow-sm transition-all flex items-center justify-center gap-2 disabled:opacity-60 disabled:cursor-not-allowed mt-2 cursor-pointer">
                        <span x-show="!submitting" class="flex items-center gap-2">
                            <span>Sign In</span>
                            <x-mary-icon name="o-arrow-right" class="w-4 h-4" />
                        </span>
                        <span x-show="submitting" class="flex items-center gap-2" style="display:none">
                            <span class="loading loading-spinner loading-xs" aria-hidden="true"></span>
                            <span>Authenticating...</span>
                        </span>
                    </button>
                </form>

                {{-- Security Notice --}}
                <div class="mt-6 pt-4 border-t border-slate-100 dark:border-slate-800 flex items-center justify-center gap-1.5 text-[11px] text-slate-400">
                    <x-mary-icon name="o-shield-check" class="w-3.5 h-3.5 text-emerald-600 dark:text-emerald-400" />
                    <span>Authorized Personnel Only</span>
                </div>
            </div>
        </main>

        {{-- Page Footer --}}
        <footer class="w-full max-w-md text-center text-xs text-slate-400 dark:text-slate-600 py-3">
            © {{ date('Y') }} Natanem Engineering & Construction PLC
        </footer>
    </div>
</x-layouts.auth>
