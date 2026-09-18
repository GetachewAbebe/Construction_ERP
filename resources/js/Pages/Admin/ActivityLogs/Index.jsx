import { Head, Link } from '@inertiajs/react';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { Activity, Clock, User, Shield, ArrowLeft } from 'lucide-react';

export default function Index({ logs }) {
    const logList = logs?.data || [];

    return (
        <AuthenticatedLayout title="Audit Trail" header="Administration">
            <div className="space-y-6">
                <div className="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-2 border-b border-slate-200 dark:border-slate-800">
                    <div>
                        <h1 className="text-xl sm:text-2xl font-extrabold tracking-tight text-slate-900 dark:text-white">
                            Activity Logs & Security Audit
                        </h1>
                        <p className="text-xs sm:text-sm text-slate-500 dark:text-slate-400 mt-0.5">
                            Real-time forensic ledger of administrative approvals, logins, and entity updates.
                        </p>
                    </div>
                </div>

                <div className="rounded-2xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 shadow-xs overflow-hidden">
                    <div className="overflow-x-auto">
                        <table className="w-full text-left text-xs">
                            <thead className="bg-slate-50 dark:bg-slate-800/60 text-slate-500 uppercase font-semibold text-[10px] tracking-wider border-b border-slate-200 dark:border-slate-800">
                                <tr>
                                    <th className="py-3.5 px-4">Timestamp</th>
                                    <th className="py-3.5 px-4">Actor / User</th>
                                    <th className="py-3.5 px-4">Action</th>
                                    <th className="py-3.5 px-4">Description</th>
                                    <th className="py-3.5 px-4">IP Address</th>
                                </tr>
                            </thead>
                            <tbody className="divide-y divide-slate-100 dark:divide-slate-800/80">
                                {logList.length === 0 ? (
                                    <tr>
                                        <td colSpan={5} className="py-8 text-center text-slate-400 text-xs">
                                            No security logs recorded.
                                        </td>
                                    </tr>
                                ) : (
                                    logList.map((log) => (
                                        <tr key={log.id} className="hover:bg-slate-50/70 dark:hover:bg-slate-800/40 transition-colors">
                                            <td className="py-3 px-4 font-mono text-slate-500 text-[11px]">
                                                {log.created_at ? new Date(log.created_at).toLocaleString() : '—'}
                                            </td>
                                            <td className="py-3 px-4">
                                                <div className="font-bold text-slate-900 dark:text-white">
                                                    {log.user ? log.user.name : 'System Automated Service'}
                                                </div>
                                                <div className="text-[11px] text-slate-400">
                                                    {log.user ? log.user.email : 'system@natanem.com'}
                                                </div>
                                            </td>
                                            <td className="py-3 px-4">
                                                <span className="px-2 py-0.5 rounded-md bg-blue-50 dark:bg-blue-950/50 text-blue-900 dark:text-blue-300 font-semibold text-[11px] font-mono">
                                                    {log.action || log.event || 'UPDATE'}
                                                </span>
                                            </td>
                                            <td className="py-3 px-4 text-slate-700 dark:text-slate-300 max-w-md truncate">
                                                {log.description || log.notes || 'Activity logged.'}
                                            </td>
                                            <td className="py-3 px-4 font-mono text-slate-500 text-[11px]">
                                                {log.ip_address || '127.0.0.1'}
                                            </td>
                                        </tr>
                                    ))
                                )}
                            </tbody>
                        </table>
                    </div>

                    {logs?.links && logs.links.length > 3 && (
                        <div className="px-4 py-3 border-t border-slate-200 dark:border-slate-800 flex items-center justify-between text-xs text-slate-500">
                            <div>Showing {logs.from || 0} to {logs.to || 0} of {logs.total || 0} entries</div>
                            <div className="flex items-center gap-1">
                                {logs.links.map((link, idx) => (
                                    <Link
                                        key={idx}
                                        href={link.url || '#'}
                                        preserveScroll
                                        className={`px-2.5 py-1 rounded-lg text-xs font-semibold ${
                                            link.active
                                                ? 'bg-blue-900 text-white'
                                                : link.url
                                                ? 'text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800'
                                                : 'text-slate-300 dark:text-slate-700 cursor-not-allowed'
                                        }`}
                                        dangerouslySetInnerHTML={{ __html: link.label }}
                                    />
                                ))}
                            </div>
                        </div>
                    )}
                </div>
            </div>
        </AuthenticatedLayout>
    );
}
