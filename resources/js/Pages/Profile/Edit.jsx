import { Head, Link, useForm } from '@inertiajs/react';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import {
    User,
    Mail,
    Phone,
    Lock,
    ArrowLeft,
    Save,
    Camera,
    AlertCircle,
    CheckCircle2
} from 'lucide-react';

export default function Edit({ user = {}, status }) {
    const roleString = (user?.role || (Array.isArray(user?.roles) ? user.roles[0] : '') || '').toLowerCase();
    const isAdmin = roleString.includes('admin');
    const isHr = !isAdmin && (roleString.includes('hr') || roleString.includes('human'));
    const isInventory = !isAdmin && roleString.includes('inventory');
    const isFinance = !isAdmin && roleString.includes('financ');

    const profileUrl = (() => {
        if (isHr) return '/hr/profile';
        if (isInventory) return '/inventory/profile';
        if (isFinance) return '/finance/profile';
        return '/admin/profile';
    })();

    const profileUpdateUrl = (() => {
        if (isHr) return '/hr/profile/update';
        if (isInventory) return '/inventory/profile/update';
        if (isFinance) return '/finance/profile/update';
        return '/admin/profile/update';
    })();

    const { data, setData, post, processing, errors } = useForm({
        _method: 'PUT',
        name: user?.name || '',
        email: user?.email || '',
        phone_number: user?.phone_number || '',
        password: '',
        password_confirmation: '',
        profile_picture: null,
    });

    const submit = (e) => {
        e.preventDefault();
        post(profileUpdateUrl, {
            preserveScroll: true,
        });
    };

    return (
        <AuthenticatedLayout title="Edit Profile">
            <div className="space-y-6 max-w-4xl mx-auto">
                {/* Back link */}
                <div className="flex items-center justify-between gap-4">
                    <Link
                        href={profileUrl}
                        className="inline-flex items-center gap-2 text-xs font-bold text-slate-500 hover:text-slate-900 dark:hover:text-white transition-colors"
                    >
                        <ArrowLeft className="w-4 h-4" />
                        <span>Back to Profile Overview</span>
                    </Link>
                </div>

                {/* Form Card */}
                <div className="rounded-3xl border border-slate-200/80 dark:border-slate-800 bg-white dark:bg-slate-900 shadow-xl p-6 sm:p-10">
                    <div className="mb-6 pb-4 border-b border-slate-100 dark:border-slate-800">
                        <h1 className="text-xl sm:text-2xl font-black text-slate-900 dark:text-white">
                            Edit Profile Credentials
                        </h1>
                        <p className="text-xs text-slate-500 dark:text-slate-400 mt-1">
                            Update your personal identity, contact information, and security passcode.
                        </p>
                    </div>

                    {/* Status Alert */}
                    {status && (
                        <div className="mb-6 p-4 rounded-2xl bg-emerald-50 dark:bg-emerald-950/40 text-emerald-800 dark:text-emerald-300 text-xs font-semibold border border-emerald-200 dark:border-emerald-800/60 flex items-center gap-3">
                            <CheckCircle2 className="w-4 h-4 text-emerald-600 shrink-0" />
                            <span>{status}</span>
                        </div>
                    )}

                    {/* Errors Alert */}
                    {Object.keys(errors).length > 0 && (
                        <div className="mb-6 p-4 rounded-2xl bg-rose-50 dark:bg-rose-950/40 text-rose-800 dark:text-rose-300 text-xs font-semibold border border-rose-200 dark:border-rose-800/60 flex items-start gap-3">
                            <AlertCircle className="w-4 h-4 text-rose-600 shrink-0 mt-0.5" />
                            <div>
                                <span className="font-bold block">Please resolve the following:</span>
                                <span className="text-[11px] block mt-0.5">{Object.values(errors)[0]}</span>
                            </div>
                        </div>
                    )}

                    <form onSubmit={submit} className="space-y-6">
                        <div className="grid grid-cols-1 sm:grid-cols-2 gap-5">
                            {/* Full Name */}
                            <div>
                                <label className="block text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300 mb-1.5">
                                    Full Name
                                </label>
                                <input
                                    type="text"
                                    value={data.name}
                                    onChange={(e) => setData('name', e.target.value)}
                                    required
                                    className="w-full px-4 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50/60 dark:bg-slate-800/50 text-slate-900 dark:text-white text-sm focus:ring-2 focus:ring-blue-600"
                                />
                            </div>

                            {/* Corporate Email */}
                            <div>
                                <label className="block text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300 mb-1.5">
                                    Corporate Email
                                </label>
                                <input
                                    type="email"
                                    value={data.email}
                                    onChange={(e) => setData('email', e.target.value)}
                                    required
                                    className="w-full px-4 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50/60 dark:bg-slate-800/50 text-slate-900 dark:text-white text-sm focus:ring-2 focus:ring-blue-600"
                                />
                            </div>

                            {/* Phone Number */}
                            <div>
                                <label className="block text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300 mb-1.5">
                                    Phone Number
                                </label>
                                <input
                                    type="text"
                                    value={data.phone_number}
                                    onChange={(e) => setData('phone_number', e.target.value)}
                                    className="w-full px-4 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50/60 dark:bg-slate-800/50 text-slate-900 dark:text-white text-sm focus:ring-2 focus:ring-blue-600"
                                />
                            </div>

                            {/* Profile Picture */}
                            <div>
                                <label className="block text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300 mb-1.5">
                                    Profile Picture
                                </label>
                                <input
                                    type="file"
                                    accept="image/*"
                                    onChange={(e) => setData('profile_picture', e.target.files[0])}
                                    className="w-full text-xs text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-blue-50 file:text-blue-700 dark:file:bg-slate-800 dark:file:text-slate-200"
                                />
                            </div>
                        </div>

                        {/* Security Section */}
                        <div className="pt-6 border-t border-slate-100 dark:border-slate-800 space-y-4">
                            <h2 className="text-sm font-bold uppercase tracking-wider text-slate-900 dark:text-white flex items-center gap-2">
                                <Lock className="w-4 h-4 text-blue-600" />
                                <span>Security &amp; Passcode (Optional)</span>
                            </h2>
                            <p className="text-xs text-slate-400">
                                Leave blank if you do not want to alter your password.
                            </p>

                            <div className="grid grid-cols-1 sm:grid-cols-2 gap-5">
                                <div>
                                    <label className="block text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300 mb-1.5">
                                        New Password
                                    </label>
                                    <input
                                        type="password"
                                        value={data.password}
                                        onChange={(e) => setData('password', e.target.value)}
                                        placeholder="••••••••••••"
                                        className="w-full px-4 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50/60 dark:bg-slate-800/50 text-slate-900 dark:text-white text-sm focus:ring-2 focus:ring-blue-600"
                                    />
                                </div>

                                <div>
                                    <label className="block text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300 mb-1.5">
                                        Confirm Password
                                    </label>
                                    <input
                                        type="password"
                                        value={data.password_confirmation}
                                        onChange={(e) => setData('password_confirmation', e.target.value)}
                                        placeholder="••••••••••••"
                                        className="w-full px-4 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50/60 dark:bg-slate-800/50 text-slate-900 dark:text-white text-sm focus:ring-2 focus:ring-blue-600"
                                    />
                                </div>
                            </div>
                        </div>

                        {/* Submit Button */}
                        <div className="flex justify-end pt-4 border-t border-slate-100 dark:border-slate-800">
                            <button
                                type="submit"
                                disabled={processing}
                                className="inline-flex items-center gap-2 px-6 py-3 rounded-2xl bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs shadow-lg shadow-blue-600/25 transition-all disabled:opacity-50 cursor-pointer"
                            >
                                <Save className="w-4 h-4" />
                                <span>Save Changes</span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </AuthenticatedLayout>
    );
}
