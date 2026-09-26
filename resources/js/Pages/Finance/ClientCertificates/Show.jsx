import { useState } from 'react';
import { Head, Link, router, useForm } from '@inertiajs/react';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import {
    FileText,
    ArrowLeft,
    CheckCircle2,
    Clock,
    Printer,
    DollarSign,
    PiggyBank,
    Building2,
    Calendar,
    Receipt,
    Plus,
    X,
} from 'lucide-react';

export default function Show({ certificate }) {
    const [paymentModalOpen, setPaymentModalOpen] = useState(false);

    const { data, setData, post, processing, errors, reset } = useForm({
        amount: certificate.balance_due || '',
        payment_date: new Date().toISOString().split('T')[0],
        payment_method: 'Bank Transfer',
        bank_name: 'Commercial Bank of Ethiopia',
        transaction_reference: '',
        notes: '',
    });

    const handleCertify = () => {
        if (confirm(`Mark Certificate ${certificate.certificate_no} as officially certified by the Supervising Consultant?`)) {
            router.post(`/finance/client-certificates/${certificate.id}/certify`);
        }
    };

    const handleRecordPayment = (e) => {
        e.preventDefault();
        post(`/finance/client-certificates/${certificate.id}/payments`, {
            onSuccess: () => {
                setPaymentModalOpen(false);
                reset();
            },
        });
    };

    const getStatusBadge = (st) => {
        switch (st) {
            case 'paid':
                return <span className="inline-flex items-center gap-1 px-3 py-1 rounded-full text-xs font-bold bg-emerald-50 dark:bg-emerald-950/60 text-emerald-700 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-800"><CheckCircle2 className="w-3.5 h-3.5" /> Fully Paid</span>;
            case 'partially_paid':
                return <span className="inline-flex items-center gap-1 px-3 py-1 rounded-full text-xs font-bold bg-blue-50 dark:bg-blue-950/60 text-blue-700 dark:text-blue-300 border border-blue-200 dark:border-blue-800"><Clock className="w-3.5 h-3.5" /> Partially Paid</span>;
            case 'certified':
                return <span className="inline-flex items-center gap-1 px-3 py-1 rounded-full text-xs font-bold bg-purple-50 dark:bg-purple-950/60 text-purple-700 dark:text-purple-300 border border-purple-200 dark:border-purple-800"><CheckCircle2 className="w-3.5 h-3.5" /> Consultant Certified</span>;
            case 'submitted':
                return <span className="inline-flex items-center gap-1 px-3 py-1 rounded-full text-xs font-bold bg-amber-50 dark:bg-amber-950/60 text-amber-700 dark:text-amber-300 border border-amber-200 dark:border-amber-800"><Clock className="w-3.5 h-3.5" /> Under Review</span>;
            default:
                return <span className="px-3 py-1 rounded-full text-xs font-bold bg-slate-100 dark:bg-slate-800 text-slate-700">{st}</span>;
        }
    };

    const totalCollected = parseFloat(certificate.amount_paid) || 0;
    const totalCertified = parseFloat(certificate.total_certified_amount) || 0;
    const paymentPct = totalCertified > 0 ? Math.min(100, Math.round((totalCollected / totalCertified) * 100)) : 0;

    return (
        <AuthenticatedLayout title={`Certificate ${certificate.certificate_no}`} header="Finance & Commercial">
            <div className="max-w-5xl mx-auto space-y-6">
                {/* Header */}
                <div className="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-2 border-b border-slate-200 dark:border-slate-800">
                    <div>
                        <div className="flex items-center gap-3">
                            <h1 className="text-xl sm:text-2xl font-black tracking-tight text-slate-900 dark:text-white">
                                {certificate.certificate_no}
                            </h1>
                            {getStatusBadge(certificate.status)}
                        </div>
                        <p className="text-xs sm:text-sm text-slate-500 dark:text-slate-400 mt-1">
                            {certificate.project?.name} • IPC #{certificate.ipc_sequence} • Period: {certificate.period_start ? new Date(certificate.period_start).toLocaleDateString() : ''} - {certificate.period_end ? new Date(certificate.period_end).toLocaleDateString() : ''}
                        </p>
                    </div>

                    <div className="flex items-center gap-2">
                        <Link
                            href="/finance/client-certificates"
                            className="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl border border-slate-200 dark:border-slate-800 text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800 font-bold text-xs transition-colors"
                        >
                            <ArrowLeft className="w-3.5 h-3.5" />
                            <span>Back</span>
                        </Link>

                        <a
                            href={`/finance/client-certificates/${certificate.id}/print`}
                            target="_blank"
                            rel="noreferrer"
                            className="inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-xl border border-slate-200 dark:border-slate-800 text-slate-700 dark:text-slate-200 hover:bg-slate-100 dark:hover:bg-slate-800 text-xs font-bold transition-colors cursor-pointer"
                        >
                            <Printer className="w-3.5 h-3.5" />
                            <span>Print Certificate / PDF</span>
                        </a>

                        {certificate.status === 'submitted' && (
                            <button
                                type="button"
                                onClick={handleCertify}
                                className="inline-flex items-center gap-1.5 px-4 py-1.5 rounded-xl bg-purple-600 hover:bg-purple-700 text-white text-xs font-bold shadow-md shadow-purple-500/20 transition-all cursor-pointer"
                            >
                                <CheckCircle2 className="w-3.5 h-3.5" />
                                <span>Mark Certified</span>
                            </button>
                        )}

                        {parseFloat(certificate.balance_due) > 0 && (
                            <button
                                type="button"
                                onClick={() => setPaymentModalOpen(true)}
                                className="inline-flex items-center gap-1.5 px-4 py-1.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold shadow-md shadow-emerald-500/20 transition-all cursor-pointer"
                            >
                                <DollarSign className="w-3.5 h-3.5" />
                                <span>Record Payment Deposit</span>
                            </button>
                        )}
                    </div>
                </div>

                {/* Progress Valuation Bar */}
                <div className="rounded-2xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 p-4">
                    <div className="flex items-center justify-between text-xs font-bold mb-1.5">
                        <span className="text-slate-600 dark:text-slate-300">Client Revenue Inflow / Collection</span>
                        <span className="text-emerald-600 dark:text-emerald-400">{paymentPct}% Collected</span>
                    </div>
                    <div className="w-full h-2.5 rounded-full bg-slate-100 dark:bg-slate-800 overflow-hidden">
                        <div
                            className="h-full bg-emerald-500 rounded-full transition-all duration-500"
                            style={{ width: `${paymentPct}%` }}
                        />
                    </div>
                    <div className="flex justify-between text-[11px] text-slate-400 mt-1">
                        <span>{Number(certificate.amount_paid).toLocaleString()} ETB paid</span>
                        <span>Balance Due: {Number(certificate.balance_due).toLocaleString()} ETB</span>
                    </div>
                </div>

                {/* Valuation Breakdown Table */}
                <div className="rounded-2xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 overflow-hidden shadow-xs">
                    <div className="p-4 border-b border-slate-200 dark:border-slate-800 flex items-center justify-between">
                        <h2 className="text-sm font-bold text-slate-800 dark:text-slate-200 uppercase tracking-wider">
                            Certified Valuation Breakdown
                        </h2>
                    </div>

                    <div className="p-6 space-y-3 text-xs">
                        <div className="flex justify-between py-1 border-b border-slate-100 dark:border-slate-800">
                            <span className="text-slate-600 dark:text-slate-400">1. Cumulative Gross Value of Work to Date:</span>
                            <span className="font-bold text-slate-900 dark:text-white">{Number(certificate.cumulative_gross_amount).toLocaleString(undefined, { minimumFractionDigits: 2 })} ETB</span>
                        </div>
                        <div className="flex justify-between py-1 border-b border-slate-100 dark:border-slate-800 text-slate-500">
                            <span>2. Less: Previous Cumulative Gross Certified:</span>
                            <span>({Number(certificate.previous_gross_amount).toLocaleString(undefined, { minimumFractionDigits: 2 })}) ETB</span>
                        </div>
                        <div className="flex justify-between py-1.5 bg-slate-50 dark:bg-slate-800/40 px-2 rounded-lg font-bold">
                            <span className="text-indigo-600 dark:text-indigo-400">3. Current Gross Value of Work Done (1 - 2):</span>
                            <span className="text-indigo-600 dark:text-indigo-400">{Number(certificate.current_gross_amount).toLocaleString(undefined, { minimumFractionDigits: 2 })} ETB</span>
                        </div>
                        {parseFloat(certificate.materials_on_site) > 0 && (
                            <div className="flex justify-between py-1 border-b border-slate-100 dark:border-slate-800">
                                <span>4. Add: Materials on Site (MOS):</span>
                                <span className="font-semibold">{Number(certificate.materials_on_site).toLocaleString(undefined, { minimumFractionDigits: 2 })} ETB</span>
                            </div>
                        )}
                        <div className="flex justify-between py-1 border-b border-slate-100 dark:border-slate-800 text-amber-600 dark:text-amber-400">
                            <span>5. Less: Retention Money Deduction ({certificate.retention_rate}%):</span>
                            <span className="font-semibold">({Number(certificate.retention_deduction).toLocaleString(undefined, { minimumFractionDigits: 2 })}) ETB</span>
                        </div>
                        {parseFloat(certificate.advance_deduction) > 0 && (
                            <div className="flex justify-between py-1 border-b border-slate-100 dark:border-slate-800 text-rose-600 dark:text-rose-400">
                                <span>6. Less: Mobilization Advance Recoupment ({certificate.advance_recoupment_rate}%):</span>
                                <span className="font-semibold">({Number(certificate.advance_deduction).toLocaleString(undefined, { minimumFractionDigits: 2 })}) ETB</span>
                            </div>
                        )}
                        <div className="flex justify-between py-1 border-b border-slate-100 dark:border-slate-800 font-bold">
                            <span>7. Subtotal Net Interim Valuation:</span>
                            <span>{Number(certificate.subtotal_net_amount).toLocaleString(undefined, { minimumFractionDigits: 2 })} ETB</span>
                        </div>
                        <div className="flex justify-between py-1 border-b border-slate-100 dark:border-slate-800">
                            <span>8. Value Added Tax (VAT {certificate.tax_rate}%):</span>
                            <span className="font-semibold">{Number(certificate.tax_amount).toLocaleString(undefined, { minimumFractionDigits: 2 })} ETB</span>
                        </div>
                        <div className="flex justify-between py-2 text-sm font-black text-indigo-700 dark:text-indigo-300 bg-indigo-50/50 dark:bg-indigo-950/30 px-3 rounded-xl border border-indigo-200 dark:border-indigo-900">
                            <span>TOTAL CERTIFIED CLAIM AMOUNT:</span>
                            <span>{Number(certificate.total_certified_amount).toLocaleString(undefined, { minimumFractionDigits: 2 })} ETB</span>
                        </div>
                    </div>
                </div>

                {/* Scope of Work */}
                <div className="rounded-2xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 p-6 space-y-2">
                    <h3 className="text-xs font-bold text-slate-500 uppercase tracking-wider">
                        Work Executed This Period:
                    </h3>
                    <p className="text-xs text-slate-800 dark:text-slate-200 font-medium">
                        {certificate.work_description}
                    </p>
                </div>

                {/* Payment Collections History */}
                <div className="rounded-2xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 p-6 space-y-4">
                    <div className="flex items-center justify-between">
                        <h2 className="text-sm font-bold text-slate-800 dark:text-slate-200 uppercase tracking-wider flex items-center gap-2">
                            <Receipt className="w-4 h-4 text-emerald-600" />
                            Client Payment Receipts ({certificate.receipts?.length || 0})
                        </h2>

                        {parseFloat(certificate.balance_due) > 0 && (
                            <button
                                type="button"
                                onClick={() => setPaymentModalOpen(true)}
                                className="inline-flex items-center gap-1.5 px-3 py-1.5 bg-emerald-50 dark:bg-emerald-950/60 hover:bg-emerald-100 text-emerald-700 dark:text-emerald-300 rounded-xl text-xs font-bold transition-colors cursor-pointer border border-emerald-200 dark:border-emerald-800"
                            >
                                <Plus className="w-3.5 h-3.5" />
                                <span>Record Payment</span>
                            </button>
                        )}
                    </div>

                    {(!certificate.receipts || certificate.receipts.length === 0) ? (
                        <div className="p-6 text-center border border-dashed border-slate-200 dark:border-slate-800 rounded-xl text-slate-400 text-xs">
                            No payment receipts recorded yet against this claim.
                        </div>
                    ) : (
                        <div className="space-y-2">
                            {certificate.receipts.map((rcp) => (
                                <div key={rcp.id} className="p-3.5 rounded-xl border border-slate-200 dark:border-slate-800 flex items-center justify-between text-xs">
                                    <div>
                                        <div className="font-bold text-emerald-600">{rcp.receipt_no}</div>
                                        <div className="text-[11px] text-slate-500 mt-0.5">
                                            {rcp.payment_method} • {rcp.bank_name || 'Bank'} • Ref: <strong>{rcp.transaction_reference || 'N/A'}</strong> • {new Date(rcp.payment_date).toLocaleDateString()}
                                        </div>
                                    </div>
                                    <div className="text-right">
                                        <div className="font-black text-slate-900 dark:text-white text-sm">
                                            +{Number(rcp.amount).toLocaleString(undefined, { minimumFractionDigits: 2 })} ETB
                                        </div>
                                        <div className="text-[10px] text-slate-400">Received by {rcp.receiver?.name || 'Cashier'}</div>
                                    </div>
                                </div>
                            ))}
                        </div>
                    )}
                </div>
            </div>

            {/* Record Payment Modal */}
            {paymentModalOpen && (
                <div className="fixed inset-0 z-50 bg-black/60 backdrop-blur-xs flex items-center justify-center p-4">
                    <div className="w-full max-w-md rounded-2xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 p-6 shadow-2xl space-y-4">
                        <div className="flex items-center justify-between">
                            <div className="flex items-center gap-2 text-emerald-600">
                                <DollarSign className="w-5 h-5" />
                                <h3 className="text-base font-bold text-slate-900 dark:text-white">Record Client Payment</h3>
                            </div>
                            <button
                                type="button"
                                onClick={() => setPaymentModalOpen(false)}
                                className="text-slate-400 hover:text-slate-600"
                            >
                                <X className="w-5 h-5" />
                            </button>
                        </div>

                        <p className="text-xs text-slate-500 dark:text-slate-400">
                            Record client fund deposit against certificate <strong>{certificate.certificate_no}</strong>. Remaining balance: <strong>{Number(certificate.balance_due).toLocaleString()} ETB</strong>.
                        </p>

                        <form onSubmit={handleRecordPayment} className="space-y-3 text-xs">
                            <div>
                                <label className="block font-bold text-slate-700 dark:text-slate-300 mb-1">
                                    Amount Received (ETB) <span className="text-rose-500">*</span>
                                </label>
                                <input
                                    type="number"
                                    step="0.01"
                                    min="0.01"
                                    required
                                    value={data.amount}
                                    onChange={(e) => setData('amount', e.target.value)}
                                    className="w-full px-3 py-2 rounded-xl border border-slate-200 dark:border-slate-800 bg-slate-50 dark:bg-slate-950 font-bold text-emerald-600 text-sm"
                                />
                            </div>

                            <div className="grid grid-cols-2 gap-3">
                                <div>
                                    <label className="block font-bold text-slate-700 dark:text-slate-300 mb-1">
                                        Payment Date <span className="text-rose-500">*</span>
                                    </label>
                                    <input
                                        type="date"
                                        required
                                        value={data.payment_date}
                                        onChange={(e) => setData('payment_date', e.target.value)}
                                        className="w-full px-3 py-2 rounded-xl border border-slate-200 dark:border-slate-800 bg-slate-50 dark:bg-slate-950"
                                    />
                                </div>

                                <div>
                                    <label className="block font-bold text-slate-700 dark:text-slate-300 mb-1">
                                        Payment Method <span className="text-rose-500">*</span>
                                    </label>
                                    <select
                                        value={data.payment_method}
                                        onChange={(e) => setData('payment_method', e.target.value)}
                                        className="w-full px-3 py-2 rounded-xl border border-slate-200 dark:border-slate-800 bg-slate-50 dark:bg-slate-950 font-semibold"
                                    >
                                        <option value="Bank Transfer">Bank Transfer / RTGS</option>
                                        <option value="CPO">Certified Payment Order (CPO)</option>
                                        <option value="Check">Corporate Check</option>
                                        <option value="Cash">Cash Deposit</option>
                                    </select>
                                </div>
                            </div>

                            <div className="grid grid-cols-2 gap-3">
                                <div>
                                    <label className="block font-bold text-slate-700 dark:text-slate-300 mb-1">
                                        Bank Name
                                    </label>
                                    <input
                                        type="text"
                                        value={data.bank_name}
                                        onChange={(e) => setData('bank_name', e.target.value)}
                                        placeholder="e.g. CBE, Awash..."
                                        className="w-full px-3 py-2 rounded-xl border border-slate-200 dark:border-slate-800 bg-slate-50 dark:bg-slate-950"
                                    />
                                </div>

                                <div>
                                    <label className="block font-bold text-slate-700 dark:text-slate-300 mb-1">
                                        Slip / CPO Ref #
                                    </label>
                                    <input
                                        type="text"
                                        value={data.transaction_reference}
                                        onChange={(e) => setData('transaction_reference', e.target.value)}
                                        placeholder="e.g. CPO-192842"
                                        className="w-full px-3 py-2 rounded-xl border border-slate-200 dark:border-slate-800 bg-slate-50 dark:bg-slate-950 font-mono"
                                    />
                                </div>
                            </div>

                            <div>
                                <label className="block font-bold text-slate-700 dark:text-slate-300 mb-1">
                                    Notes
                                </label>
                                <input
                                    type="text"
                                    value={data.notes}
                                    onChange={(e) => setData('notes', e.target.value)}
                                    placeholder="e.g. Cleared into Company CBE Account"
                                    className="w-full px-3 py-2 rounded-xl border border-slate-200 dark:border-slate-800 bg-slate-50 dark:bg-slate-950"
                                />
                            </div>

                            <div className="flex items-center justify-end gap-2 pt-2">
                                <button
                                    type="button"
                                    onClick={() => setPaymentModalOpen(false)}
                                    className="px-3.5 py-2 font-bold rounded-xl border border-slate-200 dark:border-slate-800 text-slate-600 dark:text-slate-300 hover:bg-slate-100"
                                >
                                    Cancel
                                </button>
                                <button
                                    type="submit"
                                    disabled={processing}
                                    className="px-4 py-2 font-bold rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white"
                                >
                                    {processing ? 'Saving...' : 'Record Deposit'}
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            )}
        </AuthenticatedLayout>
    );
}
