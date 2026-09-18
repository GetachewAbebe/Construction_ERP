import { Head, Link } from '@inertiajs/react';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { ArrowLeft, Shield, Mail, Phone, Building2, Calendar, Edit, User } from 'lucide-react';

export default function Show({ user }) {
    const roleName = user.roles?.[0]?.name || user.role || 'Staff';

    return (
        <AuthenticatedLayout title={user.name} header="Administration">
            <div className="max-w-3xl mx-auto space-y-6">
                <div className="flex items-center justify-between gap-4 pb-2 border-b border-slate-200 dark:border-slate-800">
                    <div className="flex items-center gap-3">
                        <Link
                            href="/admin/users"
                            className="p-2 rounded-xl bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 text-slate-600 dark:text-slate-300 transition-colors"
                        >
                            <ArrowLeft className="w-4 h-4" />
                        </Link>
                        <div>
                            <h1 className="text-xl font-extrabold tracking-tight text-slate-900 dark:text-white">
                                {user.name}
                            </h1>
                            <p className="text-xs text-slate-500 dark:text-slate-400 mt-0.5">
                                User credentials, role assignments, and authentication status.
                            </p>
                        </div>
                    </div>

                    <Link
                        href={`/admin/users/${user.id}/edit`}
                        className="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl bg-blue-900 hover:bg-blue-950 text-white font-bold text-xs shadow-sm transition-colors"
                    >
                        <Edit className="w-4 h-4" />
                        <span>Edit Account</span>
                    </Link>
                </div>

                <div className="rounded-2xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 p-6 shadow-xs space-y-6">
                    <div className="flex items-center gap-4 pb-6 border-b border-slate-100 dark:border-slate-800">
                        <div className="h-16 w-16 rounded-2xl bg-blue-900 text-white font-black text-2xl flex items-center justify-center">
                            {user.name?.charAt(0) || 'U'}
                        </div>
                        <div>
                            <h2 className="text-lg font-black text-slate-900 dark:text-white">
                                {user.name}
                            </h2>
                            <div className="text-xs text-slate-400 font-mono mt-0.5">
                                {user.email}
                            </div>
                            <div className="flex items-center gap-2 mt-2">
                                <span className="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-blue-50 dark:bg-blue-950/50 text-blue-900 dark:text-blue-300 border border-blue-200 dark:border-blue-900">
                                    <Shield className="w-3 h-3" />
                                    <span>{roleName}</span>
                                </span>
                                <span className="px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-50 text-emerald-600 dark:bg-emerald-950/50 dark:text-emerald-400 border border-emerald-200 dark:border-emerald-900">
                                    {user.status || 'Active'}
                                </span>
                            </div>
                        </div>
                    </div>

                    <div className="grid grid-cols-1 sm:grid-cols-2 gap-4 text-xs">
                        <div className="p-4 rounded-xl bg-slate-50 dark:bg-slate-800/50">
                            <span className="text-slate-400 block mb-1">Department / Division</span>
                            <span className="font-bold text-slate-900 dark:text-white">
                                {user.department || 'Operations HQ'}
                            </span>
                        </div>
                        <div className="p-4 rounded-xl bg-slate-50 dark:bg-slate-800/50">
                            <span className="text-slate-400 block mb-1">Position / Designation</span>
                            <span className="font-bold text-slate-900 dark:text-white">
                                {user.position || roleName}
                            </span>
                        </div>
                        <div className="p-4 rounded-xl bg-slate-50 dark:bg-slate-800/50">
                            <span className="text-slate-400 block mb-1">Account Registered</span>
                            <span className="font-bold text-slate-900 dark:text-white font-mono">
                                {user.created_at ? new Date(user.created_at).toLocaleString() : '—'}
                            </span>
                        </div>
                        <div className="p-4 rounded-xl bg-slate-50 dark:bg-slate-800/50">
                            <span className="text-slate-400 block mb-1">Email Verification</span>
                            <span className="font-bold text-emerald-600 dark:text-emerald-400 font-mono">
                                Verified
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </AuthenticatedLayout>
    );
}
