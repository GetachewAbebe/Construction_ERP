import { useState } from 'react';
import { Head, Link, router, useForm } from '@inertiajs/react';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import {
    FileSpreadsheet,
    ArrowLeft,
    Printer,
    CheckCircle2,
    Clock,
    Building2,
    Calendar,
    Users,
    DollarSign,
    QrCode,
    CreditCard,
    Receipt,
    HardHat,
    AlertCircle,
    X,
} from 'lucide-react';

export default function Show({ musterRoll }) {
    const [isPayoutModalOpen, setIsPayoutModalOpen] = useState(false);

    const { data: payoutData, setData: setPayoutData, post: postPayout, processing: payoutProcessing, reset: resetPayout } = useForm({
        payment_method: 'cash',
        payout_reference: '',
    });

    const handleApprove = () => {
        if (confirm(`Approve Muster Roll ${musterRoll.muster_roll_no} for cash wage disbursement?`)) {
            router.post(`/labor/muster-rolls/${musterRoll.id}/approve`);
        }
    };

    const handlePayoutSubmit = (e) => {
        e.preventDefault();
        postPayout(`/labor/muster-rolls/${musterRoll.id}/payout`, {
            onSuccess: () => {
                setIsPayoutModalOpen(false);
                resetPayout();
            },
        });
    };

    const getStatusBadge = (st) => {
        switch (st) {
            case 'paid':
                return (
                    <span className="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-emerald-50 dark:bg-emerald-950/60 text-emerald-700 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-800">
                        <CheckCircle2 className="w-3.5 h-3.5" /> Wages Disbursed & Accounted
                    </span>
                );
            case 'approved':
                return (
                    <span className="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-blue-50 dark:bg-blue-950/60 text-blue-700 dark:text-blue-300 border border-blue-200 dark:border-blue-800">
                        <CheckCircle2 className="w-3.5 h-3.5" /> Approved for Payout
                    </span>
                );
            case 'submitted':
                return (
                    <span className="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-amber-50 dark:bg-amber-950/60 text-amber-700 dark:text-amber-300 border border-amber-200 dark:border-amber-800">
                        <Clock className="w-3.5 h-3.5" /> Pending PM Approval
                    </span>
                );
            case 'draft':
                return (
                    <span className="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300">
                        Draft Sheet
                    </span>
                );
            default:
                return <span className="px-3 py-1 rounded-full text-xs font-bold bg-slate-100">{st}</span>;
        }
    };

    const items = musterRoll.items || [];

    return (
        <AuthenticatedLayout title={`Muster Roll ${musterRoll.muster_roll_no}`} header="Site Operations">
            <div className="space-y-6 pb-12">
                {/* Header */}
                <div className="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-2 border-b border-slate-200 dark:border-slate-800">
                    <div className="flex items-center gap-3">
                        <Link
                            href="/labor/muster-rolls"
                            className="p-2 rounded-xl bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300 transition-colors"
                        >
                            <ArrowLeft className="w-5 h-5" />
                        </Link>
                        <div>
                            <div className="flex items-center gap-2.5">
                                <h1 className="text-xl sm:text-2xl font-extrabold tracking-tight text-slate-900 dark:text-white font-mono">
                                    {musterRoll.muster_roll_no}
                                </h1>
                                {getStatusBadge(musterRoll.status)}
                            </div>
                            <p className="text-xs sm:text-sm text-slate-500 dark:text-slate-400 mt-0.5">
                                {musterRoll.project?.name} • {new Date(musterRoll.date).toLocaleDateString('en-US', { weekday: 'long', month: 'long', day: 'numeric', year: 'numeric' })}
                            </p>
                        </div>
                    </div>

                    <div className="flex flex-wrap items-center gap-2.5">
                        <a
                            href={`/labor/muster-rolls/${musterRoll.id}/print`}
                            target="_blank"
                            rel="noopener noreferrer"
                            className="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300 font-semibold text-xs transition-all cursor-pointer"
                        >
                            <Printer className="w-4 h-4 text-blue-500" />
                            <span>Print Corporate Sheet</span>
                        </a>

                        {musterRoll.status === 'submitted' && (
                            <button
                                onClick={handleApprove}
                                className="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs shadow-md shadow-blue-500/20 transition-all cursor-pointer"
                            >
                                <CheckCircle2 className="w-4 h-4" />
                                <span>Approve Muster Roll</span>
                            </button>
                        )}

                        {musterRoll.status === 'approved' && (
                            <button
                                onClick={() => setIsPayoutModalOpen(true)}
                                className="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs shadow-md shadow-emerald-500/20 transition-all cursor-pointer"
                            >
                                <DollarSign className="w-4 h-4" />
                                <span>Disburse Wage Payout</span>
                            </button>
                        )}
                    </div>
                </div>

                {/* Auto-posted Project Expense Notification Alert */}
                {musterRoll.expense && (
                    <div className="p-4 bg-emerald-50 dark:bg-emerald-950/40 border border-emerald-200 dark:border-emerald-800 rounded-2xl flex items-center justify-between gap-4">
                        <div className="flex items-center gap-3">
                            <div className="p-2 bg-emerald-500/10 rounded-xl text-emerald-600 dark:text-emerald-400">
                                <Receipt className="w-5 h-5" />
                            </div>
                            <div>
                                <h4 className="text-xs font-bold text-emerald-900 dark:text-emerald-200">
                                    Project Financial Expense Auto-Posted
                                </h4>
                                <p className="text-[11px] text-emerald-700 dark:text-emerald-300 mt-0.5">
                                    Labor expense #{musterRoll.expense.reference_no} ({Number(musterRoll.expense.amount).toLocaleString()} ETB) has been allocated directly to {musterRoll.project?.name} budget.
                                </p>
                            </div>
                        </div>
                        <a
                            href={`/finance/expenses/${musterRoll.expense.id}/print`}
                            target="_blank"
                            rel="noopener noreferrer"
                            className="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-emerald-600 text-white text-xs font-bold shadow-xs hover:bg-emerald-700 transition-colors"
                        >
                            <span>View Expense Voucher</span>
                        </a>
                    </div>
                )}

                {/* Summary KPI Cards */}
                <div className="grid grid-cols-2 lg:grid-cols-4 gap-4">
                    <div className="bg-white dark:bg-slate-900 p-4 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-xs">
                        <span className="text-xs font-semibold text-slate-500 dark:text-slate-400">Total Net Payout</span>
                        <div className="mt-2 text-2xl font-black text-emerald-600 dark:text-emerald-400">
                            {Number(musterRoll.total_net_amount).toLocaleString()} <span className="text-xs font-bold text-slate-400">ETB</span>
                        </div>
                        <span className="text-[11px] text-slate-500">Gross: {Number(Number(musterRoll.total_regular_amount) + Number(musterRoll.total_overtime_amount)).toLocaleString()} ETB</span>
                    </div>

                    <div className="bg-white dark:bg-slate-900 p-4 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-xs">
                        <span className="text-xs font-semibold text-slate-500 dark:text-slate-400">Labor Force Present</span>
                        <div className="mt-2 text-2xl font-black text-slate-900 dark:text-white">
                            {musterRoll.total_workers} <span className="text-xs font-normal text-slate-400">/ {items.length} workers</span>
                        </div>
                        <span className="text-[11px] text-amber-600">Site daily muster roster</span>
                    </div>

                    <div className="bg-white dark:bg-slate-900 p-4 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-xs">
                        <span className="text-xs font-semibold text-slate-500 dark:text-slate-400">Overtime Wages</span>
                        <div className="mt-2 text-2xl font-black text-blue-600 dark:text-blue-400">
                            {Number(musterRoll.total_overtime_amount).toLocaleString()} <span className="text-xs font-bold text-slate-400">ETB</span>
                        </div>
                        <span className="text-[11px] text-slate-500">Deductions: -{Number(musterRoll.total_deductions).toLocaleString()} ETB</span>
                    </div>

                    <div className="bg-white dark:bg-slate-900 p-4 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-xs">
                        <span className="text-xs font-semibold text-slate-500 dark:text-slate-400">Disbursement Status</span>
                        <div className="mt-2 text-sm font-extrabold text-slate-900 dark:text-white">
                            {musterRoll.status === 'paid' ? (
                                <span className="text-emerald-600 dark:text-emerald-400">
                                    Via {String(musterRoll.payment_method).toUpperCase()}
                                </span>
                            ) : (
                                <span className="text-amber-600">{musterRoll.status}</span>
                            )}
                        </div>
                        <span className="text-[11px] text-slate-500">
                            {musterRoll.paid_at ? new Date(musterRoll.paid_at).toLocaleDateString() : 'Awaiting payout'}
                        </span>
                    </div>
                </div>

                {/* General Information Box */}
                <div className="bg-white dark:bg-slate-900 p-5 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-xs">
                    <div className="grid grid-cols-1 sm:grid-cols-3 gap-4 text-xs">
                        <div>
                            <span className="text-slate-400 block font-semibold">Project Site</span>
                            <span className="font-bold text-slate-900 dark:text-white text-sm">
                                {musterRoll.project?.name}
                            </span>
                            <span className="text-slate-500 block">{musterRoll.project?.location || 'Site Location'}</span>
                        </div>
                        <div>
                            <span className="text-slate-400 block font-semibold">Activity / Description</span>
                            <span className="font-bold text-slate-900 dark:text-white text-sm">
                                {musterRoll.title || 'Site Operations'}
                            </span>
                            <span className="text-slate-500 block">Prepared by: {musterRoll.supervisor?.name || 'Foreman'}</span>
                        </div>
                        <div>
                            <span className="text-slate-400 block font-semibold">Approval & Audit Trail</span>
                            <span className="font-bold text-slate-900 dark:text-white">
                                PM: {musterRoll.approved_by ? musterRoll.approved_by.name : 'Pending'}
                            </span>
                            <span className="text-slate-500 block">
                                Cashier: {musterRoll.paid_by ? musterRoll.paid_by.name : 'Unpaid'}
                            </span>
                        </div>
                    </div>
                </div>

                {/* Worker Line Items Table */}
                <div className="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-xs overflow-hidden">
                    <div className="p-4 border-b border-slate-200 dark:border-slate-800 flex items-center justify-between">
                        <h3 className="text-sm font-bold text-slate-900 dark:text-white flex items-center gap-2">
                            <Users className="w-4 h-4 text-emerald-600" />
                            Itemized Worker Roster & Attendance Breakdown
                        </h3>
                        <span className="text-xs text-slate-500">
                            {musterRoll.total_workers} of {items.length} workers credited
                        </span>
                    </div>

                    <div className="overflow-x-auto">
                        <table className="w-full text-left text-xs">
                            <thead className="bg-slate-50 dark:bg-slate-800/60 border-b border-slate-200 dark:border-slate-800 text-slate-500 dark:text-slate-400 font-semibold uppercase tracking-wider">
                                <tr>
                                    <th className="py-3 px-4">#</th>
                                    <th className="py-3 px-4">Worker ID</th>
                                    <th className="py-3 px-4">Worker Full Name</th>
                                    <th className="py-3 px-4">Trade / Craft</th>
                                    <th className="py-3 px-4 text-center">Days Worked</th>
                                    <th className="py-3 px-4 text-right">Daily Rate</th>
                                    <th className="py-3 px-4 text-right">Regular Total</th>
                                    <th className="py-3 px-4 text-center">OT (Hrs)</th>
                                    <th className="py-3 px-4 text-right">OT Pay</th>
                                    <th className="py-3 px-4 text-right">Deduction</th>
                                    <th className="py-3 px-4 text-right">Net Payable</th>
                                    <th className="py-3 px-4">Task Assigned</th>
                                </tr>
                            </thead>
                            <tbody className="divide-y divide-slate-200 dark:divide-slate-800">
                                {items.map((item, idx) => (
                                    <tr key={item.id} className="hover:bg-slate-50/80 dark:hover:bg-slate-800/40 transition-colors">
                                        <td className="py-3 px-4 font-mono text-slate-400">{idx + 1}</td>
                                        <td className="py-3 px-4 font-mono font-bold text-amber-600 dark:text-amber-400">
                                            {item.casual_laborer?.worker_code || '-'}
                                        </td>
                                        <td className="py-3 px-4 font-bold text-slate-900 dark:text-white">
                                            {item.casual_laborer?.full_name || 'Worker'}
                                        </td>
                                        <td className="py-3 px-4 text-slate-600 dark:text-slate-300">
                                            {item.trade}
                                        </td>
                                        <td className="py-3 px-4 text-center font-bold">
                                            {Number(item.days_worked).toFixed(1)}
                                        </td>
                                        <td className="py-3 px-4 text-right text-slate-600 dark:text-slate-300">
                                            {Number(item.daily_rate).toLocaleString()} ETB
                                        </td>
                                        <td className="py-3 px-4 text-right font-medium text-slate-900 dark:text-white">
                                            {Number(item.regular_amount).toLocaleString()} ETB
                                        </td>
                                        <td className="py-3 px-4 text-center text-slate-600 dark:text-slate-300">
                                            {Number(item.overtime_hours) > 0 ? `${Number(item.overtime_hours).toFixed(1)} hrs` : '-'}
                                        </td>
                                        <td className="py-3 px-4 text-right text-blue-600 dark:text-blue-400">
                                            {Number(item.overtime_amount) > 0 ? `+${Number(item.overtime_amount).toLocaleString()}` : '-'}
                                        </td>
                                        <td className="py-3 px-4 text-right text-rose-600">
                                            {Number(item.deduction_amount) > 0 ? `-${Number(item.deduction_amount).toLocaleString()}` : '-'}
                                        </td>
                                        <td className="py-3 px-4 text-right font-black text-slate-900 dark:text-white">
                                            {Number(item.net_amount).toLocaleString()} ETB
                                        </td>
                                        <td className="py-3 px-4 text-slate-500 italic">
                                            {item.task_assigned || '-'}
                                        </td>
                                    </tr>
                                ))}
                            </tbody>
                        </table>
                    </div>
                </div>

                {/* Payout Modal */}
                {isPayoutModalOpen && (
                    <div className="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-xs">
                        <div className="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl max-w-md w-full p-6 shadow-2xl">
                            <div className="flex items-center justify-between pb-3 border-b border-slate-200 dark:border-slate-800">
                                <h3 className="text-base font-bold text-slate-900 dark:text-white flex items-center gap-2">
                                    <DollarSign className="w-5 h-5 text-emerald-600" />
                                    Disburse Casual Labor Wages
                                </h3>
                                <button
                                    onClick={() => setIsPayoutModalOpen(false)}
                                    className="p-1 rounded-lg text-slate-400 hover:text-slate-600 dark:hover:text-slate-200"
                                >
                                    <X className="w-5 h-5" />
                                </button>
                            </div>

                            <form onSubmit={handlePayoutSubmit} className="mt-4 space-y-4">
                                <div className="p-3 bg-emerald-50 dark:bg-emerald-950/40 border border-emerald-200 dark:border-emerald-800 rounded-xl">
                                    <span className="text-[11px] text-emerald-800 dark:text-emerald-300 font-semibold block">Total Amount to Disburse:</span>
                                    <span className="text-xl font-black text-emerald-600 dark:text-emerald-400">
                                        {Number(musterRoll.total_net_amount).toLocaleString()} ETB
                                    </span>
                                    <p className="text-[10px] text-slate-500 mt-1">
                                        * Submitting will instantly post an approved Project Expense Voucher under category "Labor".
                                    </p>
                                </div>

                                <div>
                                    <label className="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">
                                        Disbursement Method *
                                    </label>
                                    <select
                                        value={payoutData.payment_method}
                                        onChange={(e) => setPayoutData('payment_method', e.target.value)}
                                        className="w-full px-3 py-2 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-xs text-slate-900 dark:text-white focus:ring-2 focus:ring-emerald-500"
                                    >
                                        <option value="cash">Site Petty Cash</option>
                                        <option value="telebirr">Telebirr Payout</option>
                                        <option value="cbe_birr">CBE Birr</option>
                                        <option value="bank_transfer">Bank Transfer / Check</option>
                                    </select>
                                </div>

                                <div>
                                    <label className="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">
                                        Transaction Reference / Voucher #
                                    </label>
                                    <input
                                        type="text"
                                        placeholder="e.g. Telebirr Txn ID, PCV-0012"
                                        value={payoutData.payout_reference}
                                        onChange={(e) => setPayoutData('payout_reference', e.target.value)}
                                        className="w-full px-3 py-2 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-xs text-slate-900 dark:text-white focus:ring-2 focus:ring-emerald-500"
                                    />
                                </div>

                                <div className="flex justify-end gap-2 pt-3 border-t border-slate-200 dark:border-slate-800">
                                    <button
                                        type="button"
                                        onClick={() => setIsPayoutModalOpen(false)}
                                        className="px-4 py-2 bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300 text-xs font-semibold rounded-xl"
                                    >
                                        Cancel
                                    </button>
                                    <button
                                        type="submit"
                                        disabled={payoutProcessing}
                                        className="px-5 py-2 bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs rounded-xl shadow-md transition-all disabled:opacity-50"
                                    >
                                        {payoutProcessing ? 'Disbursing...' : 'Confirm Payout'}
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
