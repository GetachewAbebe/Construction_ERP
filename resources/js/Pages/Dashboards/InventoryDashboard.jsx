import { useState } from 'react';
import { Head, Link } from '@inertiajs/react';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import DonutBreakdownChart from '@/Components/Dashboard/DonutBreakdownChart';
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
    ArrowRight,
    Truck,
    HardHat,
    Wrench,
    Search,
    Printer,
    Layers,
    Warehouse,
    QrCode,
} from 'lucide-react';

export default function InventoryDashboard({
    totalItems = 0,
    stableItemsCount = 0,
    lowStockCount = 0,
    zeroStockCount = 0,
    openLoanCount = 0,
    pendingLoanCount = 0,
    healthPercentage = 0,
    topItems = [],
    chartCategories = [],
    chartData = [],
    recentAlerts = [],
    zeroStockItems = [],
    fleetTotal = 0,
    fleetOperational = 0,
    fleetMaintenance = 0,
    fleetStandby = 0,
    fleetServiceDue = 0,
    activeLoansList = [],
    categoryBreakdown = [],
}) {
    const [itemSearch, setItemSearch] = useState('');

    const allAlertItems = [
        ...zeroStockItems.map(i => ({ ...i, isZero: true })),
        ...recentAlerts.map(i => ({ ...i, isZero: false })),
    ];

    const filteredAlerts = allAlertItems.filter(item =>
        (item.name || '').toLowerCase().includes(itemSearch.toLowerCase()) ||
        (item.category || '').toLowerCase().includes(itemSearch.toLowerCase())
    );

    const fleetHealthPct = fleetTotal > 0 ? Math.round((fleetOperational / fleetTotal) * 100) : 100;

    const formattedCategoryData = categoryBreakdown.map(c => ({
        category: c.category,
        total: Number(c.count || 0),
    }));

    return (
        <AuthenticatedLayout title="Inventory & Warehouse Dashboard" header="Inventory & Warehouse">
            <div className="space-y-6">
                {/* HERO HEADER */}
                <div className="rounded-3xl bg-gradient-to-r from-slate-950 via-slate-900 to-amber-950 p-6 sm:p-8 text-white shadow-lg relative overflow-hidden">
                    <div className="absolute right-0 top-0 translate-x-12 -translate-y-12 w-96 h-96 bg-amber-500/10 rounded-full blur-3xl pointer-events-none" />

                    <div className="relative z-10 flex flex-col lg:flex-row lg:items-center lg:justify-between gap-6">
                        <div>
                            <div className="flex items-center gap-2 mb-2">
                                <span className="px-2.5 py-0.5 rounded-full bg-amber-500/20 text-amber-300 text-[10px] font-extrabold tracking-wider uppercase border border-amber-500/30 flex items-center gap-1.5">
                                    <span className="w-1.5 h-1.5 rounded-full bg-amber-400 animate-pulse" />
                                    Warehouse Logistics &amp; Plant Command
                                </span>
                                <span className="text-xs text-slate-300 hidden sm:inline">
                                    Stock Stability Index: {healthPercentage}%
                                </span>
                            </div>
                            <h1 className="text-2xl sm:text-3xl font-black tracking-tight text-white">
                                Materials Inventory &amp; Fleet Operations
                            </h1>
                            <p className="text-xs sm:text-sm text-slate-300 mt-1 max-w-2xl">
                                Real-time warehouse catalog levels, gate pass loan tracking, critical replenishment alerts, and plant machinery telemetry.
                            </p>
                        </div>

                        <div className="flex flex-wrap items-center gap-2.5 shrink-0">
                            <Link
                                href="/inventory/items/create"
                                className="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-amber-500 hover:bg-amber-400 text-slate-950 font-extrabold text-xs shadow-md shadow-amber-500/20 transition-all cursor-pointer"
                            >
                                <Plus className="w-4 h-4" />
                                <span>Add Stock SKU</span>
                            </Link>

                            <Link
                                href="/inventory/loans/create"
                                className="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-white/10 hover:bg-white/20 text-white font-bold text-xs border border-white/20 backdrop-blur-sm transition-all cursor-pointer relative"
                            >
                                <Repeat className="w-4 h-4 text-amber-300" />
                                <span>Issue Gate Pass</span>
                                {pendingLoanCount > 0 && (
                                    <span className="px-1.5 py-0.2 rounded-full bg-amber-400 text-slate-950 text-[10px] font-black ml-0.5">
                                        {pendingLoanCount}
                                    </span>
                                )}
                            </Link>

                            <Link
                                href="/inventory/equipment"
                                className="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-white/10 hover:bg-white/20 text-white font-bold text-xs border border-white/20 backdrop-blur-sm transition-all cursor-pointer"
                            >
                                <Truck className="w-4 h-4 text-orange-400" />
                                <span>Fleet &amp; Plant</span>
                            </Link>
                        </div>
                    </div>
                </div>

                {/* 5 TOP LOGISTICS KPI METRICS */}
                <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4">
                    {/* 1. Total Items */}
                    <div className="rounded-2xl sm:rounded-3xl border border-slate-100 dark:border-slate-800/80 bg-white dark:bg-slate-900 p-5 shadow-xs flex flex-col justify-between">
                        <div className="flex items-start justify-between gap-2">
                            <div>
                                <span className="text-[11px] font-bold uppercase tracking-wider text-slate-400">
                                    Catalog SKUs
                                </span>
                                <div className="text-2xl sm:text-3xl font-black text-slate-900 dark:text-white font-mono mt-1">
                                    {totalItems}
                                </div>
                            </div>
                            <div className="p-2.5 rounded-2xl bg-blue-50 dark:bg-blue-950/60 text-blue-600 dark:text-blue-400 shrink-0">
                                <Package className="w-5 h-5" />
                            </div>
                        </div>
                        <div className="mt-3 pt-2.5 border-t border-slate-100 dark:border-slate-800 flex items-center justify-between text-[11px]">
                            <span className="text-slate-500">Catalog Health</span>
                            <span className="font-bold text-blue-600 dark:text-blue-400 font-mono">
                                {healthPercentage}% Stable
                            </span>
                        </div>
                    </div>

                    {/* 2. Low Stock Alerts */}
                    <div className="rounded-2xl sm:rounded-3xl border border-slate-100 dark:border-slate-800/80 bg-white dark:bg-slate-900 p-5 shadow-xs flex flex-col justify-between">
                        <div className="flex items-start justify-between gap-2">
                            <div>
                                <span className="text-[11px] font-bold uppercase tracking-wider text-slate-400">
                                    Low Stock (&le; 5)
                                </span>
                                <div className="text-2xl sm:text-3xl font-black text-amber-600 dark:text-amber-400 font-mono mt-1">
                                    {lowStockCount}
                                </div>
                            </div>
                            <div className="p-2.5 rounded-2xl bg-amber-50 dark:bg-amber-950/60 text-amber-600 dark:text-amber-400 shrink-0">
                                <AlertTriangle className="w-5 h-5" />
                            </div>
                        </div>
                        <div className="mt-3 pt-2.5 border-t border-slate-100 dark:border-slate-800 flex items-center justify-between text-[11px]">
                            <span className="text-slate-500">Near Buffer</span>
                            <span className="font-bold text-amber-600 dark:text-amber-400">Reorder Soon</span>
                        </div>
                    </div>

                    {/* 3. Depleted Stockouts */}
                    <div className={`rounded-2xl sm:rounded-3xl border p-5 shadow-xs flex flex-col justify-between transition-colors ${
                        zeroStockCount > 0
                            ? 'border-rose-200 dark:border-rose-900/60 bg-rose-50/40 dark:bg-rose-950/20'
                            : 'border-slate-100 dark:border-slate-800/80 bg-white dark:bg-slate-900'
                    }`}>
                        <div className="flex items-start justify-between gap-2">
                            <div>
                                <span className="text-[11px] font-bold uppercase tracking-wider text-slate-400">
                                    Stockouts (0 Qty)
                                </span>
                                <div className="text-2xl sm:text-3xl font-black text-rose-600 dark:text-rose-400 font-mono mt-1">
                                    {zeroStockCount}
                                </div>
                            </div>
                            <div className="p-2.5 rounded-2xl bg-rose-100 dark:bg-rose-950/80 text-rose-600 dark:text-rose-400 shrink-0">
                                <XCircle className="w-5 h-5" />
                            </div>
                        </div>
                        <div className="mt-3 pt-2.5 border-t border-slate-100 dark:border-slate-800/80 flex items-center justify-between text-[11px]">
                            <span className="text-slate-500">Urgency Level</span>
                            <span className="font-bold text-rose-600 dark:text-rose-400">Restock Priority</span>
                        </div>
                    </div>

                    {/* 4. Active Loans & Gate Passes */}
                    <div className="rounded-2xl sm:rounded-3xl border border-slate-100 dark:border-slate-800/80 bg-white dark:bg-slate-900 p-5 shadow-xs flex flex-col justify-between">
                        <div className="flex items-start justify-between gap-2">
                            <div>
                                <span className="text-[11px] font-bold uppercase tracking-wider text-slate-400">
                                    Active Gate Passes
                                </span>
                                <div className="text-2xl sm:text-3xl font-black text-indigo-600 dark:text-indigo-400 font-mono mt-1">
                                    {openLoanCount}
                                </div>
                            </div>
                            <div className="p-2.5 rounded-2xl bg-indigo-50 dark:bg-indigo-950/60 text-indigo-600 dark:text-indigo-400 shrink-0">
                                <Repeat className="w-5 h-5" />
                            </div>
                        </div>
                        <div className="mt-3 pt-2.5 border-t border-slate-100 dark:border-slate-800 flex items-center justify-between text-[11px]">
                            <span className="text-slate-500">Field Loans</span>
                            <span className="font-semibold text-indigo-600 dark:text-indigo-400">Deployed</span>
                        </div>
                    </div>

                    {/* 5. Machinery Fleet Status */}
                    <Link
                        href="/inventory/equipment"
                        className="rounded-2xl sm:rounded-3xl border border-slate-100 dark:border-slate-800/80 bg-white dark:bg-slate-900 p-5 shadow-xs flex flex-col justify-between hover:border-orange-300 dark:hover:border-orange-700 transition-colors group cursor-pointer"
                    >
                        <div className="flex items-start justify-between gap-2">
                            <div>
                                <span className="text-[11px] font-bold uppercase tracking-wider text-slate-400 group-hover:text-orange-600 dark:group-hover:text-orange-400">
                                    Heavy Plant Fleet
                                </span>
                                <div className="text-2xl sm:text-3xl font-black text-slate-900 dark:text-white font-mono mt-1">
                                    {fleetTotal} Units
                                </div>
                            </div>
                            <div className="p-2.5 rounded-2xl bg-orange-50 dark:bg-orange-950/60 text-orange-600 dark:text-orange-400 shrink-0 group-hover:scale-105 transition-transform">
                                <Truck className="w-5 h-5" />
                            </div>
                        </div>
                        <div className="mt-3 pt-2.5 border-t border-slate-100 dark:border-slate-800 flex items-center justify-between text-[11px]">
                            <span className="text-slate-500">{fleetOperational} Operational</span>
                            <span className="font-bold text-emerald-600 dark:text-emerald-400 font-mono">
                                {fleetHealthPct}% Health
                            </span>
                        </div>
                    </Link>
                </div>

                {/* VISUAL ANALYTICS ROW: TOP STOCK LEVELS + FLEET TELEMETRY */}
                <div className="grid grid-cols-1 lg:grid-cols-12 gap-6 items-stretch">
                    {/* Top Stock Levels Visual Bar Chart */}
                    <div className="lg:col-span-7 rounded-2xl sm:rounded-3xl border border-slate-100 dark:border-slate-800/80 bg-white dark:bg-slate-900 p-5 sm:p-7 shadow-xs flex flex-col justify-between">
                        <div>
                            <div className="flex flex-col sm:flex-row sm:items-center justify-between gap-2 mb-4">
                                <div>
                                    <h3 className="text-base font-bold text-slate-900 dark:text-white tracking-tight">
                                        Top Material Stock Levels
                                    </h3>
                                    <p className="text-xs text-slate-500 dark:text-slate-400 mt-0.5">
                                        Warehouse inventory balances against replenishment thresholds.
                                    </p>
                                </div>
                                <Link
                                    href="/inventory/items"
                                    className="text-xs font-bold text-blue-600 dark:text-blue-400 hover:underline shrink-0"
                                >
                                    Full Catalog &rarr;
                                </Link>
                            </div>

                            {topItems.length === 0 ? (
                                <div className="py-12 text-center text-xs text-slate-400">
                                    No material items recorded in inventory.
                                </div>
                            ) : (
                                <div className="space-y-3.5">
                                    {topItems.slice(0, 6).map((item, idx) => {
                                        const qty = Number(item.quantity || 0);
                                        const maxQty = Math.max(...topItems.map(i => Number(i.quantity || 0)), 50);
                                        const pct = Math.min(100, Math.round((qty / maxQty) * 100));
                                        const isLow = qty <= 5;

                                        return (
                                            <div key={idx} className="space-y-1.5">
                                                <div className="flex items-center justify-between text-xs">
                                                    <span className="font-bold text-slate-900 dark:text-white truncate max-w-[200px] sm:max-w-[280px]">
                                                        {item.name}
                                                    </span>
                                                    <span className="font-mono font-bold text-slate-700 dark:text-slate-300">
                                                        {qty} {item.unit || 'units'}
                                                    </span>
                                                </div>
                                                <div className="h-2.5 w-full rounded-full bg-slate-100 dark:bg-slate-800 overflow-hidden">
                                                    <div
                                                        className={`h-full rounded-full transition-all duration-500 ${
                                                            isLow
                                                                ? 'bg-amber-500'
                                                                : 'bg-gradient-to-r from-blue-600 to-teal-500'
                                                        }`}
                                                        style={{ width: `${Math.max(5, pct)}%` }}
                                                    />
                                                </div>
                                            </div>
                                        );
                                    })}
                                </div>
                            )}
                        </div>

                        <div className="pt-4 mt-4 border-t border-slate-100 dark:border-slate-800/80 flex items-center justify-between text-xs text-slate-500">
                            <span>Catalog Stability: <strong className="text-slate-900 dark:text-white font-mono">{healthPercentage}%</strong></span>
                            <span>Healthy Stock: <strong className="text-emerald-600 dark:text-emerald-400 font-mono">{stableItemsCount}</strong> items</span>
                        </div>
                    </div>

                    {/* Heavy Machinery Fleet Telemetry Widget */}
                    <div className="lg:col-span-5 rounded-2xl sm:rounded-3xl border border-slate-100 dark:border-slate-800/80 bg-white dark:bg-slate-900 p-5 sm:p-7 shadow-xs flex flex-col justify-between">
                        <div>
                            <div className="flex items-center justify-between mb-4">
                                <h3 className="text-base font-bold text-slate-900 dark:text-white flex items-center gap-2">
                                    <Truck className="w-5 h-5 text-orange-500" />
                                    <span>Plant &amp; Fleet Telemetry</span>
                                </h3>
                                <Link
                                    href="/inventory/equipment"
                                    className="text-xs font-bold text-orange-600 dark:text-orange-400 hover:underline"
                                >
                                    Fleet Logs &rarr;
                                </Link>
                            </div>

                            <div className="p-4 rounded-2xl bg-slate-50 dark:bg-slate-800/50 border border-slate-100 dark:border-slate-700/60 mb-4">
                                <div className="flex items-center justify-between text-xs font-semibold text-slate-500 mb-2">
                                    <span>Fleet Operational Capacity</span>
                                    <span className="font-mono font-bold text-slate-900 dark:text-white">{fleetHealthPct}%</span>
                                </div>
                                <div className="h-3 w-full rounded-full bg-slate-200 dark:bg-slate-700 overflow-hidden">
                                    <div
                                        className="h-full rounded-full bg-gradient-to-r from-emerald-500 to-teal-500 transition-all duration-500"
                                        style={{ width: `${fleetHealthPct}%` }}
                                    />
                                </div>
                            </div>

                            <div className="grid grid-cols-2 gap-3 text-xs">
                                <div className="p-3 rounded-xl bg-emerald-50/70 dark:bg-emerald-950/30 border border-emerald-100 dark:border-emerald-900/40">
                                    <span className="text-[10px] uppercase font-bold text-emerald-700 dark:text-emerald-400">Operational</span>
                                    <div className="text-xl font-black text-emerald-900 dark:text-emerald-200 font-mono mt-0.5">
                                        {fleetOperational}
                                    </div>
                                    <span className="text-[10px] text-emerald-600 dark:text-emerald-400">Active on site</span>
                                </div>

                                <div className="p-3 rounded-xl bg-amber-50/70 dark:bg-amber-950/30 border border-amber-100 dark:border-amber-900/40">
                                    <span className="text-[10px] uppercase font-bold text-amber-700 dark:text-amber-400">In Workshop</span>
                                    <div className="text-xl font-black text-amber-900 dark:text-amber-200 font-mono mt-0.5">
                                        {fleetMaintenance}
                                    </div>
                                    <span className="text-[10px] text-amber-600 dark:text-amber-400">Service / repair</span>
                                </div>

                                <div className="p-3 rounded-xl bg-slate-100/70 dark:bg-slate-800/60 border border-slate-200 dark:border-slate-700">
                                    <span className="text-[10px] uppercase font-bold text-slate-600 dark:text-slate-400">Standby Reserve</span>
                                    <div className="text-xl font-black text-slate-900 dark:text-white font-mono mt-0.5">
                                        {fleetStandby}
                                    </div>
                                    <span className="text-[10px] text-slate-500">Ready to deploy</span>
                                </div>

                                <div className="p-3 rounded-xl bg-rose-50/70 dark:bg-rose-950/30 border border-rose-100 dark:border-rose-900/40">
                                    <span className="text-[10px] uppercase font-bold text-rose-700 dark:text-rose-400">Service Due</span>
                                    <div className="text-xl font-black text-rose-900 dark:text-rose-200 font-mono mt-0.5">
                                        {fleetServiceDue}
                                    </div>
                                    <span className="text-[10px] text-rose-600 dark:text-rose-400">&le; 25 hrs remaining</span>
                                </div>
                            </div>
                        </div>

                        <div className="pt-3 mt-4 border-t border-slate-100 dark:border-slate-800/80 flex items-center justify-between text-xs">
                            <span className="text-slate-400">Total Machinery: <strong className="text-slate-900 dark:text-white font-mono">{fleetTotal}</strong></span>
                            <Link
                                href="/inventory/equipment?action=register"
                                className="text-orange-600 dark:text-orange-400 font-bold hover:underline"
                            >
                                + Register Equipment
                            </Link>
                        </div>
                    </div>
                </div>

                {/* CRITICAL STOCK REORDER ALERTS GRID */}
                {allAlertItems.length > 0 && (
                    <div className="rounded-2xl sm:rounded-3xl border border-rose-200 dark:border-rose-900/60 bg-gradient-to-br from-rose-50/50 via-white to-rose-50/20 dark:from-rose-950/20 dark:via-slate-900 dark:to-slate-900 p-5 sm:p-7 shadow-xs">
                        <div className="flex flex-col sm:flex-row sm:items-center justify-between gap-3 mb-5">
                            <div>
                                <div className="flex items-center gap-2">
                                    <span className="px-2 py-0.5 rounded-full bg-rose-600 text-white text-[10px] font-black uppercase tracking-wider">
                                        Immediate Attention
                                    </span>
                                    <h3 className="text-base sm:text-lg font-bold text-slate-900 dark:text-white">
                                        Critical Stock &amp; Depletion Alerts ({allAlertItems.length})
                                    </h3>
                                </div>
                                <p className="text-xs text-slate-500 dark:text-slate-400 mt-0.5">
                                    Items that have hit zero balance or breached safety replenishment thresholds.
                                </p>
                            </div>

                            <div className="relative">
                                <Search className="w-3.5 h-3.5 absolute left-3 top-1/2 -translate-y-1/2 text-slate-400" />
                                <input
                                    type="text"
                                    placeholder="Search alerts..."
                                    value={itemSearch}
                                    onChange={(e) => setItemSearch(e.target.value)}
                                    className="pl-8 pr-3 py-1.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs text-slate-900 dark:text-white placeholder-slate-400 focus:outline-none focus:ring-1 focus:ring-rose-500"
                                />
                            </div>
                        </div>

                        <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                            {filteredAlerts.slice(0, 6).map((item) => (
                                <div
                                    key={item.id}
                                    className="p-4 rounded-2xl bg-white dark:bg-slate-800/80 border border-slate-200 dark:border-slate-700/70 shadow-xs flex flex-col justify-between gap-3"
                                >
                                    <div>
                                        <div className="flex items-center justify-between gap-2 mb-2">
                                            <span className={`px-2 py-0.5 rounded-md text-[10px] font-bold ${
                                                item.isZero
                                                    ? 'bg-rose-100 dark:bg-rose-950/70 text-rose-700 dark:text-rose-300'
                                                    : 'bg-amber-100 dark:bg-amber-950/70 text-amber-700 dark:text-amber-300'
                                            }`}>
                                                {item.isZero ? 'OUT OF STOCK' : 'LOW STOCK'}
                                            </span>
                                            <span className="text-[10px] text-slate-400 font-mono">
                                                ID: #{item.id}
                                            </span>
                                        </div>

                                        <h4 className="font-bold text-slate-900 dark:text-white text-sm truncate">
                                            {item.name}
                                        </h4>
                                        <div className="text-[11px] text-slate-400 mt-0.5">
                                            {item.category || 'General Materials'}
                                        </div>

                                        <div className="mt-3 flex items-baseline gap-2">
                                            <span className={`text-2xl font-black font-mono ${
                                                item.isZero ? 'text-rose-600 dark:text-rose-400' : 'text-amber-600 dark:text-amber-400'
                                            }`}>
                                                {item.quantity}
                                            </span>
                                            <span className="text-xs text-slate-500 font-semibold">
                                                {item.unit || 'units'} available
                                            </span>
                                        </div>
                                    </div>

                                    <div className="pt-3 border-t border-slate-100 dark:border-slate-700 flex items-center justify-between gap-2">
                                        <span className="text-[10px] text-slate-400">
                                            Buffer: &le; 5 units
                                        </span>
                                        <Link
                                            href={`/inventory/items/${item.id}/edit`}
                                            className="px-2.5 py-1 rounded-lg bg-rose-600 hover:bg-rose-500 text-white font-bold text-[11px] transition-colors"
                                        >
                                            Restock SKU
                                        </Link>
                                    </div>
                                </div>
                            ))}
                        </div>
                    </div>
                )}

                {/* ACTIVE SITE LOANS & GATE PASSES STREAM */}
                <div className="rounded-2xl sm:rounded-3xl border border-slate-100 dark:border-slate-800/80 bg-white dark:bg-slate-900 p-5 sm:p-7 shadow-xs">
                    <div className="flex flex-col sm:flex-row sm:items-center justify-between gap-3 mb-5">
                        <div>
                            <h3 className="text-base sm:text-lg font-bold text-slate-900 dark:text-white flex items-center gap-2">
                                <Repeat className="w-5 h-5 text-indigo-600" />
                                <span>Active Field Material Loans &amp; Gate Passes</span>
                            </h3>
                            <p className="text-xs text-slate-500 dark:text-slate-400 mt-0.5">
                                Track site equipment, materials, and tools out on loan with verified gate pass receipts.
                            </p>
                        </div>
                        <Link
                            href="/inventory/loans"
                            className="text-xs font-bold text-indigo-600 dark:text-indigo-400 hover:underline"
                        >
                            All Gate Passes &rarr;
                        </Link>
                    </div>

                    {activeLoansList.length === 0 ? (
                        <div className="py-8 text-center text-xs text-slate-400">
                            No materials or tools are currently out on site gate passes.
                        </div>
                    ) : (
                        <div className="divide-y divide-slate-100 dark:divide-slate-800/80">
                            {activeLoansList.map((loan) => (
                                <div key={loan.id} className="py-3.5 flex flex-col sm:flex-row sm:items-center justify-between gap-3 text-xs">
                                    <div className="flex items-center gap-3">
                                        <div className="w-9 h-9 rounded-xl bg-indigo-50 dark:bg-indigo-950/50 text-indigo-600 dark:text-indigo-400 flex items-center justify-center shrink-0">
                                            <Repeat className="w-5 h-5" />
                                        </div>
                                        <div>
                                            <div className="font-bold text-slate-900 dark:text-white flex items-center gap-2">
                                                <span>{loan.item?.name || 'Inventory Item'}</span>
                                                <span className="font-mono text-[10px] px-1.5 py-0.2 rounded bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-400 font-bold">
                                                    Qty: {loan.quantity} {loan.item?.unit || 'pcs'}
                                                </span>
                                            </div>
                                            <div className="text-[11px] text-slate-400 mt-0.5 flex flex-wrap items-center gap-2">
                                                <span>Borrower: <strong className="text-slate-700 dark:text-slate-300">{loan.employee ? `${loan.employee.first_name} ${loan.employee.last_name}` : 'Staff'}</strong></span>
                                                <span>·</span>
                                                <span>{loan.employee?.department_rel?.name || 'Operations'}</span>
                                                <span>·</span>
                                                <span>Due: {loan.due_date ? new Date(loan.due_date).toLocaleDateString() : 'Open'}</span>
                                            </div>
                                        </div>
                                    </div>

                                    <div className="flex items-center justify-between sm:justify-end gap-3 shrink-0">
                                        <span className="px-2.5 py-1 rounded-full text-[10px] font-bold bg-indigo-100 dark:bg-indigo-950/60 text-indigo-700 dark:text-indigo-300">
                                            {loan.status ? loan.status.toUpperCase() : 'APPROVED'}
                                        </span>
                                        <a
                                            href={`/inventory/loans/${loan.id}/print`}
                                            target="_blank"
                                            rel="noreferrer"
                                            className="p-2 rounded-xl border border-slate-200 dark:border-slate-700 hover:bg-slate-100 dark:hover:bg-slate-800 text-slate-600 dark:text-slate-300 transition-colors"
                                            title="Print Gate Pass"
                                        >
                                            <Printer className="w-4 h-4" />
                                        </a>
                                    </div>
                                </div>
                            ))}
                        </div>
                    )}
                </div>
            </div>
        </AuthenticatedLayout>
    );
}
