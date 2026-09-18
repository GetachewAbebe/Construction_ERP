import { Head, Link } from '@inertiajs/react';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { ArrowLeft, Clock, User, Shield, Activity, Globe } from 'lucide-react';

export default function Show({ log }) {
    return (
        <AuthenticatedLayout title="Audit Log Detail" header="Administration">
            <Head title={`Audit Log #${log.id}`} />

            <div className="max-w-3xl mx-auto space-y-6">
                <div className="flex items-center justify-between pb-2 border-b border-slate-200 dark:border-slate-800">
                    <div className="flex items-center gap-3">
                        <Link
                            href="/admin/activity-logs"
                            className="p-1.5 rounded-lg text-slate-400 hover:text-slate-700 dark:hover:text-slate-200 hover:bg-slate-100 dark:hover:bg-slate-800 transition"
                        >
                            <ArrowLeft className="w-4 h-4" />
                        </Link>
                        <div>
                            <h1 className="text-xl sm:text-2xl font-extrabold tracking-tight text-slate-900 dark:text-white">
                                Audit Log Record #{log.id}
                            </h1>
                            <p className="text-xs sm:text-sm text-slate-500 dark:text-slate-400">
                                Recorded at {log.created_at ? new Date(log.created_at).toLocaleString() : '—'}
                            </p>
                        </div>
                    </div>
                </div>

                <div className="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 p-6 shadow-sm space-y-5">
                    <div className="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div className="p-4 rounded-xl bg-slate-50 dark:bg-slate-800/40">
                            <div className="flex items-center gap-2 text-slate-400 text-xs font-bold uppercase tracking-wider mb-1">
                                <User className="w-3.5 h-3.5 text-blue-600" />
                                <span>Actor</span>
                            </div>
                            <div className="font-bold text-sm text-slate-900 dark:text-white">
                                {log.user?.name || 'System Automated Service'}
                            </div>
                            <div className="text-xs text-slate-400 font-mono">
                                {log.user?.email || 'N/A'}
                            </div>
                        </div>

                        <div className="p-4 rounded-xl bg-slate-50 dark:bg-slate-800/40">
                            <div className="flex items-center gap-2 text-slate-400 text-xs font-bold uppercase tracking-wider mb-1">
                                <Activity className="w-3.5 h-3.5 text-emerald-600" />
                                <span>Action Type</span>
                            </div>
                            <span className="inline-flex px-2.5 py-1 rounded-md bg-blue-50 dark:bg-blue-950/50 text-blue-900 dark:text-blue-300 font-mono font-bold text-xs">
                                {log.action || log.event || 'OPERATION'}
                            </span>
                        </div>

                        <div className="p-4 rounded-xl bg-slate-50 dark:bg-slate-800/40 sm:col-span-2">
                            <div className="flex items-center gap-2 text-slate-400 text-xs font-bold uppercase tracking-wider mb-1">
                                <Shield className="w-3.5 h-3.5 text-purple-600" />
                                <span>Description / Payload</span>
                            </div>
                            <div className="text-sm text-slate-800 dark:text-slate-200">
                                {log.description || log.notes || 'No description recorded.'}
                            </div>
                        </div>

                        <div className="p-4 rounded-xl bg-slate-50 dark:bg-slate-800/40 sm:col-span-2">
                            <div className="flex items-center gap-2 text-slate-400 text-xs font-bold uppercase tracking-wider mb-1">
                                <Globe className="w-3.5 h-3.5 text-amber-600" />
                                <span>Network Provenance</span>
                            </div>
                            <div className="font-mono text-xs text-slate-700 dark:text-slate-300">
                                IP: {log.ip_address || '127.0.0.1'} • User Agent: {log.user_agent || 'Standard Client'}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </AuthenticatedLayout>
    );
}
