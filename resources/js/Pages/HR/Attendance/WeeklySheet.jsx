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
    CheckCircle2,
    AlertCircle,
    Loader2,
    ShieldAlert,
} from 'lucide-react';

const DAYS_OF_WEEK = [
    { name: 'Monday', offset: 0 },
    { name: 'Tuesday', offset: 1 },
    { name: 'Wednesday', offset: 2 },
    { name: 'Thursday', offset: 3 },
    { name: 'Friday', offset: 4 },
    { name: 'Saturday', offset: 5 },
];

export default function WeeklySheet({
    monday,
    saturday,
    employees = [],
    attendances: initialAttendances = {},
    date,
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

    // Calculate dates for Mon-Sat
    const mondayObj = new Date(monday);
    const todayStr = new Date().toISOString().split('T')[0];

    const weekDays = DAYS_OF_WEEK.map(({ name, offset }) => {
        const d = new Date(mondayObj);
        d.setDate(d.getDate() + offset);
        const dateStr = d.toISOString().split('T')[0];
        const display = d.toLocaleDateString('en-US', { month: 'short', day: 'numeric' });
        return {
            name,
            dateStr,
            display,
            isToday: dateStr === todayStr,
        };
    });

    const handleDateChange = (newDate) => {
        setSelectedDate(newDate);
        router.get('/hr/attendance/weekly-sheet', { date: newDate }, { preserveState: false });
    };

    const navigateWeek = (offsetDays) => {
        const d = new Date(selectedDate);
        d.setDate(d.getDate() + offsetDays);
        handleDateChange(d.toISOString().split('T')[0]);
    };

    const handleToggle = async (employeeId, dayDateStr, session, action) => {
        if (dayDateStr !== todayStr) {
            showToast('Editing restricted: live logging is enabled for today only.', 'error');
            return;
        }

        const key = `${employeeId}-${dayDateStr}-${session}`;
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
                    date: dayDateStr,
                    session,
                    action,
                }),
            });

            const data = await res.json();

            if (data.success) {
                setAttendances(prev => {
                    const empMap = { ...(prev[employeeId] || {}) };
                    const currentRecords = empMap[dayDateStr] ? [...empMap[dayDateStr]] : [{}];
                    const targetRecord = { ...currentRecords[0] };

                    if (session === 'morning') {
                        targetRecord.morning_status = data.status;
                        if (data.time) targetRecord.clock_in = data.time;
                    } else {
                        targetRecord.afternoon_status = data.status;
                        if (data.time) targetRecord.clock_out = data.time;
                    }

                    targetRecord.total_credit = data.total_credit;
                    empMap[dayDateStr] = [targetRecord];

                    return {
                        ...prev,
                        [employeeId]: empMap,
                    };
                });
                showToast(`Session updated for ${dayDateStr}`);
            } else {
                showToast(data.message || 'Action rejected', 'error');
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
        <AuthenticatedLayout title="Weekly Attendance Matrix" header="Human Resources">
            <Head title={`Weekly Attendance (${monday} to ${saturday})`} />

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
                                Weekly Workforce Presence
                            </h1>
                        </div>
                        <p className="text-xs sm:text-sm text-slate-500 dark:text-slate-400 mt-0.5 ml-7">
                            Monday through Saturday attendance grid with shift check-ins and check-outs.
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
                            className="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl border border-blue-600 bg-blue-50 dark:bg-blue-900/30 text-xs font-semibold text-blue-700 dark:text-blue-300"
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

                {/* Filter / Week Navigation Bar */}
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
                                Week of {weekDays[0].display} — {weekDays[5].display}
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

                        <button
                            type="button"
                            onClick={() => handleDateChange(todayStr)}
                            className="ml-2 px-3 py-1.5 text-xs font-semibold rounded-xl bg-blue-50 dark:bg-blue-900/30 text-blue-700 dark:text-blue-300 hover:bg-blue-100 transition"
                        >
                            Current Week
                        </button>
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

                {/* Info Alert */}
                <div className="flex items-center gap-2.5 p-3.5 rounded-xl border border-blue-200 dark:border-blue-900/40 bg-blue-50/50 dark:bg-blue-950/20 text-xs sm:text-sm text-blue-900 dark:text-blue-300">
                    <ShieldAlert className="w-4 h-4 shrink-0 text-blue-600 dark:text-blue-400" />
                    <span>
                        <strong>Weekly Matrix Overview:</strong> The highlighted column represents today's active session. Live check-in/out buttons are operational for today; prior days reflect archived records.
                    </span>
                </div>

                {/* Table */}
                <div className="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-sm overflow-hidden">
                    <div className="overflow-x-auto">
                        <table className="w-full text-left border-collapse text-xs">
                            <thead>
                                <tr className="border-b border-slate-200 dark:border-slate-800 bg-slate-50 dark:bg-slate-800/60 text-[11px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">
                                    <th className="py-3 px-4 min-w-[200px] sticky left-0 bg-slate-50 dark:bg-slate-800 z-10 shadow-r">
                                        Employee
                                    </th>
                                    {weekDays.map(day => (
                                        <th
                                            key={day.name}
                                            className={`py-3 px-2 text-center min-w-[140px] ${
                                                day.isToday
                                                    ? 'bg-blue-100/70 dark:bg-blue-900/40 text-blue-950 dark:text-blue-200 font-extrabold'
                                                    : ''
                                            }`}
                                        >
                                            <div>{day.name}</div>
                                            <div className="text-[10px] font-medium opacity-70">{day.display}</div>
                                        </th>
                                    ))}
                                </tr>
                            </thead>
                            <tbody className="divide-y divide-slate-100 dark:divide-slate-800/60">
                                {filteredEmployees.length === 0 ? (
                                    <tr>
                                        <td colSpan={7} className="py-12 text-center text-slate-400 text-sm">
                                            No personnel found matching the criteria.
                                        </td>
                                    </tr>
                                ) : (
                                    filteredEmployees.map(emp => {
                                        const empAttMap = attendances[emp.id] || {};

                                        return (
                                            <tr key={emp.id} className="hover:bg-slate-50/50 dark:hover:bg-slate-800/30 transition-colors">
                                                {/* Sticky Employee column */}
                                                <td className="py-3 px-4 sticky left-0 bg-white dark:bg-slate-900 z-10">
                                                    <div className="font-semibold text-slate-900 dark:text-white">
                                                        {emp.first_name} {emp.last_name}
                                                    </div>
                                                    <div className="text-[11px] text-slate-400">
                                                        {emp.department || 'General'}
                                                    </div>
                                                </td>

                                                {/* Week Days */}
                                                {weekDays.map(day => {
                                                    const dayAttArr = empAttMap[day.dateStr];
                                                    const att = Array.isArray(dayAttArr) ? dayAttArr[0] : dayAttArr;
                                                    const morning = att?.morning_status || 'absent';
                                                    const afternoon = att?.afternoon_status || 'absent';
                                                    const mClock = formatClock(att?.clock_in);
                                                    const eClock = formatClock(att?.clock_out);

                                                    const mLoading = loadingMap[`${emp.id}-${day.dateStr}-morning`];
                                                    const eLoading = loadingMap[`${emp.id}-${day.dateStr}-afternoon`];

                                                    return (
                                                        <td
                                                            key={day.name}
                                                            className={`py-2 px-2 align-top text-center border-l border-slate-100 dark:border-slate-800/60 ${
                                                                day.isToday ? 'bg-blue-50/30 dark:bg-blue-950/10' : ''
                                                            }`}
                                                        >
                                                            <div className="flex flex-col gap-1.5">
                                                                {/* Morning */}
                                                                <div className="p-1.5 rounded-lg border border-slate-200/80 dark:border-slate-800 bg-white dark:bg-slate-800/50 flex flex-col gap-1">
                                                                    <div className="flex items-center justify-between text-[10px] text-slate-400 font-semibold px-1">
                                                                        <span>IN</span>
                                                                        <span className="font-mono text-blue-600 dark:text-blue-400">{mClock}</span>
                                                                    </div>
                                                                    <button
                                                                        type="button"
                                                                        disabled={!day.isToday || mLoading}
                                                                        onClick={() => handleToggle(emp.id, day.dateStr, 'morning', 'check-in')}
                                                                        className={`w-full py-1 text-[10px] font-bold rounded-md transition flex items-center justify-center gap-1 ${
                                                                            morning === 'late'
                                                                                ? 'bg-amber-600 text-white'
                                                                                : ['present'].includes(morning)
                                                                                ? 'bg-emerald-600 text-white'
                                                                                : 'border border-slate-200 dark:border-slate-700 text-slate-600 dark:text-slate-400 hover:bg-slate-100'
                                                                        } ${!day.isToday ? 'cursor-not-allowed opacity-60' : ''}`}
                                                                    >
                                                                        {mLoading ? (
                                                                            <Loader2 className="w-3 h-3 animate-spin" />
                                                                        ) : morning === 'late' ? (
                                                                            'LATE'
                                                                        ) : morning === 'present' ? (
                                                                            'PRESENT'
                                                                        ) : (
                                                                            'CHECK IN'
                                                                        )}
                                                                    </button>
                                                                </div>

                                                                {/* Evening */}
                                                                <div className="p-1.5 rounded-lg border border-slate-200/80 dark:border-slate-800 bg-white dark:bg-slate-800/50 flex flex-col gap-1">
                                                                    <div className="flex items-center justify-between text-[10px] text-slate-400 font-semibold px-1">
                                                                        <span>OUT</span>
                                                                        <span className="font-mono text-blue-600 dark:text-blue-400">{eClock}</span>
                                                                    </div>
                                                                    <button
                                                                        type="button"
                                                                        disabled={!day.isToday || eLoading}
                                                                        onClick={() => handleToggle(emp.id, day.dateStr, 'afternoon', 'check-out')}
                                                                        className={`w-full py-1 text-[10px] font-bold rounded-md transition flex items-center justify-center gap-1 ${
                                                                            afternoon === 'present'
                                                                                ? 'bg-emerald-600 text-white'
                                                                                : 'border border-slate-200 dark:border-slate-700 text-slate-600 dark:text-slate-400 hover:bg-slate-100'
                                                                        } ${!day.isToday ? 'cursor-not-allowed opacity-60' : ''}`}
                                                                    >
                                                                        {eLoading ? (
                                                                            <Loader2 className="w-3 h-3 animate-spin" />
                                                                        ) : afternoon === 'present' ? (
                                                                            'DEPARTED'
                                                                        ) : (
                                                                            'CHECK OUT'
                                                                        )}
                                                                    </button>
                                                                </div>
                                                            </div>
                                                        </td>
                                                    );
                                                })}
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
