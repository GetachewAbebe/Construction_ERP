import { useState } from 'react';
import { Head, Link, router } from '@inertiajs/react';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import {
    Wrench,
    Plus,
    Search,
    Filter,
    Truck,
    AlertTriangle,
    Clock,
    DollarSign,
    CheckCircle2,
    Eye,
    Printer,
    Building2,
    Calendar,
    Gauge,
    Flame,
    FileText,
} from 'lucide-react';

export default function Index({
    workOrders,
    equipmentList = [],
    projects = [],
    stats = {
        active_breakdowns: 0,
        pending_orders: 0,
        total_completed: 0,
        total_maintenance_costs: 0,
        total_downtime_hours: 0,
        service_due_machines: 0,
    },
    filters = {},
}) {
    const [search, setSearch] = useState(filters.search || '');
    const [equipmentId, setEquipmentId] = useState(filters.equipment_id || '');
    const [projectId, setProjectId] = useState(filters.project_id || '');
    const [status, setStatus] = useState(filters.status || '');
    const [orderType, setOrderType] = useState(filters.order_type || '');
    const [priority, setPriority] = useState(filters.priority || '');

    const handleFilter = (e) => {
        if (e) e.preventDefault();
        router.get('/equipment/maintenance', {
            search,
            equipment_id: equipmentId,
            project_id: projectId,
            status,
            order_type: orderType,
            priority,
        }, {
            preserveState: true,
            preserveScroll: true,
        });
    };

    const orders = workOrders?.data || [];

    const getStatusBadge = (st) => {
        switch (st) {
            case 'completed':
                return (
                    <span className="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-semibold bg-emerald-50 dark:bg-emerald-950/60 text-emerald-700 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-800">
                        <CheckCircle2 className="w-3 h-3" /> Repaired & Closed
                    </span>
                );
            case 'in_progress':
                return (
                    <span className="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-semibold bg-blue-50 dark:bg-blue-950/60 text-blue-700 dark:text-blue-300 border border-blue-200 dark:border-blue-800">
                        <Wrench className="w-3 h-3" /> In Progress
                    </span>
                );
            case 'pending':
                return (
                    <span className="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-semibold bg-amber-50 dark:bg-amber-950/60 text-amber-700 dark:text-amber-300 border border-amber-200 dark:border-amber-800">
                        <Clock className="w-3 h-3" /> Queued / Pending
                    </span>
                );
            default:
                return <span className="px-2.5 py-0.5 rounded-full text-xs font-semibold bg-slate-100 dark:bg-slate-800">{st}</span>;
        }
    };

    const getTypeBadge = (type) => {
        switch (type) {
            case 'breakdown':
                return (
                    <span className="inline-flex items-center gap-1 px-2 py-0.5 rounded-md text-[11px] font-bold bg-rose-50 dark:bg-rose-950/60 text-rose-700 dark:text-rose-300 border border-rose-200 dark:border-rose-800">
                        <AlertTriangle className="w-3 h-3 text-rose-500" /> Breakdown
                    </span>
                );
            case 'preventive':
                return (
                    <span className="inline-flex items-center gap-1 px-2 py-0.5 rounded-md text-[11px] font-bold bg-sky-50 dark:bg-sky-950/60 text-sky-700 dark:text-sky-300 border border-sky-200 dark:border-sky-800">
                        <Wrench className="w-3 h-3 text-sky-500" /> Preventive
                    </span>
                );
            default:
                return <span className="px-2 py-0.5 rounded-md text-[11px] font-bold bg-slate-100 dark:bg-slate-800">{type}</span>;
        }
    };

    return (
        <AuthenticatedLayout title="Fleet & Equipment Maintenance" header="Fleet Management">
            <div className="space-y-6">
                {/* Header */}
                <div className="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-2 border-b border-slate-200 dark:border-slate-800">
                    <div>
                        <h1 className="text-xl sm:text-2xl font-extrabold tracking-tight text-slate-900 dark:text-white flex items-center gap-2.5">
                            <Wrench className="w-7 h-7 text-amber-500" />
                            Fleet & Heavy Equipment Maintenance
                        </h1>
                        <p className="text-xs sm:text-sm text-slate-500 dark:text-slate-400 mt-0.5">
                            Preventive service schedules, field breakdown work orders, spare parts consumption, and downtime tracking.
                        </p>
                    </div>

                    <div className="flex items-center gap-2.5">
                        <Link
                            href="/equipment"
                            className="inline-flex items-center gap-2 px-3.5 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-200 font-semibold text-xs transition-all cursor-pointer"
                        >
                            <Truck className="w-4 h-4 text-blue-500" />
                            <span>Fleet Registry</span>
                        </Link>
                        <Link
                            href="/equipment/maintenance/create"
                            className="inline-flex items-center justify-center gap-2 px-4 py-2 rounded-xl bg-amber-500 hover:bg-amber-600 text-slate-950 font-bold text-xs shadow-md shadow-amber-500/20 transition-all cursor-pointer"
                        >
                            <Plus className="w-4 h-4" />
                            <span>Open Maintenance Work Order</span>
                        </Link>
                    </div>
                </div>

                {/* KPI Metrics */}
                <div className="grid grid-cols-2 lg:grid-cols-4 gap-4">
                    <div className="bg-white dark:bg-slate-900 p-4 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-xs">
                        <div className="flex items-center justify-between">
                            <span className="text-xs font-semibold text-slate-500 dark:text-slate-400">Active Breakdowns</span>
                            <AlertTriangle className="w-4 h-4 text-rose-500" />
                        </div>
                        <div className="mt-2 text-2xl font-extrabold text-rose-600 dark:text-rose-400">
                            {stats.active_breakdowns}
                        </div>
                        <span className="text-[11px] text-rose-500">Unresolved breakdown stoppage</span>
                    </div>

                    <div className="bg-white dark:bg-slate-900 p-4 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-xs">
                        <div className="flex items-center justify-between">
                            <span className="text-xs font-semibold text-slate-500 dark:text-slate-400">Service Due Machines</span>
                            <Gauge className="w-4 h-4 text-amber-500" />
                        </div>
                        <div className="mt-2 text-2xl font-extrabold text-amber-600 dark:text-amber-400">
                            {stats.service_due_machines}
                        </div>
                        <span className="text-[11px] text-slate-500">At or near service hour limits</span>
                    </div>

                    <div className="bg-white dark:bg-slate-900 p-4 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-xs">
                        <div className="flex items-center justify-between">
                            <span className="text-xs font-semibold text-slate-500 dark:text-slate-400">Total Maintenance Costs</span>
                            <DollarSign className="w-4 h-4 text-emerald-500" />
                        </div>
                        <div className="mt-2 text-2xl font-extrabold text-slate-900 dark:text-white">
                            {Number(stats.total_maintenance_costs).toLocaleString()} <span className="text-xs font-bold text-slate-400">ETB</span>
                        </div>
                        <span className="text-[11px] text-slate-500">Parts, fluids & mechanic labor</span>
                    </div>

                    <div className="bg-white dark:bg-slate-900 p-4 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-xs">
                        <div className="flex items-center justify-between">
                            <span className="text-xs font-semibold text-slate-500 dark:text-slate-400">Fleet Downtime Lost</span>
                            <Clock className="w-4 h-4 text-indigo-500" />
                        </div>
                        <div className="mt-2 text-2xl font-extrabold text-slate-900 dark:text-white">
                            {Number(stats.total_downtime_hours).toLocaleString()} <span className="text-xs font-bold text-slate-400">Hours</span>
                        </div>
                        <span className="text-[11px] text-slate-500">Total idle breakdown duration</span>
                    </div>
                </div>

                {/* Filters */}
                <form onSubmit={handleFilter} className="bg-white dark:bg-slate-900 p-4 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-xs">
                    <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-6 gap-3">
                        <div className="lg:col-span-2 relative">
                            <Search className="w-4 h-4 absolute left-3 top-3 text-slate-400" />
                            <input
                                type="text"
                                placeholder="Search by WO #, title, machine..."
                                value={search}
                                onChange={(e) => setSearch(e.target.value)}
                                className="w-full pl-9 pr-3 py-2 bg-slate-50 dark:bg-slate-800/60 border border-slate-200 dark:border-slate-700 rounded-xl text-xs text-slate-900 dark:text-white focus:outline-hidden focus:ring-2 focus:ring-amber-500"
                            />
                        </div>

                        <div>
                            <select
                                value={equipmentId}
                                onChange={(e) => setEquipmentId(e.target.value)}
                                className="w-full px-3 py-2 bg-slate-50 dark:bg-slate-800/60 border border-slate-200 dark:border-slate-700 rounded-xl text-xs text-slate-900 dark:text-white focus:outline-hidden focus:ring-2 focus:ring-amber-500"
                            >
                                <option value="">All Machinery</option>
                                {equipmentList.map((eq) => (
                                    <option key={eq.id} value={eq.id}>{eq.name} ({eq.plate_number || eq.type})</option>
                                ))}
                            </select>
                        </div>

                        <div>
                            <select
                                value={status}
                                onChange={(e) => setStatus(e.target.value)}
                                className="w-full px-3 py-2 bg-slate-50 dark:bg-slate-800/60 border border-slate-200 dark:border-slate-700 rounded-xl text-xs text-slate-900 dark:text-white focus:outline-hidden focus:ring-2 focus:ring-amber-500"
                            >
                                <option value="">All Statuses</option>
                                <option value="pending">Pending / Queued</option>
                                <option value="in_progress">In Progress</option>
                                <option value="completed">Completed / Closed</option>
                            </select>
                        </div>

                        <div>
                            <select
                                value={orderType}
                                onChange={(e) => setOrderType(e.target.value)}
                                className="w-full px-3 py-2 bg-slate-50 dark:bg-slate-800/60 border border-slate-200 dark:border-slate-700 rounded-xl text-xs text-slate-900 dark:text-white focus:outline-hidden focus:ring-2 focus:ring-amber-500"
                            >
                                <option value="">All Service Types</option>
                                <option value="preventive">Preventive Service</option>
                                <option value="breakdown">Breakdown Repair</option>
                                <option value="inspection">Safety Inspection</option>
                            </select>
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
                                    setEquipmentId('');
                                    setProjectId('');
                                    setStatus('');
                                    setOrderType('');
                                    setPriority('');
                                    router.get('/equipment/maintenance');
                                }}
                                className="px-3 py-2 bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-600 dark:text-slate-300 rounded-xl text-xs font-semibold transition-all cursor-pointer"
                            >
                                Reset
                            </button>
                        </div>
                    </div>
                </form>

                {/* Work Orders Table */}
                <div className="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-xs overflow-hidden">
                    <div className="overflow-x-auto">
                        <table className="w-full text-left text-xs">
                            <thead className="bg-slate-50 dark:bg-slate-800/60 border-b border-slate-200 dark:border-slate-800 text-slate-500 dark:text-slate-400 font-semibold uppercase tracking-wider">
                                <tr>
                                    <th className="py-3 px-4">Work Order #</th>
                                    <th className="py-3 px-4">Equipment Machine</th>
                                    <th className="py-3 px-4">Service Scope & Title</th>
                                    <th className="py-3 px-4">Type & Priority</th>
                                    <th className="py-3 px-4">Location / Site</th>
                                    <th className="py-3 px-4 text-right">Cost (ETB)</th>
                                    <th className="py-3 px-4 text-center">Downtime</th>
                                    <th className="py-3 px-4">Status</th>
                                    <th className="py-3 px-4 text-right">Actions</th>
                                </tr>
                            </thead>
                            <tbody className="divide-y divide-slate-200 dark:divide-slate-800">
                                {orders.length === 0 ? (
                                    <tr>
                                        <td colSpan="9" className="py-8 text-center text-slate-500 dark:text-slate-400">
                                            No maintenance work orders found matching criteria.
                                        </td>
                                    </tr>
                                ) : (
                                    orders.map((wo) => (
                                        <tr key={wo.id} className="hover:bg-slate-50/80 dark:hover:bg-slate-800/40 transition-colors">
                                            <td className="py-3 px-4 font-mono font-bold text-amber-600 dark:text-amber-400">
                                                <Link href={`/equipment/maintenance/${wo.id}`} className="hover:underline">
                                                    {wo.work_order_no}
                                                </Link>
                                            </td>
                                            <td className="py-3 px-4">
                                                <div className="font-bold text-slate-900 dark:text-white">
                                                    {wo.equipment?.name || 'Equipment'}
                                                </div>
                                                <div className="text-[11px] text-slate-400 font-mono">
                                                    {wo.equipment?.plate_number || wo.equipment?.type} • {Number(wo.operating_hours).toFixed(1)} hrs
                                                </div>
                                            </td>
                                            <td className="py-3 px-4 font-medium text-slate-900 dark:text-white max-w-xs truncate">
                                                {wo.title}
                                            </td>
                                            <td className="py-3 px-4">
                                                <div className="flex flex-col gap-1">
                                                    {getTypeBadge(wo.order_type)}
                                                    <span className="text-[10px] text-slate-500 uppercase font-semibold">
                                                        {wo.priority}
                                                    </span>
                                                </div>
                                            </td>
                                            <td className="py-3 px-4 text-slate-600 dark:text-slate-300">
                                                {wo.project?.name || 'Central Fleet Workshop'}
                                            </td>
                                            <td className="py-3 px-4 text-right font-extrabold text-slate-900 dark:text-white">
                                                {Number(wo.total_cost).toLocaleString()} ETB
                                            </td>
                                            <td className="py-3 px-4 text-center font-medium text-slate-700 dark:text-slate-300">
                                                {Number(wo.downtime_hours) > 0 ? `${Number(wo.downtime_hours).toFixed(1)} hrs` : '-'}
                                            </td>
                                            <td className="py-3 px-4">
                                                {getStatusBadge(wo.status)}
                                            </td>
                                            <td className="py-3 px-4 text-right">
                                                <div className="inline-flex items-center gap-1">
                                                    <Link
                                                        href={`/equipment/maintenance/${wo.id}`}
                                                        className="p-1.5 text-slate-500 hover:text-amber-600 dark:hover:text-amber-400 hover:bg-slate-100 dark:hover:bg-slate-800 rounded-lg transition-colors cursor-pointer"
                                                        title="View Work Order"
                                                    >
                                                        <Eye className="w-3.5 h-3.5" />
                                                    </Link>
                                                    <a
                                                        href={`/equipment/maintenance/${wo.id}/print`}
                                                        target="_blank"
                                                        rel="noopener noreferrer"
                                                        className="p-1.5 text-slate-500 hover:text-blue-600 dark:hover:text-blue-400 hover:bg-slate-100 dark:hover:bg-slate-800 rounded-lg transition-colors cursor-pointer"
                                                        title="Print Job Card"
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
                    {workOrders?.links && workOrders.links.length > 3 && (
                        <div className="flex items-center justify-between px-4 py-3 border-t border-slate-200 dark:border-slate-800 text-xs">
                            <span className="text-slate-500">
                                Showing {workOrders.from || 0} to {workOrders.to || 0} of {workOrders.total} work orders
                            </span>
                            <div className="flex gap-1">
                                {workOrders.links.map((link, idx) => (
                                    <Link
                                        key={idx}
                                        href={link.url || '#'}
                                        dangerouslySetInnerHTML={{ __html: link.label }}
                                        className={`px-3 py-1 rounded-lg border ${
                                            link.active
                                                ? 'bg-amber-500 border-amber-500 text-slate-950 font-bold'
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
