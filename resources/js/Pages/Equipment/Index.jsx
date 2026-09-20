import { useState, useEffect } from 'react';
import { Head, Link, router, useForm } from '@inertiajs/react';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import {
    Truck,
    PlusCircle,
    Search,
    Wrench,
    Clock,
    AlertTriangle,
    CheckCircle2,
    Fuel,
    MapPin,
    Edit,
    Trash2,
    X,
    Save,
    Activity,
    Shield,
    Download,
} from 'lucide-react';

export default function Index({
    equipmentList,
    projects = [],
    totals = {},
    filters = {},
    canRegister = false,
    isInventoryContext = false,
}) {
    const [search, setSearch] = useState(filters.q || '');
    const [statusFilter, setStatusFilter] = useState(filters.status || '');
    const [projectFilter, setProjectFilter] = useState(filters.project_id || '');

    // Form modal
    const [showFormModal, setShowFormModal] = useState(false);
    const [editingEquipment, setEditingEquipment] = useState(null);

    // Maintenance log modal
    const [showLogModal, setShowLogModal] = useState(false);
    const [selectedEquipmentForLog, setSelectedEquipmentForLog] = useState(null);

    const basePath = isInventoryContext ? '/inventory/equipment' : '/equipment';

    useEffect(() => {
        if (canRegister && typeof window !== 'undefined') {
            const params = new URLSearchParams(window.location.search);
            if (params.get('action') === 'register') {
                openCreate();
            }
        }
    }, [canRegister]);

    const { data, setData, post, put, reset, processing, errors } = useForm({
        name: '',
        plate_number: '',
        type: 'Excavator',
        project_id: '',
        status: 'operational',
        operating_hours: '0',
        next_service_hours: '250',
        fuel_type: 'Diesel',
        purchase_date: '',
        purchase_cost: '',
        notes: '',
    });

    const logForm = useForm({
        log_type: 'service',
        hours_at_log: '',
        cost: '0.00',
        description: '',
        logged_at: new Date().toISOString().split('T')[0],
    });

    const handleFilter = (e) => {
        if (e) e.preventDefault();
        router.get(basePath, {
            q: search,
            status: statusFilter,
            project_id: projectFilter,
        }, {
            preserveState: true,
            preserveScroll: true,
        });
    };

    const openCreate = () => {
        setEditingEquipment(null);
        reset();
        setShowFormModal(true);
    };

    const openEdit = (eq) => {
        setEditingEquipment(eq);
        setData({
            name: eq.name || '',
            plate_number: eq.plate_number || '',
            type: eq.type || 'Excavator',
            project_id: eq.project_id || '',
            status: eq.status || 'operational',
            operating_hours: eq.operating_hours || '0',
            next_service_hours: eq.next_service_hours || '',
            fuel_type: eq.fuel_type || 'Diesel',
            purchase_date: eq.purchase_date ? eq.purchase_date.substring(0, 10) : '',
            purchase_cost: eq.purchase_cost || '',
            notes: eq.notes || '',
        });
        setShowFormModal(true);
    };

    const openLog = (eq) => {
        setSelectedEquipmentForLog(eq);
        logForm.setData({
            log_type: 'service',
            hours_at_log: eq.operating_hours || '',
            cost: '0.00',
            description: '',
            logged_at: new Date().toISOString().split('T')[0],
        });
        setShowLogModal(true);
    };

    const handleFormSubmit = (e) => {
        e.preventDefault();
        if (editingEquipment) {
            put(`${basePath}/${editingEquipment.id}`, {
                onSuccess: () => setShowFormModal(false),
            });
        } else {
            post(basePath, {
                onSuccess: () => setShowFormModal(false),
            });
        }
    };

    const handleLogSubmit = (e) => {
        e.preventDefault();
        logForm.post(`${basePath}/${selectedEquipmentForLog.id}/logs`, {
            onSuccess: () => setShowLogModal(false),
        });
    };

    const handleDelete = (id, name) => {
        if (confirm(`Decommission machinery unit "${name}"?`)) {
            router.delete(`${basePath}/${id}`);
        }
    };

    const machines = equipmentList?.data || [];

    return (
        <AuthenticatedLayout title="Machinery Fleet" header={isInventoryContext ? "Inventory & Assets" : "Executive Fleet Telemetry"}>
            <div className="space-y-6">
                {/* Header */}
                <div className="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-2 border-b border-slate-200 dark:border-slate-800">
                    <div>
                        <h1 className="text-xl sm:text-2xl font-extrabold tracking-tight text-slate-900 dark:text-white">
                            Heavy Machinery & Plant Fleet
                        </h1>
                        <p className="text-xs sm:text-sm text-slate-500 dark:text-slate-400 mt-0.5">
                            Excavators, batching plants, tippers, telemetry hours, and maintenance service intervals.
                        </p>
                    </div>

                    <div className="flex flex-wrap items-center gap-2 self-start sm:self-auto">
                        <a
                            href={`${basePath}/export?q=${encodeURIComponent(search)}&status=${encodeURIComponent(statusFilter)}&project_id=${encodeURIComponent(projectFilter)}`}
                            className="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 text-slate-700 dark:text-slate-200 hover:bg-slate-50 dark:hover:bg-slate-800 font-bold text-xs shadow-xs transition-colors cursor-pointer"
                            title="Export fleet records and telemetry to CSV"
                        >
                            <Download className="w-4 h-4 text-blue-600" />
                            <span>Export Telemetry</span>
                        </a>

                        {canRegister ? (
                            <button
                                onClick={openCreate}
                                className="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl bg-blue-900 hover:bg-blue-950 text-white font-bold text-xs shadow-sm transition-colors cursor-pointer"
                            >
                                <PlusCircle className="w-4 h-4" />
                                <span>Register Machinery</span>
                            </button>
                        ) : (
                            <div className="inline-flex items-center gap-2 px-3.5 py-2 rounded-xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 shadow-xs">
                                <span className="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                                <span className="text-xs font-bold text-slate-700 dark:text-slate-300">Executive Fleet Telemetry</span>
                            </div>
                        )}
                    </div>
                </div>

                {/* Totals */}
                <div className="grid grid-cols-2 sm:grid-cols-5 gap-4">
                    <div className="rounded-2xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 p-4 shadow-xs">
                        <span className="text-[11px] font-semibold text-slate-400 uppercase tracking-wider block">Total Fleet</span>
                        <div className="text-xl font-black text-slate-900 dark:text-white mt-1 font-mono">
                            {totals.total || 0}
                        </div>
                    </div>
                    <div className="rounded-2xl border border-emerald-200 dark:border-emerald-900/40 bg-emerald-50/40 dark:bg-emerald-950/20 p-4">
                        <span className="text-[11px] font-semibold text-emerald-600 dark:text-emerald-400 uppercase tracking-wider block">Operational</span>
                        <div className="text-xl font-black text-emerald-700 dark:text-emerald-300 mt-1 font-mono">
                            {totals.operational || 0}
                        </div>
                    </div>
                    <button
                        type="button"
                        onClick={() => {
                            const next = statusFilter === 'service_due' ? '' : 'service_due';
                            setStatusFilter(next);
                            router.get(basePath, {
                                q: search,
                                status: next,
                                project_id: projectFilter,
                            }, { preserveState: true, preserveScroll: true });
                        }}
                        className={`rounded-2xl border p-4 text-left transition-all cursor-pointer ${
                            statusFilter === 'service_due'
                                ? 'border-orange-500 bg-orange-500/10 ring-2 ring-orange-500/20'
                                : 'border-orange-200 dark:border-orange-900/40 bg-orange-50/40 dark:bg-orange-950/20 hover:border-orange-300'
                        }`}
                    >
                        <span className="text-[11px] font-semibold text-orange-600 dark:text-orange-400 uppercase tracking-wider block">Service Due Soon</span>
                        <div className="text-xl font-black text-orange-700 dark:text-orange-300 mt-1 font-mono flex items-center justify-between">
                            <span>{totals.service_due || 0}</span>
                            {(totals.service_due || 0) > 0 && (
                                <AlertTriangle className="w-4 h-4 text-orange-500 animate-pulse" />
                            )}
                        </div>
                    </button>
                    <div className="rounded-2xl border border-amber-200 dark:border-amber-900/40 bg-amber-50/40 dark:bg-amber-950/20 p-4">
                        <span className="text-[11px] font-semibold text-amber-600 dark:text-amber-400 uppercase tracking-wider block">Maintenance</span>
                        <div className="text-xl font-black text-amber-700 dark:text-amber-300 mt-1 font-mono">
                            {totals.maintenance || 0}
                        </div>
                    </div>
                    <div className="rounded-2xl border border-rose-200 dark:border-rose-900/40 bg-rose-50/40 dark:bg-rose-950/20 p-4">
                        <span className="text-[11px] font-semibold text-rose-600 dark:text-rose-400 uppercase tracking-wider block">Breakdowns</span>
                        <div className="text-xl font-black text-rose-700 dark:text-rose-300 mt-1 font-mono">
                            {totals.breakdown || 0}
                        </div>
                    </div>
                </div>

                {/* Filters */}
                <div className="rounded-2xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 p-4 shadow-xs">
                    <form onSubmit={handleFilter} className="grid grid-cols-1 sm:grid-cols-4 gap-3">
                        <div className="relative sm:col-span-2">
                            <Search className="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-400 pointer-events-none" />
                            <input
                                type="text"
                                placeholder="Search by machinery name, plate number, type..."
                                value={search}
                                onChange={(e) => setSearch(e.target.value)}
                                className="w-full pl-9 pr-3 py-2 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-xs text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-900/20"
                            />
                        </div>

                        <select
                            value={statusFilter}
                            onChange={(e) => setStatusFilter(e.target.value)}
                            className="w-full px-3 py-2 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-xs text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-900/20"
                        >
                            <option value="">All Statuses</option>
                            <option value="operational">Operational</option>
                            <option value="service_due">⚠️ Service Due Soon (Within 25 hrs)</option>
                            <option value="maintenance">Maintenance</option>
                            <option value="breakdown">Breakdown</option>
                            <option value="idle">Idle</option>
                        </select>

                        <button
                            type="submit"
                            className="w-full py-2 rounded-xl bg-blue-900 hover:bg-blue-950 text-white font-bold text-xs transition-colors cursor-pointer"
                        >
                            Filter Fleet
                        </button>
                    </form>
                </div>

                {/* Machinery List */}
                <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                    {machines.length === 0 ? (
                        <div className="col-span-full py-12 text-center text-slate-400 text-xs bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800">
                            No heavy machinery units registered.
                        </div>
                    ) : (
                        machines.map((eq) => {
                            const hours = Number(eq.operating_hours) || 0;
                            const nextService = Number(eq.next_service_hours) || (hours + 100);
                            const hoursLeft = Math.max(nextService - hours, 0);
                            const isServiceDue = eq.next_service_hours && (hours >= (nextService - 25));
                            const isOverdue = eq.next_service_hours && (hours >= nextService);

                            return (
                                <div key={eq.id} className={`rounded-2xl border bg-white dark:bg-slate-900 p-5 shadow-xs flex flex-col justify-between space-y-4 ${
                                    isOverdue
                                        ? 'border-rose-400 dark:border-rose-800 ring-1 ring-rose-500/20'
                                        : isServiceDue
                                        ? 'border-orange-400 dark:border-orange-800 ring-1 ring-orange-500/20'
                                        : 'border-slate-200 dark:border-slate-800'
                                }`}>
                                    <div>
                                        {isServiceDue && (
                                            <div className={`-mt-5 -mx-5 mb-4 px-4 py-1.5 text-[11px] font-bold rounded-t-2xl flex items-center justify-between ${
                                                isOverdue
                                                    ? 'bg-rose-600 text-white'
                                                    : 'bg-orange-500 text-slate-950'
                                            }`}>
                                                <span className="flex items-center gap-1.5">
                                                    <AlertTriangle className="w-3.5 h-3.5" />
                                                    <span>{isOverdue ? 'CRITICAL: OVERDUE FOR SERVICE' : 'PREVENTIVE SERVICE DUE'}</span>
                                                </span>
                                                <span className="font-mono text-[10px]">
                                                    {isOverdue ? `${Math.abs(hours - nextService).toFixed(0)} hrs past due` : `${hoursLeft.toFixed(1)} hrs left`}
                                                </span>
                                            </div>
                                        )}

                                        <div className="flex items-start justify-between gap-2">
                                            <div>
                                                <h3 className="font-bold text-slate-900 dark:text-white text-sm">
                                                    {eq.name}
                                                </h3>
                                                <div className="text-[11px] text-slate-400 font-mono">
                                                    {eq.type} · {eq.plate_number || 'Plate Unassigned'}
                                                </div>
                                            </div>
                                            <span className={`px-2 py-0.5 rounded-full text-[10px] font-bold uppercase ${
                                                eq.status === 'operational'
                                                    ? 'bg-emerald-50 dark:bg-emerald-950/50 text-emerald-600 dark:text-emerald-400 border border-emerald-200 dark:border-emerald-900'
                                                    : eq.status === 'maintenance'
                                                    ? 'bg-amber-50 dark:bg-amber-950/50 text-amber-600 dark:text-amber-400 border border-amber-200 dark:border-amber-900'
                                                    : eq.status === 'breakdown'
                                                    ? 'bg-rose-50 dark:bg-rose-950/50 text-rose-600 dark:text-rose-400 border border-rose-200 dark:border-rose-900'
                                                    : 'bg-slate-100 text-slate-600 dark:bg-slate-800 dark:text-slate-400'
                                            }`}>
                                                {eq.status}
                                            </span>
                                        </div>

                                        <div className="grid grid-cols-2 gap-2 mt-4 text-xs">
                                            <div className="p-2.5 rounded-xl bg-slate-50 dark:bg-slate-800/60">
                                                <span className="text-[10px] text-slate-400 block font-semibold">Operating Hours</span>
                                                <span className="font-mono font-bold text-slate-900 dark:text-white">
                                                    {hours.toFixed(1)} hrs
                                                </span>
                                            </div>
                                            <div className="p-2.5 rounded-xl bg-slate-50 dark:bg-slate-800/60">
                                                <span className="text-[10px] text-slate-400 block font-semibold">Service Due In</span>
                                                <span className={`font-mono font-bold ${hoursLeft < 30 ? 'text-amber-600' : 'text-slate-900 dark:text-white'}`}>
                                                    {hoursLeft.toFixed(1)} hrs
                                                </span>
                                            </div>
                                        </div>

                                        <div className="flex items-center gap-1 text-[11px] text-slate-500 dark:text-slate-400 mt-3">
                                            <MapPin className="w-3 h-3 shrink-0" />
                                            <span>{eq.project ? eq.project.name : 'Unassigned to Site'}</span>
                                        </div>
                                    </div>

                                    <div className="pt-3 border-t border-slate-100 dark:border-slate-800 flex items-center justify-between">
                                        <button
                                            onClick={() => openLog(eq)}
                                            className="inline-flex items-center gap-1 text-xs font-bold text-blue-900 dark:text-blue-400 hover:underline cursor-pointer"
                                        >
                                            <Wrench className="w-3.5 h-3.5" />
                                            <span>Record Service</span>
                                        </button>

                                        {canRegister && (
                                            <div className="flex items-center gap-1">
                                                <button
                                                    onClick={() => openEdit(eq)}
                                                    className="p-1.5 rounded-lg text-slate-500 hover:text-blue-900 dark:hover:text-white hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors cursor-pointer"
                                                    title="Edit Machinery"
                                                >
                                                    <Edit className="w-3.5 h-3.5" />
                                                </button>
                                                <button
                                                    onClick={() => handleDelete(eq.id, eq.name)}
                                                    className="p-1.5 rounded-lg text-slate-500 hover:text-rose-600 dark:hover:text-rose-400 hover:bg-rose-50 dark:hover:bg-rose-950/40 transition-colors cursor-pointer"
                                                    title="Decommission"
                                                >
                                                    <Trash2 className="w-3.5 h-3.5" />
                                                </button>
                                            </div>
                                        )}
                                    </div>
                                </div>
                            );
                        })
                    )}
                </div>

                {/* Pagination */}
                {equipmentList?.links && equipmentList.links.length > 3 && (
                    <div className="px-4 py-3 rounded-2xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 flex items-center justify-between text-xs text-slate-500">
                        <div>Showing {equipmentList.from || 0} to {equipmentList.to || 0} of {equipmentList.total || 0} units</div>
                        <div className="flex items-center gap-1">
                            {equipmentList.links.map((link, idx) => (
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

                {/* Create/Edit Modal */}
                {showFormModal && (
                    <div className="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-xs">
                        <div className="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-3xl p-6 w-full max-w-2xl max-h-[90vh] overflow-y-auto space-y-4">
                            <div className="flex items-center justify-between pb-3 border-b border-slate-100 dark:border-slate-800">
                                <h3 className="font-extrabold text-slate-900 dark:text-white text-base">
                                    {editingEquipment ? `Edit ${editingEquipment.name}` : 'Register Machinery Unit'}
                                </h3>
                                <button
                                    onClick={() => setShowFormModal(false)}
                                    className="p-1 rounded-lg text-slate-400 hover:text-slate-600 dark:hover:text-slate-200"
                                >
                                    <X className="w-4 h-4" />
                                </button>
                            </div>

                            <form onSubmit={handleFormSubmit} className="space-y-4">
                                <div className="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                    <div className="sm:col-span-2">
                                        <label className="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">
                                            Machinery Name <span className="text-rose-500">*</span>
                                        </label>
                                        <input
                                            type="text"
                                            required
                                            value={data.name}
                                            onChange={(e) => setData('name', e.target.value)}
                                            placeholder="e.g. CAT 320D Hydraulic Excavator"
                                            className="w-full px-3 py-2 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-xs text-slate-900 dark:text-white"
                                        />
                                    </div>

                                    <div>
                                        <label className="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">
                                            Plate / Serial Number
                                        </label>
                                        <input
                                            type="text"
                                            value={data.plate_number}
                                            onChange={(e) => setData('plate_number', e.target.value)}
                                            className="w-full px-3 py-2 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-xs text-slate-900 dark:text-white"
                                        />
                                    </div>

                                    <div>
                                        <label className="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">
                                            Equipment Type
                                        </label>
                                        <input
                                            type="text"
                                            value={data.type}
                                            onChange={(e) => setData('type', e.target.value)}
                                            placeholder="Excavator, Bulldozer, Loader, Dump Truck..."
                                            className="w-full px-3 py-2 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-xs text-slate-900 dark:text-white"
                                        />
                                    </div>

                                    <div>
                                        <label className="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">
                                            Assigned Site Project
                                        </label>
                                        <select
                                            value={data.project_id}
                                            onChange={(e) => setData('project_id', e.target.value)}
                                            className="w-full px-3 py-2 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-xs text-slate-900 dark:text-white"
                                        >
                                            <option value="">Unassigned (HQ / Yard)</option>
                                            {projects.map((p) => (
                                                <option key={p.id} value={p.id}>{p.name}</option>
                                            ))}
                                        </select>
                                    </div>

                                    <div>
                                        <label className="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">
                                            Operational Status
                                        </label>
                                        <select
                                            value={data.status}
                                            onChange={(e) => setData('status', e.target.value)}
                                            className="w-full px-3 py-2 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-xs text-slate-900 dark:text-white"
                                        >
                                            <option value="operational">Operational</option>
                                            <option value="maintenance">Under Maintenance</option>
                                            <option value="breakdown">Breakdown</option>
                                            <option value="idle">Idle / Standby</option>
                                        </select>
                                    </div>

                                    <div>
                                        <label className="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">
                                            Operating Hours
                                        </label>
                                        <input
                                            type="number"
                                            step="0.1"
                                            value={data.operating_hours}
                                            onChange={(e) => setData('operating_hours', e.target.value)}
                                            className="w-full px-3 py-2 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-xs text-slate-900 dark:text-white font-mono"
                                        />
                                    </div>

                                    <div>
                                        <label className="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">
                                            Next Service Due (Hours)
                                        </label>
                                        <input
                                            type="number"
                                            step="0.1"
                                            value={data.next_service_hours}
                                            onChange={(e) => setData('next_service_hours', e.target.value)}
                                            className="w-full px-3 py-2 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-xs text-slate-900 dark:text-white font-mono"
                                        />
                                    </div>
                                </div>

                                <div className="pt-3 border-t border-slate-100 dark:border-slate-800 flex items-center justify-end gap-2">
                                    <button
                                        type="button"
                                        onClick={() => setShowFormModal(false)}
                                        className="px-4 py-2 rounded-xl border border-slate-200 dark:border-slate-700 text-slate-600 dark:text-slate-300 text-xs font-bold"
                                    >
                                        Cancel
                                    </button>
                                    <button
                                        type="submit"
                                        disabled={processing}
                                        className="px-5 py-2 rounded-xl bg-blue-900 hover:bg-blue-950 text-white font-bold text-xs cursor-pointer"
                                    >
                                        Save Machinery
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                )}

                {/* Maintenance Log Modal */}
                {showLogModal && selectedEquipmentForLog && (
                    <div className="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-xs">
                        <div className="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-3xl p-6 w-full max-w-lg space-y-4">
                            <div className="flex items-center justify-between pb-3 border-b border-slate-100 dark:border-slate-800">
                                <div>
                                    <h3 className="font-extrabold text-slate-900 dark:text-white text-sm">
                                        Log Maintenance / Service
                                    </h3>
                                    <p className="text-[11px] text-slate-400">
                                        {selectedEquipmentForLog.name}
                                    </p>
                                </div>
                                <button
                                    onClick={() => setShowLogModal(false)}
                                    className="p-1 rounded-lg text-slate-400 hover:text-slate-600 dark:hover:text-slate-200"
                                >
                                    <X className="w-4 h-4" />
                                </button>
                            </div>

                            <form onSubmit={handleLogSubmit} className="space-y-4">
                                <div>
                                    <label className="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">
                                        Event Type
                                    </label>
                                    <select
                                        value={logForm.data.log_type}
                                        onChange={(e) => logForm.setData('log_type', e.target.value)}
                                        className="w-full px-3 py-2 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-xs text-slate-900 dark:text-white"
                                    >
                                        <option value="service">Periodic Service & Oil Change</option>
                                        <option value="repair">Mechanical Repair</option>
                                        <option value="fuel">Fuel Fill-up</option>
                                        <option value="hours_update">Operating Hours Telemetry</option>
                                        <option value="breakdown">Site Breakdown Notice</option>
                                    </select>
                                </div>

                                <div className="grid grid-cols-2 gap-3">
                                    <div>
                                        <label className="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">
                                            Current Hours
                                        </label>
                                        <input
                                            type="number"
                                            step="0.1"
                                            value={logForm.data.hours_at_log}
                                            onChange={(e) => logForm.setData('hours_at_log', e.target.value)}
                                            className="w-full px-3 py-2 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-xs text-slate-900 dark:text-white font-mono"
                                        />
                                    </div>

                                    <div>
                                        <label className="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">
                                            Cost (ETB)
                                        </label>
                                        <input
                                            type="number"
                                            step="0.01"
                                            value={logForm.data.cost}
                                            onChange={(e) => logForm.setData('cost', e.target.value)}
                                            className="w-full px-3 py-2 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-xs text-slate-900 dark:text-white font-mono"
                                        />
                                    </div>
                                </div>

                                <div>
                                    <label className="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">
                                        Description & Replaced Parts
                                    </label>
                                    <textarea
                                        rows={3}
                                        required
                                        value={logForm.data.description}
                                        onChange={(e) => logForm.setData('description', e.target.value)}
                                        placeholder="Oil filter replacement, hydraulic hose repair, fuel litres..."
                                        className="w-full px-3 py-2 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-xs text-slate-900 dark:text-white"
                                    />
                                </div>

                                <div className="pt-3 border-t border-slate-100 dark:border-slate-800 flex items-center justify-end gap-2">
                                    <button
                                        type="button"
                                        onClick={() => setShowLogModal(false)}
                                        className="px-4 py-2 rounded-xl border border-slate-200 dark:border-slate-700 text-slate-600 dark:text-slate-300 text-xs font-bold"
                                    >
                                        Cancel
                                    </button>
                                    <button
                                        type="submit"
                                        disabled={logForm.processing}
                                        className="px-5 py-2 rounded-xl bg-blue-900 hover:bg-blue-950 text-white font-bold text-xs cursor-pointer"
                                    >
                                        Save Log
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                )}
            </div>
        </AuthenticatedLayout>
    );
}
