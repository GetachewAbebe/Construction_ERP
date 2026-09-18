import { Head, Link } from '@inertiajs/react';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import {
    Package,
    AlertTriangle,
    XCircle,
    Repeat,
    Plus,
    FileSpreadsheet,
    ShieldAlert,
    CheckCircle2,
    ArrowUpRight,
} from 'lucide-react';

export default function InventoryDashboard({
    totalItems = 0,
    lowStockCount = 0,
    zeroStockCount = 0,
    openLoanCount = 0,
    healthPercentage = 0,
    chartCategories = [],
    chartData = [],
    recentAlerts = [],
}) {
    return (
        <AuthenticatedLayout title="Inventory Dashboard" header="Inventory & Warehouse">
            <div className="space-y-6">
                {/* Header Title */}
                <div className="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-2 border-b border-slate-200 dark:border-slate-800">
                    <div>
                        <h1 className="text-xl sm:text-2xl font-extrabold tracking-tight text-slate-900 dark:text-white">
                            Inventory & Store Command
                        </h1>
                        <p className="text-xs sm:text-sm text-slate-500 dark:text-slate-400 mt-0.5">
                            Real-time stock catalog, warehouse movement logs, and material loans.
                        </p>
                    </div>

                    <div className="flex items-center gap-2">
                        <Link
                            href="/inventory/items/create"
                            className="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl bg-blue-900 hover:bg-blue-950 text-white font-bold text-xs shadow-sm transition-colors"
                        >
                            <Plus className="w-4 h-4" />
                            <span>Add Stock Item</span>
                        </Link>
                        <Link
                            href="/inventory/loans/create"
                            className="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl border border-slate-200 dark:border-slate-800 hover:bg-slate-100 dark:hover:bg-slate-800 text-slate-800 dark:text-white font-bold text-xs transition-colors"
                        >
                            <Repeat className="w-4 h-4 text-amber-500" />
                            <span>Issue Loan</span>
                        </Link>
                    </div>
                </div>

                {/* KPIs */}
                <div className="grid grid-cols-2 lg:grid-cols-4 gap-4">
                    <div className="rounded-2xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 p-5 shadow-xs flex items-center justify-between">
                        <div>
                            <div className="text-[11px] font-bold uppercase tracking-wider text-slate-400">Total Items</div>
                            <div className="text-2xl sm:text-3xl font-black text-blue-900 dark:text-blue-400 font-mono mt-1">{totalItems}</div>
                            <div className="text-[11px] text-slate-500 mt-0.5">Active Catalog</div>
                        </div>
                        <div className="p-3 rounded-xl bg-blue-50 dark:bg-blue-950/60 text-blue-900 dark:text-blue-400">
                            <Package className="w-6 h-6" />
                        </div>
                    </div>

                    <div className="rounded-2xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 p-5 shadow-xs flex items-center justify-between">
                        <div>
                            <div className="text-[11px] font-bold uppercase tracking-wider text-slate-400">Low Stock</div>
                            <div className="text-2xl sm:text-3xl font-black text-amber-600 dark:text-amber-400 font-mono mt-1">{lowStockCount}</div>
                            <div className="text-[11px] text-slate-500 mt-0.5">&le; 5 Units Remaining</div>
                        </div>
                        <div className="p-3 rounded-xl bg-amber-50 dark:bg-amber-950/60 text-amber-600 dark:text-amber-400">
                            <AlertTriangle className="w-6 h-6" />
                        </div>
                    </div>

                    <div className="rounded-2xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 p-5 shadow-xs flex items-center justify-between">
                        <div>
                            <div className="text-[11px] font-bold uppercase tracking-wider text-slate-400">Out of Stock</div>
                            <div className="text-2xl sm:text-3xl font-black text-rose-600 dark:text-rose-400 font-mono mt-1">{zeroStockCount}</div>
                            <div className="text-[11px] text-slate-500 mt-0.5">Zero Quantity</div>
                        </div>
                        <div className="p-3 rounded-xl bg-rose-50 dark:bg-rose-950/60 text-rose-600 dark:text-rose-400">
                            <XCircle className="w-6 h-6" />
                        </div>
                    </div>

                    <div className="rounded-2xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 p-5 shadow-xs flex items-center justify-between">
                        <div>
                            <div className="text-[11px] font-bold uppercase tracking-wider text-slate-400">Active Loans</div>
                            <div className="text-2xl sm:text-3xl font-black text-indigo-600 dark:text-indigo-400 font-mono mt-1">{openLoanCount}</div>
                            <div className="text-[11px] text-slate-500 mt-0.5">Site Gate Passes</div>
                        </div>
                        <div className="p-3 rounded-xl bg-indigo-50 dark:bg-indigo-950/60 text-indigo-600 dark:text-indigo-400">
                            <Repeat className="w-6 h-6" />
                        </div>
                    </div>
                </div>

                {/* Content Split */}
                <div className="grid grid-cols-1 lg:grid-cols-3 gap-6">
                    {/* Top Stock Levels */}
                    <div className="lg:col-span-2 rounded-2xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 p-5 shadow-xs">
                        <div className="flex items-center justify-between mb-4">
                            <div>
                                <h3 className="text-sm font-bold text-slate-900 dark:text-white">Top Inventory Items by Stock Level</h3>
                                <p className="text-xs text-slate-400">Catalog Stability: {healthPercentage}%</p>
                            </div>
                            <Link href="/inventory/items" className="text-xs font-bold text-blue-900 dark:text-blue-400 hover:underline">
                                Catalog &rarr;
                            </Link>
                        </div>

                        {chartCategories.length === 0 ? (
                            <p className="text-xs text-slate-400 py-6 text-center">No inventory items in stock.</p>
                        ) : (
                            <div className="space-y-3">
                                {chartCategories.map((name, i) => {
                                    const qty = chartData[i] || 0;
                                    const maxVal = Math.max(...chartData, 1);
                                    const pct = Math.min(100, Math.round((qty / maxVal) * 100));

                                    return (
                                        <div key={i} className="space-y-1">
                                            <div className="flex items-center justify-between text-xs">
                                                <span className="font-semibold text-slate-800 dark:text-slate-200 truncate">{name}</span>
                                                <span className="font-mono font-bold text-slate-900 dark:text-white">{qty} units</span>
                                            </div>
                                            <div className="h-2 w-full rounded-full bg-slate-100 dark:bg-slate-800 overflow-hidden">
                                                <div
                                                    className="h-full rounded-full bg-gradient-to-r from-blue-700 to-indigo-600"
                                                    style={{ width: `${pct}%` }}
                                                />
                                            </div>
                                        </div>
                                    );
                                })}
                            </div>
                        )}
                    </div>

                    {/* Low Stock Alerts */}
                    <div className="rounded-2xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 p-5 shadow-xs">
                        <div className="flex items-center justify-between mb-3">
                            <h3 className="text-sm font-bold text-slate-900 dark:text-white flex items-center gap-2">
                                <ShieldAlert className="w-4 h-4 text-amber-500" />
                                <span>Low Stock Alerts</span>
                            </h3>
                            <Link
                                href="/inventory/items"
                                className="text-[11px] font-bold text-blue-600 dark:text-blue-400 hover:underline inline-flex items-center gap-1"
                            >
                                <span>Manage All</span>
                                <ArrowUpRight className="w-3 h-3" />
                            </Link>
                        </div>

                        {recentAlerts.length === 0 ? (
                            <div className="py-8 text-center text-xs text-emerald-600 dark:text-emerald-400 flex flex-col items-center gap-2">
                                <CheckCircle2 className="w-6 h-6" />
                                <span>All items have adequate inventory levels.</span>
                            </div>
                        ) : (
                            <div className="space-y-2.5">
                                {recentAlerts.map((item) => {
                                    const isCritical = (item.quantity ?? 0) <= 0;
                                    return (
                                        <Link
                                            key={item.id}
                                            href={`/inventory/items/${item.id}/edit`}
                                            className={`p-3 rounded-xl border flex items-center justify-between text-xs transition-colors block hover:opacity-90 ${
                                                isCritical
                                                    ? 'bg-rose-50/70 dark:bg-rose-950/30 border-rose-200 dark:border-rose-900/50'
                                                    : 'bg-amber-50/60 dark:bg-amber-950/30 border-amber-200/80 dark:border-amber-900/40'
                                            }`}
                                        >
                                            <div className="min-w-0 pr-2">
                                                <div className="font-bold text-slate-900 dark:text-white truncate">{item.name}</div>
                                                <div className="text-[11px] text-slate-500">SKU: #{item.item_no || item.sku || item.id}</div>
                                            </div>
                                            <span className={`px-2 py-0.5 rounded-full font-bold text-[10px] shrink-0 ${
                                                isCritical
                                                    ? 'bg-rose-600 text-white'
                                                    : 'bg-amber-500 text-slate-950'
                                            }`}>
                                                {isCritical ? 'Out of Stock' : `${item.quantity} ${item.unit_of_measurement || 'left'}`}
                                            </span>
                                        </Link>
                                    );
                                })}
                            </div>
                        )}
                    </div>
                </div>
            </div>
        </AuthenticatedLayout>
    );
}
