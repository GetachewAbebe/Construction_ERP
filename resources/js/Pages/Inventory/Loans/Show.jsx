import { Head, Link } from '@inertiajs/react';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import {
    ArrowLeft,
    Printer,
    CheckCircle2,
    Clock,
    XCircle,
    Package,
    User,
    Building2,
    Calendar,
    Shield,
    MapPin,
} from 'lucide-react';

export default function Show({ loan }) {
    const handlePrint = () => {
        window.print();
    };

    const passId = `PASS-${String(loan.id).padStart(5, '0')}`;
    const borrowerName = loan.employee?.name || `${loan.employee?.first_name || ''} ${loan.employee?.last_name || ''}`.trim() || 'Site Personnel';

    return (
        <AuthenticatedLayout title={`Gate Pass #${passId}`} header="Inventory & Store">
            <div className="max-w-4xl mx-auto space-y-6">
                {/* Print and Back Controls */}
                <div className="flex items-center justify-between gap-4 pb-2 border-b border-slate-200 dark:border-slate-800 print:hidden">
                    <div className="flex items-center gap-3">
                        <Link
                            href="/inventory/loans"
                            className="p-2 rounded-xl bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 text-slate-600 dark:text-slate-300 transition-colors"
                        >
                            <ArrowLeft className="w-4 h-4" />
                        </Link>
                        <div>
                            <h1 className="text-xl font-extrabold tracking-tight text-slate-900 dark:text-white">
                                Store Gate Pass #{passId}
                            </h1>
                            <p className="text-xs text-slate-500 dark:text-slate-400 mt-0.5">
                                Official material movement clearance, tool dispatch note, and equipment return record.
                            </p>
                        </div>
                    </div>

                    <div className="flex items-center gap-2">
                        <button
                            onClick={handlePrint}
                            className="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl bg-blue-900 hover:bg-blue-950 text-white font-bold text-xs shadow-sm transition-colors cursor-pointer"
                        >
                            <Printer className="w-4 h-4" />
                            <span>Print Gate Pass / PDF</span>
                        </button>
                    </div>
                </div>

                {/* Gate Pass Card (Print-styled) */}
                <div className="rounded-3xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 p-8 shadow-sm space-y-8 print:border-none print:shadow-none print:p-0">
                    {/* Header */}
                    <div className="flex justify-between items-start pb-6 border-b-2 border-blue-900">
                        <div>
                            <h2 className="text-xl font-black tracking-tight text-blue-900 dark:text-blue-400">
                                NATANEM ENGINEERING
                            </h2>
                            <p className="text-[11px] font-semibold text-slate-500 uppercase tracking-wider mt-0.5">
                                Warehouse, Central Store &amp; Asset Dispatch
                            </p>
                        </div>

                        <div className="text-right">
                            <span className="px-3 py-1 rounded-full text-xs font-bold uppercase tracking-wider bg-emerald-50 dark:bg-emerald-950/50 text-emerald-900 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-900">
                                Material Gate Pass
                            </span>
                            <div className="font-mono font-bold text-sm text-slate-900 dark:text-white mt-1">
                                #{passId}
                            </div>
                            <div className="text-[11px] text-slate-400 font-mono mt-0.5">
                                Issue Date: {loan.requested_at ? new Date(loan.requested_at).toLocaleDateString(undefined, { year: 'numeric', month: 'long', day: 'numeric' }) : (loan.created_at ? new Date(loan.created_at).toLocaleDateString(undefined, { year: 'numeric', month: 'long', day: 'numeric' }) : '—')}
                            </div>
                        </div>
                    </div>

                    {/* Metadata Grid */}
                    <div className="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div className="p-4 rounded-xl border border-slate-100 dark:border-slate-800 bg-slate-50/60 dark:bg-slate-800/40">
                            <span className="text-[10px] font-bold text-slate-400 uppercase tracking-wider block mb-1">
                                Recipient / Borrower Staff
                            </span>
                            <div className="font-bold text-slate-900 dark:text-white text-sm">
                                {borrowerName}
                            </div>
                            <div className="text-xs text-slate-500 dark:text-slate-400 mt-0.5">
                                {loan.employee?.position_rel?.name || 'Site Personnel'} · {loan.employee?.department_rel?.name || 'Operations'}
                            </div>
                            {loan.employee?.phone && (
                                <div className="text-[11px] text-slate-400 font-mono mt-1">
                                    Contact: {loan.employee.phone}
                                </div>
                            )}
                        </div>

                        <div className="p-4 rounded-xl border border-slate-100 dark:border-slate-800 bg-slate-50/60 dark:bg-slate-800/40">
                            <span className="text-[10px] font-bold text-slate-400 uppercase tracking-wider block mb-1">
                                Authorization Status
                            </span>
                            <span className={`font-bold text-sm uppercase inline-flex items-center gap-1.5 ${
                                loan.status === 'approved'
                                    ? 'text-emerald-600 dark:text-emerald-400'
                                    : loan.status === 'returned'
                                    ? 'text-blue-600 dark:text-blue-400'
                                    : loan.status === 'rejected'
                                    ? 'text-rose-600 dark:text-rose-400'
                                    : 'text-amber-600 dark:text-amber-400'
                            }`}>
                                {loan.status === 'approved' ? (
                                    <CheckCircle2 className="w-4 h-4" />
                                ) : loan.status === 'returned' ? (
                                    <Package className="w-4 h-4" />
                                ) : loan.status === 'rejected' ? (
                                    <XCircle className="w-4 h-4" />
                                ) : (
                                    <Clock className="w-4 h-4" />
                                )}
                                <span>{loan.status}</span>
                            </span>
                            {loan.approved_by && (
                                <div className="text-[11px] text-slate-400 mt-1">
                                    Authorized by: {loan.approved_by?.name || 'Administrator'}
                                </div>
                            )}
                        </div>

                        <div className="p-4 rounded-xl border border-slate-100 dark:border-slate-800 bg-slate-50/60 dark:bg-slate-800/40">
                            <span className="text-[10px] font-bold text-slate-400 uppercase tracking-wider block mb-1">
                                Scheduled Return Date
                            </span>
                            <span className="font-mono font-bold text-slate-900 dark:text-white text-sm">
                                {loan.due_date ? new Date(loan.due_date).toLocaleDateString(undefined, { year: 'numeric', month: 'long', day: 'numeric' }) : (loan.expected_return_date ? new Date(loan.expected_return_date).toLocaleDateString(undefined, { year: 'numeric', month: 'long', day: 'numeric' }) : 'Indefinite / Project Duration')}
                            </span>
                        </div>

                        <div className="p-4 rounded-xl border border-slate-100 dark:border-slate-800 bg-slate-50/60 dark:bg-slate-800/40">
                            <span className="text-[10px] font-bold text-slate-400 uppercase tracking-wider block mb-1">
                                Actual Return Date
                            </span>
                            <span className="font-mono font-bold text-slate-900 dark:text-white text-sm">
                                {loan.returned_at ? new Date(loan.returned_at).toLocaleDateString(undefined, { year: 'numeric', month: 'long', day: 'numeric' }) : 'Pending Return (In Field)'}
                            </span>
                        </div>
                    </div>

                    {/* Material Dispatch Table */}
                    <div className="overflow-hidden rounded-2xl border border-slate-200 dark:border-slate-800">
                        <table className="w-full text-left text-xs">
                            <thead className="bg-slate-50 dark:bg-slate-800/60 text-slate-500 uppercase font-bold text-[10px] tracking-wider border-b border-slate-200 dark:border-slate-800">
                                <tr>
                                    <th className="py-3 px-4">Item Number / SKU</th>
                                    <th className="py-3 px-4">Material / Equipment Description</th>
                                    <th className="py-3 px-4">Store Location</th>
                                    <th className="py-3 px-4 text-right">Dispatched Qty</th>
                                </tr>
                            </thead>
                            <tbody className="divide-y divide-slate-100 dark:divide-slate-800">
                                <tr>
                                    <td className="py-3.5 px-4 font-mono font-bold text-slate-900 dark:text-white">
                                        {loan.item?.item_no || `ITM-${String(loan.inventory_item_id || loan.item?.id || 0).padStart(4, '0')}`}
                                    </td>
                                    <td className="py-3.5 px-4">
                                        <div className="font-bold text-slate-900 dark:text-white text-sm">
                                            {loan.item?.name || 'Material Asset'}
                                        </div>
                                        <div className="text-[11px] text-slate-400 mt-0.5">
                                            {loan.remarks || loan.notes || 'Standard construction site requisition and equipment gate pass.'}
                                        </div>
                                    </td>
                                    <td className="py-3.5 px-4 text-slate-600 dark:text-slate-300">
                                        {loan.item?.store_location || 'Central Warehouse'}
                                    </td>
                                    <td className="py-3.5 px-4 text-right font-mono font-bold text-base text-blue-900 dark:text-blue-300">
                                        {loan.quantity} {loan.item?.unit_of_measurement || 'pcs'}
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    {/* Purpose / Remarks */}
                    {(loan.remarks || loan.notes) && (
                        <div className="p-4 rounded-xl border border-slate-100 dark:border-slate-800 bg-slate-50/60 dark:bg-slate-800/40">
                            <span className="text-[10px] font-bold text-slate-400 uppercase tracking-wider block mb-1">
                                Operational Purpose &amp; Site Notes
                            </span>
                            <p className="text-xs text-slate-700 dark:text-slate-300 leading-relaxed">
                                {loan.remarks || loan.notes}
                            </p>
                        </div>
                    )}

                    {/* Tripartite Signatures */}
                    <div className="grid grid-cols-3 gap-6 pt-10">
                        <div className="border-t border-slate-300 dark:border-slate-700 pt-3 text-center">
                            <span className="text-[10px] font-bold text-slate-500 uppercase tracking-wider block">
                                Storekeeper / Issued By
                            </span>
                            <span className="text-xs font-semibold text-slate-900 dark:text-white mt-1 block">
                                Central Storekeeper
                            </span>
                        </div>

                        <div className="border-t border-slate-300 dark:border-slate-700 pt-3 text-center">
                            <span className="text-[10px] font-bold text-slate-500 uppercase tracking-wider block">
                                Received By (Borrower)
                            </span>
                            <span className="text-xs font-semibold text-slate-900 dark:text-white mt-1 block">
                                {borrowerName}
                            </span>
                        </div>

                        <div className="border-t border-slate-300 dark:border-slate-700 pt-3 text-center">
                            <span className="text-[10px] font-bold text-slate-500 uppercase tracking-wider block">
                                Security / Gate Clearance
                            </span>
                            <span className="text-xs font-semibold text-slate-900 dark:text-white mt-1 block">
                                Site Gate Guard
                            </span>
                        </div>
                    </div>

                    {/* Footer */}
                    <div className="text-center pt-6 border-t border-dashed border-slate-200 dark:border-slate-800 text-[10px] text-slate-400">
                        Official material movement pass issued by Natanem Engineering ERP · Valid with authorized security stamp.
                    </div>
                </div>
            </div>
        </AuthenticatedLayout>
    );
}

