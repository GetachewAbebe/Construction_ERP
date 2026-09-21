import { useState } from 'react';
import { Head, Link, router, useForm } from '@inertiajs/react';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import {
    FileSpreadsheet,
    PlusCircle,
    Search,
    Calendar,
    Users,
    CloudSun,
    MapPin,
    Eye,
    X,
    ShieldAlert,
    CheckCircle2,
    Printer,
    Camera,
} from 'lucide-react';

export default function Index({ reports, projects = [], filters = {} }) {
    const [search, setSearch] = useState(filters.q || '');
    const [projectFilter, setProjectFilter] = useState(filters.project_id || '');
    const [dateFilter, setDateFilter] = useState(filters.date || '');

    const [showCreateModal, setShowCreateModal] = useState(false);
    const [selectedReport, setSelectedReport] = useState(null);

    const { data, setData, post, reset, processing, errors } = useForm({
        project_id: projects.length > 0 ? projects[0].id : '',
        report_date: new Date().toISOString().split('T')[0],
        weather: 'Clear / Sunny',
        manpower_count: '15',
        work_performed: '',
        materials_received: '',
        machinery_deployed: '',
        safety_incidents: 'Zero safety incidents reported today.',
        photos: [],
    });

    const handleFilter = (e) => {
        if (e) e.preventDefault();
        router.get('/projects/daily-reports', {
            q: search,
            project_id: projectFilter,
            date: dateFilter,
        }, {
            preserveState: true,
            preserveScroll: true,
        });
    };

    const handleCreateSubmit = (e) => {
        e.preventDefault();
        post('/projects/daily-reports', {
            onSuccess: () => {
                setShowCreateModal(false);
                reset();
            },
        });
    };

    const reportList = reports?.data || [];

    return (
        <AuthenticatedLayout title="Daily Site Reports" header="Operations & Plant">
            <div className="space-y-6">
                {/* Header */}
                <div className="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-2 border-b border-slate-200 dark:border-slate-800">
                    <div>
                        <h1 className="text-xl sm:text-2xl font-extrabold tracking-tight text-slate-900 dark:text-white">
                            Site Daily Progress Reports (DPR)
                        </h1>
                        <p className="text-xs sm:text-sm text-slate-500 dark:text-slate-400 mt-0.5">
                            Daily construction site logs, trade manpower attendance, materials delivered, and work execution progress.
                        </p>
                    </div>

                    <button
                        onClick={() => setShowCreateModal(true)}
                        className="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl bg-blue-900 hover:bg-blue-950 text-white font-bold text-xs shadow-sm transition-colors cursor-pointer self-start sm:self-auto"
                    >
                        <PlusCircle className="w-4 h-4" />
                        <span>Log Daily Progress</span>
                    </button>
                </div>

                {/* Filters */}
                <div className="rounded-2xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 p-4 shadow-xs">
                    <form onSubmit={handleFilter} className="grid grid-cols-1 sm:grid-cols-4 gap-3">
                        <div className="relative">
                            <Search className="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-400 pointer-events-none" />
                            <input
                                type="text"
                                placeholder="Search progress logs..."
                                value={search}
                                onChange={(e) => setSearch(e.target.value)}
                                className="w-full pl-9 pr-3 py-2 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-xs text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-900/20"
                            />
                        </div>

                        <div>
                            <select
                                value={projectFilter}
                                onChange={(e) => setProjectFilter(e.target.value)}
                                className="w-full px-3 py-2 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-xs text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-900/20"
                            >
                                <option value="">All Projects</option>
                                {projects.map((p) => (
                                    <option key={p.id} value={p.id}>{p.name}</option>
                                ))}
                            </select>
                        </div>

                        <div>
                            <input
                                type="date"
                                value={dateFilter}
                                onChange={(e) => setDateFilter(e.target.value)}
                                className="w-full px-3 py-2 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-xs text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-900/20"
                            />
                        </div>

                        <div>
                            <button
                                type="submit"
                                className="w-full py-2 rounded-xl bg-blue-900 hover:bg-blue-950 text-white font-bold text-xs transition-colors cursor-pointer"
                            >
                                Filter Reports
                            </button>
                        </div>
                    </form>
                </div>

                {/* Table */}
                <div className="rounded-2xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 shadow-xs overflow-hidden">
                    <div className="overflow-x-auto">
                        <table className="w-full text-left text-xs">
                            <thead className="bg-slate-50 dark:bg-slate-800/60 text-slate-500 uppercase font-semibold text-[10px] tracking-wider border-b border-slate-200 dark:border-slate-800">
                                <tr>
                                    <th className="py-3.5 px-4">Date & Project</th>
                                    <th className="py-3.5 px-4">Weather</th>
                                    <th className="py-3.5 px-4">Manpower</th>
                                    <th className="py-3.5 px-4">Work Performed Summary</th>
                                    <th className="py-3.5 px-4">Recorded By</th>
                                    <th className="py-3.5 px-4 text-right">Details</th>
                                </tr>
                            </thead>
                            <tbody className="divide-y divide-slate-100 dark:divide-slate-800/80">
                                {reportList.length === 0 ? (
                                    <tr>
                                        <td colSpan={6} className="py-8 text-center text-slate-400 text-xs">
                                            No daily progress reports recorded.
                                        </td>
                                    </tr>
                                ) : (
                                    reportList.map((rep) => (
                                        <tr key={rep.id} className="hover:bg-slate-50/70 dark:hover:bg-slate-800/40 transition-colors">
                                            <td className="py-3 px-4">
                                                <div className="font-bold text-slate-900 dark:text-white">
                                                    {rep.project ? rep.project.name : 'Unassigned Site'}
                                                </div>
                                                <div className="text-[11px] text-slate-400 font-mono">
                                                    {rep.report_date ? new Date(rep.report_date).toLocaleDateString() : '—'}
                                                </div>
                                            </td>
                                            <td className="py-3 px-4 text-slate-700 dark:text-slate-300">
                                                <span className="px-2 py-0.5 rounded-md bg-slate-100 dark:bg-slate-800 text-[11px]">
                                                    {rep.weather}
                                                </span>
                                            </td>
                                            <td className="py-3 px-4 font-mono font-bold text-slate-900 dark:text-white">
                                                {rep.manpower_count} staff
                                            </td>
                                            <td className="py-3 px-4 text-slate-600 dark:text-slate-300 max-w-xs truncate">
                                                {rep.work_performed}
                                            </td>
                                            <td className="py-3 px-4 text-slate-500 text-[11px]">
                                                {rep.author ? rep.author.name : 'Site Engineer'}
                                            </td>
                                            <td className="py-3 px-4 text-right">
                                                <div className="flex items-center justify-end gap-1">
                                                    <a
                                                        href={`/projects/daily-reports/${rep.id}/print`}
                                                        target="_blank"
                                                        rel="noopener noreferrer"
                                                        className="p-1.5 rounded-lg text-slate-500 hover:text-teal-600 dark:hover:text-teal-400 hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors cursor-pointer"
                                                        title="Print DPR Voucher / PDF"
                                                    >
                                                        <Printer className="w-3.5 h-3.5" />
                                                    </a>
                                                    <button
                                                        onClick={() => setSelectedReport(rep)}
                                                        className="p-1.5 rounded-lg text-slate-500 hover:text-blue-900 dark:hover:text-white hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors cursor-pointer"
                                                        title="View Full Diary"
                                                    >
                                                        <Eye className="w-3.5 h-3.5" />
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
                    {reports?.links && reports.links.length > 3 && (
                        <div className="px-4 py-3 border-t border-slate-200 dark:border-slate-800 flex items-center justify-between text-xs text-slate-500">
                            <div>Showing {reports.from || 0} to {reports.to || 0} of {reports.total || 0} reports</div>
                            <div className="flex items-center gap-1">
                                {reports.links.map((link, idx) => (
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
                </div>

                {/* Create Modal */}
                {showCreateModal && (
                    <div className="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-xs">
                        <div className="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-3xl p-6 w-full max-w-2xl max-h-[90vh] overflow-y-auto space-y-4">
                            <div className="flex items-center justify-between pb-3 border-b border-slate-100 dark:border-slate-800">
                                <h3 className="font-extrabold text-slate-900 dark:text-white text-base">
                                    Log Daily Progress Report (DPR)
                                </h3>
                                <button
                                    onClick={() => setShowCreateModal(false)}
                                    className="p-1 rounded-lg text-slate-400 hover:text-slate-600 dark:hover:text-slate-200"
                                >
                                    <X className="w-4 h-4" />
                                </button>
                            </div>

                            <form onSubmit={handleCreateSubmit} className="space-y-4">
                                <div className="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                    <div>
                                        <label className="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">
                                            Project Site <span className="text-rose-500">*</span>
                                        </label>
                                        <select
                                            required
                                            value={data.project_id}
                                            onChange={(e) => setData('project_id', e.target.value)}
                                            className="w-full px-3 py-2 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-xs text-slate-900 dark:text-white"
                                        >
                                            {projects.map((p) => (
                                                <option key={p.id} value={p.id}>{p.name}</option>
                                            ))}
                                        </select>
                                    </div>

                                    <div>
                                        <label className="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">
                                            Report Date <span className="text-rose-500">*</span>
                                        </label>
                                        <input
                                            type="date"
                                            required
                                            value={data.report_date}
                                            onChange={(e) => setData('report_date', e.target.value)}
                                            className="w-full px-3 py-2 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-xs text-slate-900 dark:text-white"
                                        />
                                    </div>

                                    <div>
                                        <label className="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">
                                            Weather Conditions
                                        </label>
                                        <input
                                            type="text"
                                            value={data.weather}
                                            onChange={(e) => setData('weather', e.target.value)}
                                            placeholder="e.g. Sunny / Clear, Light Rain, Heavy Wind"
                                            className="w-full px-3 py-2 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-xs text-slate-900 dark:text-white"
                                        />
                                    </div>

                                    <div>
                                        <label className="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">
                                            Site Trade Manpower
                                        </label>
                                        <input
                                            type="number"
                                            value={data.manpower_count}
                                            onChange={(e) => setData('manpower_count', e.target.value)}
                                            className="w-full px-3 py-2 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-xs text-slate-900 dark:text-white font-mono"
                                        />
                                    </div>

                                    <div className="sm:col-span-2">
                                        <label className="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">
                                            Work Activities Performed <span className="text-rose-500">*</span>
                                        </label>
                                        <textarea
                                            rows={3}
                                            required
                                            value={data.work_performed}
                                            onChange={(e) => setData('work_performed', e.target.value)}
                                            placeholder="Foundations poured, rebar installation grid B-4, formwork removal on level 2..."
                                            className="w-full px-3 py-2 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-xs text-slate-900 dark:text-white"
                                        />
                                    </div>

                                    <div>
                                        <label className="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">
                                            Materials Received On-Site
                                        </label>
                                        <textarea
                                            rows={2}
                                            value={data.materials_received}
                                            onChange={(e) => setData('materials_received', e.target.value)}
                                            placeholder="400 bags OPC cement, 12mm rebar bundles..."
                                            className="w-full px-3 py-2 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-xs text-slate-900 dark:text-white"
                                        />
                                    </div>

                                    <div>
                                        <label className="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">
                                            Heavy Plant & Machinery Deployed
                                        </label>
                                        <textarea
                                            rows={2}
                                            value={data.machinery_deployed}
                                            onChange={(e) => setData('machinery_deployed', e.target.value)}
                                            placeholder="1x CAT 320D Excavator, 2x Sino dump trucks..."
                                            className="w-full px-3 py-2 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-xs text-slate-900 dark:text-white"
                                        />
                                    </div>

                                    <div className="sm:col-span-2">
                                        <label className="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">
                                            Safety, Incidents & HSE Notes
                                        </label>
                                        <input
                                            type="text"
                                            value={data.safety_incidents}
                                            onChange={(e) => setData('safety_incidents', e.target.value)}
                                            className="w-full px-3 py-2 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-xs text-slate-900 dark:text-white"
                                        />
                                    </div>

                                    <div className="sm:col-span-2">
                                        <label className="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">
                                            Field Photos & Visual Evidence (Multi-Upload)
                                        </label>
                                        <input
                                            type="file"
                                            multiple
                                            accept="image/*"
                                            onChange={(e) => setData('photos', Array.from(e.target.files))}
                                            className="w-full text-xs text-slate-500 file:mr-3 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-teal-50 file:text-teal-700 dark:file:bg-teal-950/40 dark:file:text-teal-300 hover:file:bg-teal-100"
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
                                        disabled={processing}
                                        className="px-5 py-2 rounded-xl bg-blue-900 hover:bg-blue-950 text-white font-bold text-xs cursor-pointer"
                                    >
                                        Archive Diary Log
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                )}

                {/* Detail View Modal */}
                {selectedReport && (
                    <div className="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-xs">
                        <div className="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-3xl p-6 w-full max-w-2xl max-h-[90vh] overflow-y-auto space-y-5">
                            <div className="flex items-center justify-between pb-3 border-b border-slate-100 dark:border-slate-800">
                                <div>
                                    <h3 className="font-extrabold text-slate-900 dark:text-white text-base">
                                        {selectedReport.project?.name || 'Site Progress Report'}
                                    </h3>
                                    <p className="text-xs text-slate-400 font-mono">
                                        Date: {selectedReport.report_date ? new Date(selectedReport.report_date).toDateString() : '—'}
                                    </p>
                                </div>
                                <button
                                    onClick={() => setSelectedReport(null)}
                                    className="p-1 rounded-lg text-slate-400 hover:text-slate-600 dark:hover:text-slate-200"
                                >
                                    <X className="w-4 h-4" />
                                </button>
                            </div>

                            <div className="grid grid-cols-2 gap-3 text-xs">
                                <div className="p-3 rounded-xl bg-slate-50 dark:bg-slate-800/60">
                                    <span className="text-slate-400 block font-semibold">Weather</span>
                                    <span className="font-bold text-slate-900 dark:text-white">{selectedReport.weather}</span>
                                </div>
                                <div className="p-3 rounded-xl bg-slate-50 dark:bg-slate-800/60">
                                    <span className="text-slate-400 block font-semibold">Manpower</span>
                                    <span className="font-bold text-slate-900 dark:text-white font-mono">{selectedReport.manpower_count} Workers</span>
                                </div>
                            </div>

                            <div className="space-y-3 text-xs">
                                <div className="p-4 rounded-xl border border-slate-100 dark:border-slate-800 bg-slate-50/60 dark:bg-slate-800/40">
                                    <span className="text-[10px] font-bold text-slate-400 uppercase tracking-wider block mb-1">
                                        Work Performed & Progress
                                    </span>
                                    <p className="text-slate-800 dark:text-slate-200 leading-relaxed whitespace-pre-wrap">
                                        {selectedReport.work_performed}
                                    </p>
                                </div>

                                {selectedReport.materials_received && (
                                    <div className="p-4 rounded-xl border border-slate-100 dark:border-slate-800 bg-slate-50/60 dark:bg-slate-800/40">
                                        <span className="text-[10px] font-bold text-slate-400 uppercase tracking-wider block mb-1">
                                            Materials Received
                                        </span>
                                        <p className="text-slate-800 dark:text-slate-200 leading-relaxed">
                                            {selectedReport.materials_received}
                                        </p>
                                    </div>
                                )}

                                {selectedReport.machinery_deployed && (
                                    <div className="p-4 rounded-xl border border-slate-100 dark:border-slate-800 bg-slate-50/60 dark:bg-slate-800/40">
                                        <span className="text-[10px] font-bold text-slate-400 uppercase tracking-wider block mb-1">
                                            Machinery & Fleet Operating
                                        </span>
                                        <p className="text-slate-800 dark:text-slate-200 leading-relaxed">
                                            {selectedReport.machinery_deployed}
                                        </p>
                                    </div>
                                )}

                                {selectedReport.safety_incidents && (
                                    <div className="p-4 rounded-xl border border-slate-100 dark:border-slate-800 bg-slate-50/60 dark:bg-slate-800/40">
                                        <span className="text-[10px] font-bold text-slate-400 uppercase tracking-wider block mb-1">
                                            Health, Safety & Environment (HSE)
                                        </span>
                                        <p className="text-slate-800 dark:text-slate-200 leading-relaxed">
                                            {selectedReport.safety_incidents}
                                        </p>
                                    </div>
                                )}
                            </div>

                            <div className="pt-3 border-t border-slate-100 dark:border-slate-800 text-right">
                                <button
                                    onClick={() => setSelectedReport(null)}
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
