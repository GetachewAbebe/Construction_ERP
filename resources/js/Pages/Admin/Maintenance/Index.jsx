import { useState } from 'react';
import { Head, Link, router } from '@inertiajs/react';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import {
    Database,
    Cpu,
    HardDrive,
    Trash2,
    Zap,
    FileText,
    Download,
    Plus,
    RefreshCw,
    CheckCircle2,
    AlertCircle,
    ArrowLeft,
    ShieldCheck,
    Layers,
    Server,
} from 'lucide-react';

export default function Index({
    systemInfo = {},
    cacheInfo = {},
    storageInfo = {},
    logFiles = [],
    pendingMigrations = 0,
    backups = [],
}) {
    const [actionRunning, setActionRunning] = useState(null);

    const executeAction = (url, name) => {
        setActionRunning(name);
        router.post(url, {}, {
            onFinish: () => setActionRunning(null),
        });
    };

    return (
        <AuthenticatedLayout title="System Maintenance" header="System Administration">
            <Head title="System Maintenance & Health" />

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
                                System Health & Maintenance
                            </h1>
                            <p className="text-xs sm:text-sm text-slate-500 dark:text-slate-400">
                                Cache management, schema migrations, automated snapshots, and storage diagnostics.
                            </p>
                        </div>
                    </div>
                </div>

                {/* Schema Migration Status Banner */}
                <div className="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 p-5 shadow-sm flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                    <div className="flex items-center gap-4">
                        <div className={`p-3 rounded-2xl ${
                            pendingMigrations > 0
                                ? 'bg-amber-100 dark:bg-amber-950/50 text-amber-700 dark:text-amber-300'
                                : 'bg-emerald-100 dark:bg-emerald-950/50 text-emerald-700 dark:text-emerald-300'
                        }`}>
                            <Database className="w-6 h-6" />
                        </div>
                        <div>
                            <h2 className="text-base font-bold text-slate-900 dark:text-white">Database Schema Status</h2>
                            <p className="text-xs sm:text-sm mt-0.5">
                                {pendingMigrations > 0 ? (
                                    <span className="text-rose-600 font-semibold">{pendingMigrations} pending migration script(s) waiting for execution.</span>
                                ) : (
                                    <span className="text-emerald-600 font-semibold flex items-center gap-1">
                                        <CheckCircle2 className="w-3.5 h-3.5" /> All database table migrations are fully synchronized.
                                    </span>
                                )}
                            </p>
                        </div>
                    </div>

                    <button
                        type="button"
                        disabled={actionRunning === 'migrate'}
                        onClick={() => executeAction('/admin/maintenance/migrate', 'migrate')}
                        className="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold transition shadow-sm disabled:opacity-50"
                    >
                        <RefreshCw className={`w-3.5 h-3.5 ${actionRunning === 'migrate' ? 'animate-spin' : ''}`} />
                        <span>{actionRunning === 'migrate' ? 'Executing Migrations...' : 'Execute Migrations'}</span>
                    </button>
                </div>

                {/* Info Diagnostics Grids */}
                <div className="grid grid-cols-1 lg:grid-cols-2 gap-5">
                    {/* System Parameters Card */}
                    <div className="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 p-5 shadow-sm space-y-4">
                        <div className="flex items-center gap-2 pb-2 border-b border-slate-100 dark:border-slate-800 text-slate-900 dark:text-white font-bold text-sm">
                            <Cpu className="w-4 h-4 text-blue-600" />
                            <span>Environment & Infrastructure</span>
                        </div>
                        <dl className="grid grid-cols-2 gap-3 text-xs">
                            {Object.entries(systemInfo).map(([key, value]) => (
                                <div key={key} className="p-2.5 rounded-xl bg-slate-50 dark:bg-slate-800/50">
                                    <dt className="text-[10px] uppercase font-bold text-slate-400 tracking-wider">
                                        {key.replace(/_/g, ' ')}
                                    </dt>
                                    <dd className="font-mono font-semibold text-slate-900 dark:text-white mt-1">
                                        {String(value)}
                                    </dd>
                                </div>
                            ))}
                        </dl>
                    </div>

                    {/* Storage & Memory Card */}
                    <div className="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 p-5 shadow-sm space-y-4">
                        <div className="flex items-center gap-2 pb-2 border-b border-slate-100 dark:border-slate-800 text-slate-900 dark:text-white font-bold text-sm">
                            <HardDrive className="w-4 h-4 text-emerald-600" />
                            <span>Storage & Cache Volumes</span>
                        </div>
                        <dl className="grid grid-cols-2 gap-3 text-xs">
                            {Object.entries(storageInfo).map(([key, value]) => (
                                <div key={key} className="p-2.5 rounded-xl bg-slate-50 dark:bg-slate-800/50">
                                    <dt className="text-[10px] uppercase font-bold text-slate-400 tracking-wider">
                                        {key.replace(/_/g, ' ')}
                                    </dt>
                                    <dd className="font-mono font-semibold text-emerald-600 dark:text-emerald-400 mt-1">
                                        {String(value)}
                                    </dd>
                                </div>
                            ))}
                            <div className="p-2.5 rounded-xl bg-slate-50 dark:bg-slate-800/50">
                                <dt className="text-[10px] uppercase font-bold text-slate-400 tracking-wider">
                                    Cache Driver
                                </dt>
                                <dd className="font-mono font-semibold text-slate-900 dark:text-white mt-1">
                                    {cacheInfo.driver || 'file'}
                                </dd>
                            </div>
                        </dl>
                    </div>
                </div>

                {/* Maintenance Quick Actions */}
                <div className="grid grid-cols-1 sm:grid-cols-3 gap-5">
                    {/* Clear Cache */}
                    <div className="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 p-5 shadow-sm flex flex-col justify-between items-center text-center">
                        <div className="p-3 rounded-2xl bg-amber-50 dark:bg-amber-950/40 text-amber-600 mb-3">
                            <Trash2 className="w-6 h-6" />
                        </div>
                        <h2 className="font-bold text-sm text-slate-900 dark:text-white">Purge System Cache</h2>
                        <p className="text-xs text-slate-500 mt-1 mb-4">
                            Flush configuration, route maps, and compiled view caches from disk memory.
                        </p>
                        <button
                            type="button"
                            disabled={actionRunning === 'cache'}
                            onClick={() => executeAction('/admin/maintenance/clear-cache', 'cache')}
                            className="w-full py-2 px-3 rounded-xl bg-amber-600 hover:bg-amber-700 text-white text-xs font-bold transition shadow-xs disabled:opacity-50"
                        >
                            {actionRunning === 'cache' ? 'Purging...' : 'Purge All Caches'}
                        </button>
                    </div>

                    {/* Optimize */}
                    <div className="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 p-5 shadow-sm flex flex-col justify-between items-center text-center">
                        <div className="p-3 rounded-2xl bg-emerald-50 dark:bg-emerald-950/40 text-emerald-600 mb-3">
                            <Zap className="w-6 h-6" />
                        </div>
                        <h2 className="font-bold text-sm text-slate-900 dark:text-white">Optimize Execution</h2>
                        <p className="text-xs text-slate-500 mt-1 mb-4">
                            Compile production manifests for configuration, routing, and framework views.
                        </p>
                        <button
                            type="button"
                            disabled={actionRunning === 'optimize'}
                            onClick={() => executeAction('/admin/maintenance/optimize', 'optimize')}
                            className="w-full py-2 px-3 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold transition shadow-xs disabled:opacity-50"
                        >
                            {actionRunning === 'optimize' ? 'Optimizing...' : 'Run Optimization'}
                        </button>
                    </div>

                    {/* Clear Logs */}
                    <div className="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 p-5 shadow-sm flex flex-col justify-between items-center text-center">
                        <div className="p-3 rounded-2xl bg-rose-50 dark:bg-rose-950/40 text-rose-600 mb-3">
                            <FileText className="w-6 h-6" />
                        </div>
                        <h2 className="font-bold text-sm text-slate-900 dark:text-white">Flush Error Logs</h2>
                        <p className="text-xs text-slate-500 mt-1 mb-4">
                            Truncate historical application exception log files to reclaim storage capacity.
                        </p>
                        <button
                            type="button"
                            disabled={actionRunning === 'logs'}
                            onClick={() => executeAction('/admin/maintenance/clear-logs', 'logs')}
                            className="w-full py-2 px-3 rounded-xl bg-rose-600 hover:bg-rose-700 text-white text-xs font-bold transition shadow-xs disabled:opacity-50"
                        >
                            {actionRunning === 'logs' ? 'Flushing...' : 'Clear Log Files'}
                        </button>
                    </div>
                </div>

                {/* Database Backups Card */}
                <div className="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-sm overflow-hidden">
                    <div className="p-4 border-b border-slate-100 dark:border-slate-800 flex items-center justify-between">
                        <div className="flex items-center gap-2 text-slate-900 dark:text-white font-bold text-sm">
                            <Server className="w-4 h-4 text-blue-600" />
                            <span>Database Backup Archives</span>
                        </div>
                        <button
                            type="button"
                            disabled={actionRunning === 'backup'}
                            onClick={() => executeAction('/admin/maintenance/create-backup', 'backup')}
                            className="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold transition shadow-xs disabled:opacity-50"
                        >
                            <Plus className="w-3.5 h-3.5" />
                            <span>{actionRunning === 'backup' ? 'Generating Snapshot...' : 'New Backup Snapshot'}</span>
                        </button>
                    </div>

                    <div className="overflow-x-auto">
                        <table className="w-full text-left border-collapse text-xs">
                            <thead>
                                <tr className="border-b border-slate-200 dark:border-slate-800 bg-slate-50 dark:bg-slate-800/60 text-[11px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">
                                    <th className="py-2.5 px-4">Archive Snapshot File</th>
                                    <th className="py-2.5 px-4">File Size</th>
                                    <th className="py-2.5 px-4">Timestamp</th>
                                    <th className="py-2.5 px-4 text-right">Download</th>
                                </tr>
                            </thead>
                            <tbody className="divide-y divide-slate-100 dark:divide-slate-800/60">
                                {backups.length === 0 ? (
                                    <tr>
                                        <td colSpan={4} className="py-8 text-center text-slate-400">
                                            No backup archives detected in storage repository.
                                        </td>
                                    </tr>
                                ) : (
                                    backups.map(b => (
                                        <tr key={b.name} className="hover:bg-slate-50/50 dark:hover:bg-slate-800/30">
                                            <td className="py-2.5 px-4 font-mono text-slate-900 dark:text-white font-semibold">
                                                {b.name}
                                            </td>
                                            <td className="py-2.5 px-4 text-slate-500 font-mono">
                                                {b.size}
                                            </td>
                                            <td className="py-2.5 px-4 text-slate-500">
                                                {b.date}
                                            </td>
                                            <td className="py-2.5 px-4 text-right">
                                                <a
                                                    href={`/admin/maintenance/backup/download/${b.name}`}
                                                    className="inline-flex items-center gap-1 text-blue-600 dark:text-blue-400 font-bold hover:underline"
                                                >
                                                    <Download className="w-3 h-3" /> Download
                                                </a>
                                            </td>
                                        </tr>
                                    ))
                                )}
                            </tbody>
                        </table>
                    </div>
                </div>

                {/* Recent Log Files Card */}
                <div className="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-sm overflow-hidden">
                    <div className="p-4 border-b border-slate-100 dark:border-slate-800">
                        <div className="flex items-center gap-2 text-slate-900 dark:text-white font-bold text-sm">
                            <FileText className="w-4 h-4 text-slate-400" />
                            <span>Recent Application Log Files</span>
                        </div>
                    </div>

                    <div className="overflow-x-auto">
                        <table className="w-full text-left border-collapse text-xs">
                            <thead>
                                <tr className="border-b border-slate-200 dark:border-slate-800 bg-slate-50 dark:bg-slate-800/60 text-[11px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">
                                    <th className="py-2.5 px-4">Log Filename</th>
                                    <th className="py-2.5 px-4">File Size</th>
                                    <th className="py-2.5 px-4">Last Modified</th>
                                </tr>
                            </thead>
                            <tbody className="divide-y divide-slate-100 dark:divide-slate-800/60">
                                {logFiles.length === 0 ? (
                                    <tr>
                                        <td colSpan={3} className="py-6 text-center text-slate-400">
                                            No recent log files found.
                                        </td>
                                    </tr>
                                ) : (
                                    logFiles.map(log => (
                                        <tr key={log.name} className="hover:bg-slate-50/50 dark:hover:bg-slate-800/30">
                                            <td className="py-2 px-4 font-mono text-slate-900 dark:text-white">
                                                {log.name}
                                            </td>
                                            <td className="py-2 px-4 font-mono text-slate-500">
                                                {log.size}
                                            </td>
                                            <td className="py-2 px-4 text-slate-500">
                                                {log.modified}
                                            </td>
                                        </tr>
                                    ))
                                )}
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </AuthenticatedLayout>
    );
}
