import { useState } from 'react';
import { Head, Link, router } from '@inertiajs/react';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import {
    Receipt,
    PlusCircle,
    Search,
    Edit,
    Trash2,
    Eye,
    Calendar,
    DollarSign,
    Building2,
    FileText,
    FileSpreadsheet,
    Paperclip,
} from 'lucide-react';

export default function Index({ expenses, q = '' }) {
    const [search, setSearch] = useState(q);

    const handleFilter = (e) => {
        if (e) e.preventDefault();
        router.get('/finance/expenses', {
            q: search,
        }, {
            preserveState: true,
            preserveScroll: true,
        });
    };

    const handleDelete = (id) => {
        if (confirm('Delete this expenditure entry? Budget funds will be recovered.')) {
            router.delete(`/finance/expenses/${id}`);
        }
    };

    const expenseList = expenses?.data || [];

    return (
        <AuthenticatedLayout title="Expenditure Ledger" header="Finance & Operations">
            <div className="space-y-6">
                {/* Header */}
                <div className="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-2 border-b border-slate-200 dark:border-slate-800">
                    <div>
                        <h1 className="text-xl sm:text-2xl font-extrabold tracking-tight text-slate-900 dark:text-white">
                            Site Expenditures & Vouchers
                        </h1>
                        <p className="text-xs sm:text-sm text-slate-500 dark:text-slate-400 mt-0.5">
                            Track field purchases, materials procurement expenses, and treasury disbursements.
                        </p>
                    </div>

                    <div className="flex items-center gap-2 self-start sm:self-auto">
                        <a
                            href="/finance/expenses/export"
                            className="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 hover:bg-slate-50 dark:hover:bg-slate-800 text-slate-700 dark:text-slate-200 font-bold text-xs shadow-xs transition-colors cursor-pointer"
                            title="Export all expense records to CSV"
                        >
                            <FileSpreadsheet className="w-4 h-4 text-emerald-600" />
                            <span>Export CSV</span>
                        </a>

                        <Link
                            href="/finance/expenses/create"
                            className="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl bg-blue-900 hover:bg-blue-950 text-white font-bold text-xs shadow-sm transition-colors cursor-pointer"
                        >
                            <PlusCircle className="w-4 h-4" />
                            <span>Log Expense</span>
                        </Link>
                    </div>
                </div>

                {/* Filter Form */}
                <div className="rounded-2xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 p-4 shadow-xs">
                    <form onSubmit={handleFilter} className="flex flex-col sm:flex-row gap-3">
                        <div className="relative flex-1">
                            <Search className="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-400 pointer-events-none" />
                            <input
                                type="text"
                                placeholder="Search by category, description..."
                                value={search}
                                onChange={(e) => setSearch(e.target.value)}
                                className="w-full pl-9 pr-3 py-2 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-xs text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-900/20"
                            />
                        </div>

                        <button
                            type="submit"
                            className="px-4 py-2 rounded-xl bg-blue-900 hover:bg-blue-950 text-white font-bold text-xs transition-colors cursor-pointer"
                        >
                            Filter
                        </button>
                    </form>
                </div>

                {/* Table */}
                <div className="rounded-2xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 shadow-xs overflow-hidden">
                    <div className="overflow-x-auto">
                        <table className="w-full text-left text-xs">
                            <thead className="bg-slate-50 dark:bg-slate-800/60 text-slate-500 uppercase font-semibold text-[10px] tracking-wider border-b border-slate-200 dark:border-slate-800">
                                <tr>
                                    <th className="py-3.5 px-4">Expense & Project</th>
                                    <th className="py-3.5 px-4">Date</th>
                                    <th className="py-3.5 px-4">Category</th>
                                    <th className="py-3.5 px-4">Amount (ETB)</th>
                                    <th className="py-3.5 px-4">Status</th>
                                    <th className="py-3.5 px-4 text-right">Actions</th>
                                </tr>
                            </thead>
                            <tbody className="divide-y divide-slate-100 dark:divide-slate-800/80">
                                {expenseList.length === 0 ? (
                                    <tr>
                                        <td colSpan={6} className="py-8 text-center text-slate-400 text-xs">
                                            No expenditures logged.
                                        </td>
                                    </tr>
                                ) : (
                                    expenseList.map((exp) => (
                                        <tr key={exp.id} className="hover:bg-slate-50/70 dark:hover:bg-slate-800/40 transition-colors">
                                            <td className="py-3 px-4">
                                                <div className="flex items-center gap-1.5">
                                                    <span className="font-bold text-slate-900 dark:text-white">
                                                        {exp.description || exp.category}
                                                    </span>
                                                    {exp.attachment_path && (
                                                        <Link
                                                            href={`/finance/expenses/${exp.id}`}
                                                            title="Receipt Document Attached"
                                                            className="text-blue-600 dark:text-blue-400 hover:text-blue-700 inline-flex items-center"
                                                        >
                                                            <Paperclip className="w-3.5 h-3.5" />
                                                        </Link>
                                                    )}
                                                </div>
                                                <div className="text-[11px] text-slate-400">
                                                    Project: {exp.project ? exp.project.name : 'General Overhead'}
                                                </div>
                                            </td>
                                            <td className="py-3 px-4 font-mono text-slate-600 dark:text-slate-300 text-[11px]">
                                                {exp.expense_date ? new Date(exp.expense_date).toLocaleDateString() : '—'}
                                            </td>
                                            <td className="py-3 px-4">
                                                <span className="px-2 py-0.5 rounded-md bg-slate-100 dark:bg-slate-800 font-semibold text-slate-700 dark:text-slate-300 text-[11px]">
                                                    {exp.category}
                                                </span>
                                            </td>
                                            <td className="py-3 px-4 font-mono font-bold text-slate-900 dark:text-white">
                                                {Number(exp.amount).toLocaleString(undefined, { minimumFractionDigits: 2 })}
                                            </td>
                                            <td className="py-3 px-4">
                                                <span className={`inline-block px-2 py-0.5 rounded-full text-[10px] font-bold ${
                                                    exp.status === 'approved'
                                                        ? 'bg-emerald-50 dark:bg-emerald-950/50 text-emerald-600 dark:text-emerald-400 border border-emerald-200 dark:border-emerald-900'
                                                        : exp.status === 'rejected'
                                                        ? 'bg-rose-50 dark:bg-rose-950/50 text-rose-600 dark:text-rose-400 border border-rose-200 dark:border-rose-900'
                                                        : 'bg-amber-50 dark:bg-amber-950/50 text-amber-600 dark:text-amber-400 border border-amber-200 dark:border-amber-900'
                                                }`}>
                                                    {exp.status}
                                                </span>
                                            </td>
                                            <td className="py-3 px-4 text-right">
                                                <div className="flex items-center justify-end gap-1.5">
                                                    <Link
                                                        href={`/finance/expenses/${exp.id}`}
                                                        className="p-1.5 rounded-lg text-slate-500 hover:text-blue-900 dark:hover:text-white hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors"
                                                        title="Voucher"
                                                    >
                                                        <Eye className="w-3.5 h-3.5" />
                                                    </Link>
                                                    {exp.status === 'pending' && (
                                                        <>
                                                            <Link
                                                                href={`/finance/expenses/${exp.id}/edit`}
                                                                className="p-1.5 rounded-lg text-slate-500 hover:text-blue-900 dark:hover:text-white hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors"
                                                                title="Edit"
                                                            >
                                                                <Edit className="w-3.5 h-3.5" />
                                                            </Link>
                                                            <button
                                                                onClick={() => handleDelete(exp.id)}
                                                                className="p-1.5 rounded-lg text-slate-500 hover:text-rose-600 dark:hover:text-rose-400 hover:bg-rose-50 dark:hover:bg-rose-950/40 transition-colors cursor-pointer"
                                                                title="Delete"
                                                            >
                                                                <Trash2 className="w-3.5 h-3.5" />
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
                    </div>

                    {/* Pagination */}
                    {expenses?.links && expenses.links.length > 3 && (
                        <div className="px-4 py-3 border-t border-slate-200 dark:border-slate-800 flex items-center justify-between text-xs text-slate-500">
                            <div>
                                Showing {expenses.from || 0} to {expenses.to || 0} of {expenses.total || 0} expenses
                            </div>
                            <div className="flex items-center gap-1">
                                {expenses.links.map((link, idx) => (
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
