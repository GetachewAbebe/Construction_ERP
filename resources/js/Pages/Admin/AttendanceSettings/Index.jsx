import { Head, Link, useForm } from '@inertiajs/react';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import {
    Clock,
    Sliders,
    Save,
    ArrowLeft,
    CheckCircle2,
    AlertCircle,
    Info,
    Calendar,
} from 'lucide-react';

export default function Index({ settings = {} }) {
    const { data, setData, post, processing, errors, recentlySuccessful } = useForm({
        shift_start_time: settings.shift_start_time || '09:00',
        shift_end_time: settings.shift_end_time || '17:00',
        grace_period_minutes: settings.grace_period_minutes || 15,
    });

    const handleSubmit = (e) => {
        e.preventDefault();
        post('/admin/attendance-settings');
    };

    return (
        <AuthenticatedLayout title="Attendance Rules" header="System Administration">
            <Head title="Attendance Shift Parameters & Rules" />

            <div className="max-w-3xl mx-auto space-y-6">
                {/* Header */}
                <div className="flex items-center justify-between gap-4 pb-2 border-b border-slate-200 dark:border-slate-800">
                    <div className="flex items-center gap-2">
                        <Link
                            href="/admin"
                            className="p-1.5 rounded-lg text-slate-400 hover:text-slate-700 dark:hover:text-slate-200 hover:bg-slate-100 dark:hover:bg-slate-800 transition"
                        >
                            <ArrowLeft className="w-4 h-4" />
                        </Link>
                        <div>
                            <h1 className="text-xl sm:text-2xl font-extrabold tracking-tight text-slate-900 dark:text-white">
                                Shift & Attendance Policies
                            </h1>
                            <p className="text-xs sm:text-sm text-slate-500 dark:text-slate-400">
                                Operational work hours, tardiness grace periods, and attendance scoring parameters.
                            </p>
                        </div>
                    </div>
                </div>

                {recentlySuccessful && (
                    <div className="flex items-center gap-2 p-4 rounded-xl bg-emerald-50 dark:bg-emerald-950/30 border border-emerald-200 dark:border-emerald-800 text-emerald-800 dark:text-emerald-300 text-xs sm:text-sm">
                        <CheckCircle2 className="w-4 h-4 shrink-0 text-emerald-600" />
                        <span>Attendance policies successfully updated and synchronized across all tracking engines.</span>
                    </div>
                )}

                <form onSubmit={handleSubmit} className="space-y-6">
                    {/* Shift Timing Card */}
                    <div className="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 p-6 shadow-sm space-y-5">
                        <div className="flex items-center gap-2 pb-3 border-b border-slate-100 dark:border-slate-800 text-slate-900 dark:text-white font-bold text-sm">
                            <Clock className="w-4 h-4 text-blue-600" />
                            <span>Standard Operational Shift Window</span>
                        </div>

                        <div className="grid grid-cols-1 sm:grid-cols-2 gap-5">
                            <div>
                                <label className="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">
                                    Morning Shift Start Time (Clock-In) <span className="text-rose-500">*</span>
                                </label>
                                <input
                                    type="time"
                                    required
                                    value={data.shift_start_time}
                                    onChange={e => setData('shift_start_time', e.target.value)}
                                    className={`w-full text-sm font-mono rounded-xl border ${
                                        errors.shift_start_time ? 'border-rose-500' : 'border-slate-200 dark:border-slate-700'
                                    } bg-white dark:bg-slate-800 text-slate-900 dark:text-white py-2 px-3 focus:outline-none focus:ring-2 focus:ring-blue-500`}
                                />
                                {errors.shift_start_time && (
                                    <p className="text-rose-500 text-[11px] mt-1">{errors.shift_start_time}</p>
                                )}
                                <p className="text-[11px] text-slate-400 mt-1">Personnel expected on site by this time.</p>
                            </div>

                            <div>
                                <label className="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">
                                    Evening Shift End Time (Clock-Out) <span className="text-rose-500">*</span>
                                </label>
                                <input
                                    type="time"
                                    required
                                    value={data.shift_end_time}
                                    onChange={e => setData('shift_end_time', e.target.value)}
                                    className={`w-full text-sm font-mono rounded-xl border ${
                                        errors.shift_end_time ? 'border-rose-500' : 'border-slate-200 dark:border-slate-700'
                                    } bg-white dark:bg-slate-800 text-slate-900 dark:text-white py-2 px-3 focus:outline-none focus:ring-2 focus:ring-blue-500`}
                                />
                                {errors.shift_end_time && (
                                    <p className="text-rose-500 text-[11px] mt-1">{errors.shift_end_time}</p>
                                )}
                                <p className="text-[11px] text-slate-400 mt-1">Official close of business day.</p>
                            </div>
                        </div>
                    </div>

                    {/* Lateness Tolerance Card */}
                    <div className="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 p-6 shadow-sm space-y-5">
                        <div className="flex items-center gap-2 pb-3 border-b border-slate-100 dark:border-slate-800 text-slate-900 dark:text-white font-bold text-sm">
                            <Sliders className="w-4 h-4 text-amber-600" />
                            <span>Tardiness Tolerance & Grace Period</span>
                        </div>

                        <div>
                            <label className="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">
                                Lateness Grace Period (Minutes) <span className="text-rose-500">*</span>
                            </label>
                            <input
                                type="number"
                                min="0"
                                max="120"
                                required
                                value={data.grace_period_minutes}
                                onChange={e => setData('grace_period_minutes', parseInt(e.target.value, 10) || 0)}
                                className={`w-40 text-sm font-mono rounded-xl border ${
                                    errors.grace_period_minutes ? 'border-rose-500' : 'border-slate-200 dark:border-slate-700'
                                } bg-white dark:bg-slate-800 text-slate-900 dark:text-white py-2 px-3 focus:outline-none focus:ring-2 focus:ring-blue-500`}
                            />
                            {errors.grace_period_minutes && (
                                <p className="text-rose-500 text-[11px] mt-1">{errors.grace_period_minutes}</p>
                            )}
                            <p className="text-[11px] text-slate-400 mt-1">
                                Check-ins recorded after {data.shift_start_time} plus this grace duration will automatically be flagged as <strong>LATE</strong>.
                            </p>
                        </div>

                        {/* Visual Timeline representation */}
                        <div className="p-4 rounded-xl bg-slate-50 dark:bg-slate-800/50 border border-slate-200/80 dark:border-slate-800 flex items-center gap-3">
                            <Info className="w-4 h-4 text-blue-600 shrink-0" />
                            <div className="text-xs text-slate-600 dark:text-slate-300">
                                Personnel clocking in between <strong>{data.shift_start_time}</strong> and <strong>+{data.grace_period_minutes} min</strong> are credited on-time. Beyond that threshold, sessions are classified as Late Arrival.
                            </div>
                        </div>
                    </div>

                    {/* Actions */}
                    <div className="flex items-center justify-end gap-3 pt-2">
                        <Link
                            href="/admin"
                            className="px-4 py-2 rounded-xl text-xs font-semibold text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 transition"
                        >
                            Cancel
                        </Link>
                        <button
                            type="submit"
                            disabled={processing}
                            className="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold transition shadow-sm disabled:opacity-50"
                        >
                            <Save className="w-4 h-4" />
                            <span>{processing ? 'Applying...' : 'Apply Shift Policies'}</span>
                        </button>
                    </div>
                </form>
            </div>
        </AuthenticatedLayout>
    );
}
