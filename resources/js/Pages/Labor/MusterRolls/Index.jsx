import { useState } from 'react';
import { Head, Link, router } from '@inertiajs/react';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import {
    FileSpreadsheet,
    Plus,
    Search,
    Filter,
    HardHat,
    DollarSign,
    CheckCircle2,
    Clock,
    Eye,
    Printer,
    Building2,
    Calendar,
    Users,
    Receipt,
} from 'lucide-react';

export default function Index({
    musterRolls,
    projects = [],
    stats = { total_disbursed: 0, pending_approval: 0, paid_rolls: 0, total_workers_active: 0 },
    filters = {},
}) {
    const [search, setSearch] = useState(filters.search || '');
    const [status, setStatus] = useState(filters.status || '');
    const [projectId, setProjectId] = useState(filters.project_id || '');
    const [fromDate, setFromDate] = useState(filters.from_date || '');
    const [toDate, setToDate] = useState(filters.to_date || '');

    const handleFilter = (e) => {
        if (e) e.preventDefault();
        router.get('/labor/muster-rolls', {
            search,
            status,
            project_id: projectId,
            from_date: fromDate,
            to_date: toDate,
        }, {
            preserveState: true,
            preserveScroll: true,
        });
    };

    const rolls = musterRolls?.data || [];

    const getStatusBadge = (st) => {
        switch (st) {
            case 'paid':
                return (
                    <span className="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-semibold bg-emerald-50 dark:bg-emerald-950/60 text-emerald-700 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-800">
                        <CheckCircle2 className="w-3 h-3" /> Wages Disbursed
                    </span>
                );
            case 'approved':
                return (
                    <span className="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-semibold bg-blue-50 dark:bg-blue-950/60 text-blue-700 dark:text-blue-300 border border-blue-200 dark:border-blue-800">
                        <CheckCircle2 className="w-3 h-3" /> PM Approved
                    </span>
                );
            case 'submitted':
                return (
                    <span className="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-semibold bg-amber-50 dark:bg-amber-950/60 text-amber-700 dark:text-amber-300 border border-amber-200 dark:border-amber-800">
                        <Clock className="w-3 h-3" /> Pending Approval
                    </span>
                );
            case 'draft':
                return (
                    <span className="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-semibold bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300">
                        Draft
                    </span>
                );
            default:
                return <span className="px-2.5 py-0.5 rounded-full text-xs font-semibold bg-slate-100 dark:bg-slate-800">{st}</span>;
        }
    };

    return (
        <AuthenticatedLayout title="Site Muster Rolls & Labor WBS" header="Site Operations">
            <div className="space-y-6">
                {/* Header */}
                <div className="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-2 border-b border-slate-200 dark:border-slate-800">
                    <div>
                        <h1 className="text-xl sm:text-2xl font-extrabold tracking-tight text-slate-900 dark:text-white flex items-center gap-2.5">
                            <FileSpreadsheet className="w-7 h-7 text-emerald-600 dark:text-emerald-400" />
                            Site Casual Labor & Daily Muster Rolls
                        </h1>
                        <p className="text-xs sm:text-sm text-slate-500 dark:text-slate-400 mt-0.5">
                            Daily attendance, skill craft wages, overtime calculations, cash payouts, and project expense posting.
                        </p>
                    </div>

                    <div className="flex items-center gap-2.5">
                        <Link
                            href="/labor/workers"
                            className="inline-flex items-center gap-2 px-3.5 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-200 font-semibold text-xs transition-all cursor-pointer"
                        >
                            <HardHat className="w-4 h-4 text-amber-500" />
                            <span>Workers Directory</span>
                        </Link>
                        <Link
                            href="/labor/muster-rolls/create"
                            className="inline-flex items-center justify-center gap-2 px-4 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs shadow-md shadow-emerald-500/20 transition-all cursor-pointer"
                        >
                            <Plus className="w-4 h-4" />
                            <span>Record Daily Muster Roll</span>
                        </Link>
                    </div>
                </div>

                {/* KPI Metrics */}
                <div className="grid grid-cols-2 lg:grid-cols-4 gap-4">
                    <div className="bg-white dark:bg-slate-900 p-4 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-xs">
                        <div className="flex items-center justify-between">
                            <span className="text-xs font-semibold text-slate-500 dark:text-slate-400">Total Labor Disbursed</span>
                            <DollarSign className="w-4 h-4 text-emerald-500" />
                        </div>
                        <div className="mt-2 text-2xl font-extrabold text-slate-900 dark:text-white">
                            {Number(stats.total_disbursed).toLocaleString()} <span className="text-xs font-bold text-slate-400">ETB</span>
                        </div>
                        <span className="text-[11px] text-emerald-600">Posted directly to project expenses</span>
                    </div>

                    <div className="bg-white dark:bg-slate-900 p-4 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-xs">
                        <div className="flex items-center justify-between">
                            <span className="text-xs font-semibold text-slate-500 dark:text-slate-400">Pending PM Approval</span>
                            <Clock className="w-4 h-4 text-amber-500" />
                        </div>
                        <div className="mt-2 text-2xl font-extrabold text-amber-600 dark:text-amber-400">
                            {stats.pending_approval}
                        </div>
                        <span className="text-[11px] text-slate-500">Muster rolls awaiting approval</span>
                    </div>

                    <div className="bg-white dark:bg-slate-900 p-4 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-xs">
                        <div className="flex items-center justify-between">
                            <span className="text-xs font-semibold text-slate-500 dark:text-slate-400">Paid Muster Sheets</span>
                            <CheckCircle2 className="w-4 h-4 text-blue-500" />
                        </div>
                        <div className="mt-2 text-2xl font-extrabold text-blue-600 dark:text-blue-400">
                            {stats.paid_rolls}
                        </div>
                        <span className="text-[11px] text-slate-500">Completed wage disbursements</span>
                    </div>

                    <div className="bg-white dark:bg-slate-900 p-4 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-xs">
                        <div className="flex items-center justify-between">
                            <span className="text-xs font-semibold text-slate-500 dark:text-slate-400">Active Labor Force</span>
                            <Users className="w-4 h-4 text-indigo-500" />
                        </div>
                        <div className="mt-2 text-2xl font-extrabold text-slate-900 dark:text-white">
                            {stats.total_workers_active}
                        </div>
                        <span className="text-[11px] text-slate-500">Artisans & laborers on roster</span>
                    </div>
                </div>

                {/* Filters */}
                <form onSubmit={handleFilter} className="bg-white dark:bg-slate-900 p-4 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-xs">
                    <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-6 gap-3">
                        <div className="lg:col-span-2 relative">
                            <Search className="w-4 h-4 absolute left-3 top-3 text-slate-400" />
                            <input
                                type="text"
                                placeholder="Search by roll #, project, activity..."
                                value={search}
                                onChange={(e) => setSearch(e.target.value)}
                                className="w-full pl-9 pr-3 py-2 bg-slate-50 dark:bg-slate-800/60 border border-slate-200 dark:border-slate-700 rounded-xl text-xs text-slate-900 dark:text-white focus:outline-hidden focus:ring-2 focus:ring-emerald-500"
                            />
                        </div>

                        <div>
                            <select
                                value={projectId}
                                onChange={(e) => setProjectId(e.target.value)}
                                className="w-full px-3 py-2 bg-slate-50 dark:bg-slate-800/60 border border-slate-200 dark:border-slate-700 rounded-xl text-xs text-slate-900 dark:text-white focus:outline-hidden focus:ring-2 focus:ring-emerald-500"
                            >
                                <option value="">All Project Sites</option>
                                {projects.map((p) => (
                                    <option key={p.id} value={p.id}>{p.name}</option>
                                ))}
                            </select>
                        </div>

                        <div>
                            <select
                                value={status}
                                onChange={(e) => setStatus(e.target.value)}
                                className="w-full px-3 py-2 bg-slate-50 dark:bg-slate-800/60 border border-slate-200 dark:border-slate-700 rounded-xl text-xs text-slate-900 dark:text-white focus:outline-hidden focus:ring-2 focus:ring-emerald-500"
                            >
                                <option value="">All Statuses</option>
                                <option value="draft">Draft</option>
                                <option value="submitted">Pending Approval</option>
                                <option value="approved">Approved (Unpaid)</option>
                                <option value="paid">Paid & Disbursed</option>
                            </select>
                        </div>

                        <div>
                            <input
                                type="date"
                                value={fromDate}
                                onChange={(e) => setFromDate(e.target.value)}
                                className="w-full px-3 py-2 bg-slate-50 dark:bg-slate-800/60 border border-slate-200 dark:border-slate-700 rounded-xl text-xs text-slate-900 dark:text-white focus:outline-hidden focus:ring-2 focus:ring-emerald-500"
                                title="From Date"
                            />
                        </div>

                        <div className="flex gap-2">
                            <button
                                type="submit"
                                className="flex-1 inline-flex items-center justify-center gap-1.5 px-3 py-2 bg-slate-900 hover:bg-slate-800 dark:bg-slate-700 dark:hover:bg-slate-600 text-white rounded-xl text-xs font-semibold transition-all cursor-pointer"
                            >
                                <Filter className="w-3.5 h-3.5" />
                                <span>Filter</span>
                            </button>
                            <button
                                type="button"
                                onClick={() => {
                                    setSearch('');
                                    setStatus('');
                                    setProjectId('');
                                    setFromDate('');
                                    setToDate('');
                                    router.get('/labor/muster-rolls');
                                }}
                                className="px-3 py-2 bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-600 dark:text-slate-300 rounded-xl text-xs font-semibold transition-all cursor-pointer"
                            >
                                Reset
                            </button>
                        </div>
                    </div>
                </form>

                {/* Muster Rolls Table */}
                <div className="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-xs overflow-hidden">
                    <div className="overflow-x-auto">
                        <table className="w-full text-left text-xs">
                            <thead className="bg-slate-50 dark:bg-slate-800/60 border-b border-slate-200 dark:border-slate-800 text-slate-500 dark:text-slate-400 font-semibold uppercase tracking-wider">
                                <tr>
                                    <th className="py-3 px-4">Muster Roll #</th>
                                    <th className="py-3 px-4">Work Date</th>
                                    <th className="py-3 px-4">Project Site</th>
                                    <th className="py-3 px-4">Activity / Description</th>
                                    <th className="py-3 px-4 text-center">Labor Count</th>
                                    <th className="py-3 px-4 text-right">Net Wages</th>
                                    <th className="py-3 px-4">Status</th>
                                    <th className="py-3 px-4 text-right">Actions</th>
                                </tr>
                            </thead>
                            <tbody className="divide-y divide-slate-200 dark:divide-slate-800">
                                {rolls.length === 0 ? (
                                    <tr>
                                        <td colSpan="8" className="py-8 text-center text-slate-500 dark:text-slate-400">
                                            No site muster rolls recorded. Click "Record Daily Muster Roll" to create one.
                                        </td>
                                    </tr>
                                ) : (
                                    rolls.map((roll) => (
                                        <tr key={roll.id} className="hover:bg-slate-50/80 dark:hover:bg-slate-800/40 transition-colors">
                                            <td className="py-3 px-4 font-mono font-bold text-emerald-600 dark:text-emerald-400">
                                                <Link href={`/labor/muster-rolls/${roll.id}`} className="hover:underline">
                                                    {roll.muster_roll_no}
                                                </Link>
                                            </td>
                                            <td className="py-3 px-4 text-slate-700 dark:text-slate-300 font-medium">
                                                <span className="flex items-center gap-1.5">
                                                    <Calendar className="w-3.5 h-3.5 text-slate-400" />
                                                    {new Date(roll.date).toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' })}
                                                </span>
                                            </td>
                                            <td className="py-3 px-4 font-medium text-slate-900 dark:text-white">
                                                <span className="flex items-center gap-1.5">
                                                    <Building2 className="w-3.5 h-3.5 text-slate-400" />
                                                    {roll.project?.name || 'Project Site'}
                                                </span>
                                            </td>
                                            <td className="py-3 px-4 text-slate-600 dark:text-slate-300 max-w-xs truncate">
                                                {roll.title || 'Civil Works & Site Operations'}
                                            </td>
                                            <td className="py-3 px-4 text-center">
                                                <span className="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-bold bg-slate-100 dark:bg-slate-800 text-slate-800 dark:text-slate-200">
                                                    <Users className="w-3 h-3 text-amber-500" />
                                                    {roll.total_workers} workers
                                                </span>
                                            </td>
                                            <td className="py-3 px-4 text-right font-extrabold text-slate-900 dark:text-white">
                                                {Number(roll.total_net_amount).toLocaleString()} ETB
                                            </td>
                                            <td className="py-3 px-4">
                                                <div className="flex flex-col gap-1">
                                                    {getStatusBadge(roll.status)}
                                                    {roll.expense_id && (
                                                        <span className="inline-flex items-center gap-1 text-[10px] text-emerald-600 dark:text-emerald-400 font-mono">
                                                            <Receipt className="w-2.5 h-2.5" /> Expense #{roll.expense?.reference_no || roll.muster_roll_no}
                                                        </span>
                                                    )}
                                                </div>
                                            </td>
                                            <td className="py-3 px-4 text-right">
                                                <div className="inline-flex items-center gap-1">
                                                    <Link
                                                        href={`/labor/muster-rolls/${roll.id}`}
                                                        className="p-1.5 text-slate-500 hover:text-emerald-600 dark:hover:text-emerald-400 hover:bg-slate-100 dark:hover:bg-slate-800 rounded-lg transition-colors cursor-pointer"
                                                        title="View Details"
                                                    >
                                                        <Eye className="w-3.5 h-3.5" />
                                                    </Link>
                                                    <a
                                                        href={`/labor/muster-rolls/${roll.id}/print`}
                                                        target="_blank"
                                                        rel="noopener noreferrer"
                                                        className="p-1.5 text-slate-500 hover:text-blue-600 dark:hover:text-blue-400 hover:bg-slate-100 dark:hover:bg-slate-800 rounded-lg transition-colors cursor-pointer"
                                                        title="Print Sheet"
                                                    >
                                                        <Printer className="w-3.5 h-3.5" />
                                                    </a>
                                                </div>
                                            </td>
                                        </tr>
                                    ))
                                )}
                            </tbody>
                        </table>
                    </div>

                    {/* Pagination */}
                    {musterRolls?.links && musterRolls.links.length > 3 && (
                        <div className="flex items-center justify-between px-4 py-3 border-t border-slate-200 dark:border-slate-800 text-xs">
                            <span className="text-slate-500">
                                Showing {musterRolls.from || 0} to {musterRolls.to || 0} of {musterRolls.total} sheets
                            </span>
                            <div className="flex gap-1">
                                {musterRolls.links.map((link, idx) => (
                                    <Link
                                        key={idx}
                                        href={link.url || '#'}
                                        dangerouslySetInnerHTML={{ __html: link.label }}
                                        className={`px-3 py-1 rounded-lg border ${
                                            link.active
                                                ? 'bg-emerald-600 border-emerald-600 text-white font-bold'
                                                : 'bg-white dark:bg-slate-800 border-slate-200 dark:border-slate-700 text-slate-700 dark:text-slate-300'
                                        } ${!link.url ? 'opacity-50 cursor-not-allowed' : 'hover:bg-slate-100 dark:hover:bg-slate-700'}`}
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
