import { useState } from 'react';
import { Head, Link, router, useForm } from '@inertiajs/react';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import {
    Users,
    UserPlus,
    Search,
    Filter,
    HardHat,
    Briefcase,
    Phone,
    CreditCard,
    DollarSign,
    CheckCircle2,
    XCircle,
    Edit3,
    Trash2,
    X,
    FileSpreadsheet,
} from 'lucide-react';

export default function Index({
    workers,
    projects = [],
    trades = [],
    skillLevels = {},
    stats = { total_workers: 0, active_workers: 0, total_trades: 0, avg_daily_rate: 0 },
    filters = {},
}) {
    const [search, setSearch] = useState(filters.search || '');
    const [selectedTrade, setSelectedTrade] = useState(filters.trade || '');
    const [selectedProjectId, setSelectedProjectId] = useState(filters.project_id || '');
    const [selectedActive, setSelectedActive] = useState(filters.is_active || '');

    // Modal state for worker registration & editing
    const [isCreateOpen, setIsCreateOpen] = useState(false);
    const [editingWorker, setEditingWorker] = useState(null);

    const { data, setData, post, put, processing, errors, reset } = useForm({
        first_name: '',
        middle_name: '',
        last_name: '',
        phone: '',
        id_card_number: '',
        trade: trades[0] || 'Daily Laborer (Unskilled)',
        skill_level: 'unskilled',
        daily_wage_rate: '',
        overtime_hourly_rate: '',
        project_id: '',
        emergency_contact_name: '',
        emergency_contact_phone: '',
        notes: '',
        is_active: true,
    });

    const handleFilter = (e) => {
        if (e) e.preventDefault();
        router.get('/labor/workers', {
            search,
            trade: selectedTrade,
            project_id: selectedProjectId,
            is_active: selectedActive,
        }, {
            preserveState: true,
            preserveScroll: true,
        });
    };

    const openCreateModal = () => {
        reset();
        setEditingWorker(null);
        setIsCreateOpen(true);
    };

    const openEditModal = (worker) => {
        setEditingWorker(worker);
        setData({
            first_name: worker.first_name,
            middle_name: worker.middle_name || '',
            last_name: worker.last_name,
            phone: worker.phone || '',
            id_card_number: worker.id_card_number || '',
            trade: worker.trade,
            skill_level: worker.skill_level || 'unskilled',
            daily_wage_rate: worker.daily_wage_rate,
            overtime_hourly_rate: worker.overtime_hourly_rate || '',
            project_id: worker.project_id || '',
            emergency_contact_name: worker.emergency_contact_name || '',
            emergency_contact_phone: worker.emergency_contact_phone || '',
            notes: worker.notes || '',
            is_active: worker.is_active,
        });
        setIsCreateOpen(true);
    };

    const handleSubmit = (e) => {
        e.preventDefault();
        if (editingWorker) {
            put(`/labor/workers/${editingWorker.id}`, {
                onSuccess: () => {
                    setIsCreateOpen(false);
                    setEditingWorker(null);
                },
            });
        } else {
            post('/labor/workers', {
                onSuccess: () => {
                    setIsCreateOpen(false);
                    reset();
                },
            });
        }
    };

    const handleDelete = (worker) => {
        if (confirm(`Are you sure you want to remove worker ${worker.full_name} (${worker.worker_code})?`)) {
            router.delete(`/labor/workers/${worker.id}`);
        }
    };

    const workerList = workers?.data || [];

    return (
        <AuthenticatedLayout title="Site Casual Laborers" header="Labor Force">
            <div className="space-y-6">
                {/* Header */}
                <div className="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-2 border-b border-slate-200 dark:border-slate-800">
                    <div>
                        <h1 className="text-xl sm:text-2xl font-extrabold tracking-tight text-slate-900 dark:text-white flex items-center gap-2.5">
                            <HardHat className="w-7 h-7 text-amber-500" />
                            Site Casual Laborers & Daily Artisans
                        </h1>
                        <p className="text-xs sm:text-sm text-slate-500 dark:text-slate-400 mt-0.5">
                            Directory of daily temporary workers, masons, carpenters, bar benders, and site helpers.
                        </p>
                    </div>

                    <div className="flex items-center gap-2.5">
                        <Link
                            href="/labor/muster-rolls"
                            className="inline-flex items-center gap-2 px-3.5 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-200 font-semibold text-xs transition-all cursor-pointer"
                        >
                            <FileSpreadsheet className="w-4 h-4 text-emerald-500" />
                            <span>Muster Rolls</span>
                        </Link>
                        <button
                            onClick={openCreateModal}
                            className="inline-flex items-center justify-center gap-2 px-4 py-2 rounded-xl bg-amber-500 hover:bg-amber-600 text-slate-950 font-bold text-xs shadow-md shadow-amber-500/20 transition-all cursor-pointer"
                        >
                            <UserPlus className="w-4 h-4" />
                            <span>Register Worker</span>
                        </button>
                    </div>
                </div>

                {/* KPI Metrics */}
                <div className="grid grid-cols-2 lg:grid-cols-4 gap-4">
                    <div className="bg-white dark:bg-slate-900 p-4 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-xs">
                        <div className="flex items-center justify-between">
                            <span className="text-xs font-semibold text-slate-500 dark:text-slate-400">Total Registered</span>
                            <Users className="w-4 h-4 text-amber-500" />
                        </div>
                        <div className="mt-2 text-2xl font-extrabold text-slate-900 dark:text-white">
                            {stats.total_workers}
                        </div>
                        <span className="text-[11px] text-slate-500">Casual site laborers</span>
                    </div>

                    <div className="bg-white dark:bg-slate-900 p-4 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-xs">
                        <div className="flex items-center justify-between">
                            <span className="text-xs font-semibold text-slate-500 dark:text-slate-400">Active On Sites</span>
                            <CheckCircle2 className="w-4 h-4 text-emerald-500" />
                        </div>
                        <div className="mt-2 text-2xl font-extrabold text-emerald-600 dark:text-emerald-400">
                            {stats.active_workers}
                        </div>
                        <span className="text-[11px] text-emerald-600/80">Available for muster rolls</span>
                    </div>

                    <div className="bg-white dark:bg-slate-900 p-4 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-xs">
                        <div className="flex items-center justify-between">
                            <span className="text-xs font-semibold text-slate-500 dark:text-slate-400">Trades & Crafts</span>
                            <Briefcase className="w-4 h-4 text-blue-500" />
                        </div>
                        <div className="mt-2 text-2xl font-extrabold text-blue-600 dark:text-blue-400">
                            {stats.total_trades}
                        </div>
                        <span className="text-[11px] text-slate-500">Skill specializations</span>
                    </div>

                    <div className="bg-white dark:bg-slate-900 p-4 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-xs">
                        <div className="flex items-center justify-between">
                            <span className="text-xs font-semibold text-slate-500 dark:text-slate-400">Avg Daily Wage</span>
                            <DollarSign className="w-4 h-4 text-indigo-500" />
                        </div>
                        <div className="mt-2 text-2xl font-extrabold text-slate-900 dark:text-white">
                            {Number(stats.avg_daily_rate).toLocaleString()} <span className="text-xs font-bold text-slate-400">ETB</span>
                        </div>
                        <span className="text-[11px] text-slate-500">Average standard rate</span>
                    </div>
                </div>

                {/* Filters */}
                <form onSubmit={handleFilter} className="bg-white dark:bg-slate-900 p-4 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-xs">
                    <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-3">
                        <div className="relative">
                            <Search className="w-4 h-4 absolute left-3 top-3 text-slate-400" />
                            <input
                                type="text"
                                placeholder="Search by name, code, phone..."
                                value={search}
                                onChange={(e) => setSearch(e.target.value)}
                                className="w-full pl-9 pr-3 py-2 bg-slate-50 dark:bg-slate-800/60 border border-slate-200 dark:border-slate-700 rounded-xl text-xs text-slate-900 dark:text-white focus:outline-hidden focus:ring-2 focus:ring-amber-500"
                            />
                        </div>

                        <div>
                            <select
                                value={selectedTrade}
                                onChange={(e) => setSelectedTrade(e.target.value)}
                                className="w-full px-3 py-2 bg-slate-50 dark:bg-slate-800/60 border border-slate-200 dark:border-slate-700 rounded-xl text-xs text-slate-900 dark:text-white focus:outline-hidden focus:ring-2 focus:ring-amber-500"
                            >
                                <option value="">All Trades / Crafts</option>
                                {trades.map((t) => (
                                    <option key={t} value={t}>{t}</option>
                                ))}
                            </select>
                        </div>

                        <div>
                            <select
                                value={selectedProjectId}
                                onChange={(e) => setSelectedProjectId(e.target.value)}
                                className="w-full px-3 py-2 bg-slate-50 dark:bg-slate-800/60 border border-slate-200 dark:border-slate-700 rounded-xl text-xs text-slate-900 dark:text-white focus:outline-hidden focus:ring-2 focus:ring-amber-500"
                            >
                                <option value="">All Project Sites</option>
                                {projects.map((p) => (
                                    <option key={p.id} value={p.id}>{p.name}</option>
                                ))}
                            </select>
                        </div>

                        <div>
                            <select
                                value={selectedActive}
                                onChange={(e) => setSelectedActive(e.target.value)}
                                className="w-full px-3 py-2 bg-slate-50 dark:bg-slate-800/60 border border-slate-200 dark:border-slate-700 rounded-xl text-xs text-slate-900 dark:text-white focus:outline-hidden focus:ring-2 focus:ring-amber-500"
                            >
                                <option value="">All Statuses</option>
                                <option value="true">Active Only</option>
                                <option value="false">Inactive</option>
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
                                    setSelectedTrade('');
                                    setSelectedProjectId('');
                                    setSelectedActive('');
                                    router.get('/labor/workers');
                                }}
                                className="px-3 py-2 bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-600 dark:text-slate-300 rounded-xl text-xs font-semibold transition-all cursor-pointer"
                            >
                                Reset
                            </button>
                        </div>
                    </div>
                </form>

                {/* Workers Table */}
                <div className="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-xs overflow-hidden">
                    <div className="overflow-x-auto">
                        <table className="w-full text-left text-xs">
                            <thead className="bg-slate-50 dark:bg-slate-800/60 border-b border-slate-200 dark:border-slate-800 text-slate-500 dark:text-slate-400 font-semibold uppercase tracking-wider">
                                <tr>
                                    <th className="py-3 px-4">Worker ID</th>
                                    <th className="py-3 px-4">Full Name</th>
                                    <th className="py-3 px-4">Trade / Craft</th>
                                    <th className="py-3 px-4">Daily Rate</th>
                                    <th className="py-3 px-4">Assigned Project</th>
                                    <th className="py-3 px-4">Contact</th>
                                    <th className="py-3 px-4">Status</th>
                                    <th className="py-3 px-4 text-right">Actions</th>
                                </tr>
                            </thead>
                            <tbody className="divide-y divide-slate-200 dark:divide-slate-800">
                                {workerList.length === 0 ? (
                                    <tr>
                                        <td colSpan="8" className="py-8 text-center text-slate-500 dark:text-slate-400">
                                            No casual laborers found matching your search.
                                        </td>
                                    </tr>
                                ) : (
                                    workerList.map((worker) => (
                                        <tr key={worker.id} className="hover:bg-slate-50/80 dark:hover:bg-slate-800/40 transition-colors">
                                            <td className="py-3 px-4 font-mono font-bold text-amber-600 dark:text-amber-400">
                                                {worker.worker_code}
                                            </td>
                                            <td className="py-3 px-4 font-bold text-slate-900 dark:text-white">
                                                {worker.full_name}
                                                {worker.id_card_number && (
                                                    <span className="block text-[10px] font-normal text-slate-400">
                                                        ID: {worker.id_card_number}
                                                    </span>
                                                )}
                                            </td>
                                            <td className="py-3 px-4">
                                                <span className="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[11px] font-semibold bg-amber-50 dark:bg-amber-950/40 text-amber-700 dark:text-amber-300 border border-amber-200 dark:border-amber-800">
                                                    {worker.trade}
                                                </span>
                                            </td>
                                            <td className="py-3 px-4 font-extrabold text-slate-900 dark:text-white">
                                                {Number(worker.daily_wage_rate).toLocaleString()} ETB
                                                <span className="block text-[10px] font-normal text-slate-400">
                                                    OT: {Number(worker.overtime_hourly_rate || 0).toLocaleString()} ETB/hr
                                                </span>
                                            </td>
                                            <td className="py-3 px-4 text-slate-600 dark:text-slate-300">
                                                {worker.project?.name || <span className="text-slate-400 italic">Unassigned (Floating)</span>}
                                            </td>
                                            <td className="py-3 px-4 text-slate-600 dark:text-slate-300">
                                                {worker.phone || '-'}
                                            </td>
                                            <td className="py-3 px-4">
                                                {worker.is_active ? (
                                                    <span className="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-semibold bg-emerald-50 dark:bg-emerald-950/60 text-emerald-700 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-800">
                                                        <CheckCircle2 className="w-2.5 h-2.5" /> Active
                                                    </span>
                                                ) : (
                                                    <span className="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-semibold bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-400 border border-slate-200 dark:border-slate-700">
                                                        <XCircle className="w-2.5 h-2.5" /> Inactive
                                                    </span>
                                                )}
                                            </td>
                                            <td className="py-3 px-4 text-right">
                                                <div className="inline-flex items-center gap-1">
                                                    <button
                                                        onClick={() => openEditModal(worker)}
                                                        className="p-1.5 text-slate-500 hover:text-amber-600 dark:hover:text-amber-400 hover:bg-slate-100 dark:hover:bg-slate-800 rounded-lg transition-colors cursor-pointer"
                                                        title="Edit Worker"
                                                    >
                                                        <Edit3 className="w-3.5 h-3.5" />
                                                    </button>
                                                    <button
                                                        onClick={() => handleDelete(worker)}
                                                        className="p-1.5 text-slate-500 hover:text-rose-600 dark:hover:text-rose-400 hover:bg-slate-100 dark:hover:bg-slate-800 rounded-lg transition-colors cursor-pointer"
                                                        title="Delete Worker"
                                                    >
                                                        <Trash2 className="w-3.5 h-3.5" />
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                    ))
                                )}
                            </tbody>
                        </table>
                    </div>

                    {/* Pagination */}
                    {workers?.links && workers.links.length > 3 && (
                        <div className="flex items-center justify-between px-4 py-3 border-t border-slate-200 dark:border-slate-800 text-xs">
                            <span className="text-slate-500">
                                Showing {workers.from || 0} to {workers.to || 0} of {workers.total} workers
                            </span>
                            <div className="flex gap-1">
                                {workers.links.map((link, idx) => (
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

                {/* Worker Register / Edit Modal */}
                {isCreateOpen && (
                    <div className="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-xs">
                        <div className="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl max-w-xl w-full max-h-[90vh] overflow-y-auto p-6 shadow-2xl">
                            <div className="flex items-center justify-between pb-3 border-b border-slate-200 dark:border-slate-800">
                                <h3 className="text-lg font-bold text-slate-900 dark:text-white flex items-center gap-2">
                                    <HardHat className="w-5 h-5 text-amber-500" />
                                    {editingWorker ? `Edit Worker: ${editingWorker.worker_code}` : 'Register Site Casual Worker'}
                                </h3>
                                <button
                                    onClick={() => setIsCreateOpen(false)}
                                    className="p-1 rounded-lg text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors"
                                >
                                    <X className="w-5 h-5" />
                                </button>
                            </div>

                            <form onSubmit={handleSubmit} className="mt-4 space-y-4">
                                <div className="grid grid-cols-1 sm:grid-cols-3 gap-3">
                                    <div>
                                        <label className="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">
                                            First Name *
                                        </label>
                                        <input
                                            type="text"
                                            required
                                            value={data.first_name}
                                            onChange={(e) => setData('first_name', e.target.value)}
                                            className="w-full px-3 py-2 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-xs text-slate-900 dark:text-white focus:ring-2 focus:ring-amber-500"
                                        />
                                        {errors.first_name && <p className="text-rose-500 text-[10px] mt-1">{errors.first_name}</p>}
                                    </div>
                                    <div>
                                        <label className="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">
                                            Middle Name
                                        </label>
                                        <input
                                            type="text"
                                            value={data.middle_name}
                                            onChange={(e) => setData('middle_name', e.target.value)}
                                            className="w-full px-3 py-2 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-xs text-slate-900 dark:text-white focus:ring-2 focus:ring-amber-500"
                                        />
                                    </div>
                                    <div>
                                        <label className="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">
                                            Last Name *
                                        </label>
                                        <input
                                            type="text"
                                            required
                                            value={data.last_name}
                                            onChange={(e) => setData('last_name', e.target.value)}
                                            className="w-full px-3 py-2 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-xs text-slate-900 dark:text-white focus:ring-2 focus:ring-amber-500"
                                        />
                                        {errors.last_name && <p className="text-rose-500 text-[10px] mt-1">{errors.last_name}</p>}
                                    </div>
                                </div>

                                <div className="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                    <div>
                                        <label className="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">
                                            Trade / Craft *
                                        </label>
                                        <select
                                            required
                                            value={data.trade}
                                            onChange={(e) => setData('trade', e.target.value)}
                                            className="w-full px-3 py-2 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-xs text-slate-900 dark:text-white focus:ring-2 focus:ring-amber-500"
                                        >
                                            {trades.map((t) => (
                                                <option key={t} value={t}>{t}</option>
                                            ))}
                                        </select>
                                    </div>

                                    <div>
                                        <label className="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">
                                            Skill Level *
                                        </label>
                                        <select
                                            required
                                            value={data.skill_level}
                                            onChange={(e) => setData('skill_level', e.target.value)}
                                            className="w-full px-3 py-2 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-xs text-slate-900 dark:text-white focus:ring-2 focus:ring-amber-500"
                                        >
                                            {Object.entries(skillLevels).map(([key, label]) => (
                                                <option key={key} value={key}>{label}</option>
                                            ))}
                                        </select>
                                    </div>
                                </div>

                                <div className="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                    <div>
                                        <label className="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">
                                            Daily Wage Rate (ETB) *
                                        </label>
                                        <input
                                            type="number"
                                            step="0.01"
                                            min="0"
                                            required
                                            placeholder="e.g. 350.00"
                                            value={data.daily_wage_rate}
                                            onChange={(e) => {
                                                const rate = e.target.value;
                                                setData('daily_wage_rate', rate);
                                                if (rate && !data.overtime_hourly_rate) {
                                                    setData('overtime_hourly_rate', (Number(rate) / 8 * 1.25).toFixed(2));
                                                }
                                            }}
                                            className="w-full px-3 py-2 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-xs text-slate-900 dark:text-white focus:ring-2 focus:ring-amber-500"
                                        />
                                        {errors.daily_wage_rate && <p className="text-rose-500 text-[10px] mt-1">{errors.daily_wage_rate}</p>}
                                    </div>

                                    <div>
                                        <label className="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">
                                            Overtime Rate (ETB / Hour)
                                        </label>
                                        <input
                                            type="number"
                                            step="0.01"
                                            min="0"
                                            placeholder="Standard 1.25x hourly"
                                            value={data.overtime_hourly_rate}
                                            onChange={(e) => setData('overtime_hourly_rate', e.target.value)}
                                            className="w-full px-3 py-2 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-xs text-slate-900 dark:text-white focus:ring-2 focus:ring-amber-500"
                                        />
                                    </div>
                                </div>

                                <div className="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                    <div>
                                        <label className="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">
                                            Phone Number
                                        </label>
                                        <input
                                            type="text"
                                            placeholder="0911..."
                                            value={data.phone}
                                            onChange={(e) => setData('phone', e.target.value)}
                                            className="w-full px-3 py-2 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-xs text-slate-900 dark:text-white focus:ring-2 focus:ring-amber-500"
                                        />
                                    </div>

                                    <div>
                                        <label className="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">
                                            Kebele / National ID #
                                        </label>
                                        <input
                                            type="text"
                                            placeholder="ID-..."
                                            value={data.id_card_number}
                                            onChange={(e) => setData('id_card_number', e.target.value)}
                                            className="w-full px-3 py-2 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-xs text-slate-900 dark:text-white focus:ring-2 focus:ring-amber-500"
                                        />
                                    </div>
                                </div>

                                <div>
                                    <label className="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">
                                        Assigned Project Site
                                    </label>
                                    <select
                                        value={data.project_id}
                                        onChange={(e) => setData('project_id', e.target.value)}
                                        className="w-full px-3 py-2 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-xs text-slate-900 dark:text-white focus:ring-2 focus:ring-amber-500"
                                    >
                                        <option value="">Floating (Available for Any Site)</option>
                                        {projects.map((p) => (
                                            <option key={p.id} value={p.id}>{p.name}</option>
                                        ))}
                                    </select>
                                </div>

                                <div className="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                    <div>
                                        <label className="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">
                                            Emergency Contact Name
                                        </label>
                                        <input
                                            type="text"
                                            value={data.emergency_contact_name}
                                            onChange={(e) => setData('emergency_contact_name', e.target.value)}
                                            className="w-full px-3 py-2 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-xs text-slate-900 dark:text-white focus:ring-2 focus:ring-amber-500"
                                        />
                                    </div>
                                    <div>
                                        <label className="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">
                                            Emergency Phone
                                        </label>
                                        <input
                                            type="text"
                                            value={data.emergency_contact_phone}
                                            onChange={(e) => setData('emergency_contact_phone', e.target.value)}
                                            className="w-full px-3 py-2 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-xs text-slate-900 dark:text-white focus:ring-2 focus:ring-amber-500"
                                        />
                                    </div>
                                </div>

                                {editingWorker && (
                                    <div className="flex items-center gap-2 pt-2">
                                        <input
                                            type="checkbox"
                                            id="is_active"
                                            checked={data.is_active}
                                            onChange={(e) => setData('is_active', e.target.checked)}
                                            className="rounded text-amber-500 focus:ring-amber-500 h-4 w-4"
                                        />
                                        <label htmlFor="is_active" className="text-xs font-medium text-slate-700 dark:text-slate-300">
                                            Active (Available for Site Muster Rolls)
                                        </label>
                                    </div>
                                )}

                                <div className="flex justify-end gap-2 pt-3 border-t border-slate-200 dark:border-slate-800">
                                    <button
                                        type="button"
                                        onClick={() => setIsCreateOpen(false)}
                                        className="px-4 py-2 bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300 text-xs font-semibold rounded-xl transition-all cursor-pointer"
                                    >
                                        Cancel
                                    </button>
                                    <button
                                        type="submit"
                                        disabled={processing}
                                        className="px-5 py-2 bg-amber-500 hover:bg-amber-600 text-slate-950 font-bold text-xs rounded-xl shadow-md transition-all cursor-pointer disabled:opacity-50"
                                    >
                                        {processing ? 'Saving...' : editingWorker ? 'Update Worker' : 'Register Worker'}
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
