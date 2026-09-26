import { useState } from 'react';
import { Head, Link, router } from '@inertiajs/react';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import {
    ShoppingCart,
    Plus,
    Search,
    Filter,
    CheckCircle2,
    Clock,
    XCircle,
    Eye,
    Printer,
    FileInput,
    Truck,
    Building2,
    Banknote,
} from 'lucide-react';

export default function Index({
    orders,
    stats = { total: 0, issued: 0, partially_received: 0, received: 0, total_value: 0 },
    vendors = [],
    projects = [],
    filters = {},
}) {
    const [search, setSearch] = useState(filters.q || '');
    const [status, setStatus] = useState(filters.status || '');
    const [vendorId, setVendorId] = useState(filters.vendor_id || '');
    const [projectId, setProjectId] = useState(filters.project_id || '');

    const handleFilter = (e) => {
        if (e) e.preventDefault();
        router.get('/inventory/purchase-orders', {
            q: search,
            status,
            vendor_id: vendorId,
            project_id: projectId,
        }, {
            preserveState: true,
            preserveScroll: true,
        });
    };

    const orderList = orders?.data || [];

    const getStatusBadge = (st) => {
        switch (st) {
            case 'received':
                return <span className="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-semibold bg-emerald-50 dark:bg-emerald-950/60 text-emerald-700 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-800"><CheckCircle2 className="w-3 h-3" /> Fully Received</span>;
            case 'partially_received':
                return <span className="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-semibold bg-amber-50 dark:bg-amber-950/60 text-amber-700 dark:text-amber-300 border border-amber-200 dark:border-amber-800"><Clock className="w-3 h-3" /> Partial Delivery</span>;
            case 'issued':
                return <span className="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-semibold bg-blue-50 dark:bg-blue-950/60 text-blue-700 dark:text-blue-300 border border-blue-200 dark:border-blue-800"><ShoppingCart className="w-3 h-3" /> Issued / Pending</span>;
            case 'cancelled':
                return <span className="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-semibold bg-rose-50 dark:bg-rose-950/60 text-rose-700 dark:text-rose-300 border border-rose-200 dark:border-rose-800"><XCircle className="w-3 h-3" /> Cancelled</span>;
            default:
                return <span className="px-2.5 py-0.5 rounded-full text-xs font-semibold bg-slate-100 dark:bg-slate-800 text-slate-700">{st}</span>;
        }
    };

    return (
        <AuthenticatedLayout title="Purchase Orders" header="Material Procurement">
            <div className="space-y-6">
                {/* Header */}
                <div className="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-2 border-b border-slate-200 dark:border-slate-800">
                    <div>
                        <h1 className="text-xl sm:text-2xl font-extrabold tracking-tight text-slate-900 dark:text-white flex items-center gap-2.5">
                            <ShoppingCart className="w-7 h-7 text-indigo-600 dark:text-indigo-400" />
                            Purchase Orders (PO)
                        </h1>
                        <p className="text-xs sm:text-sm text-slate-500 dark:text-slate-400 mt-0.5">
                            Legally binding supply contracts issued to vendors for jobsite and warehouse materials.
                        </p>
                    </div>

                    <Link
                        href="/inventory/purchase-orders/create"
                        className="inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-xs shadow-md shadow-indigo-500/20 transition-all cursor-pointer"
                    >
                        <Plus className="w-4 h-4" />
                        <span>Issue Purchase Order</span>
                    </Link>
                </div>

                {/* Metrics */}
                <div className="grid grid-cols-2 lg:grid-cols-4 gap-4">
                    <div className="rounded-2xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 p-4">
                        <div className="text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Total Orders</div>
                        <div className="text-2xl font-black text-slate-900 dark:text-white mt-1">{stats.total}</div>
                    </div>
                    <div className="rounded-2xl border border-blue-200 dark:border-blue-900/40 bg-blue-50/50 dark:bg-blue-950/20 p-4">
                        <div className="text-xs font-bold text-blue-600 dark:text-blue-400 uppercase tracking-wider flex items-center gap-1.5">
                            <ShoppingCart className="w-3.5 h-3.5" /> Issued / Pending
                        </div>
                        <div className="text-2xl font-black text-blue-700 dark:text-blue-300 mt-1">{stats.issued}</div>
                    </div>
                    <div className="rounded-2xl border border-amber-200 dark:border-amber-900/40 bg-amber-50/50 dark:bg-amber-950/20 p-4">
                        <div className="text-xs font-bold text-amber-600 dark:text-amber-400 uppercase tracking-wider flex items-center gap-1.5">
                            <Clock className="w-3.5 h-3.5" /> Partial Deliveries
                        </div>
                        <div className="text-2xl font-black text-amber-700 dark:text-amber-300 mt-1">{stats.partially_received}</div>
                    </div>
                    <div className="rounded-2xl border border-emerald-200 dark:border-emerald-900/40 bg-emerald-50/50 dark:bg-emerald-950/20 p-4">
                        <div className="text-xs font-bold text-emerald-600 dark:text-emerald-400 uppercase tracking-wider flex items-center gap-1.5">
                            <Banknote className="w-3.5 h-3.5" /> Total Order Value
                        </div>
                        <div className="text-xl font-black text-emerald-700 dark:text-emerald-300 mt-1 truncate">
                            {Number(stats.total_value).toLocaleString(undefined, { maximumFractionDigits: 0 })} ETB
                        </div>
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
                                placeholder="Search PO #, vendor, site..."
                                className="w-full pl-9 pr-3 py-2 text-xs rounded-xl border border-slate-200 dark:border-slate-800 bg-slate-50 dark:bg-slate-950 text-slate-800 dark:text-slate-100 focus:outline-none focus:ring-2 focus:ring-indigo-500"
                            />
                        </div>

                        <div>
                            <select
                                value={vendorId}
                                onChange={(e) => setVendorId(e.target.value)}
                                className="w-full px-3 py-2 text-xs rounded-xl border border-slate-200 dark:border-slate-800 bg-slate-50 dark:bg-slate-950 text-slate-800 dark:text-slate-100 focus:outline-none focus:ring-2 focus:ring-indigo-500"
                            >
                                <option value="">All Vendors</option>
                                {vendors.map((v) => (
                                    <option key={v.id} value={v.id}>{v.name} ({v.code})</option>
                                ))}
                            </select>
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
                                <option value="issued">Issued</option>
                                <option value="partially_received">Partially Received</option>
                                <option value="received">Fully Received</option>
                                <option value="cancelled">Cancelled</option>
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
                                    setVendorId('');
                                    setProjectId('');
                                    router.get('/inventory/purchase-orders');
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
                                    <th className="py-3.5 px-4">PO Number</th>
                                    <th className="py-3.5 px-4">Vendor</th>
                                    <th className="py-3.5 px-4">Project Site</th>
                                    <th className="py-3.5 px-4">Order Date</th>
                                    <th className="py-3.5 px-4 text-right">Total Amount (ETB)</th>
                                    <th className="py-3.5 px-4">Status</th>
                                    <th className="py-3.5 px-4 text-right">Actions</th>
                                </tr>
                            </thead>
                            <tbody className="divide-y divide-slate-100 dark:divide-slate-800/60">
                                {orderList.length === 0 ? (
                                    <tr>
                                        <td colSpan={7} className="py-12 text-center text-slate-500 dark:text-slate-400">
                                            <ShoppingCart className="w-10 h-10 mx-auto text-slate-300 dark:text-slate-600 mb-2" />
                                            <p className="font-semibold">No purchase orders found.</p>
                                            <p className="text-xs mt-0.5">Issue your first purchase order or convert an approved requisition.</p>
                                        </td>
                                    </tr>
                                ) : (
                                    orderList.map((po) => (
                                        <tr key={po.id} className="hover:bg-slate-50/50 dark:hover:bg-slate-800/30 transition-colors">
                                            <td className="py-3.5 px-4 font-bold text-indigo-600 dark:text-indigo-400">
                                                <Link href={`/inventory/purchase-orders/${po.id}`} className="hover:underline">
                                                    {po.po_no}
                                                </Link>
                                                {po.items && (
                                                    <div className="text-[10px] font-normal text-slate-400">
                                                        {po.items.length} line item(s)
                                                    </div>
                                                )}
                                            </td>
                                            <td className="py-3.5 px-4 text-slate-800 dark:text-slate-200">
                                                <div className="font-semibold">{po.vendor?.name}</div>
                                                <div className="text-[10px] text-slate-400">{po.vendor?.code}</div>
                                            </td>
                                            <td className="py-3.5 px-4 text-slate-800 dark:text-slate-200">
                                                <div className="font-semibold">{po.project ? po.project.name : 'Central Warehouse'}</div>
                                                <div className="text-[10px] text-slate-400">{po.delivery_site}</div>
                                            </td>
                                            <td className="py-3.5 px-4 text-slate-600 dark:text-slate-300 font-medium">
                                                {po.order_date ? new Date(po.order_date).toLocaleDateString() : 'N/A'}
                                            </td>
                                            <td className="py-3.5 px-4 text-right font-black text-slate-900 dark:text-white">
                                                {Number(po.total_amount).toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 })}
                                            </td>
                                            <td className="py-3.5 px-4">
                                                {getStatusBadge(po.status)}
                                            </td>
                                            <td className="py-3.5 px-4 text-right">
                                                <div className="inline-flex items-center gap-1.5">
                                                    <Link
                                                        href={`/inventory/purchase-orders/${po.id}`}
                                                        className="p-1.5 rounded-lg border border-slate-200 dark:border-slate-800 text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors"
                                                        title="View Details"
                                                    >
                                                        <Eye className="w-3.5 h-3.5" />
                                                    </Link>

                                                    <a
                                                        href={`/inventory/purchase-orders/${po.id}/print`}
                                                        target="_blank"
                                                        rel="noreferrer"
                                                        className="p-1.5 rounded-lg border border-slate-200 dark:border-slate-800 text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors"
                                                        title="Print Corporate PO"
                                                    >
                                                        <Printer className="w-3.5 h-3.5" />
                                                    </a>

                                                    {po.status !== 'received' && po.status !== 'cancelled' && (
                                                        <Link
                                                            href={`/inventory/goods-receiving/create?po_id=${po.id}`}
                                                            className="inline-flex items-center gap-1 px-2.5 py-1 bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg text-xs font-bold transition-colors cursor-pointer"
                                                            title="Receive Delivery (GRN)"
                                                        >
                                                            <FileInput className="w-3.5 h-3.5" />
                                                            <span>Receive</span>
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
                    {orders?.links && orders.links.length > 3 && (
                        <div className="p-4 border-t border-slate-100 dark:border-slate-800 flex items-center justify-between">
                            <div className="text-xs text-slate-500">
                                Showing {orders.from || 0} to {orders.to || 0} of {orders.total} orders
                            </div>
                            <div className="flex gap-1">
                                {orders.links.map((link, idx) => (
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
