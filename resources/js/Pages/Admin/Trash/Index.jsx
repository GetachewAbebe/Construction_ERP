import { useState } from 'react';
import { Head, Link, router } from '@inertiajs/react';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import {
    Trash2,
    RotateCcw,
    Search,
    Filter,
    Clock,
    AlertCircle,
    CheckCircle2,
    ArrowLeft,
    ShieldAlert,
} from 'lucide-react';

const TYPE_COLORS = {
    User: 'bg-indigo-100 text-indigo-800 dark:bg-indigo-950/40 dark:text-indigo-300',
    Employee: 'bg-blue-100 text-blue-800 dark:bg-blue-950/40 dark:text-blue-300',
    Expense: 'bg-emerald-100 text-emerald-800 dark:bg-emerald-950/40 dark:text-emerald-300',
    InventoryItem: 'bg-amber-100 text-amber-800 dark:bg-amber-950/40 dark:text-amber-300',
    Project: 'bg-purple-100 text-purple-800 dark:bg-purple-950/40 dark:text-purple-300',
    LeaveRequest: 'bg-rose-100 text-rose-800 dark:bg-rose-950/40 dark:text-rose-300',
};

export default function Index({ trashedItems = [] }) {
    const [search, setSearch] = useState('');
    const [typeFilter, setTypeFilter] = useState('');
    const [restoringId, setRestoringId] = useState(null);

    const handleRestore = (item) => {
        setRestoringId(`${item.model}-${item.id}`);
        router.post('/admin/trash/restore', {
            model: item.model,
            id: item.id,
        }, {
            onFinish: () => setRestoringId(null),
        });
    };

    const filteredItems = trashedItems.filter(item => {
        const matchesType = !typeFilter || item.type === typeFilter;
        const q = search.toLowerCase();
        const matchesSearch = !search ||
            (item.name && String(item.name).toLowerCase().includes(q)) ||
            (item.type && item.type.toLowerCase().includes(q));
        return matchesType && matchesSearch;
    });

    const uniqueTypes = Array.from(new Set(trashedItems.map(i => i.type)));

    const formatDate = (dateStr) => {
        if (!dateStr) return '—';
        try {
            const d = new Date(dateStr);
            return d.toLocaleDateString('en-US', {
                month: 'short',
                day: 'numeric',
                year: 'numeric',
                hour: '2-digit',
                minute: '2-digit',
            });
        } catch (e) {
            return String(dateStr);
        }
    };

    return (
        <AuthenticatedLayout title="Recycle Bin" header="System Administration">
            <Head title="Recycle Bin & Data Vault" />

            <div className="space-y-6">
                {/* Header */}
                <div className="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-2 border-b border-slate-200 dark:border-slate-800">
                    <div className="flex items-center gap-2">
                        <Link
                            href="/admin"
                            className="p-1.5 rounded-lg text-slate-400 hover:text-slate-700 dark:hover:text-slate-200 hover:bg-slate-100 dark:hover:bg-slate-800 transition"
                        >
                            <ArrowLeft className="w-4 h-4" />
                        </Link>
                        <div>
                            <h1 className="text-xl sm:text-2xl font-extrabold tracking-tight text-slate-900 dark:text-white">
                                Recycle Bin & Archive Vault
                            </h1>
                            <p className="text-xs sm:text-sm text-slate-500 dark:text-slate-400">
                                Recover soft-deleted business objects, users, inventory items, and financial records.
                            </p>
                        </div>
                    </div>
                </div>

                {/* Filter and Search Bar */}
                <div className="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 p-4 shadow-sm flex flex-col md:flex-row items-center justify-between gap-4">
                    <div className="flex items-center gap-2 w-full md:w-auto">
                        <Filter className="w-4 h-4 text-slate-400 shrink-0" />
                        <select
                            value={typeFilter}
                            onChange={e => setTypeFilter(e.target.value)}
                            className="text-xs font-semibold rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-slate-900 dark:text-white py-2 px-3 focus:outline-none focus:ring-2 focus:ring-blue-500"
                        >
                            <option value="">All Entity Types ({trashedItems.length})</option>
                            {uniqueTypes.map(t => (
                                <option key={t} value={t}>{t}</option>
                            ))}
                        </select>
                    </div>

                    <div className="relative w-full md:w-72">
                        <Search className="w-4 h-4 absolute left-3 top-1/2 -translate-y-1/2 text-slate-400" />
                        <input
                            type="text"
                            placeholder="Filter by name or identifier..."
                            value={search}
                            onChange={e => setSearch(e.target.value)}
                            className="w-full pl-9 pr-3 py-2 text-xs rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-slate-900 dark:text-white placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-blue-500"
                        />
                    </div>
                </div>

                {/* Table */}
                <div className="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-sm overflow-hidden">
                    <div className="overflow-x-auto">
                        <table className="w-full text-left border-collapse text-sm">
                            <thead>
                                <tr className="border-b border-slate-200 dark:border-slate-800 bg-slate-50 dark:bg-slate-800/60 text-[11px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">
                                    <th className="py-3 px-4">Entity Type</th>
                                    <th className="py-3 px-4">Object / Record Identifier</th>
                                    <th className="py-3 px-4">Deleted Timestamp</th>
                                    <th className="py-3 px-4 text-right">Recovery Protocol</th>
                                </tr>
                            </thead>
                            <tbody className="divide-y divide-slate-100 dark:divide-slate-800/60">
                                {filteredItems.length === 0 ? (
                                    <tr>
                                        <td colSpan={4} className="py-16 text-center">
                                            <div className="flex flex-col items-center justify-center gap-2 text-slate-400">
                                                <div className="p-3 rounded-full bg-slate-100 dark:bg-slate-800">
                                                    <Trash2 className="w-8 h-8 text-slate-300 dark:text-slate-600" />
                                                </div>
                                                <p className="font-semibold text-sm text-slate-600 dark:text-slate-300">
                                                    Archive Vault is Empty
                                                </p>
                                                <p className="text-xs text-slate-400 max-w-xs">
                                                    No soft-deleted records currently exist in the database vault matching your criteria.
                                                </p>
                                            </div>
                                        </td>
                                    </tr>
                                ) : (
                                    filteredItems.map(item => {
                                        const colorClass = TYPE_COLORS[item.type] || 'bg-slate-100 text-slate-800';
                                        const isRestoring = restoringId === `${item.model}-${item.id}`;

                                        return (
                                            <tr key={`${item.type}-${item.id}`} className="hover:bg-slate-50/60 dark:hover:bg-slate-800/40 transition">
                                                <td className="py-3 px-4">
                                                    <span className={`inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-bold ${colorClass}`}>
                                                        {item.type}
                                                    </span>
                                                </td>
                                                <td className="py-3 px-4">
                                                    <div className="font-semibold text-slate-900 dark:text-white text-sm">
                                                        {item.name}
                                                    </div>
                                                    <div className="text-xs font-mono text-slate-400">
                                                        ID #{item.id} • {item.model?.split('\\').pop()}
                                                    </div>
                                                </td>
                                                <td className="py-3 px-4 text-xs text-slate-500 font-mono">
                                                    {formatDate(item.deleted_at)}
                                                </td>
                                                <td className="py-3 px-4 text-right">
                                                    <button
                                                        type="button"
                                                        disabled={isRestoring}
                                                        onClick={() => handleRestore(item)}
                                                        className="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl border border-blue-200 dark:border-blue-900/60 text-xs font-bold text-blue-600 dark:text-blue-400 hover:bg-blue-50 dark:hover:bg-blue-950/40 transition shadow-2xs disabled:opacity-50"
                                                    >
                                                        <RotateCcw className={`w-3.5 h-3.5 ${isRestoring ? 'animate-spin' : ''}`} />
                                                        <span>{isRestoring ? 'Restoring...' : 'Restore Record'}</span>
                                                    </button>
                                                </td>
                                            </tr>
                                        );
                                    })
                                )}
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </AuthenticatedLayout>
    );
}
