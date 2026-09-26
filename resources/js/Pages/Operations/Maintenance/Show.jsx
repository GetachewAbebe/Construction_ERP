import { useState } from 'react';
import { Head, Link, router, useForm } from '@inertiajs/react';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import {
    Wrench,
    ArrowLeft,
    Printer,
    CheckCircle2,
    Clock,
    Truck,
    Building2,
    Calendar,
    DollarSign,
    Package,
    Receipt,
    AlertTriangle,
    ShieldAlert,
    Gauge,
    X,
} from 'lucide-react';

export default function Show({ workOrder }) {
    const [isCompleteModalOpen, setIsCompleteModalOpen] = useState(false);

    const { data: completeData, setData: setCompleteData, post: postComplete, processing: completeProcessing, reset: resetComplete } = useForm({
        final_operating_hours: workOrder.operating_hours || '',
        downtime_hours: workOrder.downtime_hours || '0',
        work_performed: workOrder.work_performed || '',
    });

    const handleCompleteSubmit = (e) => {
        e.preventDefault();
        postComplete(`/equipment/maintenance/${workOrder.id}/complete`, {
            onSuccess: () => {
                setIsCompleteModalOpen(false);
                resetComplete();
            },
        });
    };

    const getStatusBadge = (st) => {
        switch (st) {
            case 'completed':
                return (
                    <span className="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-emerald-50 dark:bg-emerald-950/60 text-emerald-700 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-800">
                        <CheckCircle2 className="w-3.5 h-3.5" /> Maintenance Completed & Certified
                    </span>
                );
            case 'in_progress':
                return (
                    <span className="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-blue-50 dark:bg-blue-950/60 text-blue-700 dark:text-blue-300 border border-blue-200 dark:border-blue-800">
                        <Wrench className="w-3.5 h-3.5" /> In Progress
                    </span>
                );
            case 'pending':
                return (
                    <span className="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-amber-50 dark:bg-amber-950/60 text-amber-700 dark:text-amber-300 border border-amber-200 dark:border-amber-800">
                        <Clock className="w-3.5 h-3.5" /> Queued / Pending
                    </span>
                );
            default:
                return <span className="px-3 py-1 rounded-full text-xs font-bold bg-slate-100">{st}</span>;
        }
    };

    const parts = workOrder.parts || [];

    return (
        <AuthenticatedLayout title={`Work Order ${workOrder.work_order_no}`} header="Fleet Management">
            <div className="space-y-6 pb-12">
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
                            <div className="flex items-center gap-2.5">
                                <h1 className="text-xl sm:text-2xl font-extrabold tracking-tight text-slate-900 dark:text-white font-mono">
                                    {workOrder.work_order_no}
                                </h1>
                                {getStatusBadge(workOrder.status)}
                            </div>
                            <p className="text-xs sm:text-sm text-slate-500 dark:text-slate-400 mt-0.5">
                                {workOrder.equipment?.name} ({workOrder.equipment?.plate_number || workOrder.equipment?.type}) • {workOrder.title}
                            </p>
                        </div>
                    </div>

                    <div className="flex flex-wrap items-center gap-2.5">
                        <a
                            href={`/equipment/maintenance/${workOrder.id}/print`}
                            target="_blank"
                            rel="noopener noreferrer"
                            className="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300 font-semibold text-xs transition-all cursor-pointer"
                        >
                            <Printer className="w-4 h-4 text-blue-500" />
                            <span>Print Job Card</span>
                        </a>

                        {workOrder.status !== 'completed' && (
                            <button
                                onClick={() => setIsCompleteModalOpen(true)}
                                className="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs shadow-md shadow-emerald-500/20 transition-all cursor-pointer"
                            >
                                <CheckCircle2 className="w-4 h-4" />
                                <span>Complete & Certify Repair</span>
                            </button>
                        )}
                    </div>
                </div>

                {/* Auto-posted Project Expense Notification Alert */}
                {workOrder.expense && (
                    <div className="p-4 bg-emerald-50 dark:bg-emerald-950/40 border border-emerald-200 dark:border-emerald-800 rounded-2xl flex items-center justify-between gap-4">
                        <div className="flex items-center gap-3">
                            <div className="p-2 bg-emerald-500/10 rounded-xl text-emerald-600 dark:text-emerald-400">
                                <Receipt className="w-5 h-5" />
                            </div>
                            <div>
                                <h4 className="text-xs font-bold text-emerald-900 dark:text-emerald-200">
                                    Project Financial Expense Auto-Posted
                                </h4>
                                <p className="text-[11px] text-emerald-700 dark:text-emerald-300 mt-0.5">
                                    Fleet maintenance expense #{workOrder.expense.reference_no} ({Number(workOrder.expense.amount).toLocaleString()} ETB) allocated to {workOrder.project?.name} budget.
                                </p>
                            </div>
                        </div>
                        <a
                            href={`/finance/expenses/${workOrder.expense.id}/print`}
                            target="_blank"
                            rel="noopener noreferrer"
                            className="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-emerald-600 text-white text-xs font-bold shadow-xs hover:bg-emerald-700 transition-colors"
                        >
                            <span>View Expense Voucher</span>
                        </a>
                    </div>
                )}

                {/* Summary KPI Cards */}
                <div className="grid grid-cols-2 lg:grid-cols-4 gap-4">
                    <div className="bg-white dark:bg-slate-900 p-4 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-xs">
                        <span className="text-xs font-semibold text-slate-500 dark:text-slate-400">Total Repair Cost</span>
                        <div className="mt-2 text-2xl font-black text-amber-600 dark:text-amber-400">
                            {Number(workOrder.total_cost).toLocaleString()} <span className="text-xs font-bold text-slate-400">ETB</span>
                        </div>
                        <span className="text-[11px] text-slate-500">
                            Parts: {Number(workOrder.parts_cost).toLocaleString()} | Labor: {Number(workOrder.labor_cost).toLocaleString()}
                        </span>
                    </div>

                    <div className="bg-white dark:bg-slate-900 p-4 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-xs">
                        <span className="text-xs font-semibold text-slate-500 dark:text-slate-400">Hour Meter Reading</span>
                        <div className="mt-2 text-2xl font-black text-slate-900 dark:text-white">
                            {Number(workOrder.operating_hours).toFixed(1)} <span className="text-xs font-normal text-slate-400">Hours</span>
                        </div>
                        <span className="text-[11px] text-slate-500">
                            Next Due: {Number(workOrder.equipment?.next_service_hours || 0).toFixed(0)}h
                        </span>
                    </div>

                    <div className="bg-white dark:bg-slate-900 p-4 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-xs">
                        <span className="text-xs font-semibold text-slate-500 dark:text-slate-400">Downtime Duration</span>
                        <div className="mt-2 text-2xl font-black text-rose-600 dark:text-rose-400">
                            {Number(workOrder.downtime_hours).toFixed(1)} <span className="text-xs font-normal text-slate-400">Hours</span>
                        </div>
                        <span className="text-[11px] text-slate-500">Idle production hours</span>
                    </div>

                    <div className="bg-white dark:bg-slate-900 p-4 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-xs">
                        <span className="text-xs font-semibold text-slate-500 dark:text-slate-400">Assigned Mechanic</span>
                        <div className="mt-2 text-base font-extrabold text-slate-900 dark:text-white truncate">
                            {workOrder.assigned_mechanic?.name || 'Workshop Team'}
                        </div>
                        <span className="text-[11px] text-slate-500">Lead technician in charge</span>
                    </div>
                </div>

                {/* Machine Specifications Box */}
                <div className="bg-white dark:bg-slate-900 p-5 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-xs">
                    <h3 className="text-sm font-bold text-slate-900 dark:text-white mb-3 flex items-center gap-2">
                        <Truck className="w-4 h-4 text-amber-500" />
                        Machinery Profile & Scope
                    </h3>
                    <div className="grid grid-cols-1 sm:grid-cols-4 gap-4 text-xs">
                        <div>
                            <span className="text-slate-400 block font-semibold">Machine Name</span>
                            <span className="font-bold text-slate-900 dark:text-white text-sm">
                                {workOrder.equipment?.name}
                            </span>
                            <span className="text-slate-500 block">Plate: {workOrder.equipment?.plate_number || 'N/A'}</span>
                        </div>
                        <div>
                            <span className="text-slate-400 block font-semibold">Classification & Fuel</span>
                            <span className="font-bold text-slate-900 dark:text-white">
                                {workOrder.equipment?.type}
                            </span>
                            <span className="text-slate-500 block">Fuel: {workOrder.equipment?.fuel_type || 'Diesel'}</span>
                        </div>
                        <div>
                            <span className="text-slate-400 block font-semibold">Deployment Location</span>
                            <span className="font-bold text-slate-900 dark:text-white">
                                {workOrder.project?.name || 'Central Fleet Workshop'}
                            </span>
                            <span className="text-slate-500 block">{workOrder.project?.location || 'Central Workshop'}</span>
                        </div>
                        <div>
                            <span className="text-slate-400 block font-semibold">Order Classification</span>
                            <span className="font-bold text-slate-900 dark:text-white uppercase">
                                {workOrder.order_type} ({workOrder.priority})
                            </span>
                            <span className="text-slate-500 block">
                                Opened: {workOrder.start_date ? new Date(workOrder.start_date).toLocaleDateString() : 'N/A'}
                            </span>
                        </div>
                    </div>
                </div>

                {/* Problem Statement & Resolution Notes */}
                <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div className="bg-white dark:bg-slate-900 p-5 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-xs">
                        <h4 className="text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-2 flex items-center gap-1.5">
                            <AlertTriangle className="w-4 h-4 text-amber-500" />
                            Operator Problem Statement / Symptoms
                        </h4>
                        <p className="text-xs text-slate-600 dark:text-slate-400 whitespace-pre-line">
                            {workOrder.fault_description || 'Routine scheduled preventive maintenance and inspection.'}
                        </p>
                    </div>

                    <div className="bg-white dark:bg-slate-900 p-5 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-xs">
                        <h4 className="text-xs font-bold text-emerald-600 dark:text-emerald-400 uppercase tracking-wider mb-2 flex items-center gap-1.5">
                            <CheckCircle2 className="w-4 h-4 text-emerald-500" />
                            Mechanic Corrective Actions & Resolution
                        </h4>
                        <p className="text-xs text-slate-600 dark:text-slate-400 whitespace-pre-line">
                            {workOrder.work_performed || 'Work is currently in progress or awaiting certification.'}
                        </p>
                        {workOrder.completed_date && (
                            <div className="mt-3 text-[11px] text-slate-400">
                                Certified by: <strong>{workOrder.completer?.name || 'Mechanic'}</strong> on {new Date(workOrder.completed_date).toLocaleString()}
                            </div>
                        )}
                    </div>
                </div>

                {/* Consumed Spare Parts & Lubricants Table */}
                <div className="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-xs overflow-hidden">
                    <div className="p-4 border-b border-slate-200 dark:border-slate-800 flex items-center justify-between">
                        <h3 className="text-sm font-bold text-slate-900 dark:text-white flex items-center gap-2">
                            <Package className="w-4 h-4 text-emerald-600" />
                            Spare Parts, Filters & Lubricants Installed ({parts.length})
                        </h3>
                        <span className="text-xs font-bold text-slate-900 dark:text-white">
                            Subtotal: {Number(workOrder.parts_cost).toLocaleString()} ETB
                        </span>
                    </div>

                    <div className="overflow-x-auto">
                        <table className="w-full text-left text-xs">
                            <thead className="bg-slate-50 dark:bg-slate-800/60 border-b border-slate-200 dark:border-slate-800 text-slate-500 uppercase font-semibold">
                                <tr>
                                    <th className="py-3 px-4">#</th>
                                    <th className="py-3 px-4">Part Description</th>
                                    <th className="py-3 px-4">OEM Part #</th>
                                    <th className="py-3 px-4 text-center">Quantity</th>
                                    <th className="py-3 px-4 text-right">Unit Price</th>
                                    <th className="py-3 px-4 text-right">Total Price</th>
                                </tr>
                            </thead>
                            <tbody className="divide-y divide-slate-200 dark:divide-slate-800">
                                {parts.length === 0 ? (
                                    <tr>
                                        <td colSpan="6" className="py-6 text-center text-slate-400">
                                            No inventory parts or fluids logged for this repair order.
                                        </td>
                                    </tr>
                                ) : (
                                    parts.map((p, idx) => (
                                        <tr key={p.id} className="hover:bg-slate-50/80 dark:hover:bg-slate-800/40">
                                            <td className="py-3 px-4 font-mono text-slate-400">{idx + 1}</td>
                                            <td className="py-3 px-4 font-bold text-slate-900 dark:text-white">
                                                {p.part_name}
                                                {p.inventory_item && (
                                                    <span className="block text-[10px] text-emerald-600 font-normal">
                                                        Deducted from warehouse stock (#{p.inventory_item.item_no})
                                                    </span>
                                                )}
                                            </td>
                                            <td className="py-3 px-4 font-mono text-slate-600 dark:text-slate-300">
                                                {p.part_number || '-'}
                                            </td>
                                            <td className="py-3 px-4 text-center font-bold text-slate-900 dark:text-white">
                                                {Number(p.quantity).toFixed(2)} {p.unit_of_measurement}
                                            </td>
                                            <td className="py-3 px-4 text-right text-slate-600 dark:text-slate-300">
                                                {Number(p.unit_price).toLocaleString()} ETB
                                            </td>
                                            <td className="py-3 px-4 text-right font-black text-slate-900 dark:text-white">
                                                {Number(p.total_price).toLocaleString()} ETB
                                            </td>
                                        </tr>
                                    ))
                                )}
                            </tbody>
                        </table>
                    </div>
                </div>

                {/* Complete Repair Modal */}
                {isCompleteModalOpen && (
                    <div className="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-xs">
                        <div className="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl max-w-lg w-full p-6 shadow-2xl">
                            <div className="flex items-center justify-between pb-3 border-b border-slate-200 dark:border-slate-800">
                                <h3 className="text-base font-bold text-slate-900 dark:text-white flex items-center gap-2">
                                    <CheckCircle2 className="w-5 h-5 text-emerald-600" />
                                    Complete & Certify Work Order #{workOrder.work_order_no}
                                </h3>
                                <button
                                    onClick={() => setIsCompleteModalOpen(false)}
                                    className="p-1 rounded-lg text-slate-400 hover:text-slate-600 dark:hover:text-slate-200"
                                >
                                    <X className="w-5 h-5" />
                                </button>
                            </div>

                            <form onSubmit={handleCompleteSubmit} className="mt-4 space-y-4">
                                <div className="p-3 bg-emerald-50 dark:bg-emerald-950/40 border border-emerald-200 dark:border-emerald-800 rounded-xl text-xs text-emerald-800 dark:text-emerald-300">
                                    <strong>Automated Workflow Actions:</strong>
                                    <ul className="list-disc list-inside mt-1 space-y-0.5 text-[11px]">
                                        <li>Equipment status updated to <strong>Operational</strong>.</li>
                                        <li>Next service threshold advanced by +250 hours.</li>
                                        <li>Consumed spare parts deducted from warehouse inventory.</li>
                                        <li>Maintenance cost ({Number(workOrder.total_cost).toLocaleString()} ETB) posted to project expenses.</li>
                                    </ul>
                                </div>

                                <div className="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                    <div>
                                        <label className="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">
                                            Final Hour Meter Reading
                                        </label>
                                        <input
                                            type="number"
                                            step="0.1"
                                            min="0"
                                            value={completeData.final_operating_hours}
                                            onChange={(e) => setCompleteData('final_operating_hours', e.target.value)}
                                            className="w-full px-3 py-2 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-xs text-slate-900 dark:text-white focus:ring-2 focus:ring-emerald-500"
                                        />
                                    </div>

                                    <div>
                                        <label className="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">
                                            Total Downtime (Hours Lost)
                                        </label>
                                        <input
                                            type="number"
                                            step="0.5"
                                            min="0"
                                            value={completeData.downtime_hours}
                                            onChange={(e) => setCompleteData('downtime_hours', e.target.value)}
                                            className="w-full px-3 py-2 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-xs text-slate-900 dark:text-white focus:ring-2 focus:ring-emerald-500"
                                        />
                                    </div>
                                </div>

                                <div>
                                    <label className="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">
                                        Work Performed / Diagnostic Resolution Notes *
                                    </label>
                                    <textarea
                                        rows={3}
                                        required
                                        placeholder="Detail test results, parts replaced, torque specifications, and clearance to operate..."
                                        value={completeData.work_performed}
                                        onChange={(e) => setCompleteData('work_performed', e.target.value)}
                                        className="w-full px-3 py-2 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-xs text-slate-900 dark:text-white focus:ring-2 focus:ring-emerald-500"
                                    />
                                </div>

                                <div className="flex justify-end gap-2 pt-3 border-t border-slate-200 dark:border-slate-800">
                                    <button
                                        type="button"
                                        onClick={() => setIsCompleteModalOpen(false)}
                                        className="px-4 py-2 bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300 text-xs font-semibold rounded-xl"
                                    >
                                        Cancel
                                    </button>
                                    <button
                                        type="submit"
                                        disabled={completeProcessing}
                                        className="px-5 py-2 bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs rounded-xl shadow-md transition-all disabled:opacity-50"
                                    >
                                        {completeProcessing ? 'Closing...' : 'Confirm Completion'}
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                )}
            </div>
        </AuthenticatedLayout>
    );
}
