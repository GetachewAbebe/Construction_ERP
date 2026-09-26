import { useState } from 'react';
import { Head, Link, router } from '@inertiajs/react';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import {
    FileInput,
    Plus,
    Search,
    Filter,
    CheckCircle2,
    Clock,
    Eye,
    Printer,
    ShoppingCart,
    Building2,
    Calendar,
    Truck,
    PackageCheck,
} from 'lucide-react';

export default function Index({
    notes,
    stats = { total: 0, today: 0, this_month: 0 },
    projects = [],
    filters = {},
}) {
    const [search, setSearch] = useState(filters.q || '');
    const [projectId, setProjectId] = useState(filters.project_id || '');

    const handleFilter = (e) => {
        if (e) e.preventDefault();
        router.get('/inventory/goods-receiving', {
            q: search,
            project_id: projectId,
        }, {
            preserveState: true,
            preserveScroll: true,
        });
    };

    const noteList = notes?.data || [];

    return (
        <AuthenticatedLayout title="Goods Receiving Notes" header="Material Procurement">
            <div className="space-y-6">
                {/* Header */}
                <div className="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-2 border-b border-slate-200 dark:border-slate-800">
                    <div>
                        <h1 className="text-xl sm:text-2xl font-extrabold tracking-tight text-slate-900 dark:text-white flex items-center gap-2.5">
                            <FileInput className="w-7 h-7 text-emerald-600 dark:text-emerald-400" />
                            Store Receiving Vouchers (GRN)
                        </h1>
                        <p className="text-xs sm:text-sm text-slate-500 dark:text-slate-400 mt-0.5">
                            Warehouse and site receiving vouchers that verify material delivery and automatically post to inventory stock.
                        </p>
                    </div>

                    <Link
                        href="/inventory/goods-receiving/create"
                        className="inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs shadow-md shadow-emerald-500/20 transition-all cursor-pointer"
                    >
                        <Plus className="w-4 h-4" />
                        <span>Receive Delivery (GRN)</span>
                    </Link>
                </div>

                {/* Metrics */}
                <div className="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <div className="rounded-2xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 p-4">
                        <div className="text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Total Deliveries Logged</div>
                        <div className="text-2xl font-black text-slate-900 dark:text-white mt-1">{stats.total}</div>
                    </div>
                    <div className="rounded-2xl border border-emerald-200 dark:border-emerald-900/40 bg-emerald-50/50 dark:bg-emerald-950/20 p-4">
                        <div className="text-xs font-bold text-emerald-600 dark:text-emerald-400 uppercase tracking-wider flex items-center gap-1.5">
                            <PackageCheck className="w-3.5 h-3.5" /> Received Today
                        </div>
                        <div className="text-2xl font-black text-emerald-700 dark:text-emerald-300 mt-1">{stats.today}</div>
                    </div>
                    <div className="rounded-2xl border border-blue-200 dark:border-blue-900/40 bg-blue-50/50 dark:bg-blue-950/20 p-4">
                        <div className="text-xs font-bold text-blue-600 dark:text-blue-400 uppercase tracking-wider flex items-center gap-1.5">
                            <Calendar className="w-3.5 h-3.5" /> Received This Month
                        </div>
                        <div className="text-2xl font-black text-blue-700 dark:text-blue-300 mt-1">{stats.this_month}</div>
                    </div>
                </div>

                {/* Filters */}
                <div className="rounded-2xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 p-4">
                    <form onSubmit={handleFilter} className="grid grid-cols-1 sm:grid-cols-3 gap-3">
                        <div className="relative">
                            <Search className="w-4 h-4 absolute left-3 top-3 text-slate-400" />
                            <input
                                type="text"
                                value={search}
                                onChange={(e) => setSearch(e.target.value)}
                                placeholder="Search GRN #, PO #, Waybill..."
                                className="w-full pl-9 pr-3 py-2 text-xs rounded-xl border border-slate-200 dark:border-slate-800 bg-slate-50 dark:bg-slate-950 text-slate-800 dark:text-slate-100 focus:outline-none focus:ring-2 focus:ring-emerald-500"
                            />
                        </div>

                        <div>
                            <select
                                value={projectId}
                                onChange={(e) => setProjectId(e.target.value)}
                                className="w-full px-3 py-2 text-xs rounded-xl border border-slate-200 dark:border-slate-800 bg-slate-50 dark:bg-slate-950 text-slate-800 dark:text-slate-100 focus:outline-none focus:ring-2 focus:ring-emerald-500"
                            >
                                <option value="">All Projects / Stores</option>
                                {projects.map((p) => (
                                    <option key={p.id} value={p.id}>{p.name}</option>
                                ))}
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
                                    setProjectId('');
                                    router.get('/inventory/goods-receiving');
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
                                    <th className="py-3.5 px-4">GRN Voucher #</th>
                                    <th className="py-3.5 px-4">PO Reference</th>
                                    <th className="py-3.5 px-4">Supplier / Vendor</th>
                                    <th className="py-3.5 px-4">Project Site</th>
                                    <th className="py-3.5 px-4">Vendor Delivery Note #</th>
                                    <th className="py-3.5 px-4">Received Date</th>
                                    <th className="py-3.5 px-4">Storekeeper</th>
                                    <th className="py-3.5 px-4 text-right">Actions</th>
                                </tr>
                            </thead>
                            <tbody className="divide-y divide-slate-100 dark:divide-slate-800/60">
                                {noteList.length === 0 ? (
                                    <tr>
                                        <td colSpan={8} className="py-12 text-center text-slate-500 dark:text-slate-400">
                                            <FileInput className="w-10 h-10 mx-auto text-slate-300 dark:text-slate-600 mb-2" />
                                            <p className="font-semibold">No goods receiving notes recorded yet.</p>
                                            <p className="text-xs mt-0.5">Record a material delivery against an issued purchase order to update stock.</p>
                                        </td>
                                    </tr>
                                ) : (
                                    noteList.map((grn) => (
                                        <tr key={grn.id} className="hover:bg-slate-50/50 dark:hover:bg-slate-800/30 transition-colors">
                                            <td className="py-3.5 px-4 font-bold text-emerald-600 dark:text-emerald-400">
                                                <Link href={`/inventory/goods-receiving/${grn.id}`} className="hover:underline">
                                                    {grn.grn_no}
                                                </Link>
                                                {grn.items && (
                                                    <div className="text-[10px] font-normal text-slate-400">
                                                        {grn.items.length} item(s) inspected
                                                    </div>
                                                )}
                                            </td>
                                            <td className="py-3.5 px-4 font-semibold text-slate-800 dark:text-slate-200">
                                                {grn.purchase_order ? (
                                                    <Link href={`/inventory/purchase-orders/${grn.purchase_order.id}`} className="hover:text-blue-600 hover:underline">
                                                        {grn.purchase_order.po_no}
                                                    </Link>
                                                ) : 'N/A'}
                                            </td>
                                            <td className="py-3.5 px-4 text-slate-800 dark:text-slate-200">
                                                <div className="font-semibold">{grn.vendor?.name}</div>
                                            </td>
                                            <td className="py-3.5 px-4 text-slate-700 dark:text-slate-300">
                                                <div className="font-semibold">{grn.project ? grn.project.name : 'Central Warehouse'}</div>
                                            </td>
                                            <td className="py-3.5 px-4 font-mono font-medium text-slate-600 dark:text-slate-300">
                                                {grn.delivery_note_no || 'N/A'}
                                            </td>
                                            <td className="py-3.5 px-4 text-slate-600 dark:text-slate-300 font-medium">
                                                {grn.received_date ? new Date(grn.received_date).toLocaleDateString() : 'N/A'}
                                            </td>
                                            <td className="py-3.5 px-4 text-slate-600 dark:text-slate-300">
                                                {grn.receiver?.name || 'Storekeeper'}
                                            </td>
                                            <td className="py-3.5 px-4 text-right">
                                                <div className="inline-flex items-center gap-1.5">
                                                    <Link
                                                        href={`/inventory/goods-receiving/${grn.id}`}
                                                        className="p-1.5 rounded-lg border border-slate-200 dark:border-slate-800 text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors"
                                                        title="View Details"
                                                    >
                                                        <Eye className="w-3.5 h-3.5" />
                                                    </Link>

                                                    <a
                                                        href={`/inventory/goods-receiving/${grn.id}/print`}
                                                        target="_blank"
                                                        rel="noreferrer"
                                                        className="p-1.5 rounded-lg border border-slate-200 dark:border-slate-800 text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors"
                                                        title="Print Store Receiving Voucher"
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
                    {notes?.links && notes.links.length > 3 && (
                        <div className="p-4 border-t border-slate-100 dark:border-slate-800 flex items-center justify-between">
                            <div className="text-xs text-slate-500">
                                Showing {notes.from || 0} to {notes.to || 0} of {notes.total} receipts
                            </div>
                            <div className="flex gap-1">
                                {notes.links.map((link, idx) => (
                                    <Link
                                        key={idx}
                                        href={link.url || '#'}
                                        dangerouslySetInnerHTML={{ __html: link.label }}
                                        className={`px-3 py-1 text-xs rounded-lg font-semibold transition-colors ${
                                            link.active
                                                ? 'bg-emerald-600 text-white'
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
