import { useState } from 'react';
import { Head, Link } from '@inertiajs/react';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import AttendanceTrendChart from '@/Components/Dashboard/AttendanceTrendChart';
import DonutBreakdownChart from '@/Components/Dashboard/DonutBreakdownChart';
import {
    Users,
    UserCheck,
    Calendar,
    Clock,
    UserPlus,
    CalendarCheck,
    Building2,
    ArrowRight,
    ArrowUpRight,
    CheckCircle2,
    AlertCircle,
    FileSpreadsheet,
    Shield,
    HardHat,
    Briefcase,
    Search,
    ChevronRight,
} from 'lucide-react';

export default function HrDashboard({
    employeeCount = 0,
    activeEmployees = 0,
    onLeaveTodayCount = 0,
    pendingLeaveApprovals = 0,
    recentHires = 0,
    latestEmployees = [],
    pendingLeaves = [],
    departmentStats = [],
    chartLabels = [],
    onTimeData = [],
    lateData = [],
    attendanceDailyTotals = [],
    presentToday = 0,
    lateToday = 0,
    attendanceRate = 100,
    punctualityRate = 95,
}) {
    const [staffSearch, setStaffSearch] = useState('');

    const filteredEmployees = latestEmployees.filter(emp =>
        `${emp.first_name || ''} ${emp.last_name || ''}`.toLowerCase().includes(staffSearch.toLowerCase()) ||
        (emp.department_rel?.name || '').toLowerCase().includes(staffSearch.toLowerCase()) ||
        (emp.position_rel?.name || emp.position || '').toLowerCase().includes(staffSearch.toLowerCase())
    );

    // Format department stats for Donut Breakdown
    const formattedDeptData = departmentStats.map(d => ({
        category: d.name,
        total: Number(d.total || 0),
    }));

    return (
        <AuthenticatedLayout title="HR Workforce Hub" header="Human Resources Hub">
            <div className="space-y-6">
                {/* HERO HEADER */}
                <div className="rounded-3xl bg-gradient-to-r from-blue-950 via-indigo-900 to-slate-900 p-6 sm:p-8 text-white shadow-lg relative overflow-hidden">
                    <div className="absolute right-0 top-0 translate-x-12 -translate-y-12 w-96 h-96 bg-blue-500/10 rounded-full blur-3xl pointer-events-none" />

                    <div className="relative z-10 flex flex-col lg:flex-row lg:items-center lg:justify-between gap-6">
                        <div>
                            <div className="flex items-center gap-2 mb-2">
                                <span className="px-2.5 py-0.5 rounded-full bg-blue-500/20 text-blue-300 text-[10px] font-extrabold tracking-wider uppercase border border-blue-500/30 flex items-center gap-1.5">
                                    <span className="w-1.5 h-1.5 rounded-full bg-blue-400 animate-pulse" />
                                    Workforce Command &amp; Field Labor Operations
                                </span>
                                <span className="text-xs text-slate-300 hidden sm:inline">
                                    Site Operations Roster
                                </span>
                            </div>
                            <h1 className="text-2xl sm:text-3xl font-black tracking-tight text-white">
                                Personnel Directory &amp; Shift Rostering
                            </h1>
                            <p className="text-xs sm:text-sm text-slate-300 mt-1 max-w-2xl">
                                Site crew deployment, shift attendance tracking, leave requests clearing, and workforce allocation across construction projects.
                            </p>
                        </div>

                        <div className="flex flex-wrap items-center gap-2.5 shrink-0">
                            <Link
                                href="/hr/employees/create"
                                className="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-white text-blue-950 font-extrabold text-xs shadow-md hover:bg-slate-100 transition-all cursor-pointer"
                            >
                                <UserPlus className="w-4 h-4 text-blue-700" />
                                <span>Onboard Personnel</span>
                            </Link>

                            <Link
                                href="/admin/requests/leave-approvals"
                                className="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-white/10 hover:bg-white/20 text-white font-bold text-xs border border-white/20 backdrop-blur-sm transition-all cursor-pointer relative"
                            >
                                <Calendar className="w-4 h-4 text-purple-300" />
                                <span>Leave Approvals</span>
                                {pendingLeaveApprovals > 0 && (
                                    <span className="px-1.5 py-0.2 rounded-full bg-amber-400 text-slate-950 text-[10px] font-black ml-0.5">
                                        {pendingLeaveApprovals}
                                    </span>
                                )}
                            </Link>

                            <Link
                                href="/hr/attendance"
                                className="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-white/10 hover:bg-white/20 text-white font-bold text-xs border border-white/20 backdrop-blur-sm transition-all cursor-pointer"
                            >
                                <CalendarCheck className="w-4 h-4 text-emerald-300" />
                                <span>Daily Attendance</span>
                            </Link>
                        </div>
                    </div>
                </div>

                {/* 5 TOP WORKFORCE KPI METRICS */}
                <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4">
                    {/* 1. Total Workforce */}
                    <div className="rounded-2xl sm:rounded-3xl border border-slate-100 dark:border-slate-800/80 bg-white dark:bg-slate-900 p-5 shadow-xs flex flex-col justify-between">
                        <div className="flex items-start justify-between gap-2">
                            <div>
                                <span className="text-[11px] font-bold uppercase tracking-wider text-slate-400">
                                    Total Workforce
                                </span>
                                <div className="text-2xl sm:text-3xl font-black text-slate-900 dark:text-white font-mono mt-1">
                                    {employeeCount}
                                </div>
                            </div>
                            <div className="p-2.5 rounded-2xl bg-blue-50 dark:bg-blue-950/60 text-blue-600 dark:text-blue-400 shrink-0">
                                <Users className="w-5 h-5" />
                            </div>
                        </div>
                        <div className="mt-3 pt-2.5 border-t border-slate-100 dark:border-slate-800 flex items-center justify-between text-[11px]">
                            <span className="text-slate-500">Active Personnel</span>
                            <span className="font-bold text-blue-600 dark:text-blue-400">{activeEmployees} On Duty</span>
                        </div>
                    </div>

                    {/* 2. On-Site Today */}
                    <div className="rounded-2xl sm:rounded-3xl border border-slate-100 dark:border-slate-800/80 bg-white dark:bg-slate-900 p-5 shadow-xs flex flex-col justify-between">
                        <div className="flex items-start justify-between gap-2">
                            <div>
                                <span className="text-[11px] font-bold uppercase tracking-wider text-slate-400">
                                    Present Today
                                </span>
                                <div className="text-2xl sm:text-3xl font-black text-emerald-600 dark:text-emerald-400 font-mono mt-1">
                                    {presentToday > 0 ? presentToday : (activeEmployees > 0 ? activeEmployees : 0)}
                                </div>
                            </div>
                            <div className="p-2.5 rounded-2xl bg-emerald-50 dark:bg-emerald-950/60 text-emerald-600 dark:text-emerald-400 shrink-0">
                                <UserCheck className="w-5 h-5" />
                            </div>
                        </div>
                        <div className="mt-3 pt-2.5 border-t border-slate-100 dark:border-slate-800 flex items-center justify-between text-[11px]">
                            <span className="text-slate-500">Deployment Rate</span>
                            <span className="font-bold text-emerald-600 dark:text-emerald-400 font-mono">
                                {attendanceRate}% Today
                            </span>
                        </div>
                    </div>

                    {/* 3. Punctuality Rate */}
                    <div className="rounded-2xl sm:rounded-3xl border border-slate-100 dark:border-slate-800/80 bg-white dark:bg-slate-900 p-5 shadow-xs flex flex-col justify-between">
                        <div className="flex items-start justify-between gap-2">
                            <div>
                                <span className="text-[11px] font-bold uppercase tracking-wider text-slate-400">
                                    Punctuality Rate
                                </span>
                                <div className="text-2xl sm:text-3xl font-black text-teal-600 dark:text-teal-400 font-mono mt-1">
                                    {punctualityRate}%
                                </div>
                            </div>
                            <div className="p-2.5 rounded-2xl bg-teal-50 dark:bg-teal-950/60 text-teal-600 dark:text-teal-400 shrink-0">
                                <Clock className="w-5 h-5" />
                            </div>
                        </div>
                        <div className="mt-3 pt-2.5 border-t border-slate-100 dark:border-slate-800 flex items-center justify-between text-[11px]">
                            <span className="text-slate-500">Late Clock-Ins</span>
                            <span className={`font-bold font-mono ${lateToday > 0 ? 'text-amber-600 dark:text-amber-400' : 'text-slate-500'}`}>
                                {lateToday} Staff
                            </span>
                        </div>
                    </div>

                    {/* 4. On Leave Today */}
                    <div className="rounded-2xl sm:rounded-3xl border border-slate-100 dark:border-slate-800/80 bg-white dark:bg-slate-900 p-5 shadow-xs flex flex-col justify-between">
                        <div className="flex items-start justify-between gap-2">
                            <div>
                                <span className="text-[11px] font-bold uppercase tracking-wider text-slate-400">
                                    On Leave Today
                                </span>
                                <div className="text-2xl sm:text-3xl font-black text-sky-600 dark:text-sky-400 font-mono mt-1">
                                    {onLeaveTodayCount}
                                </div>
                            </div>
                            <div className="p-2.5 rounded-2xl bg-sky-50 dark:bg-sky-950/60 text-sky-600 dark:text-sky-400 shrink-0">
                                <Calendar className="w-5 h-5" />
                            </div>
                        </div>
                        <div className="mt-3 pt-2.5 border-t border-slate-100 dark:border-slate-800 flex items-center justify-between text-[11px]">
                            <span className="text-slate-500">Authorized Absences</span>
                            <span className="font-semibold text-sky-600 dark:text-sky-400">Excused</span>
                        </div>
                    </div>

                    {/* 5. Pending Leave Approvals */}
                    <div className={`rounded-2xl sm:rounded-3xl border p-5 shadow-xs flex flex-col justify-between transition-colors ${
                        pendingLeaveApprovals > 0
                            ? 'border-purple-200 dark:border-purple-900/60 bg-purple-50/40 dark:bg-purple-950/20'
                            : 'border-slate-100 dark:border-slate-800/80 bg-white dark:bg-slate-900'
                    }`}>
                        <div className="flex items-start justify-between gap-2">
                            <div>
                                <span className="text-[11px] font-bold uppercase tracking-wider text-slate-400">
                                    Pending Time-Off
                                </span>
                                <div className="text-2xl sm:text-3xl font-black text-purple-600 dark:text-purple-400 font-mono mt-1">
                                    {pendingLeaveApprovals}
                                </div>
                            </div>
                            <div className="p-2.5 rounded-2xl bg-purple-100 dark:bg-purple-950/80 text-purple-600 dark:text-purple-400 shrink-0">
                                <Clock className="w-5 h-5" />
                            </div>
                        </div>
                        <div className="mt-3 pt-2.5 border-t border-slate-100 dark:border-slate-800/80 flex items-center justify-between text-[11px]">
                            <span className="text-slate-500">Filings Queue</span>
                            <span className="font-bold text-purple-600 dark:text-purple-400">Requires Action</span>
                        </div>
                    </div>
                </div>

                {/* VISUAL ANALYTICS: 7-DAY ATTENDANCE TREND + DEPARTMENT ALLOCATION */}
                <div className="grid grid-cols-1 lg:grid-cols-12 gap-6 items-stretch">
                    {/* Attendance Trend Chart */}
                    <div className="lg:col-span-7 flex flex-col">
                        <AttendanceTrendChart
                            labels={chartLabels}
                            onTimeData={onTimeData}
                            lateData={lateData}
                            dailyTotals={attendanceDailyTotals}
                        />
                    </div>

                    {/* Department Breakdown Donut */}
                    <div className="lg:col-span-5 flex flex-col">
                        <DonutBreakdownChart
                            title="Workforce by Department"
                            subtitle="Staff headcount distribution"
                            data={formattedDeptData}
                            valueKey="total"
                            labelKey="category"
                            centerLabel="Total Staff"
                            currency=""
                        />
                    </div>
                </div>

                {/* PENDING LEAVE APPROVALS ACTIONABLE QUEUE */}
                {pendingLeaves.length > 0 && (
                    <div className="rounded-2xl sm:rounded-3xl border border-purple-200 dark:border-purple-900/60 bg-gradient-to-br from-purple-50/50 via-white to-purple-50/20 dark:from-purple-950/20 dark:via-slate-900 dark:to-slate-900 p-5 sm:p-7 shadow-xs">
                        <div className="flex flex-col sm:flex-row sm:items-center justify-between gap-3 mb-5">
                            <div>
                                <div className="flex items-center gap-2">
                                    <span className="px-2 py-0.5 rounded-full bg-purple-600 text-white text-[10px] font-black uppercase tracking-wider">
                                        Action Required
                                    </span>
                                    <h3 className="text-base sm:text-lg font-bold text-slate-900 dark:text-white">
                                        Pending Time-Off Filings ({pendingLeaveApprovals})
                                    </h3>
                                </div>
                                <p className="text-xs text-slate-500 dark:text-slate-400 mt-0.5">
                                    Workforce leave applications queued for supervisor and HR authorization.
                                </p>
                            </div>
                            <Link
                                href="/admin/requests/leave-approvals"
                                className="inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-xl bg-purple-600 hover:bg-purple-700 text-white font-bold text-xs shadow-xs transition-colors cursor-pointer"
                            >
                                <span>Leave Approvals Hub</span>
                                <ArrowRight className="w-3.5 h-3.5" />
                            </Link>
                        </div>

                        <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                            {pendingLeaves.map((lv) => {
                                const startDate = lv.start_date ? new Date(lv.start_date) : null;
                                const endDate = lv.end_date ? new Date(lv.end_date) : null;
                                const days = startDate && endDate ? Math.max(1, Math.round((endDate - startDate) / (1000 * 60 * 60 * 24)) + 1) : 1;

                                return (
                                    <div
                                        key={lv.id}
                                        className="p-4 rounded-2xl bg-white dark:bg-slate-800/80 border border-slate-200 dark:border-slate-700/70 shadow-xs flex flex-col justify-between gap-3"
                                    >
                                        <div>
                                            <div className="flex items-center justify-between gap-2 mb-2">
                                                <span className="px-2 py-0.5 rounded-md bg-purple-100 dark:bg-purple-950/70 text-purple-700 dark:text-purple-300 text-[10px] font-bold">
                                                    {days} {days === 1 ? 'Day' : 'Days'} Leave
                                                </span>
                                                <span className="text-[10px] text-slate-400 font-mono">
                                                    #{lv.id}
                                                </span>
                                            </div>
                                            <div className="font-bold text-slate-900 dark:text-white text-sm">
                                                {lv.employee?.first_name} {lv.employee?.last_name}
                                            </div>
                                            <div className="text-[11px] text-slate-400 mt-0.5">
                                                {lv.employee?.department_rel?.name || 'Operations'}
                                            </div>
                                            <p className="text-xs text-slate-600 dark:text-slate-300 mt-2 bg-slate-50 dark:bg-slate-900/60 p-2 rounded-xl italic">
                                                "{lv.reason || 'Personal time off requested'}"
                                            </p>
                                            <div className="text-[11px] text-slate-500 mt-2 flex items-center gap-1">
                                                <Calendar className="w-3.5 h-3.5 text-purple-500" />
                                                <span>
                                                    {startDate ? startDate.toLocaleDateString() : 'N/A'} &rarr; {endDate ? endDate.toLocaleDateString() : 'N/A'}
                                                </span>
                                            </div>
                                        </div>

                                        <div className="pt-3 border-t border-slate-100 dark:border-slate-700 flex items-center justify-between gap-2">
                                            <span className="text-[10px] text-slate-400">
                                                Status: <strong className="text-amber-600">Pending Review</strong>
                                            </span>
                                            <Link
                                                href="/admin/requests/leave-approvals"
                                                className="px-3 py-1 rounded-lg bg-purple-600 hover:bg-purple-700 text-white font-bold text-[11px] transition-colors"
                                            >
                                                Review
                                            </Link>
                                        </div>
                                    </div>
                                );
                            })}
                        </div>
                    </div>
                )}

                {/* RECENT RECRUITS & STAFF DIRECTORY ROSTER */}
                <div className="rounded-2xl sm:rounded-3xl border border-slate-100 dark:border-slate-800/80 bg-white dark:bg-slate-900 p-5 sm:p-7 shadow-xs">
                    <div className="flex flex-col sm:flex-row sm:items-center justify-between gap-3 mb-5">
                        <div>
                            <h3 className="text-base sm:text-lg font-bold text-slate-900 dark:text-white flex items-center gap-2">
                                <Users className="w-5 h-5 text-blue-600" />
                                <span>Recent Recruits &amp; Personnel Directory</span>
                            </h3>
                            <p className="text-xs text-slate-500 dark:text-slate-400 mt-0.5">
                                Onboarded construction engineers, site foremen, plant operators, and craftsmen.
                            </p>
                        </div>

                        <div className="flex items-center gap-3">
                            <div className="relative">
                                <Search className="w-3.5 h-3.5 absolute left-3 top-1/2 -translate-y-1/2 text-slate-400" />
                                <input
                                    type="text"
                                    placeholder="Search staff..."
                                    value={staffSearch}
                                    onChange={(e) => setStaffSearch(e.target.value)}
                                    className="pl-8 pr-3 py-1.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-xs text-slate-900 dark:text-white placeholder-slate-400 focus:outline-none focus:ring-1 focus:ring-blue-500"
                                />
                            </div>
                            <Link
                                href="/hr/employees"
                                className="text-xs font-bold text-blue-600 dark:text-blue-400 hover:underline shrink-0"
                            >
                                Full Directory &rarr;
                            </Link>
                        </div>
                    </div>

                    {filteredEmployees.length === 0 ? (
                        <div className="py-8 text-center text-xs text-slate-400">
                            No employee records found matching your query.
                        </div>
                    ) : (
                        <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                            {filteredEmployees.map((emp) => {
                                const initials = `${emp.first_name ? emp.first_name.charAt(0) : 'E'}${emp.last_name ? emp.last_name.charAt(0) : ''}`;

                                return (
                                    <div
                                        key={emp.id}
                                        className="p-4 rounded-2xl bg-slate-50/70 dark:bg-slate-800/50 border border-slate-200/80 dark:border-slate-700/60 flex items-center justify-between gap-3 hover:border-blue-300 dark:hover:border-blue-700 transition-colors group"
                                    >
                                        <div className="flex items-center gap-3 min-w-0">
                                            <div className="w-11 h-11 rounded-2xl bg-gradient-to-br from-blue-600 to-indigo-700 text-white font-bold flex items-center justify-center text-sm shadow-sm shrink-0">
                                                {initials}
                                            </div>
                                            <div className="min-w-0">
                                                <div className="font-bold text-slate-900 dark:text-white text-xs sm:text-sm truncate">
                                                    {emp.first_name} {emp.last_name}
                                                </div>
                                                <div className="text-[11px] text-slate-500 dark:text-slate-400 truncate">
                                                    {emp.position_rel?.name || emp.position || 'Operations Staff'}
                                                </div>
                                                <div className="flex items-center gap-1.5 mt-1">
                                                    <span className="inline-block px-2 py-0.5 rounded-full text-[10px] font-bold bg-blue-100/70 dark:bg-blue-950/60 text-blue-700 dark:text-blue-300 truncate">
                                                        {emp.department_rel?.name || 'Site Operations'}
                                                    </span>
                                                </div>
                                            </div>
                                        </div>

                                        <Link
                                            href={`/hr/employees/${emp.id}`}
                                            className="p-2 rounded-xl bg-white dark:bg-slate-800 text-slate-400 hover:text-blue-600 dark:hover:text-blue-400 border border-slate-200 dark:border-slate-700 shadow-xs transition-colors shrink-0"
                                            title="View Profile"
                                        >
                                            <ChevronRight className="w-4 h-4" />
                                        </Link>
                                    </div>
                                );
                            })}
                        </div>
                    )}
                </div>

                {/* HR QUICK OPERATIONS TOOLBAR */}
                <div className="rounded-2xl sm:rounded-3xl border border-slate-100 dark:border-slate-800/80 bg-white dark:bg-slate-900 p-5 sm:p-6 shadow-xs">
                    <h4 className="text-xs font-bold text-slate-400 uppercase tracking-wider mb-4">
                        Workforce Shift Tools &amp; Timesheets
                    </h4>
                    <div className="grid grid-cols-2 sm:grid-cols-4 gap-3">
                        <Link
                            href="/hr/attendance"
                            className="p-3.5 rounded-2xl bg-slate-50 dark:bg-slate-800/60 hover:bg-blue-50 dark:hover:bg-blue-950/40 border border-slate-200/60 dark:border-slate-700/50 transition-colors group text-left"
                        >
                            <CalendarCheck className="w-5 h-5 text-blue-600 dark:text-blue-400 mb-2" />
                            <div className="font-bold text-xs text-slate-900 dark:text-white group-hover:text-blue-600 dark:group-hover:text-blue-400">
                                Daily Attendance
                            </div>
                            <div className="text-[10px] text-slate-400 mt-0.5">
                                Site roll call &amp; punches
                            </div>
                        </Link>

                        <Link
                            href="/hr/weekly-sheet"
                            className="p-3.5 rounded-2xl bg-slate-50 dark:bg-slate-800/60 hover:bg-emerald-50 dark:hover:bg-emerald-950/40 border border-slate-200/60 dark:border-slate-700/50 transition-colors group text-left"
                        >
                            <FileSpreadsheet className="w-5 h-5 text-emerald-600 dark:text-emerald-400 mb-2" />
                            <div className="font-bold text-xs text-slate-900 dark:text-white group-hover:text-emerald-600 dark:group-hover:text-emerald-400">
                                Weekly Timesheets
                            </div>
                            <div className="text-[10px] text-slate-400 mt-0.5">
                                Field hours &amp; overtime
                            </div>
                        </Link>

                        <Link
                            href="/hr/monthly-summary"
                            className="p-3.5 rounded-2xl bg-slate-50 dark:bg-slate-800/60 hover:bg-purple-50 dark:hover:bg-purple-950/40 border border-slate-200/60 dark:border-slate-700/50 transition-colors group text-left"
                        >
                            <Calendar className="w-5 h-5 text-purple-600 dark:text-purple-400 mb-2" />
                            <div className="font-bold text-xs text-slate-900 dark:text-white group-hover:text-purple-600 dark:group-hover:text-purple-400">
                                Monthly Summary
                            </div>
                            <div className="text-[10px] text-slate-400 mt-0.5">
                                Payroll readiness
                            </div>
                        </Link>

                        <Link
                            href="/hr/employees"
                            className="p-3.5 rounded-2xl bg-slate-50 dark:bg-slate-800/60 hover:bg-indigo-50 dark:hover:bg-indigo-950/40 border border-slate-200/60 dark:border-slate-700/50 transition-colors group text-left"
                        >
                            <Users className="w-5 h-5 text-indigo-600 dark:text-indigo-400 mb-2" />
                            <div className="font-bold text-xs text-slate-900 dark:text-white group-hover:text-indigo-600 dark:group-hover:text-indigo-400">
                                Employee Roster
                            </div>
                            <div className="text-[10px] text-slate-400 mt-0.5">
                                Directory &amp; contracts
                            </div>
                        </Link>
                    </div>
                </div>
            </div>
        </AuthenticatedLayout>
    );
}
