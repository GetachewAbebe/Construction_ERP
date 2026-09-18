import { useState } from 'react';
import { Head, Link, useForm } from '@inertiajs/react';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import {
    ShieldCheck,
    Key,
    ArrowLeft,
    Check,
    Save,
    CheckSquare,
    Square,
} from 'lucide-react';

export default function Create({ permissions = {} }) {
    const { data, setData, post, processing, errors } = useForm({
        name: '',
        display_name: '',
        description: '',
        permissions: [],
    });

    const handlePermissionToggle = (id) => {
        setData('permissions', data.permissions.includes(id)
            ? data.permissions.filter(pId => pId !== id)
            : [...data.permissions, id]
        );
    };

    const handleModuleToggle = (modulePermissions, shouldSelect) => {
        const moduleIds = modulePermissions.map(p => p.id);
        if (shouldSelect) {
            const next = Array.from(new Set([...data.permissions, ...moduleIds]));
            setData('permissions', next);
        } else {
            setData('permissions', data.permissions.filter(id => !moduleIds.includes(id)));
        }
    };

    const handleSubmit = (e) => {
        e.preventDefault();
        post('/admin/roles');
    };

    return (
        <AuthenticatedLayout title="New Role" header="System Administration">
            <Head title="Create Authorization Tier" />

            <div className="max-w-4xl mx-auto space-y-6">
                {/* Header */}
                <div className="flex items-center justify-between gap-4 pb-2 border-b border-slate-200 dark:border-slate-800">
                    <div className="flex items-center gap-2">
                        <Link
                            href="/admin/roles"
                            className="p-1.5 rounded-lg text-slate-400 hover:text-slate-700 dark:hover:text-slate-200 hover:bg-slate-100 dark:hover:bg-slate-800 transition"
                        >
                            <ArrowLeft className="w-4 h-4" />
                        </Link>
                        <div>
                            <h1 className="text-xl sm:text-2xl font-extrabold tracking-tight text-slate-900 dark:text-white">
                                Provision Authorization Tier
                            </h1>
                            <p className="text-xs sm:text-sm text-slate-500 dark:text-slate-400">
                                Define new security role and configure granted operational permissions.
                            </p>
                        </div>
                    </div>
                </div>

                <form onSubmit={handleSubmit} className="space-y-6">
                    {/* Role Details Card */}
                    <div className="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 p-6 shadow-sm space-y-5">
                        <div className="flex items-center gap-2 pb-3 border-b border-slate-100 dark:border-slate-800 text-slate-900 dark:text-white font-bold text-sm">
                            <ShieldCheck className="w-4 h-4 text-blue-600" />
                            <span>Role Specification</span>
                        </div>

                        <div className="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label className="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">
                                    System Role Key <span className="text-rose-500">*</span>
                                </label>
                                <input
                                    type="text"
                                    required
                                    placeholder="e.g. SiteAuditor or LogisticsLead"
                                    value={data.name}
                                    onChange={e => setData('name', e.target.value)}
                                    className={`w-full text-xs rounded-xl border ${
                                        errors.name ? 'border-rose-500' : 'border-slate-200 dark:border-slate-700'
                                    } bg-white dark:bg-slate-800 text-slate-900 dark:text-white py-2 px-3 focus:outline-none focus:ring-2 focus:ring-blue-500`}
                                />
                                {errors.name && <p className="text-rose-500 text-[11px] mt-1">{errors.name}</p>}
                            </div>

                            <div>
                                <label className="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">
                                    Display Name (Optional)
                                </label>
                                <input
                                    type="text"
                                    placeholder="e.g. Site Quality Auditor"
                                    value={data.display_name}
                                    onChange={e => setData('display_name', e.target.value)}
                                    className="w-full text-xs rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-white py-2 px-3 focus:outline-none focus:ring-2 focus:ring-blue-500"
                                />
                            </div>

                            <div className="sm:col-span-2">
                                <label className="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">
                                    Description & Functional Scope
                                </label>
                                <textarea
                                    rows={2}
                                    placeholder="Summarize the authorization level and business scope of this tier..."
                                    value={data.description}
                                    onChange={e => setData('description', e.target.value)}
                                    className="w-full text-xs rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-white py-2 px-3 focus:outline-none focus:ring-2 focus:ring-blue-500"
                                />
                            </div>
                        </div>
                    </div>

                    {/* Permissions Matrix Card */}
                    <div className="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 p-6 shadow-sm space-y-5">
                        <div className="flex items-center justify-between pb-3 border-b border-slate-100 dark:border-slate-800">
                            <div className="flex items-center gap-2 text-slate-900 dark:text-white font-bold text-sm">
                                <Key className="w-4 h-4 text-emerald-600" />
                                <span>Privilege Matrix ({data.permissions.length} Selected)</span>
                            </div>
                        </div>

                        <div className="space-y-4">
                            {Object.entries(permissions).map(([moduleName, modulePerms]) => {
                                const allSelected = modulePerms.every(p => data.permissions.includes(p.id));
                                const someSelected = modulePerms.some(p => data.permissions.includes(p.id));

                                return (
                                    <div
                                        key={moduleName}
                                        className="rounded-xl border border-slate-200/80 dark:border-slate-800 bg-slate-50/50 dark:bg-slate-800/30 p-4"
                                    >
                                        <div className="flex items-center justify-between mb-3 pb-2 border-b border-slate-200/60 dark:border-slate-800">
                                            <div className="flex items-center gap-2">
                                                <button
                                                    type="button"
                                                    onClick={() => handleModuleToggle(modulePerms, !allSelected)}
                                                    className="flex items-center gap-2 text-xs font-bold uppercase tracking-wider text-slate-800 dark:text-slate-200 hover:text-blue-600 transition"
                                                >
                                                    {allSelected ? (
                                                        <CheckSquare className="w-4 h-4 text-blue-600" />
                                                    ) : (
                                                        <Square className="w-4 h-4 text-slate-400" />
                                                    )}
                                                    <span>Module: {moduleName}</span>
                                                </button>
                                            </div>
                                            <span className="text-[11px] font-medium text-slate-400">
                                                {modulePerms.filter(p => data.permissions.includes(p.id)).length} of {modulePerms.length} enabled
                                            </span>
                                        </div>

                                        <div className="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-2.5">
                                            {modulePerms.map(perm => {
                                                const isChecked = data.permissions.includes(perm.id);
                                                return (
                                                    <label
                                                        key={perm.id}
                                                        className={`flex items-center gap-2 p-2 rounded-lg border text-xs cursor-pointer select-none transition ${
                                                            isChecked
                                                                ? 'border-blue-500 bg-blue-50/60 dark:bg-blue-950/40 text-blue-900 dark:text-blue-200 font-semibold'
                                                                : 'border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-700 dark:text-slate-300 hover:bg-slate-50'
                                                        }`}
                                                    >
                                                        <input
                                                            type="checkbox"
                                                            checked={isChecked}
                                                            onChange={() => handlePermissionToggle(perm.id)}
                                                            className="rounded border-slate-300 text-blue-600 focus:ring-blue-500 w-3.5 h-3.5"
                                                        />
                                                        <span className="truncate">{perm.name}</span>
                                                    </label>
                                                );
                                            })}
                                        </div>
                                    </div>
                                );
                            })}
                        </div>
                    </div>

                    {/* Actions */}
                    <div className="flex items-center justify-end gap-3 pt-2">
                        <Link
                            href="/admin/roles"
                            className="px-4 py-2 rounded-xl text-xs font-semibold text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 transition"
                        >
                            Cancel
                        </Link>
                        <button
                            type="submit"
                            disabled={processing}
                            className="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold transition shadow-sm disabled:opacity-50"
                        >
                            <Save className="w-4 h-4" />
                            <span>{processing ? 'Provisioning...' : 'Provision Role Tier'}</span>
                        </button>
                    </div>
                </form>
            </div>
        </AuthenticatedLayout>
    );
}
