import { useState } from 'react';
import { Head, Link, router } from '@inertiajs/react';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import {
    Repeat,
    Plus,
    Search,
    Filter,
    Eye,
    Edit,
    CheckCircle2,
    Clock,
    XCircle,
    Printer,
    FileSpreadsheet,
} from 'lucide-react';

export default function Index({ loans, q = '', status = '' }) {
    const [search, setSearch] = useState(q);
    const [loanStatus, setLoanStatus] = useState(status);

    const handleFilter = (e) => {
        if (e) e.preventDefault();
        router.get('/inventory/loans', {
            q: search,
            status: loanStatus,
        }, {
            preserveState: true,
            preserveScroll: true,
        });
    };

    const loanList = loans?.data || [];

    return (
        <AuthenticatedLayout title="Material Loans & Gate Passes" header="Inventory & Store">
            <div className="space-y-6">
                {/* Header */}
                <div className="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-2 border-b border-slate-200 dark:border-slate-800">
                    <div>
                        <h1 className="text-xl sm:text-2xl font-extrabold tracking-tight text-slate-900 dark:text-white">
                            Material Loans & Gate Passes
                        </h1>
                        <p className="text-xs sm:text-sm text-slate-500 dark:text-slate-400 mt-0.5">
                            Track borrowed tools, equipment gate passes, and returns across construction project sites.
                        </p>
                    </div>

                    <div className="flex items-center gap-2 self-start sm:self-auto">
                        <a
                            href="/inventory/loans/export"
                            className="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 hover:bg-slate-50 dark:hover:bg-slate-800 text-slate-700 dark:text-slate-200 font-bold text-xs shadow-xs transition-colors cursor-pointer"
                            title="Export all loan records to CSV"
                        >
                            <FileSpreadsheet className="w-4 h-4 text-emerald-600" />
                            <span>Export CSV</span>
                        </a>

                        <Link
                            href="/inventory/loans/create"
                            className="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl bg-blue-900 hover:bg-blue-950 text-white font-bold text-xs shadow-sm transition-colors cursor-pointer"
                        >
                            <Plus className="w-4 h-4" />
                            <span>Issue Gate Pass</span>
                        </Link>
                    </div>
                </div>

                {/* Filter */}
                <div className="rounded-2xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 p-4 shadow-xs">
                    <form onSubmit={handleFilter} className="grid grid-cols-1 sm:grid-cols-3 gap-3">
                        <div className="relative sm:col-span-2">
                            <Search className="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-400 pointer-events-none" />
                            <input
                                type="text"
                                placeholder="Search by borrower name, item name, code..."
                                value={search}
                                onChange={(e) => setSearch(e.target.value)}
                                className="w-full pl-9 pr-3 py-2 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-xs text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-900/20"
                            />
                        </div>

                        <div className="flex items-center gap-2">
                            <select
                                value={loanStatus}
                                onChange={(e) => setLoanStatus(e.target.value)}
                                className="w-full px-3 py-2 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-xs text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-900/20"
                            >
                                <option value="">All Statuses</option>
                                <option value="pending">Pending Approval</option>
                                <option value="approved">Approved & Active</option>
                                <option value="returned">Returned & Closed</option>
                                <option value="rejected">Rejected</option>
                            </select>

                            <button
                                type="submit"
                                className="px-4 py-2 rounded-xl bg-blue-900 hover:bg-blue-950 text-white font-bold text-xs transition-colors shrink-0 cursor-pointer"
                            >
                                Filter
                            </button>
                        </div>
                    </form>
                </div>

                {/* Table */}
                <div className="rounded-2xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 shadow-xs overflow-hidden">
                    <div className="overflow-x-auto">
                        <table className="w-full text-left text-xs">
                            <thead className="bg-slate-50 dark:bg-slate-800/60 text-slate-500 uppercase font-semibold text-[10px] tracking-wider border-b border-slate-200 dark:border-slate-800">
                                <tr>
                                    <th className="py-3.5 px-4">Pass ID</th>
                                    <th className="py-3.5 px-4">Borrower (Personnel)</th>
                                    <th className="py-3.5 px-4">Item & Quantity</th>
                                    <th className="py-3.5 px-4">Expected Return</th>
                                    <th className="py-3.5 px-4">Status</th>
                                    <th className="py-3.5 px-4 text-right">Actions</th>
                                </tr>
                            </thead>
                            <tbody className="divide-y divide-slate-100 dark:divide-slate-800/80">
                                {loanList.length === 0 ? (
                                    <tr>
                                        <td colSpan={6} className="py-8 text-center text-slate-400 text-xs">
                                            No loan or gate pass records found.
                                        </td>
                                    </tr>
                                ) : (
                                    loanList.map((loan) => (
                                        <tr key={loan.id} className="hover:bg-slate-50/70 dark:hover:bg-slate-800/40 transition-colors">
                                            <td className="py-3 px-4 font-mono font-bold text-blue-900 dark:text-blue-400">
                                                #{loan.id}
                                            </td>
                                            <td className="py-3 px-4">
                                                <div className="font-bold text-slate-900 dark:text-white">
                                                    {loan.employee?.first_name} {loan.employee?.last_name}
                                                </div>
                                                <div className="text-[11px] text-slate-400">
                                                    {loan.employee?.position_rel?.name || 'Site Personnel'}
                                                </div>
                                            </td>
                                            <td className="py-3 px-4">
                                                <div className="font-semibold text-slate-800 dark:text-slate-200">
                                                    {loan.item?.name || 'Item'}
                                                </div>
                                                <div className="text-[11px] text-slate-400 font-mono">
                                                    Quantity: {loan.quantity} {loan.item?.unit_of_measurement || 'pcs'}
                                                </div>
                                            </td>
                                            <td className="py-3 px-4 font-mono text-slate-600 dark:text-slate-300">
                                                {loan.expected_return_date ? new Date(loan.expected_return_date).toLocaleDateString() : 'Indefinite'}
                                            </td>
                                            <td className="py-3 px-4">
                                                <span className={`inline-flex items-center gap-1.5 px-2 py-0.5 rounded-full text-[10px] font-bold capitalize ${
                                                    loan.status === 'approved'
                                                        ? 'bg-emerald-50 dark:bg-emerald-950/50 text-emerald-600 dark:text-emerald-400 border border-emerald-200 dark:border-emerald-900'
                                                        : loan.status === 'pending'
                                                        ? 'bg-amber-50 dark:bg-amber-950/50 text-amber-600 dark:text-amber-400 border border-amber-200 dark:border-amber-900'
                                                        : loan.status === 'returned'
                                                        ? 'bg-blue-50 dark:bg-blue-950/50 text-blue-600 dark:text-blue-400 border border-blue-200 dark:border-blue-900'
                                                        : 'bg-rose-50 dark:bg-rose-950/50 text-rose-600 dark:text-rose-400 border border-rose-200 dark:border-rose-900'
                                                }`}>
                                                    {loan.status}
                                                </span>
                                            </td>
                                            <td className="py-3 px-4 text-right">
                                                <div className="flex items-center justify-end gap-1.5">
                                                    <Link
                                                        href={`/inventory/loans/${loan.id}`}
                                                        className="p-1.5 rounded-lg text-slate-500 hover:text-slate-900 dark:hover:text-white hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors"
                                                        title="Print Gate Pass"
                                                    >
                                                        <Printer className="w-3.5 h-3.5" />
                                                    </Link>
                                                    <Link
                                                        href={`/inventory/loans/${loan.id}`}
                                                        className="p-1.5 rounded-lg text-slate-500 hover:text-blue-900 dark:hover:text-white hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors"
                                                        title="View details"
                                                    >
                                                        <Eye className="w-3.5 h-3.5" />
                                                    </Link>
                                                    {loan.status === 'pending' && (
                                                        <Link
                                                            href={`/inventory/loans/${loan.id}/edit`}
                                                            className="p-1.5 rounded-lg text-slate-500 hover:text-blue-900 dark:hover:text-white hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors"
                                                            title="Edit"
                                                        >
                                                            <Edit className="w-3.5 h-3.5" />
                                                        </Link>
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
                    {loans?.links && loans.links.length > 3 && (
                        <div className="px-4 py-3 border-t border-slate-200 dark:border-slate-800 flex items-center justify-between text-xs text-slate-500">
                            <div>Showing {loans.from || 0} to {loans.to || 0} of {loans.total || 0} loans</div>
                            <div className="flex items-center gap-1">
                                {loans.links.map((link, idx) => (
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
