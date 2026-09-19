import { Head, Link } from '@inertiajs/react';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import {
    Receipt,
    ArrowLeft,
    Printer,
    Building2,
    Calendar,
    DollarSign,
    User,
    CheckCircle2,
    Clock,
    XCircle,
    FileText,
    ExternalLink,
    Download,
} from 'lucide-react';

export default function Show({ expense }) {
    const handlePrint = () => {
        window.print();
    };

    const voucherId = `EXP-${String(expense.id).padStart(5, '0')}`;
    const amount = Number(expense.amount) || 0;

    return (
        <AuthenticatedLayout title={`Voucher #${voucherId}`} header="Finance & Operations">
            <div className="max-w-4xl mx-auto space-y-6 print:space-y-0 print:max-w-none print:w-full print:p-0 print:m-0">
                {/* Print and Back Controls */}
                <div className="flex items-center justify-between gap-4 pb-2 border-b border-slate-200 dark:border-slate-800 print:hidden no-print">
                    <div className="flex items-center gap-3">
                        <Link
                            href="/finance/expenses"
                            className="p-2 rounded-xl bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 text-slate-600 dark:text-slate-300 transition-colors"
                        >
                            <ArrowLeft className="w-4 h-4" />
                        </Link>
                        <div>
                            <h1 className="text-xl font-extrabold tracking-tight text-slate-900 dark:text-white">
                                Payment Voucher #{voucherId}
                            </h1>
                            <p className="text-xs text-slate-500 dark:text-slate-400 mt-0.5">
                                Official electronic disbursement record and corporate treasury authorization.
                            </p>
                        </div>
                    </div>

                    <div className="flex items-center gap-2">
                        <button
                            onClick={handlePrint}
                            className="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl bg-blue-900 hover:bg-blue-950 text-white font-bold text-xs shadow-sm transition-colors cursor-pointer"
                        >
                            <Printer className="w-4 h-4" />
                            <span>Print Voucher / PDF</span>
                        </button>
                    </div>
                </div>

                {/* Voucher Card (Print-styled) */}
                <div className="rounded-3xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 p-8 shadow-sm space-y-8 print:border-none print:shadow-none print:p-0">
                    {/* Header */}
                    <div className="flex justify-between items-start pb-6 border-b-2 border-blue-900">
                        <div>
                            <h2 className="text-xl font-black tracking-tight text-blue-900 dark:text-blue-400">
                                NATANEM ENGINEERING
                            </h2>
                            <p className="text-[11px] font-semibold text-slate-500 uppercase tracking-wider mt-0.5">
                                Construction, General Contracting & Engineering PLC
                            </p>
                        </div>

                        <div className="text-right">
                            <span className="px-3 py-1 rounded-full text-xs font-bold uppercase tracking-wider bg-blue-50 dark:bg-blue-950/50 text-blue-900 dark:text-blue-300 border border-blue-200 dark:border-blue-900">
                                Payment Voucher
                            </span>
                            <div className="font-mono font-bold text-sm text-slate-900 dark:text-white mt-1">
                                #{voucherId}
                            </div>
                            <div className="text-[11px] text-slate-400 font-mono mt-0.5">
                                {expense.expense_date ? new Date(expense.expense_date).toLocaleDateString(undefined, { year: 'numeric', month: 'long', day: 'numeric' }) : '—'}
                            </div>
                        </div>
                    </div>

                    {/* Metadata Grid */}
                    <div className="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div className="p-4 rounded-xl border border-slate-100 dark:border-slate-800 bg-slate-50/60 dark:bg-slate-800/40">
                            <span className="text-[10px] font-bold text-slate-400 uppercase tracking-wider block mb-1">
                                Project / Cost Center
                            </span>
                            <span className="font-bold text-slate-900 dark:text-white text-sm">
                                {expense.project ? expense.project.name : 'Corporate Overhead / HQ'}
                            </span>
                        </div>

                        <div className="p-4 rounded-xl border border-slate-100 dark:border-slate-800 bg-slate-50/60 dark:bg-slate-800/40">
                            <span className="text-[10px] font-bold text-slate-400 uppercase tracking-wider block mb-1">
                                Expense Category
                            </span>
                            <span className="font-bold text-slate-900 dark:text-white text-sm">
                                {expense.category}
                            </span>
                        </div>

                        <div className="p-4 rounded-xl border border-slate-100 dark:border-slate-800 bg-slate-50/60 dark:bg-slate-800/40">
                            <span className="text-[10px] font-bold text-slate-400 uppercase tracking-wider block mb-1">
                                Prepared / Requested By
                            </span>
                            <span className="font-bold text-slate-900 dark:text-white text-sm">
                                {expense.user?.name || 'Staff Member'}
                            </span>
                        </div>

                        <div className="p-4 rounded-xl border border-slate-100 dark:border-slate-800 bg-slate-50/60 dark:bg-slate-800/40">
                            <span className="text-[10px] font-bold text-slate-400 uppercase tracking-wider block mb-1">
                                Authorization Status
                            </span>
                            <span className={`font-bold text-sm uppercase ${
                                expense.status === 'approved'
                                    ? 'text-emerald-600 dark:text-emerald-400'
                                    : expense.status === 'rejected'
                                    ? 'text-rose-600 dark:text-rose-400'
                                    : 'text-amber-600 dark:text-amber-400'
                            }`}>
                                {expense.status}
                            </span>
                        </div>
                    </div>

                    {/* Justification Box */}
                    <div className="p-5 rounded-xl border border-slate-100 dark:border-slate-800 bg-slate-50/60 dark:bg-slate-800/40">
                        <span className="text-[10px] font-bold text-slate-400 uppercase tracking-wider block mb-2">
                            Expenditure Description & Justification
                        </span>
                        <p className="text-xs text-slate-800 dark:text-slate-200 leading-relaxed">
                            {expense.description || 'Payment disbursed for project operations and site procurement requirements.'}
                        </p>
                    </div>

                    {/* Receipt / Invoice Attachment Card */}
                    {expense.attachment_path && (
                        <div className="p-5 rounded-2xl border border-slate-200 dark:border-slate-800 bg-slate-50/50 dark:bg-slate-800/30 space-y-3">
                            <div className="flex items-center justify-between">
                                <span className="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">
                                    Official Receipt / Invoice Evidence
                                </span>
                                <a
                                    href={expense.attachment_url || `/storage/${expense.attachment_path}`}
                                    target="_blank"
                                    rel="noreferrer"
                                    className="inline-flex items-center gap-1 text-xs font-bold text-blue-600 dark:text-blue-400 hover:underline"
                                >
                                    <span>Open Original</span>
                                    <ExternalLink className="w-3.5 h-3.5" />
                                </a>
                            </div>

                            {/\.(jpg|jpeg|png|webp)$/i.test(expense.attachment_path) ? (
                                <div className="rounded-xl overflow-hidden border border-slate-200 dark:border-slate-700 bg-slate-100 dark:bg-slate-900 max-h-80 flex items-center justify-center">
                                    <img
                                        src={expense.attachment_url || `/storage/${expense.attachment_path}`}
                                        alt="Expense Receipt"
                                        className="max-h-80 w-auto object-contain rounded-lg"
                                    />
                                </div>
                            ) : (
                                <div className="p-4 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-900 flex items-center justify-between">
                                    <div className="flex items-center gap-3">
                                        <div className="w-10 h-10 rounded-xl bg-blue-100 dark:bg-blue-950 text-blue-600 dark:text-blue-400 flex items-center justify-center">
                                            <FileText className="w-5 h-5" />
                                        </div>
                                        <div>
                                            <div className="font-bold text-xs text-slate-900 dark:text-white">
                                                Invoice Document (PDF)
                                            </div>
                                            <div className="text-[10px] text-slate-400">
                                                Electronic verification attachment
                                            </div>
                                        </div>
                                    </div>
                                    <a
                                        href={expense.attachment_url || `/storage/${expense.attachment_path}`}
                                        target="_blank"
                                        rel="noreferrer"
                                        download
                                        className="px-3 py-1.5 rounded-lg bg-blue-900 hover:bg-blue-950 text-white font-bold text-xs flex items-center gap-1.5 transition-colors"
                                    >
                                        <Download className="w-3.5 h-3.5" />
                                        <span>Download</span>
                                    </a>
                                </div>
                            )}
                        </div>
                    )}

                    {/* Total Disbursed Summary Banner */}
                    <div className="p-6 rounded-2xl bg-blue-50 dark:bg-blue-950/40 border-2 border-blue-200 dark:border-blue-800 flex items-center justify-between">
                        <div>
                            <span className="text-xs font-bold text-blue-900 dark:text-blue-300 uppercase tracking-wider block">
                                Total Disbursed Amount
                            </span>
                            <span className="text-[11px] text-blue-700 dark:text-blue-400">
                                Ethiopian Birr (ETB)
                            </span>
                        </div>
                        <div className="text-2xl sm:text-3xl font-black text-blue-900 dark:text-white font-mono">
                            ETB {amount.toLocaleString(undefined, { minimumFractionDigits: 2 })}
                        </div>
                    </div>

                    {/* Signatures */}
                    <div className="grid grid-cols-3 gap-6 pt-10">
                        <div className="border-t border-slate-300 dark:border-slate-700 pt-3 text-center">
                            <span className="text-[10px] font-bold text-slate-500 uppercase tracking-wider block">
                                Prepared By
                            </span>
                            <span className="text-xs font-semibold text-slate-900 dark:text-white mt-1 block">
                                {expense.user?.name || 'Authorized Staff'}
                            </span>
                        </div>

                        <div className="border-t border-slate-300 dark:border-slate-700 pt-3 text-center">
                            <span className="text-[10px] font-bold text-slate-500 uppercase tracking-wider block">
                                Verified / Checked By
                            </span>
                            <span className="text-xs font-semibold text-slate-900 dark:text-white mt-1 block">
                                Internal Auditor
                            </span>
                        </div>

                        <div className="border-t border-slate-300 dark:border-slate-700 pt-3 text-center">
                            <span className="text-[10px] font-bold text-slate-500 uppercase tracking-wider block">
                                Approved By
                            </span>
                            <span className="text-xs font-semibold text-slate-900 dark:text-white mt-1 block">
                                {expense.approved_by?.name || 'Managing Director'}
                            </span>
                        </div>
                    </div>

                    {/* Footer */}
                    <div className="text-center pt-6 border-t border-dashed border-slate-200 dark:border-slate-800 text-[10px] text-slate-400">
                        Generated electronically by Natanem Engineering ERP System · Valid without physical alteration.
                    </div>
                </div>
            </div>
        </AuthenticatedLayout>
    );
}
