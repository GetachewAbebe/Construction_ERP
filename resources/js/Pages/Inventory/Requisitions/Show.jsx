import { useState } from 'react';
import { Head, Link, router } from '@inertiajs/react';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import {
    ClipboardList,
    ArrowLeft,
    CheckCircle2,
    XCircle,
    ShoppingCart,
    Clock,
    User,
    Building2,
    Calendar,
    AlertTriangle,
    FileText,
} from 'lucide-react';

export default function Show({ requisition }) {
    const [rejectModalOpen, setRejectModalOpen] = useState(false);
    const [rejectionReason, setRejectionReason] = useState('');
    const [approvalItems, setApprovalItems] = useState(() =>
        (requisition.items || []).map((it) => ({
            id: it.id,
            quantity_approved: it.quantity_approved || it.quantity_requested,
        }))
    );

    const handleApprove = () => {
        if (confirm(`Approve purchase requisition ${requisition.requisition_no}?`)) {
            router.post(`/inventory/requisitions/${requisition.id}/approve`, {
                items: approvalItems,
            });
        }
    };

    const handleReject = (e) => {
        e.preventDefault();
        router.post(`/inventory/requisitions/${requisition.id}/reject`, {
            rejection_reason: rejectionReason,
        }, {
            onSuccess: () => setRejectModalOpen(false),
        });
    };

    const getStatusBadge = (st) => {
        switch (st) {
            case 'approved':
                return <span className="inline-flex items-center gap-1 px-3 py-1 rounded-full text-xs font-bold bg-emerald-50 dark:bg-emerald-950/60 text-emerald-700 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-800"><CheckCircle2 className="w-3.5 h-3.5" /> Approved</span>;
            case 'pending':
                return <span className="inline-flex items-center gap-1 px-3 py-1 rounded-full text-xs font-bold bg-amber-50 dark:bg-amber-950/60 text-amber-700 dark:text-amber-300 border border-amber-200 dark:border-amber-800"><Clock className="w-3.5 h-3.5" /> Pending Approval</span>;
            case 'ordered':
                return <span className="inline-flex items-center gap-1 px-3 py-1 rounded-full text-xs font-bold bg-blue-50 dark:bg-blue-950/60 text-blue-700 dark:text-blue-300 border border-blue-200 dark:border-blue-800"><ShoppingCart className="w-3.5 h-3.5" /> PO Issued</span>;
            case 'completed':
                return <span className="inline-flex items-center gap-1 px-3 py-1 rounded-full text-xs font-bold bg-purple-50 dark:bg-purple-950/60 text-purple-700 dark:text-purple-300 border border-purple-200 dark:border-purple-800"><CheckCircle2 className="w-3.5 h-3.5" /> Completed</span>;
            case 'rejected':
                return <span className="inline-flex items-center gap-1 px-3 py-1 rounded-full text-xs font-bold bg-rose-50 dark:bg-rose-950/60 text-rose-700 dark:text-rose-300 border border-rose-200 dark:border-rose-800"><XCircle className="w-3.5 h-3.5" /> Rejected</span>;
            default:
                return <span className="px-3 py-1 rounded-full text-xs font-bold bg-slate-100 dark:bg-slate-800 text-slate-700">{st}</span>;
        }
    };

    return (
        <AuthenticatedLayout title={`Requisition ${requisition.requisition_no}`} header="Material Procurement">
            <div className="max-w-5xl mx-auto space-y-6">
                {/* Header */}
                <div className="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-2 border-b border-slate-200 dark:border-slate-800">
                    <div>
                        <div className="flex items-center gap-3">
                            <h1 className="text-xl sm:text-2xl font-black tracking-tight text-slate-900 dark:text-white">
                                {requisition.requisition_no}
                            </h1>
                            {getStatusBadge(requisition.status)}
                        </div>
                        <p className="text-xs sm:text-sm text-slate-500 dark:text-slate-400 mt-1">
                            Submitted on {new Date(requisition.created_at).toLocaleDateString()} by <strong>{requisition.requester?.name || 'Staff'}</strong>
                        </p>
                    </div>

                    <div className="flex items-center gap-2">
                        <Link
                            href="/inventory/requisitions"
                            className="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl border border-slate-200 dark:border-slate-800 text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800 font-bold text-xs transition-colors"
                        >
                            <ArrowLeft className="w-3.5 h-3.5" />
                            <span>Back</span>
                        </Link>

                        {requisition.status === 'pending' && (
                            <>
                                <button
                                    type="button"
                                    onClick={() => setRejectModalOpen(true)}
                                    className="inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-xl border border-rose-200 dark:border-rose-800 bg-rose-50 dark:bg-rose-950/40 text-rose-700 dark:text-rose-300 hover:bg-rose-100 text-xs font-bold transition-colors cursor-pointer"
                                >
                                    <XCircle className="w-3.5 h-3.5" />
                                    <span>Reject</span>
                                </button>
                                <button
                                    type="button"
                                    onClick={handleApprove}
                                    className="inline-flex items-center gap-1.5 px-4 py-1.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold shadow-md shadow-emerald-500/20 transition-all cursor-pointer"
                                >
                                    <CheckCircle2 className="w-3.5 h-3.5" />
                                    <span>Approve PR</span>
                                </button>
                            </>
                        )}

                        {requisition.status === 'approved' && (
                            <Link
                                href={`/inventory/purchase-orders/create?pr_id=${requisition.id}`}
                                className="inline-flex items-center gap-1.5 px-4 py-1.5 rounded-xl bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold shadow-md shadow-blue-500/20 transition-all cursor-pointer"
                            >
                                <ShoppingCart className="w-3.5 h-3.5" />
                                <span>Create Purchase Order</span>
                            </Link>
                        )}
                    </div>
                </div>

                {/* Rejection Alert */}
                {requisition.status === 'rejected' && requisition.rejection_reason && (
                    <div className="p-4 rounded-2xl bg-rose-50 dark:bg-rose-950/40 border border-rose-200 dark:border-rose-800/60 flex items-start gap-3">
                        <AlertTriangle className="w-5 h-5 text-rose-600 shrink-0 mt-0.5" />
                        <div>
                            <div className="text-xs font-bold text-rose-900 dark:text-rose-200">Requisition Rejected</div>
                            <div className="text-xs text-rose-700 dark:text-rose-300 mt-1">
                                {requisition.rejection_reason}
                            </div>
                        </div>
                    </div>
                )}

                {/* Details Grid */}
                <div className="grid grid-cols-1 sm:grid-cols-4 gap-4">
                    <div className="rounded-2xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 p-4">
                        <div className="text-[11px] font-bold text-slate-400 uppercase tracking-wider">Project Destination</div>
                        <div className="text-sm font-bold text-slate-800 dark:text-slate-100 mt-1">
                            {requisition.project ? requisition.project.name : 'Central Warehouse / Overhead'}
                        </div>
                        <div className="text-[11px] text-slate-500">{requisition.project?.location}</div>
                    </div>

                    <div className="rounded-2xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 p-4">
                        <div className="text-[11px] font-bold text-slate-400 uppercase tracking-wider">Required Date</div>
                        <div className="text-sm font-bold text-slate-800 dark:text-slate-100 mt-1">
                            {requisition.required_date ? new Date(requisition.required_date).toLocaleDateString() : 'N/A'}
                        </div>
                    </div>

                    <div className="rounded-2xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 p-4">
                        <div className="text-[11px] font-bold text-slate-400 uppercase tracking-wider">Priority</div>
                        <div className="text-sm font-bold uppercase mt-1">
                            {requisition.priority}
                        </div>
                    </div>

                    <div className="rounded-2xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 p-4">
                        <div className="text-[11px] font-bold text-slate-400 uppercase tracking-wider">Approved By</div>
                        <div className="text-sm font-bold text-slate-800 dark:text-slate-100 mt-1">
                            {requisition.approver ? requisition.approver.name : 'Pending Review'}
                        </div>
                    </div>
                </div>

                {requisition.purpose && (
                    <div className="rounded-2xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 p-4">
                        <div className="text-xs font-bold text-slate-500 uppercase tracking-wider">Work Purpose / Scope:</div>
                        <div className="text-xs text-slate-800 dark:text-slate-200 mt-1 font-medium">{requisition.purpose}</div>
                    </div>
                )}

                {/* Items Table */}
                <div className="rounded-2xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 overflow-hidden shadow-xs">
                    <div className="p-4 border-b border-slate-200 dark:border-slate-800 flex items-center justify-between">
                        <h2 className="text-sm font-bold text-slate-800 dark:text-slate-200 uppercase tracking-wider">
                            Requisition Items ({requisition.items?.length || 0})
                        </h2>
                    </div>

                    <div className="overflow-x-auto">
                        <table className="w-full text-left border-collapse text-xs">
                            <thead>
                                <tr className="border-b border-slate-200 dark:border-slate-800 bg-slate-50/75 dark:bg-slate-800/40 text-slate-500 dark:text-slate-400 font-bold uppercase tracking-wider text-[11px]">
                                    <th className="py-3 px-4">#</th>
                                    <th className="py-3 px-4">Item Name / Description</th>
                                    <th className="py-3 px-4">Specifications</th>
                                    <th className="py-3 px-4 text-right">Qty Requested</th>
                                    <th className="py-3 px-4 text-right">Qty Approved</th>
                                    <th className="py-3 px-4 text-right">Est. Unit Price</th>
                                    <th className="py-3 px-4 text-right">Est. Total</th>
                                </tr>
                            </thead>
                            <tbody className="divide-y divide-slate-100 dark:divide-slate-800/60">
                                {(requisition.items || []).map((it, idx) => {
                                    const total = (parseFloat(it.quantity_requested) || 0) * (parseFloat(it.estimated_unit_price) || 0);
                                    return (
                                        <tr key={it.id} className="hover:bg-slate-50/50 dark:hover:bg-slate-800/30">
                                            <td className="py-3 px-4 font-bold text-slate-400">{idx + 1}</td>
                                            <td className="py-3 px-4">
                                                <div className="font-bold text-slate-900 dark:text-white">{it.item_name}</div>
                                                {it.inventory_item && (
                                                    <div className="text-[10px] text-blue-500">Catalog SKU: {it.inventory_item.item_no}</div>
                                                )}
                                            </td>
                                            <td className="py-3 px-4 text-slate-500 dark:text-slate-400">{it.specifications || '-'}</td>
                                            <td className="py-3 px-4 text-right font-semibold text-slate-700 dark:text-slate-300">
                                                {it.quantity_requested} {it.unit_of_measurement}
                                            </td>
                                            <td className="py-3 px-4 text-right font-bold text-emerald-600 dark:text-emerald-400">
                                                {it.quantity_approved || it.quantity_requested} {it.unit_of_measurement}
                                            </td>
                                            <td className="py-3 px-4 text-right text-slate-600 dark:text-slate-300">
                                                {it.estimated_unit_price ? Number(it.estimated_unit_price).toFixed(2) : '0.00'} ETB
                                            </td>
                                            <td className="py-3 px-4 text-right font-bold text-slate-900 dark:text-white">
                                                {total.toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 })} ETB
                                            </td>
                                        </tr>
                                    );
                                })}
                            </tbody>
                        </table>
                    </div>
                </div>

                {/* Linked Purchase Orders */}
                {requisition.purchase_orders && requisition.purchase_orders.length > 0 && (
                    <div className="rounded-2xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 p-6 space-y-3">
                        <h2 className="text-sm font-bold text-slate-800 dark:text-slate-200 uppercase tracking-wider flex items-center gap-2">
                            <ShoppingCart className="w-4 h-4 text-blue-600" />
                            Linked Purchase Orders
                        </h2>
                        <div className="space-y-2">
                            {requisition.purchase_orders.map((po) => (
                                <div key={po.id} className="p-3 rounded-xl border border-slate-200 dark:border-slate-800 flex items-center justify-between text-xs">
                                    <div>
                                        <Link href={`/inventory/purchase-orders/${po.id}`} className="font-bold text-blue-600 hover:underline">
                                            {po.po_no}
                                        </Link>
                                        <span className="text-slate-500 ml-2">to {po.vendor?.name}</span>
                                    </div>
                                    <div className="flex items-center gap-3">
                                        <span className="font-bold text-slate-900 dark:text-white">{Number(po.total_amount).toFixed(2)} ETB</span>
                                        <span className="uppercase text-[10px] font-bold px-2 py-0.5 rounded bg-slate-100 dark:bg-slate-800">{po.status}</span>
                                    </div>
                                </div>
                            ))}
                        </div>
                    </div>
                )}
            </div>

            {/* Rejection Modal */}
            {rejectModalOpen && (
                <div className="fixed inset-0 z-50 bg-black/60 backdrop-blur-xs flex items-center justify-center p-4">
                    <div className="w-full max-w-md rounded-2xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 p-6 shadow-2xl space-y-4">
                        <div className="flex items-center gap-2 text-rose-600">
                            <XCircle className="w-5 h-5" />
                            <h3 className="text-base font-bold text-slate-900 dark:text-white">Reject Requisition</h3>
                        </div>

                        <p className="text-xs text-slate-500 dark:text-slate-400">
                            Please specify the reason for rejecting requisition <strong>{requisition.requisition_no}</strong>.
                        </p>

                        <form onSubmit={handleReject} className="space-y-4">
                            <textarea
                                required
                                rows={3}
                                value={rejectionReason}
                                onChange={(e) => setRejectionReason(e.target.value)}
                                placeholder="e.g. Budget ceiling reached, material already available in Central Store, specifications insufficient..."
                                className="w-full px-3 py-2 text-xs rounded-xl border border-slate-200 dark:border-slate-800 bg-slate-50 dark:bg-slate-950 text-slate-800 dark:text-slate-100 focus:outline-none focus:ring-2 focus:ring-rose-500"
                            />

                            <div className="flex items-center justify-end gap-2">
                                <button
                                    type="button"
                                    onClick={() => setRejectModalOpen(false)}
                                    className="px-3.5 py-2 text-xs font-bold rounded-xl border border-slate-200 dark:border-slate-800 text-slate-600 dark:text-slate-300 hover:bg-slate-100"
                                >
                                    Cancel
                                </button>
                                <button
                                    type="submit"
                                    className="px-4 py-2 text-xs font-bold rounded-xl bg-rose-600 hover:bg-rose-700 text-white"
                                >
                                    Confirm Rejection
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            )}
        </AuthenticatedLayout>
    );
}
