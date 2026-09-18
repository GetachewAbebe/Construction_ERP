import { Head, Link, router } from '@inertiajs/react';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { CalendarDays, ArrowLeft, Check, X, User, Clock, FileText } from 'lucide-react';

export default function Show({ leave }) {
    const handleApprove = () => {
        if (confirm('Approve this leave request?')) {
            router.post(`/admin/leave/${leave.id}/approve`);
        }
    };

    const handleReject = () => {
        const reason = prompt('Please specify the reason for rejecting this leave request:');
        if (reason !== null) {
            router.post(`/admin/leave/${leave.id}/reject`, { reason });
        }
    };

    return (
        <AuthenticatedLayout title={`Leave Request #${leave.id}`} header="Human Resources">
            <div className="max-w-3xl mx-auto space-y-6">
                <div className="flex items-center justify-between gap-4 pb-2 border-b border-slate-200 dark:border-slate-800">
                    <div className="flex items-center gap-3">
                        <Link
                            href="/hr/leaves"
                            className="p-2 rounded-xl bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 text-slate-600 dark:text-slate-300 transition-colors"
                        >
                            <ArrowLeft className="w-4 h-4" />
                        </Link>
                        <div>
                            <h1 className="text-xl font-extrabold tracking-tight text-slate-900 dark:text-white">
                                Leave Application #{leave.id}
                            </h1>
                            <p className="text-xs text-slate-500 dark:text-slate-400 mt-0.5">
                                Review detailed request parameters and disposition.
                            </p>
                        </div>
                    </div>

                    {leave.status === 'Pending' && (
                        <div className="flex items-center gap-2">
                            <button
                                onClick={handleApprove}
                                className="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs shadow-sm transition-colors cursor-pointer"
                            >
                                <Check className="w-4 h-4" />
                                <span>Approve</span>
                            </button>
                            <button
                                onClick={handleReject}
                                className="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl bg-rose-600 hover:bg-rose-700 text-white font-bold text-xs shadow-sm transition-colors cursor-pointer"
                            >
                                <X className="w-4 h-4" />
                                <span>Reject</span>
                            </button>
                        </div>
                    )}
                </div>

                <div className="rounded-2xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 p-6 shadow-xs space-y-6">
                    <div className="flex items-center justify-between pb-4 border-b border-slate-100 dark:border-slate-800">
                        <div className="flex items-center gap-3">
                            <div className="h-10 w-10 rounded-xl bg-blue-900 text-white font-bold flex items-center justify-center">
                                {leave.employee?.first_name?.charAt(0) || 'E'}
                            </div>
                            <div>
                                <div className="font-bold text-slate-900 dark:text-white">
                                    {leave.employee?.first_name} {leave.employee?.last_name}
                                </div>
                                <div className="text-xs text-slate-400">
                                    {leave.employee?.email || 'No email provided'}
                                </div>
                            </div>
                        </div>

                        <span className={`px-3 py-1 rounded-full text-xs font-bold ${
                            leave.status === 'Approved'
                                ? 'bg-emerald-50 dark:bg-emerald-950/50 text-emerald-600 dark:text-emerald-400 border border-emerald-200 dark:border-emerald-900'
                            : leave.status === 'Rejected'
                                ? 'bg-rose-50 dark:bg-rose-950/50 text-rose-600 dark:text-rose-400 border border-rose-200 dark:border-rose-900'
                                : 'bg-amber-50 dark:bg-amber-950/50 text-amber-600 dark:text-amber-400 border border-amber-200 dark:border-amber-900'
                        }`}>
                            {leave.status}
                        </span>
                    </div>

                    <div className="grid grid-cols-1 sm:grid-cols-2 gap-4 text-xs">
                        <div className="p-4 rounded-xl bg-slate-50 dark:bg-slate-800/50">
                            <span className="text-slate-400 block mb-1">Start Date</span>
                            <span className="font-bold text-slate-900 dark:text-white font-mono text-sm">
                                {leave.start_date ? new Date(leave.start_date).toDateString() : '—'}
                            </span>
                        </div>

                        <div className="p-4 rounded-xl bg-slate-50 dark:bg-slate-800/50">
                            <span className="text-slate-400 block mb-1">End Date</span>
                            <span className="font-bold text-slate-900 dark:text-white font-mono text-sm">
                                {leave.end_date ? new Date(leave.end_date).toDateString() : '—'}
                            </span>
                        </div>
                    </div>

                    <div className="p-4 rounded-xl bg-slate-50 dark:bg-slate-800/50 text-xs">
                        <span className="text-slate-400 block mb-1">Reason / Comments</span>
                        <p className="text-slate-800 dark:text-slate-200 leading-relaxed">
                            {leave.reason || 'No detailed reason was attached to this request.'}
                        </p>
                    </div>

                    {leave.rejection_reason && (
                        <div className="p-4 rounded-xl bg-rose-50 dark:bg-rose-950/30 border border-rose-200 dark:border-rose-900/50 text-xs">
                            <span className="text-rose-500 font-bold block mb-1">Rejection Reason</span>
                            <p className="text-rose-800 dark:text-rose-300">
                                {leave.rejection_reason}
                            </p>
                        </div>
                    )}
                </div>
            </div>
        </AuthenticatedLayout>
    );
}
