import { useState } from 'react';
import { Head, Link, router } from '@inertiajs/react';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import {
    CalendarDays,
    PlusCircle,
    Search,
    CheckCircle2,
    XCircle,
    Clock,
    Eye,
    Check,
    X,
    Filter,
} from 'lucide-react';

export default function Index({
    requests,
    approved,
    pendingCount = 0,
    approvedCount = 0,
    rejectedCount = 0,
    view = 'active',
    q = '',
    status = '',
}) {
    const [search, setSearch] = useState(q);
    const [filterStatus, setFilterStatus] = useState(status);

    const handleFilter = (e) => {
        if (e) e.preventDefault();
        router.get('/hr/leaves', {
            view,
            q: search,
            status: filterStatus,
        }, {
            preserveState: true,
            preserveScroll: true,
        });
    };

    const handleApprove = (id) => {
        if (confirm('Approve this leave request?')) {
            router.post(`/admin/leave/${id}/approve`);
        }
    };

    const handleReject = (id) => {
        const reason = prompt('Please specify the reason for rejecting this leave request:');
        if (reason !== null) {
            router.post(`/admin/leave/${id}/reject`, { reason });
        }
    };

    const isLogs = view === 'logs';
    const activeList = requests?.data || [];
    const logsList = approved?.data || [];

    return (
        <AuthenticatedLayout title="Leave Applications" header="Human Resources">
            <div className="space-y-6">
                {/* Header */}
                <div className="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-2 border-b border-slate-200 dark:border-slate-800">
                    <div>
                        <h1 className="text-xl sm:text-2xl font-extrabold tracking-tight text-slate-900 dark:text-white">
                            Time-Off & Leave Management
                        </h1>
                        <p className="text-xs sm:text-sm text-slate-500 dark:text-slate-400 mt-0.5">
                            Process employee leave requests, view approval status, and audit leave rosters.
                        </p>
                    </div>

                    <Link
                        href="/hr/leaves/create"
                        className="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl bg-blue-900 hover:bg-blue-950 text-white font-bold text-xs shadow-sm transition-colors cursor-pointer self-start sm:self-auto"
                    >
                        <PlusCircle className="w-4 h-4" />
                        <span>Request Leave</span>
                    </Link>
                </div>

                {/* KPI Cards */}
                <div className="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <div className="rounded-2xl border border-amber-200 dark:border-amber-900/50 bg-amber-50/50 dark:bg-amber-950/20 p-4">
                        <div className="flex items-center justify-between">
                            <span className="text-xs font-semibold text-amber-700 dark:text-amber-400">Pending Review</span>
                            <Clock className="w-4 h-4 text-amber-600 dark:text-amber-400" />
                        </div>
                        <div className="text-2xl font-extrabold text-amber-900 dark:text-amber-200 mt-2 font-mono">
                            {pendingCount}
                        </div>
                    </div>

                    <div className="rounded-2xl border border-emerald-200 dark:border-emerald-900/50 bg-emerald-50/50 dark:bg-emerald-950/20 p-4">
                        <div className="flex items-center justify-between">
                            <span className="text-xs font-semibold text-emerald-700 dark:text-emerald-400">Approved</span>
                            <CheckCircle2 className="w-4 h-4 text-emerald-600 dark:text-emerald-400" />
                        </div>
                        <div className="text-2xl font-extrabold text-emerald-900 dark:text-emerald-200 mt-2 font-mono">
                            {approvedCount}
                        </div>
                    </div>

                    <div className="rounded-2xl border border-rose-200 dark:border-rose-900/50 bg-rose-50/50 dark:bg-rose-950/20 p-4">
                        <div className="flex items-center justify-between">
                            <span className="text-xs font-semibold text-rose-700 dark:text-rose-400">Rejected</span>
                            <XCircle className="w-4 h-4 text-rose-600 dark:text-rose-400" />
                        </div>
                        <div className="text-2xl font-extrabold text-rose-900 dark:text-rose-200 mt-2 font-mono">
                            {rejectedCount}
                        </div>
                    </div>
                </div>

                {/* Navigation Tabs */}
                <div className="flex items-center gap-2 border-b border-slate-200 dark:border-slate-800">
                    <Link
                        href="/hr/leaves?view=active"
                        className={`px-4 py-2.5 text-xs font-bold border-b-2 transition-colors -mb-px ${
                            !isLogs
                                ? 'border-blue-900 text-blue-900 dark:text-blue-400 dark:border-blue-400'
                                : 'border-transparent text-slate-500 hover:text-slate-700 dark:hover:text-slate-300'
                        }`}
                    >
                        Active Applications
                    </Link>
                    <Link
                        href="/hr/leaves?view=logs"
                        className={`px-4 py-2.5 text-xs font-bold border-b-2 transition-colors -mb-px ${
                            isLogs
                                ? 'border-blue-900 text-blue-900 dark:text-blue-400 dark:border-blue-400'
                                : 'border-transparent text-slate-500 hover:text-slate-700 dark:hover:text-slate-300'
                        }`}
                    >
                        Approved History & Logs
                    </Link>
                </div>

                {/* Filters */}
                {!isLogs && (
                    <div className="rounded-2xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 p-4 shadow-xs">
                        <form onSubmit={handleFilter} className="flex flex-col sm:flex-row gap-3">
                            <div className="relative flex-1">
                                <Search className="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-400 pointer-events-none" />
                                <input
                                    type="text"
                                    placeholder="Search by employee name..."
                                    value={search}
                                    onChange={(e) => setSearch(e.target.value)}
                                    className="w-full pl-9 pr-3 py-2 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-xs text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-900/20"
                                />
                            </div>

                            <select
                                value={filterStatus}
                                onChange={(e) => setFilterStatus(e.target.value)}
                                className="px-3 py-2 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-xs text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-900/20"
                            >
                                <option value="">All Statuses</option>
                                <option value="Pending">Pending</option>
                                <option value="Approved">Approved</option>
                                <option value="Rejected">Rejected</option>
                            </select>

                            <button
                                type="submit"
                                className="px-4 py-2 rounded-xl bg-blue-900 hover:bg-blue-950 text-white font-bold text-xs transition-colors cursor-pointer"
                            >
                                Filter
                            </button>
                        </form>
                    </div>
                )}

                {/* Content Table */}
                <div className="rounded-2xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 shadow-xs overflow-hidden">
                    <div className="overflow-x-auto">
                        {!isLogs ? (
                            <table className="w-full text-left text-xs">
                                <thead className="bg-slate-50 dark:bg-slate-800/60 text-slate-500 uppercase font-semibold text-[10px] tracking-wider border-b border-slate-200 dark:border-slate-800">
                                    <tr>
                                        <th className="py-3.5 px-4">Employee</th>
                                        <th className="py-3.5 px-4">Period</th>
                                        <th className="py-3.5 px-4">Reason / Notes</th>
                                        <th className="py-3.5 px-4">Status</th>
                                        <th className="py-3.5 px-4">Submitted</th>
                                        <th className="py-3.5 px-4 text-right">Actions</th>
                                    </tr>
                                </thead>
                                <tbody className="divide-y divide-slate-100 dark:divide-slate-800/80">
                                    {activeList.length === 0 ? (
                                        <tr>
                                            <td colSpan={6} className="py-8 text-center text-slate-400 text-xs">
                                                No leave applications found.
                                            </td>
                                        </tr>
                                    ) : (
                                        activeList.map((req) => (
                                            <tr key={req.id} className="hover:bg-slate-50/70 dark:hover:bg-slate-800/40 transition-colors">
                                                <td className="py-3 px-4">
                                                    <div className="font-bold text-slate-900 dark:text-white">
                                                        {req.employee ? `${req.employee.first_name} ${req.employee.last_name}` : 'Unknown Employee'}
                                                    </div>
                                                    <div className="text-[11px] text-slate-400 font-mono">
                                                        ID: #{req.employee?.employee_id || req.employee_id}
                                                    </div>
                                                </td>
                                                <td className="py-3 px-4">
                                                    <div className="font-semibold text-slate-800 dark:text-slate-200">
                                                        {req.start_date ? new Date(req.start_date).toLocaleDateString() : '—'} &rarr; {req.end_date ? new Date(req.end_date).toLocaleDateString() : '—'}
                                                    </div>
                                                </td>
                                                <td className="py-3 px-4 text-slate-600 dark:text-slate-300 max-w-xs truncate">
                                                    {req.reason || '—'}
                                                </td>
                                                <td className="py-3 px-4">
                                                    <span className={`inline-block px-2 py-0.5 rounded-full text-[10px] font-bold ${
                                                        req.status === 'Approved'
                                                            ? 'bg-emerald-50 dark:bg-emerald-950/50 text-emerald-600 dark:text-emerald-400 border border-emerald-200 dark:border-emerald-900'
                                                            : req.status === 'Rejected'
                                                            ? 'bg-rose-50 dark:bg-rose-950/50 text-rose-600 dark:text-rose-400 border border-rose-200 dark:border-rose-900'
                                                            : 'bg-amber-50 dark:bg-amber-950/50 text-amber-600 dark:text-amber-400 border border-amber-200 dark:border-amber-900'
                                                    }`}>
                                                        {req.status}
                                                    </span>
                                                </td>
                                                <td className="py-3 px-4 text-slate-500 font-mono text-[11px]">
                                                    {req.created_at ? new Date(req.created_at).toLocaleDateString() : '—'}
                                                </td>
                                                <td className="py-3 px-4 text-right">
                                                    <div className="flex items-center justify-end gap-1.5">
                                                        <Link
                                                            href={`/hr/leaves/${req.id}`}
                                                            className="p-1.5 rounded-lg text-slate-500 hover:text-blue-900 dark:hover:text-white hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors"
                                                            title="View Details"
                                                        >
                                                            <Eye className="w-3.5 h-3.5" />
                                                        </Link>
                                                        {req.status === 'Pending' && (
                                                            <>
                                                                <button
                                                                    onClick={() => handleApprove(req.id)}
                                                                    className="p-1.5 rounded-lg text-emerald-600 hover:bg-emerald-50 dark:hover:bg-emerald-950/40 transition-colors cursor-pointer"
                                                                    title="Approve"
                                                                >
                                                                    <Check className="w-3.5 h-3.5" />
                                                                </button>
                                                                <button
                                                                    onClick={() => handleReject(req.id)}
                                                                    className="p-1.5 rounded-lg text-rose-600 hover:bg-rose-50 dark:hover:bg-rose-950/40 transition-colors cursor-pointer"
                                                                    title="Reject"
                                                                >
                                                                    <X className="w-3.5 h-3.5" />
                                                                </button>
                                                            </>
                                                        )}
                                                    </div>
                                                </td>
                                            </tr>
                                        ))
                                    )}
                                </tbody>
                            </table>
                        ) : (
                            <table className="w-full text-left text-xs">
                                <thead className="bg-slate-50 dark:bg-slate-800/60 text-slate-500 uppercase font-semibold text-[10px] tracking-wider border-b border-slate-200 dark:border-slate-800">
                                    <tr>
                                        <th className="py-3.5 px-4">Employee</th>
                                        <th className="py-3.5 px-4">Approved By</th>
                                        <th className="py-3.5 px-4">Approved At</th>
                                    </tr>
                                </thead>
                                <tbody className="divide-y divide-slate-100 dark:divide-slate-800/80">
                                    {logsList.length === 0 ? (
                                        <tr>
                                            <td colSpan={3} className="py-8 text-center text-slate-400 text-xs">
                                                No approved leave logs found.
                                            </td>
                                        </tr>
                                    ) : (
                                        logsList.map((log) => (
                                            <tr key={log.id} className="hover:bg-slate-50/70 dark:hover:bg-slate-800/40 transition-colors">
                                                <td className="py-3 px-4 font-bold text-slate-900 dark:text-white">
                                                    {log.employee ? `${log.employee.first_name} ${log.employee.last_name}` : 'Unknown'}
                                                </td>
                                                <td className="py-3 px-4 text-slate-600 dark:text-slate-300">
                                                    {log.approver ? log.approver.name : 'System Admin'}
                                                </td>
                                                <td className="py-3 px-4 text-slate-500 font-mono text-[11px]">
                                                    {log.approved_at ? new Date(log.approved_at).toLocaleString() : '—'}
                                                </td>
                                            </tr>
                                        ))
                                    )}
                                </tbody>
                            </table>
                        )}
                    </div>

                    {/* Pagination */}
                    {((!isLogs && requests?.links) || (isLogs && approved?.links)) && (
                        <div className="px-4 py-3 border-t border-slate-200 dark:border-slate-800 flex items-center justify-between text-xs text-slate-500">
                            <div>
                                Showing {(!isLogs ? requests : approved)?.from || 0} to {(!isLogs ? requests : approved)?.to || 0} of {(!isLogs ? requests : approved)?.total || 0} records
                            </div>
                            <div className="flex items-center gap-1">
                                {(!isLogs ? requests : approved).links.map((link, idx) => (
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
