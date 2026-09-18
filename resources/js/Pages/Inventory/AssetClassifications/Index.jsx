import { useState } from 'react';
import { Head, Link, router } from '@inertiajs/react';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import {
    Boxes,
    Layers,
    Plus,
    Edit3,
    Trash2,
    ArrowLeft,
    Search,
    ChevronRight,
} from 'lucide-react';

export default function Index({ classifications }) {
    const [search, setSearch] = useState('');
    const [deleteModalItem, setDeleteModalItem] = useState(null);

    const items = classifications?.data || [];

    const handleDelete = (item) => {
        router.delete(`/inventory/asset-classifications/${item.id}`, {
            onSuccess: () => setDeleteModalItem(null),
        });
    };

    const filteredItems = items.filter(item => {
        const q = search.toLowerCase();
        return (
            item.name?.toLowerCase().includes(q) ||
            item.code?.toLowerCase().includes(q) ||
            item.full_nomenclature?.toLowerCase().includes(q)
        );
    });

    return (
        <AuthenticatedLayout title="Asset Classifications" header="Inventory & Store">
            <Head title="Asset Classifications & Taxonomy" />

            {/* Delete Confirmation Modal */}
            {deleteModalItem && (
                <div className="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-xs">
                    <div className="bg-white dark:bg-slate-900 rounded-2xl max-w-md w-full p-6 shadow-2xl border border-slate-200 dark:border-slate-800 animate-in fade-in zoom-in duration-150">
                        <div className="flex items-center gap-3 text-rose-600 mb-3">
                            <div className="p-2.5 rounded-xl bg-rose-100 dark:bg-rose-950/50">
                                <Trash2 className="w-5 h-5" />
                            </div>
                            <h3 className="font-bold text-base text-slate-900 dark:text-white">Delete Asset Category</h3>
                        </div>
                        <p className="text-sm text-slate-600 dark:text-slate-300">
                            Are you sure you want to decommission category <strong>{deleteModalItem.name}</strong> ({deleteModalItem.code})? Categories with linked active items or subcategories cannot be deleted.
                        </p>
                        <div className="mt-6 flex items-center justify-end gap-3">
                            <button
                                type="button"
                                onClick={() => setDeleteModalItem(null)}
                                className="px-4 py-2 rounded-xl text-xs font-semibold text-slate-700 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800 transition"
                            >
                                Cancel
                            </button>
                            <button
                                type="button"
                                onClick={() => handleDelete(deleteModalItem)}
                                className="px-4 py-2 rounded-xl text-xs font-bold bg-rose-600 hover:bg-rose-700 text-white transition shadow-sm"
                            >
                                Confirm Removal
                            </button>
                        </div>
                    </div>
                </div>
            )}

            <div className="space-y-6">
                {/* Header */}
                <div className="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-2 border-b border-slate-200 dark:border-slate-800">
                    <div className="flex items-center gap-2">
                        <Link
                            href="/inventory"
                            className="p-1.5 rounded-lg text-slate-400 hover:text-slate-700 dark:hover:text-slate-200 hover:bg-slate-100 dark:hover:bg-slate-800 transition"
                        >
                            <ArrowLeft className="w-4 h-4" />
                        </Link>
                        <div>
                            <h1 className="text-xl sm:text-2xl font-extrabold tracking-tight text-slate-900 dark:text-white">
                                Asset Taxonomy & Classifications
                            </h1>
                            <p className="text-xs sm:text-sm text-slate-500 dark:text-slate-400">
                                Hierarchical categorization structure for materials, machinery, and equipment.
                            </p>
                        </div>
                    </div>

                    <Link
                        href="/inventory/asset-classifications/create"
                        className="inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-xl bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold transition shadow-sm"
                    >
                        <Plus className="w-3.5 h-3.5" />
                        <span>New Category</span>
                    </Link>
                </div>

                {/* Search Bar */}
                <div className="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 p-4 shadow-sm flex items-center justify-between">
                    <div className="relative w-full sm:w-80">
                        <Search className="w-4 h-4 absolute left-3 top-1/2 -translate-y-1/2 text-slate-400" />
                        <input
                            type="text"
                            placeholder="Filter by title, code, or hierarchy..."
                            value={search}
                            onChange={e => setSearch(e.target.value)}
                            className="w-full pl-9 pr-3 py-2 text-xs rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-slate-900 dark:text-white placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-blue-500"
                        />
                    </div>
                </div>

                {/* Taxonomy Table */}
                <div className="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-sm overflow-hidden">
                    <div className="overflow-x-auto">
                        <table className="w-full text-left border-collapse text-sm">
                            <thead>
                                <tr className="border-b border-slate-200 dark:border-slate-800 bg-slate-50 dark:bg-slate-800/60 text-[11px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">
                                    <th className="py-3 px-4">Classification</th>
                                    <th className="py-3 px-4">Hierarchy Tier</th>
                                    <th className="py-3 px-4">Full Nomenclature Path</th>
                                    <th className="py-3 px-4 text-center">Active Assets</th>
                                    <th className="py-3 px-4 text-right">Actions</th>
                                </tr>
                            </thead>
                            <tbody className="divide-y divide-slate-100 dark:divide-slate-800/60">
                                {filteredItems.length === 0 ? (
                                    <tr>
                                        <td colSpan={5} className="py-12 text-center text-slate-400 text-xs">
                                            No classification nodes found.
                                        </td>
                                    </tr>
                                ) : (
                                    filteredItems.map(item => (
                                        <tr key={item.id} className="hover:bg-slate-50/60 dark:hover:bg-slate-800/40 transition">
                                            <td className="py-3 px-4">
                                                <div className="flex items-center gap-3">
                                                    <div className="p-2 rounded-xl bg-blue-50 dark:bg-blue-950/50 text-blue-600 dark:text-blue-400">
                                                        <Boxes className="w-4 h-4" />
                                                    </div>
                                                    <div>
                                                        <div className="font-semibold text-slate-900 dark:text-white text-sm">
                                                            {item.name}
                                                        </div>
                                                        <div className="font-mono text-xs text-slate-400">
                                                            {item.code || `ID #${item.id}`}
                                                        </div>
                                                    </div>
                                                </div>
                                            </td>

                                            <td className="py-3 px-4">
                                                <span className={`inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-bold ${
                                                    item.depth === 0
                                                        ? 'bg-blue-100 text-blue-800 dark:bg-blue-950/40 dark:text-blue-300'
                                                        : 'bg-slate-100 text-slate-600 dark:bg-slate-800 dark:text-slate-400'
                                                }`}>
                                                    Level {Number(item.depth || 0) + 1}
                                                </span>
                                            </td>

                                            <td className="py-3 px-4 font-mono text-xs text-slate-500 max-w-xs truncate">
                                                {item.full_nomenclature || item.name}
                                            </td>

                                            <td className="py-3 px-4 text-center font-bold text-xs text-slate-700 dark:text-slate-300">
                                                {Number(item.recursive_asset_count || 0).toLocaleString()}
                                            </td>

                                            <td className="py-3 px-4 text-right">
                                                <div className="flex items-center justify-end gap-1.5">
                                                    <Link
                                                        href={`/inventory/asset-classifications/${item.id}/edit`}
                                                        className="p-1.5 rounded-lg text-slate-400 hover:text-slate-800 dark:hover:text-slate-200 hover:bg-slate-100 dark:hover:bg-slate-800 transition"
                                                        title="Edit Category"
                                                    >
                                                        <Edit3 className="w-3.5 h-3.5" />
                                                    </Link>
                                                    <button
                                                        type="button"
                                                        onClick={() => setDeleteModalItem(item)}
                                                        className="p-1.5 rounded-lg text-slate-400 hover:text-rose-600 hover:bg-rose-50 dark:hover:bg-rose-950/30 transition"
                                                        title="Decommission Category"
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

                    {classifications?.links && classifications.links.length > 3 && (
                        <div className="px-4 py-3 border-t border-slate-200 dark:border-slate-800 flex items-center justify-between text-xs text-slate-500">
                            <div>Showing {classifications.from || 0} to {classifications.to || 0} of {classifications.total || 0} entries</div>
                            <div className="flex items-center gap-1">
                                {classifications.links.map((link, idx) => (
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
