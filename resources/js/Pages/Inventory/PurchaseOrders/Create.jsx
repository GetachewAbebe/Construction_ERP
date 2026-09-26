import { useState, useEffect } from 'react';
import { Head, Link, useForm, router } from '@inertiajs/react';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import {
    ShoppingCart,
    ArrowLeft,
    Plus,
    Trash2,
    Save,
    AlertTriangle,
    Package,
    Building2,
    Truck,
    Calendar,
    FileText,
} from 'lucide-react';

export default function Create({
    vendors = [],
    projects = [],
    inventoryItems = [],
    approvedRequisitions = [],
    sourceRequisition = null,
    suggestedPoNo = '',
}) {
    const initialItems = sourceRequisition?.items?.length
        ? sourceRequisition.items.map((it) => ({
            inventory_item_id: it.inventory_item_id || '',
            item_name: it.item_name,
            unit_of_measurement: it.unit_of_measurement || 'pcs',
            quantity: parseFloat(it.quantity_approved || it.quantity_requested) || 1,
            unit_price: parseFloat(it.estimated_unit_price) || 0,
        }))
        : [
            {
                inventory_item_id: '',
                item_name: '',
                unit_of_measurement: 'pcs',
                quantity: 1,
                unit_price: 0,
            },
        ];

    const { data, setData, post, processing, errors } = useForm({
        purchase_requisition_id: sourceRequisition?.id || '',
        project_id: sourceRequisition?.project_id || '',
        vendor_id: '',
        order_date: new Date().toISOString().split('T')[0],
        delivery_due_date: '',
        delivery_site: sourceRequisition?.project ? sourceRequisition.project.location : '',
        payment_terms: '',
        tax_rate: 15,
        notes: '',
        items: initialItems,
    });

    const handleRequisitionChange = (prId) => {
        if (!prId) {
            router.get('/inventory/purchase-orders/create');
            return;
        }
        router.get('/inventory/purchase-orders/create', { pr_id: prId }, {
            preserveState: false,
        });
    };

    const handleVendorChange = (venId) => {
        setData('vendor_id', venId);
        const selected = vendors.find((v) => String(v.id) === String(venId));
        if (selected && selected.payment_terms && !data.payment_terms) {
            setData((prev) => ({ ...prev, vendor_id: venId, payment_terms: selected.payment_terms }));
        }
    };

    const addItem = () => {
        setData('items', [
            ...data.items,
            {
                inventory_item_id: '',
                item_name: '',
                unit_of_measurement: 'pcs',
                quantity: 1,
                unit_price: 0,
            },
        ]);
    };

    const removeItem = (index) => {
        if (data.items.length <= 1) return;
        const next = data.items.filter((_, i) => i !== index);
        setData('items', next);
    };

    const updateItem = (index, field, value) => {
        const next = [...data.items];
        next[index][field] = value;

        if (field === 'inventory_item_id' && value) {
            const found = inventoryItems.find((inv) => String(inv.id) === String(value));
            if (found) {
                next[index].item_name = found.name;
                next[index].unit_of_measurement = found.unit_of_measurement || 'pcs';
            }
        }

        setData('items', next);
    };

    // Calculate financials
    const subtotal = data.items.reduce((sum, it) => {
        const qty = parseFloat(it.quantity) || 0;
        const price = parseFloat(it.unit_price) || 0;
        return sum + qty * price;
    }, 0);

    const taxRate = parseFloat(data.tax_rate) || 0;
    const taxAmount = subtotal * (taxRate / 100);
    const grandTotal = subtotal + taxAmount;

    const submit = (e) => {
        e.preventDefault();
        post('/inventory/purchase-orders');
    };

    return (
        <AuthenticatedLayout title="Issue Purchase Order" header="Material Procurement">
            <div className="max-w-5xl mx-auto space-y-6">
                {/* Header */}
                <div className="flex items-center justify-between pb-2 border-b border-slate-200 dark:border-slate-800">
                    <div>
                        <h1 className="text-xl sm:text-2xl font-extrabold tracking-tight text-slate-900 dark:text-white flex items-center gap-2">
                            <ShoppingCart className="w-6 h-6 text-indigo-600 dark:text-indigo-400" />
                            Issue Purchase Order (PO)
                        </h1>
                        <p className="text-xs sm:text-sm text-slate-500 dark:text-slate-400 mt-0.5">
                            Auto-numbered as <strong className="text-indigo-600 dark:text-indigo-400">{suggestedPoNo}</strong> • Official Supply Contract
                        </p>
                    </div>

                    <Link
                        href="/inventory/purchase-orders"
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
                            <span className="block font-bold">Please correct the errors before issuing:</span>
                            <ul className="list-disc list-inside text-[11px] font-normal text-rose-700 dark:text-rose-300/90 mt-1 space-y-0.5">
                                {Object.values(errors).map((err, i) => (
                                    <li key={i}>{err}</li>
                                ))}
                            </ul>
                        </div>
                    </div>
                )}

                <form onSubmit={submit} className="space-y-6">
                    {/* Source Requisition Conversion Card */}
                    <div className="rounded-2xl border border-indigo-200 dark:border-indigo-900/40 bg-indigo-50/40 dark:bg-indigo-950/20 p-5 space-y-3">
                        <div className="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                            <div>
                                <label className="block text-xs font-bold text-indigo-900 dark:text-indigo-300 uppercase tracking-wider">
                                    Convert From Approved Requisition (Optional)
                                </label>
                                <p className="text-xs text-indigo-700/80 dark:text-indigo-400">
                                    Auto-loads approved quantities and project details directly from site requests.
                                </p>
                            </div>

                            <select
                                value={data.purchase_requisition_id}
                                onChange={(e) => handleRequisitionChange(e.target.value)}
                                className="px-3 py-1.5 text-xs rounded-xl border border-indigo-200 dark:border-indigo-800 bg-white dark:bg-slate-900 text-slate-800 dark:text-slate-100 font-semibold focus:outline-none focus:ring-2 focus:ring-indigo-500"
                            >
                                <option value="">-- Direct PO (No Linked PR) --</option>
                                {approvedRequisitions.map((pr) => (
                                    <option key={pr.id} value={pr.id}>
                                        {pr.requisition_no} - {pr.project?.name || 'General Store'} ({pr.required_date})
                                    </option>
                                ))}
                            </select>
                        </div>
                    </div>

                    {/* Order Details Card */}
                    <div className="rounded-2xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 p-6 shadow-xs space-y-4">
                        <h2 className="text-sm font-bold text-slate-800 dark:text-slate-200 uppercase tracking-wider">
                            Vendor & Delivery Terms
                        </h2>

                        <div className="grid grid-cols-1 sm:grid-cols-3 gap-4">
                            <div>
                                <label className="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5 uppercase tracking-wider">
                                    Supplier / Vendor <span className="text-rose-500">*</span>
                                </label>
                                <select
                                    required
                                    value={data.vendor_id}
                                    onChange={(e) => handleVendorChange(e.target.value)}
                                    className="w-full px-3 py-2 text-xs rounded-xl border border-slate-200 dark:border-slate-800 bg-slate-50 dark:bg-slate-950 text-slate-800 dark:text-slate-100 focus:outline-none focus:ring-2 focus:ring-indigo-500 font-bold"
                                >
                                    <option value="">-- Select Vendor --</option>
                                    {vendors.map((v) => (
                                        <option key={v.id} value={v.id}>
                                            {v.name} ({v.code})
                                        </option>
                                    ))}
                                </select>
                            </div>

                            <div>
                                <label className="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5 uppercase tracking-wider">
                                    Project Site Destination
                                </label>
                                <select
                                    value={data.project_id}
                                    onChange={(e) => setData('project_id', e.target.value)}
                                    className="w-full px-3 py-2 text-xs rounded-xl border border-slate-200 dark:border-slate-800 bg-slate-50 dark:bg-slate-950 text-slate-800 dark:text-slate-100 focus:outline-none focus:ring-2 focus:ring-indigo-500"
                                >
                                    <option value="">Central Store / Overhead</option>
                                    {projects.map((p) => (
                                        <option key={p.id} value={p.id}>{p.name} ({p.location || 'Site'})</option>
                                    ))}
                                </select>
                            </div>

                            <div>
                                <label className="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5 uppercase tracking-wider">
                                    Order Date <span className="text-rose-500">*</span>
                                </label>
                                <input
                                    type="date"
                                    required
                                    value={data.order_date}
                                    onChange={(e) => setData('order_date', e.target.value)}
                                    className="w-full px-3 py-2 text-xs rounded-xl border border-slate-200 dark:border-slate-800 bg-slate-50 dark:bg-slate-950 text-slate-800 dark:text-slate-100 focus:outline-none focus:ring-2 focus:ring-indigo-500"
                                />
                            </div>
                        </div>

                        <div className="grid grid-cols-1 sm:grid-cols-3 gap-4 pt-1">
                            <div>
                                <label className="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5 uppercase tracking-wider">
                                    Delivery Due Date
                                </label>
                                <input
                                    type="date"
                                    value={data.delivery_due_date}
                                    onChange={(e) => setData('delivery_due_date', e.target.value)}
                                    className="w-full px-3 py-2 text-xs rounded-xl border border-slate-200 dark:border-slate-800 bg-slate-50 dark:bg-slate-950 text-slate-800 dark:text-slate-100 focus:outline-none focus:ring-2 focus:ring-indigo-500"
                                />
                            </div>

                            <div>
                                <label className="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5 uppercase tracking-wider">
                                    Jobsite Delivery Address / Gate
                                </label>
                                <input
                                    type="text"
                                    value={data.delivery_site}
                                    onChange={(e) => setData('delivery_site', e.target.value)}
                                    placeholder="e.g. Bole Site Main Gate, Addis Ababa"
                                    className="w-full px-3 py-2 text-xs rounded-xl border border-slate-200 dark:border-slate-800 bg-slate-50 dark:bg-slate-950 text-slate-800 dark:text-slate-100 focus:outline-none focus:ring-2 focus:ring-indigo-500"
                                />
                            </div>

                            <div>
                                <label className="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5 uppercase tracking-wider">
                                    Payment Terms
                                </label>
                                <input
                                    type="text"
                                    value={data.payment_terms}
                                    onChange={(e) => setData('payment_terms', e.target.value)}
                                    placeholder="e.g. 100% on delivery, 30 days credit..."
                                    className="w-full px-3 py-2 text-xs rounded-xl border border-slate-200 dark:border-slate-800 bg-slate-50 dark:bg-slate-950 text-slate-800 dark:text-slate-100 focus:outline-none focus:ring-2 focus:ring-indigo-500"
                                />
                            </div>
                        </div>
                    </div>

                    {/* Order Line Items */}
                    <div className="rounded-2xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 p-6 shadow-xs space-y-4">
                        <div className="flex items-center justify-between">
                            <div>
                                <h2 className="text-sm font-bold text-slate-800 dark:text-slate-200 uppercase tracking-wider">
                                    Purchase Order Items
                                </h2>
                                <p className="text-xs text-slate-500 dark:text-slate-400">
                                    Specify agreed supplier unit rates for each required material.
                                </p>
                            </div>

                            <button
                                type="button"
                                onClick={addItem}
                                className="inline-flex items-center gap-1.5 px-3 py-1.5 bg-indigo-50 dark:bg-indigo-950/60 hover:bg-indigo-100 text-indigo-700 dark:text-indigo-300 rounded-xl text-xs font-bold transition-colors cursor-pointer border border-indigo-200 dark:border-indigo-800"
                            >
                                <Plus className="w-3.5 h-3.5" />
                                <span>Add Material</span>
                            </button>
                        </div>

                        <div className="space-y-3">
                            {data.items.map((item, index) => {
                                const lineTotal = (parseFloat(item.quantity) || 0) * (parseFloat(item.unit_price) || 0);
                                return (
                                    <div
                                        key={index}
                                        className="p-4 rounded-xl border border-slate-200 dark:border-slate-800 bg-slate-50/50 dark:bg-slate-800/20 space-y-3"
                                    >
                                        <div className="flex items-center justify-between">
                                            <span className="text-xs font-bold text-slate-700 dark:text-slate-300">
                                                Item #{index + 1}
                                            </span>
                                            {data.items.length > 1 && (
                                                <button
                                                    type="button"
                                                    onClick={() => removeItem(index)}
                                                    className="text-rose-500 hover:text-rose-700 text-xs font-semibold inline-flex items-center gap-1 cursor-pointer"
                                                >
                                                    <Trash2 className="w-3.5 h-3.5" />
                                                    <span>Remove</span>
                                                </button>
                                            )}
                                        </div>

                                        <div className="grid grid-cols-1 sm:grid-cols-12 gap-3">
                                            {/* Link Existing Stock Item */}
                                            <div className="sm:col-span-3">
                                                <label className="block text-[11px] font-bold text-slate-600 dark:text-slate-400 mb-1">
                                                    Link Warehouse SKU
                                                </label>
                                                <select
                                                    value={item.inventory_item_id}
                                                    onChange={(e) => updateItem(index, 'inventory_item_id', e.target.value)}
                                                    className="w-full px-2.5 py-1.5 text-xs rounded-lg border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-900 text-slate-800 dark:text-slate-100"
                                                >
                                                    <option value="">-- Custom Material --</option>
                                                    {inventoryItems.map((inv) => (
                                                        <option key={inv.id} value={inv.id}>
                                                            {inv.name} ({inv.item_no})
                                                        </option>
                                                    ))}
                                                </select>
                                            </div>

                                            {/* Item Name */}
                                            <div className="sm:col-span-3">
                                                <label className="block text-[11px] font-bold text-slate-600 dark:text-slate-400 mb-1">
                                                    Material Name / Spec <span className="text-rose-500">*</span>
                                                </label>
                                                <input
                                                    type="text"
                                                    required
                                                    value={item.item_name}
                                                    onChange={(e) => updateItem(index, 'item_name', e.target.value)}
                                                    placeholder="e.g. Reinforcement Bar Ø16mm"
                                                    className="w-full px-2.5 py-1.5 text-xs rounded-lg border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-900 text-slate-800 dark:text-slate-100"
                                                />
                                            </div>

                                            {/* Unit */}
                                            <div className="sm:col-span-2">
                                                <label className="block text-[11px] font-bold text-slate-600 dark:text-slate-400 mb-1">
                                                    Unit <span className="text-rose-500">*</span>
                                                </label>
                                                <input
                                                    type="text"
                                                    required
                                                    value={item.unit_of_measurement}
                                                    onChange={(e) => updateItem(index, 'unit_of_measurement', e.target.value)}
                                                    placeholder="pcs, m3, tons..."
                                                    className="w-full px-2.5 py-1.5 text-xs rounded-lg border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-900 text-slate-800 dark:text-slate-100"
                                                />
                                            </div>

                                            {/* Quantity */}
                                            <div className="sm:col-span-2">
                                                <label className="block text-[11px] font-bold text-slate-600 dark:text-slate-400 mb-1">
                                                    Quantity <span className="text-rose-500">*</span>
                                                </label>
                                                <input
                                                    type="number"
                                                    step="0.01"
                                                    min="0.01"
                                                    required
                                                    value={item.quantity}
                                                    onChange={(e) => updateItem(index, 'quantity', e.target.value)}
                                                    className="w-full px-2.5 py-1.5 text-xs rounded-lg border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-900 text-slate-800 dark:text-slate-100 font-bold"
                                                />
                                            </div>

                                            {/* Unit Price */}
                                            <div className="sm:col-span-2">
                                                <label className="block text-[11px] font-bold text-slate-600 dark:text-slate-400 mb-1">
                                                    Unit Price (ETB) <span className="text-rose-500">*</span>
                                                </label>
                                                <input
                                                    type="number"
                                                    step="0.01"
                                                    min="0"
                                                    required
                                                    value={item.unit_price}
                                                    onChange={(e) => updateItem(index, 'unit_price', e.target.value)}
                                                    placeholder="0.00"
                                                    className="w-full px-2.5 py-1.5 text-xs rounded-lg border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-900 text-slate-800 dark:text-slate-100 font-bold text-right"
                                                />
                                            </div>
                                        </div>

                                        <div className="flex justify-end pt-1">
                                            <div className="text-right">
                                                <span className="text-[10px] uppercase font-bold text-slate-400 mr-2">Line Total:</span>
                                                <span className="text-xs font-black text-slate-900 dark:text-white">
                                                    {lineTotal.toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 })} ETB
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                );
                            })}
                        </div>

                        {/* Financial Totals Card */}
                        <div className="p-4 rounded-xl bg-slate-50 dark:bg-slate-800/40 border border-slate-200 dark:border-slate-700/60 max-w-sm ml-auto space-y-2 text-xs">
                            <div className="flex justify-between">
                                <span className="text-slate-500">Subtotal:</span>
                                <span className="font-bold text-slate-800 dark:text-slate-200">
                                    {subtotal.toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 })} ETB
                                </span>
                            </div>

                            <div className="flex items-center justify-between">
                                <span className="text-slate-500 flex items-center gap-1">
                                    VAT Rate (%):
                                    <input
                                        type="number"
                                        min="0"
                                        max="100"
                                        step="0.5"
                                        value={data.tax_rate}
                                        onChange={(e) => setData('tax_rate', e.target.value)}
                                        className="w-14 px-1.5 py-0.5 text-[11px] rounded border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-900 text-right"
                                    />
                                </span>
                                <span className="font-bold text-slate-800 dark:text-slate-200">
                                    {taxAmount.toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 })} ETB
                                </span>
                            </div>

                            <div className="border-t border-slate-200 dark:border-slate-700 pt-2 flex justify-between items-center">
                                <span className="text-sm font-extrabold text-indigo-900 dark:text-indigo-300 uppercase">
                                    Total PO Value:
                                </span>
                                <span className="text-base font-black text-indigo-600 dark:text-indigo-400">
                                    {grandTotal.toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 })} ETB
                                </span>
                            </div>
                        </div>
                    </div>

                    {/* Notes */}
                    <div className="rounded-2xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 p-6 space-y-2">
                        <label className="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider">
                            Special Instructions / Quality Specifications
                        </label>
                        <textarea
                            rows={3}
                            value={data.notes}
                            onChange={(e) => setData('notes', e.target.value)}
                            placeholder="e.g. Delivery must be between 8:00 AM - 5:00 PM. Quality test certificates must be handed to Site Storekeeper."
                            className="w-full px-3 py-2 text-xs rounded-xl border border-slate-200 dark:border-slate-800 bg-slate-50 dark:bg-slate-950 text-slate-800 dark:text-slate-100 focus:outline-none focus:ring-2 focus:ring-indigo-500"
                        />
                    </div>

                    {/* Actions */}
                    <div className="flex items-center justify-end gap-3 pt-2">
                        <Link
                            href="/inventory/purchase-orders"
                            className="px-4 py-2.5 rounded-xl border border-slate-200 dark:border-slate-800 text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800 font-bold text-xs transition-colors cursor-pointer"
                        >
                            Cancel
                        </Link>
                        <button
                            type="submit"
                            disabled={processing}
                            className="inline-flex items-center gap-2 px-6 py-2.5 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-xs shadow-md shadow-indigo-500/25 transition-all cursor-pointer disabled:opacity-50"
                        >
                            <Save className="w-4 h-4" />
                            <span>{processing ? 'Issuing PO...' : 'Issue Purchase Order'}</span>
                        </button>
                    </div>
                </form>
            </div>
        </AuthenticatedLayout>
    );
}
