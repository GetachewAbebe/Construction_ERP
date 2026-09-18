import { useState } from 'react';
import { Head, router } from '@inertiajs/react';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import {
    Package,
    Clock,
    CheckCircle2,
    XCircle,
    Building2,
    Shield,
    Users,
    HardHat,
    Calendar,
    RotateCcw,
} from 'lucide-react';

export default function LoanApprovals({ pendingLoans = { data: [] }, historyLoans = { data: [] }, stats = {} }) {
    const [activeTab, setActiveTab] = useState('pending');
    const [processingId, setProcessingId] = useState(null);
    const [rejectingLoan, setRejectingLoan] = useState(null);
    const [rejectionReason, setRejectionReason] = useState('');

    const pendingList = pendingLoans.data || [];
    const historyList = historyLoans.data || [];

    const handleApprove = (loan) => {
        setProcessingId(loan.id);
        router.post(`/admin/requests/items/${loan.id}/approve`, {}, {
            onFinish: () => setProcessingId(null),
        });
    };

    const handleConfirmReject = (e) => {
        e.preventDefault();
        setProcessingId(rejectingLoan.id);
        router.post(
            `/admin/requests/items/${rejectingLoan.id}/reject`,
            { rejection_reason: rejectionReason },
            {
                onFinish: () => {
                    setProcessingId(null);
                    setRejectingLoan(null);
                    setRejectionReason('');
                },
            }
        );
    };

    const getStatusBadge = (status) => {
        switch (status) {
            case 'approved':
                return { label: 'Active Loan', color: 'bg-emerald-50 dark:bg-emerald-950/60 text-emerald-600 dark:text-emerald-300 border-emerald-200/60 dark:border-emerald-800/40', icon: CheckCircle2 };
            case 'returned':
                return { label: 'Returned', color: 'bg-blue-50 dark:bg-blue-950/60 text-blue-600 dark:text-blue-300 border-blue-200/60 dark:border-blue-800/40', icon: RotateCcw };
            case 'rejected':
                return { label: 'Declined', color: 'bg-rose-50 dark:bg-rose-950/60 text-rose-600 dark:text-rose-300 border-rose-200/60 dark:border-rose-800/40', icon: XCircle };
            default:
                return { label: 'Pending', color: 'bg-amber-50 dark:bg-amber-950/60 text-amber-600 dark:text-amber-300 border-amber-200/60 dark:border-amber-800/40', icon: Clock };
        }
    };

    return (
        <AuthenticatedLayout title="Asset Loan Approvals — Administration">
            <div className="space-y-6">
                {/* PAGE HEADER */}
                <div className="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                    <div>
                        <div className="flex items-center gap-2 text-xs font-semibold text-blue-600 dark:text-blue-400 uppercase tracking-wider mb-1">
                            <Shield className="w-3.5 h-3.5" />
                            <span>Executive Governance</span>
                        </div>
                        <h1 className="text-2xl sm:text-3xl font-extrabold tracking-tight text-slate-900 dark:text-white">
                            Asset &amp; Material Lending
                        </h1>
                        <p className="text-xs sm:text-sm text-slate-500 dark:text-slate-400 mt-1">
                            Review, authorize, and track inventory loans, heavy equipment check-outs, and gate passes.
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
                            <span>Pending Requests</span>
                            {pendingList.length > 0 && (
                                <span className="px-1.5 py-0.2 rounded-full bg-rose-500 text-white text-[10px] font-extrabold">
                                    {pendingLoans.total || pendingList.length}
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
                            <span>Lending Archive</span>
                            <span className="text-[11px] opacity-75 font-semibold">
                                ({historyLoans.total || historyList.length})
                            </span>
                        </button>
                    </div>
                </div>

                {/* KPI STATS ROW */}
                <div className="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <div className="p-4 sm:p-5 rounded-2xl bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 shadow-sm flex items-center justify-between">
                        <div>
                            <div className="text-xs font-semibold uppercase tracking-wider text-slate-400">
                                Pending Authorizations
                            </div>
                            <div className="text-2xl sm:text-3xl font-black text-slate-900 dark:text-white mt-1">
                                {stats.pending_count || pendingList.length}
                            </div>
                            <div className="text-[11px] text-amber-500 font-medium mt-0.5">
                                Awaiting material gate pass
                            </div>
                        </div>
                        <div className="w-12 h-12 rounded-2xl bg-amber-50 dark:bg-amber-950/40 text-amber-600 flex items-center justify-center border border-amber-200 dark:border-amber-800/40">
                            <Clock className="w-6 h-6" />
                        </div>
                    </div>

                    <div className="p-4 sm:p-5 rounded-2xl bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 shadow-sm flex items-center justify-between">
                        <div>
                            <div className="text-xs font-semibold uppercase tracking-wider text-slate-400">
                                Active Borrowed Assets
                            </div>
                            <div className="text-2xl sm:text-3xl font-black text-slate-900 dark:text-white mt-1">
                                {stats.active_borrowed || 0}
                            </div>
                            <div className="text-[11px] text-blue-500 font-medium mt-0.5">
                                Currently in field possession
                            </div>
                        </div>
                        <div className="w-12 h-12 rounded-2xl bg-blue-50 dark:bg-blue-950/40 text-blue-600 flex items-center justify-center border border-blue-200 dark:border-blue-800/40">
                            <Package className="w-6 h-6" />
                        </div>
                    </div>

                    <div className="p-4 sm:p-5 rounded-2xl bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 shadow-sm flex items-center justify-between">
                        <div>
                            <div className="text-xs font-semibold uppercase tracking-wider text-slate-400">
                                Authorized This Month
                            </div>
                            <div className="text-2xl sm:text-3xl font-black text-slate-900 dark:text-white mt-1">
                                {stats.approved_this_month || historyList.length}
                            </div>
                            <div className="text-[11px] text-emerald-500 font-medium mt-0.5">
                                Current cycle approvals
                            </div>
                        </div>
                        <div className="w-12 h-12 rounded-2xl bg-emerald-50 dark:bg-emerald-950/40 text-emerald-600 flex items-center justify-center border border-emerald-200 dark:border-emerald-800/40">
                            <CheckCircle2 className="w-6 h-6" />
                        </div>
                    </div>
                </div>

                {/* TAB 1: PENDING LENDING REQUESTS */}
                {activeTab === 'pending' && (
                    <div className="rounded-2xl sm:rounded-3xl bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 shadow-xl overflow-hidden">
                        <div className="px-6 py-4 border-b border-slate-100 dark:border-slate-800 flex items-center justify-between">
                            <div className="flex items-center gap-2.5">
                                <span className="w-2.5 h-2.5 rounded-full bg-amber-500 animate-pulse" />
                                <h3 className="font-extrabold text-sm text-slate-900 dark:text-white uppercase tracking-wider">
                                    Requests Awaiting Gate Pass Authorization
                                </h3>
                            </div>
                            <span className="text-xs font-semibold text-slate-400">
                                {pendingList.length} request{pendingList.length === 1 ? '' : 's'}
                            </span>
                        </div>

                        {pendingList.length === 0 ? (
                            <div className="p-12 text-center">
                                <div className="w-14 h-14 rounded-2xl bg-emerald-50 dark:bg-emerald-950/30 text-emerald-600 flex items-center justify-center mx-auto mb-3 border border-emerald-200/60 dark:border-emerald-800/40">
                                    <CheckCircle2 className="w-7 h-7" />
                                </div>
                                <h4 className="font-bold text-slate-800 dark:text-slate-200 text-sm">
                                    All Lending Requests Cleared
                                </h4>
                                <p className="text-xs text-slate-400 mt-1 max-w-sm mx-auto">
                                    No pending item loans or equipment dispatches are awaiting executive approval.
                                </p>
                            </div>
                        ) : (
                            <div className="overflow-x-auto">
                                <table className="w-full text-left text-xs">
                                    <thead>
                                        <tr className="border-b border-slate-100 dark:border-slate-800/80 bg-slate-50/50 dark:bg-slate-800/30 text-slate-400 uppercase font-bold tracking-wider text-[10px]">
                                            <th className="px-6 py-3.5">Inventory Item</th>
                                            <th className="px-6 py-3.5">Borrower Personnel</th>
                                            <th className="px-6 py-3.5 text-center">Requested Qty</th>
                                            <th className="px-6 py-3.5">Requested On</th>
                                            <th className="px-6 py-3.5 text-right">Adjudication</th>
                                        </tr>
                                    </thead>
                                    <tbody className="divide-y divide-slate-100 dark:divide-slate-800/60">
                                        {pendingList.map((loan) => {
                                            const isProc = processingId === loan.id;
                                            const item = loan.item;
                                            const emp = loan.employee;
                                            return (
                                                <tr key={loan.id} className="hover:bg-slate-50/70 dark:hover:bg-slate-800/40 transition-colors">
                                                    <td className="px-6 py-4">
                                                        <div className="flex items-center gap-3">
                                                            <div className="w-9 h-9 rounded-xl bg-blue-50 dark:bg-blue-950/40 text-blue-600 dark:text-blue-400 flex items-center justify-center font-bold text-xs border border-blue-200/60 dark:border-blue-800/40 shrink-0">
                                                                <Package className="w-4 h-4" />
                                                            </div>
                                                            <div>
                                                                <div className="font-bold text-slate-900 dark:text-white text-xs sm:text-sm">
                                                                    {item?.name || 'Inventory Asset'}
                                                                </div>
                                                                <div className="flex items-center gap-2 mt-0.5 text-[11px] text-slate-400">
                                                                    <span>NO: {item?.item_no || 'N/A'}</span>
                                                                    {item?.quantity !== undefined && (
                                                                        <>
                                                                            <span>•</span>
                                                                            <span className="text-slate-600 dark:text-slate-300 font-medium">
                                                                                Stock: {item.quantity} in store
                                                                            </span>
                                                                        </>
                                                                    )}
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </td>

                                                    <td className="px-6 py-4">
                                                        <div className="font-semibold text-slate-900 dark:text-white">
                                                            {emp?.name || `${emp?.first_name || ''} ${emp?.last_name || ''}`.trim() || 'Personnel'}
                                                        </div>
                                                        <div className="text-[11px] text-slate-400 mt-0.5">
                                                            ID #{emp?.id || '—'} {emp?.department ? `• ${emp.department}` : ''}
                                                        </div>
                                                    </td>

                                                    <td className="px-6 py-4 text-center">
                                                        <span className="inline-flex items-center px-3 py-1 rounded-full text-xs font-black bg-slate-100 dark:bg-slate-800 text-slate-800 dark:text-slate-200 border border-slate-200 dark:border-slate-700">
                                                            {loan.quantity} {loan.quantity === 1 ? 'Unit' : 'Units'}
                                                        </span>
                                                    </td>

                                                    <td className="px-6 py-4 text-slate-500 whitespace-nowrap">
                                                        {loan.created_at ? new Date(loan.created_at).toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' }) : '—'}
                                                    </td>

                                                    <td className="px-6 py-4 text-right whitespace-nowrap">
                                                        <div className="flex items-center justify-end gap-2">
                                                            <button
                                                                type="button"
                                                                disabled={isProc}
                                                                onClick={() => handleApprove(loan)}
                                                                className="px-3.5 py-1.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs shadow-md shadow-emerald-600/20 transition-all cursor-pointer flex items-center gap-1.5"
                                                            >
                                                                <CheckCircle2 className="w-3.5 h-3.5" />
                                                                <span>{isProc ? 'Authorizing...' : 'Authorize'}</span>
                                                            </button>

                                                            <button
                                                                type="button"
                                                                disabled={isProc}
                                                                onClick={() => {
                                                                    setRejectingLoan(loan);
                                                                    setRejectionReason('');
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

                {/* TAB 2: LENDING ARCHIVE */}
                {activeTab === 'history' && (
                    <div className="rounded-2xl sm:rounded-3xl bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 shadow-xl overflow-hidden">
                        <div className="px-6 py-4 border-b border-slate-100 dark:border-slate-800 flex items-center justify-between">
                            <div className="flex items-center gap-2.5">
                                <span className="w-2.5 h-2.5 rounded-full bg-emerald-500" />
                                <h3 className="font-extrabold text-sm text-slate-900 dark:text-white uppercase tracking-wider">
                                    Adjudicated Loans Register
                                </h3>
                            </div>
                            <span className="text-xs font-semibold text-slate-400">
                                {historyList.length} records
                            </span>
                        </div>

                        {historyList.length === 0 ? (
                            <div className="p-12 text-center">
                                <div className="w-14 h-14 rounded-2xl bg-slate-100 dark:bg-slate-800 text-slate-400 flex items-center justify-center mx-auto mb-3">
                                    <Package className="w-7 h-7" />
                                </div>
                                <h4 className="font-bold text-slate-700 dark:text-slate-300 text-sm">
                                    No Historical Records
                                </h4>
                                <p className="text-xs text-slate-400 mt-1 max-w-sm mx-auto">
                                    No completed material loan authorizations were found in the audit logs.
                                </p>
                            </div>
                        ) : (
                            <div className="overflow-x-auto">
                                <table className="w-full text-left text-xs">
                                    <thead>
                                        <tr className="border-b border-slate-100 dark:border-slate-800/80 bg-slate-50/50 dark:bg-slate-800/30 text-slate-400 uppercase font-bold tracking-wider text-[10px]">
                                            <th className="px-6 py-3.5">Inventory Item</th>
                                            <th className="px-6 py-3.5">Borrower</th>
                                            <th className="px-6 py-3.5 text-center">Qty</th>
                                            <th className="px-6 py-3.5">Status</th>
                                            <th className="px-6 py-3.5 text-right">Adjudicated On</th>
                                        </tr>
                                    </thead>
                                    <tbody className="divide-y divide-slate-100 dark:divide-slate-800/60">
                                        {historyList.map((loan) => {
                                            const badge = getStatusBadge(loan.status);
                                            const BadgeIcon = badge.icon;
                                            return (
                                                <tr key={loan.id} className="hover:bg-slate-50/70 dark:hover:bg-slate-800/40 transition-colors">
                                                    <td className="px-6 py-4">
                                                        <div className="font-semibold text-slate-900 dark:text-white">
                                                            {loan.item?.name || 'Item'}
                                                        </div>
                                                        <div className="text-[11px] text-slate-400">
                                                            NO: {loan.item?.item_no || 'N/A'}
                                                        </div>
                                                        {loan.rejection_reason && (
                                                            <div className="text-[11px] text-rose-500 italic mt-1">
                                                                Reason: "{loan.rejection_reason}"
                                                            </div>
                                                        )}
                                                    </td>

                                                    <td className="px-6 py-4">
                                                        <div className="font-medium text-slate-800 dark:text-slate-200">
                                                            {loan.employee?.name || 'Personnel'}
                                                        </div>
                                                        <div className="text-[11px] text-slate-400">
                                                            ID #{loan.employee?.id || '—'}
                                                        </div>
                                                    </td>

                                                    <td className="px-6 py-4 text-center font-bold text-slate-800 dark:text-slate-200">
                                                        {loan.quantity}
                                                    </td>

                                                    <td className="px-6 py-4">
                                                        <span className={`inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-extrabold border ${badge.color}`}>
                                                            <BadgeIcon className="w-3 h-3" />
                                                            <span>{badge.label}</span>
                                                        </span>
                                                    </td>

                                                    <td className="px-6 py-4 text-right whitespace-nowrap text-slate-400">
                                                        {loan.updated_at ? new Date(loan.updated_at).toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' }) : '—'}
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

                {/* DECLINE LOAN MODAL */}
                {rejectingLoan && (
                    <div className="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/60 backdrop-blur-sm animate-in fade-in duration-150">
                        <div className="w-full max-w-md rounded-3xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 p-6 shadow-2xl space-y-4">
                            <div className="flex items-center gap-3">
                                <div className="w-11 h-11 rounded-2xl bg-rose-50 dark:bg-rose-950/40 text-rose-600 flex items-center justify-center border border-rose-200 dark:border-rose-800/40">
                                    <XCircle className="w-6 h-6" />
                                </div>
                                <div>
                                    <h3 className="font-extrabold text-base text-slate-900 dark:text-white">
                                        Decline Loan Request
                                    </h3>
                                    <p className="text-xs text-slate-500 dark:text-slate-400">
                                        Provide a reason for declining this material check-out.
                                    </p>
                                </div>
                            </div>

                            <div className="p-3.5 rounded-2xl bg-slate-50 dark:bg-slate-800/50 border border-slate-100 dark:border-slate-800 text-xs space-y-1">
                                <div className="flex justify-between">
                                    <span className="text-slate-400 font-medium">Item:</span>
                                    <span className="font-bold text-slate-900 dark:text-white">
                                        {rejectingLoan.item?.name || 'Inventory Asset'}
                                    </span>
                                </div>
                                <div className="flex justify-between">
                                    <span className="text-slate-400 font-medium">Borrower:</span>
                                    <span className="font-semibold text-slate-700 dark:text-slate-300">
                                        {rejectingLoan.employee?.name || 'Personnel'}
                                    </span>
                                </div>
                            </div>

                            <form onSubmit={handleConfirmReject} className="space-y-3">
                                <div>
                                    <label className="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1.5">
                                        Rejection Reason (Optional)
                                    </label>
                                    <textarea
                                        rows={3}
                                        value={rejectionReason}
                                        onChange={(e) => setRejectionReason(e.target.value)}
                                        placeholder="e.g. Asset scheduled for priority maintenance, insufficient critical inventory reserves..."
                                        className="w-full text-xs rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 px-3.5 py-2.5 text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-rose-500/40"
                                    />
                                </div>

                                <div className="flex items-center justify-end gap-2.5 pt-2">
                                    <button
                                        type="button"
                                        onClick={() => {
                                            setRejectingLoan(null);
                                            setRejectionReason('');
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
