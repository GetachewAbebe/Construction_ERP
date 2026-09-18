import { Head, Link } from '@inertiajs/react';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import {
    Briefcase,
    Banknote,
    CreditCard,
    Wallet,
    Plus,
    Building2,
    TrendingUp,
} from 'lucide-react';

export default function FinanceDashboard({
    totalProjects = 0,
    totalBudget = 0,
    totalExpenses = 0,
    remainingBudget = 0,
    usagePercentage = 0,
    recentProjects = [],
    portfolioLabels = [],
    portfolioBudgets = [],
    portfolioExpenses = [],
}) {
    const formatCurrency = (val) => {
        return 'ETB ' + Number(val || 0).toLocaleString('en-US', {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2,
        });
    };

    return (
        <AuthenticatedLayout title="Finance Dashboard" header="Financial Treasury">
            <div className="space-y-6">
                {/* Hero Header */}
                <div className="rounded-2xl bg-gradient-to-r from-emerald-950 via-teal-900 to-slate-900 p-6 sm:p-7 text-white shadow-md relative overflow-hidden">
                    <div className="relative z-10 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                        <div>
                            <div className="flex items-center gap-2 mb-1">
                                <span className="px-2.5 py-0.5 rounded-full bg-emerald-500/20 text-emerald-300 text-[10px] font-bold tracking-wider uppercase border border-emerald-500/30">
                                    Capital & Disbursals
                                </span>
                            </div>
                            <h1 className="text-2xl sm:text-3xl font-black tracking-tight">
                                Financial Treasury & Budgets
                            </h1>
                            <p className="text-xs sm:text-sm text-slate-300 mt-1">
                                Project capital allocations, field expenditure audits, and payment voucher workflows.
                            </p>
                        </div>

                        <div className="flex items-center gap-2 shrink-0">
                            <Link
                                href="/finance/expenses/create"
                                className="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl bg-emerald-500 hover:bg-emerald-600 text-slate-950 font-bold text-xs shadow-sm transition-colors"
                            >
                                <Plus className="w-4 h-4" />
                                <span>Record Expense</span>
                            </Link>
                            <Link
                                href="/finance/projects"
                                className="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl bg-white/10 text-white font-bold text-xs border border-white/20 hover:bg-white/20 transition-colors"
                            >
                                <Briefcase className="w-4 h-4 text-emerald-300" />
                                <span>Projects</span>
                            </Link>
                        </div>
                    </div>
                </div>

                {/* 4 KPIs */}
                <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                    <div className="rounded-2xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 p-5 shadow-xs flex items-center justify-between">
                        <div>
                            <div className="text-[11px] font-bold uppercase tracking-wider text-slate-400">Total Projects</div>
                            <div className="text-3xl font-black text-blue-900 dark:text-blue-400 font-mono mt-1">{totalProjects}</div>
                            <div className="text-[11px] text-slate-500 mt-0.5">Active Portfolios</div>
                        </div>
                        <div className="p-3 rounded-xl bg-blue-50 dark:bg-blue-950/60 text-blue-900 dark:text-blue-400">
                            <Briefcase className="w-6 h-6" />
                        </div>
                    </div>

                    <div className="rounded-2xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 p-5 shadow-xs flex items-center justify-between">
                        <div>
                            <div className="text-[11px] font-bold uppercase tracking-wider text-slate-400">Total Capital Budget</div>
                            <div className="text-xl sm:text-2xl font-black text-slate-900 dark:text-white font-mono mt-1">{formatCurrency(totalBudget)}</div>
                            <div className="text-[11px] text-slate-500 mt-0.5">Contract Valuation</div>
                        </div>
                        <div className="p-3 rounded-xl bg-emerald-50 dark:bg-emerald-950/60 text-emerald-700 dark:text-emerald-400">
                            <Banknote className="w-6 h-6" />
                        </div>
                    </div>

                    <div className="rounded-2xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 p-5 shadow-xs flex items-center justify-between">
                        <div>
                            <div className="text-[11px] font-bold uppercase tracking-wider text-slate-400">Cumulative Expenses</div>
                            <div className="text-xl sm:text-2xl font-black text-amber-600 dark:text-amber-400 font-mono mt-1">{formatCurrency(totalExpenses)}</div>
                            <div className="text-[11px] text-slate-500 mt-0.5">Disbursed Capital</div>
                        </div>
                        <div className="p-3 rounded-xl bg-amber-50 dark:bg-amber-950/60 text-amber-600 dark:text-amber-400">
                            <CreditCard className="w-6 h-6" />
                        </div>
                    </div>

                    <div className="rounded-2xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 p-5 shadow-xs flex items-center justify-between">
                        <div>
                            <div className="text-[11px] font-bold uppercase tracking-wider text-slate-400">Treasury Reserve</div>
                            <div className="text-xl sm:text-2xl font-black text-emerald-600 dark:text-emerald-400 font-mono mt-1">{formatCurrency(remainingBudget)}</div>
                            <div className="text-[11px] text-slate-500 mt-0.5">Unallocated Balance</div>
                        </div>
                        <div className="p-3 rounded-xl bg-emerald-50 dark:bg-emerald-950/60 text-emerald-600 dark:text-emerald-400">
                            <Wallet className="w-6 h-6" />
                        </div>
                    </div>
                </div>

                {/* Overall Portfolio Budget Consumption */}
                <div className="rounded-2xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 p-6 shadow-xs">
                    <div className="flex items-center justify-between text-xs mb-2">
                        <span className="font-bold text-slate-900 dark:text-white">Overall Portfolio Budget Consumption</span>
                        <span className="font-mono font-bold text-slate-800 dark:text-slate-200">{usagePercentage}% Utilized</span>
                    </div>
                    <div className="h-3 w-full rounded-full bg-slate-100 dark:bg-slate-800 overflow-hidden">
                        <div
                            className="h-full rounded-full bg-gradient-to-r from-emerald-600 to-teal-500 transition-all duration-500"
                            style={{ width: `${Math.min(100, usagePercentage)}%` }}
                        />
                    </div>
                </div>

                {/* Project Portfolio Breakdown */}
                <div className="rounded-2xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 p-5 shadow-xs">
                    <div className="flex items-center justify-between mb-4">
                        <h3 className="text-sm font-bold text-slate-900 dark:text-white">Project Portfolios & Expenditure Status</h3>
                        <Link href="/finance/projects" className="text-xs font-bold text-blue-900 dark:text-blue-400 hover:underline">
                            View All Projects &rarr;
                        </Link>
                    </div>

                    {portfolioLabels.length === 0 ? (
                        <p className="text-xs text-slate-400 py-6 text-center">No projects recorded in the system.</p>
                    ) : (
                        <div className="divide-y divide-slate-100 dark:divide-slate-800">
                            {portfolioLabels.map((name, i) => {
                                const budget = Number(portfolioBudgets[i] || 0);
                                const spent = Number(portfolioExpenses[i] || 0);
                                const pct = budget > 0 ? Math.min(100, Math.round((spent / budget) * 100)) : 0;

                                return (
                                    <div key={i} className="py-3 space-y-1.5 text-xs">
                                        <div className="flex items-center justify-between">
                                            <span className="font-bold text-slate-900 dark:text-white">{name}</span>
                                            <span className="font-mono font-semibold text-slate-600 dark:text-slate-400">
                                                {formatCurrency(spent)} of {formatCurrency(budget)} ({pct}%)
                                            </span>
                                        </div>
                                        <div className="h-2 w-full rounded-full bg-slate-100 dark:bg-slate-800 overflow-hidden">
                                            <div
                                                className={`h-full rounded-full ${
                                                    pct > 90
                                                        ? 'bg-rose-500'
                                                        : pct > 75
                                                        ? 'bg-amber-500'
                                                        : 'bg-emerald-500'
                                                }`}
                                                style={{ width: `${pct}%` }}
                                            />
                                        </div>
                                    </div>
                                );
                            })}
                        </div>
                    )}
                </div>
            </div>
        </AuthenticatedLayout>
    );
}
