import { Head, Link, useForm } from '@inertiajs/react';
import GuestLayout from '@/Layouts/GuestLayout';
import { Mail, ArrowLeft, ArrowRight, CheckCircle, AlertTriangle } from 'lucide-react';

export default function ForgotPassword({ status }) {
    const { data, setData, post, processing, errors } = useForm({
        email: '',
    });

    const submit = (e) => {
        e.preventDefault();
        post('/forgot-password');
    };

    return (
        <GuestLayout>
            <Head title="Reset Password" />

            <div className="text-center mb-6">
                <h2 className="text-2xl font-black tracking-tight text-slate-900 dark:text-white">
                    Natanem Engineering
                </h2>
            </div>

            <div className="rounded-2xl border border-slate-200/90 dark:border-slate-800 bg-white dark:bg-slate-900 p-7 sm:p-9 shadow-xl shadow-slate-900/5 dark:shadow-black/40">
                <div className="mb-6">
                    <h1 className="text-xl font-bold tracking-tight text-slate-900 dark:text-white">
                        Reset password
                    </h1>
                    <p className="text-xs text-slate-500 dark:text-slate-400 mt-1">
                        Enter your corporate email address and we will send you a password reset link.
                    </p>
                </div>

                {status && (
                    <div className="mb-5 p-3 rounded-xl bg-emerald-50 dark:bg-emerald-950/40 text-emerald-800 dark:text-emerald-300 text-xs font-semibold border border-emerald-200 dark:border-emerald-800/50 flex items-center gap-2">
                        <CheckCircle className="w-4 h-4 shrink-0 text-emerald-600 dark:text-emerald-400" />
                        <span>{status}</span>
                    </div>
                )}

                {errors.email && (
                    <div className="mb-5 p-3.5 rounded-xl bg-rose-50 dark:bg-rose-950/40 text-rose-800 dark:text-rose-300 text-xs font-semibold border border-rose-200 dark:border-rose-800/50 flex items-start gap-2.5">
                        <AlertTriangle className="w-4 h-4 shrink-0 text-rose-600 dark:text-rose-400 mt-0.5" />
                        <span className="text-[11px] font-normal text-rose-700 dark:text-rose-300/90">
                            {errors.email}
                        </span>
                    </div>
                )}

                <form onSubmit={submit} className="space-y-4" noValidate>
                    <div>
                        <label htmlFor="email" className="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5 uppercase tracking-wider">
                            Corporate Email
                        </label>
                        <div className="relative">
                            <span className="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
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
                                className="w-full pl-10 pr-4 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50/70 dark:bg-slate-800/70 text-slate-900 dark:text-white text-sm focus:outline-none focus:ring-2 focus:ring-blue-900/20 focus:border-blue-900 dark:focus:border-blue-500 transition-all placeholder-slate-400"
                            />
                        </div>
                    </div>

                    <button
                        type="submit"
                        disabled={processing}
                        className="w-full py-3 px-4 rounded-xl bg-blue-900 hover:bg-blue-950 active:bg-blue-900 text-white font-bold text-sm shadow-sm transition-all flex items-center justify-center gap-2 disabled:opacity-60 disabled:cursor-not-allowed mt-2 cursor-pointer"
                    >
                        {processing ? (
                            <span className="flex items-center gap-2">
                                <span className="inline-block w-4 h-4 border-2 border-white/30 border-t-white rounded-full animate-spin"></span>
                                <span>Sending link...</span>
                            </span>
                        ) : (
                            <span className="flex items-center gap-2">
                                <span>Send reset link</span>
                                <ArrowRight className="w-4 h-4" />
                            </span>
                        )}
                    </button>
                </form>

                <div className="mt-6 pt-4 border-t border-slate-100 dark:border-slate-800 text-center">
                    <Link
                        href="/"
                        className="inline-flex items-center gap-1.5 text-xs font-semibold text-slate-500 hover:text-slate-800 dark:hover:text-slate-200 transition-colors"
                    >
                        <ArrowLeft className="w-3.5 h-3.5" />
                        <span>Back to sign in</span>
                    </Link>
                </div>
            </div>
        </GuestLayout>
    );
}
