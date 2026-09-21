import { useState } from 'react';
import { Head, Link } from '@inertiajs/react';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import CostTrendChart from '@/Components/Dashboard/CostTrendChart';
import DonutBreakdownChart from '@/Components/Dashboard/DonutBreakdownChart';
import {
    Briefcase,
    Banknote,
    CreditCard,
    Wallet,
    Plus,
    Building2,
    TrendingUp,
    Clock,
    CheckCircle2,
    AlertTriangle,
    ArrowRight,
    ArrowUpRight,
    FileText,
    Printer,
    Search,
    ShieldCheck,
    DollarSign,
} from 'lucide-react';

export default function FinanceDashboard({
    totalProjects = 0,
    totalBudget = 0,
    totalExpenses = 0,
    remainingBudget = 0,
    usagePercentage = 0,
    avgWeeklyBurn = 0,
    pendingExpenseCount = 0,
    pendingExpenseAmount = 0,
    pendingExpenses = [],
    recentTransactions = [],
    expenseCategories = [],
    monthlyCashFlow = [],
    projectBreakdown = [],
    portfolioLabels = [],
    portfolioBudgets = [],
    portfolioExpenses = [],
}) {
    const [projectSearch, setProjectSearch] = useState('');

    const formatCurrency = (val) => {
        return 'ETB ' + Number(val || 0).toLocaleString('en-US', {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2,
        });
    };

    const formatShortCurrency = (num) => {
        const val = Number(num || 0);
        if (val >= 1000000) {
            return `ETB ${(val / 1000000).toFixed(2)}M`;
        }
        if (val >= 1000) {
            return `ETB ${(val / 1000).toFixed(0)}K`;
        }
        return `ETB ${val.toLocaleString()}`;
    };

    // Filter project breakdown if search used
    const filteredProjects = projectBreakdown.filter(p =>
        (p.name || '').toLowerCase().includes(projectSearch.toLowerCase()) ||
        (p.location || '').toLowerCase().includes(projectSearch.toLowerCase())
    );

    return (
        <AuthenticatedLayout title="Finance Treasury Dashboard" header="Financial Treasury">
            <div className="space-y-6">
                {/* HERO HEADER */}
                <div className="rounded-3xl bg-gradient-to-r from-emerald-950 via-teal-900 to-slate-900 p-6 sm:p-8 text-white shadow-lg relative overflow-hidden">
                    <div className="absolute right-0 top-0 translate-x-12 -translate-y-12 w-96 h-96 bg-emerald-500/10 rounded-full blur-3xl pointer-events-none" />
                    
                    <div className="relative z-10 flex flex-col lg:flex-row lg:items-center lg:justify-between gap-6">
                        <div>
                            <div className="flex items-center gap-2 mb-2">
                                <span className="px-2.5 py-0.5 rounded-full bg-emerald-500/20 text-emerald-300 text-[10px] font-extrabold tracking-wider uppercase border border-emerald-500/30 flex items-center gap-1.5">
                                    <span className="w-1.5 h-1.5 rounded-full bg-emerald-400 animate-pulse" />
                                    Treasury &amp; Disbursements Command
                                </span>
                                <span className="text-xs text-slate-300 hidden sm:inline">
                                    FY {new Date().getFullYear()} Active Portfolios
                                </span>
                            </div>
                            <h1 className="text-2xl sm:text-3xl font-black tracking-tight text-white">
                                Capital Commitments &amp; Budget Pacing
                            </h1>
                            <p className="text-xs sm:text-sm text-slate-300 mt-1 max-w-2xl">
                                Real-time project capital allocations, contractor expense audits, payment voucher clearing, and treasury burn rate analytics.
                            </p>
                        </div>

                        <div className="flex flex-wrap items-center gap-2.5 shrink-0">
                            <Link
                                href="/finance/expenses/create"
                                className="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-emerald-500 hover:bg-emerald-400 text-slate-950 font-extrabold text-xs shadow-md shadow-emerald-500/20 transition-all cursor-pointer"
                            >
                                <Plus className="w-4 h-4" />
                                <span>Record Expenditure</span>
                            </Link>

                            <Link
                                href="/admin/requests/finance"
                                className="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-white/10 hover:bg-white/20 text-white font-bold text-xs border border-white/20 backdrop-blur-sm transition-all cursor-pointer relative"
                            >
                                <Banknote className="w-4 h-4 text-emerald-300" />
                                <span>Requisitions Queue</span>
                                {pendingExpenseCount > 0 && (
                                    <span className="px-1.5 py-0.2 rounded-full bg-amber-500 text-slate-950 text-[10px] font-black ml-0.5">
                                        {pendingExpenseCount}
                                    </span>
                                )}
                            </Link>

                            <Link
                                href="/finance/projects"
                                className="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-white/10 hover:bg-white/20 text-white font-bold text-xs border border-white/20 backdrop-blur-sm transition-all cursor-pointer"
                            >
                                <Briefcase className="w-4 h-4 text-teal-300" />
                                <span>Projects Ledger</span>
                            </Link>
                        </div>
                    </div>
                </div>

                {/* 5 TOP KPI METRICS */}
                <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4">
                    {/* 1. Total Capital Budget */}
                    <div className="rounded-2xl sm:rounded-3xl border border-slate-100 dark:border-slate-800/80 bg-white dark:bg-slate-900 p-5 shadow-xs flex flex-col justify-between">
                        <div className="flex items-start justify-between gap-2">
                            <div>
                                <span className="text-[11px] font-bold uppercase tracking-wider text-slate-400">
                                    Total Capital Budget
                                </span>
                                <div className="text-xl sm:text-2xl font-black text-slate-900 dark:text-white font-mono mt-1">
                                    {formatShortCurrency(totalBudget)}
                                </div>
                            </div>
                            <div className="p-2.5 rounded-2xl bg-blue-50 dark:bg-blue-950/60 text-blue-600 dark:text-blue-400 shrink-0">
                                <Briefcase className="w-5 h-5" />
                            </div>
                        </div>
                        <div className="mt-3 pt-2.5 border-t border-slate-100 dark:border-slate-800 flex items-center justify-between text-[11px]">
                            <span className="text-slate-500">{totalProjects} Active Portfolios</span>
                            <span className="font-semibold text-blue-600 dark:text-blue-400">Allocated</span>
                        </div>
                    </div>

                    {/* 2. Cumulative Expended */}
                    <div className="rounded-2xl sm:rounded-3xl border border-slate-100 dark:border-slate-800/80 bg-white dark:bg-slate-900 p-5 shadow-xs flex flex-col justify-between">
                        <div className="flex items-start justify-between gap-2">
                            <div>
                                <span className="text-[11px] font-bold uppercase tracking-wider text-slate-400">
                                    Disbursed Outflows
                                </span>
                                <div className="text-xl sm:text-2xl font-black text-amber-600 dark:text-amber-400 font-mono mt-1">
                                    {formatShortCurrency(totalExpenses)}
                                </div>
                            </div>
                            <div className="p-2.5 rounded-2xl bg-amber-50 dark:bg-amber-950/60 text-amber-600 dark:text-amber-400 shrink-0">
                                <CreditCard className="w-5 h-5" />
                            </div>
                        </div>
                        <div className="mt-3 pt-2.5 border-t border-slate-100 dark:border-slate-800 flex items-center justify-between text-[11px]">
                            <span className="text-slate-500">Burn Ratio</span>
                            <span className="font-bold text-amber-600 dark:text-amber-400 font-mono">{usagePercentage}% Used</span>
                        </div>
                    </div>

                    {/* 3. Treasury Reserve */}
                    <div className="rounded-2xl sm:rounded-3xl border border-slate-100 dark:border-slate-800/80 bg-white dark:bg-slate-900 p-5 shadow-xs flex flex-col justify-between">
                        <div className="flex items-start justify-between gap-2">
                            <div>
                                <span className="text-[11px] font-bold uppercase tracking-wider text-slate-400">
                                    Treasury Reserve
                                </span>
                                <div className="text-xl sm:text-2xl font-black text-emerald-600 dark:text-emerald-400 font-mono mt-1">
                                    {formatShortCurrency(remainingBudget)}
                                </div>
                            </div>
                            <div className="p-2.5 rounded-2xl bg-emerald-50 dark:bg-emerald-950/60 text-emerald-600 dark:text-emerald-400 shrink-0">
                                <Wallet className="w-5 h-5" />
                            </div>
                        </div>
                        <div className="mt-3 pt-2.5 border-t border-slate-100 dark:border-slate-800 flex items-center justify-between text-[11px]">
                            <span className="text-slate-500">Uncommitted</span>
                            <span className="font-bold text-emerald-600 dark:text-emerald-400 font-mono">
                                {totalBudget > 0 ? (100 - usagePercentage).toFixed(1) : 100}% Buffer
                            </span>
                        </div>
                    </div>

                    {/* 4. Pending Requisitions */}
                    <div className={`rounded-2xl sm:rounded-3xl border p-5 shadow-xs flex flex-col justify-between transition-colors ${
                        pendingExpenseCount > 0
                            ? 'border-rose-200 dark:border-rose-900/60 bg-rose-50/40 dark:bg-rose-950/20'
                            : 'border-slate-100 dark:border-slate-800/80 bg-white dark:bg-slate-900'
                    }`}>
                        <div className="flex items-start justify-between gap-2">
                            <div>
                                <span className="text-[11px] font-bold uppercase tracking-wider text-slate-400">
                                    Pending Clearance
                                </span>
                                <div className="text-xl sm:text-2xl font-black text-rose-600 dark:text-rose-400 font-mono mt-1">
                                    {pendingExpenseCount} Vouchers
                                </div>
                            </div>
                            <div className="p-2.5 rounded-2xl bg-rose-100 dark:bg-rose-950/80 text-rose-600 dark:text-rose-400 shrink-0">
                                <Clock className="w-5 h-5" />
                            </div>
                        </div>
                        <div className="mt-3 pt-2.5 border-t border-slate-100 dark:border-slate-800/80 flex items-center justify-between text-[11px]">
                            <span className="text-slate-500">Amount Awaiting</span>
                            <span className="font-bold text-rose-600 dark:text-rose-400 font-mono">
                                {formatShortCurrency(pendingExpenseAmount)}
                            </span>
                        </div>
                    </div>

                    {/* 5. 30-Day Outflow Run Rate */}
                    <div className="rounded-2xl sm:rounded-3xl border border-slate-100 dark:border-slate-800/80 bg-white dark:bg-slate-900 p-5 shadow-xs flex flex-col justify-between">
                        <div className="flex items-start justify-between gap-2">
                            <div>
                                <span className="text-[11px] font-bold uppercase tracking-wider text-slate-400">
                                    Weekly Outflow Run Rate
                                </span>
                                <div className="text-xl sm:text-2xl font-black text-teal-600 dark:text-teal-400 font-mono mt-1">
                                    {formatShortCurrency(avgWeeklyBurn)}
                                </div>
                            </div>
                            <div className="p-2.5 rounded-2xl bg-teal-50 dark:bg-teal-950/60 text-teal-600 dark:text-teal-400 shrink-0">
                                <TrendingUp className="w-5 h-5" />
                            </div>
                        </div>
                        <div className="mt-3 pt-2.5 border-t border-slate-100 dark:border-slate-800 flex items-center justify-between text-[11px]">
                            <span className="text-slate-500">Trailing 30 Days</span>
                            <span className="font-semibold text-teal-600 dark:text-teal-400">Pacing Safe</span>
                        </div>
                    </div>
                </div>

                {/* OVERALL PORTFOLIO BUDGET CONSUMPTION GAUGE */}
                <div className="rounded-2xl sm:rounded-3xl border border-slate-100 dark:border-slate-800/80 bg-white dark:bg-slate-900 p-5 sm:p-6 shadow-xs">
                    <div className="flex flex-col sm:flex-row sm:items-center justify-between gap-2 mb-3">
                        <div className="flex items-center gap-2">
                            <span className="w-3 h-3 rounded-full bg-emerald-500" />
                            <h3 className="text-xs sm:text-sm font-bold text-slate-900 dark:text-white">
                                Enterprise Budget Burn &amp; Consumption Capacity
                            </h3>
                        </div>
                        <span className="font-mono text-xs font-bold text-slate-700 dark:text-slate-300">
                            {formatCurrency(totalExpenses)} expended of {formatCurrency(totalBudget)} ({usagePercentage}%)
                        </span>
                    </div>
                    <div className="h-3.5 w-full rounded-full bg-slate-100 dark:bg-slate-800 overflow-hidden relative">
                        <div
                            className="h-full rounded-full bg-gradient-to-r from-emerald-500 via-teal-500 to-amber-500 transition-all duration-700"
                            style={{ width: `${Math.min(100, usagePercentage)}%` }}
                        />
                    </div>
                    <div className="flex items-center justify-between text-[10px] text-slate-400 mt-2 font-mono">
                        <span>0% (Commencement)</span>
                        <span>50% (Milestone Midpoint)</span>
                        <span>75% (Target Completion)</span>
                        <span className="text-rose-500 font-bold">100% (Ceiling)</span>
                    </div>
                </div>

                {/* VISUAL ANALYTICS ROW: CASH FLOW TRAJECTORY + CATEGORY BREAKDOWN */}
                <div className="grid grid-cols-1 lg:grid-cols-12 gap-6 items-stretch">
                    {/* Trailing 6 Months Cash Flow Outflow */}
                    <div className="lg:col-span-7 flex flex-col">
                        <CostTrendChart
                            data={monthlyCashFlow}
                            title="Capital Outflows &amp; Disbursal Trajectory"
                            currency="ETB"
                        />
                    </div>

                    {/* Expense Categories Distribution */}
                    <div className="lg:col-span-5 flex flex-col">
                        <DonutBreakdownChart
                            title="Expenditure Breakdown by Cost Head"
                            subtitle="Disbursements grouped by category"
                            data={expenseCategories}
                            valueKey="total"
                            labelKey="category"
                            centerLabel="Total Spend"
                            currency="ETB"
                        />
                    </div>
                </div>

                {/* PENDING EXPENSE REQUISITIONS HUB (ACTIONABLE QUEUE) */}
                {pendingExpenses.length > 0 && (
                    <div className="rounded-2xl sm:rounded-3xl border border-amber-200 dark:border-amber-900/60 bg-gradient-to-br from-amber-50/50 via-white to-amber-50/20 dark:from-amber-950/20 dark:via-slate-900 dark:to-slate-900 p-5 sm:p-7 shadow-xs">
                        <div className="flex flex-col sm:flex-row sm:items-center justify-between gap-3 mb-5">
                            <div>
                                <div className="flex items-center gap-2">
                                    <span className="px-2 py-0.5 rounded-full bg-amber-500 text-slate-950 text-[10px] font-black uppercase tracking-wider">
                                        Action Required
                                    </span>
                                    <h3 className="text-base sm:text-lg font-bold text-slate-900 dark:text-white">
                                        Pending Expense Requisitions ({pendingExpenseCount})
                                    </h3>
                                </div>
                                <p className="text-xs text-slate-500 dark:text-slate-400 mt-0.5">
                                    Project vouchers awaiting CFO signature and financial clearance.
                                </p>
                            </div>
                            <Link
                                href="/admin/requests/finance"
                                className="inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-xl bg-amber-500 hover:bg-amber-600 text-slate-950 font-bold text-xs shadow-xs transition-colors cursor-pointer"
                            >
                                <span>Open Approvals Desk</span>
                                <ArrowRight className="w-3.5 h-3.5" />
                            </Link>
                        </div>

                        <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                            {pendingExpenses.map((exp) => (
                                <div
                                    key={exp.id}
                                    className="p-4 rounded-2xl bg-white dark:bg-slate-800/80 border border-slate-200 dark:border-slate-700/70 shadow-xs flex flex-col justify-between gap-3"
                                >
                                    <div>
                                        <div className="flex items-center justify-between gap-2 mb-2">
                                            <span className="px-2 py-0.5 rounded-md bg-slate-100 dark:bg-slate-700 text-slate-700 dark:text-slate-300 text-[10px] font-bold">
                                                {exp.category ? exp.category.toUpperCase() : 'GENERAL'}
                                            </span>
                                            <span className="text-[10px] text-slate-400 font-mono">
                                                {exp.expense_date ? new Date(exp.expense_date).toLocaleDateString() : 'Pending'}
                                            </span>
                                        </div>
                                        <div className="text-lg font-black text-slate-900 dark:text-white font-mono">
                                            {formatCurrency(exp.amount)}
                                        </div>
                                        <p className="text-xs font-semibold text-slate-700 dark:text-slate-300 mt-1 truncate">
                                            {exp.description || 'Project expense claim'}
                                        </p>
                                        <div className="text-[11px] text-slate-400 mt-1 flex items-center gap-1">
                                            <Building2 className="w-3 h-3 text-slate-400" />
                                            <span className="truncate">{exp.project?.name || 'Site Operations'}</span>
                                        </div>
                                    </div>

                                    <div className="pt-3 border-t border-slate-100 dark:border-slate-700 flex items-center justify-between gap-2">
                                        <span className="text-[10px] text-slate-400">
                                            By: <strong className="text-slate-600 dark:text-slate-300">{exp.user?.name || 'Staff'}</strong>
                                        </span>
                                        <Link
                                            href="/admin/requests/finance"
                                            className="px-2.5 py-1 rounded-lg bg-amber-500 hover:bg-amber-400 text-slate-950 font-bold text-[11px] transition-colors"
                                        >
                                            Review
                                        </Link>
                                    </div>
                                </div>
                            ))}
                        </div>
                    </div>
                )}

                {/* PROJECT PORTFOLIO BUDGET VARIANCE TABLE */}
                <div className="rounded-2xl sm:rounded-3xl border border-slate-100 dark:border-slate-800/80 bg-white dark:bg-slate-900 p-5 sm:p-7 shadow-xs">
                    <div className="flex flex-col sm:flex-row sm:items-center justify-between gap-3 mb-5">
                        <div>
                            <h3 className="text-base sm:text-lg font-bold text-slate-900 dark:text-white flex items-center gap-2">
                                <Briefcase className="w-5 h-5 text-emerald-600" />
                                <span>Project Capital Allocation &amp; Overrun Status</span>
                            </h3>
                            <p className="text-xs text-slate-500 dark:text-slate-400 mt-0.5">
                                Contract commitments, disbursed actuals, and cost overrun risk indexing.
                            </p>
                        </div>

                        <div className="flex items-center gap-3">
                            <div className="relative">
                                <Search className="w-3.5 h-3.5 absolute left-3 top-1/2 -translate-y-1/2 text-slate-400" />
                                <input
                                    type="text"
                                    placeholder="Filter projects..."
                                    value={projectSearch}
                                    onChange={(e) => setProjectSearch(e.target.value)}
                                    className="pl-8 pr-3 py-1.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-xs text-slate-900 dark:text-white placeholder-slate-400 focus:outline-none focus:ring-1 focus:ring-emerald-500"
                                />
                            </div>
                            <Link
                                href="/finance/projects"
                                className="text-xs font-bold text-emerald-600 dark:text-emerald-400 hover:underline shrink-0"
                            >
                                All Projects &rarr;
                            </Link>
                        </div>
                    </div>

                    {filteredProjects.length === 0 ? (
                        <div className="py-8 text-center text-xs text-slate-400">
                            No project portfolio records match your search.
                        </div>
                    ) : (
                        <div className="overflow-x-auto">
                            <table className="w-full text-left text-xs">
                                <thead>
                                    <tr className="border-b border-slate-100 dark:border-slate-800 text-slate-400 uppercase tracking-wider text-[10px]">
                                        <th className="pb-3 font-bold">Project Name &amp; Site</th>
                                        <th className="pb-3 font-bold">Contract Budget</th>
                                        <th className="pb-3 font-bold">Disbursed (Actual)</th>
                                        <th className="pb-3 font-bold">Remaining Buffer</th>
                                        <th className="pb-3 font-bold">Pacing Gauge</th>
                                        <th className="pb-3 font-bold text-right">Risk Assessment</th>
                                    </tr>
                                </thead>
                                <tbody className="divide-y divide-slate-100 dark:divide-slate-800/80">
                                    {filteredProjects.map((p) => {
                                        const pct = p.usage_pct || 0;
                                        const isOverrun = pct > 90;
                                        const isCaution = pct > 75 && !isOverrun;

                                        return (
                                            <tr key={p.id} className="hover:bg-slate-50 dark:hover:bg-slate-800/40 transition-colors">
                                                <td className="py-3 pr-3">
                                                    <div className="font-bold text-slate-900 dark:text-white">
                                                        {p.name}
                                                    </div>
                                                    <div className="text-[11px] text-slate-400 flex items-center gap-1 mt-0.5">
                                                        <Building2 className="w-3 h-3 text-slate-400" />
                                                        <span>{p.location}</span>
                                                    </div>
                                                </td>
                                                <td className="py-3 font-mono font-bold text-slate-800 dark:text-slate-200">
                                                    {formatCurrency(p.budget)}
                                                </td>
                                                <td className="py-3 font-mono font-bold text-amber-600 dark:text-amber-400">
                                                    {formatCurrency(p.spent)}
                                                </td>
                                                <td className="py-3 font-mono font-bold text-emerald-600 dark:text-emerald-400">
                                                    {formatCurrency(p.remaining)}
                                                </td>
                                                <td className="py-3 min-w-[130px]">
                                                    <div className="flex items-center justify-between text-[10px] font-mono mb-1 text-slate-500">
                                                        <span>{pct}%</span>
                                                    </div>
                                                    <div className="h-2 w-full rounded-full bg-slate-100 dark:bg-slate-800 overflow-hidden">
                                                        <div
                                                            className={`h-full rounded-full ${
                                                                isOverrun
                                                                    ? 'bg-rose-500'
                                                                    : isCaution
                                                                    ? 'bg-amber-500'
                                                                    : 'bg-emerald-500'
                                                            }`}
                                                            style={{ width: `${Math.min(100, pct)}%` }}
                                                        />
                                                    </div>
                                                </td>
                                                <td className="py-3 text-right">
                                                    <span className={`inline-block px-2.5 py-0.5 rounded-full text-[10px] font-bold ${
                                                        isOverrun
                                                            ? 'bg-rose-100 dark:bg-rose-950/60 text-rose-700 dark:text-rose-300 border border-rose-200 dark:border-rose-900/60'
                                                            : isCaution
                                                            ? 'bg-amber-100 dark:bg-amber-950/60 text-amber-700 dark:text-amber-300 border border-amber-200 dark:border-amber-900/60'
                                                            : 'bg-emerald-100 dark:bg-emerald-950/60 text-emerald-700 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-900/60'
                                                    }`}>
                                                        {p.status_label || (isOverrun ? 'Critical Overrun' : (isCaution ? 'Caution Buffer' : 'On Target'))}
                                                    </span>
                                                </td>
                                            </tr>
                                        );
                                    })}
                                </tbody>
                            </table>
                        </div>
                    )}
                </div>

                {/* RECENT APPROVED TRANSACTIONS & FIELD VOUCHERS LEDGER */}
                <div className="rounded-2xl sm:rounded-3xl border border-slate-100 dark:border-slate-800/80 bg-white dark:bg-slate-900 p-5 sm:p-7 shadow-xs">
                    <div className="flex flex-col sm:flex-row sm:items-center justify-between gap-3 mb-5">
                        <div>
                            <h3 className="text-base sm:text-lg font-bold text-slate-900 dark:text-white flex items-center gap-2">
                                <CreditCard className="w-5 h-5 text-teal-600" />
                                <span>Recent Approved Disbursements &amp; Vouchers</span>
                            </h3>
                            <p className="text-xs text-slate-500 dark:text-slate-400 mt-0.5">
                                Verified expense transactions with instant printable vouchers and field receipts.
                            </p>
                        </div>
                        <Link
                            href="/finance/expenses"
                            className="text-xs font-bold text-emerald-600 dark:text-emerald-400 hover:underline"
                        >
                            Full Expense Register &rarr;
                        </Link>
                    </div>

                    {recentTransactions.length === 0 ? (
                        <div className="py-8 text-center text-xs text-slate-400">
                            No approved financial transactions recorded yet.
                        </div>
                    ) : (
                        <div className="divide-y divide-slate-100 dark:divide-slate-800/80">
                            {recentTransactions.map((tx) => (
                                <div key={tx.id} className="py-3.5 flex flex-col sm:flex-row sm:items-center justify-between gap-3 text-xs">
                                    <div className="flex items-center gap-3">
                                        <div className="w-9 h-9 rounded-xl bg-emerald-50 dark:bg-emerald-950/50 text-emerald-600 dark:text-emerald-400 flex items-center justify-center shrink-0">
                                            <CheckCircle2 className="w-5 h-5" />
                                        </div>
                                        <div>
                                            <div className="font-bold text-slate-900 dark:text-white flex items-center gap-2">
                                                <span>{tx.description || 'Project Expenditure'}</span>
                                                {tx.reference_no && (
                                                    <span className="text-[10px] font-mono px-1.5 py-0.2 rounded bg-slate-100 dark:bg-slate-800 text-slate-500">
                                                        #{tx.reference_no}
                                                    </span>
                                                )}
                                            </div>
                                            <div className="text-[11px] text-slate-400 mt-0.5 flex flex-wrap items-center gap-2">
                                                <span>{tx.project?.name || 'General Project'}</span>
                                                <span>·</span>
                                                <span className="font-semibold text-slate-600 dark:text-slate-300">
                                                    {tx.category ? tx.category.toUpperCase() : 'EXPENSE'}
                                                </span>
                                                <span>·</span>
                                                <span>{tx.expense_date ? new Date(tx.expense_date).toLocaleDateString() : 'Today'}</span>
                                            </div>
                                        </div>
                                    </div>

                                    <div className="flex items-center justify-between sm:justify-end gap-3 shrink-0">
                                        <div className="text-right">
                                            <div className="font-mono font-bold text-slate-900 dark:text-white text-sm">
                                                {formatCurrency(tx.amount)}
                                            </div>
                                            <span className="text-[10px] text-emerald-600 dark:text-emerald-400 font-semibold flex items-center justify-end gap-1">
                                                <ShieldCheck className="w-3 h-3" />
                                                Authorized
                                            </span>
                                        </div>
                                        <a
                                            href={`/finance/expenses/${tx.id}/print`}
                                            target="_blank"
                                            rel="noreferrer"
                                            className="p-2 rounded-xl border border-slate-200 dark:border-slate-700 hover:bg-slate-100 dark:hover:bg-slate-800 text-slate-600 dark:text-slate-300 transition-colors"
                                            title="Print Expense Voucher"
                                        >
                                            <Printer className="w-4 h-4" />
                                        </a>
                                    </div>
                                </div>
                            ))}
                        </div>
                    )}
                </div>
            </div>
        </AuthenticatedLayout>
    );
}
