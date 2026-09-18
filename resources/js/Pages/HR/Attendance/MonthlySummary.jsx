import { useState } from 'react';
import { Head, Link, router } from '@inertiajs/react';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import {
    Calendar,
    FileSpreadsheet,
    DollarSign,
    BarChart3,
    ArrowLeft,
    Download,
    Filter,
    Users,
    TrendingUp,
    Search,
    RefreshCw,
} from 'lucide-react';

const MONTH_NAMES = [
    'January', 'February', 'March', 'April', 'May', 'June',
    'July', 'August', 'September', 'October', 'November', 'December'
];

export default function MonthlySummary({
    perEmployee = [],
    totalEmployeesInScope = 0,
    totalCreditsInScope = 0,
    year,
    month,
    departmentFilter = '',
    departments = [],
    startOfMonth,
    endOfMonth,
}) {
    const [selectedYear, setSelectedYear] = useState(year || new Date().getFullYear());
    const [selectedMonth, setSelectedMonth] = useState(month || new Date().getMonth() + 1);
    const [selectedDept, setSelectedDept] = useState(departmentFilter || '');
    const [search, setSearch] = useState('');

    const totalPayable = perEmployee.reduce((acc, curr) => acc + (Number(curr.payable_amount) || 0), 0);

    const handleFilter = (e) => {
        if (e) e.preventDefault();
        router.get('/hr/attendance/monthly-summary', {
            year: selectedYear,
            month: selectedMonth,
            department: selectedDept || undefined,
        }, {
            preserveState: true,
            preserveScroll: true,
        });
    };

    const handleReset = () => {
        const now = new Date();
        setSelectedYear(now.getFullYear());
        setSelectedMonth(now.getMonth() + 1);
        setSelectedDept('');
        router.get('/hr/attendance/monthly-summary', {}, { preserveState: false });
    };

    const exportCsvUrl = `/hr/attendance/monthly-summary/export?year=${selectedYear}&month=${selectedMonth}${
        selectedDept ? `&department=${encodeURIComponent(selectedDept)}` : ''
    }`;

    const filteredList = perEmployee.filter(row => {
        const emp = row.employee || {};
        const fullName = `${emp.first_name || ''} ${emp.last_name || ''}`.toLowerCase();
        const dept = (emp.department || '').toLowerCase();
        const q = search.toLowerCase();
        return fullName.includes(q) || dept.includes(q);
    });

    const monthLabel = MONTH_NAMES[selectedMonth - 1] || 'Month';

    return (
        <AuthenticatedLayout title={`Monthly Summary • ${monthLabel} ${selectedYear}`} header="Human Resources">
            <Head title={`Monthly Attendance Summary (${monthLabel} ${selectedYear})`} />

            <div className="space-y-6">
                {/* Navigation & Header */}
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
                                Monthly Attendance Summary
                            </h1>
                        </div>
                        <p className="text-xs sm:text-sm text-slate-500 dark:text-slate-400 mt-0.5 ml-7">
                            Audit period: {monthLabel} {selectedYear} {selectedDept ? `• ${selectedDept}` : '• All Divisions'}
                        </p>
                    </div>

                    {/* Quick Access Nav Tabs & Actions */}
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
                            className="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl border border-blue-600 bg-blue-50 dark:bg-blue-900/30 text-xs font-semibold text-blue-700 dark:text-blue-300"
                        >
                            <BarChart3 className="w-3.5 h-3.5" />
                            <span>Monthly Audit</span>
                        </Link>
                        <Link
                            href="/hr/attendance/weekly-salary"
                            className="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs font-semibold text-slate-700 dark:text-slate-200 hover:bg-slate-50 dark:hover:bg-slate-700 transition-colors"
                        >
                            <DollarSign className="w-3.5 h-3.5" />
                            <span>Salary Credits</span>
                        </Link>
                        <a
                            href={exportCsvUrl}
                            className="inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-xl bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold transition shadow-sm"
                        >
                            <Download className="w-3.5 h-3.5" />
                            <span>Export CSV</span>
                        </a>
                    </div>
                </div>

                {/* Filters and KPI Cards Row */}
                <div className="grid grid-cols-1 lg:grid-cols-3 gap-5">
                    {/* Period & Department Filter Box */}
                    <div className="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 p-5 shadow-sm">
                        <div className="flex items-center gap-2 mb-4 pb-2 border-b border-slate-100 dark:border-slate-800">
                            <Filter className="w-4 h-4 text-blue-600" />
                            <h2 className="text-sm font-bold text-slate-900 dark:text-white">Audit Parameters</h2>
                        </div>
                        <form onSubmit={handleFilter} className="space-y-3.5">
                            <div className="grid grid-cols-2 gap-3">
                                <div>
                                    <label className="block text-xs font-semibold text-slate-500 mb-1">Year</label>
                                    <select
                                        value={selectedYear}
                                        onChange={(e) => setSelectedYear(Number(e.target.value))}
                                        className="w-full text-xs font-medium rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-slate-900 dark:text-white py-2 px-3 focus:outline-none focus:ring-2 focus:ring-blue-500"
                                    >
                                        {[2024, 2025, 2026, 2027].map(y => (
                                            <option key={y} value={y}>{y}</option>
                                        ))}
                                    </select>
                                </div>
                                <div>
                                    <label className="block text-xs font-semibold text-slate-500 mb-1">Month</label>
                                    <select
                                        value={selectedMonth}
                                        onChange={(e) => setSelectedMonth(Number(e.target.value))}
                                        className="w-full text-xs font-medium rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-slate-900 dark:text-white py-2 px-3 focus:outline-none focus:ring-2 focus:ring-blue-500"
                                    >
                                        {MONTH_NAMES.map((m, idx) => (
                                            <option key={m} value={idx + 1}>{m}</option>
                                        ))}
                                    </select>
                                </div>
                            </div>

                            <div>
                                <label className="block text-xs font-semibold text-slate-500 mb-1">Department Scope</label>
                                <select
                                    value={selectedDept}
                                    onChange={(e) => setSelectedDept(e.target.value)}
                                    className="w-full text-xs font-medium rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-slate-900 dark:text-white py-2 px-3 focus:outline-none focus:ring-2 focus:ring-blue-500"
                                >
                                    <option value="">All Departments</option>
                                    {departments.map(dept => (
                                        <option key={dept} value={dept}>{dept}</option>
                                    ))}
                                </select>
                            </div>

                            <div className="flex items-center gap-2 pt-1">
                                <button
                                    type="submit"
                                    className="flex-1 py-2 px-3 rounded-xl bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold transition"
                                >
                                    Apply Audit
                                </button>
                                <button
                                    type="button"
                                    onClick={handleReset}
                                    className="py-2 px-3 rounded-xl border border-slate-200 dark:border-slate-700 text-slate-600 dark:text-slate-300 text-xs font-medium hover:bg-slate-100 dark:hover:bg-slate-800 transition"
                                >
                                    Reset
                                </button>
                            </div>
                        </form>
                    </div>

                    {/* KPI Statistics */}
                    <div className="lg:col-span-2 grid grid-cols-1 sm:grid-cols-3 gap-4 content-start">
                        <div className="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 p-5 shadow-sm">
                            <div className="flex items-center justify-between">
                                <span className="text-xs font-bold uppercase tracking-wider text-slate-400">Total Workforce</span>
                                <div className="p-2 rounded-xl bg-blue-50 dark:bg-blue-900/30 text-blue-600">
                                    <Users className="w-4 h-4" />
                                </div>
                            </div>
                            <div className="mt-3 text-2xl font-black text-slate-900 dark:text-white">
                                {totalEmployeesInScope}
                            </div>
                            <div className="mt-1 text-[11px] text-slate-500">Personnel in current scope</div>
                        </div>

                        <div className="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 p-5 shadow-sm">
                            <div className="flex items-center justify-between">
                                <span className="text-xs font-bold uppercase tracking-wider text-slate-400">Total Credits</span>
                                <div className="p-2 rounded-xl bg-emerald-50 dark:bg-emerald-900/30 text-emerald-600">
                                    <TrendingUp className="w-4 h-4" />
                                </div>
                            </div>
                            <div className="mt-3 text-2xl font-black text-emerald-600 dark:text-emerald-400">
                                {Number(totalCreditsInScope).toFixed(1)}
                            </div>
                            <div className="mt-1 text-[11px] text-slate-500">Approved attendance units</div>
                        </div>

                        <div className="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 p-5 shadow-sm">
                            <div className="flex items-center justify-between">
                                <span className="text-xs font-bold uppercase tracking-wider text-slate-400">Total Payable</span>
                                <div className="p-2 rounded-xl bg-purple-50 dark:bg-purple-900/30 text-purple-600">
                                    <DollarSign className="w-4 h-4" />
                                </div>
                            </div>
                            <div className="mt-3 text-2xl font-black text-purple-600 dark:text-purple-400">
                                {Number(totalPayable).toLocaleString('en-US', { minimumFractionDigits: 0, maximumFractionDigits: 0 })} ETB
                            </div>
                            <div className="mt-1 text-[11px] text-slate-500">Projected compensation</div>
                        </div>
                    </div>
                </div>

                {/* Personnel Attendance Table */}
                <div className="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-sm overflow-hidden">
                    <div className="p-4 border-b border-slate-100 dark:border-slate-800 flex flex-col sm:flex-row items-center justify-between gap-3">
                        <div className="font-bold text-sm text-slate-900 dark:text-white">
                            Staff Summary ({filteredList.length} Associates)
                        </div>
                        <div className="relative w-full sm:w-64">
                            <Search className="w-4 h-4 absolute left-3 top-1/2 -translate-y-1/2 text-slate-400" />
                            <input
                                type="text"
                                placeholder="Search by name..."
                                value={search}
                                onChange={(e) => setSearch(e.target.value)}
                                className="w-full pl-9 pr-3 py-1.5 text-xs rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-slate-900 dark:text-white placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-blue-500"
                            />
                        </div>
                    </div>

                    <div className="overflow-x-auto">
                        <table className="w-full text-left border-collapse text-sm">
                            <thead>
                                <tr className="border-b border-slate-200 dark:border-slate-800 bg-slate-50 dark:bg-slate-800/60 text-[11px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">
                                    <th className="py-3 px-4">Employee</th>
                                    <th className="py-3 px-4">Department</th>
                                    <th className="py-3 px-4 text-center">Present Days</th>
                                    <th className="py-3 px-4 text-center">Late Days</th>
                                    <th className="py-3 px-4 text-center">Total Credits</th>
                                    <th className="py-3 px-4 text-right">Projected Pay (ETB)</th>
                                </tr>
                            </thead>
                            <tbody className="divide-y divide-slate-100 dark:divide-slate-800/60">
                                {filteredList.length === 0 ? (
                                    <tr>
                                        <td colSpan={6} className="py-12 text-center text-slate-400 text-sm">
                                            No personnel records found for {monthLabel} {selectedYear}.
                                        </td>
                                    </tr>
                                ) : (
                                    filteredList.map((row, idx) => {
                                        const emp = row.employee || {};
                                        return (
                                            <tr key={emp.id || idx} className="hover:bg-slate-50/70 dark:hover:bg-slate-800/40 transition-colors">
                                                <td className="py-3 px-4">
                                                    <div className="font-semibold text-slate-900 dark:text-white">
                                                        {emp.first_name} {emp.last_name}
                                                    </div>
                                                    <div className="text-xs text-slate-400">
                                                        ID #{emp.id}
                                                    </div>
                                                </td>
                                                <td className="py-3 px-4 text-xs font-medium text-slate-600 dark:text-slate-300">
                                                    {emp.department || 'Unassigned'}
                                                </td>
                                                <td className="py-3 px-4 text-center text-xs font-semibold text-slate-700 dark:text-slate-300">
                                                    {row.present_days ?? row.records ?? 0}
                                                </td>
                                                <td className="py-3 px-4 text-center text-xs font-semibold text-amber-600 dark:text-amber-400">
                                                    {row.late_days ?? 0}
                                                </td>
                                                <td className="py-3 px-4 text-center">
                                                    <span className="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-bold bg-blue-100 text-blue-800 dark:bg-blue-950/40 dark:text-blue-300">
                                                        {Number(row.total_credits || 0).toFixed(1)}
                                                    </span>
                                                </td>
                                                <td className="py-3 px-4 text-right font-mono font-bold text-slate-900 dark:text-white">
                                                    {Number(row.payable_amount || 0).toLocaleString('en-US', { minimumFractionDigits: 0, maximumFractionDigits: 0 })} ETB
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
