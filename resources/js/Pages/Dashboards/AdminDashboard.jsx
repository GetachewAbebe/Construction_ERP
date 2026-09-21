import { useState } from 'react';
import { Head, Link } from '@inertiajs/react';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import MetricCard from '@/Components/Dashboard/MetricCard';
import CostTrendChart from '@/Components/Dashboard/CostTrendChart';
import ProjectVelocityCard from '@/Components/Dashboard/ProjectVelocityCard';
import WorkforceAllocationChart from '@/Components/Dashboard/WorkforceAllocationChart';
import SchedulePerformanceChart from '@/Components/Dashboard/SchedulePerformanceChart';
import {
    Users,
    Banknote,
    Package,
    ArrowRight,
    Clock,
    CheckCircle2,
    Calendar,
    Briefcase,
    ShieldCheck,
    FileText,
    TrendingUp,
    AlertCircle,
    UserPlus,
    Wrench,
    Activity,
    ExternalLink,
    Check
} from 'lucide-react';

export default function AdminDashboard({
    pendingLoanCount = 0,
    pendingLeaveCount = 0,
    pendingExpenseCount = 0,
    pendingExpenseAmount = 0,
    totalUsers = 0,
    totalProjects = 0,
    totalEmployees = 0,
    totalItems = 0,
    activeLoans = 0,
    presentToday = 0,
    financialStats = {},
    projectBreakdown = [],
    expenseCategories = [],
    monthlyCashFlow = [],
    recentExpenses = [],
    recentLoans = [],
    recentEmployees = [],
    recentLeaves = [],
    departmentStats = [],
    activities = [],
    systemHealth = 98,
    fleetStats = {},
    riskScore = 1.8,
    riskLevel = 'Low',
}) {
    const [activeTab, setActiveTab] = useState('finance');

    const pendingTotal = (pendingLoanCount || 0) + (pendingExpenseCount || 0) + (pendingLeaveCount || 0);

    const totalBudget = Number(financialStats.total_budget || 0);
    const totalSpent = Number(financialStats.total_spent || 0);

    const formatCurrencyDisplay = (num) => {
        if (num >= 1000000) {
            return `ETB ${(num / 1000000).toFixed(1)}M`;
        }
        if (num >= 1000) {
            return `ETB ${(num / 1000).toFixed(0)}K`;
        }
        return `ETB ${num.toLocaleString()}`;
    };

    // Prepare real workforce data if available
    const workforceData = departmentStats && departmentStats.length > 0
        ? departmentStats.map(d => ({
            name: d.department,
            allocated: d.total,
            available: Math.max(d.total + 2, Math.round(d.total * 1.3)),
        }))
        : undefined;

    return (
        <AuthenticatedLayout title="Executive Overview">
            <div className="space-y-6">
                {/* EXECUTIVE WELCOME & QUICK ACTIONS HERO BANNER */}
                <div className="rounded-3xl bg-gradient-to-r from-slate-950 via-blue-950 to-indigo-950 p-6 sm:p-8 text-white shadow-lg relative overflow-hidden">
                    <div className="absolute right-0 top-0 translate-x-12 -translate-y-12 w-96 h-96 bg-blue-500/10 rounded-full blur-3xl pointer-events-none" />

                    <div className="relative z-10 flex flex-col lg:flex-row lg:items-center lg:justify-between gap-6">
                        <div>
                            <div className="flex items-center gap-2 mb-2">
                                <span className="px-2.5 py-0.5 rounded-full bg-blue-500/20 text-blue-300 text-[10px] font-extrabold tracking-wider uppercase border border-blue-500/30 flex items-center gap-1.5">
                                    <span className="w-1.5 h-1.5 rounded-full bg-blue-400 animate-pulse" />
                                    Live Enterprise Command &amp; C-Suite Operations
                                </span>
                                <span className="text-xs text-slate-300 hidden sm:inline">
                                    {new Date().toLocaleDateString(undefined, { weekday: 'short', month: 'short', day: 'numeric', year: 'numeric' })}
                                </span>
                            </div>
                            <h1 className="text-2xl sm:text-3xl font-black tracking-tight text-white">
                                Executive Administration &amp; Operations
                            </h1>
                            <p className="text-xs sm:text-sm text-slate-300 mt-1 max-w-2xl">
                                Real-time portfolio capital tracking, workflow approval authorizations, plant telemetry, and workforce pacing.
                            </p>
                        </div>

                        {/* Quick Shortcuts */}
                        <div className="flex flex-wrap items-center gap-2.5 shrink-0">
                            <Link
                                href="/admin/requests/finance"
                                className="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl text-xs font-extrabold bg-amber-500 hover:bg-amber-400 text-slate-950 shadow-md shadow-amber-500/20 transition-all cursor-pointer relative"
                            >
                                <Banknote className="w-4 h-4" />
                                <span>Approvals Desk</span>
                                {pendingTotal > 0 && (
                                    <span className="px-1.5 py-0.2 rounded-full bg-slate-950 text-amber-400 text-[10px] font-black ml-0.5">
                                        {pendingTotal}
                                    </span>
                                )}
                            </Link>

                            <Link
                                href="/admin/users/create"
                                className="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl text-xs font-bold bg-white/10 hover:bg-white/20 text-white border border-white/20 backdrop-blur-sm transition-all cursor-pointer"
                            >
                                <UserPlus className="w-4 h-4 text-blue-300" />
                                <span>Add User</span>
                            </Link>

                            <Link
                                href="/projects/daily-reports"
                                className="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl text-xs font-bold bg-white/10 hover:bg-white/20 text-white border border-white/20 backdrop-blur-sm transition-all cursor-pointer"
                            >
                                <FileText className="w-4 h-4 text-emerald-300" />
                                <span>Site Reports</span>
                            </Link>

                            <Link
                                href="/inventory/equipment"
                                className="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl text-xs font-bold bg-white/10 hover:bg-white/20 text-white border border-white/20 backdrop-blur-sm transition-all cursor-pointer"
                            >
                                <Wrench className="w-4 h-4 text-orange-400" />
                                <span>Fleet &amp; Plant</span>
                            </Link>
                        </div>
                    </div>
                </div>

                {/* EXECUTIVE APPROVALS HUB (Actionable Requisitions) */}
                {pendingTotal > 0 ? (
                    <div className="grid grid-cols-1 md:grid-cols-3 gap-4">
                        {/* 1. Expense Approvals */}
                        <div className="p-4 sm:p-5 rounded-3xl bg-gradient-to-br from-amber-500/10 via-amber-500/5 to-transparent border border-amber-500/20 dark:border-amber-500/30 flex flex-col justify-between gap-3">
                            <div className="flex items-start justify-between gap-3">
                                <div>
                                    <span className="text-[11px] font-bold text-amber-700 dark:text-amber-400 uppercase tracking-wider">
                                        Finance Requisitions
                                    </span>
                                    <h3 className="text-2xl font-black text-slate-900 dark:text-white mt-0.5">
                                        {pendingExpenseCount} Pending
                                    </h3>
                                    <p className="text-xs text-slate-600 dark:text-slate-400 mt-1">
                                        {pendingExpenseAmount > 0 ? (
                                            <>Totaling <span className="font-bold text-amber-600 dark:text-amber-400">ETB {pendingExpenseAmount.toLocaleString()}</span> awaiting authorization.</>
                                        ) : (
                                            'Project disbursements requiring executive clearance.'
                                        )}
                                    </p>
                                </div>
                                <div className="w-10 h-10 rounded-2xl bg-amber-500 text-slate-950 flex items-center justify-center shrink-0 shadow-md shadow-amber-500/20">
                                    <Banknote className="w-5 h-5" />
                                </div>
                            </div>
                            <Link
                                href="/admin/requests/finance"
                                className="inline-flex items-center justify-between px-4 py-2 rounded-xl text-xs font-bold bg-amber-500 hover:bg-amber-600 text-slate-950 transition-all cursor-pointer shadow-sm shadow-amber-500/20"
                            >
                                <span>Review &amp; Approve</span>
                                <ArrowRight className="w-4 h-4" />
                            </Link>
                        </div>

                        {/* 2. Asset / Tool Loans */}
                        <div className="p-4 sm:p-5 rounded-3xl bg-gradient-to-br from-blue-500/10 via-blue-500/5 to-transparent border border-blue-500/20 dark:border-blue-500/30 flex flex-col justify-between gap-3">
                            <div className="flex items-start justify-between gap-3">
                                <div>
                                    <span className="text-[11px] font-bold text-blue-700 dark:text-blue-400 uppercase tracking-wider">
                                        Asset &amp; Material Loans
                                    </span>
                                    <h3 className="text-2xl font-black text-slate-900 dark:text-white mt-0.5">
                                        {pendingLoanCount} Pending
                                    </h3>
                                    <p className="text-xs text-slate-600 dark:text-slate-400 mt-1">
                                        Equipment, tools, and inventory requisitioned by site teams.
                                    </p>
                                </div>
                                <div className="w-10 h-10 rounded-2xl bg-blue-600 text-white flex items-center justify-center shrink-0 shadow-md shadow-blue-600/20">
                                    <Package className="w-5 h-5" />
                                </div>
                            </div>
                            <Link
                                href="/admin/requests/items"
                                className="inline-flex items-center justify-between px-4 py-2 rounded-xl text-xs font-bold bg-blue-600 hover:bg-blue-700 text-white transition-all cursor-pointer shadow-sm shadow-blue-600/20"
                            >
                                <span>Review &amp; Approve</span>
                                <ArrowRight className="w-4 h-4" />
                            </Link>
                        </div>

                        {/* 3. Leave Requests */}
                        <div className="p-4 sm:p-5 rounded-3xl bg-gradient-to-br from-purple-500/10 via-purple-500/5 to-transparent border border-purple-500/20 dark:border-purple-500/30 flex flex-col justify-between gap-3">
                            <div className="flex items-start justify-between gap-3">
                                <div>
                                    <span className="text-[11px] font-bold text-purple-700 dark:text-purple-400 uppercase tracking-wider">
                                        Staff Leaves
                                    </span>
                                    <h3 className="text-2xl font-black text-slate-900 dark:text-white mt-0.5">
                                        {pendingLeaveCount} Pending
                                    </h3>
                                    <p className="text-xs text-slate-600 dark:text-slate-400 mt-1">
                                        Workforce time-off and leave requests awaiting clearance.
                                    </p>
                                </div>
                                <div className="w-10 h-10 rounded-2xl bg-purple-600 text-white flex items-center justify-center shrink-0 shadow-md shadow-purple-600/20">
                                    <Calendar className="w-5 h-5" />
                                </div>
                            </div>
                            <Link
                                href="/admin/requests/leave-approvals"
                                className="inline-flex items-center justify-between px-4 py-2 rounded-xl text-xs font-bold bg-purple-600 hover:bg-purple-700 text-white transition-all cursor-pointer shadow-sm shadow-purple-600/20"
                            >
                                <span>Review &amp; Approve</span>
                                <ArrowRight className="w-4 h-4" />
                            </Link>
                        </div>
                    </div>
                ) : (
                    <div className="p-4 rounded-2xl bg-emerald-50/60 dark:bg-emerald-950/20 border border-emerald-200 dark:border-emerald-800/40 flex items-center justify-between gap-3">
                        <div className="flex items-center gap-3">
                            <div className="w-8 h-8 rounded-xl bg-emerald-600 text-white flex items-center justify-center shrink-0">
                                <Check className="w-4 h-4" />
                            </div>
                            <div>
                                <h4 className="text-xs sm:text-sm font-bold text-emerald-900 dark:text-emerald-200">
                                    All Workflow Queues Clear
                                </h4>
                                <p className="text-[11px] text-emerald-700 dark:text-emerald-400">
                                    No pending expense requisitions, loan requests, or leave approvals requiring executive action.
                                </p>
                            </div>
                        </div>
                        <span className="hidden sm:inline-block px-2.5 py-1 rounded-full bg-emerald-100 dark:bg-emerald-900/60 text-emerald-800 dark:text-emerald-200 text-[10px] font-bold">
                            Operational 100%
                        </span>
                    </div>
                )}

                {/* ROW 1: 4 TOP KPI METRIC CARDS */}
                <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-6 items-stretch">
                    {/* 1. Budget */}
                    <MetricCard
                        title="Budget"
                        value={formatCurrencyDisplay(totalBudget)}
                        trend={`${financialStats.usage_pct || 0}% used`}
                        trendPositive={Number(financialStats.usage_pct || 0) < 85}
                        comparisonText="allocated across sites"
                        type="bars"
                        href="/finance/projects"
                    />

                    {/* 2. Cost Variance / Total Spent */}
                    <MetricCard
                        title="Total Expended"
                        value={formatCurrencyDisplay(totalSpent)}
                        trend={totalBudget > 0 ? `${Math.round((totalSpent / totalBudget) * 100)}%` : '0%'}
                        trendPositive={totalSpent <= totalBudget}
                        comparisonText="against project budgets"
                        type="stepped"
                        href="/finance/expenses"
                    />

                    {/* 3. Active Workforce Attendance */}
                    <MetricCard
                        title="Workforce on Site"
                        value={presentToday > 0 ? `${presentToday} Present` : (totalEmployees > 0 ? `${totalEmployees} Staff` : '34 Staff')}
                        trend={totalEmployees > 0 ? `${Math.round((presentToday / totalEmployees) * 100)}%` : '100%'}
                        trendPositive={true}
                        comparisonText="attendance today"
                        type="progress"
                        progressPercent={totalEmployees > 0 ? Math.round((presentToday / totalEmployees) * 100) : 80}
                        href="/hr/attendance"
                    />

                    {/* 4. Dynamic Risk Score */}
                    <MetricCard
                        title="Risk Index"
                        value={`${riskScore}/10`}
                        trend={`${riskLevel} Risk`}
                        trendPositive={riskScore < 5.0}
                        comparisonText="site budget &amp; telemetry"
                        type="arc"
                        href="/projects/daily-reports"
                    />
                </div>

                {/* ROW 2: MIDDLE SECTION (Cost Trend Analysis 65% + Site Velocity 35%) */}
                <div className="grid grid-cols-1 lg:grid-cols-12 gap-4 sm:gap-6 items-stretch">
                    {/* Cost Trend Analysis with live monthly cash flow */}
                    <div className="lg:col-span-8 flex flex-col">
                        <CostTrendChart
                            data={monthlyCashFlow}
                            title="Cost Trend Analysis"
                            currency="ETB"
                        />
                    </div>

                    {/* Active Construction Sites & Fleet Velocity */}
                    <div className="lg:col-span-4 flex flex-col">
                        <ProjectVelocityCard
                            projects={projectBreakdown}
                            fleetStats={fleetStats}
                            title="Site Execution &amp; Health"
                            href="/finance/projects"
                        />
                    </div>
                </div>

                {/* ROW 3: BOTTOM SECTION (Workforce Allocation 50% + Schedule Performance 50%) */}
                <div className="grid grid-cols-1 lg:grid-cols-2 gap-4 sm:gap-6 items-stretch">
                    {/* Workforce Allocation */}
                    <div className="flex flex-col">
                        <WorkforceAllocationChart
                            title="Workforce Allocation"
                            data={workforceData}
                        />
                    </div>

                    {/* Schedule Performance */}
                    <div className="flex flex-col">
                        <SchedulePerformanceChart
                            title="Schedule Performance"
                        />
                    </div>
                </div>

                {/* INTERACTIVE MULTI-MODULE OPERATIONAL FEED (Tabbed Card) */}
                <div className="rounded-2xl sm:rounded-3xl bg-white dark:bg-slate-900 border border-slate-100 dark:border-slate-800/80 p-5 sm:p-7 shadow-sm">
                    {/* Header with Navigation Tabs */}
                    <div className="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-5 pb-4 border-b border-slate-100 dark:border-slate-800/80">
                        <div>
                            <h3 className="text-base sm:text-lg font-bold text-slate-900 dark:text-white flex items-center gap-2">
                                <Activity className="w-5 h-5 text-blue-600" />
                                <span>Recent Enterprise Operations</span>
                            </h3>
                            <p className="text-xs text-slate-500 dark:text-slate-400 mt-0.5">
                                Cross-module disbursements, tool loans, leave filings, and audit trails.
                            </p>
                        </div>

                        {/* Tabs Switcher */}
                        <div className="flex flex-wrap items-center gap-1.5 p-1 rounded-2xl bg-slate-100 dark:bg-slate-800/60 text-xs font-semibold">
                            <button
                                type="button"
                                onClick={() => setActiveTab('finance')}
                                className={`px-3 py-1.5 rounded-xl transition-all cursor-pointer ${
                                    activeTab === 'finance'
                                        ? 'bg-white dark:bg-slate-900 text-slate-900 dark:text-white shadow-sm font-bold'
                                        : 'text-slate-500 hover:text-slate-900 dark:hover:text-slate-200'
                                }`}
                            >
                                💳 Finance ({recentExpenses.length})
                            </button>
                            <button
                                type="button"
                                onClick={() => setActiveTab('inventory')}
                                className={`px-3 py-1.5 rounded-xl transition-all cursor-pointer ${
                                    activeTab === 'inventory'
                                        ? 'bg-white dark:bg-slate-900 text-slate-900 dark:text-white shadow-sm font-bold'
                                        : 'text-slate-500 hover:text-slate-900 dark:hover:text-slate-200'
                                }`}
                            >
                                📦 Loans ({recentLoans.length})
                            </button>
                            <button
                                type="button"
                                onClick={() => setActiveTab('hr')}
                                className={`px-3 py-1.5 rounded-xl transition-all cursor-pointer ${
                                    activeTab === 'hr'
                                        ? 'bg-white dark:bg-slate-900 text-slate-900 dark:text-white shadow-sm font-bold'
                                        : 'text-slate-500 hover:text-slate-900 dark:hover:text-slate-200'
                                }`}
                            >
                                📅 Leaves ({recentLeaves.length})
                            </button>
                            <button
                                type="button"
                                onClick={() => setActiveTab('audit')}
                                className={`px-3 py-1.5 rounded-xl transition-all cursor-pointer ${
                                    activeTab === 'audit'
                                        ? 'bg-white dark:bg-slate-900 text-slate-900 dark:text-white shadow-sm font-bold'
                                        : 'text-slate-500 hover:text-slate-900 dark:hover:text-slate-200'
                                }`}
                            >
                                🛡️ Audit ({activities.length})
                            </button>
                        </div>
                    </div>

                    {/* TAB 1: FINANCE DISBURSEMENTS */}
                    {activeTab === 'finance' && (
                        <div className="overflow-x-auto">
                            <table className="w-full text-left text-xs">
                                <thead className="bg-slate-50 dark:bg-slate-800/60 text-slate-500 font-bold uppercase tracking-wider">
                                    <tr>
                                        <th className="py-2.5 px-4 rounded-l-xl">Reference / Title</th>
                                        <th className="py-2.5 px-4">Project</th>
                                        <th className="py-2.5 px-4">Amount</th>
                                        <th className="py-2.5 px-4">Status</th>
                                        <th className="py-2.5 px-4 rounded-r-xl text-right">Action</th>
                                    </tr>
                                </thead>
                                <tbody className="divide-y divide-slate-100 dark:divide-slate-800/60">
                                    {recentExpenses && recentExpenses.length > 0 ? (
                                        recentExpenses.map((exp) => (
                                            <tr key={exp.id} className="hover:bg-slate-50/60 dark:hover:bg-slate-800/40 transition-colors">
                                                <td className="py-3 px-4 font-semibold text-slate-800 dark:text-slate-200">
                                                    {exp.title || `Expense #${exp.id}`}
                                                </td>
                                                <td className="py-3 px-4 text-slate-500">
                                                    {exp.project?.name || 'General Operations'}
                                                </td>
                                                <td className="py-3 px-4 font-bold text-slate-900 dark:text-white">
                                                    ETB {Number(exp.amount || 0).toLocaleString()}
                                                </td>
                                                <td className="py-3 px-4">
                                                    <span className={`px-2.5 py-0.5 rounded-full text-[10px] font-bold ${
                                                        exp.status === 'approved'
                                                            ? 'bg-emerald-50 text-emerald-700 dark:bg-emerald-950/50 dark:text-emerald-300'
                                                            : 'bg-amber-50 text-amber-700 dark:bg-amber-950/50 dark:text-amber-300'
                                                    }`}>
                                                        {exp.status || 'Pending'}
                                                    </span>
                                                </td>
                                                <td className="py-3 px-4 text-right">
                                                    <Link
                                                        href={`/finance/expenses/${exp.id}`}
                                                        className="text-blue-600 hover:text-blue-800 font-semibold"
                                                    >
                                                        View Voucher →
                                                    </Link>
                                                </td>
                                            </tr>
                                        ))
                                    ) : (
                                        <tr>
                                            <td colSpan="5" className="py-6 text-center text-slate-400">
                                                No recent project expense records.
                                            </td>
                                        </tr>
                                    )}
                                </tbody>
                            </table>
                        </div>
                    )}

                    {/* TAB 2: INVENTORY & TOOL LOANS */}
                    {activeTab === 'inventory' && (
                        <div className="overflow-x-auto">
                            <table className="w-full text-left text-xs">
                                <thead className="bg-slate-50 dark:bg-slate-800/60 text-slate-500 font-bold uppercase tracking-wider">
                                    <tr>
                                        <th className="py-2.5 px-4 rounded-l-xl">Item / Machine</th>
                                        <th className="py-2.5 px-4">Borrower</th>
                                        <th className="py-2.5 px-4">Requested At</th>
                                        <th className="py-2.5 px-4">Status</th>
                                        <th className="py-2.5 px-4 rounded-r-xl text-right">Action</th>
                                    </tr>
                                </thead>
                                <tbody className="divide-y divide-slate-100 dark:divide-slate-800/60">
                                    {recentLoans && recentLoans.length > 0 ? (
                                        recentLoans.map((loan) => (
                                            <tr key={loan.id} className="hover:bg-slate-50/60 dark:hover:bg-slate-800/40 transition-colors">
                                                <td className="py-3 px-4 font-semibold text-slate-800 dark:text-slate-200">
                                                    {loan.item?.name || `Asset Loan #${loan.id}`}
                                                </td>
                                                <td className="py-3 px-4 text-slate-500">
                                                    {loan.employee?.user?.name || loan.employee?.name || 'Staff Member'}
                                                </td>
                                                <td className="py-3 px-4 text-slate-400">
                                                    {loan.created_at ? new Date(loan.created_at).toLocaleDateString() : 'Recent'}
                                                </td>
                                                <td className="py-3 px-4">
                                                    <span className={`px-2.5 py-0.5 rounded-full text-[10px] font-bold ${
                                                        loan.status === 'approved'
                                                            ? 'bg-emerald-50 text-emerald-700 dark:bg-emerald-950/50 dark:text-emerald-300'
                                                            : 'bg-blue-50 text-blue-700 dark:bg-blue-950/50 dark:text-blue-300'
                                                    }`}>
                                                        {loan.status || 'Pending'}
                                                    </span>
                                                </td>
                                                <td className="py-3 px-4 text-right">
                                                    <Link
                                                        href="/inventory/loans"
                                                        className="text-blue-600 hover:text-blue-800 font-semibold"
                                                    >
                                                        View Loans →
                                                    </Link>
                                                </td>
                                            </tr>
                                        ))
                                    ) : (
                                        <tr>
                                            <td colSpan="5" className="py-6 text-center text-slate-400">
                                                No active asset loan requests recorded.
                                            </td>
                                        </tr>
                                    )}
                                </tbody>
                            </table>
                        </div>
                    )}

                    {/* TAB 3: HR LEAVE APPLICATIONS */}
                    {activeTab === 'hr' && (
                        <div className="overflow-x-auto">
                            <table className="w-full text-left text-xs">
                                <thead className="bg-slate-50 dark:bg-slate-800/60 text-slate-500 font-bold uppercase tracking-wider">
                                    <tr>
                                        <th className="py-2.5 px-4 rounded-l-xl">Employee</th>
                                        <th className="py-2.5 px-4">Period</th>
                                        <th className="py-2.5 px-4">Type</th>
                                        <th className="py-2.5 px-4">Status</th>
                                        <th className="py-2.5 px-4 rounded-r-xl text-right">Action</th>
                                    </tr>
                                </thead>
                                <tbody className="divide-y divide-slate-100 dark:divide-slate-800/60">
                                    {recentLeaves && recentLeaves.length > 0 ? (
                                        recentLeaves.map((leave) => (
                                            <tr key={leave.id} className="hover:bg-slate-50/60 dark:hover:bg-slate-800/40 transition-colors">
                                                <td className="py-3 px-4 font-semibold text-slate-800 dark:text-slate-200">
                                                    {leave.employee?.user?.name || leave.employee?.first_name || `Leave #${leave.id}`}
                                                </td>
                                                <td className="py-3 px-4 text-slate-500">
                                                    {leave.start_date ? new Date(leave.start_date).toLocaleDateString() : 'N/A'} - {leave.end_date ? new Date(leave.end_date).toLocaleDateString() : 'N/A'}
                                                </td>
                                                <td className="py-3 px-4 text-slate-500">
                                                    {leave.leave_type || 'Annual Leave'}
                                                </td>
                                                <td className="py-3 px-4">
                                                    <span className={`px-2.5 py-0.5 rounded-full text-[10px] font-bold ${
                                                        leave.status === 'Approved' || leave.status === 'approved'
                                                            ? 'bg-emerald-50 text-emerald-700 dark:bg-emerald-950/50 dark:text-emerald-300'
                                                            : 'bg-purple-50 text-purple-700 dark:bg-purple-950/50 dark:text-purple-300'
                                                    }`}>
                                                        {leave.status || 'Pending'}
                                                    </span>
                                                </td>
                                                <td className="py-3 px-4 text-right">
                                                    <Link
                                                        href="/hr/leaves"
                                                        className="text-blue-600 hover:text-blue-800 font-semibold"
                                                    >
                                                        View Leaves →
                                                    </Link>
                                                </td>
                                            </tr>
                                        ))
                                    ) : (
                                        <tr>
                                            <td colSpan="5" className="py-6 text-center text-slate-400">
                                                No employee leave filings found.
                                            </td>
                                        </tr>
                                    )}
                                </tbody>
                            </table>
                        </div>
                    )}

                    {/* TAB 4: AUDIT & ACTIVITY STREAM */}
                    {activeTab === 'audit' && (
                        <div className="overflow-x-auto">
                            <table className="w-full text-left text-xs">
                                <thead className="bg-slate-50 dark:bg-slate-800/60 text-slate-500 font-bold uppercase tracking-wider">
                                    <tr>
                                        <th className="py-2.5 px-4 rounded-l-xl">User / Operator</th>
                                        <th className="py-2.5 px-4">Event Description</th>
                                        <th className="py-2.5 px-4">Logged At</th>
                                        <th className="py-2.5 px-4 rounded-r-xl text-right">Action</th>
                                    </tr>
                                </thead>
                                <tbody className="divide-y divide-slate-100 dark:divide-slate-800/60">
                                    {activities && activities.length > 0 ? (
                                        activities.map((act) => (
                                            <tr key={act.id} className="hover:bg-slate-50/60 dark:hover:bg-slate-800/40 transition-colors">
                                                <td className="py-3 px-4 font-semibold text-slate-800 dark:text-slate-200">
                                                    {act.user?.name || act.user?.email || 'System'}
                                                </td>
                                                <td className="py-3 px-4 text-slate-600 dark:text-slate-300">
                                                    {act.description || act.action || 'System action logged'}
                                                </td>
                                                <td className="py-3 px-4 text-slate-400">
                                                    {act.created_at ? new Date(act.created_at).toLocaleString() : 'Just now'}
                                                </td>
                                                <td className="py-3 px-4 text-right">
                                                    <Link
                                                        href="/admin/activity-logs"
                                                        className="text-blue-600 hover:text-blue-800 font-semibold"
                                                    >
                                                        Full Log →
                                                    </Link>
                                                </td>
                                            </tr>
                                        ))
                                    ) : (
                                        <tr>
                                            <td colSpan="4" className="py-6 text-center text-slate-400">
                                                No recent audit log entries recorded.
                                            </td>
                                        </tr>
                                    )}
                                </tbody>
                            </table>
                        </div>
                    )}
                </div>
            </div>
        </AuthenticatedLayout>
    );
}
