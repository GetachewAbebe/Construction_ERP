import { useState } from 'react';
import { Head, Link } from '@inertiajs/react';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import {
    FileInput,
    ArrowLeft,
    CheckCircle2,
    Printer,
    Building2,
    Truck,
    Calendar,
    ShoppingCart,
    PackageCheck,
    AlertOctagon,
} from 'lucide-react';

export default function Show({ note }) {
    const totalDelivered = (note.items || []).reduce((sum, it) => sum + (parseFloat(it.quantity_delivered) || 0), 0);
    const totalAccepted = (note.items || []).reduce((sum, it) => sum + (parseFloat(it.quantity_accepted) || 0), 0);
    const totalRejected = (note.items || []).reduce((sum, it) => sum + (parseFloat(it.quantity_rejected) || 0), 0);

    return (
        <AuthenticatedLayout title={`Store Receiving Voucher ${note.grn_no}`} header="Material Procurement">
            <div className="max-w-5xl mx-auto space-y-6">
                {/* Header */}
                <div className="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-2 border-b border-slate-200 dark:border-slate-800">
                    <div>
                        <div className="flex items-center gap-3">
                            <h1 className="text-xl sm:text-2xl font-black tracking-tight text-slate-900 dark:text-white">
                                {note.grn_no}
                            </h1>
                            <span className="inline-flex items-center gap-1 px-3 py-1 rounded-full text-xs font-bold bg-emerald-50 dark:bg-emerald-950/60 text-emerald-700 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-800">
                                <CheckCircle2 className="w-3.5 h-3.5" /> Stock Credited
                            </span>
                        </div>
                        <p className="text-xs sm:text-sm text-slate-500 dark:text-slate-400 mt-1">
                            Received on {note.received_date ? new Date(note.received_date).toLocaleDateString() : 'N/A'} by <strong>{note.receiver?.name || 'Storekeeper'}</strong>
                        </p>
                    </div>

                    <div className="flex items-center gap-2">
                        <Link
                            href="/inventory/goods-receiving"
                            className="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl border border-slate-200 dark:border-slate-800 text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800 font-bold text-xs transition-colors"
                        >
                            <ArrowLeft className="w-3.5 h-3.5" />
                            <span>Back</span>
                        </Link>

                        <a
                            href={`/inventory/goods-receiving/${note.id}/print`}
                            target="_blank"
                            rel="noreferrer"
                            className="inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-xl border border-slate-200 dark:border-slate-800 text-slate-700 dark:text-slate-200 hover:bg-slate-100 dark:hover:bg-slate-800 text-xs font-bold transition-colors cursor-pointer"
                        >
                            <Printer className="w-3.5 h-3.5" />
                            <span>Print Voucher / PDF</span>
                        </a>
                    </div>
                </div>

                {/* Metrics */}
                <div className="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <div className="rounded-2xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 p-4">
                        <div className="text-[11px] font-bold text-slate-400 uppercase tracking-wider">Total Quantity Delivered</div>
                        <div className="text-2xl font-black text-slate-900 dark:text-white mt-1">
                            {totalDelivered.toFixed(2)}
                        </div>
                    </div>

                    <div className="rounded-2xl border border-emerald-200 dark:border-emerald-900/40 bg-emerald-50/50 dark:bg-emerald-950/20 p-4">
                        <div className="text-[11px] font-bold text-emerald-600 dark:text-emerald-400 uppercase tracking-wider flex items-center gap-1">
                            <PackageCheck className="w-3.5 h-3.5" /> Accepted to Warehouse
                        </div>
                        <div className="text-2xl font-black text-emerald-700 dark:text-emerald-300 mt-1">
                            {totalAccepted.toFixed(2)}
                        </div>
                    </div>

                    <div className="rounded-2xl border border-rose-200 dark:border-rose-900/40 bg-rose-50/50 dark:bg-rose-950/20 p-4">
                        <div className="text-[11px] font-bold text-rose-600 dark:text-rose-400 uppercase tracking-wider flex items-center gap-1">
                            <AlertOctagon className="w-3.5 h-3.5" /> Rejected / Damaged
                        </div>
                        <div className="text-2xl font-black text-rose-700 dark:text-rose-300 mt-1">
                            {totalRejected.toFixed(2)}
                        </div>
                    </div>
                </div>

                {/* Information Grid */}
                <div className="grid grid-cols-1 sm:grid-cols-4 gap-4">
                    <div className="rounded-2xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 p-4">
                        <div className="text-[11px] font-bold text-slate-400 uppercase tracking-wider">Purchase Order</div>
                        <div className="text-sm font-bold text-blue-600 dark:text-blue-400 mt-1">
                            {note.purchase_order ? (
                                <Link href={`/inventory/purchase-orders/${note.purchase_order.id}`} className="hover:underline">
                                    {note.purchase_order.po_no}
                                </Link>
                            ) : 'N/A'}
                        </div>
                    </div>

                    <div className="rounded-2xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 p-4">
                        <div className="text-[11px] font-bold text-slate-400 uppercase tracking-wider">Delivering Supplier</div>
                        <div className="text-sm font-bold text-slate-800 dark:text-slate-100 mt-1">
                            {note.vendor?.name}
                        </div>
                        <div className="text-[11px] text-slate-500">{note.vendor?.code}</div>
                    </div>

                    <div className="rounded-2xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 p-4">
                        <div className="text-[11px] font-bold text-slate-400 uppercase tracking-wider">Project Store Destination</div>
                        <div className="text-sm font-bold text-slate-800 dark:text-slate-100 mt-1">
                            {note.project ? note.project.name : 'Central Warehouse'}
                        </div>
                    </div>

                    <div className="rounded-2xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 p-4">
                        <div className="text-[11px] font-bold text-slate-400 uppercase tracking-wider">Vendor Delivery Waybill #</div>
                        <div className="text-sm font-mono font-bold text-slate-800 dark:text-slate-100 mt-1">
                            {note.delivery_note_no || 'None Provided'}
                        </div>
                    </div>
                </div>

                {note.remarks && (
                    <div className="rounded-2xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 p-4">
                        <div className="text-xs font-bold text-slate-500 uppercase tracking-wider">Storekeeper Remarks:</div>
                        <div className="text-xs text-slate-800 dark:text-slate-200 mt-1 font-medium">{note.remarks}</div>
                    </div>
                )}

                {/* Received Line Items Table */}
                <div className="rounded-2xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 overflow-hidden shadow-xs">
                    <div className="p-4 border-b border-slate-200 dark:border-slate-800 flex items-center justify-between">
                        <h2 className="text-sm font-bold text-slate-800 dark:text-slate-200 uppercase tracking-wider">
                            Inspected Items ({note.items?.length || 0})
                        </h2>
                    </div>

                    <div className="overflow-x-auto">
                        <table className="w-full text-left border-collapse text-xs">
                            <thead>
                                <tr className="border-b border-slate-200 dark:border-slate-800 bg-slate-50/75 dark:bg-slate-800/40 text-slate-500 dark:text-slate-400 font-bold uppercase tracking-wider text-[11px]">
                                    <th className="py-3 px-4">#</th>
                                    <th className="py-3 px-4">Material Description</th>
                                    <th className="py-3 px-4 text-right">Qty Delivered</th>
                                    <th className="py-3 px-4 text-right">Qty Accepted</th>
                                    <th className="py-3 px-4 text-right">Qty Rejected</th>
                                    <th className="py-3 px-4">Rejection Notes</th>
                                </tr>
                            </thead>
                            <tbody className="divide-y divide-slate-100 dark:divide-slate-800/60">
                                {(note.items || []).map((it, idx) => (
                                    <tr key={it.id} className="hover:bg-slate-50/50 dark:hover:bg-slate-800/30">
                                        <td className="py-3 px-4 font-bold text-slate-400">{idx + 1}</td>
                                        <td className="py-3 px-4">
                                            <div className="font-bold text-slate-900 dark:text-white">{it.item_name}</div>
                                            {it.inventory_item && (
                                                <div className="text-[10px] text-emerald-600 dark:text-emerald-400 font-semibold">
                                                    Stock Catalog: {it.inventory_item.item_no}
                                                </div>
                                            )}
                                        </td>
                                        <td className="py-3 px-4 text-right font-medium text-slate-700 dark:text-slate-300">
                                            {it.quantity_delivered} {it.unit_of_measurement}
                                        </td>
                                        <td className="py-3 px-4 text-right font-black text-emerald-600 dark:text-emerald-400">
                                            {it.quantity_accepted} {it.unit_of_measurement}
                                        </td>
                                        <td className="py-3 px-4 text-right font-bold text-rose-600 dark:text-rose-400">
                                            {parseFloat(it.quantity_rejected) > 0 ? `${it.quantity_rejected} ${it.unit_of_measurement}` : '-'}
                                        </td>
                                        <td className="py-3 px-4 text-slate-500 dark:text-slate-400 italic">
                                            {it.rejection_reason || '-'}
                                        </td>
                                    </tr>
                                ))}
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </AuthenticatedLayout>
    );
}
