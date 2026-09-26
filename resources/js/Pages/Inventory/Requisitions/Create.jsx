import { useState } from 'react';
import { Head, Link, useForm } from '@inertiajs/react';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import {
    ClipboardList,
    ArrowLeft,
    Plus,
    Trash2,
    Save,
    AlertTriangle,
    Package,
    Building2,
    Calendar,
} from 'lucide-react';

export default function Create({ projects = [], inventoryItems = [], suggestedRequisitionNo = '' }) {
    const { data, setData, post, processing, errors } = useForm({
        project_id: '',
        required_date: new Date().toISOString().split('T')[0],
        priority: 'medium',
        purpose: '',
        remarks: '',
        items: [
            {
                inventory_item_id: '',
                item_name: '',
                unit_of_measurement: 'pcs',
                quantity_requested: 1,
                estimated_unit_price: 0,
                specifications: '',
            },
        ],
    });

    const addItem = () => {
        setData('items', [
            ...data.items,
            {
                inventory_item_id: '',
                item_name: '',
                unit_of_measurement: 'pcs',
                quantity_requested: 1,
                estimated_unit_price: 0,
                specifications: '',
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

        // If choosing an existing catalog item, auto-fill unit & name
        if (field === 'inventory_item_id' && value) {
            const found = inventoryItems.find((inv) => String(inv.id) === String(value));
            if (found) {
                next[index].item_name = found.name;
                next[index].unit_of_measurement = found.unit_of_measurement || 'pcs';
            }
        }

        setData('items', next);
    };

    const totalEstimated = data.items.reduce((sum, item) => {
        const qty = parseFloat(item.quantity_requested) || 0;
        const price = parseFloat(item.estimated_unit_price) || 0;
        return sum + qty * price;
    }, 0);

    const submit = (e) => {
        e.preventDefault();
        post('/inventory/requisitions');
    };

    return (
        <AuthenticatedLayout title="New Material Requisition" header="Material Procurement">
            <div className="max-w-5xl mx-auto space-y-6">
                {/* Header */}
                <div className="flex items-center justify-between pb-2 border-b border-slate-200 dark:border-slate-800">
                    <div>
                        <h1 className="text-xl sm:text-2xl font-extrabold tracking-tight text-slate-900 dark:text-white flex items-center gap-2">
                            <ClipboardList className="w-6 h-6 text-blue-600 dark:text-blue-400" />
                            New Material Requisition (PR)
                        </h1>
                        <p className="text-xs sm:text-sm text-slate-500 dark:text-slate-400 mt-0.5">
                            Auto-numbered as <strong className="text-blue-600 dark:text-blue-400">{suggestedRequisitionNo}</strong> • Submitted for Project Manager / Admin Approval
                        </p>
                    </div>

                    <Link
                        href="/inventory/requisitions"
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
                            <span className="block font-bold">Please correct the errors before submitting:</span>
                            <ul className="list-disc list-inside text-[11px] font-normal text-rose-700 dark:text-rose-300/90 mt-1 space-y-0.5">
                                {Object.values(errors).map((err, i) => (
                                    <li key={i}>{err}</li>
                                ))}
                            </ul>
                        </div>
                    </div>
                )}

                <form onSubmit={submit} className="space-y-6">
                    {/* General Information Card */}
                    <div className="rounded-2xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 p-6 shadow-xs space-y-4">
                        <h2 className="text-sm font-bold text-slate-800 dark:text-slate-200 uppercase tracking-wider">
                            Requisition Details
                        </h2>

                        <div className="grid grid-cols-1 sm:grid-cols-3 gap-4">
                            <div>
                                <label className="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5 uppercase tracking-wider">
                                    Project Site Destination
                                </label>
                                <select
                                    value={data.project_id}
                                    onChange={(e) => setData('project_id', e.target.value)}
                                    className="w-full px-3 py-2 text-xs rounded-xl border border-slate-200 dark:border-slate-800 bg-slate-50 dark:bg-slate-950 text-slate-800 dark:text-slate-100 focus:outline-none focus:ring-2 focus:ring-blue-500"
                                >
                                    <option value="">Central Store / Overhead</option>
                                    {projects.map((p) => (
                                        <option key={p.id} value={p.id}>{p.name} ({p.location || 'Site'})</option>
                                    ))}
                                </select>
                            </div>

                            <div>
                                <label className="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5 uppercase tracking-wider">
                                    Required On-Site Date <span className="text-rose-500">*</span>
                                </label>
                                <input
                                    type="date"
                                    required
                                    value={data.required_date}
                                    onChange={(e) => setData('required_date', e.target.value)}
                                    className="w-full px-3 py-2 text-xs rounded-xl border border-slate-200 dark:border-slate-800 bg-slate-50 dark:bg-slate-950 text-slate-800 dark:text-slate-100 focus:outline-none focus:ring-2 focus:ring-blue-500"
                                />
                            </div>

                            <div>
                                <label className="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5 uppercase tracking-wider">
                                    Priority Level <span className="text-rose-500">*</span>
                                </label>
                                <select
                                    value={data.priority}
                                    onChange={(e) => setData('priority', e.target.value)}
                                    className="w-full px-3 py-2 text-xs rounded-xl border border-slate-200 dark:border-slate-800 bg-slate-50 dark:bg-slate-950 text-slate-800 dark:text-slate-100 focus:outline-none focus:ring-2 focus:ring-blue-500 font-bold"
                                >
                                    <option value="low">Low Priority</option>
                                    <option value="medium">Medium Priority</option>
                                    <option value="high">High Priority</option>
                                    <option value="urgent">Urgent / Site Critical</option>
                                </select>
                            </div>
                        </div>

                        <div className="grid grid-cols-1 sm:grid-cols-2 gap-4 pt-2">
                            <div>
                                <label className="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5 uppercase tracking-wider">
                                    Work Activity / Purpose
                                </label>
                                <input
                                    type="text"
                                    value={data.purpose}
                                    onChange={(e) => setData('purpose', e.target.value)}
                                    placeholder="e.g. Ground Floor Column & Beam Concreting"
                                    className="w-full px-3 py-2 text-xs rounded-xl border border-slate-200 dark:border-slate-800 bg-slate-50 dark:bg-slate-950 text-slate-800 dark:text-slate-100 focus:outline-none focus:ring-2 focus:ring-blue-500"
                                />
                            </div>

                            <div>
                                <label className="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5 uppercase tracking-wider">
                                    General Remarks / Instructions
                                </label>
                                <input
                                    type="text"
                                    value={data.remarks}
                                    onChange={(e) => setData('remarks', e.target.value)}
                                    placeholder="e.g. Material test certificates required upon delivery"
                                    className="w-full px-3 py-2 text-xs rounded-xl border border-slate-200 dark:border-slate-800 bg-slate-50 dark:bg-slate-950 text-slate-800 dark:text-slate-100 focus:outline-none focus:ring-2 focus:ring-blue-500"
                                />
                            </div>
                        </div>
                    </div>

                    {/* Line Items Card */}
                    <div className="rounded-2xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 p-6 shadow-xs space-y-4">
                        <div className="flex items-center justify-between">
                            <div>
                                <h2 className="text-sm font-bold text-slate-800 dark:text-slate-200 uppercase tracking-wider">
                                    Requested Materials & Consumables
                                </h2>
                                <p className="text-xs text-slate-500 dark:text-slate-400">
                                    Select from stock items catalog or type any custom construction material.
                                </p>
                            </div>

                            <button
                                type="button"
                                onClick={addItem}
                                className="inline-flex items-center gap-1.5 px-3 py-1.5 bg-blue-50 dark:bg-blue-950/60 hover:bg-blue-100 text-blue-700 dark:text-blue-300 rounded-xl text-xs font-bold transition-colors cursor-pointer border border-blue-200 dark:border-blue-800"
                            >
                                <Plus className="w-3.5 h-3.5" />
                                <span>Add Line Item</span>
                            </button>
                        </div>

                        <div className="space-y-3">
                            {data.items.map((item, index) => {
                                const lineTotal = (parseFloat(item.quantity_requested) || 0) * (parseFloat(item.estimated_unit_price) || 0);
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
                                            {/* Existing Catalog Selection */}
                                            <div className="sm:col-span-4">
                                                <label className="block text-[11px] font-bold text-slate-600 dark:text-slate-400 mb-1">
                                                    Link Existing Stock Item
                                                </label>
                                                <select
                                                    value={item.inventory_item_id}
                                                    onChange={(e) => updateItem(index, 'inventory_item_id', e.target.value)}
                                                    className="w-full px-2.5 py-1.5 text-xs rounded-lg border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-900 text-slate-800 dark:text-slate-100"
                                                >
                                                    <option value="">-- Custom Material / Not in Stock --</option>
                                                    {inventoryItems.map((inv) => (
                                                        <option key={inv.id} value={inv.id}>
                                                            {inv.name} ({inv.item_no}) - Current Stock: {inv.quantity}
                                                        </option>
                                                    ))}
                                                </select>
                                            </div>

                                            {/* Item Name */}
                                            <div className="sm:col-span-4">
                                                <label className="block text-[11px] font-bold text-slate-600 dark:text-slate-400 mb-1">
                                                    Material Name / Description <span className="text-rose-500">*</span>
                                                </label>
                                                <input
                                                    type="text"
                                                    required
                                                    value={item.item_name}
                                                    onChange={(e) => updateItem(index, 'item_name', e.target.value)}
                                                    placeholder="e.g. Dangote OPC Cement 42.5R"
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
                                                    placeholder="bags, pcs, m3..."
                                                    className="w-full px-2.5 py-1.5 text-xs rounded-lg border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-900 text-slate-800 dark:text-slate-100"
                                                />
                                            </div>

                                            {/* Quantity */}
                                            <div className="sm:col-span-2">
                                                <label className="block text-[11px] font-bold text-slate-600 dark:text-slate-400 mb-1">
                                                    Qty Requested <span className="text-rose-500">*</span>
                                                </label>
                                                <input
                                                    type="number"
                                                    step="0.01"
                                                    min="0.01"
                                                    required
                                                    value={item.quantity_requested}
                                                    onChange={(e) => updateItem(index, 'quantity_requested', e.target.value)}
                                                    className="w-full px-2.5 py-1.5 text-xs rounded-lg border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-900 text-slate-800 dark:text-slate-100 font-bold"
                                                />
                                            </div>
                                        </div>

                                        <div className="grid grid-cols-1 sm:grid-cols-12 gap-3 pt-1">
                                            {/* Est Price */}
                                            <div className="sm:col-span-4">
                                                <label className="block text-[11px] font-bold text-slate-600 dark:text-slate-400 mb-1">
                                                    Est. Unit Price (ETB)
                                                </label>
                                                <input
                                                    type="number"
                                                    step="0.01"
                                                    min="0"
                                                    value={item.estimated_unit_price}
                                                    onChange={(e) => updateItem(index, 'estimated_unit_price', e.target.value)}
                                                    placeholder="0.00"
                                                    className="w-full px-2.5 py-1.5 text-xs rounded-lg border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-900 text-slate-800 dark:text-slate-100"
                                                />
                                            </div>

                                            {/* Specifications */}
                                            <div className="sm:col-span-5">
                                                <label className="block text-[11px] font-bold text-slate-600 dark:text-slate-400 mb-1">
                                                    Technical Specs / Brand Note
                                                </label>
                                                <input
                                                    type="text"
                                                    value={item.specifications}
                                                    onChange={(e) => updateItem(index, 'specifications', e.target.value)}
                                                    placeholder="Grade, thickness, test requirements..."
                                                    className="w-full px-2.5 py-1.5 text-xs rounded-lg border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-900 text-slate-800 dark:text-slate-100"
                                                />
                                            </div>

                                            {/* Line Total */}
                                            <div className="sm:col-span-3 flex flex-col justify-end text-right">
                                                <div className="text-[10px] uppercase font-bold text-slate-400">Est. Line Total</div>
                                                <div className="text-sm font-black text-slate-900 dark:text-white">
                                                    {lineTotal.toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 })} ETB
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                );
                            })}
                        </div>

                        {/* Grand Total Summary */}
                        <div className="p-4 rounded-xl bg-blue-50/60 dark:bg-blue-950/30 border border-blue-200 dark:border-blue-900 flex flex-col sm:flex-row items-center justify-between gap-2">
                            <span className="text-xs font-bold text-blue-900 dark:text-blue-200 uppercase tracking-wider">
                                Total Estimated Requisition Value:
                            </span>
                            <span className="text-xl font-black text-blue-700 dark:text-blue-300">
                                {totalEstimated.toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 })} ETB
                            </span>
                        </div>
                    </div>

                    {/* Actions */}
                    <div className="flex items-center justify-end gap-3 pt-2">
                        <Link
                            href="/inventory/requisitions"
                            className="px-4 py-2.5 rounded-xl border border-slate-200 dark:border-slate-800 text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800 font-bold text-xs transition-colors cursor-pointer"
                        >
                            Cancel
                        </Link>
                        <button
                            type="submit"
                            disabled={processing}
                            className="inline-flex items-center gap-2 px-6 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs shadow-md shadow-blue-500/25 transition-all cursor-pointer disabled:opacity-50"
                        >
                            <Save className="w-4 h-4" />
                            <span>{processing ? 'Submitting...' : 'Submit Requisition'}</span>
                        </button>
                    </div>
                </form>
            </div>
        </AuthenticatedLayout>
    );
}
