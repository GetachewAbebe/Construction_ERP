import { Head, Link } from '@inertiajs/react';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import MetricCard from '@/Components/Dashboard/MetricCard';
import CostTrendChart from '@/Components/Dashboard/CostTrendChart';
import RiskGauge from '@/Components/Dashboard/RiskGauge';
import WorkforceAllocationChart from '@/Components/Dashboard/WorkforceAllocationChart';
import SchedulePerformanceChart from '@/Components/Dashboard/SchedulePerformanceChart';
import {
    Users,
    Banknote,
    Package,
    ArrowUpRight,
    Clock,
    CheckCircle2,
    Calendar,
    Briefcase,
    ShieldCheck,
    FileText,
    TrendingUp,
} from 'lucide-react';

export default function AdminDashboard({
    pendingLoanCount = 0,
    pendingLeaveCount = 0,
    pendingExpenseCount = 0,
    totalUsers = 0,
    totalProjects = 0,
    totalEmployees = 0,
    totalItems = 0,
    activeLoans = 0,
    presentToday = 0,
    financialStats = {},
    projectBreakdown = [],
    expenseCategories = [],
    monthlyCashFlow = {},
    recentExpenses = [],
    recentLoans = [],
    recentEmployees = [],
    recentLeaves = [],
    departmentStats = [],
    systemHealth = 98,
}) {
    const pendingTotal = (pendingLoanCount || 0) + (pendingExpenseCount || 0) + (pendingLeaveCount || 0);

    const totalBudget = Number(financialStats.total_budget || 90000000);
    const totalSpent = Number(financialStats.total_spent || 1100000);

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
            available: Math.max(d.total + 4, Math.round(d.total * 1.4)),
        }))
        : undefined;

    return (
        <AuthenticatedLayout title="Executive Overview">
            <div className="space-y-6">
                {/* ROW 1: 4 TOP KPI METRIC CARDS (Matching Reference Layout) */}
                <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-6 items-stretch">
                    {/* 1. Budget */}
                    <MetricCard
                        title="Budget"
                        value={formatCurrencyDisplay(totalBudget)}
                        trend="2.3%"
                        trendPositive={true}
                        comparisonText="vs last month"
                        type="bars"
                        href="/finance/projects"
                    />

                    {/* 2. Cost Variance */}
                    <MetricCard
                        title="Cost Variance"
                        value={formatCurrencyDisplay(totalSpent)}
                        trend="0.5%"
                        trendPositive={false}
                        comparisonText="vs last month"
                        type="stepped"
                        href="/finance/expenses"
                    />

                    {/* 3. Active Employee */}
                    <MetricCard
                        title="Active Employee"
                        value={presentToday > 0 ? `${presentToday} Active` : (totalEmployees > 0 ? `${totalEmployees} Staff` : '34 Staff')}
                        trend="2.3%"
                        trendPositive={true}
                        comparisonText="vs last month"
                        type="progress"
                        progressPercent={totalEmployees > 0 ? Math.round((presentToday / totalEmployees) * 100) : 74}
                        href="/hr/employees"
                    />

                    {/* 4. Risk Score */}
                    <MetricCard
                        title="Risk Score"
                        value="6.2/10"
                        trend="2.3%"
                        trendPositive={true}
                        comparisonText="vs last month"
                        type="arc"
                        href="/admin/system-settings"
                    />
                </div>

                {/* ROW 2: MIDDLE SECTION (Cost Trend Analysis 65% + Risk Distribution 35%) */}
                <div className="grid grid-cols-1 lg:grid-cols-12 gap-4 sm:gap-6 items-stretch">
                    {/* Cost Trend Analysis */}
                    <div className="lg:col-span-8 flex flex-col">
                        <CostTrendChart
                            title="Cost Trend Analysis"
                            currency="ETB"
                        />
                    </div>

                    {/* Risk Distribution Radial Gauge */}
                    <div className="lg:col-span-4 flex flex-col">
                        <RiskGauge
                            score={80}
                            status="Medium"
                            title="Risk Distribution"
                            healthPercent={systemHealth}
                            href="/projects/daily-reports"
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

                {/* RECENT OPERATIONAL ACTIVITY (Integrated Clean Table) */}
                <div className="rounded-2xl sm:rounded-3xl bg-white dark:bg-slate-900 border border-slate-100 dark:border-slate-800/80 p-5 sm:p-7 shadow-sm">
                    <div className="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-4 pb-3 border-b border-slate-100 dark:border-slate-800/80">
                        <div>
                            <h3 className="text-base sm:text-lg font-bold text-slate-900 dark:text-white">
                                Recent Operational Vouchers &amp; Logs
                            </h3>
                            <p className="text-xs text-slate-500 dark:text-slate-400 mt-0.5">
                                Real-time disbursements, loan dispatches, and pending approvals.
                            </p>
                        </div>
                        <div className="flex items-center gap-2">
                            {pendingTotal > 0 && (
                                <span className="px-3 py-1 rounded-full bg-amber-50 dark:bg-amber-950/50 text-amber-700 dark:text-amber-300 font-bold text-xs border border-amber-200 dark:border-amber-800/50">
                                    {pendingTotal} Pending Actions
                                </span>
                            )}
                        </div>
                    </div>

                    <div className="overflow-x-auto">
                        <table className="w-full text-left text-xs">
                            <thead className="bg-slate-50 dark:bg-slate-800/60 text-slate-500 font-bold uppercase tracking-wider">
                                <tr>
                                    <th className="py-2.5 px-4 rounded-l-xl">Reference / Title</th>
                                    <th className="py-2.5 px-4">Category</th>
                                    <th className="py-2.5 px-4">Amount / Items</th>
                                    <th className="py-2.5 px-4">Status</th>
                                    <th className="py-2.5 px-4 rounded-r-xl text-right">Action</th>
                                </tr>
                            </thead>
                            <tbody className="divide-y divide-slate-100 dark:divide-slate-800/60">
                                {recentExpenses && recentExpenses.length > 0 ? (
                                    recentExpenses.slice(0, 4).map((exp) => (
                                        <tr key={exp.id} className="hover:bg-slate-50/60 dark:hover:bg-slate-800/40 transition-colors">
                                            <td className="py-3 px-4 font-semibold text-slate-800 dark:text-slate-200">
                                                {exp.title || `Expense #${exp.id}`}
                                            </td>
                                            <td className="py-3 px-4 text-slate-500">
                                                {typeof exp.category === 'string' ? exp.category : (exp.category?.name || 'Project Expense')}
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
                                                    View →
                                                </Link>
                                            </td>
                                        </tr>
                                    ))
                                ) : (
                                    <tr>
                                        <td colSpan="5" className="py-6 text-center text-slate-400">
                                            All project expense registers are up to date.
                                        </td>
                                    </tr>
                                )}
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </AuthenticatedLayout>
    );
}
