import { useState } from 'react';
import { Head, Link, router, useForm } from '@inertiajs/react';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import {
    Briefcase,
    PlusCircle,
    Search,
    Building2,
    FileCheck2,
    Shield,
    DollarSign,
    Users,
    Phone,
    Mail,
    X,
    CheckCircle2,
    Clock,
    FileText,
    ChevronRight,
} from 'lucide-react';

export default function Index({
    subcontractors,
    projects = [],
    totals = {},
    filters = {},
}) {
    const [search, setSearch] = useState(filters.q || '');
    const [projectFilter, setProjectFilter] = useState(filters.project_id || '');
    const [statusFilter, setStatusFilter] = useState(filters.status || '');

    const [showCreateModal, setShowCreateModal] = useState(false);
    const [selectedSubForCert, setSelectedSubForCert] = useState(null);
    const [selectedSubForView, setSelectedSubForView] = useState(null);

    // Form: Create Subcontractor
    const createForm = useForm({
        project_id: projects.length > 0 ? projects[0].id : '',
        name: '',
        trade: 'Masonry & Concrete Works',
        contract_number: '',
        contract_sum: '',
        contact_person: '',
        phone: '',
        email: '',
        notes: '',
    });

    // Form: Issue Interim Payment Certificate (IPC)
    const certForm = useForm({
        period_start: new Date(new Date().setDate(new Date().getDate() - 30)).toISOString().split('T')[0],
        period_end: new Date().toISOString().split('T')[0],
        work_description: '',
        gross_amount: '',
        retention_percent: '5.00',
    });

    const handleFilter = (e) => {
        if (e) e.preventDefault();
        router.get('/contracts/subcontractors', {
            q: search,
            project_id: projectFilter,
            status: statusFilter,
        }, {
            preserveState: true,
            preserveScroll: true,
        });
    };

    const handleCreateSubmit = (e) => {
        e.preventDefault();
        createForm.post('/contracts/subcontractors', {
            onSuccess: () => {
                setShowCreateModal(false);
                createForm.reset();
            },
        });
    };

    const handleCertSubmit = (e) => {
        e.preventDefault();
        if (!selectedSubForCert) return;
        certForm.post(`/contracts/subcontractors/${selectedSubForCert.id}/certificates`, {
            onSuccess: () => {
                setSelectedSubForCert(null);
                certForm.reset();
            },
        });
    };

    const subList = subcontractors?.data || [];

    // Live calculation for certificate modal
    const grossVal = parseFloat(certForm.data.gross_amount) || 0;
    const retentionRate = parseFloat(certForm.data.retention_percent) || 0;
    const computedRetention = (grossVal * (retentionRate / 100));
    const computedNet = Math.max(0, grossVal - computedRetention);

    return (
        <AuthenticatedLayout title="Subcontractor & Progress Billing" header="Contracts & Finance">
            <Head title="Subcontractors & Trade Contracts" />

            <div className="space-y-6">
                {/* Header */}
                <div className="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-2 border-b border-slate-200 dark:border-slate-800">
                    <div>
                        <h1 className="text-xl sm:text-2xl font-extrabold tracking-tight text-slate-900 dark:text-white">
                            Subcontractor Contracts & Work Measurement
                        </h1>
                        <p className="text-xs sm:text-sm text-slate-500 dark:text-slate-400 mt-0.5">
                            Manage specialty trade contracts, cumulative work certifications, and contractual retention deductions.
                        </p>
                    </div>

                    <button
                        onClick={() => setShowCreateModal(true)}
                        className="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs shadow-md shadow-blue-600/20 transition-colors cursor-pointer self-start sm:self-auto"
                    >
                        <PlusCircle className="w-4 h-4" />
                        <span>Register Trade Contract</span>
                    </button>
                </div>

                {/* Metric Strip */}
                <div className="grid grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4">
                    <div className="p-4 rounded-2xl bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 shadow-xs">
                        <div className="flex items-center gap-2 text-slate-400 text-xs font-semibold">
                            <Briefcase className="w-4 h-4 text-blue-500" />
                            <span>Trade Contracts</span>
                        </div>
                        <div className="text-xl font-black text-slate-900 dark:text-white mt-1 font-mono">
                            {totals.total_subcontractors || 0}
                        </div>
                        <div className="text-[10px] text-slate-400 mt-0.5">Specialty partners</div>
                    </div>

                    <div className="p-4 rounded-2xl bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 shadow-xs">
                        <div className="flex items-center gap-2 text-slate-400 text-xs font-semibold">
                            <DollarSign className="w-4 h-4 text-emerald-500" />
                            <span>Contracted Sum</span>
                        </div>
                        <div className="text-xl font-black text-slate-900 dark:text-white mt-1 font-mono">
                            ETB {((totals.total_contract_sum || 0) / 1000000).toFixed(2)}M
                        </div>
                        <div className="text-[10px] text-slate-400 mt-0.5">Total commitment value</div>
                    </div>

                    <div className="p-4 rounded-2xl bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 shadow-xs">
                        <div className="flex items-center gap-2 text-slate-400 text-xs font-semibold">
                            <FileCheck2 className="w-4 h-4 text-indigo-500" />
                            <span>Certified Works</span>
                        </div>
                        <div className="text-xl font-black text-indigo-600 dark:text-indigo-400 mt-1 font-mono">
                            ETB {((totals.total_certified_gross || 0) / 1000).toFixed(1)}K
                        </div>
                        <div className="text-[10px] text-slate-400 mt-0.5">Approved valuations</div>
                    </div>

                    <div className="p-4 rounded-2xl bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 shadow-xs">
                        <div className="flex items-center gap-2 text-slate-400 text-xs font-semibold">
                            <Shield className="w-4 h-4 text-amber-500" />
                            <span>Retention Held</span>
                        </div>
                        <div className="text-xl font-black text-amber-600 dark:text-amber-400 mt-1 font-mono">
                            ETB {((totals.total_retention_held || 0) / 1000).toFixed(1)}K
                        </div>
                        <div className="text-[10px] text-slate-400 mt-0.5">Security against defects</div>
                    </div>
                </div>

                {/* Filters */}
                <div className="rounded-2xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 p-4 shadow-xs">
                    <form onSubmit={handleFilter} className="grid grid-cols-1 sm:grid-cols-4 gap-3">
                        <div className="relative sm:col-span-2">
                            <Search className="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-400 pointer-events-none" />
                            <input
                                type="text"
                                placeholder="Search by contractor name, trade, contract number..."
                                value={search}
                                onChange={(e) => setSearch(e.target.value)}
                                className="w-full pl-9 pr-3 py-2 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-xs text-slate-900 dark:text-white"
                            />
                        </div>

                        <select
                            value={projectFilter}
                            onChange={(e) => setProjectFilter(e.target.value)}
                            className="w-full px-3 py-2 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-xs text-slate-900 dark:text-white"
                        >
                            <option value="">All Projects</option>
                            {projects.map((p) => (
                                <option key={p.id} value={p.id}>{p.name}</option>
                            ))}
                        </select>

                        <button
                            type="submit"
                            className="w-full py-2 rounded-xl bg-slate-900 hover:bg-black text-white dark:bg-slate-800 dark:hover:bg-slate-700 font-bold text-xs transition-colors cursor-pointer"
                        >
                            Filter Contracts
                        </button>
                    </form>
                </div>

                {/* Subcontractors Cards Grid */}
                <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                    {subList.length === 0 ? (
                        <div className="col-span-full py-12 text-center text-slate-400 text-xs bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800">
                            No trade subcontractors registered yet. Click "Register Trade Contract" to add one.
                        </div>
                    ) : (
                        subList.map((sub) => {
                            const contractSum = Number(sub.contract_sum) || 0;
                            const certified = Number(sub.total_certified) || 0;
                            const remaining = Number(sub.remaining_balance) || 0;
                            const percent = contractSum > 0 ? Math.min(100, Math.round((certified / contractSum) * 100)) : 0;
                            const certs = sub.certificates || [];

                            return (
                                <div
                                    key={sub.id}
                                    className="rounded-2xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 p-5 shadow-xs flex flex-col justify-between space-y-4"
                                >
                                    <div>
                                        <div className="flex items-start justify-between gap-2">
                                            <div>
                                                <h3 className="font-bold text-slate-900 dark:text-white text-sm">
                                                    {sub.name}
                                                </h3>
                                                <div className="text-[11px] text-blue-600 dark:text-blue-400 font-semibold">
                                                    {sub.trade}
                                                </div>
                                            </div>
                                            <span className="px-2 py-0.5 rounded-full text-[10px] font-mono font-bold bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300">
                                                {sub.contract_number}
                                            </span>
                                        </div>

                                        <div className="flex items-center gap-1.5 text-xs text-slate-500 mt-2">
                                            <Building2 className="w-3.5 h-3.5 text-slate-400 shrink-0" />
                                            <span className="truncate">{sub.project?.name || 'Corporate Multi-Site'}</span>
                                        </div>

                                        {/* Financial Progress */}
                                        <div className="mt-4 p-3 rounded-xl bg-slate-50 dark:bg-slate-800/60 space-y-2">
                                            <div className="flex justify-between items-center text-xs">
                                                <span className="text-slate-400">Contract Value:</span>
                                                <span className="font-mono font-bold text-slate-900 dark:text-white">
                                                    ETB {Number(contractSum).toLocaleString()}
                                                </span>
                                            </div>
                                            <div className="flex justify-between items-center text-xs">
                                                <span className="text-slate-400">Certified to Date:</span>
                                                <span className="font-mono font-bold text-indigo-600 dark:text-indigo-400">
                                                    ETB {Number(certified).toLocaleString()}
                                                </span>
                                            </div>
                                            <div className="flex justify-between items-center text-xs">
                                                <span className="text-slate-400">Remaining Commitment:</span>
                                                <span className="font-mono font-semibold text-slate-600 dark:text-slate-300">
                                                    ETB {Number(remaining).toLocaleString()}
                                                </span>
                                            </div>

                                            {/* Progress Bar */}
                                            <div className="pt-1">
                                                <div className="flex justify-between text-[10px] text-slate-400 mb-1 font-semibold">
                                                    <span>Financial Progress</span>
                                                    <span>{percent}%</span>
                                                </div>
                                                <div className="w-full h-1.5 rounded-full bg-slate-200 dark:bg-slate-700 overflow-hidden">
                                                    <div
                                                        className="h-full rounded-full bg-blue-600"
                                                        style={{ width: `${percent}%` }}
                                                    />
                                                </div>
                                            </div>
                                        </div>

                                        {/* Contact Person */}
                                        {sub.contact_person && (
                                            <div className="flex items-center gap-3 text-[11px] text-slate-500 mt-3">
                                                <span>👤 {sub.contact_person}</span>
                                                {sub.phone && <span>📞 {sub.phone}</span>}
                                            </div>
                                        )}
                                    </div>

                                    <div className="pt-3 border-t border-slate-100 dark:border-slate-800 flex items-center justify-between gap-2">
                                        <button
                                            type="button"
                                            onClick={() => setSelectedSubForView(sub)}
                                            className="text-xs font-bold text-slate-500 hover:text-slate-900 dark:hover:text-white transition-colors cursor-pointer"
                                        >
                                            View IPCs ({certs.length})
                                        </button>

                                        <button
                                            type="button"
                                            onClick={() => setSelectedSubForCert(sub)}
                                            className="inline-flex items-center gap-1 px-3 py-1.5 rounded-xl bg-blue-50 dark:bg-blue-950/50 hover:bg-blue-100 text-blue-700 dark:text-blue-300 text-xs font-bold transition-colors cursor-pointer"
                                        >
                                            <FileCheck2 className="w-3.5 h-3.5" />
                                            <span>Issue IPC</span>
                                        </button>
                                    </div>
                                </div>
                            );
                        })
                    )}
                </div>

                {/* Create Subcontractor Modal */}
                {showCreateModal && (
                    <div className="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-xs">
                        <div className="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-3xl p-6 w-full max-w-lg max-h-[90vh] overflow-y-auto space-y-4">
                            <div className="flex items-center justify-between pb-3 border-b border-slate-100 dark:border-slate-800">
                                <h3 className="font-extrabold text-slate-900 dark:text-white text-base">
                                    Register Specialty Trade Contract
                                </h3>
                                <button
                                    onClick={() => setShowCreateModal(false)}
                                    className="p-1 rounded-lg text-slate-400 hover:text-slate-600"
                                >
                                    <X className="w-4 h-4" />
                                </button>
                            </div>

                            <form onSubmit={handleCreateSubmit} className="space-y-4">
                                <div>
                                    <label className="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">
                                        Subcontractor / Trade Firm Name <span className="text-rose-500">*</span>
                                    </label>
                                    <input
                                        type="text"
                                        required
                                        value={createForm.data.name}
                                        onChange={(e) => createForm.setData('name', e.target.value)}
                                        placeholder="e.g. Abyssinia Steel Fixing PLC"
                                        className="w-full px-3 py-2 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-xs text-slate-900 dark:text-white"
                                    />
                                </div>

                                <div className="grid grid-cols-2 gap-3">
                                    <div>
                                        <label className="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">
                                            Specialty Trade <span className="text-rose-500">*</span>
                                        </label>
                                        <select
                                            value={createForm.data.trade}
                                            onChange={(e) => createForm.setData('trade', e.target.value)}
                                            className="w-full px-3 py-2 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-xs text-slate-900 dark:text-white"
                                        >
                                            <option value="Masonry & Concrete Works">Masonry & Concrete</option>
                                            <option value="Steel Fixing & Rebar">Steel Fixing & Rebar</option>
                                            <option value="Formwork & Scaffolding">Formwork & Scaffolding</option>
                                            <option value="Electrical Installation">Electrical Installation</option>
                                            <option value="Plumbing & Sanitary">Plumbing & Sanitary</option>
                                            <option value="Plastering & Painting">Plastering & Painting</option>
                                            <option value="Earthworks & Excavation">Earthworks & Excavation</option>
                                        </select>
                                    </div>

                                    <div>
                                        <label className="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">
                                            Assigned Project
                                        </label>
                                        <select
                                            value={createForm.data.project_id}
                                            onChange={(e) => createForm.setData('project_id', e.target.value)}
                                            className="w-full px-3 py-2 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-xs text-slate-900 dark:text-white"
                                        >
                                            <option value="">Company-Wide / Multiple</option>
                                            {projects.map((p) => (
                                                <option key={p.id} value={p.id}>{p.name}</option>
                                            ))}
                                        </select>
                                    </div>
                                </div>

                                <div className="grid grid-cols-2 gap-3">
                                    <div>
                                        <label className="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">
                                            Total Contract Sum (ETB) <span className="text-rose-500">*</span>
                                        </label>
                                        <input
                                            type="number"
                                            step="0.01"
                                            required
                                            value={createForm.data.contract_sum}
                                            onChange={(e) => createForm.setData('contract_sum', e.target.value)}
                                            placeholder="500,000.00"
                                            className="w-full px-3 py-2 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-xs text-slate-900 dark:text-white font-mono"
                                        />
                                    </div>

                                    <div>
                                        <label className="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">
                                            Contract Number
                                        </label>
                                        <input
                                            type="text"
                                            value={createForm.data.contract_number}
                                            onChange={(e) => createForm.setData('contract_number', e.target.value)}
                                            placeholder="Auto-generated if blank"
                                            className="w-full px-3 py-2 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-xs text-slate-900 dark:text-white"
                                        />
                                    </div>
                                </div>

                                <div className="grid grid-cols-2 gap-3">
                                    <div>
                                        <label className="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">
                                            Contact Person
                                        </label>
                                        <input
                                            type="text"
                                            value={createForm.data.contact_person}
                                            onChange={(e) => createForm.setData('contact_person', e.target.value)}
                                            className="w-full px-3 py-2 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-xs text-slate-900 dark:text-white"
                                        />
                                    </div>
                                    <div>
                                        <label className="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">
                                            Phone Number
                                        </label>
                                        <input
                                            type="text"
                                            value={createForm.data.phone}
                                            onChange={(e) => createForm.setData('phone', e.target.value)}
                                            placeholder="+251 9..."
                                            className="w-full px-3 py-2 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-xs text-slate-900 dark:text-white"
                                        />
                                    </div>
                                </div>

                                <div className="pt-3 border-t border-slate-100 dark:border-slate-800 flex items-center justify-end gap-2">
                                    <button
                                        type="button"
                                        onClick={() => setShowCreateModal(false)}
                                        className="px-4 py-2 rounded-xl border border-slate-200 dark:border-slate-700 text-slate-600 dark:text-slate-300 text-xs font-bold"
                                    >
                                        Cancel
                                    </button>
                                    <button
                                        type="submit"
                                        disabled={createForm.processing}
                                        className="px-5 py-2 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs cursor-pointer"
                                    >
                                        Register Contract
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                )}

                {/* Issue Interim Payment Certificate Modal */}
                {selectedSubForCert && (
                    <div className="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-xs">
                        <div className="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-3xl p-6 w-full max-w-lg space-y-4">
                            <div className="flex items-center justify-between pb-3 border-b border-slate-100 dark:border-slate-800">
                                <div>
                                    <h3 className="font-extrabold text-slate-900 dark:text-white text-base">
                                        Issue Interim Payment Certificate (IPC)
                                    </h3>
                                    <p className="text-xs text-slate-400">
                                        {selectedSubForCert.name} • {selectedSubForCert.trade}
                                    </p>
                                </div>
                                <button
                                    onClick={() => setSelectedSubForCert(null)}
                                    className="p-1 rounded-lg text-slate-400 hover:text-slate-600"
                                >
                                    <X className="w-4 h-4" />
                                </button>
                            </div>

                            <form onSubmit={handleCertSubmit} className="space-y-4">
                                <div className="grid grid-cols-2 gap-3">
                                    <div>
                                        <label className="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">
                                            Period Start
                                        </label>
                                        <input
                                            type="date"
                                            required
                                            value={certForm.data.period_start}
                                            onChange={(e) => certForm.setData('period_start', e.target.value)}
                                            className="w-full px-3 py-2 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-xs text-slate-900 dark:text-white"
                                        />
                                    </div>
                                    <div>
                                        <label className="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">
                                            Period End
                                        </label>
                                        <input
                                            type="date"
                                            required
                                            value={certForm.data.period_end}
                                            onChange={(e) => certForm.setData('period_end', e.target.value)}
                                            className="w-full px-3 py-2 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-xs text-slate-900 dark:text-white"
                                        />
                                    </div>
                                </div>

                                <div>
                                    <label className="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">
                                        Work Measured & Items Executed <span className="text-rose-500">*</span>
                                    </label>
                                    <textarea
                                        rows={3}
                                        required
                                        value={certForm.data.work_description}
                                        onChange={(e) => certForm.setData('work_description', e.target.value)}
                                        placeholder="e.g. 450 m2 Hollow Concrete Block (HCB) wall at 2nd floor, curing completed, joint inspections passed."
                                        className="w-full px-3 py-2 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-xs text-slate-900 dark:text-white"
                                    />
                                </div>

                                <div className="grid grid-cols-2 gap-3">
                                    <div>
                                        <label className="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">
                                            Gross Valuation Amount (ETB) <span className="text-rose-500">*</span>
                                        </label>
                                        <input
                                            type="number"
                                            step="0.01"
                                            required
                                            value={certForm.data.gross_amount}
                                            onChange={(e) => certForm.setData('gross_amount', e.target.value)}
                                            placeholder="100,000.00"
                                            className="w-full px-3 py-2 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-xs text-slate-900 dark:text-white font-mono"
                                        />
                                    </div>
                                    <div>
                                        <label className="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">
                                            Retention Deducted (%)
                                        </label>
                                        <input
                                            type="number"
                                            step="0.1"
                                            min="0"
                                            max="100"
                                            value={certForm.data.retention_percent}
                                            onChange={(e) => certForm.setData('retention_percent', e.target.value)}
                                            className="w-full px-3 py-2 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-xs text-slate-900 dark:text-white font-mono"
                                        />
                                    </div>
                                </div>

                                {/* Live Breakdown Box */}
                                <div className="p-3.5 rounded-2xl bg-slate-50 dark:bg-slate-800/80 border border-slate-200 dark:border-slate-700 space-y-1.5 text-xs">
                                    <div className="flex justify-between text-slate-500">
                                        <span>Gross Work Valuation:</span>
                                        <span className="font-mono font-bold text-slate-900 dark:text-white">
                                            ETB {grossVal.toLocaleString(undefined, { minimumFractionDigits: 2 })}
                                        </span>
                                    </div>
                                    <div className="flex justify-between text-amber-600 dark:text-amber-400">
                                        <span>Less Retention ({retentionRate}%):</span>
                                        <span className="font-mono font-bold">
                                            - ETB {computedRetention.toLocaleString(undefined, { minimumFractionDigits: 2 })}
                                        </span>
                                    </div>
                                    <div className="pt-1.5 border-t border-slate-200 dark:border-slate-700 flex justify-between font-black text-emerald-600 dark:text-emerald-400 text-sm">
                                        <span>Net Certified Payable:</span>
                                        <span className="font-mono">
                                            ETB {computedNet.toLocaleString(undefined, { minimumFractionDigits: 2 })}
                                        </span>
                                    </div>
                                </div>

                                <div className="pt-3 border-t border-slate-100 dark:border-slate-800 flex items-center justify-end gap-2">
                                    <button
                                        type="button"
                                        onClick={() => setSelectedSubForCert(null)}
                                        className="px-4 py-2 rounded-xl border border-slate-200 dark:border-slate-700 text-slate-600 dark:text-slate-300 text-xs font-bold"
                                    >
                                        Cancel
                                    </button>
                                    <button
                                        type="submit"
                                        disabled={certForm.processing}
                                        className="px-5 py-2 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs cursor-pointer shadow-md shadow-blue-600/20"
                                    >
                                        Authorize & Issue IPC
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                )}

                {/* View Past Certificates Modal */}
                {selectedSubForView && (
                    <div className="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-xs">
                        <div className="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-3xl p-6 w-full max-w-2xl max-h-[90vh] overflow-y-auto space-y-4">
                            <div className="flex items-center justify-between pb-3 border-b border-slate-100 dark:border-slate-800">
                                <div>
                                    <h3 className="font-extrabold text-slate-900 dark:text-white text-base">
                                        Payment Certificates Registry
                                    </h3>
                                    <p className="text-xs text-slate-400">
                                        {selectedSubForView.name} • {selectedSubForView.contract_number}
                                    </p>
                                </div>
                                <button
                                    onClick={() => setSelectedSubForView(null)}
                                    className="p-1 rounded-lg text-slate-400 hover:text-slate-600"
                                >
                                    <X className="w-4 h-4" />
                                </button>
                            </div>

                            {(!selectedSubForView.certificates || selectedSubForView.certificates.length === 0) ? (
                                <div className="py-8 text-center text-xs text-slate-400">
                                    No payment certificates issued yet for this subcontractor contract.
                                </div>
                            ) : (
                                <div className="space-y-3">
                                    {selectedSubForView.certificates.map((cert) => (
                                        <div
                                            key={cert.id}
                                            className="p-3.5 rounded-2xl border border-slate-200 dark:border-slate-800 bg-slate-50/60 dark:bg-slate-800/40 space-y-2 text-xs"
                                        >
                                            <div className="flex items-center justify-between">
                                                <div className="font-mono font-bold text-blue-600 dark:text-blue-400">
                                                    #{cert.certificate_number}
                                                </div>
                                                <span className="px-2 py-0.5 rounded-full text-[10px] font-bold uppercase bg-emerald-50 text-emerald-700 dark:bg-emerald-950/50 dark:text-emerald-300">
                                                    {cert.status}
                                                </span>
                                            </div>

                                            <p className="text-slate-700 dark:text-slate-300 text-[11px] leading-relaxed">
                                                {cert.work_description}
                                            </p>

                                            <div className="grid grid-cols-3 gap-2 pt-1 border-t border-slate-200/60 dark:border-slate-700/60 font-mono text-[11px]">
                                                <div>
                                                    <span className="text-slate-400 block text-[9px]">Gross</span>
                                                    <span className="font-bold">ETB {Number(cert.gross_amount).toLocaleString()}</span>
                                                </div>
                                                <div>
                                                    <span className="text-slate-400 block text-[9px]">Retention ({cert.retention_percent}%)</span>
                                                    <span className="font-bold text-amber-600">ETB {Number(cert.retention_amount).toLocaleString()}</span>
                                                </div>
                                                <div>
                                                    <span className="text-slate-400 block text-[9px]">Net Payable</span>
                                                    <span className="font-bold text-emerald-600">ETB {Number(cert.net_payable).toLocaleString()}</span>
                                                </div>
                                            </div>
                                        </div>
                                    ))}
                                </div>
                            )}

                            <div className="pt-3 border-t border-slate-100 dark:border-slate-800 text-right">
                                <button
                                    onClick={() => setSelectedSubForView(null)}
                                    className="px-4 py-2 rounded-xl bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 font-bold text-xs"
                                >
                                    Close
                                </button>
                            </div>
                        </div>
                    </div>
                )}
            </div>
        </AuthenticatedLayout>
    );
}
