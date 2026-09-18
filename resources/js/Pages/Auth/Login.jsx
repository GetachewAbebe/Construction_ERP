import { useState } from 'react';
import { Head, Link, useForm } from '@inertiajs/react';
import GuestLayout from '@/Layouts/GuestLayout';
import { 
    Mail, 
    Lock, 
    Eye, 
    EyeOff, 
    ArrowRight, 
    CheckCircle2, 
    AlertCircle
} from 'lucide-react';

export default function Login({ status }) {
    const [showPassword, setShowPassword] = useState(false);

    const { data, setData, post, processing, errors, reset } = useForm({
        email: '',
        password: '',
        remember: false,
    });

    const submit = (e) => {
        e.preventDefault();
        post('/', {
            onFinish: () => reset('password'),
        });
    };

    return (
        <GuestLayout>
            <Head title="Sign In — Natanem Engineering" />

            {/* Header / Brand Identity */}
            <div className="text-center mb-8">
                <h1 className="text-3xl font-extrabold tracking-tight text-slate-900 dark:text-white">
                    Natanem Engineering
                </h1>
            </div>

            {/* Form Card */}
            <div className="rounded-3xl border border-slate-200/80 dark:border-slate-800/80 bg-white/95 dark:bg-slate-900/90 p-8 sm:p-10 shadow-xl shadow-slate-900/5 dark:shadow-black/50 backdrop-blur-xl">
                {/* Status Alert */}
                {status && (
                    <div className="mb-6 p-4 rounded-2xl bg-emerald-50 dark:bg-emerald-950/40 text-emerald-800 dark:text-emerald-300 text-xs font-semibold border border-emerald-200 dark:border-emerald-800/60 flex items-center gap-3">
                        <CheckCircle2 className="w-4 h-4 shrink-0 text-emerald-600 dark:text-emerald-400" />
                        <span>{status}</span>
                    </div>
                )}

                {/* Validation Error Alert */}
                {Object.keys(errors).length > 0 && (
                    <div className="mb-6 p-4 rounded-2xl bg-rose-50 dark:bg-rose-950/40 text-rose-800 dark:text-rose-300 text-xs font-semibold border border-rose-200 dark:border-rose-800/60 flex items-start gap-3">
                        <AlertCircle className="w-4 h-4 shrink-0 text-rose-600 dark:text-rose-400 mt-0.5" />
                        <div>
                            <span className="block font-bold">Unable to sign in</span>
                            <span className="text-[11px] font-normal text-rose-700 dark:text-rose-300/90 mt-0.5 block">
                                {errors.email || errors.password || Object.values(errors)[0]}
                            </span>
                        </div>
                    </div>
                )}

                <form onSubmit={submit} className="space-y-5" noValidate>
                    {/* Corporate Email */}
                    <div>
                        <label 
                            htmlFor="email" 
                            className="block text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300 mb-2"
                        >
                            Corporate Email
                        </label>
                        <div className="relative">
                            <span className="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-slate-400">
                                <Mail className="w-4 h-4" />
                            </span>
                            <input
                                id="email"
                                type="email"
                                name="email"
                                value={data.email}
                                onChange={(e) => setData('email', e.target.value)}
                                required
                                autoFocus
                                autoComplete="username"
                                placeholder="name@natanemengineering.com"
                                className="w-full pl-11 pr-4 py-3 rounded-2xl border border-slate-200 dark:border-slate-700/80 bg-slate-50/60 dark:bg-slate-800/50 text-slate-900 dark:text-white text-sm focus:outline-none focus:ring-2 focus:ring-blue-600/30 focus:border-blue-600 dark:focus:border-blue-500 transition-all placeholder-slate-400"
                            />
                        </div>
                    </div>

                    {/* Password */}
                    <div>
                        <div className="flex items-center justify-between mb-2">
                            <label 
                                htmlFor="password" 
                                className="block text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300"
                            >
                                Password
                            </label>
                            <Link
                                href="/forgot-password"
                                className="text-xs font-semibold text-blue-700 dark:text-blue-400 hover:text-blue-800 dark:hover:text-blue-300 transition-colors"
                            >
                                Forgot password?
                            </Link>
                        </div>
                        <div className="relative">
                            <span className="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-slate-400">
                                <Lock className="w-4 h-4" />
                            </span>
                            <input
                                id="password"
                                type={showPassword ? 'text' : 'password'}
                                name="password"
                                value={data.password}
                                onChange={(e) => setData('password', e.target.value)}
                                required
                                autoComplete="current-password"
                                placeholder="••••••••••••"
                                className="w-full pl-11 pr-11 py-3 rounded-2xl border border-slate-200 dark:border-slate-700/80 bg-slate-50/60 dark:bg-slate-800/50 text-slate-900 dark:text-white text-sm focus:outline-none focus:ring-2 focus:ring-blue-600/30 focus:border-blue-600 dark:focus:border-blue-500 transition-all placeholder-slate-400"
                            />
                            <button
                                type="button"
                                onClick={() => setShowPassword(!showPassword)}
                                aria-label={showPassword ? 'Hide password' : 'Show password'}
                                className="absolute inset-y-0 right-0 pr-3.5 flex items-center text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 transition-colors cursor-pointer"
                            >
                                {showPassword ? (
                                    <EyeOff className="w-4 h-4" />
                                ) : (
                                    <Eye className="w-4 h-4" />
                                )}
                            </button>
                        </div>
                    </div>

                    {/* Remember Me */}
                    <div className="flex items-center pt-1">
                        <label className="flex cursor-pointer items-center gap-2.5 text-xs font-medium text-slate-600 dark:text-slate-400 select-none">
                            <input
                                type="checkbox"
                                name="remember"
                                checked={data.remember}
                                onChange={(e) => setData('remember', e.target.checked)}
                                className="w-4 h-4 rounded-md border-slate-300 dark:border-slate-700 text-blue-900 focus:ring-blue-800 cursor-pointer"
                            />
                            <span>Keep me signed in on this device</span>
                        </label>
                    </div>

                    {/* Submit Button */}
                    <button
                        type="submit"
                        disabled={processing}
                        className="w-full py-3.5 px-6 rounded-2xl bg-gradient-to-r from-blue-900 via-blue-800 to-indigo-900 hover:from-blue-950 hover:via-blue-900 hover:to-indigo-950 text-white font-bold text-sm shadow-lg shadow-blue-950/25 active:scale-[0.99] transition-all flex items-center justify-center gap-2.5 disabled:opacity-60 disabled:cursor-not-allowed mt-3 cursor-pointer"
                    >
                        {processing ? (
                            <span className="flex items-center gap-2">
                                <span className="inline-block w-4 h-4 border-2 border-white/30 border-t-white rounded-full animate-spin"></span>
                                <span>Signing in...</span>
                            </span>
                        ) : (
                            <span className="flex items-center gap-2">
                                <span>Sign In to Workspace</span>
                                <ArrowRight className="w-4 h-4" />
                            </span>
                        )}
                    </button>
                </form>
            </div>
        </GuestLayout>
    );
}
