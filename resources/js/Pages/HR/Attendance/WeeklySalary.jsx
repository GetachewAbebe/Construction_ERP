import { useState } from 'react';
import { Head, Link, router } from '@inertiajs/react';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import {
    Calendar,
    FileSpreadsheet,
    DollarSign,
    BarChart3,
    ArrowLeft,
    Search,
    ChevronLeft,
    ChevronRight,
    Info,
    CheckCircle2,
    Clock,
    AlertCircle,
} from 'lucide-react';

export default function WeeklySalary({
    weekStart,
    weekEnd,
    analysis = [],
}) {
    const [selectedDate, setSelectedDate] = useState(weekStart);
    const [search, setSearch] = useState('');

    const handleDateChange = (newDate) => {
        setSelectedDate(newDate);
        router.get('/hr/attendance/weekly-salary', { week_start: newDate }, {
            preserveState: true,
            preserveScroll: true,
        });
    };

    const navigateWeek = (offsetDays) => {
        const d = new Date(selectedDate);
        d.setDate(d.getDate() + offsetDays);
        handleDateChange(d.toISOString().split('T')[0]);
    };

    const filteredAnalysis = analysis.filter(row => {
        const emp = row.employee || {};
        const fullName = `${emp.first_name || ''} ${emp.last_name || ''}`.toLowerCase();
        const dept = (emp.department || '').toLowerCase();
        const pos = (emp.position || '').toLowerCase();
        const q = search.toLowerCase();
        return fullName.includes(q) || dept.includes(q) || pos.includes(q);
    });

    const totalProjectedPayout = analysis.reduce((acc, curr) => acc + (Number(curr.payable_amount) || 0), 0);
    const totalCredits = analysis.reduce((acc, curr) => acc + (Number(curr.credits) || 0), 0);

    const formatETB = (val) => {
        return Number(val || 0).toLocaleString('en-US', {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2,
        });
    };

    return (
        <AuthenticatedLayout title="Weekly Salary Credits" header="Human Resources">
            <Head title={`Weekly Salary Analysis (${weekStart} to ${weekEnd})`} />

            <div className="space-y-6">
                {/* Header */}
                <div className="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-2 border-b border-slate-200 dark:border-slate-800">
                    <div>
                        <div className="flex items-center gap-2">
                            <Link
                                href="/hr/attendance"
                                className="p-1.5 rounded-lg text-slate-400 hover:text-slate-700 dark:hover:text-slate-200 hover:bg-slate-100 dark:hover:bg-slate-800 transition"
                            >
                                <ArrowLeft className="w-4 h-4" />
                            </Link>
                            <h1 className="text-xl sm:text-2xl font-extrabold tracking-tight text-slate-900 dark:text-white">
                                Weekly Salary & Credit Projections
                            </h1>
                        </div>
                        <p className="text-xs sm:text-sm text-slate-500 dark:text-slate-400 mt-0.5 ml-7">
                            Attendance credits, standard daily yields, and gross wage forecasts.
                        </p>
                    </div>

                    {/* Quick Access Nav Tabs */}
                    <div className="flex flex-wrap items-center gap-2">
                        <Link
                            href="/hr/attendance/daily-sheet"
                            className="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs font-semibold text-slate-700 dark:text-slate-200 hover:bg-slate-50 dark:hover:bg-slate-700 transition-colors"
                        >
                            <Calendar className="w-3.5 h-3.5" />
                            <span>Daily Sheet</span>
                        </Link>
                        <Link
                            href="/hr/attendance/weekly-sheet"
                            className="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs font-semibold text-slate-700 dark:text-slate-200 hover:bg-slate-50 dark:hover:bg-slate-700 transition-colors"
                        >
                            <FileSpreadsheet className="w-3.5 h-3.5" />
                            <span>Weekly Batch</span>
                        </Link>
                        <Link
                            href="/hr/attendance/monthly-summary"
                            className="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs font-semibold text-slate-700 dark:text-slate-200 hover:bg-slate-50 dark:hover:bg-slate-700 transition-colors"
                        >
                            <BarChart3 className="w-3.5 h-3.5" />
                            <span>Monthly Audit</span>
                        </Link>
                        <Link
                            href="/hr/attendance/weekly-salary"
                            className="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl border border-blue-600 bg-blue-50 dark:bg-blue-900/30 text-xs font-semibold text-blue-700 dark:text-blue-300"
                        >
                            <DollarSign className="w-3.5 h-3.5" />
                            <span>Salary Credits</span>
                        </Link>
                    </div>
                </div>

                {/* Information Banner */}
                <div className="flex items-center gap-3 p-4 rounded-2xl border border-blue-200 dark:border-blue-900/40 bg-blue-50/50 dark:bg-blue-950/20 text-xs sm:text-sm text-blue-900 dark:text-blue-300">
                    <Info className="w-5 h-5 shrink-0 text-blue-600 dark:text-blue-400" />
                    <span>
                        <strong>Weekly Pay Formula:</strong> Cycle active from <strong>{weekStart}</strong> to <strong>{weekEnd}</strong>. Daily yield derived from monthly base ÷ 22 business days. Projected wages are synchronized with verified session attendance credits.
                    </span>
                </div>

                {/* Filters & Navigation Controls */}
                <div className="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 p-4 shadow-sm flex flex-col md:flex-row items-center justify-between gap-4">
                    <div className="flex items-center gap-2 w-full md:w-auto">
                        <button
                            type="button"
                            onClick={() => navigateWeek(-7)}
                            className="p-2 rounded-xl border border-slate-200 dark:border-slate-700 hover:bg-slate-100 dark:hover:bg-slate-800 text-slate-600 dark:text-slate-300 transition"
                            title="Previous week"
                        >
                            <ChevronLeft className="w-4 h-4" />
                        </button>

                        <div className="flex items-center gap-2">
                            <span className="text-xs font-bold text-slate-700 dark:text-slate-300">
                                Week Starting:
                            </span>
                            <input
                                type="date"
                                value={selectedDate}
                                onChange={(e) => handleDateChange(e.target.value)}
                                className="px-2.5 py-1 text-xs rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-white font-medium focus:ring-2 focus:ring-blue-500"
                            />
                        </div>

                        <button
                            type="button"
                            onClick={() => navigateWeek(7)}
                            className="p-2 rounded-xl border border-slate-200 dark:border-slate-700 hover:bg-slate-100 dark:hover:bg-slate-800 text-slate-600 dark:text-slate-300 transition"
                            title="Next week"
                        >
                            <ChevronRight className="w-4 h-4" />
                        </button>
                    </div>

                    <div className="relative w-full md:w-72">
                        <Search className="w-4 h-4 absolute left-3 top-1/2 -translate-y-1/2 text-slate-400" />
                        <input
                            type="text"
                            placeholder="Filter employee or position..."
                            value={search}
                            onChange={(e) => setSearch(e.target.value)}
                            className="w-full pl-9 pr-3 py-2 text-xs rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-slate-900 dark:text-white placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-blue-500"
                        />
                    </div>
                </div>

                {/* KPI Summary Cards */}
                <div className="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <div className="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 p-5 shadow-sm">
                        <span className="text-xs font-bold uppercase tracking-wider text-slate-400">Total Associates</span>
                        <div className="mt-2 text-2xl font-black text-slate-900 dark:text-white">
                            {analysis.length}
                        </div>
                        <div className="mt-1 text-[11px] text-slate-500">Active roster candidates</div>
                    </div>

                    <div className="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 p-5 shadow-sm">
                        <span className="text-xs font-bold uppercase tracking-wider text-slate-400">Total Weekly Credits</span>
                        <div className="mt-2 text-2xl font-black text-blue-600 dark:text-blue-400">
                            {totalCredits.toFixed(1)}
                        </div>
                        <div className="mt-1 text-[11px] text-slate-500">Work sessions recorded</div>
                    </div>

                    <div className="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 p-5 shadow-sm">
                        <span className="text-xs font-bold uppercase tracking-wider text-slate-400">Projected Payout</span>
                        <div className="mt-2 text-2xl font-black text-emerald-600 dark:text-emerald-400">
                            {formatETB(totalProjectedPayout)} ETB
                        </div>
                        <div className="mt-1 text-[11px] text-slate-500">Gross week compensation</div>
                    </div>
                </div>

                {/* Salary Projections Table */}
                <div className="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-sm overflow-hidden">
                    <div className="overflow-x-auto">
                        <table className="w-full text-left border-collapse text-sm">
                            <thead>
                                <tr className="border-b border-slate-200 dark:border-slate-800 bg-slate-50 dark:bg-slate-800/60 text-[11px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">
                                    <th className="py-3 px-4">Associate</th>
                                    <th className="py-3 px-4 text-center">Sessions / Credits</th>
                                    <th className="py-3 px-4 text-center">Daily Yield</th>
                                    <th className="py-3 px-4 text-center">Projected Pay</th>
                                    <th className="py-3 px-4 text-right">Fulfillment</th>
                                </tr>
                            </thead>
                            <tbody className="divide-y divide-slate-100 dark:divide-slate-800/60">
                                {filteredAnalysis.length === 0 ? (
                                    <tr>
                                        <td colSpan={5} className="py-12 text-center text-slate-400 text-sm">
                                            No employee pay records match your query.
                                        </td>
                                    </tr>
                                ) : (
                                    filteredAnalysis.map((row, idx) => {
                                        const emp = row.employee || {};
                                        const credits = Number(row.credits || 0);
                                        const dailyRate = Number(row.daily_rate || 0);
                                        const payable = Number(row.payable_amount || 0);

                                        return (
                                            <tr key={emp.id || idx} className="hover:bg-slate-50/70 dark:hover:bg-slate-800/40 transition-colors">
                                                {/* Associate info */}
                                                <td className="py-3 px-4">
                                                    <div className="flex items-center gap-3">
                                                        <div className="h-9 w-9 rounded-xl bg-blue-100 dark:bg-blue-900/40 text-blue-700 dark:text-blue-300 font-bold flex items-center justify-center text-xs shrink-0">
                                                            {emp.first_name ? emp.first_name.charAt(0).toUpperCase() : 'E'}
                                                        </div>
                                                        <div>
                                                            <div className="font-semibold text-slate-900 dark:text-white">
                                                                {emp.first_name} {emp.last_name}
                                                            </div>
                                                            <div className="text-xs text-slate-400">
                                                                {emp.position || 'Staff'} • {emp.department || 'General'}
                                                            </div>
                                                        </div>
                                                    </div>
                                                </td>

                                                {/* Credits */}
                                                <td className="py-3 px-4 text-center">
                                                    <div className="text-base font-black text-blue-600 dark:text-blue-400">
                                                        {credits.toFixed(1)}
                                                    </div>
                                                    <div className="text-[10px] uppercase font-bold text-slate-400">sessions</div>
                                                </td>

                                                {/* Daily Yield */}
                                                <td className="py-3 px-4 text-center font-mono text-xs font-semibold text-slate-600 dark:text-slate-300">
                                                    {formatETB(dailyRate)} ETB
                                                </td>

                                                {/* Projected Pay */}
                                                <td className="py-3 px-4 text-center">
                                                    <span className="inline-flex items-center px-3 py-1 rounded-xl text-xs font-bold font-mono bg-slate-100 dark:bg-slate-800 text-slate-900 dark:text-white">
                                                        {formatETB(payable)} ETB
                                                    </span>
                                                </td>

                                                {/* Fulfillment */}
                                                <td className="py-3 px-4 text-right">
                                                    {credits >= 5 ? (
                                                        <span className="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-emerald-100 text-emerald-800 dark:bg-emerald-950/40 dark:text-emerald-300">
                                                            Full Week
                                                        </span>
                                                    ) : credits > 0 ? (
                                                        <span className="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-amber-100 text-amber-800 dark:bg-amber-950/40 dark:text-amber-300">
                                                            Partial ({credits.toFixed(1)})
                                                        </span>
                                                    ) : (
                                                        <span className="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-rose-100 text-rose-800 dark:bg-rose-950/40 dark:text-rose-300">
                                                            Inactive
                                                        </span>
                                                    )}
                                                </td>
                                            </tr>
                                        );
                                    })
                                )}
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </AuthenticatedLayout>
    );
}
