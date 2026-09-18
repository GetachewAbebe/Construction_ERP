import { Head, Link } from '@inertiajs/react';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import {
    Briefcase,
    ArrowLeft,
    MapPin,
    Calendar,
    DollarSign,
    Edit,
    PlusCircle,
    Receipt,
    Clock,
    CheckCircle2,
    XCircle,
} from 'lucide-react';

export default function Show({ project }) {
    const budget = Number(project.budget) || 0;
    const expenses = project.expenses || [];

    const approvedSpent = expenses
        .filter((e) => e.status === 'approved')
        .reduce((sum, e) => sum + (Number(e.amount) || 0), 0);

    const pendingSpent = expenses
        .filter((e) => e.status === 'pending')
        .reduce((sum, e) => sum + (Number(e.amount) || 0), 0);

    const remaining = Math.max(budget - (approvedSpent + pendingSpent), 0);
    const consumptionPct = budget > 0 ? Math.min(Math.round((approvedSpent / budget) * 100), 100) : 0;

    return (
        <AuthenticatedLayout title={project.name} header="Finance & Operations">
            <div className="space-y-6">
                {/* Header */}
                <div className="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-2 border-b border-slate-200 dark:border-slate-800">
                    <div className="flex items-center gap-3">
                        <Link
                            href="/finance/projects"
                            className="p-2 rounded-xl bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 text-slate-600 dark:text-slate-300 transition-colors"
                        >
                            <ArrowLeft className="w-4 h-4" />
                        </Link>
                        <div>
                            <div className="flex items-center gap-2">
                                <h1 className="text-xl font-extrabold tracking-tight text-slate-900 dark:text-white">
                                    {project.name}
                                </h1>
                                <span className={`px-2.5 py-0.5 rounded-full text-[10px] font-bold ${
                                    project.status === 'In Progress'
                                        ? 'bg-emerald-50 dark:bg-emerald-950/50 text-emerald-600 dark:text-emerald-400 border border-emerald-200 dark:border-emerald-900'
                                        : project.status === 'Completed'
                                        ? 'bg-blue-50 dark:bg-blue-950/50 text-blue-600 dark:text-blue-400 border border-blue-200 dark:border-blue-900'
                                        : 'bg-slate-100 text-slate-600 dark:bg-slate-800 dark:text-slate-400'
                                }`}>
                                    {project.status}
                                </span>
                            </div>
                            <div className="flex items-center gap-1 text-xs text-slate-400 mt-0.5">
                                <MapPin className="w-3.5 h-3.5 shrink-0" />
                                <span>{project.location || 'Addis Ababa HQ'}</span>
                            </div>
                        </div>
                    </div>

                    <div className="flex items-center gap-2">
                        <Link
                            href={`/finance/projects/${project.id}/edit`}
                            className="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs font-semibold text-slate-700 dark:text-slate-200 hover:bg-slate-50 dark:hover:bg-slate-700 transition-colors"
                        >
                            <Edit className="w-3.5 h-3.5" />
                            <span>Edit Configuration</span>
                        </Link>
                        <Link
                            href="/finance/expenses/create"
                            className="inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-xl bg-blue-900 hover:bg-blue-950 text-white font-bold text-xs shadow-sm transition-colors cursor-pointer"
                        >
                            <PlusCircle className="w-4 h-4" />
                            <span>Log Expenditure</span>
                        </Link>
                    </div>
                </div>

                {/* Budget Financials */}
                <div className="grid grid-cols-1 sm:grid-cols-4 gap-4">
                    <div className="rounded-2xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 p-4 shadow-xs">
                        <span className="text-[11px] font-semibold text-slate-400 uppercase tracking-wider block">Authorized Budget</span>
                        <div className="text-xl font-black text-slate-900 dark:text-white mt-1 font-mono">
                            ETB {budget.toLocaleString(undefined, { minimumFractionDigits: 2 })}
                        </div>
                    </div>

                    <div className="rounded-2xl border border-emerald-200 dark:border-emerald-900/50 bg-emerald-50/40 dark:bg-emerald-950/20 p-4">
                        <span className="text-[11px] font-semibold text-emerald-600 dark:text-emerald-400 uppercase tracking-wider block">Approved Spent</span>
                        <div className="text-xl font-black text-emerald-700 dark:text-emerald-300 mt-1 font-mono">
                            ETB {approvedSpent.toLocaleString(undefined, { minimumFractionDigits: 2 })}
                        </div>
                    </div>

                    <div className="rounded-2xl border border-amber-200 dark:border-amber-900/50 bg-amber-50/40 dark:bg-amber-950/20 p-4">
                        <span className="text-[11px] font-semibold text-amber-600 dark:text-amber-400 uppercase tracking-wider block">Pending Approvals</span>
                        <div className="text-xl font-black text-amber-700 dark:text-amber-300 mt-1 font-mono">
                            ETB {pendingSpent.toLocaleString(undefined, { minimumFractionDigits: 2 })}
                        </div>
                    </div>

                    <div className="rounded-2xl border border-blue-200 dark:border-blue-900/50 bg-blue-50/40 dark:bg-blue-950/20 p-4">
                        <span className="text-[11px] font-semibold text-blue-600 dark:text-blue-400 uppercase tracking-wider block">Remaining Balance</span>
                        <div className="text-xl font-black text-blue-900 dark:text-blue-200 mt-1 font-mono">
                            ETB {remaining.toLocaleString(undefined, { minimumFractionDigits: 2 })}
                        </div>
                    </div>
                </div>

                {/* Progress bar */}
                <div className="rounded-2xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 p-5 shadow-xs space-y-2">
                    <div className="flex items-center justify-between text-xs">
                        <span className="font-bold text-slate-700 dark:text-slate-300">Capital Absorption Ratio</span>
                        <span className="font-mono font-bold text-blue-900 dark:text-blue-400">{consumptionPct}%</span>
                    </div>
                    <div className="h-2 w-full bg-slate-100 dark:bg-slate-800 rounded-full overflow-hidden">
                        <div
                            className={`h-full rounded-full transition-all ${
                                consumptionPct > 90 ? 'bg-rose-500' : consumptionPct > 70 ? 'bg-amber-500' : 'bg-blue-900 dark:bg-blue-500'
                            }`}
                            style={{ width: `${consumptionPct}%` }}
                        />
                    </div>
                </div>

                {/* Project Description & Metadata */}
                {project.description && (
                    <div className="rounded-2xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 p-5 shadow-xs">
                        <h2 className="text-xs font-bold text-slate-400 uppercase tracking-wider mb-2">Scope & Specifications</h2>
                        <p className="text-xs text-slate-700 dark:text-slate-300 leading-relaxed">
                            {project.description}
                        </p>
                    </div>
                )}

                {/* Expenses Table */}
                <div className="rounded-2xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 shadow-xs overflow-hidden">
                    <div className="px-5 py-4 border-b border-slate-100 dark:border-slate-800 flex items-center justify-between">
                        <h2 className="text-sm font-bold text-slate-900 dark:text-white">
                            Transaction & Expense Journal ({expenses.length})
                        </h2>
                    </div>

                    <div className="overflow-x-auto">
                        <table className="w-full text-left text-xs">
                            <thead className="bg-slate-50 dark:bg-slate-800/60 text-slate-500 uppercase font-semibold text-[10px] tracking-wider border-b border-slate-200 dark:border-slate-800">
                                <tr>
                                    <th className="py-3 px-4">Date</th>
                                    <th className="py-3 px-4">Category & Description</th>
                                    <th className="py-3 px-4">Filed By</th>
                                    <th className="py-3 px-4">Amount (ETB)</th>
                                    <th className="py-3 px-4">Status</th>
                                    <th className="py-3 px-4 text-right">Voucher</th>
                                </tr>
                            </thead>
                            <tbody className="divide-y divide-slate-100 dark:divide-slate-800/80">
                                {expenses.length === 0 ? (
                                    <tr>
                                        <td colSpan={6} className="py-8 text-center text-slate-400 text-xs">
                                            No expenditure transactions charged to this site yet.
                                        </td>
                                    </tr>
                                ) : (
                                    expenses.map((exp) => (
                                        <tr key={exp.id} className="hover:bg-slate-50/70 dark:hover:bg-slate-800/40 transition-colors">
                                            <td className="py-3 px-4 font-mono text-slate-600 dark:text-slate-400 text-[11px]">
                                                {exp.expense_date ? new Date(exp.expense_date).toLocaleDateString() : '—'}
                                            </td>
                                            <td className="py-3 px-4">
                                                <div className="font-bold text-slate-900 dark:text-white">
                                                    {exp.category}
                                                </div>
                                                <div className="text-[11px] text-slate-400 truncate max-w-xs">
                                                    {exp.description || '—'}
                                                </div>
                                            </td>
                                            <td className="py-3 px-4 text-slate-600 dark:text-slate-300">
                                                {exp.user?.name || 'Authorized Agent'}
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
                                                <Link
                                                    href={`/finance/expenses/${exp.id}`}
                                                    className="px-2.5 py-1 rounded-lg bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300 font-semibold text-[11px] transition-colors"
                                                >
                                                    View
                                                </Link>
                                            </td>
                                        </tr>
                                    ))
                                )}
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </AuthenticatedLayout>
    );
}
