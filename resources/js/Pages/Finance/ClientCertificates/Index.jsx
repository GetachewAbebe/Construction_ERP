import { useState } from 'react';
import { Head, Link, router } from '@inertiajs/react';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import {
    FileText,
    Plus,
    Search,
    Filter,
    CheckCircle2,
    Clock,
    Eye,
    Printer,
    Building2,
    DollarSign,
    ShieldAlert,
    PiggyBank,
    TrendingUp,
} from 'lucide-react';

export default function Index({
    certificates,
    stats = { total_certificates: 0, total_certified: 0, total_collected: 0, outstanding_receivable: 0, total_retention_held: 0 },
    projects = [],
    filters = {},
}) {
    const [search, setSearch] = useState(filters.q || '');
    const [status, setStatus] = useState(filters.status || '');
    const [projectId, setProjectId] = useState(filters.project_id || '');

    const handleFilter = (e) => {
        if (e) e.preventDefault();
        router.get('/finance/client-certificates', {
            q: search,
            status,
            project_id: projectId,
        }, {
            preserveState: true,
            preserveScroll: true,
        });
    };

    const certList = certificates?.data || [];

    const getStatusBadge = (st) => {
        switch (st) {
            case 'paid':
                return <span className="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-semibold bg-emerald-50 dark:bg-emerald-950/60 text-emerald-700 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-800"><CheckCircle2 className="w-3 h-3" /> Fully Paid</span>;
            case 'partially_paid':
                return <span className="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-semibold bg-blue-50 dark:bg-blue-950/60 text-blue-700 dark:text-blue-300 border border-blue-200 dark:border-blue-800"><Clock className="w-3 h-3" /> Partially Paid</span>;
            case 'certified':
                return <span className="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-semibold bg-purple-50 dark:bg-purple-950/60 text-purple-700 dark:text-purple-300 border border-purple-200 dark:border-purple-800"><CheckCircle2 className="w-3 h-3" /> Consultant Certified</span>;
            case 'submitted':
                return <span className="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-semibold bg-amber-50 dark:bg-amber-950/60 text-amber-700 dark:text-amber-300 border border-amber-200 dark:border-amber-800"><Clock className="w-3 h-3" /> Under Review</span>;
            default:
                return <span className="px-2.5 py-0.5 rounded-full text-xs font-semibold bg-slate-100 dark:bg-slate-800 text-slate-700">{st}</span>;
        }
    };

    return (
        <AuthenticatedLayout title="Client Progress Claims (IPC)" header="Finance & Commercial">
            <div className="space-y-6">
                {/* Header */}
                <div className="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-2 border-b border-slate-200 dark:border-slate-800">
                    <div>
                        <h1 className="text-xl sm:text-2xl font-extrabold tracking-tight text-slate-900 dark:text-white flex items-center gap-2.5">
                            <FileText className="w-7 h-7 text-indigo-600 dark:text-indigo-400" />
                            Client Interim Payment Certificates (IPC)
                        </h1>
                        <p className="text-xs sm:text-sm text-slate-500 dark:text-slate-400 mt-0.5">
                            Contractor progress billing claims submitted to project owners and supervising consultant engineers.
                        </p>
                    </div>

                    <Link
                        href="/finance/client-certificates/create"
                        className="inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-xs shadow-md shadow-indigo-500/20 transition-all cursor-pointer"
                    >
                        <Plus className="w-4 h-4" />
                        <span>Submit Progress Claim (IPC)</span>
                    </Link>
                </div>

                {/* Metrics */}
                <div className="grid grid-cols-2 lg:grid-cols-4 gap-4">
                    <div className="rounded-2xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 p-4">
                        <div className="text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider flex items-center gap-1.5">
                            <TrendingUp className="w-3.5 h-3.5 text-indigo-500" /> Certified Revenue
                        </div>
                        <div className="text-xl font-black text-slate-900 dark:text-white mt-1 truncate">
                            {Number(stats.total_certified).toLocaleString(undefined, { maximumFractionDigits: 0 })} ETB
                        </div>
                    </div>

                    <div className="rounded-2xl border border-emerald-200 dark:border-emerald-900/40 bg-emerald-50/50 dark:bg-emerald-950/20 p-4">
                        <div className="text-xs font-bold text-emerald-600 dark:text-emerald-400 uppercase tracking-wider flex items-center gap-1.5">
                            <DollarSign className="w-3.5 h-3.5" /> Total Cash Collected
                        </div>
                        <div className="text-xl font-black text-emerald-700 dark:text-emerald-300 mt-1 truncate">
                            {Number(stats.total_collected).toLocaleString(undefined, { maximumFractionDigits: 0 })} ETB
                        </div>
                    </div>

                    <div className="rounded-2xl border border-rose-200 dark:border-rose-900/40 bg-rose-50/50 dark:bg-rose-950/20 p-4">
                        <div className="text-xs font-bold text-rose-600 dark:text-rose-400 uppercase tracking-wider flex items-center gap-1.5">
                            <Clock className="w-3.5 h-3.5" /> Receivables Balance Due
                        </div>
                        <div className="text-xl font-black text-rose-700 dark:text-rose-300 mt-1 truncate">
                            {Number(stats.outstanding_receivable).toLocaleString(undefined, { maximumFractionDigits: 0 })} ETB
                        </div>
                    </div>

                    <div className="rounded-2xl border border-amber-200 dark:border-amber-900/40 bg-amber-50/50 dark:bg-amber-950/20 p-4">
                        <div className="text-xs font-bold text-amber-600 dark:text-amber-400 uppercase tracking-wider flex items-center gap-1.5">
                            <PiggyBank className="w-3.5 h-3.5" /> Retention Held by Clients
                        </div>
                        <div className="text-xl font-black text-amber-700 dark:text-amber-300 mt-1 truncate">
                            {Number(stats.total_retention_held).toLocaleString(undefined, { maximumFractionDigits: 0 })} ETB
                        </div>
                    </div>
                </div>

                {/* Filters */}
                <div className="rounded-2xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 p-4">
                    <form onSubmit={handleFilter} className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3">
                        <div className="relative">
                            <Search className="w-4 h-4 absolute left-3 top-3 text-slate-400" />
                            <input
                                type="text"
                                value={search}
                                onChange={(e) => setSearch(e.target.value)}
                                placeholder="Search IPC #, project, consultant..."
                                className="w-full pl-9 pr-3 py-2 text-xs rounded-xl border border-slate-200 dark:border-slate-800 bg-slate-50 dark:bg-slate-950 text-slate-800 dark:text-slate-100 focus:outline-none focus:ring-2 focus:ring-indigo-500"
                            />
                        </div>

                        <div>
                            <select
                                value={projectId}
                                onChange={(e) => setProjectId(e.target.value)}
                                className="w-full px-3 py-2 text-xs rounded-xl border border-slate-200 dark:border-slate-800 bg-slate-50 dark:bg-slate-950 text-slate-800 dark:text-slate-100 focus:outline-none focus:ring-2 focus:ring-indigo-500"
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
                                className="w-full px-3 py-2 text-xs rounded-xl border border-slate-200 dark:border-slate-800 bg-slate-50 dark:bg-slate-950 text-slate-800 dark:text-slate-100 focus:outline-none focus:ring-2 focus:ring-indigo-500"
                            >
                                <option value="">All Statuses</option>
                                <option value="submitted">Under Review (Submitted)</option>
                                <option value="certified">Certified (Consultant Approved)</option>
                                <option value="partially_paid">Partially Paid</option>
                                <option value="paid">Fully Paid</option>
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
                                    setProjectId('');
                                    router.get('/finance/client-certificates');
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
                                    <th className="py-3.5 px-4">Certificate #</th>
                                    <th className="py-3.5 px-4">Project & Employer</th>
                                    <th className="py-3.5 px-4">Billing Period</th>
                                    <th className="py-3.5 px-4 text-right">Current Gross</th>
                                    <th className="py-3.5 px-4 text-right">Retention</th>
                                    <th className="py-3.5 px-4 text-right">Total Certified (ETB)</th>
                                    <th className="py-3.5 px-4 text-right">Balance Due</th>
                                    <th className="py-3.5 px-4">Status</th>
                                    <th className="py-3.5 px-4 text-right">Actions</th>
                                </tr>
                            </thead>
                            <tbody className="divide-y divide-slate-100 dark:divide-slate-800/60">
                                {certList.length === 0 ? (
                                    <tr>
                                        <td colSpan={9} className="py-12 text-center text-slate-500 dark:text-slate-400">
                                            <FileText className="w-10 h-10 mx-auto text-slate-300 dark:text-slate-600 mb-2" />
                                            <p className="font-semibold">No client progress billing certificates recorded.</p>
                                            <p className="text-xs mt-0.5">Submit your first monthly Interim Payment Certificate (IPC) to track cash inflows.</p>
                                        </td>
                                    </tr>
                                ) : (
                                    certList.map((ipc) => (
                                        <tr key={ipc.id} className="hover:bg-slate-50/50 dark:hover:bg-slate-800/30 transition-colors">
                                            <td className="py-3.5 px-4 font-bold text-indigo-600 dark:text-indigo-400">
                                                <Link href={`/finance/client-certificates/${ipc.id}`} className="hover:underline">
                                                    {ipc.certificate_no}
                                                </Link>
                                                <div className="text-[10px] text-slate-400 font-normal">
                                                    IPC #{ipc.ipc_sequence}
                                                </div>
                                            </td>
                                            <td className="py-3.5 px-4 text-slate-800 dark:text-slate-200">
                                                <div className="font-semibold">{ipc.project?.name}</div>
                                                <div className="text-[10px] text-slate-400">{ipc.client_name}</div>
                                            </td>
                                            <td className="py-3.5 px-4 text-slate-600 dark:text-slate-300 font-medium">
                                                {ipc.period_start ? new Date(ipc.period_start).toLocaleDateString() : ''} - {ipc.period_end ? new Date(ipc.period_end).toLocaleDateString() : ''}
                                            </td>
                                            <td className="py-3.5 px-4 text-right font-semibold text-slate-800 dark:text-slate-200">
                                                {Number(ipc.current_gross_amount).toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 })}
                                            </td>
                                            <td className="py-3.5 px-4 text-right text-amber-600 dark:text-amber-400 font-semibold">
                                                ({Number(ipc.retention_deduction).toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 })})
                                            </td>
                                            <td className="py-3.5 px-4 text-right font-black text-slate-900 dark:text-white">
                                                {Number(ipc.total_certified_amount).toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 })}
                                            </td>
                                            <td className="py-3.5 px-4 text-right font-bold text-rose-600 dark:text-rose-400">
                                                {Number(ipc.balance_due).toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 })}
                                            </td>
                                            <td className="py-3.5 px-4">
                                                {getStatusBadge(ipc.status)}
                                            </td>
                                            <td className="py-3.5 px-4 text-right">
                                                <div className="inline-flex items-center gap-1.5">
                                                    <Link
                                                        href={`/finance/client-certificates/${ipc.id}`}
                                                        className="p-1.5 rounded-lg border border-slate-200 dark:border-slate-800 text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors"
                                                        title="View Statement & Receipts"
                                                    >
                                                        <Eye className="w-3.5 h-3.5" />
                                                    </Link>

                                                    <a
                                                        href={`/finance/client-certificates/${ipc.id}/print`}
                                                        target="_blank"
                                                        rel="noreferrer"
                                                        className="p-1.5 rounded-lg border border-slate-200 dark:border-slate-800 text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors"
                                                        title="Print Corporate IPC"
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
                    {certificates?.links && certificates.links.length > 3 && (
                        <div className="p-4 border-t border-slate-100 dark:border-slate-800 flex items-center justify-between">
                            <div className="text-xs text-slate-500">
                                Showing {certificates.from || 0} to {certificates.to || 0} of {certificates.total} certificates
                            </div>
                            <div className="flex gap-1">
                                {certificates.links.map((link, idx) => (
                                    <Link
                                        key={idx}
                                        href={link.url || '#'}
                                        dangerouslySetInnerHTML={{ __html: link.label }}
                                        className={`px-3 py-1 text-xs rounded-lg font-semibold transition-colors ${
                                            link.active
                                                ? 'bg-indigo-600 text-white'
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
