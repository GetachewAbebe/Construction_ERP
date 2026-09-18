import { Head, Link, useForm } from '@inertiajs/react';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import {
    Receipt,
    ArrowLeft,
    Building2,
    Calendar,
    DollarSign,
    PlusCircle,
    Paperclip,
    UploadCloud,
    X,
    FileCheck
} from 'lucide-react';

export default function Create({ projects = [] }) {
    const { data, setData, post, processing, errors } = useForm({
        project_id: projects.length > 0 ? projects[0].id : '',
        category: 'Materials',
        amount: '',
        description: '',
        reference_no: '',
        attachment: null,
        expense_date: new Date().toISOString().split('T')[0],
    });

    const handleSubmit = (e) => {
        e.preventDefault();
        post('/finance/expenses', {
            forceFormData: true,
        });
    };

    return (
        <AuthenticatedLayout title="Log Field Expenditure" header="Finance & Operations">
            <div className="max-w-3xl mx-auto space-y-6">
                <div className="flex items-center justify-between gap-4 pb-2 border-b border-slate-200 dark:border-slate-800">
                    <div className="flex items-center gap-3">
                        <Link
                            href="/finance/expenses"
                            className="p-2 rounded-xl bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 text-slate-600 dark:text-slate-300 transition-colors"
                        >
                            <ArrowLeft className="w-4 h-4" />
                        </Link>
                        <div>
                            <h1 className="text-xl font-extrabold tracking-tight text-slate-900 dark:text-white">
                                Record Field Expenditure
                            </h1>
                            <p className="text-xs text-slate-500 dark:text-slate-400 mt-0.5">
                                Submit a site purchase, subcontract disbursement, or operational cost for treasury validation.
                            </p>
                        </div>
                    </div>
                </div>

                <form onSubmit={handleSubmit} className="space-y-6">
                    <div className="rounded-2xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 p-6 shadow-xs space-y-6">
                        <div className="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div className="sm:col-span-2">
                                <label className="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">
                                    Target Construction Site / Project <span className="text-rose-500">*</span>
                                </label>
                                <select
                                    required
                                    value={data.project_id}
                                    onChange={(e) => setData('project_id', e.target.value)}
                                    className="w-full px-3 py-2 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-xs text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-900/20"
                                >
                                    <option value="">Select project...</option>
                                    {projects.map((p) => (
                                        <option key={p.id} value={p.id}>
                                            {p.name} (Budget: ETB {Number(p.budget).toLocaleString()})
                                        </option>
                                    ))}
                                </select>
                                {errors.project_id && (
                                    <p className="mt-1 text-[11px] text-rose-500">{errors.project_id}</p>
                                )}
                            </div>

                            <div>
                                <label className="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">
                                    Expense Category <span className="text-rose-500">*</span>
                                </label>
                                <select
                                    required
                                    value={data.category}
                                    onChange={(e) => setData('category', e.target.value)}
                                    className="w-full px-3 py-2 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-xs text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-900/20"
                                >
                                    <option value="Materials">Materials & Cement</option>
                                    <option value="Fuel & Machinery">Fuel & Machinery Dispatch</option>
                                    <option value="Labor & Daily Wages">Labor & Daily Wages</option>
                                    <option value="Equipment Rental">Equipment Rental</option>
                                    <option value="Transport & Logistics">Transport & Logistics</option>
                                    <option value="Permits & Legal">Permits & Legal</option>
                                    <option value="Site Utilities & Overhead">Site Utilities & Overhead</option>
                                    <option value="Other">Other Expenses</option>
                                </select>
                                {errors.category && (
                                    <p className="mt-1 text-[11px] text-rose-500">{errors.category}</p>
                                )}
                            </div>

                            <div>
                                <label className="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">
                                    Expense Date <span className="text-rose-500">*</span>
                                </label>
                                <div className="relative">
                                    <Calendar className="w-3.5 h-3.5 text-slate-400 absolute left-3 top-1/2 -translate-y-1/2 pointer-events-none" />
                                    <input
                                        type="date"
                                        required
                                        value={data.expense_date}
                                        onChange={(e) => setData('expense_date', e.target.value)}
                                        className="w-full pl-9 pr-3 py-2 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-xs text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-900/20"
                                    />
                                </div>
                                {errors.expense_date && (
                                    <p className="mt-1 text-[11px] text-rose-500">{errors.expense_date}</p>
                                )}
                            </div>

                            <div>
                                <label className="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">
                                    Expenditure Amount (ETB) <span className="text-rose-500">*</span>
                                </label>
                                <div className="relative">
                                    <DollarSign className="w-3.5 h-3.5 text-slate-400 absolute left-3 top-1/2 -translate-y-1/2 pointer-events-none" />
                                    <input
                                        type="number"
                                        step="0.01"
                                        required
                                        value={data.amount}
                                        onChange={(e) => setData('amount', e.target.value)}
                                        placeholder="0.00"
                                        className="w-full pl-9 pr-3 py-2 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-xs text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-900/20 font-mono"
                                    />
                                </div>
                                {errors.amount && (
                                    <p className="mt-1 text-[11px] text-rose-500">{errors.amount}</p>
                                )}
                            </div>

                            <div>
                                <label className="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">
                                    Reference / Invoice No.
                                </label>
                                <div className="relative">
                                    <Receipt className="w-3.5 h-3.5 text-slate-400 absolute left-3 top-1/2 -translate-y-1/2 pointer-events-none" />
                                    <input
                                        type="text"
                                        value={data.reference_no}
                                        onChange={(e) => setData('reference_no', e.target.value)}
                                        placeholder="e.g. INV-2026-084 or REC-9921"
                                        className="w-full pl-9 pr-3 py-2 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-xs text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-900/20"
                                    />
                                </div>
                                {errors.reference_no && (
                                    <p className="mt-1 text-[11px] text-rose-500">{errors.reference_no}</p>
                                )}
                            </div>

                            <div className="sm:col-span-2">
                                <label className="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">
                                    Item Description & Voucher Details
                                </label>
                                <textarea
                                    rows={3}
                                    value={data.description}
                                    onChange={(e) => setData('description', e.target.value)}
                                    placeholder="Specify invoice number, supplier details, delivery receipt reference..."
                                    className="w-full px-3 py-2 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-xs text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-900/20"
                                />
                                {errors.description && (
                                    <p className="mt-1 text-[11px] text-rose-500">{errors.description}</p>
                                )}
                            </div>

                            {/* RECEIPT / ATTACHMENT DROPZONE */}
                            <div className="sm:col-span-2">
                                <label className="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">
                                    Receipt or Invoice Attachment (PDF / Image)
                                </label>
                                {data.attachment ? (
                                    <div className="p-3.5 rounded-xl border border-blue-200 dark:border-blue-900/60 bg-blue-50/50 dark:bg-blue-950/20 flex items-center justify-between">
                                        <div className="flex items-center gap-2.5 min-w-0">
                                            <div className="w-8 h-8 rounded-lg bg-blue-600 text-white flex items-center justify-center shrink-0">
                                                <FileCheck className="w-4 h-4" />
                                            </div>
                                            <div className="min-w-0">
                                                <p className="text-xs font-bold text-slate-900 dark:text-white truncate">
                                                    {data.attachment.name}
                                                </p>
                                                <p className="text-[10px] text-slate-500 dark:text-slate-400">
                                                    {(data.attachment.size / 1024).toFixed(1)} KB · Ready to upload
                                                </p>
                                            </div>
                                        </div>
                                        <button
                                            type="button"
                                            onClick={() => setData('attachment', null)}
                                            className="p-1 rounded-lg text-slate-400 hover:text-rose-500 hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors"
                                            title="Remove attachment"
                                        >
                                            <X className="w-4 h-4" />
                                        </button>
                                    </div>
                                ) : (
                                    <label className="flex flex-col items-center justify-center p-5 rounded-2xl border-2 border-dashed border-slate-200 dark:border-slate-800 hover:border-blue-500/50 hover:bg-blue-50/20 dark:hover:bg-blue-950/10 cursor-pointer transition-all">
                                        <UploadCloud className="w-6 h-6 text-slate-400 mb-1.5" />
                                        <span className="text-xs font-bold text-slate-700 dark:text-slate-300">
                                            Click to browse or drop receipt
                                        </span>
                                        <span className="text-[11px] text-slate-400 mt-0.5">
                                            PDF, PNG, JPG, or WEBP up to 10MB
                                        </span>
                                        <input
                                            type="file"
                                            accept=".pdf,.jpg,.jpeg,.png,.webp"
                                            onChange={(e) => {
                                                if (e.target.files && e.target.files[0]) {
                                                    setData('attachment', e.target.files[0]);
                                                }
                                            }}
                                            className="hidden"
                                        />
                                    </label>
                                )}
                                {errors.attachment && (
                                    <p className="mt-1 text-[11px] text-rose-500">{errors.attachment}</p>
                                )}
                            </div>
                        </div>

                        <div className="pt-4 border-t border-slate-100 dark:border-slate-800 flex items-center justify-end gap-3">
                            <Link
                                href="/finance/expenses"
                                className="px-4 py-2 rounded-xl border border-slate-200 dark:border-slate-700 text-slate-600 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-800 text-xs font-bold transition-colors"
                            >
                                Cancel
                            </Link>
                            <button
                                type="submit"
                                disabled={processing}
                                className="inline-flex items-center gap-1.5 px-5 py-2 rounded-xl bg-blue-900 hover:bg-blue-950 text-white font-bold text-xs shadow-sm transition-colors cursor-pointer disabled:opacity-50"
                            >
                                <PlusCircle className="w-4 h-4" />
                                <span>{processing ? 'Logging...' : 'Log Expenditure'}</span>
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </AuthenticatedLayout>
    );
}
