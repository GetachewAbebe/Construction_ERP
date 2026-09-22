import { useState } from 'react';
import { Head, Link, useForm, router } from '@inertiajs/react';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import {
    Briefcase,
    ArrowLeft,
    MapPin,
    Calendar,
    DollarSign,
    Edit,
    PlusCircle,
    Receipt,
    Clock,
    CheckCircle2,
    XCircle,
    AlertTriangle,
    Layers,
    TrendingUp,
    Flag,
    Trash2,
    Sliders,
    ChevronRight,
    Target,
    Activity,
} from 'lucide-react';

export default function Show({ project }) {
    const [activeTab, setActiveTab] = useState('wbs'); // 'wbs' | 'expenses' | 'overview'
    const [isAddModalOpen, setIsAddModalOpen] = useState(false);
    const [editingMilestone, setEditingMilestone] = useState(null);
    const [quickProgressMilestone, setQuickProgressMilestone] = useState(null);
    const [quickProgressValue, setQuickProgressValue] = useState(0);

    const budget = Number(project.budget) || 0;
    const expenses = project.expenses || [];
    const milestones = project.milestones || [];
    const physicalProgress = Number(project.physical_progress_percentage) || 0;
    const milestoneSummary = project.milestone_summary || {
        total: milestones.length,
        completed: milestones.filter((m) => m.status === 'completed').length,
        in_progress: milestones.filter((m) => m.status === 'in_progress').length,
        delayed: milestones.filter((m) => m.status === 'delayed' || m.is_overdue).length,
        pending: milestones.filter((m) => m.status === 'pending').length,
    };

    const approvedSpent = expenses
        .filter((e) => e.status === 'approved')
        .reduce((sum, e) => sum + (Number(e.amount) || 0), 0);

    const pendingSpent = expenses
        .filter((e) => e.status === 'pending')
        .reduce((sum, e) => sum + (Number(e.amount) || 0), 0);

    const remaining = Math.max(budget - (approvedSpent + pendingSpent), 0);
    const consumptionPct = budget > 0 ? Math.min(Math.round((approvedSpent / budget) * 100), 100) : 0;

    // Progress vs Burn Variance
    const progressVariance = Math.round((physicalProgress - consumptionPct) * 10) / 10;

    // Add Milestone Form
    const {
        data: addData,
        setData: setAddData,
        post: postMilestone,
        processing: addProcessing,
        errors: addErrors,
        reset: resetAddForm,
    } = useForm({
        title: '',
        wbs_code: '',
        description: '',
        start_date: '',
        due_date: '',
        progress: 0,
        weight_pct: 0,
        allocated_budget: '',
        status: 'pending',
    });

    // Edit Milestone Form
    const {
        data: editData,
        setData: setEditData,
        put: putMilestone,
        processing: editProcessing,
        errors: editErrors,
        reset: resetEditForm,
    } = useForm({
        title: '',
        wbs_code: '',
        description: '',
        start_date: '',
        due_date: '',
        progress: 0,
        weight_pct: 0,
        allocated_budget: '',
        status: 'pending',
    });

    const handleCreateMilestone = (e) => {
        e.preventDefault();
        postMilestone(`/finance/projects/${project.id}/milestones`, {
            preserveScroll: true,
            onSuccess: () => {
                setIsAddModalOpen(false);
                resetAddForm();
            },
        });
    };

    const handleOpenEdit = (m) => {
        setEditingMilestone(m);
        setEditData({
            title: m.title || '',
            wbs_code: m.wbs_code || '',
            description: m.description || '',
            start_date: m.start_date ? m.start_date.substring(0, 10) : '',
            due_date: m.due_date ? m.due_date.substring(0, 10) : '',
            progress: m.progress ?? 0,
            weight_pct: m.weight_pct ?? 0,
            allocated_budget: m.allocated_budget ?? '',
            status: m.status || 'pending',
        });
    };

    const handleUpdateMilestone = (e) => {
        e.preventDefault();
        if (!editingMilestone) return;

        putMilestone(`/finance/projects/${project.id}/milestones/${editingMilestone.id}`, {
            preserveScroll: true,
            onSuccess: () => {
                setEditingMilestone(null);
                resetEditForm();
            },
        });
    };

    const handleDeleteMilestone = (milestoneId, title) => {
        if (!confirm(`Are you sure you want to remove milestone "${title}" from the WBS?`)) {
            return;
        }

        router.delete(`/finance/projects/${project.id}/milestones/${milestoneId}`, {
            preserveScroll: true,
        });
    };

    const handleSaveQuickProgress = (e) => {
        e.preventDefault();
        if (!quickProgressMilestone) return;

        router.patch(
            `/finance/projects/${project.id}/milestones/${quickProgressMilestone.id}/progress`,
            { progress: quickProgressValue },
            {
                preserveScroll: true,
                onSuccess: () => setQuickProgressMilestone(null),
            }
        );
    };

    const getStatusBadge = (status, isOverdue) => {
        if (status === 'completed') {
            return (
                <span className="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-emerald-50 dark:bg-emerald-950/50 text-emerald-600 dark:text-emerald-400 border border-emerald-200 dark:border-emerald-900">
                    <CheckCircle2 className="w-3 h-3" /> Completed
                </span>
            );
        }
        if (status === 'delayed' || isOverdue) {
            return (
                <span className="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-rose-50 dark:bg-rose-950/50 text-rose-600 dark:text-rose-400 border border-rose-200 dark:border-rose-900">
                    <AlertTriangle className="w-3 h-3" /> Delayed
                </span>
            );
        }
        if (status === 'in_progress') {
            return (
                <span className="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-blue-50 dark:bg-blue-950/50 text-blue-600 dark:text-blue-400 border border-blue-200 dark:border-blue-900">
                    <Activity className="w-3 h-3" /> In Progress
                </span>
            );
        }
        return (
            <span className="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-slate-100 text-slate-600 dark:bg-slate-800 dark:text-slate-400 border border-slate-200 dark:border-slate-700">
                <Clock className="w-3 h-3" /> Pending
            </span>
        );
    };

    return (
        <AuthenticatedLayout title={project.name} header="Finance & Operations">
            <Head title={`${project.name} - Project WBS & Financials`} />

            <div className="space-y-6">
                {/* Header */}
                <div className="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-2 border-b border-slate-200 dark:border-slate-800">
                    <div className="flex items-center gap-3">
                        <Link
                            href="/finance/projects"
                            className="p-2 rounded-xl bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 text-slate-600 dark:text-slate-300 transition-colors"
                        >
                            <ArrowLeft className="w-4 h-4" />
                        </Link>
                        <div>
                            <div className="flex items-center gap-2">
                                <h1 className="text-xl font-extrabold tracking-tight text-slate-900 dark:text-white">
                                    {project.name}
                                </h1>
                                <span className={`px-2.5 py-0.5 rounded-full text-[10px] font-bold ${
                                    project.status === 'In Progress' || project.status === 'in_progress'
                                        ? 'bg-emerald-50 dark:bg-emerald-950/50 text-emerald-600 dark:text-emerald-400 border border-emerald-200 dark:border-emerald-900'
                                        : project.status === 'Completed' || project.status === 'completed'
                                        ? 'bg-blue-50 dark:bg-blue-950/50 text-blue-600 dark:text-blue-400 border border-blue-200 dark:border-blue-900'
                                        : 'bg-slate-100 text-slate-600 dark:bg-slate-800 dark:text-slate-400'
                                }`}>
                                    {project.status}
                                </span>
                            </div>
                            <div className="flex items-center gap-1 text-xs text-slate-400 mt-0.5">
                                <MapPin className="w-3.5 h-3.5 shrink-0" />
                                <span>{project.location || 'Addis Ababa HQ'}</span>
                            </div>
                        </div>
                    </div>

                    <div className="flex items-center gap-2">
                        <button
                            type="button"
                            onClick={() => setIsAddModalOpen(true)}
                            className="inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-xl bg-blue-900 hover:bg-blue-950 text-white font-bold text-xs shadow-sm transition-colors cursor-pointer"
                        >
                            <PlusCircle className="w-4 h-4" />
                            <span>Add WBS Milestone</span>
                        </button>
                        <Link
                            href="/finance/expenses/create"
                            className="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs font-semibold text-slate-700 dark:text-slate-200 hover:bg-slate-50 dark:hover:bg-slate-700 transition-colors"
                        >
                            <Receipt className="w-3.5 h-3.5" />
                            <span>Log Expense</span>
                        </Link>
                        <Link
                            href={`/finance/projects/${project.id}/edit`}
                            className="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs font-semibold text-slate-700 dark:text-slate-200 hover:bg-slate-50 dark:hover:bg-slate-700 transition-colors"
                        >
                            <Edit className="w-3.5 h-3.5" />
                            <span>Edit</span>
                        </Link>
                    </div>
                </div>

                {/* Construction Performance KPIs: Physical vs Financial */}
                <div className="grid grid-cols-1 md:grid-cols-3 gap-4">
                    {/* 1. Physical WBS Progress */}
                    <div className="rounded-2xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 p-5 shadow-xs">
                        <div className="flex items-center justify-between">
                            <span className="text-[11px] font-bold text-slate-400 uppercase tracking-wider">
                                Physical Progress (WBS)
                            </span>
                            <span className="p-1.5 rounded-lg bg-blue-50 dark:bg-blue-950/50 text-blue-600 dark:text-blue-400">
                                <Flag className="w-4 h-4" />
                            </span>
                        </div>
                        <div className="flex items-baseline gap-2 mt-2">
                            <span className="text-3xl font-black text-slate-900 dark:text-white font-mono">
                                {physicalProgress}%
                            </span>
                            <span className="text-xs text-slate-500 font-medium">
                                of weighted scope
                            </span>
                        </div>
                        <div className="mt-3 h-2 w-full bg-slate-100 dark:bg-slate-800 rounded-full overflow-hidden">
                            <div
                                className="h-full rounded-full bg-blue-900 dark:bg-blue-500 transition-all duration-500"
                                style={{ width: `${Math.min(100, physicalProgress)}%` }}
                            />
                        </div>
                        <div className="flex items-center justify-between text-[11px] text-slate-400 mt-2 font-medium">
                            <span>{milestoneSummary.completed} of {milestoneSummary.total} completed</span>
                            {milestoneSummary.delayed > 0 && (
                                <span className="text-rose-500 font-bold flex items-center gap-1">
                                    <AlertTriangle className="w-3 h-3" /> {milestoneSummary.delayed} delayed
                                </span>
                            )}
                        </div>
                    </div>

                    {/* 2. Capital Absorption & Budget */}
                    <div className="rounded-2xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 p-5 shadow-xs">
                        <div className="flex items-center justify-between">
                            <span className="text-[11px] font-bold text-slate-400 uppercase tracking-wider">
                                Capital Absorption (Financial)
                            </span>
                            <span className="p-1.5 rounded-lg bg-emerald-50 dark:bg-emerald-950/50 text-emerald-600 dark:text-emerald-400">
                                <DollarSign className="w-4 h-4" />
                            </span>
                        </div>
                        <div className="flex items-baseline gap-2 mt-2">
                            <span className="text-3xl font-black text-slate-900 dark:text-white font-mono">
                                {consumptionPct}%
                            </span>
                            <span className="text-xs text-slate-500 font-medium">
                                of authorized funds
                            </span>
                        </div>
                        <div className="mt-3 h-2 w-full bg-slate-100 dark:bg-slate-800 rounded-full overflow-hidden">
                            <div
                                className={`h-full rounded-full transition-all duration-500 ${
                                    consumptionPct > 90 ? 'bg-rose-500' : consumptionPct > 70 ? 'bg-amber-500' : 'bg-emerald-600'
                                }`}
                                style={{ width: `${consumptionPct}%` }}
                            />
                        </div>
                        <div className="flex items-center justify-between text-[11px] text-slate-400 mt-2 font-medium">
                            <span>Spent: ETB {approvedSpent.toLocaleString(undefined, { minimumFractionDigits: 0 })}</span>
                            <span>Budget: ETB {budget.toLocaleString(undefined, { minimumFractionDigits: 0 })}</span>
                        </div>
                    </div>

                    {/* 3. Earned Value & Schedule Variance */}
                    <div className="rounded-2xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 p-5 shadow-xs">
                        <div className="flex items-center justify-between">
                            <span className="text-[11px] font-bold text-slate-400 uppercase tracking-wider">
                                Project Earned Value Health
                            </span>
                            <span className={`p-1.5 rounded-lg ${
                                progressVariance >= 0
                                    ? 'bg-emerald-50 dark:bg-emerald-950/50 text-emerald-600 dark:text-emerald-400'
                                    : 'bg-rose-50 dark:bg-rose-950/50 text-rose-600 dark:text-rose-400'
                            }`}>
                                <TrendingUp className="w-4 h-4" />
                            </span>
                        </div>
                        <div className="flex items-baseline gap-2 mt-2">
                            <span className={`text-3xl font-black font-mono ${
                                progressVariance >= 0 ? 'text-emerald-600 dark:text-emerald-400' : 'text-rose-600 dark:text-rose-400'
                            }`}>
                                {progressVariance > 0 ? `+${progressVariance}%` : `${progressVariance}%`}
                            </span>
                            <span className="text-xs text-slate-500 font-medium">
                                physical vs financial variance
                            </span>
                        </div>
                        <div className="mt-4 text-xs text-slate-600 dark:text-slate-300">
                            {progressVariance >= 0 ? (
                                <p className="leading-snug">
                                    <strong className="text-emerald-600 dark:text-emerald-400 font-bold">Optimal Cost Efficiency:</strong> Physical delivery leads expenditure burn rate.
                                </p>
                            ) : (
                                <p className="leading-snug">
                                    <strong className="text-rose-600 dark:text-rose-400 font-bold">Cost Over-Absorption Risk:</strong> Expenditure burn is outrunning certified physical completion.
                                </p>
                            )}
                        </div>
                    </div>
                </div>

                {/* Tabs Navigation */}
                <div className="flex items-center gap-2 border-b border-slate-200 dark:border-slate-800">
                    <button
                        type="button"
                        onClick={() => setActiveTab('wbs')}
                        className={`flex items-center gap-2 px-4 py-2.5 text-xs font-bold border-b-2 transition-colors cursor-pointer ${
                            activeTab === 'wbs'
                                ? 'border-blue-900 text-blue-900 dark:border-blue-400 dark:text-blue-400'
                                : 'border-transparent text-slate-500 hover:text-slate-800 dark:hover:text-slate-200'
                        }`}
                    >
                        <Layers className="w-4 h-4" />
                        <span>Work Breakdown Structure (WBS)</span>
                        <span className="ml-1 px-1.5 py-0.5 rounded-full text-[10px] bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300">
                            {milestones.length}
                        </span>
                    </button>

                    <button
                        type="button"
                        onClick={() => setActiveTab('expenses')}
                        className={`flex items-center gap-2 px-4 py-2.5 text-xs font-bold border-b-2 transition-colors cursor-pointer ${
                            activeTab === 'expenses'
                                ? 'border-blue-900 text-blue-900 dark:border-blue-400 dark:text-blue-400'
                                : 'border-transparent text-slate-500 hover:text-slate-800 dark:hover:text-slate-200'
                        }`}
                    >
                        <Receipt className="w-4 h-4" />
                        <span>Expenditure Ledger</span>
                        <span className="ml-1 px-1.5 py-0.5 rounded-full text-[10px] bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300">
                            {expenses.length}
                        </span>
                    </button>

                    <button
                        type="button"
                        onClick={() => setActiveTab('overview')}
                        className={`flex items-center gap-2 px-4 py-2.5 text-xs font-bold border-b-2 transition-colors cursor-pointer ${
                            activeTab === 'overview'
                                ? 'border-blue-900 text-blue-900 dark:border-blue-400 dark:text-blue-400'
                                : 'border-transparent text-slate-500 hover:text-slate-800 dark:hover:text-slate-200'
                        }`}
                    >
                        <Briefcase className="w-4 h-4" />
                        <span>Project Parameters & Budget</span>
                    </button>
                </div>

                {/* TAB 1: WORK BREAKDOWN STRUCTURE (WBS) */}
                {activeTab === 'wbs' && (
                    <div className="space-y-6">
                        {/* WBS Phase Stepper / Timeline */}
                        {milestones.length > 0 && (
                            <div className="rounded-2xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 p-5 shadow-xs">
                                <div className="flex items-center justify-between mb-4">
                                    <h3 className="text-xs font-bold text-slate-400 uppercase tracking-wider">
                                        Phase Sequence Timeline
                                    </h3>
                                    <span className="text-[11px] text-slate-500">
                                        Sequential chronological flow
                                    </span>
                                </div>

                                <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3">
                                    {milestones.map((m, index) => (
                                        <div
                                            key={m.id}
                                            className={`rounded-xl border p-3 transition-all ${
                                                m.status === 'completed'
                                                    ? 'border-emerald-200 dark:border-emerald-900/60 bg-emerald-50/20 dark:bg-emerald-950/10'
                                                    : m.status === 'delayed' || m.is_overdue
                                                    ? 'border-rose-200 dark:border-rose-900/60 bg-rose-50/20 dark:bg-rose-950/10'
                                                    : m.status === 'in_progress'
                                                    ? 'border-blue-300 dark:border-blue-800 bg-blue-50/20 dark:bg-blue-950/10 ring-1 ring-blue-500/20'
                                                    : 'border-slate-200 dark:border-slate-800 bg-slate-50/50 dark:bg-slate-800/30'
                                            }`}
                                        >
                                            <div className="flex items-center justify-between text-[11px] font-mono text-slate-400 mb-1">
                                                <span>{m.wbs_code || `P${index + 1}`}</span>
                                                <span className="font-bold text-slate-700 dark:text-slate-300">
                                                    {m.progress}%
                                                </span>
                                            </div>
                                            <h4 className="text-xs font-bold text-slate-900 dark:text-white truncate" title={m.title}>
                                                {m.title}
                                            </h4>
                                            <div className="mt-2 h-1.5 w-full bg-slate-200 dark:bg-slate-700 rounded-full overflow-hidden">
                                                <div
                                                    className={`h-full rounded-full ${
                                                        m.status === 'completed'
                                                            ? 'bg-emerald-500'
                                                            : m.status === 'delayed' || m.is_overdue
                                                            ? 'bg-rose-500'
                                                            : 'bg-blue-600'
                                                    }`}
                                                    style={{ width: `${m.progress}%` }}
                                                />
                                            </div>
                                            <div className="flex items-center justify-between text-[10px] text-slate-400 mt-2">
                                                <span>{m.due_date ? new Date(m.due_date).toLocaleDateString() : 'No deadline'}</span>
                                                <span>Weight: {m.weight_pct}%</span>
                                            </div>
                                        </div>
                                    ))}
                                </div>
                            </div>
                        )}

                        {/* Interactive Milestones Table */}
                        <div className="rounded-2xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 shadow-xs overflow-hidden">
                            <div className="px-5 py-4 border-b border-slate-100 dark:border-slate-800 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                                <div>
                                    <h2 className="text-sm font-bold text-slate-900 dark:text-white">
                                        Milestones & Work Breakdown Structure ({milestones.length})
                                    </h2>
                                    <p className="text-xs text-slate-400">
                                        Define phases, manage completion percentages, and track on-site deadlines.
                                    </p>
                                </div>

                                <button
                                    type="button"
                                    onClick={() => setIsAddModalOpen(true)}
                                    className="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-blue-900 hover:bg-blue-950 text-white font-bold text-xs shadow-sm transition-colors cursor-pointer self-start sm:self-auto"
                                >
                                    <PlusCircle className="w-3.5 h-3.5" />
                                    <span>New Phase</span>
                                </button>
                            </div>

                            <div className="overflow-x-auto">
                                <table className="w-full text-left text-xs">
                                    <thead className="bg-slate-50 dark:bg-slate-800/60 text-slate-500 uppercase font-semibold text-[10px] tracking-wider border-b border-slate-200 dark:border-slate-800">
                                        <tr>
                                            <th className="py-3 px-4">WBS Code & Milestone Title</th>
                                            <th className="py-3 px-4">Schedule Window</th>
                                            <th className="py-3 px-4">Weight %</th>
                                            <th className="py-3 px-4">Allocated Budget</th>
                                            <th className="py-3 px-4">Physical Completion</th>
                                            <th className="py-3 px-4">Status</th>
                                            <th className="py-3 px-4 text-right">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody className="divide-y divide-slate-100 dark:divide-slate-800/80">
                                        {milestones.length === 0 ? (
                                            <tr>
                                                <td colSpan={7} className="py-12 text-center text-slate-400 text-xs">
                                                    <div className="flex flex-col items-center justify-center gap-2">
                                                        <Layers className="w-8 h-8 text-slate-300 dark:text-slate-600" />
                                                        <span className="font-semibold text-slate-600 dark:text-slate-300">
                                                            No Work Breakdown Structure (WBS) phases defined.
                                                        </span>
                                                        <span className="text-[11px] text-slate-400 max-w-sm">
                                                            Break down this project into phases (e.g. Substructure, Framing, MEP, Finishing) to track delivery.
                                                        </span>
                                                        <button
                                                            type="button"
                                                            onClick={() => setIsAddModalOpen(true)}
                                                            className="mt-2 inline-flex items-center gap-1 px-3 py-1.5 rounded-xl bg-blue-900 hover:bg-blue-950 text-white font-bold text-xs shadow-sm cursor-pointer"
                                                        >
                                                            <PlusCircle className="w-3.5 h-3.5" />
                                                            <span>Add First Milestone</span>
                                                        </button>
                                                    </div>
                                                </td>
                                            </tr>
                                        ) : (
                                            milestones.map((m) => (
                                                <tr key={m.id} className="hover:bg-slate-50/70 dark:hover:bg-slate-800/40 transition-colors">
                                                    <td className="py-3.5 px-4">
                                                        <div className="flex items-center gap-2">
                                                            {m.wbs_code && (
                                                                <span className="px-1.5 py-0.5 rounded-md bg-slate-100 dark:bg-slate-800 font-mono text-[10px] font-bold text-slate-600 dark:text-slate-300">
                                                                    {m.wbs_code}
                                                                </span>
                                                            )}
                                                            <div className="font-bold text-slate-900 dark:text-white">
                                                                {m.title}
                                                            </div>
                                                        </div>
                                                        {m.description && (
                                                            <div className="text-[11px] text-slate-400 truncate max-w-xs mt-0.5">
                                                                {m.description}
                                                            </div>
                                                        )}
                                                    </td>
                                                    <td className="py-3.5 px-4 font-mono text-[11px] text-slate-600 dark:text-slate-400">
                                                        <div>{m.start_date ? new Date(m.start_date).toLocaleDateString() : '—'}</div>
                                                        <div className="text-[10px] text-slate-400">
                                                            due {m.due_date ? new Date(m.due_date).toLocaleDateString() : '—'}
                                                        </div>
                                                    </td>
                                                    <td className="py-3.5 px-4 font-mono font-bold text-slate-700 dark:text-slate-300">
                                                        {m.weight_pct}%
                                                    </td>
                                                    <td className="py-3.5 px-4 font-mono text-slate-700 dark:text-slate-300">
                                                        {m.allocated_budget
                                                            ? `ETB ${Number(m.allocated_budget).toLocaleString(undefined, { minimumFractionDigits: 0 })}`
                                                            : '—'}
                                                    </td>
                                                    <td className="py-3.5 px-4 w-44">
                                                        <div className="flex items-center justify-between text-[11px] mb-1">
                                                            <span className="font-mono font-bold text-slate-900 dark:text-white">
                                                                {m.progress}%
                                                            </span>
                                                            <button
                                                                type="button"
                                                                onClick={() => {
                                                                    setQuickProgressMilestone(m);
                                                                    setQuickProgressValue(m.progress);
                                                                }}
                                                                className="text-[10px] text-blue-600 dark:text-blue-400 hover:underline flex items-center gap-0.5 cursor-pointer"
                                                            >
                                                                <Sliders className="w-3 h-3" /> Adjust
                                                            </button>
                                                        </div>
                                                        <div className="h-2 w-full bg-slate-100 dark:bg-slate-800 rounded-full overflow-hidden">
                                                            <div
                                                                className={`h-full rounded-full ${
                                                                    m.status === 'completed'
                                                                        ? 'bg-emerald-500'
                                                                        : m.status === 'delayed' || m.is_overdue
                                                                        ? 'bg-rose-500'
                                                                        : 'bg-blue-600'
                                                                }`}
                                                                style={{ width: `${m.progress}%` }}
                                                            />
                                                        </div>
                                                    </td>
                                                    <td className="py-3.5 px-4">
                                                        {getStatusBadge(m.status, m.is_overdue)}
                                                    </td>
                                                    <td className="py-3.5 px-4 text-right">
                                                        <div className="flex items-center justify-end gap-1.5">
                                                            <button
                                                                type="button"
                                                                onClick={() => handleOpenEdit(m)}
                                                                className="p-1 rounded-lg hover:bg-slate-100 dark:hover:bg-slate-800 text-slate-600 dark:text-slate-300 transition-colors cursor-pointer"
                                                                title="Edit Phase"
                                                            >
                                                                <Edit className="w-3.5 h-3.5" />
                                                            </button>
                                                            <button
                                                                type="button"
                                                                onClick={() => handleDeleteMilestone(m.id, m.title)}
                                                                className="p-1 rounded-lg hover:bg-rose-50 dark:hover:bg-rose-950/50 text-rose-500 transition-colors cursor-pointer"
                                                                title="Delete Phase"
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
                        </div>
                    </div>
                )}

                {/* TAB 2: EXPENDITURE JOURNAL */}
                {activeTab === 'expenses' && (
                    <div className="space-y-6">
                        <div className="grid grid-cols-1 sm:grid-cols-4 gap-4">
                            <div className="rounded-2xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 p-4 shadow-xs">
                                <span className="text-[11px] font-semibold text-slate-400 uppercase tracking-wider block">Authorized Budget</span>
                                <div className="text-xl font-black text-slate-900 dark:text-white mt-1 font-mono">
                                    ETB {budget.toLocaleString(undefined, { minimumFractionDigits: 2 })}
                                </div>
                            </div>

                            <div className="rounded-2xl border border-emerald-200 dark:border-emerald-900/50 bg-emerald-50/40 dark:bg-emerald-950/20 p-4">
                                <span className="text-[11px] font-semibold text-emerald-600 dark:text-emerald-400 uppercase tracking-wider block">Approved Spent</span>
                                <div className="text-xl font-black text-emerald-700 dark:text-emerald-300 mt-1 font-mono">
                                    ETB {approvedSpent.toLocaleString(undefined, { minimumFractionDigits: 2 })}
                                </div>
                            </div>

                            <div className="rounded-2xl border border-amber-200 dark:border-amber-900/50 bg-amber-50/40 dark:bg-amber-950/20 p-4">
                                <span className="text-[11px] font-semibold text-amber-600 dark:text-amber-400 uppercase tracking-wider block">Pending Approvals</span>
                                <div className="text-xl font-black text-amber-700 dark:text-amber-300 mt-1 font-mono">
                                    ETB {pendingSpent.toLocaleString(undefined, { minimumFractionDigits: 2 })}
                                </div>
                            </div>

                            <div className="rounded-2xl border border-blue-200 dark:border-blue-900/50 bg-blue-50/40 dark:bg-blue-950/20 p-4">
                                <span className="text-[11px] font-semibold text-blue-600 dark:text-blue-400 uppercase tracking-wider block">Remaining Balance</span>
                                <div className="text-xl font-black text-blue-900 dark:text-blue-200 mt-1 font-mono">
                                    ETB {remaining.toLocaleString(undefined, { minimumFractionDigits: 2 })}
                                </div>
                            </div>
                        </div>

                        {/* Expenses Table */}
                        <div className="rounded-2xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 shadow-xs overflow-hidden">
                            <div className="px-5 py-4 border-b border-slate-100 dark:border-slate-800 flex items-center justify-between">
                                <h2 className="text-sm font-bold text-slate-900 dark:text-white">
                                    Transaction & Expense Journal ({expenses.length})
                                </h2>
                                <Link
                                    href="/finance/expenses/create"
                                    className="px-3 py-1.5 rounded-xl bg-blue-900 hover:bg-blue-950 text-white font-bold text-xs shadow-sm"
                                >
                                    Log Expenditure
                                </Link>
                            </div>

                            <div className="overflow-x-auto">
                                <table className="w-full text-left text-xs">
                                    <thead className="bg-slate-50 dark:bg-slate-800/60 text-slate-500 uppercase font-semibold text-[10px] tracking-wider border-b border-slate-200 dark:border-slate-800">
                                        <tr>
                                            <th className="py-3 px-4">Date</th>
                                            <th className="py-3 px-4">Category & Description</th>
                                            <th className="py-3 px-4">Filed By</th>
                                            <th className="py-3 px-4">Amount (ETB)</th>
                                            <th className="py-3 px-4">Status</th>
                                            <th className="py-3 px-4 text-right">Voucher</th>
                                        </tr>
                                    </thead>
                                    <tbody className="divide-y divide-slate-100 dark:divide-slate-800/80">
                                        {expenses.length === 0 ? (
                                            <tr>
                                                <td colSpan={6} className="py-8 text-center text-slate-400 text-xs">
                                                    No expenditure transactions charged to this site yet.
                                                </td>
                                            </tr>
                                        ) : (
                                            expenses.map((exp) => (
                                                <tr key={exp.id} className="hover:bg-slate-50/70 dark:hover:bg-slate-800/40 transition-colors">
                                                    <td className="py-3 px-4 font-mono text-slate-600 dark:text-slate-400 text-[11px]">
                                                        {exp.expense_date ? new Date(exp.expense_date).toLocaleDateString() : '—'}
                                                    </td>
                                                    <td className="py-3 px-4">
                                                        <div className="font-bold text-slate-900 dark:text-white">
                                                            {exp.category}
                                                        </div>
                                                        <div className="text-[11px] text-slate-400 truncate max-w-xs">
                                                            {exp.description || '—'}
                                                        </div>
                                                    </td>
                                                    <td className="py-3 px-4 text-slate-600 dark:text-slate-300">
                                                        {exp.user?.name || 'Authorized Agent'}
                                                    </td>
                                                    <td className="py-3 px-4 font-mono font-bold text-slate-900 dark:text-white">
                                                        {Number(exp.amount).toLocaleString(undefined, { minimumFractionDigits: 2 })}
                                                    </td>
                                                    <td className="py-3 px-4">
                                                        <span className={`inline-block px-2 py-0.5 rounded-full text-[10px] font-bold ${
                                                            exp.status === 'approved'
                                                                ? 'bg-emerald-50 dark:bg-emerald-950/50 text-emerald-600 dark:text-emerald-400 border border-emerald-200 dark:border-emerald-900'
                                                                : exp.status === 'rejected'
                                                                ? 'bg-rose-50 dark:bg-rose-950/50 text-rose-600 dark:text-rose-400 border border-rose-200 dark:border-rose-900'
                                                                : 'bg-amber-50 dark:bg-amber-950/50 text-amber-600 dark:text-amber-400 border border-amber-200 dark:border-amber-900'
                                                        }`}>
                                                            {exp.status}
                                                        </span>
                                                    </td>
                                                    <td className="py-3 px-4 text-right">
                                                        <Link
                                                            href={`/finance/expenses/${exp.id}`}
                                                            className="px-2.5 py-1 rounded-lg bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300 font-semibold text-[11px] transition-colors"
                                                        >
                                                            View
                                                        </Link>
                                                    </td>
                                                </tr>
                                            ))
                                        )}
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                )}

                {/* TAB 3: PROJECT SCOPE & PARAMETERS */}
                {activeTab === 'overview' && (
                    <div className="space-y-6">
                        <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div className="rounded-2xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 p-5 shadow-xs">
                                <h3 className="text-xs font-bold text-slate-400 uppercase tracking-wider mb-3">Project Metadata</h3>
                                <div className="space-y-3 text-xs">
                                    <div className="flex justify-between pb-2 border-b border-slate-100 dark:border-slate-800">
                                        <span className="text-slate-500">Site Location</span>
                                        <span className="font-semibold text-slate-800 dark:text-slate-200">{project.location || '—'}</span>
                                    </div>
                                    <div className="flex justify-between pb-2 border-b border-slate-100 dark:border-slate-800">
                                        <span className="text-slate-500">Kickoff Date</span>
                                        <span className="font-semibold text-slate-800 dark:text-slate-200">
                                            {project.start_date ? new Date(project.start_date).toLocaleDateString() : '—'}
                                        </span>
                                    </div>
                                    <div className="flex justify-between pb-2 border-b border-slate-100 dark:border-slate-800">
                                        <span className="text-slate-500">Target Handover</span>
                                        <span className="font-semibold text-slate-800 dark:text-slate-200">
                                            {project.end_date ? new Date(project.end_date).toLocaleDateString() : '—'}
                                        </span>
                                    </div>
                                    <div className="flex justify-between">
                                        <span className="text-slate-500">Registry Status</span>
                                        <span className="font-semibold text-slate-800 dark:text-slate-200 capitalize">{project.status}</span>
                                    </div>
                                </div>
                            </div>

                            <div className="rounded-2xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 p-5 shadow-xs">
                                <h3 className="text-xs font-bold text-slate-400 uppercase tracking-wider mb-3">Contractual Scope & Description</h3>
                                <p className="text-xs text-slate-700 dark:text-slate-300 leading-relaxed whitespace-pre-line">
                                    {project.description || 'No detailed scope of work description provided for this project.'}
                                </p>
                            </div>
                        </div>
                    </div>
                )}
            </div>

            {/* MODAL: ADD WBS MILESTONE */}
            {isAddModalOpen && (
                <div className="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/60 backdrop-blur-xs">
                    <div className="w-full max-w-lg bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-xl overflow-hidden">
                        <div className="px-5 py-4 border-b border-slate-100 dark:border-slate-800 flex items-center justify-between">
                            <h3 className="text-sm font-bold text-slate-900 dark:text-white flex items-center gap-2">
                                <PlusCircle className="w-4 h-4 text-blue-900 dark:text-blue-400" />
                                Add Work Breakdown Structure (WBS) Milestone
                            </h3>
                            <button
                                type="button"
                                onClick={() => setIsAddModalOpen(false)}
                                className="p-1 rounded-lg text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 cursor-pointer"
                            >
                                <XCircle className="w-5 h-5" />
                            </button>
                        </div>

                        <form onSubmit={handleCreateMilestone} className="p-5 space-y-4">
                            <div className="grid grid-cols-3 gap-3">
                                <div>
                                    <label className="block text-[11px] font-bold text-slate-600 dark:text-slate-300 uppercase mb-1">
                                        WBS Code
                                    </label>
                                    <input
                                        type="text"
                                        placeholder="e.g. 1.0"
                                        value={addData.wbs_code}
                                        onChange={(e) => setAddData('wbs_code', e.target.value)}
                                        className="w-full px-3 py-2 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs font-mono"
                                    />
                                </div>
                                <div className="col-span-2">
                                    <label className="block text-[11px] font-bold text-slate-600 dark:text-slate-300 uppercase mb-1">
                                        Milestone / Phase Title <span className="text-rose-500">*</span>
                                    </label>
                                    <input
                                        type="text"
                                        required
                                        placeholder="e.g. Substructure & Excavation"
                                        value={addData.title}
                                        onChange={(e) => setAddData('title', e.target.value)}
                                        className="w-full px-3 py-2 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs"
                                    />
                                    {addErrors.title && <p className="text-[10px] text-rose-500 mt-1">{addErrors.title}</p>}
                                </div>
                            </div>

                            <div>
                                <label className="block text-[11px] font-bold text-slate-600 dark:text-slate-300 uppercase mb-1">
                                    Phase Description & Technical Deliverables
                                </label>
                                <textarea
                                    rows="2"
                                    placeholder="Scope details, inspection gates, concrete pour grades..."
                                    value={addData.description}
                                    onChange={(e) => setAddData('description', e.target.value)}
                                    className="w-full px-3 py-2 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs"
                                />
                            </div>

                            <div className="grid grid-cols-2 gap-3">
                                <div>
                                    <label className="block text-[11px] font-bold text-slate-600 dark:text-slate-300 uppercase mb-1">
                                        Start Date
                                    </label>
                                    <input
                                        type="date"
                                        value={addData.start_date}
                                        onChange={(e) => setAddData('start_date', e.target.value)}
                                        className="w-full px-3 py-2 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs"
                                    />
                                </div>
                                <div>
                                    <label className="block text-[11px] font-bold text-slate-600 dark:text-slate-300 uppercase mb-1">
                                        Target Deadline
                                    </label>
                                    <input
                                        type="date"
                                        value={addData.due_date}
                                        onChange={(e) => setAddData('due_date', e.target.value)}
                                        className="w-full px-3 py-2 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs"
                                    />
                                    {addErrors.due_date && <p className="text-[10px] text-rose-500 mt-1">{addErrors.due_date}</p>}
                                </div>
                            </div>

                            <div className="grid grid-cols-3 gap-3">
                                <div>
                                    <label className="block text-[11px] font-bold text-slate-600 dark:text-slate-300 uppercase mb-1">
                                        Weight (%)
                                    </label>
                                    <input
                                        type="number"
                                        step="0.1"
                                        min="0"
                                        max="100"
                                        placeholder="e.g. 25"
                                        value={addData.weight_pct}
                                        onChange={(e) => setAddData('weight_pct', e.target.value)}
                                        className="w-full px-3 py-2 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs font-mono"
                                    />
                                </div>
                                <div>
                                    <label className="block text-[11px] font-bold text-slate-600 dark:text-slate-300 uppercase mb-1">
                                        Progress (%)
                                    </label>
                                    <input
                                        type="number"
                                        min="0"
                                        max="100"
                                        value={addData.progress}
                                        onChange={(e) => setAddData('progress', e.target.value)}
                                        className="w-full px-3 py-2 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs font-mono"
                                    />
                                </div>
                                <div>
                                    <label className="block text-[11px] font-bold text-slate-600 dark:text-slate-300 uppercase mb-1">
                                        Initial Status
                                    </label>
                                    <select
                                        value={addData.status}
                                        onChange={(e) => setAddData('status', e.target.value)}
                                        className="w-full px-3 py-2 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs"
                                    >
                                        <option value="pending">Pending</option>
                                        <option value="in_progress">In Progress</option>
                                        <option value="completed">Completed</option>
                                        <option value="delayed">Delayed</option>
                                    </select>
                                </div>
                            </div>

                            <div>
                                <label className="block text-[11px] font-bold text-slate-600 dark:text-slate-300 uppercase mb-1">
                                    Allocated Budget (ETB) - Optional
                                </label>
                                <input
                                    type="number"
                                    step="0.01"
                                    placeholder="e.g. 500000"
                                    value={addData.allocated_budget}
                                    onChange={(e) => setAddData('allocated_budget', e.target.value)}
                                    className="w-full px-3 py-2 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs font-mono"
                                />
                            </div>

                            <div className="flex items-center justify-end gap-2 pt-2 border-t border-slate-100 dark:border-slate-800">
                                <button
                                    type="button"
                                    onClick={() => setIsAddModalOpen(false)}
                                    className="px-3.5 py-1.5 rounded-xl border border-slate-200 dark:border-slate-700 text-xs font-semibold text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800 cursor-pointer"
                                >
                                    Cancel
                                </button>
                                <button
                                    type="submit"
                                    disabled={addProcessing}
                                    className="px-4 py-1.5 rounded-xl bg-blue-900 hover:bg-blue-950 text-white font-bold text-xs shadow-sm cursor-pointer disabled:opacity-50"
                                >
                                    {addProcessing ? 'Saving...' : 'Save Milestone'}
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            )}

            {/* MODAL: EDIT WBS MILESTONE */}
            {editingMilestone && (
                <div className="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/60 backdrop-blur-xs">
                    <div className="w-full max-w-lg bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-xl overflow-hidden">
                        <div className="px-5 py-4 border-b border-slate-100 dark:border-slate-800 flex items-center justify-between">
                            <h3 className="text-sm font-bold text-slate-900 dark:text-white flex items-center gap-2">
                                <Edit className="w-4 h-4 text-blue-900 dark:text-blue-400" />
                                Edit Milestone ({editingMilestone.title})
                            </h3>
                            <button
                                type="button"
                                onClick={() => setEditingMilestone(null)}
                                className="p-1 rounded-lg text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 cursor-pointer"
                            >
                                <XCircle className="w-5 h-5" />
                            </button>
                        </div>

                        <form onSubmit={handleUpdateMilestone} className="p-5 space-y-4">
                            <div className="grid grid-cols-3 gap-3">
                                <div>
                                    <label className="block text-[11px] font-bold text-slate-600 dark:text-slate-300 uppercase mb-1">
                                        WBS Code
                                    </label>
                                    <input
                                        type="text"
                                        value={editData.wbs_code}
                                        onChange={(e) => setEditData('wbs_code', e.target.value)}
                                        className="w-full px-3 py-2 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs font-mono"
                                    />
                                </div>
                                <div className="col-span-2">
                                    <label className="block text-[11px] font-bold text-slate-600 dark:text-slate-300 uppercase mb-1">
                                        Title <span className="text-rose-500">*</span>
                                    </label>
                                    <input
                                        type="text"
                                        required
                                        value={editData.title}
                                        onChange={(e) => setEditData('title', e.target.value)}
                                        className="w-full px-3 py-2 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs"
                                    />
                                </div>
                            </div>

                            <div>
                                <label className="block text-[11px] font-bold text-slate-600 dark:text-slate-300 uppercase mb-1">
                                    Phase Description
                                </label>
                                <textarea
                                    rows="2"
                                    value={editData.description}
                                    onChange={(e) => setEditData('description', e.target.value)}
                                    className="w-full px-3 py-2 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs"
                                />
                            </div>

                            <div className="grid grid-cols-2 gap-3">
                                <div>
                                    <label className="block text-[11px] font-bold text-slate-600 dark:text-slate-300 uppercase mb-1">
                                        Start Date
                                    </label>
                                    <input
                                        type="date"
                                        value={editData.start_date}
                                        onChange={(e) => setEditData('start_date', e.target.value)}
                                        className="w-full px-3 py-2 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs"
                                    />
                                </div>
                                <div>
                                    <label className="block text-[11px] font-bold text-slate-600 dark:text-slate-300 uppercase mb-1">
                                        Target Deadline
                                    </label>
                                    <input
                                        type="date"
                                        value={editData.due_date}
                                        onChange={(e) => setEditData('due_date', e.target.value)}
                                        className="w-full px-3 py-2 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs"
                                    />
                                </div>
                            </div>

                            <div className="grid grid-cols-3 gap-3">
                                <div>
                                    <label className="block text-[11px] font-bold text-slate-600 dark:text-slate-300 uppercase mb-1">
                                        Weight (%)
                                    </label>
                                    <input
                                        type="number"
                                        step="0.1"
                                        min="0"
                                        max="100"
                                        value={editData.weight_pct}
                                        onChange={(e) => setEditData('weight_pct', e.target.value)}
                                        className="w-full px-3 py-2 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs font-mono"
                                    />
                                </div>
                                <div>
                                    <label className="block text-[11px] font-bold text-slate-600 dark:text-slate-300 uppercase mb-1">
                                        Progress (%)
                                    </label>
                                    <input
                                        type="number"
                                        min="0"
                                        max="100"
                                        value={editData.progress}
                                        onChange={(e) => setEditData('progress', e.target.value)}
                                        className="w-full px-3 py-2 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs font-mono"
                                    />
                                </div>
                                <div>
                                    <label className="block text-[11px] font-bold text-slate-600 dark:text-slate-300 uppercase mb-1">
                                        Status
                                    </label>
                                    <select
                                        value={editData.status}
                                        onChange={(e) => setEditData('status', e.target.value)}
                                        className="w-full px-3 py-2 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs"
                                    >
                                        <option value="pending">Pending</option>
                                        <option value="in_progress">In Progress</option>
                                        <option value="completed">Completed</option>
                                        <option value="delayed">Delayed</option>
                                    </select>
                                </div>
                            </div>

                            <div className="flex items-center justify-end gap-2 pt-2 border-t border-slate-100 dark:border-slate-800">
                                <button
                                    type="button"
                                    onClick={() => setEditingMilestone(null)}
                                    className="px-3.5 py-1.5 rounded-xl border border-slate-200 dark:border-slate-700 text-xs font-semibold text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800 cursor-pointer"
                                >
                                    Cancel
                                </button>
                                <button
                                    type="submit"
                                    disabled={editProcessing}
                                    className="px-4 py-1.5 rounded-xl bg-blue-900 hover:bg-blue-950 text-white font-bold text-xs shadow-sm cursor-pointer disabled:opacity-50"
                                >
                                    {editProcessing ? 'Updating...' : 'Save Changes'}
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            )}

            {/* MODAL: QUICK ADJUST PROGRESS */}
            {quickProgressMilestone && (
                <div className="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/60 backdrop-blur-xs">
                    <div className="w-full max-w-sm bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-xl overflow-hidden">
                        <div className="px-5 py-4 border-b border-slate-100 dark:border-slate-800 flex items-center justify-between">
                            <h3 className="text-xs font-bold text-slate-900 dark:text-white flex items-center gap-1.5">
                                <Sliders className="w-3.5 h-3.5 text-blue-900 dark:text-blue-400" />
                                Adjust Progress
                            </h3>
                            <button
                                type="button"
                                onClick={() => setQuickProgressMilestone(null)}
                                className="p-1 rounded-lg text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 cursor-pointer"
                            >
                                <XCircle className="w-4 h-4" />
                            </button>
                        </div>

                        <form onSubmit={handleSaveQuickProgress} className="p-5 space-y-4">
                            <div>
                                <div className="text-xs font-bold text-slate-800 dark:text-slate-200 mb-1">
                                    {quickProgressMilestone.title}
                                </div>
                                <div className="text-[11px] text-slate-400">
                                    Slide to update physical completion percentage:
                                </div>
                            </div>

                            <div className="text-center py-2">
                                <span className="text-3xl font-black text-blue-900 dark:text-blue-400 font-mono">
                                    {quickProgressValue}%
                                </span>
                            </div>

                            <input
                                type="range"
                                min="0"
                                max="100"
                                step="5"
                                value={quickProgressValue}
                                onChange={(e) => setQuickProgressValue(Number(e.target.value))}
                                className="w-full accent-blue-900 cursor-pointer"
                            />

                            <div className="flex items-center justify-between gap-2">
                                {[0, 25, 50, 75, 100].map((val) => (
                                    <button
                                        key={val}
                                        type="button"
                                        onClick={() => setQuickProgressValue(val)}
                                        className={`px-2 py-1 rounded-lg text-[10px] font-mono font-bold cursor-pointer transition-colors ${
                                            quickProgressValue === val
                                                ? 'bg-blue-900 text-white'
                                                : 'bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-400 hover:bg-slate-200'
                                        }`}
                                    >
                                        {val}%
                                    </button>
                                ))}
                            </div>

                            <div className="flex items-center justify-end gap-2 pt-2 border-t border-slate-100 dark:border-slate-800">
                                <button
                                    type="button"
                                    onClick={() => setQuickProgressMilestone(null)}
                                    className="px-3 py-1.5 rounded-xl border border-slate-200 dark:border-slate-700 text-xs font-semibold text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800 cursor-pointer"
                                >
                                    Cancel
                                </button>
                                <button
                                    type="submit"
                                    className="px-4 py-1.5 rounded-xl bg-blue-900 hover:bg-blue-950 text-white font-bold text-xs shadow-sm cursor-pointer"
                                >
                                    Save Progress
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            )}
        </AuthenticatedLayout>
    );
}
