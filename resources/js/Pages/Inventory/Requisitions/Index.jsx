import { useState } from 'react';
import { Head, Link, router } from '@inertiajs/react';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import {
    ClipboardList,
    Plus,
    Search,
    Filter,
    CheckCircle2,
    Clock,
    XCircle,
    Eye,
    ShoppingCart,
    Building2,
    AlertCircle,
} from 'lucide-react';

export default function Index({
    requisitions,
    stats = { total: 0, pending: 0, approved: 0, completed: 0 },
    projects = [],
    filters = {},
}) {
    const [search, setSearch] = useState(filters.q || '');
    const [status, setStatus] = useState(filters.status || '');
    const [priority, setPriority] = useState(filters.priority || '');
    const [projectId, setProjectId] = useState(filters.project_id || '');

    const handleFilter = (e) => {
        if (e) e.preventDefault();
        router.get('/inventory/requisitions', {
            q: search,
            status,
            priority,
            project_id: projectId,
        }, {
            preserveState: true,
            preserveScroll: true,
        });
    };

    const requisitionList = requisitions?.data || [];

    const getStatusBadge = (st) => {
        switch (st) {
            case 'approved':
                return <span className="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-semibold bg-emerald-50 dark:bg-emerald-950/60 text-emerald-700 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-800"><CheckCircle2 className="w-3 h-3" /> Approved</span>;
            case 'pending':
                return <span className="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-semibold bg-amber-50 dark:bg-amber-950/60 text-amber-700 dark:text-amber-300 border border-amber-200 dark:border-amber-800"><Clock className="w-3 h-3" /> Pending</span>;
            case 'ordered':
                return <span className="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-semibold bg-blue-50 dark:bg-blue-950/60 text-blue-700 dark:text-blue-300 border border-blue-200 dark:border-blue-800"><ShoppingCart className="w-3 h-3" /> PO Issued</span>;
            case 'completed':
                return <span className="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-semibold bg-purple-50 dark:bg-purple-950/60 text-purple-700 dark:text-purple-300 border border-purple-200 dark:border-purple-800"><CheckCircle2 className="w-3 h-3" /> Completed</span>;
            case 'rejected':
                return <span className="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-semibold bg-rose-50 dark:bg-rose-950/60 text-rose-700 dark:text-rose-300 border border-rose-200 dark:border-rose-800"><XCircle className="w-3 h-3" /> Rejected</span>;
            default:
                return <span className="px-2.5 py-0.5 rounded-full text-xs font-semibold bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300">{st}</span>;
        }
    };

    const getPriorityBadge = (p) => {
        switch (p) {
            case 'urgent':
                return <span className="px-2 py-0.5 text-[10px] font-extrabold uppercase tracking-wider rounded bg-rose-600 text-white animate-pulse">Urgent</span>;
            case 'high':
                return <span className="px-2 py-0.5 text-[10px] font-bold uppercase tracking-wider rounded bg-amber-500/20 text-amber-600 dark:text-amber-400 border border-amber-500/30">High</span>;
            case 'medium':
                return <span className="px-2 py-0.5 text-[10px] font-medium uppercase tracking-wider rounded bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-400">Medium</span>;
            default:
                return <span className="px-2 py-0.5 text-[10px] font-medium uppercase tracking-wider rounded bg-slate-100 dark:bg-slate-800 text-slate-500">Low</span>;
        }
    };

    return (
        <AuthenticatedLayout title="Purchase Requisitions" header="Material Procurement">
            <div className="space-y-6">
                {/* Header */}
                <div className="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-2 border-b border-slate-200 dark:border-slate-800">
                    <div>
                        <h1 className="text-xl sm:text-2xl font-extrabold tracking-tight text-slate-900 dark:text-white flex items-center gap-2.5">
                            <ClipboardList className="w-7 h-7 text-blue-600 dark:text-blue-400" />
                            Site Material Requisitions (PR)
                        </h1>
                        <p className="text-xs sm:text-sm text-slate-500 dark:text-slate-400 mt-0.5">
                            Field requisitions submitted by site engineers and supervisors for material procurement.
                        </p>
                    </div>

                    <Link
                        href="/inventory/requisitions/create"
                        className="inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs shadow-md shadow-blue-500/20 transition-all cursor-pointer"
                    >
                        <Plus className="w-4 h-4" />
                        <span>New Requisition</span>
                    </Link>
                </div>

                {/* Metrics */}
                <div className="grid grid-cols-2 lg:grid-cols-4 gap-4">
                    <div className="rounded-2xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 p-4">
                        <div className="text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Total Requisitions</div>
                        <div className="text-2xl font-black text-slate-900 dark:text-white mt-1">{stats.total}</div>
                    </div>
                    <div className="rounded-2xl border border-amber-200 dark:border-amber-900/40 bg-amber-50/50 dark:bg-amber-950/20 p-4">
                        <div className="text-xs font-bold text-amber-600 dark:text-amber-400 uppercase tracking-wider flex items-center gap-1.5">
                            <Clock className="w-3.5 h-3.5" /> Pending Approval
                        </div>
                        <div className="text-2xl font-black text-amber-700 dark:text-amber-300 mt-1">{stats.pending}</div>
                    </div>
                    <div className="rounded-2xl border border-emerald-200 dark:border-emerald-900/40 bg-emerald-50/50 dark:bg-emerald-950/20 p-4">
                        <div className="text-xs font-bold text-emerald-600 dark:text-emerald-400 uppercase tracking-wider flex items-center gap-1.5">
                            <CheckCircle2 className="w-3.5 h-3.5" /> Approved Requisitions
                        </div>
                        <div className="text-2xl font-black text-emerald-700 dark:text-emerald-300 mt-1">{stats.approved}</div>
                    </div>
                    <div className="rounded-2xl border border-purple-200 dark:border-purple-900/40 bg-purple-50/50 dark:bg-purple-950/20 p-4">
                        <div className="text-xs font-bold text-purple-600 dark:text-purple-400 uppercase tracking-wider">Completed / Received</div>
                        <div className="text-2xl font-black text-purple-700 dark:text-purple-300 mt-1">{stats.completed}</div>
                    </div>
                </div>

                {/* Filters */}
                <div className="rounded-2xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 p-4">
                    <form onSubmit={handleFilter} className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-3">
                        <div className="relative">
                            <Search className="w-4 h-4 absolute left-3 top-3 text-slate-400" />
                            <input
                                type="text"
                                value={search}
                                onChange={(e) => setSearch(e.target.value)}
                                placeholder="Search PR #, purpose..."
                                className="w-full pl-9 pr-3 py-2 text-xs rounded-xl border border-slate-200 dark:border-slate-800 bg-slate-50 dark:bg-slate-950 text-slate-800 dark:text-slate-100 focus:outline-none focus:ring-2 focus:ring-blue-500"
                            />
                        </div>

                        <div>
                            <select
                                value={projectId}
                                onChange={(e) => setProjectId(e.target.value)}
                                className="w-full px-3 py-2 text-xs rounded-xl border border-slate-200 dark:border-slate-800 bg-slate-50 dark:bg-slate-950 text-slate-800 dark:text-slate-100 focus:outline-none focus:ring-2 focus:ring-blue-500"
                            >
                                <option value="">All Projects</option>
                                {projects.map((p) => (
                                    <option key={p.id} value={p.id}>{p.name}</option>
                                ))}
                            </select>
                        </div>

                        <div>
                            <select
                                value={status}
                                onChange={(e) => setStatus(e.target.value)}
                                className="w-full px-3 py-2 text-xs rounded-xl border border-slate-200 dark:border-slate-800 bg-slate-50 dark:bg-slate-950 text-slate-800 dark:text-slate-100 focus:outline-none focus:ring-2 focus:ring-blue-500"
                            >
                                <option value="">All Statuses</option>
                                <option value="pending">Pending</option>
                                <option value="approved">Approved</option>
                                <option value="ordered">PO Issued</option>
                                <option value="completed">Completed</option>
                                <option value="rejected">Rejected</option>
                            </select>
                        </div>

                        <div>
                            <select
                                value={priority}
                                onChange={(e) => setPriority(e.target.value)}
                                className="w-full px-3 py-2 text-xs rounded-xl border border-slate-200 dark:border-slate-800 bg-slate-50 dark:bg-slate-950 text-slate-800 dark:text-slate-100 focus:outline-none focus:ring-2 focus:ring-blue-500"
                            >
                                <option value="">All Priorities</option>
                                <option value="urgent">Urgent</option>
                                <option value="high">High</option>
                                <option value="medium">Medium</option>
                                <option value="low">Low</option>
                            </select>
                        </div>

                        <div className="flex gap-2">
                            <button
                                type="submit"
                                className="flex-1 inline-flex items-center justify-center gap-1.5 px-3 py-2 bg-slate-900 hover:bg-slate-800 dark:bg-slate-800 dark:hover:bg-slate-700 text-white rounded-xl text-xs font-bold transition-colors cursor-pointer"
                            >
                                <Filter className="w-3.5 h-3.5" />
                                <span>Filter</span>
                            </button>
                            <button
                                type="button"
                                onClick={() => {
                                    setSearch('');
                                    setStatus('');
                                    setPriority('');
                                    setProjectId('');
                                    router.get('/inventory/requisitions');
                                }}
                                className="px-3 py-2 border border-slate-200 dark:border-slate-800 text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800 rounded-xl text-xs font-bold transition-colors cursor-pointer"
                            >
                                Reset
                            </button>
                        </div>
                    </form>
                </div>

                {/* Table */}
                <div className="rounded-2xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 overflow-hidden shadow-xs">
                    <div className="overflow-x-auto">
                        <table className="w-full text-left border-collapse text-xs">
                            <thead>
                                <tr className="border-b border-slate-200 dark:border-slate-800 bg-slate-50/75 dark:bg-slate-800/40 text-slate-500 dark:text-slate-400 font-bold uppercase tracking-wider text-[11px]">
                                    <th className="py-3.5 px-4">Requisition #</th>
                                    <th className="py-3.5 px-4">Project Site</th>
                                    <th className="py-3.5 px-4">Items</th>
                                    <th className="py-3.5 px-4">Required Date</th>
                                    <th className="py-3.5 px-4">Priority</th>
                                    <th className="py-3.5 px-4">Status</th>
                                    <th className="py-3.5 px-4">Requested By</th>
                                    <th className="py-3.5 px-4 text-right">Actions</th>
                                </tr>
                            </thead>
                            <tbody className="divide-y divide-slate-100 dark:divide-slate-800/60">
                                {requisitionList.length === 0 ? (
                                    <tr>
                                        <td colSpan={8} className="py-12 text-center text-slate-500 dark:text-slate-400">
                                            <ClipboardList className="w-10 h-10 mx-auto text-slate-300 dark:text-slate-600 mb-2" />
                                            <p className="font-semibold">No purchase requisitions found.</p>
                                            <p className="text-xs mt-0.5">Submit your first material requisition to start the procurement cycle.</p>
                                        </td>
                                    </tr>
                                ) : (
                                    requisitionList.map((pr) => (
                                        <tr key={pr.id} className="hover:bg-slate-50/50 dark:hover:bg-slate-800/30 transition-colors">
                                            <td className="py-3.5 px-4 font-bold text-blue-600 dark:text-blue-400">
                                                <Link href={`/inventory/requisitions/${pr.id}`} className="hover:underline">
                                                    {pr.requisition_no}
                                                </Link>
                                                {pr.purpose && (
                                                    <div className="text-[11px] font-normal text-slate-500 dark:text-slate-400 truncate max-w-xs">
                                                        {pr.purpose}
                                                    </div>
                                                )}
                                            </td>
                                            <td className="py-3.5 px-4 text-slate-800 dark:text-slate-200">
                                                <div className="font-semibold">{pr.project ? pr.project.name : 'Central Warehouse'}</div>
                                                <div className="text-[10px] text-slate-400">{pr.project?.location}</div>
                                            </td>
                                            <td className="py-3.5 px-4">
                                                <span className="font-bold text-slate-900 dark:text-white">{pr.items?.length || 0}</span>
                                                <span className="text-[11px] text-slate-400 ml-1">item(s)</span>
                                            </td>
                                            <td className="py-3.5 px-4 text-slate-600 dark:text-slate-300 font-medium">
                                                {pr.required_date ? new Date(pr.required_date).toLocaleDateString() : 'N/A'}
                                            </td>
                                            <td className="py-3.5 px-4">
                                                {getPriorityBadge(pr.priority)}
                                            </td>
                                            <td className="py-3.5 px-4">
                                                {getStatusBadge(pr.status)}
                                            </td>
                                            <td className="py-3.5 px-4 text-slate-600 dark:text-slate-300">
                                                {pr.requester?.name || 'Staff'}
                                            </td>
                                            <td className="py-3.5 px-4 text-right">
                                                <div className="inline-flex items-center gap-1.5">
                                                    <Link
                                                        href={`/inventory/requisitions/${pr.id}`}
                                                        className="p-1.5 rounded-lg border border-slate-200 dark:border-slate-800 text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors"
                                                        title="View Details"
                                                    >
                                                        <Eye className="w-3.5 h-3.5" />
                                                    </Link>

                                                    {pr.status === 'approved' && (
                                                        <Link
                                                            href={`/inventory/purchase-orders/create?pr_id=${pr.id}`}
                                                            className="inline-flex items-center gap-1 px-2.5 py-1 bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg text-xs font-bold transition-colors cursor-pointer"
                                                            title="Generate PO"
                                                        >
                                                            <ShoppingCart className="w-3.5 h-3.5" />
                                                            <span>Issue PO</span>
                                                        </Link>
                                                    )}
                                                </div>
                                            </td>
                                        </tr>
                                    ))
                                )}
                            </tbody>
                        </table>
                    </div>

                    {/* Pagination */}
                    {requisitions?.links && requisitions.links.length > 3 && (
                        <div className="p-4 border-t border-slate-100 dark:border-slate-800 flex items-center justify-between">
                            <div className="text-xs text-slate-500">
                                Showing {requisitions.from || 0} to {requisitions.to || 0} of {requisitions.total} requisitions
                            </div>
                            <div className="flex gap-1">
                                {requisitions.links.map((link, idx) => (
                                    <Link
                                        key={idx}
                                        href={link.url || '#'}
                                        dangerouslySetInnerHTML={{ __html: link.label }}
                                        className={`px-3 py-1 text-xs rounded-lg font-semibold transition-colors ${
                                            link.active
                                                ? 'bg-blue-600 text-white'
                                                : link.url
                                                ? 'border border-slate-200 dark:border-slate-800 text-slate-700 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800'
                                                : 'text-slate-400 cursor-not-allowed'
                                        }`}
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
