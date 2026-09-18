import { Head, Link } from '@inertiajs/react';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import {
    Users,
    UserCheck,
    Calendar,
    Clock,
    UserPlus,
    CalendarCheck,
    Building2,
    ArrowRight,
} from 'lucide-react';

export default function HrDashboard({
    employeeCount = 0,
    activeEmployees = 0,
    onLeaveTodayCount = 0,
    pendingLeaveApprovals = 0,
    latestEmployees = [],
    departmentStats = [],
}) {
    return (
        <AuthenticatedLayout title="HR Dashboard" header="Human Resources Hub">
            <div className="space-y-6">
                {/* Hero Header */}
                <div className="rounded-2xl bg-gradient-to-r from-blue-900 via-indigo-900 to-slate-900 p-6 sm:p-7 text-white shadow-md relative overflow-hidden">
                    <div className="relative z-10 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                        <div>
                            <div className="flex items-center gap-2 mb-1">
                                <span className="px-2.5 py-0.5 rounded-full bg-blue-500/20 text-blue-300 text-[10px] font-bold tracking-wider uppercase border border-blue-500/30">
                                    Personnel & Culture
                                </span>
                            </div>
                            <h1 className="text-2xl sm:text-3xl font-black tracking-tight">
                                Human Resource Management
                            </h1>
                            <p className="text-xs sm:text-sm text-slate-300 mt-1">
                                Workforce directory, daily shift attendance sheets, and leave authorizations.
                            </p>
                        </div>

                        <div className="flex items-center gap-2 shrink-0">
                            <Link
                                href="/hr/employees/create"
                                className="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl bg-white text-blue-900 font-bold text-xs shadow-sm hover:bg-slate-100 transition-colors"
                            >
                                <UserPlus className="w-4 h-4" />
                                <span>Add Employee</span>
                            </Link>
                            <Link
                                href="/hr/attendance"
                                className="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl bg-white/10 text-white font-bold text-xs border border-white/20 hover:bg-white/20 transition-colors"
                            >
                                <CalendarCheck className="w-4 h-4 text-emerald-400" />
                                <span>Attendance</span>
                            </Link>
                        </div>
                    </div>
                </div>

                {/* KPI Cards */}
                <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                    <div className="rounded-2xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 p-5 shadow-xs flex items-center justify-between">
                        <div>
                            <div className="text-[11px] font-bold uppercase tracking-wider text-slate-400">Total Workforce</div>
                            <div className="text-3xl font-black text-blue-900 dark:text-blue-400 font-mono mt-1">{employeeCount}</div>
                            <div className="text-[11px] text-slate-500 mt-0.5">Registered Personnel</div>
                        </div>
                        <div className="p-3 rounded-xl bg-blue-50 dark:bg-blue-950/60 text-blue-900 dark:text-blue-400">
                            <Users className="w-6 h-6" />
                        </div>
                    </div>

                    <div className="rounded-2xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 p-5 shadow-xs flex items-center justify-between">
                        <div>
                            <div className="text-[11px] font-bold uppercase tracking-wider text-slate-400">Active Status</div>
                            <div className="text-3xl font-black text-emerald-600 dark:text-emerald-400 font-mono mt-1">{activeEmployees}</div>
                            <div className="text-[11px] text-slate-500 mt-0.5">Operational Staff</div>
                        </div>
                        <div className="p-3 rounded-xl bg-emerald-50 dark:bg-emerald-950/60 text-emerald-600 dark:text-emerald-400">
                            <UserCheck className="w-6 h-6" />
                        </div>
                    </div>

                    <div className="rounded-2xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 p-5 shadow-xs flex items-center justify-between">
                        <div>
                            <div className="text-[11px] font-bold uppercase tracking-wider text-slate-400">On Leave Today</div>
                            <div className="text-3xl font-black text-sky-600 dark:text-sky-400 font-mono mt-1">{onLeaveTodayCount}</div>
                            <div className="text-[11px] text-slate-500 mt-0.5">Authorized Absences</div>
                        </div>
                        <div className="p-3 rounded-xl bg-sky-50 dark:bg-sky-950/60 text-sky-600 dark:text-sky-400">
                            <Calendar className="w-6 h-6" />
                        </div>
                    </div>

                    <div className="rounded-2xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 p-5 shadow-xs flex items-center justify-between">
                        <div>
                            <div className="text-[11px] font-bold uppercase tracking-wider text-slate-400">Pending Filings</div>
                            <div className="text-3xl font-black text-amber-600 dark:text-amber-400 font-mono mt-1">{pendingLeaveApprovals}</div>
                            <div className="text-[11px] text-slate-500 mt-0.5">Review Queue</div>
                        </div>
                        <div className="p-3 rounded-xl bg-amber-50 dark:bg-amber-950/60 text-amber-600 dark:text-amber-400">
                            <Clock className="w-6 h-6" />
                        </div>
                    </div>
                </div>

                {/* Content Grid */}
                <div className="grid grid-cols-1 lg:grid-cols-3 gap-6">
                    {/* Latest Employees Table */}
                    <div className="lg:col-span-2 rounded-2xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 p-5 shadow-xs">
                        <div className="flex items-center justify-between mb-4">
                            <h3 className="text-sm font-bold text-slate-900 dark:text-white">Recent Recruits & Staff</h3>
                            <Link href="/hr/employees" className="text-xs font-bold text-blue-900 dark:text-blue-400 hover:underline">
                                View Directory →
                            </Link>
                        </div>

                        {latestEmployees.length === 0 ? (
                            <p className="text-xs text-slate-400 py-6 text-center">No employee records found.</p>
                        ) : (
                            <div className="divide-y divide-slate-100 dark:divide-slate-800">
                                {latestEmployees.map((emp) => (
                                    <div key={emp.id} className="py-3 flex items-center justify-between gap-3 text-xs">
                                        <div className="flex items-center gap-3">
                                            <div className="h-8 w-8 rounded-lg bg-slate-100 dark:bg-slate-800 flex items-center justify-center font-bold text-slate-700 dark:text-slate-300 text-xs">
                                                {emp.first_name ? emp.first_name.charAt(0) : 'E'}
                                            </div>
                                            <div>
                                                <div className="font-bold text-slate-900 dark:text-white">
                                                    {emp.first_name} {emp.last_name}
                                                </div>
                                                <div className="text-[11px] text-slate-400">
                                                    ID: #{emp.id} · {emp.position_rel?.name || emp.position || 'Staff'}
                                                </div>
                                            </div>
                                        </div>
                                        <div className="text-right">
                                            <span className="inline-block px-2 py-0.5 rounded-full text-[10px] font-bold bg-blue-50 dark:bg-blue-950/40 text-blue-800 dark:text-blue-300">
                                                {emp.department_rel?.name || 'Operations'}
                                            </span>
                                        </div>
                                    </div>
                                ))}
                            </div>
                        )}
                    </div>

                    {/* Department Distribution */}
                    <div className="rounded-2xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 p-5 shadow-xs">
                        <h3 className="text-sm font-bold text-slate-900 dark:text-white mb-4 flex items-center gap-2">
                            <Building2 className="w-4 h-4 text-blue-900 dark:text-blue-400" />
                            <span>Department Breakdown</span>
                        </h3>

                        <div className="space-y-3">
                            {departmentStats.map((dept, idx) => (
                                <div key={idx} className="flex items-center justify-between text-xs py-1.5 border-b border-slate-100 dark:border-slate-800/80 last:border-0">
                                    <span className="font-medium text-slate-700 dark:text-slate-300">{dept.name}</span>
                                    <span className="font-mono font-bold text-slate-900 dark:text-white">{dept.total} Staff</span>
                                </div>
                            ))}
                        </div>
                    </div>
                </div>
            </div>
        </AuthenticatedLayout>
    );
}
