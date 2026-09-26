import { useState, useEffect } from 'react';
import { Head, Link, useForm, router } from '@inertiajs/react';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import {
    FileInput,
    ArrowLeft,
    Save,
    AlertTriangle,
    ShoppingCart,
    Building2,
    Calendar,
    Truck,
    PackageCheck,
    CheckCircle2,
} from 'lucide-react';

export default function Create({
    openOrders = [],
    selectedPo = null,
    suggestedGrnNo = '',
}) {
    const initialItems = selectedPo?.items?.length
        ? selectedPo.items
            .filter((it) => parseFloat(it.quantity) > parseFloat(it.quantity_received))
            .map((it) => {
                const rem = Math.max(0, (parseFloat(it.quantity) || 0) - (parseFloat(it.quantity_received) || 0));
                return {
                    purchase_order_item_id: it.id,
                    inventory_item_id: it.inventory_item_id || '',
                    item_name: it.item_name,
                    unit_of_measurement: it.unit_of_measurement || 'pcs',
                    quantity_delivered: rem,
                    quantity_accepted: rem,
                    quantity_rejected: 0,
                    rejection_reason: '',
                };
            })
        : [];

    const { data, setData, post, processing, errors } = useForm({
        purchase_order_id: selectedPo?.id || '',
        received_date: new Date().toISOString().split('T')[0],
        delivery_note_no: '',
        remarks: '',
        items: initialItems,
    });

    const handlePoChange = (poId) => {
        if (!poId) {
            router.get('/inventory/goods-receiving/create');
            return;
        }
        router.get('/inventory/goods-receiving/create', { po_id: poId }, {
            preserveState: false,
        });
    };

    const updateItem = (index, field, value) => {
        const next = [...data.items];
        next[index][field] = value;

        // Auto-recalculate accepted/rejected if delivered is adjusted
        if (field === 'quantity_delivered') {
            const del = parseFloat(value) || 0;
            const rej = parseFloat(next[index].quantity_rejected) || 0;
            next[index].quantity_accepted = Math.max(0, del - rej);
        } else if (field === 'quantity_rejected') {
            const del = parseFloat(next[index].quantity_delivered) || 0;
            const rej = parseFloat(value) || 0;
            next[index].quantity_accepted = Math.max(0, del - rej);
        }

        setData('items', next);
    };

    const submit = (e) => {
        e.preventDefault();
        post('/inventory/goods-receiving');
    };

    return (
        <AuthenticatedLayout title="Receive Store Delivery" header="Material Procurement">
            <div className="max-w-5xl mx-auto space-y-6">
                {/* Header */}
                <div className="flex items-center justify-between pb-2 border-b border-slate-200 dark:border-slate-800">
                    <div>
                        <h1 className="text-xl sm:text-2xl font-extrabold tracking-tight text-slate-900 dark:text-white flex items-center gap-2">
                            <FileInput className="w-6 h-6 text-emerald-600 dark:text-emerald-400" />
                            Store Receiving Voucher (GRN)
                        </h1>
                        <p className="text-xs sm:text-sm text-slate-500 dark:text-slate-400 mt-0.5">
                            Auto-numbered as <strong className="text-emerald-600 dark:text-emerald-400">{suggestedGrnNo}</strong> • Material Inspection & Stock Credit
                        </p>
                    </div>

                    <Link
                        href="/inventory/goods-receiving"
                        className="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl border border-slate-200 dark:border-slate-800 text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800 font-bold text-xs transition-colors cursor-pointer"
                    >
                        <ArrowLeft className="w-3.5 h-3.5" />
                        <span>Cancel</span>
                    </Link>
                </div>

                {Object.keys(errors).length > 0 && (
                    <div className="p-3.5 rounded-xl bg-rose-50 dark:bg-rose-950/40 text-rose-800 dark:text-rose-300 text-xs font-semibold border border-rose-200 dark:border-rose-800/50 flex items-start gap-2.5">
                        <AlertTriangle className="w-4 h-4 text-rose-600 shrink-0 mt-0.5" />
                        <div>
                            <span className="block font-bold">Please correct the receiving form errors:</span>
                            <ul className="list-disc list-inside text-[11px] font-normal text-rose-700 dark:text-rose-300/90 mt-1 space-y-0.5">
                                {Object.values(errors).map((err, i) => (
                                    <li key={i}>{err}</li>
                                ))}
                            </ul>
                        </div>
                    </div>
                )}

                <form onSubmit={submit} className="space-y-6">
                    {/* Select Purchase Order */}
                    <div className="rounded-2xl border border-emerald-200 dark:border-emerald-900/40 bg-emerald-50/40 dark:bg-emerald-950/20 p-5 space-y-3">
                        <div className="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                            <div>
                                <label className="block text-xs font-bold text-emerald-900 dark:text-emerald-300 uppercase tracking-wider">
                                    Select Purchase Order Awaiting Receipt <span className="text-rose-500">*</span>
                                </label>
                                <p className="text-xs text-emerald-700/80 dark:text-emerald-400">
                                    Choose the open PO to inspect items and verify supplier delivery waybill.
                                </p>
                            </div>

                            <select
                                required
                                value={data.purchase_order_id}
                                onChange={(e) => handlePoChange(e.target.value)}
                                className="px-3.5 py-2 text-xs rounded-xl border border-emerald-300 dark:border-emerald-800 bg-white dark:bg-slate-900 text-slate-800 dark:text-slate-100 font-bold focus:outline-none focus:ring-2 focus:ring-emerald-500"
                            >
                                <option value="">-- Choose Open PO --</option>
                                {openOrders.map((po) => (
                                    <option key={po.id} value={po.id}>
                                        {po.po_no} - {po.vendor?.name} ({po.project?.name || 'Central Store'})
                                    </option>
                                ))}
                            </select>
                        </div>
                    </div>

                    {/* Delivery Waybill Details */}
                    <div className="rounded-2xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 p-6 shadow-xs space-y-4">
                        <h2 className="text-sm font-bold text-slate-800 dark:text-slate-200 uppercase tracking-wider">
                            Delivery & Waybill Information
                        </h2>

                        <div className="grid grid-cols-1 sm:grid-cols-3 gap-4">
                            <div>
                                <label className="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5 uppercase tracking-wider">
                                    Date Received at Store / Site <span className="text-rose-500">*</span>
                                </label>
                                <input
                                    type="date"
                                    required
                                    value={data.received_date}
                                    onChange={(e) => setData('received_date', e.target.value)}
                                    className="w-full px-3 py-2 text-xs rounded-xl border border-slate-200 dark:border-slate-800 bg-slate-50 dark:bg-slate-950 text-slate-800 dark:text-slate-100 focus:outline-none focus:ring-2 focus:ring-emerald-500"
                                />
                            </div>

                            <div>
                                <label className="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5 uppercase tracking-wider">
                                    Supplier Delivery Note / Waybill #
                                </label>
                                <input
                                    type="text"
                                    value={data.delivery_note_no}
                                    onChange={(e) => setData('delivery_note_no', e.target.value)}
                                    placeholder="e.g. WB-99214 / DN-2026-88"
                                    className="w-full px-3 py-2 text-xs rounded-xl border border-slate-200 dark:border-slate-800 bg-slate-50 dark:bg-slate-950 text-slate-800 dark:text-slate-100 focus:outline-none focus:ring-2 focus:ring-emerald-500 font-mono"
                                />
                            </div>

                            <div>
                                <label className="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5 uppercase tracking-wider">
                                    Project Site Destination
                                </label>
                                <input
                                    type="text"
                                    readOnly
                                    value={selectedPo?.project?.name || 'Central Warehouse'}
                                    className="w-full px-3 py-2 text-xs rounded-xl border border-slate-200 dark:border-slate-800 bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 font-semibold cursor-not-allowed"
                                />
                            </div>
                        </div>

                        <div>
                            <label className="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5 uppercase tracking-wider">
                                Storekeeper Inspection Notes / Quality Remarks
                            </label>
                            <input
                                type="text"
                                value={data.remarks}
                                onChange={(e) => setData('remarks', e.target.value)}
                                placeholder="e.g. All materials inspected, stamped test certificate received, unloaded at Central Yard"
                                className="w-full px-3 py-2 text-xs rounded-xl border border-slate-200 dark:border-slate-800 bg-slate-50 dark:bg-slate-950 text-slate-800 dark:text-slate-100 focus:outline-none focus:ring-2 focus:ring-emerald-500"
                            />
                        </div>
                    </div>

                    {/* Inspection Items Table */}
                    <div className="rounded-2xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 p-6 shadow-xs space-y-4">
                        <div className="flex items-center justify-between">
                            <div>
                                <h2 className="text-sm font-bold text-slate-800 dark:text-slate-200 uppercase tracking-wider">
                                    Material Inspection & Acceptance
                                </h2>
                                <p className="text-xs text-slate-500 dark:text-slate-400">
                                    Verify physical quantities. Accepted quantities will automatically increment warehouse inventory.
                                </p>
                            </div>
                        </div>

                        {data.items.length === 0 ? (
                            <div className="p-8 text-center border border-dashed border-slate-200 dark:border-slate-800 rounded-xl text-slate-400">
                                <ShoppingCart className="w-8 h-8 mx-auto mb-2 text-slate-300 dark:text-slate-600" />
                                Please select an open Purchase Order above to load line items awaiting delivery.
                            </div>
                        ) : (
                            <div className="space-y-4">
                                {data.items.map((item, index) => (
                                    <div
                                        key={index}
                                        className="p-4 rounded-xl border border-slate-200 dark:border-slate-800 bg-slate-50/50 dark:bg-slate-800/20 space-y-3"
                                    >
                                        <div className="flex items-center justify-between">
                                            <span className="text-xs font-bold text-slate-800 dark:text-slate-200">
                                                {item.item_name}
                                            </span>
                                            <span className="text-[11px] px-2 py-0.5 rounded bg-slate-200 dark:bg-slate-700 text-slate-700 dark:text-slate-300 font-semibold">
                                                Unit: {item.unit_of_measurement}
                                            </span>
                                        </div>

                                        <div className="grid grid-cols-1 sm:grid-cols-4 gap-3">
                                            <div>
                                                <label className="block text-[11px] font-bold text-slate-600 dark:text-slate-400 mb-1">
                                                    Qty Delivered (Waybill) <span className="text-rose-500">*</span>
                                                </label>
                                                <input
                                                    type="number"
                                                    step="0.01"
                                                    min="0"
                                                    required
                                                    value={item.quantity_delivered}
                                                    onChange={(e) => updateItem(index, 'quantity_delivered', e.target.value)}
                                                    className="w-full px-2.5 py-1.5 text-xs rounded-lg border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-900 font-bold"
                                                />
                                            </div>

                                            <div>
                                                <label className="block text-[11px] font-bold text-emerald-600 dark:text-emerald-400 mb-1">
                                                    Qty Accepted (To Stock) <span className="text-rose-500">*</span>
                                                </label>
                                                <input
                                                    type="number"
                                                    step="0.01"
                                                    min="0"
                                                    required
                                                    value={item.quantity_accepted}
                                                    onChange={(e) => updateItem(index, 'quantity_accepted', e.target.value)}
                                                    className="w-full px-2.5 py-1.5 text-xs rounded-lg border border-emerald-300 dark:border-emerald-700 bg-emerald-50/50 dark:bg-emerald-950/20 font-black text-emerald-700 dark:text-emerald-300"
                                                />
                                            </div>

                                            <div>
                                                <label className="block text-[11px] font-bold text-rose-600 dark:text-rose-400 mb-1">
                                                    Qty Rejected (Damaged)
                                                </label>
                                                <input
                                                    type="number"
                                                    step="0.01"
                                                    min="0"
                                                    value={item.quantity_rejected}
                                                    onChange={(e) => updateItem(index, 'quantity_rejected', e.target.value)}
                                                    className="w-full px-2.5 py-1.5 text-xs rounded-lg border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-900"
                                                />
                                            </div>

                                            <div>
                                                <label className="block text-[11px] font-bold text-slate-600 dark:text-slate-400 mb-1">
                                                    Rejection Reason (If any)
                                                </label>
                                                <input
                                                    type="text"
                                                    value={item.rejection_reason}
                                                    onChange={(e) => updateItem(index, 'rejection_reason', e.target.value)}
                                                    placeholder="Broken bags, expired..."
                                                    className="w-full px-2.5 py-1.5 text-xs rounded-lg border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-900"
                                                />
                                            </div>
                                        </div>
                                    </div>
                                ))}
                            </div>
                        )}
                    </div>

                    {/* Stock Posting Banner */}
                    <div className="p-4 rounded-xl bg-emerald-50 dark:bg-emerald-950/40 border border-emerald-200 dark:border-emerald-900 flex items-center gap-3">
                        <PackageCheck className="w-5 h-5 text-emerald-600 shrink-0" />
                        <div className="text-xs text-emerald-800 dark:text-emerald-200">
                            <strong>Instant Stock Synchronization:</strong> Submitting this Goods Receiving Voucher will automatically increase warehouse stock quantities and update PO fulfillment tracking.
                        </div>
                    </div>

                    {/* Actions */}
                    <div className="flex items-center justify-end gap-3 pt-2">
                        <Link
                            href="/inventory/goods-receiving"
                            className="px-4 py-2.5 rounded-xl border border-slate-200 dark:border-slate-800 text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800 font-bold text-xs transition-colors cursor-pointer"
                        >
                            Cancel
                        </Link>
                        <button
                            type="submit"
                            disabled={processing || data.items.length === 0}
                            className="inline-flex items-center gap-2 px-6 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs shadow-md shadow-emerald-500/25 transition-all cursor-pointer disabled:opacity-50"
                        >
                            <Save className="w-4 h-4" />
                            <span>{processing ? 'Processing Receipt...' : 'Confirm Receipt & Update Stock'}</span>
                        </button>
                    </div>
                </form>
            </div>
        </AuthenticatedLayout>
    );
}
