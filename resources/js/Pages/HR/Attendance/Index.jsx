import { useState } from 'react';
import { Head, Link, router, usePage } from '@inertiajs/react';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import {
    Clock,
    UserCheck,
    UserX,
    AlertCircle,
    Calendar,
    Search,
    LogIn,
    LogOut,
    FileSpreadsheet,
    DollarSign,
    BarChart3,
    CheckCircle2,
} from 'lucide-react';

export default function Index({
    attendances,
    employees = [],
    todayStats = {},
    myOpenAttendance,
    filters = {},
}) {
    const { auth } = usePage().props;
    const [dateFrom, setDateFrom] = useState(filters.date_from || '');
    const [dateTo, setDateTo] = useState(filters.date_to || '');
    const [empFilter, setEmpFilter] = useState(filters.employee_filter || '');
    const [statusFilter, setStatusFilter] = useState(filters.status || '');
    const [quickEmployeeId, setQuickEmployeeId] = useState('');

    const handleFilter = (e) => {
        if (e) e.preventDefault();
        router.get('/hr/attendance', {
            date_from: dateFrom,
            date_to: dateTo,
            employee_filter: empFilter,
            status: statusFilter,
        }, {
            preserveState: true,
            preserveScroll: true,
        });
    };

    const handleQuickCheckIn = (e) => {
        e.preventDefault();
        router.post('/hr/attendance/check-in', {
            employee_id: quickEmployeeId || undefined,
        });
    };

    const handleQuickCheckOut = (id) => {
        router.post(`/hr/attendance/check-out/${id}`);
    };

    const attendanceList = attendances?.data || [];

    return (
        <AuthenticatedLayout title="Attendance Roster" header="Human Resources">
            <div className="space-y-6">
                {/* Header */}
                <div className="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-2 border-b border-slate-200 dark:border-slate-800">
                    <div>
                        <h1 className="text-xl sm:text-2xl font-extrabold tracking-tight text-slate-900 dark:text-white">
                            Time & Attendance Tracking
                        </h1>
                        <p className="text-xs sm:text-sm text-slate-500 dark:text-slate-400 mt-0.5">
                            Daily clock-in/out verification, shift attendance logging, and automated credit calculation.
                        </p>
                    </div>

                    {/* Quick Access Nav Buttons */}
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
                            className="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs font-semibold text-slate-700 dark:text-slate-200 hover:bg-slate-50 dark:hover:bg-slate-700 transition-colors"
                        >
                            <DollarSign className="w-3.5 h-3.5" />
                            <span>Salary Credits</span>
                        </Link>
                    </div>
                </div>

                {/* Quick Check-in / Out Widget */}
                <div className="rounded-2xl border border-blue-100 dark:border-blue-900/40 bg-blue-50/40 dark:bg-blue-950/20 p-4">
                    <div className="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                        <div className="flex items-center gap-3">
                            <div className="h-10 w-10 rounded-xl bg-blue-900 text-white flex items-center justify-center shrink-0">
                                <Clock className="w-5 h-5" />
                            </div>
                            <div>
                                <h3 className="text-sm font-bold text-slate-900 dark:text-white">
                                    Instant Session Terminal
                                </h3>
                                <p className="text-xs text-slate-500 dark:text-slate-400">
                                    Record check-in or check-out for staff on duty today.
                                </p>
                            </div>
                        </div>

                        <div className="flex items-center gap-2">
                            {myOpenAttendance ? (
                                <button
                                    onClick={() => handleQuickCheckOut(myOpenAttendance.id)}
                                    className="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl bg-rose-600 hover:bg-rose-700 text-white font-bold text-xs shadow-sm transition-colors cursor-pointer"
                                >
                                    <LogOut className="w-4 h-4" />
                                    <span>Clock Out (Self)</span>
                                </button>
                            ) : (
                                <form onSubmit={handleQuickCheckIn} className="flex items-center gap-2">
                                    <select
                                        value={quickEmployeeId}
                                        onChange={(e) => setQuickEmployeeId(e.target.value)}
                                        className="px-3 py-1.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-900/20"
                                    >
                                        <option value="">Clock In Self</option>
                                        {employees.map((e) => (
                                            <option key={e.id} value={e.id}>
                                                {e.first_name} {e.last_name}
                                            </option>
                                        ))}
                                    </select>
                                    <button
                                        type="submit"
                                        className="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl bg-blue-900 hover:bg-blue-950 text-white font-bold text-xs shadow-sm transition-colors cursor-pointer"
                                    >
                                        <LogIn className="w-4 h-4" />
                                        <span>Check In</span>
                                    </button>
                                </form>
                            )}
                        </div>
                    </div>
                </div>

                {/* KPI Overview */}
                <div className="grid grid-cols-2 sm:grid-cols-4 gap-4">
                    <div className="rounded-2xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 p-4 shadow-xs">
                        <span className="text-[11px] font-semibold text-slate-400 block uppercase tracking-wider">Total Active Staff</span>
                        <div className="text-xl font-black text-slate-900 dark:text-white mt-1 font-mono">
                            {todayStats.total_employees || employees.length || 0}
                        </div>
                    </div>
                    <div className="rounded-2xl border border-emerald-200 dark:border-emerald-900/40 bg-emerald-50/40 dark:bg-emerald-950/20 p-4">
                        <span className="text-[11px] font-semibold text-emerald-600 dark:text-emerald-400 block uppercase tracking-wider">Present Today</span>
                        <div className="text-xl font-black text-emerald-700 dark:text-emerald-300 mt-1 font-mono">
                            {todayStats.present || 0}
                        </div>
                    </div>
                    <div className="rounded-2xl border border-amber-200 dark:border-amber-900/40 bg-amber-50/40 dark:bg-amber-950/20 p-4">
                        <span className="text-[11px] font-semibold text-amber-600 dark:text-amber-400 block uppercase tracking-wider">Late Arrivals</span>
                        <div className="text-xl font-black text-amber-700 dark:text-amber-300 mt-1 font-mono">
                            {todayStats.late || 0}
                        </div>
                    </div>
                    <div className="rounded-2xl border border-rose-200 dark:border-rose-900/40 bg-rose-50/40 dark:bg-rose-950/20 p-4">
                        <span className="text-[11px] font-semibold text-rose-600 dark:text-rose-400 block uppercase tracking-wider">On Leave / Absent</span>
                        <div className="text-xl font-black text-rose-700 dark:text-rose-300 mt-1 font-mono">
                            {todayStats.on_leave || todayStats.absent || 0}
                        </div>
                    </div>
                </div>

                {/* Filter Form */}
                <div className="rounded-2xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 p-4 shadow-xs">
                    <form onSubmit={handleFilter} className="grid grid-cols-1 sm:grid-cols-5 gap-3">
                        <div>
                            <input
                                type="date"
                                value={dateFrom}
                                onChange={(e) => setDateFrom(e.target.value)}
                                className="w-full px-3 py-2 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-xs text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-900/20"
                                placeholder="From Date"
                            />
                        </div>
                        <div>
                            <input
                                type="date"
                                value={dateTo}
                                onChange={(e) => setDateTo(e.target.value)}
                                className="w-full px-3 py-2 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-xs text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-900/20"
                                placeholder="To Date"
                            />
                        </div>
                        <div>
                            <select
                                value={empFilter}
                                onChange={(e) => setEmpFilter(e.target.value)}
                                className="w-full px-3 py-2 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-xs text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-900/20"
                            >
                                <option value="">All Employees</option>
                                {employees.map((e) => (
                                    <option key={e.id} value={e.id}>
                                        {e.first_name} {e.last_name}
                                    </option>
                                ))}
                            </select>
                        </div>
                        <div>
                            <select
                                value={statusFilter}
                                onChange={(e) => setStatusFilter(e.target.value)}
                                className="w-full px-3 py-2 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-xs text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-900/20"
                            >
                                <option value="">All Statuses</option>
                                <option value="present">Present</option>
                                <option value="late">Late</option>
                                <option value="absent">Absent</option>
                                <option value="leave">On Leave</option>
                            </select>
                        </div>
                        <div>
                            <button
                                type="submit"
                                className="w-full py-2 rounded-xl bg-blue-900 hover:bg-blue-950 text-white font-bold text-xs transition-colors cursor-pointer"
                            >
                                Filter Roster
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
                                    <th className="py-3.5 px-4">Employee</th>
                                    <th className="py-3.5 px-4">Date</th>
                                    <th className="py-3.5 px-4">Morning Session</th>
                                    <th className="py-3.5 px-4">Afternoon Session</th>
                                    <th className="py-3.5 px-4">Clock In / Out</th>
                                    <th className="py-3.5 px-4">Day Credit</th>
                                    <th className="py-3.5 px-4 text-right">Quick Action</th>
                                </tr>
                            </thead>
                            <tbody className="divide-y divide-slate-100 dark:divide-slate-800/80">
                                {attendanceList.length === 0 ? (
                                    <tr>
                                        <td colSpan={7} className="py-8 text-center text-slate-400 text-xs">
                                            No attendance logs recorded for selected dates.
                                        </td>
                                    </tr>
                                ) : (
                                    attendanceList.map((att) => (
                                        <tr key={att.id} className="hover:bg-slate-50/70 dark:hover:bg-slate-800/40 transition-colors">
                                            <td className="py-3 px-4">
                                                <div className="font-bold text-slate-900 dark:text-white">
                                                    {att.employee ? `${att.employee.first_name} ${att.employee.last_name}` : 'Unknown'}
                                                </div>
                                                <div className="text-[11px] text-slate-400">
                                                    {att.employee?.department || 'Operations'}
                                                </div>
                                            </td>
                                            <td className="py-3 px-4 text-slate-700 dark:text-slate-300 font-mono text-[11px]">
                                                {att.date ? new Date(att.date).toLocaleDateString() : '—'}
                                            </td>
                                            <td className="py-3 px-4">
                                                <span className={`inline-block px-2 py-0.5 rounded-md text-[10px] font-bold uppercase ${
                                                    att.morning_status === 'present'
                                                        ? 'bg-emerald-50 text-emerald-600 dark:bg-emerald-950/40 dark:text-emerald-400'
                                                        : att.morning_status === 'late'
                                                        ? 'bg-amber-50 text-amber-600 dark:bg-amber-950/40 dark:text-amber-400'
                                                        : att.morning_status === 'leave'
                                                        ? 'bg-sky-50 text-sky-600 dark:bg-sky-950/40 dark:text-sky-400'
                                                        : 'bg-slate-100 text-slate-600 dark:bg-slate-800 dark:text-slate-400'
                                                }`}>
                                                    {att.morning_status || 'absent'}
                                                </span>
                                            </td>
                                            <td className="py-3 px-4">
                                                <span className={`inline-block px-2 py-0.5 rounded-md text-[10px] font-bold uppercase ${
                                                    att.afternoon_status === 'present'
                                                        ? 'bg-emerald-50 text-emerald-600 dark:bg-emerald-950/40 dark:text-emerald-400'
                                                        : att.afternoon_status === 'leave'
                                                        ? 'bg-sky-50 text-sky-600 dark:bg-sky-950/40 dark:text-sky-400'
                                                        : 'bg-slate-100 text-slate-600 dark:bg-slate-800 dark:text-slate-400'
                                                }`}>
                                                    {att.afternoon_status || 'absent'}
                                                </span>
                                            </td>
                                            <td className="py-3 px-4 font-mono text-[11px] text-slate-600 dark:text-slate-300">
                                                {att.clock_in ? new Date(att.clock_in).toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' }) : '—'}
                                                {' / '}
                                                {att.clock_out ? new Date(att.clock_out).toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' }) : '—'}
                                            </td>
                                            <td className="py-3 px-4 font-bold font-mono text-slate-900 dark:text-white">
                                                {att.total_credit != null ? Number(att.total_credit).toFixed(2) : '1.00'}
                                            </td>
                                            <td className="py-3 px-4 text-right">
                                                {!att.clock_out && (
                                                    <button
                                                        onClick={() => handleQuickCheckOut(att.id)}
                                                        className="px-2.5 py-1 rounded-lg bg-slate-100 hover:bg-rose-50 text-slate-700 hover:text-rose-600 dark:bg-slate-800 dark:hover:bg-rose-950/50 dark:text-slate-300 dark:hover:text-rose-300 font-bold text-[11px] transition-colors cursor-pointer"
                                                    >
                                                        Clock Out
                                                    </button>
                                                )}
                                            </td>
                                        </tr>
                                    ))
                                )}
                            </tbody>
                        </table>
                    </div>

                    {/* Pagination */}
                    {attendances?.links && attendances.links.length > 3 && (
                        <div className="px-4 py-3 border-t border-slate-200 dark:border-slate-800 flex items-center justify-between text-xs text-slate-500">
                            <div>
                                Showing {attendances.from || 0} to {attendances.to || 0} of {attendances.total || 0} entries
                            </div>
                            <div className="flex items-center gap-1">
                                {attendances.links.map((link, idx) => (
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
