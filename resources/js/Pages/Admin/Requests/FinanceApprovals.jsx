import { useState } from 'react';
import { Head, router } from '@inertiajs/react';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import {
    Banknote,
    Clock,
    CheckCircle2,
    XCircle,
    Building2,
    Shield,
    AlertCircle,
    FileText,
    DollarSign,
    Calendar,
    ArrowUpRight,
    Paperclip,
    ExternalLink,
} from 'lucide-react';

export default function FinanceApprovals({ pendingExpenses = { data: [] }, historyExpenses = { data: [] }, stats = {} }) {
    const [activeTab, setActiveTab] = useState('pending');
    const [processingId, setProcessingId] = useState(null);
    const [rejectingExpense, setRejectingExpense] = useState(null);
    const [rejectionReason, setRejectionReason] = useState('');
    const [reasonError, setReasonError] = useState('');

    const pendingList = pendingExpenses.data || [];
    const historyList = historyExpenses.data || [];

    const handleApprove = (expense) => {
        setProcessingId(expense.id);
        router.post(`/admin/requests/finance/${expense.id}/approve`, {}, {
            onFinish: () => setProcessingId(null),
        });
    };

    const handleConfirmReject = (e) => {
        e.preventDefault();
        if (!rejectionReason.trim()) {
            setReasonError('Please provide a specific reason for declining this expenditure.');
            return;
        }

        setProcessingId(rejectingExpense.id);
        router.post(
            `/admin/requests/finance/${rejectingExpense.id}/reject`,
            { rejection_reason: rejectionReason },
            {
                onFinish: () => {
                    setProcessingId(null);
                    setRejectingExpense(null);
                    setRejectionReason('');
                    setReasonError('');
                },
            }
        );
    };

    const formatCurrency = (val) => {
        const num = Number(val) || 0;
        return new Intl.NumberFormat('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 }).format(num);
    };

    return (
        <AuthenticatedLayout title="Expenditure Approvals — Administration">
            <div className="space-y-6">
                {/* PAGE HEADER */}
                <div className="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                    <div>
                        <div className="flex items-center gap-2 text-xs font-semibold text-blue-600 dark:text-blue-400 uppercase tracking-wider mb-1">
                            <Shield className="w-3.5 h-3.5" />
                            <span>Executive Governance</span>
                        </div>
                        <h1 className="text-2xl sm:text-3xl font-extrabold tracking-tight text-slate-900 dark:text-white">
                            Finance Administration
                        </h1>
                        <p className="text-xs sm:text-sm text-slate-500 dark:text-slate-400 mt-1">
                            Review, adjudicate, and authorize field project expenditures and voucher requisitions.
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
                            <span>Pending Expenditures</span>
                            {pendingList.length > 0 && (
                                <span className="px-1.5 py-0.2 rounded-full bg-rose-500 text-white text-[10px] font-extrabold">
                                    {pendingExpenses.total || pendingList.length}
                                </span>
                            )}
                        </button>
                        <button
                            onClick={() => setActiveTab('history')}
                            className={`flex items-center gap-2 px-4 py-2 rounded-xl text-xs font-bold transition-all cursor-pointer ${
                                activeTab === 'history'
                                    ? 'bg-blue-600 text-white shadow-md shadow-blue-600/20'
                                    : 'text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800'
                            }`}
                        >
                            <CheckCircle2 className="w-3.5 h-3.5" />
                            <span>Expenditure Archive</span>
                            <span className="text-[11px] opacity-75 font-semibold">
                                ({historyExpenses.total || historyList.length})
                            </span>
                        </button>
                    </div>
                </div>

                {/* KPI STATS ROW */}
                <div className="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <div className="p-4 sm:p-5 rounded-2xl bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 shadow-sm flex items-center justify-between">
                        <div>
                            <div className="text-xs font-semibold uppercase tracking-wider text-slate-400">
                                Pending Requisitions
                            </div>
                            <div className="text-2xl sm:text-3xl font-black text-slate-900 dark:text-white mt-1">
                                {stats.pending_count || pendingList.length}
                            </div>
                            <div className="text-[11px] text-amber-500 font-medium mt-0.5">
                                Awaiting executive sign-off
                            </div>
                        </div>
                        <div className="w-12 h-12 rounded-2xl bg-amber-50 dark:bg-amber-950/40 text-amber-600 flex items-center justify-center border border-amber-200 dark:border-amber-800/40">
                            <Clock className="w-6 h-6" />
                        </div>
                    </div>

                    <div className="p-4 sm:p-5 rounded-2xl bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 shadow-sm flex items-center justify-between">
                        <div>
                            <div className="text-xs font-semibold uppercase tracking-wider text-slate-400">
                                Total Pending Value
                            </div>
                            <div className="text-xl sm:text-2xl font-black text-slate-900 dark:text-white mt-1">
                                <span className="text-xs font-normal text-slate-400 mr-1">ETB</span>
                                {formatCurrency(stats.pending_amount)}
                            </div>
                            <div className="text-[11px] text-blue-500 font-medium mt-0.5">
                                Cumulative commitments
                            </div>
                        </div>
                        <div className="w-12 h-12 rounded-2xl bg-blue-50 dark:bg-blue-950/40 text-blue-600 flex items-center justify-center border border-blue-200 dark:border-blue-800/40">
                            <Banknote className="w-6 h-6" />
                        </div>
                    </div>

                    <div className="p-4 sm:p-5 rounded-2xl bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 shadow-sm flex items-center justify-between">
                        <div>
                            <div className="text-xs font-semibold uppercase tracking-wider text-slate-400">
                                Authorized This Month
                            </div>
                            <div className="text-xl sm:text-2xl font-black text-slate-900 dark:text-white mt-1">
                                <span className="text-xs font-normal text-slate-400 mr-1">ETB</span>
                                {formatCurrency(stats.approved_this_month)}
                            </div>
                            <div className="text-[11px] text-emerald-500 font-medium mt-0.5">
                                Cleared this billing period
                            </div>
                        </div>
                        <div className="w-12 h-12 rounded-2xl bg-emerald-50 dark:bg-emerald-950/40 text-emerald-600 flex items-center justify-center border border-emerald-200 dark:border-emerald-800/40">
                            <CheckCircle2 className="w-6 h-6" />
                        </div>
                    </div>
                </div>

                {/* TAB 1: PENDING EXPENDITURES */}
                {activeTab === 'pending' && (
                    <div className="rounded-2xl sm:rounded-3xl bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 shadow-xl overflow-hidden">
                        <div className="px-6 py-4 border-b border-slate-100 dark:border-slate-800 flex items-center justify-between">
                            <div className="flex items-center gap-2.5">
                                <span className="w-2.5 h-2.5 rounded-full bg-amber-500 animate-pulse" />
                                <h3 className="font-extrabold text-sm text-slate-900 dark:text-white uppercase tracking-wider">
                                    Vouchers Requiring Authorization
                                </h3>
                            </div>
                            <span className="text-xs font-semibold text-slate-400">
                                {pendingList.length} voucher{pendingList.length === 1 ? '' : 's'}
                            </span>
                        </div>

                        {pendingList.length === 0 ? (
                            <div className="p-12 text-center">
                                <div className="w-14 h-14 rounded-2xl bg-emerald-50 dark:bg-emerald-950/30 text-emerald-600 flex items-center justify-center mx-auto mb-3 border border-emerald-200/60 dark:border-emerald-800/40">
                                    <CheckCircle2 className="w-7 h-7" />
                                </div>
                                <h4 className="font-bold text-slate-800 dark:text-slate-200 text-sm">
                                    All Financial Requisitions Processed
                                </h4>
                                <p className="text-xs text-slate-400 mt-1 max-w-sm mx-auto">
                                    No pending field expenditures or purchase vouchers require executive action.
                                </p>
                            </div>
                        ) : (
                            <div className="overflow-x-auto">
                                <table className="w-full text-left text-xs">
                                    <thead>
                                        <tr className="border-b border-slate-100 dark:border-slate-800/80 bg-slate-50/50 dark:bg-slate-800/30 text-slate-400 uppercase font-bold tracking-wider text-[10px]">
                                            <th className="px-6 py-3.5">Site &amp; Classification</th>
                                            <th className="px-6 py-3.5">Amount (ETB)</th>
                                            <th className="px-6 py-3.5">Requester</th>
                                            <th className="px-6 py-3.5">Incurred Date</th>
                                            <th className="px-6 py-3.5 text-right">Adjudication</th>
                                        </tr>
                                    </thead>
                                    <tbody className="divide-y divide-slate-100 dark:divide-slate-800/60">
                                        {pendingList.map((expense) => {
                                            const isProc = processingId === expense.id;
                                            return (
                                                <tr key={expense.id} className="hover:bg-slate-50/70 dark:hover:bg-slate-800/40 transition-colors">
                                                    <td className="px-6 py-4">
                                                        <div className="font-bold text-slate-900 dark:text-white text-xs sm:text-sm">
                                                            {expense.project?.name || 'Global Construction Operations'}
                                                        </div>
                                                        <div className="flex items-center gap-2 mt-1">
                                                            <span className="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300">
                                                                {expense.category ? expense.category.toUpperCase() : 'GENERAL'}
                                                            </span>
                                                            {expense.reference_no && (
                                                                <span className="text-[10px] text-slate-400">
                                                                    REF: {expense.reference_no}
                                                                </span>
                                                            )}
                                                            {expense.attachment_path && (
                                                                <a
                                                                    href={expense.attachment_url || `/storage/${expense.attachment_path}`}
                                                                    target="_blank"
                                                                    rel="noreferrer"
                                                                    className="inline-flex items-center gap-1 text-[10px] font-bold text-blue-600 dark:text-blue-400 hover:underline bg-blue-50 dark:bg-blue-950/40 px-2 py-0.5 rounded-full border border-blue-200 dark:border-blue-900"
                                                                    title="View Attached Invoice/Receipt"
                                                                >
                                                                    <Paperclip className="w-2.5 h-2.5" />
                                                                    <span>Receipt</span>
                                                                    <ExternalLink className="w-2.5 h-2.5" />
                                                                </a>
                                                            )}
                                                        </div>
                                                        {expense.description && (
                                                            <div className="text-xs text-slate-500 dark:text-slate-400 mt-1 max-w-sm line-clamp-1">
                                                                {expense.description}
                                                            </div>
                                                        )}
                                                    </td>

                                                    <td className="px-6 py-4 whitespace-nowrap">
                                                        <div className="font-extrabold text-slate-900 dark:text-white text-sm">
                                                            <span className="text-[10px] font-normal text-slate-400 mr-1">ETB</span>
                                                            {formatCurrency(expense.amount)}
                                                        </div>
                                                    </td>

                                                    <td className="px-6 py-4 whitespace-nowrap">
                                                        <div className="flex items-center gap-2.5">
                                                            <div className="w-7 h-7 rounded-full bg-gradient-to-tr from-amber-500 to-orange-500 text-slate-950 font-bold text-xs flex items-center justify-center">
                                                                {expense.user?.first_name ? expense.user.first_name.charAt(0).toUpperCase() : 'U'}
                                                            </div>
                                                            <div>
                                                                <div className="font-semibold text-slate-900 dark:text-white">
                                                                    {expense.user?.name || 'Personnel'}
                                                                </div>
                                                                <div className="text-[10px] text-slate-400">
                                                                    {expense.user?.role || 'Staff'}
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </td>

                                                    <td className="px-6 py-4 whitespace-nowrap text-slate-500">
                                                        {expense.expense_date ? new Date(expense.expense_date).toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' }) : '—'}
                                                    </td>

                                                    <td className="px-6 py-4 text-right whitespace-nowrap">
                                                        <div className="flex items-center justify-end gap-2">
                                                            <button
                                                                type="button"
                                                                disabled={isProc}
                                                                onClick={() => handleApprove(expense)}
                                                                className="px-3.5 py-1.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs shadow-md shadow-emerald-600/20 transition-all cursor-pointer flex items-center gap-1.5"
                                                            >
                                                                <CheckCircle2 className="w-3.5 h-3.5" />
                                                                <span>{isProc ? 'Authorizing...' : 'Authorize'}</span>
                                                            </button>

                                                            <button
                                                                type="button"
                                                                disabled={isProc}
                                                                onClick={() => {
                                                                    setRejectingExpense(expense);
                                                                    setRejectionReason('');
                                                                    setReasonError('');
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

                {/* TAB 2: EXPENDITURE ARCHIVE */}
                {activeTab === 'history' && (
                    <div className="rounded-2xl sm:rounded-3xl bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 shadow-xl overflow-hidden">
                        <div className="px-6 py-4 border-b border-slate-100 dark:border-slate-800 flex items-center justify-between">
                            <div className="flex items-center gap-2.5">
                                <span className="w-2.5 h-2.5 rounded-full bg-emerald-500" />
                                <h3 className="font-extrabold text-sm text-slate-900 dark:text-white uppercase tracking-wider">
                                    Adjudicated Vouchers Register
                                </h3>
                            </div>
                            <span className="text-xs font-semibold text-slate-400">
                                {historyList.length} records
                            </span>
                        </div>

                        {historyList.length === 0 ? (
                            <div className="p-12 text-center">
                                <div className="w-14 h-14 rounded-2xl bg-slate-100 dark:bg-slate-800 text-slate-400 flex items-center justify-center mx-auto mb-3">
                                    <FileText className="w-7 h-7" />
                                </div>
                                <h4 className="font-bold text-slate-700 dark:text-slate-300 text-sm">
                                    No Historical Records
                                </h4>
                                <p className="text-xs text-slate-400 mt-1 max-w-sm mx-auto">
                                    No completed financial voucher authorizations were found in the audit logs.
                                </p>
                            </div>
                        ) : (
                            <div className="overflow-x-auto">
                                <table className="w-full text-left text-xs">
                                    <thead>
                                        <tr className="border-b border-slate-100 dark:border-slate-800/80 bg-slate-50/50 dark:bg-slate-800/30 text-slate-400 uppercase font-bold tracking-wider text-[10px]">
                                            <th className="px-6 py-3.5">Site &amp; Classification</th>
                                            <th className="px-6 py-3.5">Amount (ETB)</th>
                                            <th className="px-6 py-3.5">Requester</th>
                                            <th className="px-6 py-3.5">Status</th>
                                            <th className="px-6 py-3.5 text-right">Adjudicated Date</th>
                                        </tr>
                                    </thead>
                                    <tbody className="divide-y divide-slate-100 dark:divide-slate-800/60">
                                        {historyList.map((record) => {
                                            const isApproved = record.status === 'approved';
                                            return (
                                                <tr key={record.id} className="hover:bg-slate-50/70 dark:hover:bg-slate-800/40 transition-colors">
                                                    <td className="px-6 py-4">
                                                        <div className="font-semibold text-slate-900 dark:text-white">
                                                            {record.project?.name || 'Global Construction Operations'}
                                                        </div>
                                                        <div className="text-[11px] text-slate-400 mt-0.5 flex items-center gap-1.5 flex-wrap">
                                                            <span>{record.category?.toUpperCase()}</span>
                                                            {record.reference_no && <span>• REF: {record.reference_no}</span>}
                                                            {record.attachment_path && (
                                                                <a
                                                                    href={record.attachment_url || `/storage/${record.attachment_path}`}
                                                                    target="_blank"
                                                                    rel="noreferrer"
                                                                    className="inline-flex items-center gap-1 text-[10px] font-bold text-blue-600 dark:text-blue-400 hover:underline bg-blue-50 dark:bg-blue-950/40 px-1.5 py-0.2 rounded border border-blue-200 dark:border-blue-900"
                                                                    title="View Attached Invoice/Receipt"
                                                                >
                                                                    <Paperclip className="w-2.5 h-2.5" />
                                                                    <span>Receipt</span>
                                                                    <ExternalLink className="w-2.5 h-2.5" />
                                                                </a>
                                                            )}
                                                        </div>
                                                        {record.rejection_reason && (
                                                            <div className="text-[11px] text-rose-500 italic mt-1">
                                                                Reason: "{record.rejection_reason}"
                                                            </div>
                                                        )}
                                                    </td>

                                                    <td className="px-6 py-4 whitespace-nowrap font-bold text-slate-900 dark:text-white">
                                                        ETB {formatCurrency(record.amount)}
                                                    </td>

                                                    <td className="px-6 py-4 whitespace-nowrap text-slate-600 dark:text-slate-300">
                                                        {record.user?.name || 'Staff'}
                                                    </td>

                                                    <td className="px-6 py-4 whitespace-nowrap">
                                                        <span className={`inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-extrabold ${
                                                            isApproved
                                                                ? 'bg-emerald-50 dark:bg-emerald-950/60 text-emerald-600 dark:text-emerald-300 border border-emerald-200/60 dark:border-emerald-800/40'
                                                                : 'bg-rose-50 dark:bg-rose-950/60 text-rose-600 dark:text-rose-300 border border-rose-200/60 dark:border-rose-800/40'
                                                        }`}>
                                                            {isApproved ? <CheckCircle2 className="w-3 h-3" /> : <XCircle className="w-3 h-3" />}
                                                            <span>{record.status?.toUpperCase()}</span>
                                                        </span>
                                                    </td>

                                                    <td className="px-6 py-4 text-right whitespace-nowrap text-slate-400">
                                                        {record.updated_at ? new Date(record.updated_at).toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' }) : '—'}
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

                {/* DECLINE REQUISITION MODAL */}
                {rejectingExpense && (
                    <div className="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/60 backdrop-blur-sm animate-in fade-in duration-150">
                        <div className="w-full max-w-md rounded-3xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 p-6 shadow-2xl space-y-4">
                            <div className="flex items-center gap-3">
                                <div className="w-11 h-11 rounded-2xl bg-rose-50 dark:bg-rose-950/40 text-rose-600 flex items-center justify-center border border-rose-200 dark:border-rose-800/40">
                                    <XCircle className="w-6 h-6" />
                                </div>
                                <div>
                                    <h3 className="font-extrabold text-base text-slate-900 dark:text-white">
                                        Decline Requisition
                                    </h3>
                                    <p className="text-xs text-slate-500 dark:text-slate-400">
                                        Specify the administrative reason for turning down this expenditure.
                                    </p>
                                </div>
                            </div>

                            <div className="p-3.5 rounded-2xl bg-slate-50 dark:bg-slate-800/50 border border-slate-100 dark:border-slate-800 text-xs space-y-1">
                                <div className="flex justify-between">
                                    <span className="text-slate-400 font-medium">Requisition Value:</span>
                                    <span className="font-bold text-slate-900 dark:text-white">
                                        ETB {formatCurrency(rejectingExpense.amount)}
                                    </span>
                                </div>
                                <div className="flex justify-between">
                                    <span className="text-slate-400 font-medium">Site / Project:</span>
                                    <span className="font-semibold text-slate-700 dark:text-slate-300">
                                        {rejectingExpense.project?.name || 'Global'}
                                    </span>
                                </div>
                            </div>

                            <form onSubmit={handleConfirmReject} className="space-y-3">
                                <div>
                                    <label className="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1.5">
                                        Reason for Rejection *
                                    </label>
                                    <textarea
                                        rows={3}
                                        value={rejectionReason}
                                        onChange={(e) => {
                                            setRejectionReason(e.target.value);
                                            if (reasonError) setReasonError('');
                                        }}
                                        placeholder="e.g. Missing supplier tax invoice, unapproved cost escalation, or improper budget code..."
                                        className="w-full text-xs rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 px-3.5 py-2.5 text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-rose-500/40"
                                    />
                                    {reasonError && (
                                        <p className="text-[11px] text-rose-500 font-medium mt-1">
                                            {reasonError}
                                        </p>
                                    )}
                                </div>

                                <div className="flex items-center justify-end gap-2.5 pt-2">
                                    <button
                                        type="button"
                                        onClick={() => {
                                            setRejectingExpense(null);
                                            setRejectionReason('');
                                            setReasonError('');
                                        }}
                                        className="px-4 py-2 rounded-xl text-xs font-bold text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors cursor-pointer"
                                    >
                                        Cancel
                                    </button>
                                    <button
                                        type="submit"
                                        disabled={processingId !== null}
                                        className="px-5 py-2 rounded-xl text-xs font-bold bg-rose-600 hover:bg-rose-700 text-white shadow-md shadow-rose-600/20 transition-all cursor-pointer"
                                    >
                                        {processingId !== null ? 'Declining...' : 'Confirm Rejection'}
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                )}
            </div>
        </AuthenticatedLayout>
    );
}
