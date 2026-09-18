import { useState } from 'react';
import { Head, Link, useForm, router } from '@inertiajs/react';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import {
    Key,
    Plus,
    Trash2,
    ArrowLeft,
    Shield,
    CheckCircle2,
    AlertCircle,
    Search,
} from 'lucide-react';

export default function Permissions({ permissions = {} }) {
    const [search, setSearch] = useState('');
    const [deletePermModal, setDeletePermModal] = useState(null);

    const { data, setData, post, processing, reset, errors } = useForm({
        name: '',
    });

    const handleCreate = (e) => {
        e.preventDefault();
        post('/admin/roles/permissions', {
            onSuccess: () => reset('name'),
        });
    };

    const handleDelete = (perm) => {
        router.delete(`/admin/roles/permissions/${perm.id}`, {
            onSuccess: () => setDeletePermModal(null),
        });
    };

    const modules = Object.keys(permissions);

    return (
        <AuthenticatedLayout title="Permission Registry" header="System Administration">
            <Head title="Security Permission Registry" />

            {/* Delete Modal */}
            {deletePermModal && (
                <div className="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-xs">
                    <div className="bg-white dark:bg-slate-900 rounded-2xl max-w-md w-full p-6 shadow-2xl border border-slate-200 dark:border-slate-800 animate-in fade-in zoom-in duration-150">
                        <div className="flex items-center gap-3 text-rose-600 mb-3">
                            <div className="p-2.5 rounded-xl bg-rose-100 dark:bg-rose-950/50">
                                <Trash2 className="w-5 h-5" />
                            </div>
                            <h3 className="font-bold text-base text-slate-900 dark:text-white">Expunge Permission</h3>
                        </div>
                        <p className="text-sm text-slate-600 dark:text-slate-300">
                            Are you sure you want to delete permission <strong>{deletePermModal.name}</strong>? Roles referencing this privilege will forfeit access to its associated features.
                        </p>
                        <div className="mt-6 flex items-center justify-end gap-3">
                            <button
                                type="button"
                                onClick={() => setDeletePermModal(null)}
                                className="px-4 py-2 rounded-xl text-xs font-semibold text-slate-700 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800 transition"
                            >
                                Cancel
                            </button>
                            <button
                                type="button"
                                onClick={() => handleDelete(deletePermModal)}
                                className="px-4 py-2 rounded-xl text-xs font-bold bg-rose-600 hover:bg-rose-700 text-white transition shadow-sm"
                            >
                                Expunge Privilege
                            </button>
                        </div>
                    </div>
                </div>
            )}

            <div className="max-w-4xl mx-auto space-y-6">
                {/* Header */}
                <div className="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-2 border-b border-slate-200 dark:border-slate-800">
                    <div className="flex items-center gap-2">
                        <Link
                            href="/admin/roles"
                            className="p-1.5 rounded-lg text-slate-400 hover:text-slate-700 dark:hover:text-slate-200 hover:bg-slate-100 dark:hover:bg-slate-800 transition"
                        >
                            <ArrowLeft className="w-4 h-4" />
                        </Link>
                        <div>
                            <h1 className="text-xl sm:text-2xl font-extrabold tracking-tight text-slate-900 dark:text-white">
                                Permission Registry
                            </h1>
                            <p className="text-xs sm:text-sm text-slate-500 dark:text-slate-400">
                                Granular RBAC capabilities and route gate privileges across system modules.
                            </p>
                        </div>
                    </div>
                </div>

                {/* Create Permission Form Card */}
                <div className="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 p-5 shadow-sm">
                    <div className="flex items-center gap-2 mb-3 text-sm font-bold text-slate-900 dark:text-white">
                        <Plus className="w-4 h-4 text-blue-600" />
                        <span>Register New Permission</span>
                    </div>
                    <form onSubmit={handleCreate} className="flex flex-col sm:flex-row items-stretch sm:items-center gap-3">
                        <div className="flex-1">
                            <input
                                type="text"
                                required
                                placeholder="e.g. project.audit or employee.terminate"
                                value={data.name}
                                onChange={e => setData('name', e.target.value)}
                                className={`w-full text-xs font-mono rounded-xl border ${
                                    errors.name ? 'border-rose-500' : 'border-slate-200 dark:border-slate-700'
                                } bg-slate-50 dark:bg-slate-800 text-slate-900 dark:text-white py-2 px-3 focus:outline-none focus:ring-2 focus:ring-blue-500`}
                            />
                            {errors.name && <p className="text-rose-500 text-[11px] mt-1">{errors.name}</p>}
                        </div>
                        <button
                            type="submit"
                            disabled={processing}
                            className="inline-flex items-center justify-center gap-1.5 px-4 py-2 rounded-xl bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold transition shadow-sm disabled:opacity-50 shrink-0"
                        >
                            <Key className="w-3.5 h-3.5" />
                            <span>{processing ? 'Registering...' : 'Register Privilege'}</span>
                        </button>
                    </form>
                </div>

                {/* Filter / Search */}
                <div className="relative">
                    <Search className="w-4 h-4 absolute left-3 top-1/2 -translate-y-1/2 text-slate-400" />
                    <input
                        type="text"
                        placeholder="Search permissions by keyword..."
                        value={search}
                        onChange={e => setSearch(e.target.value)}
                        className="w-full pl-9 pr-3 py-2 text-xs rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-900 text-slate-900 dark:text-white placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-blue-500"
                    />
                </div>

                {/* Grouped Permissions Cards */}
                <div className="space-y-4">
                    {modules.map(modName => {
                        const allPerms = permissions[modName] || [];
                        const matchedPerms = allPerms.filter(p =>
                            p.name.toLowerCase().includes(search.toLowerCase())
                        );

                        if (matchedPerms.length === 0) return null;

                        return (
                            <div
                                key={modName}
                                className="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-sm overflow-hidden"
                            >
                                <div className="px-5 py-3 border-b border-slate-100 dark:border-slate-800 bg-slate-50/70 dark:bg-slate-800/40 flex items-center justify-between">
                                    <span className="text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300">
                                        Module: {modName}
                                    </span>
                                    <span className="text-[11px] text-slate-400">
                                        {matchedPerms.length} privileges
                                    </span>
                                </div>

                                <div className="divide-y divide-slate-100 dark:divide-slate-800/60">
                                    {matchedPerms.map(perm => (
                                        <div
                                            key={perm.id}
                                            className="px-5 py-2.5 flex items-center justify-between hover:bg-slate-50/50 dark:hover:bg-slate-800/30 transition"
                                        >
                                            <div className="flex items-center gap-2">
                                                <Key className="w-3.5 h-3.5 text-slate-400" />
                                                <span className="font-mono text-xs font-medium text-slate-900 dark:text-white">
                                                    {perm.name}
                                                </span>
                                            </div>
                                            <button
                                                type="button"
                                                onClick={() => setDeletePermModal(perm)}
                                                className="p-1 rounded-lg text-slate-400 hover:text-rose-600 hover:bg-rose-50 dark:hover:bg-rose-950/30 transition"
                                                title="Delete permission"
                                            >
                                                <Trash2 className="w-3.5 h-3.5" />
                                            </button>
                                        </div>
                                    ))}
                                </div>
                            </div>
                        );
                    })}
                </div>
            </div>
        </AuthenticatedLayout>
    );
}
