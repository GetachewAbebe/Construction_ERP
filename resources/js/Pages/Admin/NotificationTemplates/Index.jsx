import { useState } from 'react';
import { Head, Link, router } from '@inertiajs/react';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import {
    Mail,
    Bell,
    MessageSquare,
    Plus,
    Edit3,
    Eye,
    Trash2,
    CheckCircle2,
    XCircle,
    ArrowLeft,
    Search,
} from 'lucide-react';

const TYPE_ICONS = {
    email: Mail,
    notification: Bell,
    sms: MessageSquare,
};

const TYPE_BADGES = {
    email: 'bg-blue-100 text-blue-800 dark:bg-blue-950/40 dark:text-blue-300',
    notification: 'bg-purple-100 text-purple-800 dark:bg-purple-950/40 dark:text-purple-300',
    sms: 'bg-emerald-100 text-emerald-800 dark:bg-emerald-950/40 dark:text-emerald-300',
};

export default function Index({ templates = [] }) {
    const [search, setSearch] = useState('');
    const [deleteModalTemplate, setDeleteModalTemplate] = useState(null);

    const handleDelete = (template) => {
        router.delete(`/admin/notification-templates/${template.id}`, {
            onSuccess: () => setDeleteModalTemplate(null),
        });
    };

    const filteredTemplates = templates.filter(t => {
        const q = search.toLowerCase();
        return (
            t.name?.toLowerCase().includes(q) ||
            t.key?.toLowerCase().includes(q) ||
            t.subject?.toLowerCase().includes(q) ||
            t.type?.toLowerCase().includes(q)
        );
    });

    return (
        <AuthenticatedLayout title="Notification Templates" header="System Administration">
            <Head title="Notification & Communication Templates" />

            {/* Delete Modal */}
            {deleteModalTemplate && (
                <div className="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-xs">
                    <div className="bg-white dark:bg-slate-900 rounded-2xl max-w-md w-full p-6 shadow-2xl border border-slate-200 dark:border-slate-800 animate-in fade-in zoom-in duration-150">
                        <div className="flex items-center gap-3 text-rose-600 mb-3">
                            <div className="p-2.5 rounded-xl bg-rose-100 dark:bg-rose-950/50">
                                <Trash2 className="w-5 h-5" />
                            </div>
                            <h3 className="font-bold text-base text-slate-900 dark:text-white">Delete Template</h3>
                        </div>
                        <p className="text-sm text-slate-600 dark:text-slate-300">
                            Are you sure you want to delete template <strong>{deleteModalTemplate.name}</strong> ({deleteModalTemplate.key})? Automated event triggers relying on this template may fail to deliver.
                        </p>
                        <div className="mt-6 flex items-center justify-end gap-3">
                            <button
                                type="button"
                                onClick={() => setDeleteModalTemplate(null)}
                                className="px-4 py-2 rounded-xl text-xs font-semibold text-slate-700 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800 transition"
                            >
                                Cancel
                            </button>
                            <button
                                type="button"
                                onClick={() => handleDelete(deleteModalTemplate)}
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
                            href="/admin"
                            className="p-1.5 rounded-lg text-slate-400 hover:text-slate-700 dark:hover:text-slate-200 hover:bg-slate-100 dark:hover:bg-slate-800 transition"
                        >
                            <ArrowLeft className="w-4 h-4" />
                        </Link>
                        <div>
                            <h1 className="text-xl sm:text-2xl font-extrabold tracking-tight text-slate-900 dark:text-white">
                                Communication & Notification Templates
                            </h1>
                            <p className="text-xs sm:text-sm text-slate-500 dark:text-slate-400">
                                Standardized copy for emails, in-app bell alerts, and SMS broadcasts.
                            </p>
                        </div>
                    </div>

                    <Link
                        href="/admin/notification-templates/create"
                        className="inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-xl bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold transition shadow-sm"
                    >
                        <Plus className="w-3.5 h-3.5" />
                        <span>New Template</span>
                    </Link>
                </div>

                {/* Filter and Search Bar */}
                <div className="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 p-4 shadow-sm flex items-center justify-between">
                    <div className="relative w-full sm:w-80">
                        <Search className="w-4 h-4 absolute left-3 top-1/2 -translate-y-1/2 text-slate-400" />
                        <input
                            type="text"
                            placeholder="Filter by title, key, or channel..."
                            value={search}
                            onChange={e => setSearch(e.target.value)}
                            className="w-full pl-9 pr-3 py-2 text-xs rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-slate-900 dark:text-white placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-blue-500"
                        />
                    </div>
                </div>

                {/* Templates Table */}
                <div className="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-sm overflow-hidden">
                    <div className="overflow-x-auto">
                        <table className="w-full text-left border-collapse text-sm">
                            <thead>
                                <tr className="border-b border-slate-200 dark:border-slate-800 bg-slate-50 dark:bg-slate-800/60 text-[11px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">
                                    <th className="py-3 px-4">Template Name</th>
                                    <th className="py-3 px-4">System Key</th>
                                    <th className="py-3 px-4">Channel Type</th>
                                    <th className="py-3 px-4 text-center">Status</th>
                                    <th className="py-3 px-4 text-right">Actions</th>
                                </tr>
                            </thead>
                            <tbody className="divide-y divide-slate-100 dark:divide-slate-800/60">
                                {filteredTemplates.length === 0 ? (
                                    <tr>
                                        <td colSpan={5} className="py-12 text-center text-slate-400 text-xs">
                                            No communication templates found.
                                        </td>
                                    </tr>
                                ) : (
                                    filteredTemplates.map(t => {
                                        const IconComponent = TYPE_ICONS[t.type] || Mail;
                                        const badgeClass = TYPE_BADGES[t.type] || 'bg-slate-100 text-slate-800';

                                        return (
                                            <tr key={t.id} className="hover:bg-slate-50/60 dark:hover:bg-slate-800/40 transition">
                                                <td className="py-3 px-4">
                                                    <div className="font-semibold text-slate-900 dark:text-white text-sm">
                                                        {t.name}
                                                    </div>
                                                    <div className="text-xs text-slate-400 truncate max-w-sm">
                                                        {t.subject || 'No subject header'}
                                                    </div>
                                                </td>

                                                <td className="py-3 px-4 font-mono text-xs text-slate-600 dark:text-slate-400">
                                                    {t.key}
                                                </td>

                                                <td className="py-3 px-4">
                                                    <span className={`inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[11px] font-bold uppercase ${badgeClass}`}>
                                                        <IconComponent className="w-3 h-3" />
                                                        <span>{t.type}</span>
                                                    </span>
                                                </td>

                                                <td className="py-3 px-4 text-center">
                                                    {t.is_active ? (
                                                        <span className="inline-flex items-center gap-1 text-xs font-semibold text-emerald-600 dark:text-emerald-400">
                                                            <CheckCircle2 className="w-3.5 h-3.5" /> Active
                                                        </span>
                                                    ) : (
                                                        <span className="inline-flex items-center gap-1 text-xs text-slate-400">
                                                            <XCircle className="w-3.5 h-3.5" /> Inactive
                                                        </span>
                                                    )}
                                                </td>

                                                <td className="py-3 px-4 text-right">
                                                    <div className="flex items-center justify-end gap-1.5">
                                                        <Link
                                                            href={`/admin/notification-templates/${t.id}/preview`}
                                                            className="p-1.5 rounded-lg text-slate-400 hover:text-blue-600 hover:bg-slate-100 dark:hover:bg-slate-800 transition"
                                                            title="Live Preview"
                                                        >
                                                            <Eye className="w-3.5 h-3.5" />
                                                        </Link>
                                                        <Link
                                                            href={`/admin/notification-templates/${t.id}/edit`}
                                                            className="p-1.5 rounded-lg text-slate-400 hover:text-slate-800 dark:hover:text-slate-200 hover:bg-slate-100 dark:hover:bg-slate-800 transition"
                                                            title="Edit Template"
                                                        >
                                                            <Edit3 className="w-3.5 h-3.5" />
                                                        </Link>
                                                        <button
                                                            type="button"
                                                            onClick={() => setDeleteModalTemplate(t)}
                                                            className="p-1.5 rounded-lg text-slate-400 hover:text-rose-600 hover:bg-rose-50 dark:hover:bg-rose-950/30 transition"
                                                            title="Delete Template"
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
                </div>
            </div>
        </AuthenticatedLayout>
    );
}
