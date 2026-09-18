import { useState } from 'react';
import { Head, Link, router } from '@inertiajs/react';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import {
    Package,
    Plus,
    Search,
    Filter,
    Edit,
    Trash2,
    CheckCircle2,
    AlertTriangle,
    XCircle,
} from 'lucide-react';

export default function Index({
    items,
    totals = { total_items: 0, low_stock_count: 0 },
    q = '',
    loc = '',
    status = '',
    classification_id = '',
    storeLocations = [],
    classifications = [],
}) {
    const [search, setSearch] = useState(q);
    const [location, setLocation] = useState(loc);
    const [itemStatus, setItemStatus] = useState(status);
    const [classification, setClassification] = useState(classification_id);

    const handleFilter = (e) => {
        if (e) e.preventDefault();
        router.get('/inventory/items', {
            q: search,
            store_location: location,
            status: itemStatus,
            classification_id: classification,
        }, {
            preserveState: true,
            preserveScroll: true,
        });
    };

    const handleDelete = (id, name) => {
        if (confirm(`Are you sure you want to delete "${name}"?`)) {
            router.delete(`/inventory/items/${id}`);
        }
    };

    const itemList = items?.data || [];

    return (
        <AuthenticatedLayout title="Stock Inventory" header="Inventory & Store">
            <div className="space-y-6">
                {/* Header */}
                <div className="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-2 border-b border-slate-200 dark:border-slate-800">
                    <div>
                        <h1 className="text-xl sm:text-2xl font-extrabold tracking-tight text-slate-900 dark:text-white">
                            Stock Item Catalog
                        </h1>
                        <p className="text-xs sm:text-sm text-slate-500 dark:text-slate-400 mt-0.5">
                            Materials, site tools, safety gear, and spare machinery components.
                        </p>
                    </div>

                    <Link
                        href="/inventory/items/create"
                        className="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl bg-blue-900 hover:bg-blue-950 text-white font-bold text-xs shadow-sm transition-colors self-start sm:self-auto cursor-pointer"
                    >
                        <Plus className="w-4 h-4" />
                        <span>Add Stock Item</span>
                    </Link>
                </div>

                {/* KPIs */}
                <div className="grid grid-cols-2 gap-4 sm:max-w-md">
                    <div className="rounded-2xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 p-4 shadow-xs flex items-center justify-between">
                        <div>
                            <div className="text-[11px] font-bold uppercase tracking-wider text-slate-400">Total Items</div>
                            <div className="text-2xl font-black text-slate-900 dark:text-white font-mono mt-0.5">
                                {totals.total_items}
                            </div>
                        </div>
                        <div className="p-2.5 rounded-xl bg-blue-50 dark:bg-blue-950/60 text-blue-900 dark:text-blue-400">
                            <Package className="w-5 h-5" />
                        </div>
                    </div>

                    <div className="rounded-2xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 p-4 shadow-xs flex items-center justify-between">
                        <div>
                            <div className="text-[11px] font-bold uppercase tracking-wider text-slate-400">Low Stock</div>
                            <div className="text-2xl font-black text-amber-600 dark:text-amber-400 font-mono mt-0.5">
                                {totals.low_stock_count}
                            </div>
                        </div>
                        <div className="p-2.5 rounded-xl bg-amber-50 dark:bg-amber-950/60 text-amber-600 dark:text-amber-400">
                            <AlertTriangle className="w-5 h-5" />
                        </div>
                    </div>
                </div>

                {/* Filters */}
                <div className="rounded-2xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 shadow-xs p-4">
                    <form onSubmit={handleFilter} className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-3">
                        <div className="relative">
                            <Search className="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-400 pointer-events-none" />
                            <input
                                type="text"
                                placeholder="Search by name, ID..."
                                value={search}
                                onChange={(e) => setSearch(e.target.value)}
                                className="w-full pl-9 pr-3 py-2 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-xs text-slate-800 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-900/20"
                            />
                        </div>

                        <select
                            value={classification}
                            onChange={(e) => setClassification(e.target.value)}
                            className="w-full px-3 py-2 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-xs text-slate-800 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-900/20"
                        >
                            <option value="">All Categories</option>
                            {classifications.map((cl) => (
                                <option key={cl.id} value={cl.id}>{cl.name}</option>
                            ))}
                        </select>

                        <select
                            value={location}
                            onChange={(e) => setLocation(e.target.value)}
                            className="w-full px-3 py-2 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-xs text-slate-800 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-900/20"
                        >
                            <option value="">All Warehouses / Sites</option>
                            {storeLocations.map((l, i) => (
                                <option key={i} value={l}>{l}</option>
                            ))}
                        </select>

                        <select
                            value={itemStatus}
                            onChange={(e) => setItemStatus(e.target.value)}
                            className="w-full px-3 py-2 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-xs text-slate-800 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-900/20"
                        >
                            <option value="">All Statuses</option>
                            <option value="in_stock">Stable Stock (&gt; 5)</option>
                            <option value="low_stock">Low Stock (1-5)</option>
                            <option value="out_of_stock">Out of Stock (0)</option>
                        </select>

                        <button
                            type="submit"
                            className="w-full py-2 px-4 rounded-xl bg-blue-900 hover:bg-blue-950 text-white font-bold text-xs transition-colors cursor-pointer flex items-center justify-center gap-1.5"
                        >
                            <Filter className="w-3.5 h-3.5" />
                            <span>Filter Items</span>
                        </button>
                    </form>
                </div>

                {/* Table */}
                <div className="rounded-2xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 shadow-xs overflow-hidden">
                    <div className="overflow-x-auto">
                        <table className="w-full text-left text-xs">
                            <thead className="bg-slate-50 dark:bg-slate-800/60 text-slate-500 uppercase font-semibold text-[10px] tracking-wider border-b border-slate-200 dark:border-slate-800">
                                <tr>
                                    <th className="py-3.5 px-4">Item Code</th>
                                    <th className="py-3.5 px-4">Item Name</th>
                                    <th className="py-3.5 px-4">Category</th>
                                    <th className="py-3.5 px-4">Warehouse Location</th>
                                    <th className="py-3.5 px-4">Quantity</th>
                                    <th className="py-3.5 px-4">In-Date</th>
                                    <th className="py-3.5 px-4 text-right">Actions</th>
                                </tr>
                            </thead>
                            <tbody className="divide-y divide-slate-100 dark:divide-slate-800/80">
                                {itemList.length === 0 ? (
                                    <tr>
                                        <td colSpan={7} className="py-8 text-center text-slate-400 text-xs">
                                            No inventory items match the current filters.
                                        </td>
                                    </tr>
                                ) : (
                                    itemList.map((item) => {
                                        const qty = Number(item.quantity || 0);
                                        const isLow = qty > 0 && qty <= 5;
                                        const isZero = qty <= 0;

                                        return (
                                            <tr key={item.id} className="hover:bg-slate-50/70 dark:hover:bg-slate-800/40 transition-colors">
                                                <td className="py-3 px-4 font-mono font-bold text-blue-900 dark:text-blue-400">
                                                    #{item.item_no || item.id}
                                                </td>
                                                <td className="py-3 px-4">
                                                    <div className="font-bold text-slate-900 dark:text-white">{item.name}</div>
                                                    {item.description && (
                                                        <div className="text-[11px] text-slate-400 line-clamp-1">{item.description}</div>
                                                    )}
                                                </td>
                                                <td className="py-3 px-4 text-slate-600 dark:text-slate-300">
                                                    {item.classification?.name || 'General Inventory'}
                                                </td>
                                                <td className="py-3 px-4 text-slate-600 dark:text-slate-300">
                                                    {item.store_location || 'Central Depot'}
                                                </td>
                                                <td className="py-3 px-4">
                                                    <div className="flex items-center gap-1.5">
                                                        <span className="font-mono font-bold text-slate-900 dark:text-white">
                                                            {qty} {item.unit || 'pcs'}
                                                        </span>
                                                        {isZero ? (
                                                            <span className="px-1.5 py-0.5 rounded text-[9px] font-bold bg-rose-50 dark:bg-rose-950/50 text-rose-600 dark:text-rose-400 border border-rose-200 dark:border-rose-900">
                                                                Out of Stock
                                                            </span>
                                                        ) : isLow ? (
                                                            <span className="px-1.5 py-0.5 rounded text-[9px] font-bold bg-amber-50 dark:bg-amber-950/50 text-amber-600 dark:text-amber-400 border border-amber-200 dark:border-amber-900">
                                                                Low Stock
                                                            </span>
                                                        ) : (
                                                            <span className="px-1.5 py-0.5 rounded text-[9px] font-bold bg-emerald-50 dark:bg-emerald-950/50 text-emerald-600 dark:text-emerald-400 border border-emerald-200 dark:border-emerald-900">
                                                                Stable
                                                            </span>
                                                        )}
                                                    </div>
                                                </td>
                                                <td className="py-3 px-4 text-slate-500 font-mono text-[11px]">
                                                    {item.in_date ? new Date(item.in_date).toLocaleDateString() : '—'}
                                                </td>
                                                <td className="py-3 px-4 text-right">
                                                    <div className="flex items-center justify-end gap-1.5">
                                                        <Link
                                                            href={`/inventory/items/${item.id}/edit`}
                                                            className="p-1.5 rounded-lg text-slate-500 hover:text-blue-900 dark:hover:text-white hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors"
                                                            title="Edit item"
                                                        >
                                                            <Edit className="w-3.5 h-3.5" />
                                                        </Link>
                                                        <button
                                                            onClick={() => handleDelete(item.id, item.name)}
                                                            className="p-1.5 rounded-lg text-slate-500 hover:text-rose-600 dark:hover:text-rose-400 hover:bg-rose-50 dark:hover:bg-rose-950/40 transition-colors cursor-pointer"
                                                            title="Delete item"
                                                        >
                                                            <Trash2 className="w-3.5 h-3.5" />
                                                        </button>
                                                    </div>
                                                </td>
                                            </tr>
                                        );
                                    })
                                )}
                            </tbody>
                        </table>
                    </div>

                    {/* Pagination Links */}
                    {items?.links && items.links.length > 3 && (
                        <div className="px-4 py-3 border-t border-slate-200 dark:border-slate-800 flex items-center justify-between text-xs text-slate-500">
                            <div>Showing {items.from || 0} to {items.to || 0} of {items.total || 0} items</div>
                            <div className="flex items-center gap-1">
                                {items.links.map((link, idx) => (
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
            </div>
        </AuthenticatedLayout>
    );
}
