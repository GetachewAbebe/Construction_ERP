import { useState } from 'react';
import { Head, router } from '@inertiajs/react';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import {
    CalendarCheck,
    Clock,
    UserCheck,
    AlertCircle,
    CheckCircle2,
    XCircle,
    Building2,
    ChevronRight,
    Calendar,
    Users,
    Shield,
    FileText,
} from 'lucide-react';

export default function LeaveApprovals({ pending = { data: [] }, approved = { data: [] }, stats = {} }) {
    const [activeTab, setActiveTab] = useState('pending');
    const [processingId, setProcessingId] = useState(null);
    const [actionType, setActionType] = useState(null); // 'approve' | 'reject'
    const [selectedLeave, setSelectedLeave] = useState(null);

    const pendingList = pending.data || [];
    const approvedList = approved.data || [];

    const handleConfirmAction = () => {
        if (!selectedLeave || !actionType) return;
        setProcessingId(selectedLeave.id);

        const url = actionType === 'approve'
            ? `/admin/requests/leave/${selectedLeave.id}/approve`
            : `/admin/requests/leave/${selectedLeave.id}/reject`;

        router.post(url, {}, {
            onFinish: () => {
                setProcessingId(null);
                setSelectedLeave(null);
                setActionType(null);
            },
        });
    };

    const calculateDays = (start, end) => {
        if (!start || !end) return 1;
        const s = new Date(start);
        const e = new Date(end);
        const diffTime = Math.abs(e - s);
        const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24)) + 1;
        return diffDays || 1;
    };

    return (
        <AuthenticatedLayout title="Leave Approvals — Administration">
            <div className="space-y-6">
                {/* PAGE HEADER */}
                <div className="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                    <div>
                        <div className="flex items-center gap-2 text-xs font-semibold text-blue-600 dark:text-blue-400 uppercase tracking-wider mb-1">
                            <Shield className="w-3.5 h-3.5" />
                            <span>Executive Governance</span>
                        </div>
                        <h1 className="text-2xl sm:text-3xl font-extrabold tracking-tight text-slate-900 dark:text-white">
                            Leave Administration
                        </h1>
                        <p className="text-xs sm:text-sm text-slate-500 dark:text-slate-400 mt-1">
                            Review, adjudicate, and audit workforce absence requests across all construction divisions.
                        </p>
                    </div>

                    {/* TABS SELECTOR */}
                    <div className="flex items-center gap-1.5 p-1 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl shadow-sm">
                        <button
                            onClick={() => setActiveTab('pending')}
                            className={`flex items-center gap-2 px-4 py-2 rounded-xl text-xs font-bold transition-all cursor-pointer ${
                                activeTab === 'pending'
                                    ? 'bg-blue-600 text-white shadow-md shadow-blue-600/20'
                                    : 'text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800'
                            }`}
                        >
                            <Clock className="w-3.5 h-3.5" />
                            <span>Pending Adjudication</span>
                            {pendingList.length > 0 && (
                                <span className="px-1.5 py-0.2 rounded-full bg-rose-500 text-white text-[10px] font-extrabold">
                                    {pending.total || pendingList.length}
                                </span>
                            )}
                        </button>
                        <button
                            onClick={() => setActiveTab('archive')}
                            className={`flex items-center gap-2 px-4 py-2 rounded-xl text-xs font-bold transition-all cursor-pointer ${
                                activeTab === 'archive'
                                    ? 'bg-blue-600 text-white shadow-md shadow-blue-600/20'
                                    : 'text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800'
                            }`}
                        >
                            <CheckCircle2 className="w-3.5 h-3.5" />
                            <span>Authorization Archive</span>
                            <span className="text-[11px] opacity-75 font-semibold">
                                ({approved.total || approvedList.length})
                            </span>
                        </button>
                    </div>
                </div>

                {/* KPI STATS ROW */}
                <div className="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <div className="p-4 sm:p-5 rounded-2xl bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 shadow-sm flex items-center justify-between">
                        <div>
                            <div className="text-xs font-semibold uppercase tracking-wider text-slate-400">
                                Pending Action
                            </div>
                            <div className="text-2xl sm:text-3xl font-black text-slate-900 dark:text-white mt-1">
                                {stats.pending_count || pendingList.length}
                            </div>
                            <div className="text-[11px] text-amber-500 font-medium mt-0.5">
                                Requires executive review
                            </div>
                        </div>
                        <div className="w-12 h-12 rounded-2xl bg-amber-50 dark:bg-amber-950/40 text-amber-600 flex items-center justify-center border border-amber-200 dark:border-amber-800/40">
                            <Clock className="w-6 h-6" />
                        </div>
                    </div>

                    <div className="p-4 sm:p-5 rounded-2xl bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 shadow-sm flex items-center justify-between">
                        <div>
                            <div className="text-xs font-semibold uppercase tracking-wider text-slate-400">
                                On Leave Today
                            </div>
                            <div className="text-2xl sm:text-3xl font-black text-slate-900 dark:text-white mt-1">
                                {stats.active_on_leave_today || 0}
                            </div>
                            <div className="text-[11px] text-emerald-500 font-medium mt-0.5">
                                Active authorized absence
                            </div>
                        </div>
                        <div className="w-12 h-12 rounded-2xl bg-emerald-50 dark:bg-emerald-950/40 text-emerald-600 flex items-center justify-center border border-emerald-200 dark:border-emerald-800/40">
                            <UserCheck className="w-6 h-6" />
                        </div>
                    </div>

                    <div className="p-4 sm:p-5 rounded-2xl bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 shadow-sm flex items-center justify-between">
                        <div>
                            <div className="text-xs font-semibold uppercase tracking-wider text-slate-400">
                                Authorized This Month
                            </div>
                            <div className="text-2xl sm:text-3xl font-black text-slate-900 dark:text-white mt-1">
                                {stats.approved_this_month || approvedList.length}
                            </div>
                            <div className="text-[11px] text-blue-500 font-medium mt-0.5">
                                Current calendar cycle
                            </div>
                        </div>
                        <div className="w-12 h-12 rounded-2xl bg-blue-50 dark:bg-blue-950/40 text-blue-600 flex items-center justify-center border border-blue-200 dark:border-blue-800/40">
                            <CalendarCheck className="w-6 h-6" />
                        </div>
                    </div>
                </div>

                {/* TAB 1: PENDING ADJUDICATIONS */}
                {activeTab === 'pending' && (
                    <div className="rounded-2xl sm:rounded-3xl bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 shadow-xl overflow-hidden">
                        <div className="px-6 py-4 border-b border-slate-100 dark:border-slate-800 flex items-center justify-between">
                            <div className="flex items-center gap-2.5">
                                <span className="w-2.5 h-2.5 rounded-full bg-amber-500 animate-pulse" />
                                <h3 className="font-extrabold text-sm text-slate-900 dark:text-white uppercase tracking-wider">
                                    Requests Awaiting Decision
                                </h3>
                            </div>
                            <span className="text-xs font-semibold text-slate-400">
                                {pendingList.length} total request{pendingList.length === 1 ? '' : 's'}
                            </span>
                        </div>

                        {pendingList.length === 0 ? (
                            <div className="p-12 text-center">
                                <div className="w-14 h-14 rounded-2xl bg-emerald-50 dark:bg-emerald-950/30 text-emerald-600 flex items-center justify-center mx-auto mb-3 border border-emerald-200/60 dark:border-emerald-800/40">
                                    <CheckCircle2 className="w-7 h-7" />
                                </div>
                                <h4 className="font-bold text-slate-800 dark:text-slate-200 text-sm">
                                    All Requests Adjudicated
                                </h4>
                                <p className="text-xs text-slate-400 mt-1 max-w-sm mx-auto">
                                    There are no pending employee leave requests requiring executive authorization.
                                </p>
                            </div>
                        ) : (
                            <div className="overflow-x-auto">
                                <table className="w-full text-left text-xs">
                                    <thead>
                                        <tr className="border-b border-slate-100 dark:border-slate-800/80 bg-slate-50/50 dark:bg-slate-800/30 text-slate-400 uppercase font-bold tracking-wider text-[10px]">
                                            <th className="px-6 py-3.5">Personnel</th>
                                            <th className="px-6 py-3.5">Absence Window</th>
                                            <th className="px-6 py-3.5">Reason &amp; Context</th>
                                            <th className="px-6 py-3.5">Filed On</th>
                                            <th className="px-6 py-3.5 text-right">Adjudication</th>
                                        </tr>
                                    </thead>
                                    <tbody className="divide-y divide-slate-100 dark:divide-slate-800/60">
                                        {pendingList.map((leave) => {
                                            const days = calculateDays(leave.start_date, leave.end_date);
                                            const emp = leave.employee;
                                            return (
                                                <tr key={leave.id} className="hover:bg-slate-50/70 dark:hover:bg-slate-800/40 transition-colors">
                                                    <td className="px-6 py-4">
                                                        <div className="flex items-center gap-3">
                                                            {emp?.profile_picture_url ? (
                                                                <img
                                                                    src={emp.profile_picture_url}
                                                                    alt={emp.name}
                                                                    className="w-9 h-9 rounded-xl object-cover ring-1 ring-slate-200 dark:ring-slate-700 shrink-0"
                                                                />
                                                            ) : (
                                                                <div className="w-9 h-9 rounded-xl bg-gradient-to-tr from-blue-600 to-indigo-600 text-white font-black text-xs flex items-center justify-center shrink-0 shadow-sm">
                                                                    {emp?.first_name ? emp.first_name.charAt(0).toUpperCase() : 'E'}
                                                                </div>
                                                            )}
                                                            <div>
                                                                <div className="font-bold text-slate-900 dark:text-white text-xs sm:text-sm">
                                                                    {emp?.name || `${emp?.first_name || ''} ${emp?.last_name || ''}`.trim() || 'Employee'}
                                                                </div>
                                                                <div className="flex items-center gap-2 mt-0.5 text-[11px] text-slate-500 dark:text-slate-400">
                                                                    <span>ID #{emp?.id || '—'}</span>
                                                                    {emp?.department && (
                                                                        <>
                                                                            <span>•</span>
                                                                            <span className="font-medium text-slate-600 dark:text-slate-300">
                                                                                {emp.department}
                                                                            </span>
                                                                        </>
                                                                    )}
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </td>

                                                    <td className="px-6 py-4">
                                                        <div className="font-semibold text-slate-800 dark:text-slate-200">
                                                            {leave.start_date} — {leave.end_date}
                                                        </div>
                                                        <div className="mt-1">
                                                            <span className="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-extrabold bg-blue-50 dark:bg-blue-950/60 text-blue-600 dark:text-blue-300 border border-blue-200/60 dark:border-blue-800/40">
                                                                {days} {days === 1 ? 'Day' : 'Days'} Duration
                                                            </span>
                                                        </div>
                                                    </td>

                                                    <td className="px-6 py-4">
                                                        <div className="max-w-xs text-slate-600 dark:text-slate-300 text-xs line-clamp-2">
                                                            {leave.reason || 'No statement provided.'}
                                                        </div>
                                                    </td>

                                                    <td className="px-6 py-4 text-slate-500 whitespace-nowrap">
                                                        {leave.created_at ? new Date(leave.created_at).toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' }) : '—'}
                                                    </td>

                                                    <td className="px-6 py-4 text-right whitespace-nowrap">
                                                        <div className="flex items-center justify-end gap-2">
                                                            <button
                                                                type="button"
                                                                onClick={() => {
                                                                    setSelectedLeave(leave);
                                                                    setActionType('approve');
                                                                }}
                                                                className="px-3.5 py-1.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs shadow-md shadow-emerald-600/20 transition-all cursor-pointer flex items-center gap-1.5"
                                                            >
                                                                <CheckCircle2 className="w-3.5 h-3.5" />
                                                                <span>Authorize</span>
                                                            </button>

                                                            <button
                                                                type="button"
                                                                onClick={() => {
                                                                    setSelectedLeave(leave);
                                                                    setActionType('reject');
                                                                }}
                                                                className="px-3 py-1.5 rounded-xl bg-rose-50 dark:bg-rose-950/30 hover:bg-rose-100 dark:hover:bg-rose-900/40 text-rose-600 dark:text-rose-400 font-bold text-xs border border-rose-200 dark:border-rose-800/40 transition-all cursor-pointer flex items-center gap-1.5"
                                                            >
                                                                <XCircle className="w-3.5 h-3.5" />
                                                                <span>Decline</span>
                                                            </button>
                                                        </div>
                                                    </td>
                                                </tr>
                                            );
                                        })}
                                    </tbody>
                                </table>
                            </div>
                        )}
                    </div>
                )}

                {/* TAB 2: AUTHORIZATION ARCHIVE */}
                {activeTab === 'archive' && (
                    <div className="rounded-2xl sm:rounded-3xl bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 shadow-xl overflow-hidden">
                        <div className="px-6 py-4 border-b border-slate-100 dark:border-slate-800 flex items-center justify-between">
                            <div className="flex items-center gap-2.5">
                                <span className="w-2.5 h-2.5 rounded-full bg-emerald-500" />
                                <h3 className="font-extrabold text-sm text-slate-900 dark:text-white uppercase tracking-wider">
                                    Historical Authorizations Register
                                </h3>
                            </div>
                            <span className="text-xs font-semibold text-slate-400">
                                {approvedList.length} records
                            </span>
                        </div>

                        {approvedList.length === 0 ? (
                            <div className="p-12 text-center">
                                <div className="w-14 h-14 rounded-2xl bg-slate-100 dark:bg-slate-800 text-slate-400 flex items-center justify-center mx-auto mb-3">
                                    <Calendar className="w-7 h-7" />
                                </div>
                                <h4 className="font-bold text-slate-700 dark:text-slate-300 text-sm">
                                    No Historical Approvals
                                </h4>
                                <p className="text-xs text-slate-400 mt-1 max-w-sm mx-auto">
                                    No archived leave authorizations were located in the system logs.
                                </p>
                            </div>
                        ) : (
                            <div className="overflow-x-auto">
                                <table className="w-full text-left text-xs">
                                    <thead>
                                        <tr className="border-b border-slate-100 dark:border-slate-800/80 bg-slate-50/50 dark:bg-slate-800/30 text-slate-400 uppercase font-bold tracking-wider text-[10px]">
                                            <th className="px-6 py-3.5">Personnel</th>
                                            <th className="px-6 py-3.5">Authorized Period</th>
                                            <th className="px-6 py-3.5">Reason</th>
                                            <th className="px-6 py-3.5 text-right">Authorized By</th>
                                        </tr>
                                    </thead>
                                    <tbody className="divide-y divide-slate-100 dark:divide-slate-800/60">
                                        {approvedList.map((record) => {
                                            const days = calculateDays(record.start_date, record.end_date);
                                            const emp = record.employee;
                                            return (
                                                <tr key={record.id} className="hover:bg-slate-50/70 dark:hover:bg-slate-800/40 transition-colors">
                                                    <td className="px-6 py-4">
                                                        <div className="flex items-center gap-3">
                                                            <div className="w-8 h-8 rounded-xl bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 font-bold text-xs flex items-center justify-center">
                                                                {emp?.first_name ? emp.first_name.charAt(0).toUpperCase() : 'E'}
                                                            </div>
                                                            <div>
                                                                <div className="font-semibold text-slate-900 dark:text-white">
                                                                    {emp?.name || `${emp?.first_name || ''} ${emp?.last_name || ''}`.trim() || 'Employee'}
                                                                </div>
                                                                <div className="text-[11px] text-slate-400">
                                                                    {emp?.department || 'Staff Division'}
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </td>

                                                    <td className="px-6 py-4">
                                                        <div className="font-medium text-slate-800 dark:text-slate-200">
                                                            {record.start_date} — {record.end_date}
                                                        </div>
                                                        <div className="mt-0.5 text-[10px] text-emerald-600 font-bold">
                                                            {days} {days === 1 ? 'day' : 'days'} authorized
                                                        </div>
                                                    </td>

                                                    <td className="px-6 py-4 text-slate-600 dark:text-slate-300 max-w-xs truncate">
                                                        {record.reason || '—'}
                                                    </td>

                                                    <td className="px-6 py-4 text-right whitespace-nowrap">
                                                        <div className="font-semibold text-slate-800 dark:text-slate-200 text-xs">
                                                            {record.approver?.name || 'Administrator'}
                                                        </div>
                                                        <div className="text-[10px] text-slate-400 mt-0.5">
                                                            {record.approved_at ? new Date(record.approved_at).toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' }) : 'Approved'}
                                                        </div>
                                                    </td>
                                                </tr>
                                            );
                                        })}
                                    </tbody>
                                </table>
                            </div>
                        )}
                    </div>
                )}

                {/* INTERACTIVE CONFIRMATION MODAL */}
                {selectedLeave && (
                    <div className="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/60 backdrop-blur-sm animate-in fade-in duration-150">
                        <div className="w-full max-w-md rounded-3xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 p-6 shadow-2xl space-y-4">
                            <div className="flex items-center gap-3">
                                <div className={`w-11 h-11 rounded-2xl flex items-center justify-center ${
                                    actionType === 'approve'
                                        ? 'bg-emerald-50 dark:bg-emerald-950/40 text-emerald-600 border border-emerald-200 dark:border-emerald-800/40'
                                        : 'bg-rose-50 dark:bg-rose-950/40 text-rose-600 border border-rose-200 dark:border-rose-800/40'
                                }`}>
                                    {actionType === 'approve' ? <CheckCircle2 className="w-6 h-6" /> : <XCircle className="w-6 h-6" />}
                                </div>
                                <div>
                                    <h3 className="font-extrabold text-base text-slate-900 dark:text-white">
                                        {actionType === 'approve' ? 'Authorize Absence Request' : 'Decline Absence Request'}
                                    </h3>
                                    <p className="text-xs text-slate-500 dark:text-slate-400">
                                        Action will be recorded and notified to staff.
                                    </p>
                                </div>
                            </div>

                            <div className="p-3.5 rounded-2xl bg-slate-50 dark:bg-slate-800/50 border border-slate-100 dark:border-slate-800 text-xs space-y-1.5">
                                <div className="flex justify-between">
                                    <span className="text-slate-400 font-medium">Employee:</span>
                                    <span className="font-bold text-slate-800 dark:text-slate-200">
                                        {selectedLeave.employee?.name || 'Staff Member'}
                                    </span>
                                </div>
                                <div className="flex justify-between">
                                    <span className="text-slate-400 font-medium">Absence Window:</span>
                                    <span className="font-semibold text-slate-800 dark:text-slate-200">
                                        {selectedLeave.start_date} to {selectedLeave.end_date}
                                    </span>
                                </div>
                                {selectedLeave.reason && (
                                    <div className="pt-1.5 border-t border-slate-200/60 dark:border-slate-700/60">
                                        <span className="text-slate-400 block font-medium">Reason:</span>
                                        <span className="text-slate-700 dark:text-slate-300 italic">
                                            "{selectedLeave.reason}"
                                        </span>
                                    </div>
                                )}
                            </div>

                            <div className="flex items-center justify-end gap-2.5 pt-2">
                                <button
                                    type="button"
                                    onClick={() => {
                                        setSelectedLeave(null);
                                        setActionType(null);
                                    }}
                                    className="px-4 py-2 rounded-xl text-xs font-bold text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors cursor-pointer"
                                >
                                    Cancel
                                </button>
                                <button
                                    type="button"
                                    disabled={processingId !== null}
                                    onClick={handleConfirmAction}
                                    className={`px-5 py-2 rounded-xl text-xs font-bold text-white shadow-md transition-all cursor-pointer ${
                                        actionType === 'approve'
                                            ? 'bg-emerald-600 hover:bg-emerald-700 shadow-emerald-600/20'
                                            : 'bg-rose-600 hover:bg-rose-700 shadow-rose-600/20'
                                    }`}
                                >
                                    {processingId !== null
                                        ? 'Recording...'
                                        : (actionType === 'approve' ? 'Confirm Authorization' : 'Confirm Rejection')
                                    }
                                </button>
                            </div>
                        </div>
                    </div>
                )}
            </div>
        </AuthenticatedLayout>
    );
}
