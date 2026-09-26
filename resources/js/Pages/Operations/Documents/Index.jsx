import { useState } from 'react';
import { Head, Link, useForm, router } from '@inertiajs/react';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import CalendarDate from '@/Components/CalendarDate';
import {
    FileText,
    UploadCloud,
    Download,
    CheckCircle2,
    Clock,
    AlertCircle,
    Archive,
    Trash2,
    Filter,
    Search,
    Plus,
    X,
    FolderKanban,
    Building2,
    Check,
    FileCode,
    FileSpreadsheet,
    FileCheck,
    Compass,
    HardHat,
    Layers,
    Eye,
} from 'lucide-react';

export default function Index({ documents, projects = [], filters = {}, stats = {} }) {
    const [search, setSearch] = useState(filters.search || '');
    const [selectedProject, setSelectedProject] = useState(filters.project_id || '');
    const [selectedType, setSelectedType] = useState(filters.document_type || '');
    const [selectedStatus, setSelectedStatus] = useState(filters.status || '');
    const [isUploadOpen, setIsUploadOpen] = useState(false);
    const [targetUploadProject, setTargetUploadProject] = useState(projects[0]?.id || '');

    const documentTypes = [
        { id: 'architectural', label: 'Architectural Drawings', icon: Compass, color: 'text-purple-400 bg-purple-500/10 border-purple-500/20' },
        { id: 'structural', label: 'Structural Blueprints', icon: Layers, color: 'text-blue-400 bg-blue-500/10 border-blue-500/20' },
        { id: 'mep', label: 'MEP (Mechanical, Electrical, Plumbing)', icon: FileCode, color: 'text-amber-400 bg-amber-500/10 border-amber-500/20' },
        { id: 'soil_geotechnical', label: 'Soil & Geotechnical Reports', icon: HardHat, color: 'text-emerald-400 bg-emerald-500/10 border-emerald-500/20' },
        { id: 'boq_spec', label: 'BOQ & Technical Specs', icon: FileSpreadsheet, color: 'text-cyan-400 bg-cyan-500/10 border-cyan-500/20' },
        { id: 'contract_agreement', label: 'Contracts & Agreements', icon: FileCheck, color: 'text-rose-400 bg-rose-500/10 border-rose-500/20' },
        { id: 'as_built', label: 'As-Built Drawings', icon: Building2, color: 'text-indigo-400 bg-indigo-500/10 border-indigo-500/20' },
        { id: 'shop_drawing', label: 'Fabrication & Shop Drawings', icon: Layers, color: 'text-orange-400 bg-orange-500/10 border-orange-500/20' },
        { id: 'site_submittal', label: 'Site Material Submittals', icon: FileText, color: 'text-teal-400 bg-teal-500/10 border-teal-500/20' },
        { id: 'other', label: 'General Project Documents', icon: FileText, color: 'text-slate-400 bg-slate-500/10 border-slate-500/20' },
    ];

    const {
        data: uploadData,
        setData: setUploadData,
        post: postUpload,
        processing: uploadProcessing,
        errors: uploadErrors,
        reset: resetUpload,
    } = useForm({
        project_id: projects[0]?.id || '',
        title: '',
        document_code: '',
        document_type: 'architectural',
        revision_number: 'Rev 0',
        file: null,
        description: '',
    });

    const handleFilterChange = (e) => {
        e.preventDefault();
        router.get(
            '/operations/documents',
            {
                search,
                project_id: selectedProject,
                document_type: selectedType,
                status: selectedStatus,
            },
            { preserveState: true }
        );
    };

    const handleResetFilters = () => {
        setSearch('');
        setSelectedProject('');
        setSelectedType('');
        setSelectedStatus('');
        router.get('/operations/documents');
    };

    const handleUploadSubmit = (e) => {
        e.preventDefault();
        const pId = uploadData.project_id || targetUploadProject;
        if (!pId) return;

        postUpload(`/projects/${pId}/documents`, {
            preserveScroll: true,
            onSuccess: () => {
                setIsUploadOpen(false);
                resetUpload();
            },
        });
    };

    const handleUpdateStatus = (docId, newStatus) => {
        router.patch(
            `/documents/${docId}/status`,
            { status: newStatus },
            { preserveScroll: true }
        );
    };

    const handleDelete = (docId, title) => {
        if (confirm(`Are you sure you want to permanently remove "${title}"?`)) {
            router.delete(`/documents/${docId}`, { preserveScroll: true });
        }
    };

    const getTypeMeta = (typeId) => {
        return (
            documentTypes.find((t) => t.id === typeId) || {
                label: typeId || 'Document',
                icon: FileText,
                color: 'text-slate-400 bg-slate-500/10 border-slate-500/20',
            }
        );
    };

    return (
        <AuthenticatedLayout title="Engineering Blueprints & Documents (EDMS)" header="Operations">
            <Head title="Engineering Blueprints & Documents (EDMS) - Natanem ERP" />

            <div className="space-y-6">
                {/* Header & Upload Action */}
                <div className="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-2 border-b border-slate-200 dark:border-slate-800">
                    <div>
                        <div className="flex items-center gap-2">
                            <span className="p-2 rounded-xl bg-blue-600/10 text-blue-500 border border-blue-500/20">
                                <Compass className="w-5 h-5" />
                            </span>
                            <h1 className="text-xl font-extrabold tracking-tight text-slate-900 dark:text-white">
                                Engineering Blueprints & Documents (EDMS)
                            </h1>
                            <span className="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-blue-100 text-blue-800 dark:bg-blue-950/60 dark:text-blue-400 border border-blue-200 dark:border-blue-900">
                                Version Controlled
                            </span>
                        </div>
                        <p className="text-xs text-slate-500 dark:text-slate-400 mt-1">
                            Central repository for CAD drawings, architectural models, structural blueprints, and site submittals with Dual-Calendar tracking.
                        </p>
                    </div>

                    <button
                        type="button"
                        onClick={() => setIsUploadOpen(true)}
                        className="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs shadow-md shadow-blue-600/20 transition-all cursor-pointer shrink-0"
                    >
                        <UploadCloud className="w-4 h-4" />
                        <span>Upload Blueprint / Drawing</span>
                    </button>
                </div>

                {/* KPI Metrics */}
                <div className="grid grid-cols-2 md:grid-cols-4 gap-4">
                    <div className="rounded-2xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 p-4 shadow-xs">
                        <div className="flex items-center justify-between">
                            <span className="text-xs font-bold text-slate-500 dark:text-slate-400">Total Drawings</span>
                            <span className="p-1.5 rounded-lg bg-blue-50 dark:bg-blue-950/50 text-blue-600 dark:text-blue-400">
                                <FileText className="w-4 h-4" />
                            </span>
                        </div>
                        <div className="text-2xl font-black text-slate-900 dark:text-white mt-1">
                            {stats.total || 0}
                        </div>
                        <span className="text-[11px] text-slate-400">In project repositories</span>
                    </div>

                    <div className="rounded-2xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 p-4 shadow-xs">
                        <div className="flex items-center justify-between">
                            <span className="text-xs font-bold text-slate-500 dark:text-slate-400">Approved for Site</span>
                            <span className="p-1.5 rounded-lg bg-emerald-50 dark:bg-emerald-950/50 text-emerald-600 dark:text-emerald-400">
                                <CheckCircle2 className="w-4 h-4" />
                            </span>
                        </div>
                        <div className="text-2xl font-black text-emerald-600 dark:text-emerald-400 mt-1">
                            {stats.approved || 0}
                        </div>
                        <span className="text-[11px] text-slate-400">Ready for execution</span>
                    </div>

                    <div className="rounded-2xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 p-4 shadow-xs">
                        <div className="flex items-center justify-between">
                            <span className="text-xs font-bold text-slate-500 dark:text-slate-400">Under Review</span>
                            <span className="p-1.5 rounded-lg bg-amber-50 dark:bg-amber-950/50 text-amber-600 dark:text-amber-400">
                                <Clock className="w-4 h-4" />
                            </span>
                        </div>
                        <div className="text-2xl font-black text-amber-600 dark:text-amber-400 mt-1">
                            {stats.under_review || 0}
                        </div>
                        <span className="text-[11px] text-slate-400">Awaiting consultant sign-off</span>
                    </div>

                    <div className="rounded-2xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 p-4 shadow-xs">
                        <div className="flex items-center justify-between">
                            <span className="text-xs font-bold text-slate-500 dark:text-slate-400">Superseded</span>
                            <span className="p-1.5 rounded-lg bg-slate-100 dark:bg-slate-800 text-slate-500">
                                <Archive className="w-4 h-4" />
                            </span>
                        </div>
                        <div className="text-2xl font-black text-slate-600 dark:text-slate-300 mt-1">
                            {stats.superseded || 0}
                        </div>
                        <span className="text-[11px] text-slate-400">Archived older revisions</span>
                    </div>
                </div>

                {/* Filter and Search Bar */}
                <form
                    onSubmit={handleFilterChange}
                    className="p-4 rounded-2xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 shadow-xs flex flex-wrap items-center gap-3"
                >
                    <div className="relative flex-1 min-w-[200px]">
                        <Search className="w-4 h-4 absolute left-3 top-1/2 -translate-y-1/2 text-slate-400" />
                        <input
                            type="text"
                            placeholder="Search by drawing code, title, or filename..."
                            value={search}
                            onChange={(e) => setSearch(e.target.value)}
                            className="w-full pl-9 pr-3 py-1.5 text-xs rounded-xl bg-slate-50 dark:bg-slate-800/60 border border-slate-200 dark:border-slate-700 text-slate-800 dark:text-slate-200 focus:outline-hidden focus:ring-2 focus:ring-blue-500"
                        />
                    </div>

                    <select
                        value={selectedProject}
                        onChange={(e) => setSelectedProject(e.target.value)}
                        className="py-1.5 px-3 text-xs rounded-xl bg-slate-50 dark:bg-slate-800/60 border border-slate-200 dark:border-slate-700 text-slate-800 dark:text-slate-200 focus:outline-hidden focus:ring-2 focus:ring-blue-500"
                    >
                        <option value="">All Projects</option>
                        {projects.map((p) => (
                            <option key={p.id} value={p.id}>
                                {p.name}
                            </option>
                        ))}
                    </select>

                    <select
                        value={selectedType}
                        onChange={(e) => setSelectedType(e.target.value)}
                        className="py-1.5 px-3 text-xs rounded-xl bg-slate-50 dark:bg-slate-800/60 border border-slate-200 dark:border-slate-700 text-slate-800 dark:text-slate-200 focus:outline-hidden focus:ring-2 focus:ring-blue-500"
                    >
                        <option value="">All Drawing Disciplines</option>
                        {documentTypes.map((t) => (
                            <option key={t.id} value={t.id}>
                                {t.label}
                            </option>
                        ))}
                    </select>

                    <select
                        value={selectedStatus}
                        onChange={(e) => setSelectedStatus(e.target.value)}
                        className="py-1.5 px-3 text-xs rounded-xl bg-slate-50 dark:bg-slate-800/60 border border-slate-200 dark:border-slate-700 text-slate-800 dark:text-slate-200 focus:outline-hidden focus:ring-2 focus:ring-blue-500"
                    >
                        <option value="">All Statuses</option>
                        <option value="under_review">Under Review</option>
                        <option value="approved">Approved</option>
                        <option value="superseded">Superseded</option>
                        <option value="rejected">Rejected</option>
                    </select>

                    <div className="flex items-center gap-2">
                        <button
                            type="submit"
                            className="px-3.5 py-1.5 rounded-xl bg-slate-900 text-white dark:bg-slate-100 dark:text-slate-950 font-bold text-xs hover:bg-slate-800 transition-colors cursor-pointer"
                        >
                            Filter
                        </button>
                        <button
                            type="button"
                            onClick={handleResetFilters}
                            className="px-3 py-1.5 rounded-xl border border-slate-200 dark:border-slate-700 text-slate-600 dark:text-slate-400 font-semibold text-xs hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors cursor-pointer"
                        >
                            Reset
                        </button>
                    </div>
                </form>

                {/* Documents Grid / Table */}
                {documents.data.length === 0 ? (
                    <div className="rounded-2xl border border-dashed border-slate-300 dark:border-slate-700 p-12 text-center bg-white/50 dark:bg-slate-900/50">
                        <FileText className="w-12 h-12 text-slate-400 mx-auto mb-3" />
                        <h3 className="text-base font-bold text-slate-800 dark:text-slate-200">
                            No engineering documents found
                        </h3>
                        <p className="text-xs text-slate-500 dark:text-slate-400 max-w-sm mx-auto mt-1 mb-4">
                            Upload your project blueprints, architectural schematics, structural designs, or site submittals to track revisions.
                        </p>
                        <button
                            type="button"
                            onClick={() => setIsUploadOpen(true)}
                            className="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs shadow-md transition-all cursor-pointer"
                        >
                            <UploadCloud className="w-4 h-4" />
                            <span>Upload First Document</span>
                        </button>
                    </div>
                ) : (
                    <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                        {documents.data.map((doc) => {
                            const meta = getTypeMeta(doc.document_type);
                            const MetaIcon = meta.icon;

                            return (
                                <div
                                    key={doc.id}
                                    className="rounded-2xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 p-5 shadow-xs hover:border-blue-500/40 dark:hover:border-blue-500/40 transition-all flex flex-col justify-between"
                                >
                                    <div className="space-y-3">
                                        {/* Top Badge Row */}
                                        <div className="flex items-start justify-between gap-2">
                                            <div className="flex items-center gap-1.5 flex-wrap">
                                                <span className={`inline-flex items-center gap-1 px-2.5 py-0.5 rounded-lg text-[10px] font-bold border ${meta.color}`}>
                                                    <MetaIcon className="w-3 h-3" />
                                                    <span>{meta.label}</span>
                                                </span>
                                                <span className="px-2 py-0.5 rounded-lg text-[10px] font-mono font-bold bg-slate-100 text-slate-700 dark:bg-slate-800 dark:text-slate-300">
                                                    {doc.revision_number}
                                                </span>
                                            </div>

                                            <span
                                                className={`px-2.5 py-0.5 rounded-full text-[10px] font-extrabold capitalize ${
                                                    doc.status === 'approved'
                                                        ? 'bg-emerald-50 dark:bg-emerald-950/60 text-emerald-600 dark:text-emerald-400 border border-emerald-200 dark:border-emerald-900'
                                                        : doc.status === 'under_review'
                                                        ? 'bg-amber-50 dark:bg-amber-950/60 text-amber-600 dark:text-amber-400 border border-amber-200 dark:border-amber-900'
                                                        : doc.status === 'superseded'
                                                        ? 'bg-slate-100 dark:bg-slate-800 text-slate-500 border border-slate-200 dark:border-slate-700'
                                                        : 'bg-rose-50 dark:bg-rose-950/60 text-rose-600 dark:text-rose-400 border border-rose-200 dark:border-rose-900'
                                                }`}
                                            >
                                                {doc.status.replace('_', ' ')}
                                            </span>
                                        </div>

                                        {/* Drawing Code & Title */}
                                        <div>
                                            <div className="text-[11px] font-mono font-bold text-blue-600 dark:text-blue-400">
                                                {doc.document_code}
                                            </div>
                                            <h3 className="text-sm font-bold text-slate-900 dark:text-white leading-snug line-clamp-2 mt-0.5" title={doc.title}>
                                                {doc.title}
                                            </h3>
                                        </div>

                                        {/* Project and Milestone */}
                                        <div className="text-xs text-slate-500 dark:text-slate-400 space-y-1 pt-1 border-t border-slate-100 dark:border-slate-800">
                                            <div className="flex items-center gap-1.5 truncate">
                                                <Building2 className="w-3.5 h-3.5 text-slate-400 shrink-0" />
                                                <span className="font-semibold text-slate-700 dark:text-slate-300 truncate">
                                                    {doc.project?.name || 'Project'}
                                                </span>
                                            </div>
                                            {doc.milestone && (
                                                <div className="flex items-center gap-1.5 text-[11px] text-slate-400 truncate">
                                                    <FolderKanban className="w-3 h-3 text-slate-400 shrink-0" />
                                                    <span className="truncate">WBS: {doc.milestone.title}</span>
                                                </div>
                                            )}
                                        </div>

                                        {/* File Metadata & Dual Calendar Display */}
                                        <div className="bg-slate-50 dark:bg-slate-800/40 rounded-xl p-2.5 text-[11px] text-slate-500 dark:text-slate-400 space-y-1">
                                            <div className="flex items-center justify-between">
                                                <span>File:</span>
                                                <span className="font-medium text-slate-700 dark:text-slate-300 truncate max-w-[150px]" title={doc.file_name}>
                                                    {doc.file_name}
                                                </span>
                                            </div>
                                            <div className="flex items-center justify-between">
                                                <span>Size / By:</span>
                                                <span>{doc.formatted_file_size} • {doc.uploader?.name || 'User'}</span>
                                            </div>
                                            <div className="flex items-center justify-between pt-1 border-t border-slate-200/50 dark:border-slate-700/50">
                                                <span>Registered:</span>
                                                <CalendarDate date={doc.created_at} className="font-semibold text-slate-700 dark:text-slate-300" />
                                            </div>
                                        </div>
                                    </div>

                                    {/* Action Bar */}
                                    <div className="flex items-center justify-between gap-2 pt-4 mt-4 border-t border-slate-100 dark:border-slate-800">
                                        <a
                                            href={`/documents/${doc.id}/download`}
                                            className="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs transition-colors shadow-xs"
                                        >
                                            <Download className="w-3.5 h-3.5" />
                                            <span>Download</span>
                                        </a>

                                        <div className="flex items-center gap-1">
                                            {doc.status !== 'approved' && (
                                                <button
                                                    type="button"
                                                    onClick={() => handleUpdateStatus(doc.id, 'approved')}
                                                    className="p-1.5 rounded-lg text-emerald-600 hover:bg-emerald-50 dark:hover:bg-emerald-950/50 transition-colors"
                                                    title="Approve for Construction"
                                                >
                                                    <CheckCircle2 className="w-4 h-4" />
                                                </button>
                                            )}

                                            {doc.status !== 'superseded' && (
                                                <button
                                                    type="button"
                                                    onClick={() => handleUpdateStatus(doc.id, 'superseded')}
                                                    className="p-1.5 rounded-lg text-slate-400 hover:text-slate-600 hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors"
                                                    title="Mark as Superseded"
                                                >
                                                    <Archive className="w-4 h-4" />
                                                </button>
                                            )}

                                            <button
                                                type="button"
                                                onClick={() => handleDelete(doc.id, doc.title)}
                                                className="p-1.5 rounded-lg text-rose-500 hover:bg-rose-50 dark:hover:bg-rose-950/50 transition-colors"
                                                title="Delete Document"
                                            >
                                                <Trash2 className="w-4 h-4" />
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            );
                        })}
                    </div>
                )}

                {/* Pagination */}
                {documents.links && documents.links.length > 3 && (
                    <div className="flex items-center justify-center gap-1 pt-4">
                        {documents.links.map((link, idx) => (
                            <Link
                                key={idx}
                                href={link.url || '#'}
                                dangerouslySetInnerHTML={{ __html: link.label }}
                                className={`px-3 py-1.5 rounded-xl text-xs font-semibold transition-colors ${
                                    link.active
                                        ? 'bg-blue-600 text-white font-bold'
                                        : link.url
                                        ? 'bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 text-slate-700 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800'
                                        : 'text-slate-400 cursor-not-allowed'
                                }`}
                            />
                        ))}
                    </div>
                )}
            </div>

            {/* UPLOAD BLUEPRINT MODAL */}
            {isUploadOpen && (
                <div className="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/70 backdrop-blur-xs">
                    <div className="w-full max-w-xl rounded-3xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 shadow-2xl p-6 overflow-hidden animate-in fade-in zoom-in-95 duration-150">
                        <div className="flex items-center justify-between pb-3 border-b border-slate-200 dark:border-slate-800">
                            <div className="flex items-center gap-2">
                                <span className="p-2 rounded-xl bg-blue-600/10 text-blue-500">
                                    <UploadCloud className="w-5 h-5" />
                                </span>
                                <div>
                                    <h2 className="text-base font-extrabold text-slate-900 dark:text-white">
                                        Upload Engineering Blueprint
                                    </h2>
                                    <p className="text-xs text-slate-400">
                                        Attach drawings with version control & Ethiopian calendar stamps
                                    </p>
                                </div>
                            </div>
                            <button
                                type="button"
                                onClick={() => setIsUploadOpen(false)}
                                className="p-2 rounded-xl text-slate-400 hover:text-slate-600 hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors"
                            >
                                <X className="w-4 h-4" />
                            </button>
                        </div>

                        <form onSubmit={handleUploadSubmit} className="space-y-4 pt-4">
                            {/* Project Picker */}
                            <div>
                                <label className="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">
                                    Project Site <span className="text-rose-500">*</span>
                                </label>
                                <select
                                    value={uploadData.project_id}
                                    onChange={(e) => {
                                        setUploadData('project_id', e.target.value);
                                        setTargetUploadProject(e.target.value);
                                    }}
                                    className="w-full px-3 py-2 text-xs rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-800 dark:text-slate-200 focus:ring-2 focus:ring-blue-500"
                                    required
                                >
                                    {projects.map((p) => (
                                        <option key={p.id} value={p.id}>
                                            {p.name}
                                        </option>
                                    ))}
                                </select>
                            </div>

                            {/* Title & Document Code */}
                            <div className="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                <div>
                                    <label className="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">
                                        Document Title <span className="text-rose-500">*</span>
                                    </label>
                                    <input
                                        type="text"
                                        placeholder="e.g. Foundation Footing Reinforcement"
                                        value={uploadData.title}
                                        onChange={(e) => setUploadData('title', e.target.value)}
                                        className="w-full px-3 py-2 text-xs rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-800 dark:text-slate-200 focus:ring-2 focus:ring-blue-500"
                                        required
                                    />
                                    {uploadErrors.title && (
                                        <p className="text-[10px] text-rose-500 mt-1">{uploadErrors.title}</p>
                                    )}
                                </div>

                                <div>
                                    <label className="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">
                                        Drawing Code (Optional)
                                    </label>
                                    <input
                                        type="text"
                                        placeholder="e.g. DWG-STR-F1-002"
                                        value={uploadData.document_code}
                                        onChange={(e) => setUploadData('document_code', e.target.value)}
                                        className="w-full px-3 py-2 text-xs rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-800 dark:text-slate-200 focus:ring-2 focus:ring-blue-500"
                                    />
                                </div>
                            </div>

                            {/* Discipline Type & Revision */}
                            <div className="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                <div>
                                    <label className="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">
                                        Discipline Type <span className="text-rose-500">*</span>
                                    </label>
                                    <select
                                        value={uploadData.document_type}
                                        onChange={(e) => setUploadData('document_type', e.target.value)}
                                        className="w-full px-3 py-2 text-xs rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-800 dark:text-slate-200 focus:ring-2 focus:ring-blue-500"
                                        required
                                    >
                                        {documentTypes.map((t) => (
                                            <option key={t.id} value={t.id}>
                                                {t.label}
                                            </option>
                                        ))}
                                    </select>
                                </div>

                                <div>
                                    <label className="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">
                                        Revision Number
                                    </label>
                                    <input
                                        type="text"
                                        placeholder="e.g. Rev 0, Rev A, Rev 1.2"
                                        value={uploadData.revision_number}
                                        onChange={(e) => setUploadData('revision_number', e.target.value)}
                                        className="w-full px-3 py-2 text-xs rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-800 dark:text-slate-200 focus:ring-2 focus:ring-blue-500"
                                    />
                                </div>
                            </div>

                            {/* File Upload Input */}
                            <div>
                                <label className="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">
                                    File Attachment (PDF, CAD/DWG, DXF, ZIP, Images, Docs) <span className="text-rose-500">*</span>
                                </label>
                                <input
                                    type="file"
                                    onChange={(e) => setUploadData('file', e.target.files[0])}
                                    className="w-full px-3 py-2 text-xs rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-800 dark:text-slate-200 focus:ring-2 focus:ring-blue-500 file:mr-3 file:py-1 file:px-2.5 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-blue-600 file:text-white hover:file:bg-blue-700"
                                    required
                                />
                                {uploadErrors.file && (
                                    <p className="text-[10px] text-rose-500 mt-1">{uploadErrors.file}</p>
                                )}
                            </div>

                            {/* Description */}
                            <div>
                                <label className="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">
                                    Engineering Notes / Revision Remarks
                                </label>
                                <textarea
                                    rows="2"
                                    placeholder="Enter revision scope, consultant approval notes, or site instructions..."
                                    value={uploadData.description}
                                    onChange={(e) => setUploadData('description', e.target.value)}
                                    className="w-full px-3 py-2 text-xs rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-800 dark:text-slate-200 focus:ring-2 focus:ring-blue-500"
                                />
                            </div>

                            {/* Submit & Cancel */}
                            <div className="flex items-center justify-end gap-2 pt-3 border-t border-slate-200 dark:border-slate-800">
                                <button
                                    type="button"
                                    onClick={() => setIsUploadOpen(false)}
                                    className="px-4 py-2 rounded-xl text-xs font-semibold text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors cursor-pointer"
                                >
                                    Cancel
                                </button>
                                <button
                                    type="submit"
                                    disabled={uploadProcessing}
                                    className="inline-flex items-center gap-2 px-5 py-2 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs shadow-md transition-all cursor-pointer disabled:opacity-50"
                                >
                                    <UploadCloud className="w-4 h-4" />
                                    <span>{uploadProcessing ? 'Uploading...' : 'Save & Register Document'}</span>
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            )}
        </AuthenticatedLayout>
    );
}
