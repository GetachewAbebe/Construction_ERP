import { useState } from 'react';
import { Head, Link, router } from '@inertiajs/react';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import {
    ShoppingCart,
    ArrowLeft,
    CheckCircle2,
    Clock,
    XCircle,
    Printer,
    FileInput,
    Truck,
    Building2,
    Calendar,
    FileText,
    QrCode,
} from 'lucide-react';

export default function Show({ order }) {
    const handleCancel = () => {
        if (confirm(`Are you sure you want to cancel purchase order ${order.po_no}?`)) {
            router.post(`/inventory/purchase-orders/${order.id}/cancel`);
        }
    };

    const getStatusBadge = (st) => {
        switch (st) {
            case 'received':
                return <span className="inline-flex items-center gap-1 px-3 py-1 rounded-full text-xs font-bold bg-emerald-50 dark:bg-emerald-950/60 text-emerald-700 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-800"><CheckCircle2 className="w-3.5 h-3.5" /> Fully Received</span>;
            case 'partially_received':
                return <span className="inline-flex items-center gap-1 px-3 py-1 rounded-full text-xs font-bold bg-amber-50 dark:bg-amber-950/60 text-amber-700 dark:text-amber-300 border border-amber-200 dark:border-amber-800"><Clock className="w-3.5 h-3.5" /> Partial Deliveries</span>;
            case 'issued':
                return <span className="inline-flex items-center gap-1 px-3 py-1 rounded-full text-xs font-bold bg-blue-50 dark:bg-blue-950/60 text-blue-700 dark:text-blue-300 border border-blue-200 dark:border-blue-800"><ShoppingCart className="w-3.5 h-3.5" /> Issued</span>;
            case 'cancelled':
                return <span className="inline-flex items-center gap-1 px-3 py-1 rounded-full text-xs font-bold bg-rose-50 dark:bg-rose-950/60 text-rose-700 dark:text-rose-300 border border-rose-200 dark:border-rose-800"><XCircle className="w-3.5 h-3.5" /> Cancelled</span>;
            default:
                return <span className="px-3 py-1 rounded-full text-xs font-bold bg-slate-100 dark:bg-slate-800 text-slate-700">{st}</span>;
        }
    };

    const totalQtyOrdered = (order.items || []).reduce((sum, it) => sum + (parseFloat(it.quantity) || 0), 0);
    const totalQtyReceived = (order.items || []).reduce((sum, it) => sum + (parseFloat(it.quantity_received) || 0), 0);
    const fulfillmentPct = totalQtyOrdered > 0 ? Math.min(100, Math.round((totalQtyReceived / totalQtyOrdered) * 100)) : 0;

    return (
        <AuthenticatedLayout title={`Purchase Order ${order.po_no}`} header="Material Procurement">
            <div className="max-w-5xl mx-auto space-y-6">
                {/* Header */}
                <div className="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-2 border-b border-slate-200 dark:border-slate-800">
                    <div>
                        <div className="flex items-center gap-3">
                            <h1 className="text-xl sm:text-2xl font-black tracking-tight text-slate-900 dark:text-white">
                                {order.po_no}
                            </h1>
                            {getStatusBadge(order.status)}
                        </div>
                        <p className="text-xs sm:text-sm text-slate-500 dark:text-slate-400 mt-1">
                            Issued to <strong>{order.vendor?.name}</strong> on {order.order_date ? new Date(order.order_date).toLocaleDateString() : 'N/A'}
                        </p>
                    </div>

                    <div className="flex items-center gap-2">
                        <Link
                            href="/inventory/purchase-orders"
                            className="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl border border-slate-200 dark:border-slate-800 text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800 font-bold text-xs transition-colors"
                        >
                            <ArrowLeft className="w-3.5 h-3.5" />
                            <span>Back</span>
                        </Link>

                        <a
                            href={`/inventory/purchase-orders/${order.id}/print`}
                            target="_blank"
                            rel="noreferrer"
                            className="inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-xl border border-slate-200 dark:border-slate-800 text-slate-700 dark:text-slate-200 hover:bg-slate-100 dark:hover:bg-slate-800 text-xs font-bold transition-colors cursor-pointer"
                        >
                            <Printer className="w-3.5 h-3.5" />
                            <span>Print PO / PDF</span>
                        </a>

                        {order.status !== 'received' && order.status !== 'cancelled' && (
                            <>
                                <Link
                                    href={`/inventory/goods-receiving/create?po_id=${order.id}`}
                                    className="inline-flex items-center gap-1.5 px-4 py-1.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold shadow-md shadow-emerald-500/20 transition-all cursor-pointer"
                                >
                                    <FileInput className="w-3.5 h-3.5" />
                                    <span>Receive Delivery (GRN)</span>
                                </Link>

                                <button
                                    type="button"
                                    onClick={handleCancel}
                                    className="p-1.5 rounded-xl border border-rose-200 dark:border-rose-900/60 text-rose-600 dark:text-rose-400 hover:bg-rose-50 dark:hover:bg-rose-950/40 text-xs font-bold transition-colors cursor-pointer"
                                    title="Cancel Purchase Order"
                                >
                                    <XCircle className="w-4 h-4" />
                                </button>
                            </>
                        )}
                    </div>
                </div>

                {/* Fulfillment Progress Bar */}
                <div className="rounded-2xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 p-4">
                    <div className="flex items-center justify-between text-xs font-bold mb-1.5">
                        <span className="text-slate-600 dark:text-slate-300">Material Fulfillment Progress</span>
                        <span className="text-emerald-600 dark:text-emerald-400">{fulfillmentPct}% Delivered</span>
                    </div>
                    <div className="w-full h-2.5 rounded-full bg-slate-100 dark:bg-slate-800 overflow-hidden">
                        <div
                            className="h-full bg-emerald-500 rounded-full transition-all duration-500"
                            style={{ width: `${fulfillmentPct}%` }}
                        />
                    </div>
                    <div className="flex justify-between text-[11px] text-slate-400 mt-1">
                        <span>{totalQtyReceived.toFixed(2)} received</span>
                        <span>{totalQtyOrdered.toFixed(2)} total ordered</span>
                    </div>
                </div>

                {/* Details Grid */}
                <div className="grid grid-cols-1 sm:grid-cols-4 gap-4">
                    <div className="rounded-2xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 p-4">
                        <div className="text-[11px] font-bold text-slate-400 uppercase tracking-wider">Vendor / Supplier</div>
                        <div className="text-sm font-bold text-slate-800 dark:text-slate-100 mt-1">
                            {order.vendor?.name}
                        </div>
                        <div className="text-[11px] text-slate-500">{order.vendor?.code} • {order.vendor?.phone || 'No phone'}</div>
                    </div>

                    <div className="rounded-2xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 p-4">
                        <div className="text-[11px] font-bold text-slate-400 uppercase tracking-wider">Project Destination</div>
                        <div className="text-sm font-bold text-slate-800 dark:text-slate-100 mt-1">
                            {order.project ? order.project.name : 'Central Warehouse / Overhead'}
                        </div>
                        <div className="text-[11px] text-slate-500">{order.delivery_site || 'Site Delivery'}</div>
                    </div>

                    <div className="rounded-2xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 p-4">
                        <div className="text-[11px] font-bold text-slate-400 uppercase tracking-wider">Payment Terms</div>
                        <div className="text-sm font-bold text-slate-800 dark:text-slate-100 mt-1">
                            {order.payment_terms || 'Standard Terms'}
                        </div>
                        <div className="text-[11px] text-slate-500">
                            Due: {order.delivery_due_date ? new Date(order.delivery_due_date).toLocaleDateString() : 'N/A'}
                        </div>
                    </div>

                    <div className="rounded-2xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 p-4">
                        <div className="text-[11px] font-bold text-slate-400 uppercase tracking-wider">Total Value (Inc. VAT)</div>
                        <div className="text-lg font-black text-indigo-600 dark:text-indigo-400 mt-1">
                            {Number(order.total_amount).toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 })} ETB
                        </div>
                    </div>
                </div>

                {order.requisition && (
                    <div className="rounded-2xl border border-indigo-100 dark:border-indigo-950 bg-indigo-50/50 dark:bg-indigo-950/20 p-4 flex items-center justify-between text-xs">
                        <div className="flex items-center gap-2 text-indigo-900 dark:text-indigo-300 font-semibold">
                            <FileText className="w-4 h-4 text-indigo-600" />
                            <span>Linked to Material Requisition: <strong>{order.requisition.requisition_no}</strong></span>
                        </div>
                        <Link href={`/inventory/requisitions/${order.requisition.id}`} className="text-indigo-600 dark:text-indigo-400 font-bold hover:underline">
                            View Requisition →
                        </Link>
                    </div>
                )}

                {/* Line Items Table */}
                <div className="rounded-2xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 overflow-hidden shadow-xs">
                    <div className="p-4 border-b border-slate-200 dark:border-slate-800 flex items-center justify-between">
                        <h2 className="text-sm font-bold text-slate-800 dark:text-slate-200 uppercase tracking-wider">
                            Order Line Items ({order.items?.length || 0})
                        </h2>
                    </div>

                    <div className="overflow-x-auto">
                        <table className="w-full text-left border-collapse text-xs">
                            <thead>
                                <tr className="border-b border-slate-200 dark:border-slate-800 bg-slate-50/75 dark:bg-slate-800/40 text-slate-500 dark:text-slate-400 font-bold uppercase tracking-wider text-[11px]">
                                    <th className="py-3 px-4">#</th>
                                    <th className="py-3 px-4">Material Description</th>
                                    <th className="py-3 px-4 text-right">Qty Ordered</th>
                                    <th className="py-3 px-4 text-right">Qty Received</th>
                                    <th className="py-3 px-4 text-right">Remaining</th>
                                    <th className="py-3 px-4 text-right">Unit Price</th>
                                    <th className="py-3 px-4 text-right">Total Price</th>
                                </tr>
                            </thead>
                            <tbody className="divide-y divide-slate-100 dark:divide-slate-800/60">
                                {(order.items || []).map((it, idx) => {
                                    const remaining = Math.max(0, (parseFloat(it.quantity) || 0) - (parseFloat(it.quantity_received) || 0));
                                    return (
                                        <tr key={it.id} className="hover:bg-slate-50/50 dark:hover:bg-slate-800/30">
                                            <td className="py-3 px-4 font-bold text-slate-400">{idx + 1}</td>
                                            <td className="py-3 px-4 font-bold text-slate-900 dark:text-white">
                                                {it.item_name}
                                                {it.inventory_item && (
                                                    <div className="text-[10px] text-blue-500 font-normal">Catalog: {it.inventory_item.item_no}</div>
                                                )}
                                            </td>
                                            <td className="py-3 px-4 text-right font-semibold text-slate-700 dark:text-slate-300">
                                                {it.quantity} {it.unit_of_measurement}
                                            </td>
                                            <td className="py-3 px-4 text-right font-bold text-emerald-600 dark:text-emerald-400">
                                                {it.quantity_received} {it.unit_of_measurement}
                                            </td>
                                            <td className="py-3 px-4 text-right font-bold text-amber-600 dark:text-amber-400">
                                                {remaining.toFixed(2)} {it.unit_of_measurement}
                                            </td>
                                            <td className="py-3 px-4 text-right text-slate-600 dark:text-slate-300">
                                                {Number(it.unit_price).toFixed(2)} ETB
                                            </td>
                                            <td className="py-3 px-4 text-right font-bold text-slate-900 dark:text-white">
                                                {Number(it.total_price).toFixed(2)} ETB
                                            </td>
                                        </tr>
                                    );
                                })}
                            </tbody>
                        </table>
                    </div>

                    <div className="p-4 bg-slate-50/50 dark:bg-slate-800/20 border-t border-slate-200 dark:border-slate-800 flex justify-end">
                        <div className="w-64 space-y-1.5 text-xs text-right">
                            <div className="flex justify-between text-slate-500">
                                <span>Subtotal:</span>
                                <span className="font-semibold text-slate-800 dark:text-slate-200">{Number(order.subtotal).toFixed(2)} ETB</span>
                            </div>
                            <div className="flex justify-between text-slate-500">
                                <span>VAT ({order.tax_rate}%):</span>
                                <span className="font-semibold text-slate-800 dark:text-slate-200">{Number(order.tax_amount).toFixed(2)} ETB</span>
                            </div>
                            <div className="flex justify-between text-sm font-black text-indigo-600 dark:text-indigo-400 pt-1 border-t border-slate-200 dark:border-slate-700">
                                <span>Grand Total:</span>
                                <span>{Number(order.total_amount).toFixed(2)} ETB</span>
                            </div>
                        </div>
                    </div>
                </div>

                {/* Linked Goods Receiving Notes (GRNs) */}
                {order.goods_receiving_notes && order.goods_receiving_notes.length > 0 && (
                    <div className="rounded-2xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 p-6 space-y-3">
                        <h2 className="text-sm font-bold text-slate-800 dark:text-slate-200 uppercase tracking-wider flex items-center gap-2">
                            <FileInput className="w-4 h-4 text-emerald-600" />
                            Store Receiving Vouchers (GRN Deliveries)
                        </h2>
                        <div className="space-y-2">
                            {order.goods_receiving_notes.map((grn) => (
                                <div key={grn.id} className="p-3.5 rounded-xl border border-slate-200 dark:border-slate-800 flex items-center justify-between text-xs">
                                    <div>
                                        <Link href={`/inventory/goods-receiving/${grn.id}`} className="font-bold text-emerald-600 hover:underline">
                                            {grn.grn_no}
                                        </Link>
                                        <span className="text-slate-500 ml-2">
                                            Received on {grn.received_date ? new Date(grn.received_date).toLocaleDateString() : 'N/A'} by {grn.receiver?.name || 'Storekeeper'}
                                        </span>
                                    </div>
                                    <div className="flex items-center gap-3">
                                        <span className="text-[11px] text-slate-400">Waybill: {grn.delivery_note_no || 'N/A'}</span>
                                        <a
                                            href={`/inventory/goods-receiving/${grn.id}/print`}
                                            target="_blank"
                                            rel="noreferrer"
                                            className="p-1 text-slate-500 hover:text-slate-800"
                                            title="Print GRN"
                                        >
                                            <Printer className="w-3.5 h-3.5" />
                                        </a>
                                    </div>
                                </div>
                            ))}
                        </div>
                    </div>
                )}
            </div>
        </AuthenticatedLayout>
    );
}
