import { useState } from 'react';
import { Head, Link, router } from '@inertiajs/react';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import {
    ShieldCheck,
    Key,
    Plus,
    Edit3,
    Trash2,
    Shield,
    AlertCircle,
    CheckCircle2,
    Lock,
} from 'lucide-react';

const PROTECTED_ROLES = ['Administrator', 'HumanResourceManager', 'InventoryManager', 'FinancialManager'];

export default function Index({ roles = [], permissions = {} }) {
    const [deleteModalRole, setDeleteModalRole] = useState(null);

    const handleDelete = (role) => {
        router.delete(`/admin/roles/${role.id}`, {
            onSuccess: () => setDeleteModalRole(null),
        });
    };

    return (
        <AuthenticatedLayout title="Roles & Permissions" header="System Administration">
            <Head title="Authorization Tiers & Roles" />

            {/* Delete Modal */}
            {deleteModalRole && (
                <div className="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-xs">
                    <div className="bg-white dark:bg-slate-900 rounded-2xl max-w-md w-full p-6 shadow-2xl border border-slate-200 dark:border-slate-800 animate-in fade-in zoom-in duration-150">
                        <div className="flex items-center gap-3 text-rose-600 mb-3">
                            <div className="p-2.5 rounded-xl bg-rose-100 dark:bg-rose-950/50">
                                <Trash2 className="w-5 h-5" />
                            </div>
                            <h3 className="font-bold text-base text-slate-900 dark:text-white">Delete Authorization Tier</h3>
                        </div>
                        <p className="text-sm text-slate-600 dark:text-slate-300">
                            Are you sure you want to expunge the role <strong>{deleteModalRole.name}</strong>? Any users currently mapped to this tier will lose associated permission privileges.
                        </p>
                        <div className="mt-6 flex items-center justify-end gap-3">
                            <button
                                type="button"
                                onClick={() => setDeleteModalRole(null)}
                                className="px-4 py-2 rounded-xl text-xs font-semibold text-slate-700 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800 transition"
                            >
                                Cancel
                            </button>
                            <button
                                type="button"
                                onClick={() => handleDelete(deleteModalRole)}
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
                    <div>
                        <h1 className="text-xl sm:text-2xl font-extrabold tracking-tight text-slate-900 dark:text-white">
                            Roles & Access Control
                        </h1>
                        <p className="text-xs sm:text-sm text-slate-500 dark:text-slate-400 mt-0.5">
                            Enterprise authorization tiers, RBAC privilege policies, and role assignment matrices.
                        </p>
                    </div>

                    <div className="flex items-center gap-2">
                        <Link
                            href="/admin/roles/permissions"
                            className="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs font-semibold text-slate-700 dark:text-slate-200 hover:bg-slate-50 dark:hover:bg-slate-700 transition"
                        >
                            <Key className="w-3.5 h-3.5 text-blue-600" />
                            <span>Permission Registry</span>
                        </Link>
                        <Link
                            href="/admin/roles/create"
                            className="inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-xl bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold transition shadow-sm"
                        >
                            <Plus className="w-3.5 h-3.5" />
                            <span>New Role</span>
                        </Link>
                    </div>
                </div>

                {/* Role Cards Grid */}
                <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">
                    {roles.map(role => {
                        const isProtected = PROTECTED_ROLES.includes(role.name);
                        const permCount = role.permissions?.length || 0;

                        return (
                            <div
                                key={role.id}
                                className="flex flex-col justify-between bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 p-5 shadow-sm hover:border-blue-200 dark:hover:border-blue-900 transition-all"
                            >
                                <div>
                                    <div className="flex items-start justify-between">
                                        <div className={`p-2.5 rounded-xl ${
                                            isProtected
                                                ? 'bg-blue-100 dark:bg-blue-950/50 text-blue-700 dark:text-blue-300'
                                                : 'bg-emerald-100 dark:bg-emerald-950/50 text-emerald-700 dark:text-emerald-300'
                                        }`}>
                                            <ShieldCheck className="w-5 h-5" />
                                        </div>
                                        {isProtected ? (
                                            <span className="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 border border-slate-200 dark:border-slate-700">
                                                <Lock className="w-3 h-3" /> Core Tier
                                            </span>
                                        ) : (
                                            <span className="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-blue-50 text-blue-700 dark:bg-blue-950/40 dark:text-blue-300">
                                                Custom Role
                                            </span>
                                        )}
                                    </div>

                                    <h3 className="mt-4 font-bold text-base text-slate-900 dark:text-white">
                                        {role.name}
                                    </h3>
                                    <p className="mt-1 text-xs text-slate-500 dark:text-slate-400">
                                        {permCount} assigned access privilege{permCount === 1 ? '' : 's'}
                                    </p>

                                    {/* Permission Pills preview */}
                                    <div className="mt-4 flex flex-wrap gap-1.5 max-h-20 overflow-y-auto pr-1">
                                        {role.permissions?.slice(0, 5).map(p => (
                                            <span
                                                key={p.id}
                                                className="px-2 py-0.5 rounded-md text-[10px] font-mono bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-400"
                                            >
                                                {p.name}
                                            </span>
                                        ))}
                                        {permCount > 5 && (
                                            <span className="px-2 py-0.5 rounded-md text-[10px] font-mono bg-slate-100 dark:bg-slate-800 text-slate-400">
                                                +{permCount - 5} more
                                            </span>
                                        )}
                                        {permCount === 0 && (
                                            <span className="text-[11px] text-slate-400 italic">No permissions mapped</span>
                                        )}
                                    </div>
                                </div>

                                <div className="mt-5 pt-4 border-t border-slate-100 dark:border-slate-800/80 flex items-center justify-between">
                                    <Link
                                        href={`/admin/roles/${role.id}/edit`}
                                        className="inline-flex items-center gap-1 px-3 py-1.5 rounded-xl border border-slate-200 dark:border-slate-700 text-xs font-semibold text-slate-700 dark:text-slate-200 hover:bg-slate-50 dark:hover:bg-slate-800 transition"
                                    >
                                        <Edit3 className="w-3 h-3" /> Edit Permissions
                                    </Link>

                                    {!isProtected && (
                                        <button
                                            type="button"
                                            onClick={() => setDeleteModalRole(role)}
                                            className="inline-flex items-center gap-1 px-2.5 py-1.5 rounded-xl text-xs font-medium text-rose-600 hover:bg-rose-50 dark:hover:bg-rose-950/30 transition"
                                        >
                                            <Trash2 className="w-3.5 h-3.5" /> Remove
                                        </button>
                                    )}
                                </div>
                            </div>
                        );
                    })}
                </div>
            </div>
        </AuthenticatedLayout>
    );
}
