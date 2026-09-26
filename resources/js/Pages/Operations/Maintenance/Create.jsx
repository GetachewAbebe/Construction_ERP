import { useState } from 'react';
import { Head, Link, useForm, router } from '@inertiajs/react';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import {
    Wrench,
    ArrowLeft,
    Plus,
    Trash2,
    Truck,
    Building2,
    Calendar,
    Clock,
    DollarSign,
    CheckCircle2,
    AlertTriangle,
    Package,
    ShieldAlert,
    Gauge,
} from 'lucide-react';

export default function Create({
    equipmentList = [],
    projects = [],
    mechanics = [],
    stockParts = [],
    schedules = [],
    selectedEquipmentId = null,
}) {
    const initialEquipment = selectedEquipmentId
        ? equipmentList.find((e) => e.id === Number(selectedEquipmentId))
        : equipmentList[0];

    const { data, setData, post, processing, errors } = useForm({
        equipment_id: initialEquipment ? String(initialEquipment.id) : '',
        project_id: initialEquipment?.project_id ? String(initialEquipment.project_id) : '',
        order_type: 'preventive',
        priority: 'routine',
        title: '',
        operating_hours: initialEquipment ? String(initialEquipment.operating_hours || 0) : '0',
        fault_description: '',
        assigned_mechanic_id: mechanics[0]?.id ? String(mechanics[0].id) : '',
        start_date: new Date().toISOString().split('T')[0],
        labor_cost: '',
        external_cost: '',
        notes: '',
        parts: [],
    });

    // Handle equipment selection change
    const handleEquipmentChange = (id) => {
        const machine = equipmentList.find((e) => String(e.id) === String(id));
        setData((prev) => ({
            ...prev,
            equipment_id: id,
            project_id: machine?.project_id ? String(machine.project_id) : prev.project_id,
            operating_hours: machine ? String(machine.operating_hours || 0) : prev.operating_hours,
        }));
    };

    // Apply quick schedule package
    const handleApplySchedule = (schedule) => {
        setData((prev) => ({
            ...prev,
            order_type: 'preventive',
            title: schedule.name,
            fault_description: schedule.description || '',
        }));
    };

    // Add spare part row
    const handleAddPart = () => {
        setData('parts', [
            ...data.parts,
            {
                inventory_item_id: '',
                part_name: '',
                part_number: '',
                quantity: 1,
                unit_of_measurement: 'pcs',
                unit_price: 0,
                total_price: 0,
            },
        ]);
    };

    // Remove spare part row
    const handleRemovePart = (index) => {
        const updated = [...data.parts];
        updated.splice(index, 1);
        setData('parts', updated);
    };

    // Update part field
    const handlePartChange = (index, field, value) => {
        const updated = [...data.parts];
        const part = { ...updated[index] };

        if (field === 'inventory_item_id') {
            const stock = stockParts.find((s) => String(s.id) === String(value));
            part.inventory_item_id = value;
            if (stock) {
                part.part_name = stock.name;
                part.part_number = stock.item_no;
                part.unit_of_measurement = stock.unit_of_measurement || 'pcs';
            }
        } else {
            part[field] = value;
        }

        const qty = Number(part.quantity) || 0;
        const price = Number(part.unit_price) || 0;
        part.total_price = Number((qty * price).toFixed(2));

        updated[index] = part;
        setData('parts', updated);
    };

    // Totals
    const partsTotal = data.parts.reduce((sum, p) => sum + (Number(p.total_price) || 0), 0);
    const laborTotal = Number(data.labor_cost) || 0;
    const externalTotal = Number(data.external_cost) || 0;
    const grandTotal = partsTotal + laborTotal + externalTotal;

    const selectedMachine = equipmentList.find((e) => String(e.id) === String(data.equipment_id));

    const handleSubmit = (e) => {
        e.preventDefault();
        post('/equipment/maintenance');
    };

    return (
        <AuthenticatedLayout title="Open Maintenance Work Order" header="Fleet Management">
            <form onSubmit={handleSubmit} className="space-y-6 pb-20">
                {/* Header */}
                <div className="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-2 border-b border-slate-200 dark:border-slate-800">
                    <div className="flex items-center gap-3">
                        <Link
                            href="/equipment/maintenance"
                            className="p-2 rounded-xl bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300 transition-colors"
                        >
                            <ArrowLeft className="w-5 h-5" />
                        </Link>
                        <div>
                            <h1 className="text-xl sm:text-2xl font-extrabold tracking-tight text-slate-900 dark:text-white flex items-center gap-2.5">
                                <Wrench className="w-7 h-7 text-amber-500" />
                                Issue Maintenance Work Order
                            </h1>
                            <p className="text-xs sm:text-sm text-slate-500 dark:text-slate-400 mt-0.5">
                                Log machinery breakdown repairs, scheduled 250h/500h services, and spare parts issues.
                            </p>
                        </div>
                    </div>

                    <div className="flex items-center gap-2">
                        <Link
                            href="/equipment/maintenance"
                            className="px-4 py-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300 font-semibold text-xs transition-all cursor-pointer"
                        >
                            Cancel
                        </Link>
                        <button
                            type="submit"
                            disabled={processing}
                            className="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-amber-500 hover:bg-amber-600 text-slate-950 font-bold text-xs shadow-md shadow-amber-500/20 transition-all cursor-pointer disabled:opacity-50"
                        >
                            <CheckCircle2 className="w-4 h-4" />
                            <span>{processing ? 'Opening Work Order...' : 'Open Work Order'}</span>
                        </button>
                    </div>
                </div>

                {/* Section 1: Equipment & Service Scope */}
                <div className="bg-white dark:bg-slate-900 p-5 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-xs">
                    <h3 className="text-sm font-bold text-slate-900 dark:text-white mb-4 flex items-center gap-2">
                        <Truck className="w-4 h-4 text-amber-500" />
                        Machinery Selection & Service Type
                    </h3>

                    <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                        <div>
                            <label className="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">
                                Heavy Machinery / Vehicle *
                            </label>
                            <select
                                required
                                value={data.equipment_id}
                                onChange={(e) => handleEquipmentChange(e.target.value)}
                                className="w-full px-3 py-2 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-xs text-slate-900 dark:text-white focus:ring-2 focus:ring-amber-500"
                            >
                                <option value="">Select Equipment</option>
                                {equipmentList.map((eq) => (
                                    <option key={eq.id} value={eq.id}>
                                        {eq.name} ({eq.plate_number || eq.type}) - {Number(eq.operating_hours).toFixed(0)}h
                                    </option>
                                ))}
                            </select>
                            {errors.equipment_id && <p className="text-rose-500 text-[10px] mt-1">{errors.equipment_id}</p>}

                            {selectedMachine && (
                                <div className="mt-2 text-[11px] text-slate-500">
                                    Status: <strong className="uppercase">{selectedMachine.status}</strong> • Next Service: <strong>{Number(selectedMachine.next_service_hours || 0).toFixed(0)}h</strong>
                                </div>
                            )}
                        </div>

                        <div>
                            <label className="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">
                                Service Order Type *
                            </label>
                            <select
                                required
                                value={data.order_type}
                                onChange={(e) => setData('order_type', e.target.value)}
                                className="w-full px-3 py-2 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-xs text-slate-900 dark:text-white focus:ring-2 focus:ring-amber-500"
                            >
                                <option value="preventive">Preventive Service (Scheduled)</option>
                                <option value="breakdown">Breakdown Repair (Unscheduled)</option>
                                <option value="inspection">Safety / Fitness Inspection</option>
                                <option value="tire_tracks">Tires & Undercarriage Overhaul</option>
                            </select>
                        </div>

                        <div>
                            <label className="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">
                                Priority Level *
                            </label>
                            <select
                                required
                                value={data.priority}
                                onChange={(e) => setData('priority', e.target.value)}
                                className="w-full px-3 py-2 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-xs text-slate-900 dark:text-white focus:ring-2 focus:ring-amber-500"
                            >
                                <option value="routine">Routine</option>
                                <option value="urgent">Urgent</option>
                                <option value="critical_downtime">Critical Stoppage (Zero Production)</option>
                            </select>
                        </div>

                        <div>
                            <label className="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">
                                Hour Meter Reading *
                            </label>
                            <input
                                type="number"
                                step="0.1"
                                min="0"
                                required
                                value={data.operating_hours}
                                onChange={(e) => setData('operating_hours', e.target.value)}
                                className="w-full px-3 py-2 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-xs text-slate-900 dark:text-white focus:ring-2 focus:ring-amber-500"
                            />
                            {errors.operating_hours && <p className="text-rose-500 text-[10px] mt-1">{errors.operating_hours}</p>}
                        </div>
                    </div>

                    {/* Pre-defined preventive service package shortcuts */}
                    {data.order_type === 'preventive' && schedules.length > 0 && (
                        <div className="mt-4 pt-3 border-t border-slate-200 dark:border-slate-800">
                            <span className="text-[11px] font-semibold text-slate-500 block mb-2">
                                Quick Apply Standard Maintenance Package:
                            </span>
                            <div className="flex flex-wrap gap-2">
                                {schedules.map((s) => (
                                    <button
                                        type="button"
                                        key={s.id}
                                        onClick={() => handleApplySchedule(s)}
                                        className="px-3 py-1.5 rounded-lg text-xs font-semibold bg-amber-50 dark:bg-amber-950/40 text-amber-800 dark:text-amber-300 border border-amber-200 dark:border-amber-800 hover:bg-amber-100 transition-colors"
                                    >
                                        + {s.name} ({s.interval_hours}h)
                                    </button>
                                ))}
                            </div>
                        </div>
                    )}
                </div>

                {/* Section 2: Details & Problem Statement */}
                <div className="bg-white dark:bg-slate-900 p-5 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-xs">
                    <h3 className="text-sm font-bold text-slate-900 dark:text-white mb-4 flex items-center gap-2">
                        <FileText className="w-4 h-4 text-blue-500" />
                        Work Order Scope & Mechanics Assignment
                    </h3>

                    <div className="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-4">
                        <div className="sm:col-span-2">
                            <label className="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">
                                Work Order Title / Problem Statement *
                            </label>
                            <input
                                type="text"
                                required
                                placeholder="e.g. 250h Engine Service or Hydraulic boom cylinder leak"
                                value={data.title}
                                onChange={(e) => setData('title', e.target.value)}
                                className="w-full px-3 py-2 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-xs text-slate-900 dark:text-white focus:ring-2 focus:ring-amber-500"
                            />
                            {errors.title && <p className="text-rose-500 text-[10px] mt-1">{errors.title}</p>}
                        </div>

                        <div>
                            <label className="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">
                                Assigned Lead Mechanic
                            </label>
                            <select
                                value={data.assigned_mechanic_id}
                                onChange={(e) => setData('assigned_mechanic_id', e.target.value)}
                                className="w-full px-3 py-2 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-xs text-slate-900 dark:text-white focus:ring-2 focus:ring-amber-500"
                            >
                                <option value="">Select Mechanic / Technician</option>
                                {mechanics.map((m) => (
                                    <option key={m.id} value={m.id}>{m.name}</option>
                                ))}
                            </select>
                        </div>
                    </div>

                    <div className="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label className="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">
                                Charge to Project Site
                            </label>
                            <select
                                value={data.project_id}
                                onChange={(e) => setData('project_id', e.target.value)}
                                className="w-full px-3 py-2 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-xs text-slate-900 dark:text-white focus:ring-2 focus:ring-amber-500"
                            >
                                <option value="">Central Workshop (Overhead)</option>
                                {projects.map((p) => (
                                    <option key={p.id} value={p.id}>{p.name}</option>
                                ))}
                            </select>
                        </div>

                        <div>
                            <label className="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">
                                Start Date
                            </label>
                            <input
                                type="date"
                                value={data.start_date}
                                onChange={(e) => setData('start_date', e.target.value)}
                                className="w-full px-3 py-2 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-xs text-slate-900 dark:text-white focus:ring-2 focus:ring-amber-500"
                            />
                        </div>
                    </div>

                    <div className="mt-4">
                        <label className="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">
                            Operator Fault Symptoms / Breakdown Description
                        </label>
                        <textarea
                            rows={3}
                            placeholder="Detail unusual noises, loss of power, fluid leaks, or dashboard error codes..."
                            value={data.fault_description}
                            onChange={(e) => setData('fault_description', e.target.value)}
                            className="w-full px-3 py-2 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-xs text-slate-900 dark:text-white focus:ring-2 focus:ring-amber-500"
                        />
                    </div>
                </div>

                {/* Section 3: Spare Parts & Lubricants Consumed */}
                <div className="bg-white dark:bg-slate-900 p-5 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-xs">
                    <div className="flex items-center justify-between mb-4 pb-3 border-b border-slate-200 dark:border-slate-800">
                        <div>
                            <h3 className="text-sm font-bold text-slate-900 dark:text-white flex items-center gap-2">
                                <Package className="w-4 h-4 text-emerald-600" />
                                Spare Parts, Filters & Lubricants Consumed
                            </h3>
                            <p className="text-xs text-slate-500 mt-0.5">
                                Select from warehouse catalog to automatically deduct stock on job completion.
                            </p>
                        </div>

                        <button
                            type="button"
                            onClick={handleAddPart}
                            className="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs shadow-xs transition-all cursor-pointer"
                        >
                            <Plus className="w-3.5 h-3.5" />
                            <span>Add Part / Fluid</span>
                        </button>
                    </div>

                    {data.parts.length === 0 ? (
                        <div className="py-6 text-center text-slate-400 text-xs">
                            No parts or lubricants added yet. Click <strong>"Add Part / Fluid"</strong> if materials are being consumed.
                        </div>
                    ) : (
                        <div className="overflow-x-auto">
                            <table className="w-full text-left text-xs min-w-[700px]">
                                <thead className="bg-slate-50 dark:bg-slate-800/60 border-b border-slate-200 dark:border-slate-800 text-slate-500 uppercase font-semibold">
                                    <tr>
                                        <th className="py-2.5 px-3">Warehouse Stock Item / Part Name</th>
                                        <th className="py-2.5 px-3">OEM Part #</th>
                                        <th className="py-2.5 px-3">Quantity</th>
                                        <th className="py-2.5 px-3">Unit</th>
                                        <th className="py-2.5 px-3">Unit Price (ETB)</th>
                                        <th className="py-2.5 px-3 text-right">Total (ETB)</th>
                                        <th className="py-2.5 px-2 text-center"></th>
                                    </tr>
                                </thead>
                                <tbody className="divide-y divide-slate-200 dark:divide-slate-800">
                                    {data.parts.map((part, index) => (
                                        <tr key={index} className="hover:bg-slate-50/60 dark:hover:bg-slate-800/40">
                                            <td className="py-2.5 px-3 w-72">
                                                <select
                                                    value={part.inventory_item_id}
                                                    onChange={(e) => handlePartChange(index, 'inventory_item_id', e.target.value)}
                                                    className="w-full px-2 py-1 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-lg text-xs mb-1"
                                                >
                                                    <option value="">Pick from Warehouse Stock...</option>
                                                    {stockParts.map((sp) => (
                                                        <option key={sp.id} value={sp.id}>
                                                            {sp.name} ({sp.quantity} in stock)
                                                        </option>
                                                    ))}
                                                </select>
                                                <input
                                                    type="text"
                                                    required
                                                    placeholder="Or custom part name..."
                                                    value={part.part_name}
                                                    onChange={(e) => handlePartChange(index, 'part_name', e.target.value)}
                                                    className="w-full px-2 py-1 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-lg text-xs"
                                                />
                                            </td>
                                            <td className="py-2.5 px-3">
                                                <input
                                                    type="text"
                                                    placeholder="OEM #"
                                                    value={part.part_number}
                                                    onChange={(e) => handlePartChange(index, 'part_number', e.target.value)}
                                                    className="w-28 px-2 py-1 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-lg text-xs font-mono"
                                                />
                                            </td>
                                            <td className="py-2.5 px-3">
                                                <input
                                                    type="number"
                                                    step="0.01"
                                                    min="0.01"
                                                    required
                                                    value={part.quantity}
                                                    onChange={(e) => handlePartChange(index, 'quantity', e.target.value)}
                                                    className="w-20 px-2 py-1 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-lg text-xs font-bold"
                                                />
                                            </td>
                                            <td className="py-2.5 px-3">
                                                <input
                                                    type="text"
                                                    placeholder="pcs/liters"
                                                    value={part.unit_of_measurement}
                                                    onChange={(e) => handlePartChange(index, 'unit_of_measurement', e.target.value)}
                                                    className="w-16 px-2 py-1 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-lg text-xs"
                                                />
                                            </td>
                                            <td className="py-2.5 px-3">
                                                <input
                                                    type="number"
                                                    step="0.01"
                                                    min="0"
                                                    value={part.unit_price}
                                                    onChange={(e) => handlePartChange(index, 'unit_price', e.target.value)}
                                                    className="w-24 px-2 py-1 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-lg text-xs"
                                                />
                                            </td>
                                            <td className="py-2.5 px-3 text-right font-extrabold text-slate-900 dark:text-white">
                                                {Number(part.total_price).toLocaleString()} ETB
                                            </td>
                                            <td className="py-2.5 px-2 text-center">
                                                <button
                                                    type="button"
                                                    onClick={() => handleRemovePart(index)}
                                                    className="p-1 text-slate-400 hover:text-rose-600 rounded transition-colors"
                                                >
                                                    <Trash2 className="w-3.5 h-3.5" />
                                                </button>
                                            </td>
                                        </tr>
                                    ))}
                                </tbody>
                            </table>
                        </div>
                    )}
                </div>

                {/* Section 4: Cost Breakdown & Estimates */}
                <div className="bg-white dark:bg-slate-900 p-5 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-xs">
                    <h3 className="text-sm font-bold text-slate-900 dark:text-white mb-4 flex items-center gap-2">
                        <DollarSign className="w-4 h-4 text-emerald-600" />
                        Labor & External Costs
                    </h3>

                    <div className="grid grid-cols-1 sm:grid-cols-3 gap-4">
                        <div>
                            <label className="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">
                                Internal Mechanic Labor Cost (ETB)
                            </label>
                            <input
                                type="number"
                                step="0.01"
                                min="0"
                                placeholder="0.00"
                                value={data.labor_cost}
                                onChange={(e) => setData('labor_cost', e.target.value)}
                                className="w-full px-3 py-2 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-xs text-slate-900 dark:text-white focus:ring-2 focus:ring-amber-500"
                            />
                        </div>

                        <div>
                            <label className="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">
                                External Machine Shop / Specialist (ETB)
                            </label>
                            <input
                                type="number"
                                step="0.01"
                                min="0"
                                placeholder="e.g. Lathe / calibration cost"
                                value={data.external_cost}
                                onChange={(e) => setData('external_cost', e.target.value)}
                                className="w-full px-3 py-2 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-xs text-slate-900 dark:text-white focus:ring-2 focus:ring-amber-500"
                            />
                        </div>

                        <div className="p-3 bg-slate-50 dark:bg-slate-800/80 rounded-xl border border-slate-200 dark:border-slate-700">
                            <span className="text-[11px] text-slate-500 block">Total Work Order Cost:</span>
                            <span className="text-xl font-black text-amber-600 dark:text-amber-400">
                                {grandTotal.toLocaleString()} ETB
                            </span>
                            <span className="text-[10px] text-slate-400 block mt-0.5">
                                Parts: {partsTotal.toLocaleString()} | Labor: {laborTotal.toLocaleString()} | Ext: {externalTotal.toLocaleString()}
                            </span>
                        </div>
                    </div>
                </div>

                {/* Submit Action Bar */}
                <div className="flex justify-end gap-3 pt-2">
                    <Link
                        href="/equipment/maintenance"
                        className="px-5 py-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300 font-semibold text-xs transition-all"
                    >
                        Cancel
                    </Link>
                    <button
                        type="submit"
                        disabled={processing}
                        className="inline-flex items-center gap-2 px-6 py-2.5 rounded-xl bg-amber-500 hover:bg-amber-600 text-slate-950 font-bold text-xs shadow-lg shadow-amber-500/25 transition-all cursor-pointer disabled:opacity-50"
                    >
                        <CheckCircle2 className="w-4 h-4" />
                        <span>{processing ? 'Submitting...' : 'Issue Maintenance Work Order'}</span>
                    </button>
                </div>
            </form>
        </AuthenticatedLayout>
    );
}
