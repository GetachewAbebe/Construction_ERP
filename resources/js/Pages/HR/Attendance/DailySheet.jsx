import { useState } from 'react';
import { Head, Link, router } from '@inertiajs/react';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import {
    Calendar,
    Clock,
    UserCheck,
    Search,
    FileSpreadsheet,
    DollarSign,
    BarChart3,
    ArrowLeft,
    CheckCircle2,
    AlertCircle,
    Loader2,
    ShieldAlert,
} from 'lucide-react';

export default function DailySheet({
    date,
    employees = [],
    attendances: initialAttendances = {},
    isToday = true,
}) {
    const [selectedDate, setSelectedDate] = useState(date);
    const [search, setSearch] = useState('');
    const [attendances, setAttendances] = useState(initialAttendances);
    const [loadingMap, setLoadingMap] = useState({});
    const [toast, setToast] = useState(null);

    const showToast = (message, type = 'success') => {
        setToast({ message, type });
        setTimeout(() => setToast(null), 3500);
    };

    const handleDateChange = (newDate) => {
        setSelectedDate(newDate);
        router.get('/hr/attendance/daily-sheet', { date: newDate }, { preserveState: false });
    };

    const handleToggle = async (employeeId, session, action) => {
        if (!isToday) {
            showToast('Editing locked. Live check-in/out is restricted to current day.', 'error');
            return;
        }

        const key = `${employeeId}-${session}`;
        setLoadingMap(prev => ({ ...prev, [key]: true }));

        try {
            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
            const res = await fetch('/hr/attendance/toggle', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': csrfToken || '',
                },
                body: JSON.stringify({
                    employee_id: employeeId,
                    date: selectedDate,
                    session,
                    action,
                }),
            });

            const data = await res.json();

            if (data.success) {
                setAttendances(prev => {
                    const current = prev[employeeId] || {};
                    const updated = {
                        ...current,
                        employee_id: employeeId,
                        date: selectedDate,
                        total_credit: data.total_credit,
                    };

                    if (session === 'morning') {
                        updated.morning_status = data.status;
                        if (data.time) updated.clock_in = data.time;
                    } else {
                        updated.afternoon_status = data.status;
                        if (data.time) updated.clock_out = data.time;
                    }

                    return {
                        ...prev,
                        [employeeId]: updated,
                    };
                });
                showToast(`Session updated: ${session} marked as ${data.status.toUpperCase()}`);
            } else {
                showToast(data.message || 'Action rejected by server', 'error');
            }
        } catch (err) {
            showToast('Network error while toggling attendance', 'error');
        } finally {
            setLoadingMap(prev => ({ ...prev, [key]: false }));
        }
    };

    const filteredEmployees = employees.filter(emp => {
        const fullName = `${emp.first_name || ''} ${emp.last_name || ''}`.toLowerCase();
        const dept = (emp.department || '').toLowerCase();
        const q = search.toLowerCase();
        return fullName.includes(q) || dept.includes(q);
    });

    const formatClock = (timeVal) => {
        if (!timeVal) return '--:--';
        if (typeof timeVal === 'string') {
            if (timeVal.includes('T')) {
                const parts = timeVal.split('T')[1]?.split(':');
                if (parts && parts.length >= 2) return `${parts[0]}:${parts[1]}`;
            }
            if (timeVal.length >= 5) return timeVal.substring(0, 5);
        }
        return timeVal;
    };

    return (
        <AuthenticatedLayout title={`Daily Sheet • ${selectedDate}`} header="Human Resources">
            <Head title={`Daily Attendance Sheet (${selectedDate})`} />

            {/* Toast feedback */}
            {toast && (
                <div className="fixed bottom-5 right-5 z-50 animate-bounce">
                    <div className={`flex items-center gap-2 px-4 py-3 rounded-xl shadow-2xl text-xs sm:text-sm font-semibold text-white ${
                        toast.type === 'error' ? 'bg-rose-600' : 'bg-emerald-600'
                    }`}>
                        {toast.type === 'error' ? <AlertCircle className="w-4 h-4" /> : <CheckCircle2 className="w-4 h-4" />}
                        <span>{toast.message}</span>
                    </div>
                </div>
            )}

            <div className="space-y-6">
                {/* Navigation Bar */}
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
                                Daily Attendance Roster
                            </h1>
                        </div>
                        <p className="text-xs sm:text-sm text-slate-500 dark:text-slate-400 mt-0.5 ml-7">
                            Live morning check-ins and afternoon check-outs for staff presence.
                        </p>
                    </div>

                    {/* Quick Access Nav Tabs */}
                    <div className="flex flex-wrap items-center gap-2">
                        <Link
                            href="/hr/attendance/daily-sheet"
                            className="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl border border-blue-600 bg-blue-50 dark:bg-blue-900/30 text-xs font-semibold text-blue-700 dark:text-blue-300"
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

                {/* Filters & Date Selector Bar */}
                <div className="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 p-4 shadow-sm flex flex-col md:flex-row items-center justify-between gap-4">
                    <div className="flex items-center gap-3 w-full md:w-auto">
                        <label className="text-xs font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400 shrink-0">
                            Sheet Date:
                        </label>
                        <input
                            type="date"
                            value={selectedDate}
                            onChange={(e) => handleDateChange(e.target.value)}
                            className="px-3 py-2 text-sm rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-white font-medium focus:ring-2 focus:ring-blue-500 focus:outline-none"
                        />
                        {!isToday && (
                            <button
                                type="button"
                                onClick={() => handleDateChange(new Date().toISOString().split('T')[0])}
                                className="px-3 py-2 text-xs font-semibold rounded-xl bg-blue-100 dark:bg-blue-900/40 text-blue-700 dark:text-blue-300 hover:bg-blue-200 transition"
                            >
                                Jump to Today
                            </button>
                        )}
                    </div>

                    <div className="relative w-full md:w-72">
                        <Search className="w-4 h-4 absolute left-3 top-1/2 -translate-y-1/2 text-slate-400" />
                        <input
                            type="text"
                            placeholder="Filter employee or department..."
                            value={search}
                            onChange={(e) => setSearch(e.target.value)}
                            className="w-full pl-9 pr-3 py-2 text-xs rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-slate-900 dark:text-white placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-blue-500"
                        />
                    </div>
                </div>

                {!isToday && (
                    <div className="flex items-center gap-2.5 p-3.5 rounded-xl border border-amber-200 dark:border-amber-900/50 bg-amber-50 dark:bg-amber-950/20 text-xs sm:text-sm text-amber-800 dark:text-amber-300">
                        <ShieldAlert className="w-4 h-4 shrink-0 text-amber-600 dark:text-amber-400" />
                        <span>
                            <strong>Historical Read-Only View:</strong> Active check-in & check-out actions are disabled for past or future dates. Select today's date to perform real-time verification.
                        </span>
                    </div>
                )}

                {/* Employees Sheet Table */}
                <div className="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-sm overflow-hidden">
                    <div className="overflow-x-auto">
                        <table className="w-full text-left border-collapse text-sm">
                            <thead>
                                <tr className="border-b border-slate-200 dark:border-slate-800 bg-slate-50 dark:bg-slate-800/60 text-[11px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">
                                    <th className="py-3 px-4">Employee</th>
                                    <th className="py-3 px-4 text-center">Morning Session (Clock-In)</th>
                                    <th className="py-3 px-4 text-center">Evening Session (Clock-Out)</th>
                                    <th className="py-3 px-4 text-center">Day Yield</th>
                                </tr>
                            </thead>
                            <tbody className="divide-y divide-slate-100 dark:divide-slate-800/60">
                                {filteredEmployees.length === 0 ? (
                                    <tr>
                                        <td colSpan={4} className="py-12 text-center text-slate-400 text-sm">
                                            No personnel match your search criteria.
                                        </td>
                                    </tr>
                                ) : (
                                    filteredEmployees.map(emp => {
                                        const att = attendances[emp.id] || null;
                                        const morning = att ? att.morning_status : 'absent';
                                        const afternoon = att ? att.afternoon_status : 'absent';
                                        const mClock = formatClock(att?.clock_in);
                                        const eClock = formatClock(att?.clock_out);
                                        const totalCredit = att?.total_credit !== undefined ? att.total_credit : 0;

                                        const morningActive = ['present', 'late'].includes(morning);
                                        const afternoonActive = afternoon === 'present';

                                        const mLoading = loadingMap[`${emp.id}-morning`];
                                        const eLoading = loadingMap[`${emp.id}-afternoon`];

                                        return (
                                            <tr
                                                key={emp.id}
                                                className={`hover:bg-slate-50/70 dark:hover:bg-slate-800/40 transition-colors ${
                                                    !isToday ? 'opacity-80' : ''
                                                }`}
                                            >
                                                {/* Employee details */}
                                                <td className="py-3 px-4">
                                                    <div className="flex items-center gap-3">
                                                        <div className="h-9 w-9 rounded-xl bg-blue-100 dark:bg-blue-900/40 text-blue-700 dark:text-blue-300 font-bold flex items-center justify-center text-xs shrink-0">
                                                            {emp.first_name ? emp.first_name.charAt(0).toUpperCase() : 'E'}
                                                        </div>
                                                        <div>
                                                            <div className="font-semibold text-slate-900 dark:text-white text-sm">
                                                                {emp.first_name} {emp.last_name}
                                                            </div>
                                                            <div className="text-xs text-slate-400">
                                                                {emp.department || 'General'} • ID #{emp.id}
                                                            </div>
                                                        </div>
                                                    </div>
                                                </td>

                                                {/* Morning Check-In */}
                                                <td className="py-3 px-4 text-center">
                                                    <div className="flex flex-col items-center gap-1.5">
                                                        <span className="font-mono text-xs font-bold text-blue-600 dark:text-blue-400">
                                                            {mClock}
                                                        </span>
                                                        <button
                                                            type="button"
                                                            disabled={!isToday || mLoading}
                                                            onClick={() => handleToggle(emp.id, 'morning', 'check-in')}
                                                            className={`w-32 py-1.5 px-3 rounded-xl text-xs font-bold transition flex items-center justify-center gap-1.5 shadow-xs ${
                                                                morning === 'late'
                                                                    ? 'bg-amber-600 hover:bg-amber-700 text-white'
                                                                    : morningActive
                                                                    ? 'bg-emerald-600 hover:bg-emerald-700 text-white'
                                                                    : 'border border-slate-300 dark:border-slate-700 text-slate-700 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800'
                                                            } ${!isToday ? 'cursor-not-allowed opacity-60' : ''}`}
                                                        >
                                                            {mLoading ? (
                                                                <Loader2 className="w-3.5 h-3.5 animate-spin" />
                                                            ) : morning === 'late' ? (
                                                                'LATE IN'
                                                            ) : morningActive ? (
                                                                'CHECKED IN'
                                                            ) : (
                                                                'CHECK IN'
                                                            )}
                                                        </button>
                                                    </div>
                                                </td>

                                                {/* Afternoon Check-Out */}
                                                <td className="py-3 px-4 text-center">
                                                    <div className="flex flex-col items-center gap-1.5">
                                                        <span className="font-mono text-xs font-bold text-blue-600 dark:text-blue-400">
                                                            {eClock}
                                                        </span>
                                                        <button
                                                            type="button"
                                                            disabled={!isToday || eLoading}
                                                            onClick={() => handleToggle(emp.id, 'afternoon', 'check-out')}
                                                            className={`w-32 py-1.5 px-3 rounded-xl text-xs font-bold transition flex items-center justify-center gap-1.5 shadow-xs ${
                                                                afternoonActive
                                                                    ? 'bg-emerald-600 hover:bg-emerald-700 text-white'
                                                                    : 'border border-slate-300 dark:border-slate-700 text-slate-700 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800'
                                                            } ${!isToday ? 'cursor-not-allowed opacity-60' : ''}`}
                                                        >
                                                            {eLoading ? (
                                                                <Loader2 className="w-3.5 h-3.5 animate-spin" />
                                                            ) : afternoonActive ? (
                                                                'CHECKED OUT'
                                                            ) : (
                                                                'CHECK OUT'
                                                            )}
                                                        </button>
                                                    </div>
                                                </td>

                                                {/* Day yield */}
                                                <td className="py-3 px-4 text-center">
                                                    <span className={`inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold ${
                                                        Number(totalCredit) >= 1
                                                            ? 'bg-emerald-100 text-emerald-800 dark:bg-emerald-950/40 dark:text-emerald-300'
                                                            : Number(totalCredit) > 0
                                                            ? 'bg-amber-100 text-amber-800 dark:bg-amber-950/40 dark:text-amber-300'
                                                            : 'bg-slate-100 text-slate-600 dark:bg-slate-800 dark:text-slate-400'
                                                    }`}>
                                                        {Number(totalCredit).toFixed(1)} Credit
                                                    </span>
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
