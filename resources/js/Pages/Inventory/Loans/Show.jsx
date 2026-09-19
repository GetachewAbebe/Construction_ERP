import { Head, Link } from '@inertiajs/react';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import {
    ArrowLeft,
    Printer,
    CheckCircle2,
    Clock,
    XCircle,
    Package,
    Building2,
    Calendar,
    Shield,
    MapPin,
    Truck,
    FileText,
    QrCode,
} from 'lucide-react';

export default function Show({ loan }) {
    const handlePrint = () => {
        window.print();
    };

    const passId = `PASS-${String(loan.id).padStart(5, '0')}`;
    const borrowerName =
        loan.employee?.name ||
        `${loan.employee?.first_name || ''} ${loan.employee?.last_name || ''}`.trim() ||
        loan.user?.name ||
        'Site Personnel';

    const departmentName = loan.employee?.department_rel?.name || loan.employee?.department || 'Operations';
    const positionName = loan.employee?.position_rel?.name || loan.employee?.position || 'Site Staff';
    const phone = loan.employee?.phone || '—';
    const itemCode = loan.item?.item_no || `ITM-${String(loan.inventory_item_id || loan.item?.id || 0).padStart(4, '0')}`;
    const itemName = loan.item?.name || 'Material Asset';
    const storeLocation = loan.item?.store_location || 'Central Warehouse';
    const quantity = loan.quantity || 1;
    const unit = loan.item?.unit_of_measurement || 'pcs';
    const purpose = loan.remarks || loan.notes || 'Site construction operations and equipment deployment.';
    const issueDate = loan.requested_at
        ? new Date(loan.requested_at).toLocaleDateString('en-US', { year: 'numeric', month: 'long', day: 'numeric' })
        : (loan.created_at ? new Date(loan.created_at).toLocaleDateString('en-US', { year: 'numeric', month: 'long', day: 'numeric' }) : new Date().toLocaleDateString('en-US', { year: 'numeric', month: 'long', day: 'numeric' }));
    const returnDate = loan.due_date
        ? new Date(loan.due_date).toLocaleDateString('en-US', { year: 'numeric', month: 'long', day: 'numeric' })
        : (loan.expected_return_date ? new Date(loan.expected_return_date).toLocaleDateString('en-US', { year: 'numeric', month: 'long', day: 'numeric' }) : 'Indefinite / Project Duration');
    const returnedAt = loan.returned_at
        ? new Date(loan.returned_at).toLocaleDateString('en-US', { year: 'numeric', month: 'long', day: 'numeric' })
        : 'Pending Return (In Field)';

    const isApproved = loan.status === 'approved';
    const isReturned = loan.status === 'returned';
    const isRejected = loan.status === 'rejected';

    return (
        <AuthenticatedLayout title={`Gate Pass #${passId}`} header="Inventory & Store">
            <div className="max-w-5xl mx-auto space-y-6 print:space-y-0 print:max-w-none print:w-full print:p-0 print:m-0">
                {/* On-Screen Action Controls (Completely suppressed in print) */}
                <div className="flex items-center justify-between gap-4 pb-2 border-b border-slate-200 dark:border-slate-800 print:hidden no-print">
                    <div className="flex items-center gap-3">
                        <Link
                            href="/inventory/loans"
                            className="p-2.5 rounded-xl bg-white dark:bg-slate-800 hover:bg-slate-100 dark:hover:bg-slate-700 text-slate-600 dark:text-slate-300 border border-slate-200 dark:border-slate-700 transition-colors shadow-xs"
                            title="Back to Loans & Gate Passes"
                        >
                            <ArrowLeft className="w-4 h-4" />
                        </Link>
                        <div>
                            <div className="flex items-center gap-2">
                                <h1 className="text-xl font-extrabold tracking-tight text-slate-900 dark:text-white">
                                    Store Gate Pass #{passId}
                                </h1>
                                <span className={`px-2.5 py-0.5 rounded-full text-xs font-bold uppercase tracking-wider ${
                                    isApproved
                                        ? 'bg-emerald-50 dark:bg-emerald-950/50 text-emerald-700 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-800'
                                        : isReturned
                                        ? 'bg-blue-50 dark:bg-blue-950/50 text-blue-700 dark:text-blue-300 border border-blue-200 dark:border-blue-800'
                                        : isRejected
                                        ? 'bg-rose-50 dark:bg-rose-950/50 text-rose-700 dark:text-rose-300 border border-rose-200 dark:border-rose-800'
                                        : 'bg-amber-50 dark:bg-amber-950/50 text-amber-700 dark:text-amber-300 border border-amber-200 dark:border-amber-800'
                                }`}>
                                    {loan.status}
                                </span>
                            </div>
                            <p className="text-xs text-slate-500 dark:text-slate-400 mt-0.5">
                                Official material movement clearance, tool dispatch note, and equipment return record.
                            </p>
                        </div>
                    </div>

                    <div className="flex items-center gap-2">
                        <button
                            onClick={handlePrint}
                            className="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-blue-900 hover:bg-blue-950 text-white font-bold text-xs shadow-md shadow-blue-900/20 transition-all cursor-pointer hover:scale-[1.02]"
                        >
                            <Printer className="w-4 h-4" />
                            <span>Print Gate Pass / Save PDF</span>
                        </button>
                    </div>
                </div>

                {/* ========================================================================= */}
                {/* OFFICIAL CORPORATE GATE PASS DOCUMENT (SCREEN + PRINT OPTIMIZED)        */}
                {/* ========================================================================= */}
                <div className="bg-white dark:bg-slate-900 text-slate-900 dark:text-slate-100 rounded-3xl border border-slate-200 dark:border-slate-800 p-8 sm:p-10 shadow-sm print:p-0 print:border-none print:shadow-none print:bg-white print:text-black space-y-6">
                    
                    {/* 1. EXECUTIVE CORPORATE HEADER */}
                    <div className="flex flex-col sm:flex-row justify-between items-start gap-4 pb-5 border-b-2 border-blue-900 print:border-blue-950">
                        {/* Company Branding */}
                        <div className="flex items-start gap-3.5">
                            {/* Emblem */}
                            <div className="w-13 h-13 rounded-2xl bg-gradient-to-tr from-blue-900 to-indigo-800 text-white flex items-center justify-center font-black text-xl shadow-md shrink-0 border border-blue-700 print:border-black print:bg-blue-950">
                                NE
                            </div>
                            <div>
                                <h1 className="text-xl sm:text-2xl font-black tracking-tight text-blue-950 dark:text-blue-300 print:text-black">
                                    NATANEM ENGINEERING &amp; CONSTRUCTION PLC
                                </h1>
                                <div className="text-[11px] font-bold text-slate-600 dark:text-slate-400 uppercase tracking-wider print:text-black">
                                    ናታኔም ኢንጂነሪንግ እና ኮንስትራክሽን ኃ/የተ/የግ/ማህበር
                                </div>
                                <div className="text-[10px] font-semibold text-blue-800 dark:text-blue-400 tracking-wide mt-0.5 print:text-black">
                                    CENTRAL WAREHOUSE, ASSETS &amp; SITE LOGISTICS CLEARANCE DIVISION
                                </div>
                                <div className="text-[9px] text-slate-500 dark:text-slate-400 font-medium tracking-tight mt-1 print:text-slate-600">
                                    TIN: 0048291048 · VAT: 28491028 · Tel: +251 11 667 8900 · Bole Sub-City, Addis Ababa, Ethiopia
                                </div>
                            </div>
                        </div>

                        {/* Document Voucher Credentials Box */}
                        <div className="sm:text-right shrink-0 w-full sm:w-auto p-3 rounded-2xl border-2 border-blue-900/40 bg-blue-50/50 dark:bg-slate-800/60 dark:border-blue-700/50 print:bg-white print:border-slate-800">
                            <div className="text-[10px] font-black uppercase tracking-widest text-blue-900 dark:text-blue-300 print:text-black">
                                STORE GATE PASS / የመጋዘን እቃ መውጫ
                            </div>
                            <div className="text-xl font-mono font-black text-blue-950 dark:text-white print:text-black mt-0.5">
                                #{passId}
                            </div>
                            <div className="text-[10px] font-mono text-slate-600 dark:text-slate-300 print:text-black mt-0.5">
                                Issue Date: <span className="font-bold">{issueDate}</span>
                            </div>

                            {/* Barcode Visual Simulation */}
                            <div className="mt-2 flex flex-col items-end print:items-end">
                                <div className="h-6 w-36 flex items-center justify-between gap-[2px] overflow-hidden px-1 bg-white border border-slate-300 rounded">
                                    <div className="w-1 h-full bg-black"></div>
                                    <div className="w-[1px] h-full bg-black"></div>
                                    <div className="w-[2px] h-full bg-black"></div>
                                    <div className="w-[1px] h-full bg-black"></div>
                                    <div className="w-[3px] h-full bg-black"></div>
                                    <div className="w-[1px] h-full bg-black"></div>
                                    <div className="w-[2px] h-full bg-black"></div>
                                    <div className="w-[1px] h-full bg-black"></div>
                                    <div className="w-[3px] h-full bg-black"></div>
                                    <div className="w-[1px] h-full bg-black"></div>
                                    <div className="w-[2px] h-full bg-black"></div>
                                    <div className="w-[1px] h-full bg-black"></div>
                                    <div className="w-[2px] h-full bg-black"></div>
                                    <div className="w-[3px] h-full bg-black"></div>
                                    <div className="w-[1px] h-full bg-black"></div>
                                    <div className="w-[2px] h-full bg-black"></div>
                                    <div className="w-1 h-full bg-black"></div>
                                </div>
                                <span className="text-[8px] font-mono text-slate-500 tracking-widest mt-0.5">*{passId}*</span>
                            </div>
                        </div>
                    </div>

                    {/* 2. CLASSIFICATION & CLEARANCE STRIP */}
                    <div className="grid grid-cols-1 sm:grid-cols-3 border border-slate-300 dark:border-slate-700 rounded-xl overflow-hidden text-xs print:border-black">
                        <div className="p-2.5 bg-slate-50 dark:bg-slate-800/80 border-b sm:border-b-0 sm:border-r border-slate-300 dark:border-slate-700 print:bg-slate-100 print:border-black">
                            <span className="text-[9px] font-bold text-slate-500 uppercase tracking-wider block print:text-black">
                                Pass Classification
                            </span>
                            <span className="font-extrabold text-slate-900 dark:text-white print:text-black">
                                RETURNABLE EQUIPMENT / TOOL DISPATCH
                            </span>
                        </div>

                        <div className="p-2.5 bg-slate-50 dark:bg-slate-800/80 border-b sm:border-b-0 sm:border-r border-slate-300 dark:border-slate-700 print:bg-slate-100 print:border-black">
                            <span className="text-[9px] font-bold text-slate-500 uppercase tracking-wider block print:text-black">
                                Destination / Project Site
                            </span>
                            <span className="font-extrabold text-slate-900 dark:text-white print:text-black">
                                {purpose.includes('Site') ? purpose.split('.')[0] : 'Warsabi Site Project (Derba Store)'}
                            </span>
                        </div>

                        <div className="p-2.5 bg-slate-50 dark:bg-slate-800/80 flex items-center justify-between print:bg-slate-100">
                            <div>
                                <span className="text-[9px] font-bold text-slate-500 uppercase tracking-wider block print:text-black">
                                    Clearance Status
                                </span>
                                <span className={`font-black uppercase text-xs tracking-wider inline-flex items-center gap-1.5 ${
                                    isApproved
                                        ? 'text-emerald-700 dark:text-emerald-400 print:text-black'
                                        : isReturned
                                        ? 'text-blue-700 dark:text-blue-400 print:text-black'
                                        : isRejected
                                        ? 'text-rose-700 dark:text-rose-400 print:text-black'
                                        : 'text-amber-700 dark:text-amber-400 print:text-black'
                                }`}>
                                    {isApproved ? '✓ AUTHORIZED FOR EXIT' : isReturned ? '✓ CLOSED & RETURNED' : isRejected ? '✕ REJECTED / VOID' : '⏳ PENDING CLEARANCE'}
                                </span>
                            </div>
                            <span className="px-2 py-0.5 rounded text-[9px] font-black uppercase tracking-wider border border-slate-400 print:border-black">
                                COPY 1: GATE
                            </span>
                        </div>
                    </div>

                    {/* 3. STRUCTURED METADATA MATRIX (HIGH CONTRAST BORDERS) */}
                    <div className="border border-slate-300 dark:border-slate-700 rounded-xl overflow-hidden print:border-black">
                        <div className="grid grid-cols-1 sm:grid-cols-2 divide-y sm:divide-y-0 sm:divide-x divide-slate-300 dark:divide-slate-700 print:divide-black">
                            {/* Quadrant 1: Borrower Information */}
                            <div className="p-4 space-y-2">
                                <div className="text-[9px] font-bold uppercase tracking-wider text-slate-500 print:text-black">
                                    Recipient / Borrower Staff (ተረካቢ ሠራተኛ)
                                </div>
                                <div className="text-base font-black text-slate-900 dark:text-white print:text-black">
                                    {borrowerName}
                                </div>
                                <div className="text-xs text-slate-600 dark:text-slate-300 print:text-black">
                                    <span className="font-semibold">{positionName}</span> · <span>{departmentName}</span>
                                </div>
                                <div className="text-xs font-mono text-slate-500 dark:text-slate-400 print:text-black">
                                    Contact Phone: <span className="font-semibold text-slate-800 dark:text-slate-200 print:text-black">{phone}</span>
                                </div>
                            </div>

                            {/* Quadrant 2: Authority & Store Information */}
                            <div className="p-4 space-y-2 bg-slate-50/40 dark:bg-slate-800/20 print:bg-white">
                                <div className="text-[9px] font-bold uppercase tracking-wider text-slate-500 print:text-black">
                                    Dispatching Depot &amp; Authority (አስረካቢ መጋዘን)
                                </div>
                                <div className="text-base font-black text-slate-900 dark:text-white print:text-black">
                                    {storeLocation} Store
                                </div>
                                <div className="text-xs text-slate-600 dark:text-slate-300 print:text-black">
                                    Authorized by: <span className="font-bold">{loan.approved_by?.name || 'Managing Director / Store Supervisor'}</span>
                                </div>
                                <div className="text-xs font-mono text-slate-500 dark:text-slate-400 print:text-black">
                                    Department: <span className="font-semibold">Warehouse &amp; Logistics Control</span>
                                </div>
                            </div>
                        </div>

                        {/* Sub-row: Timeline & Purpose */}
                        <div className="border-t border-slate-300 dark:border-slate-700 grid grid-cols-1 sm:grid-cols-2 divide-y sm:divide-y-0 sm:divide-x divide-slate-300 dark:divide-slate-700 print:border-black print:divide-black text-xs">
                            <div className="p-3 bg-slate-50/70 dark:bg-slate-800/40 print:bg-slate-50">
                                <span className="text-[9px] font-bold text-slate-500 uppercase tracking-wider block print:text-black">
                                    Scheduled Return Timeline (የመመለሻ ቀን)
                                </span>
                                <div className="font-mono font-bold text-slate-900 dark:text-white print:text-black mt-0.5">
                                    {returnDate}
                                </div>
                                <div className="text-[10px] text-slate-500 print:text-slate-600 mt-0.5">
                                    Current Status: <span className="font-semibold">{returnedAt}</span>
                                </div>
                            </div>

                            <div className="p-3 bg-slate-50/70 dark:bg-slate-800/40 print:bg-slate-50">
                                <span className="text-[9px] font-bold text-slate-500 uppercase tracking-wider block print:text-black">
                                    Operational Purpose &amp; Work Description
                                </span>
                                <div className="font-medium text-slate-800 dark:text-slate-200 print:text-black mt-0.5">
                                    {purpose}
                                </div>
                            </div>
                        </div>
                    </div>

                    {/* 4. EQUIPMENT & MATERIAL DISPATCH MANIFEST TABLE */}
                    <div className="border-2 border-slate-800 dark:border-slate-700 rounded-xl overflow-hidden print:border-black">
                        <table className="w-full text-left text-xs border-collapse">
                            <thead>
                                <tr className="bg-blue-950 text-white dark:bg-slate-800 uppercase font-black text-[10px] tracking-wider border-b-2 border-slate-800 print:bg-blue-950 print:text-white">
                                    <th className="py-2.5 px-3 w-10 text-center border-r border-blue-900 print:border-white/20">No.</th>
                                    <th className="py-2.5 px-3 w-32 border-r border-blue-900 print:border-white/20">Item Code / SKU</th>
                                    <th className="py-2.5 px-3 border-r border-blue-900 print:border-white/20">Material / Equipment Description</th>
                                    <th className="py-2.5 px-3 w-28 border-r border-blue-900 print:border-white/20">Store Location</th>
                                    <th className="py-2.5 px-3 w-28 text-right border-r border-blue-900 print:border-white/20">Dispatched Qty</th>
                                    <th className="py-2.5 px-3 w-36 text-center">Exit Inspection</th>
                                </tr>
                            </thead>
                            <tbody className="divide-y divide-slate-300 dark:divide-slate-800 print:divide-black">
                                <tr className="bg-white dark:bg-slate-900 print:bg-white">
                                    <td className="py-3.5 px-3 text-center font-bold text-slate-500 border-r border-slate-300 dark:border-slate-800 print:text-black print:border-black">
                                        01
                                    </td>
                                    <td className="py-3.5 px-3 font-mono font-black text-blue-900 dark:text-blue-300 border-r border-slate-300 dark:border-slate-800 print:text-black print:border-black">
                                        {itemCode}
                                    </td>
                                    <td className="py-3.5 px-3 border-r border-slate-300 dark:border-slate-800 print:border-black">
                                        <div className="font-extrabold text-slate-900 dark:text-white text-sm print:text-black">
                                            {itemName}
                                        </div>
                                        <div className="text-[11px] text-slate-500 dark:text-slate-400 mt-0.5 print:text-slate-600">
                                            Heavy Machinery &amp; Site Plant · Registered Asset Code: {itemCode}
                                        </div>
                                    </td>
                                    <td className="py-3.5 px-3 text-slate-700 dark:text-slate-300 border-r border-slate-300 dark:border-slate-800 font-medium print:text-black print:border-black">
                                        {storeLocation}
                                    </td>
                                    <td className="py-3.5 px-3 text-right font-mono font-black text-lg text-blue-950 dark:text-blue-300 border-r border-slate-300 dark:border-slate-800 print:text-black print:border-black">
                                        {quantity} <span className="text-xs font-bold text-slate-600 print:text-black">{unit}</span>
                                    </td>
                                    <td className="py-3.5 px-3 text-center font-bold text-[11px] text-emerald-700 dark:text-emerald-400 print:text-black">
                                        ✓ Operational &amp; Inspected
                                    </td>
                                </tr>
                            </tbody>
                        </table>

                        {/* Dispatch Quality & Checklist Strip */}
                        <div className="bg-slate-50 dark:bg-slate-800/60 p-2.5 border-t border-slate-300 dark:border-slate-700 text-[10px] text-slate-600 dark:text-slate-300 flex flex-wrap items-center justify-between gap-3 print:bg-slate-100 print:border-black print:text-black">
                            <span className="font-bold uppercase tracking-wider text-slate-700 print:text-black">
                                Dispatch Verification Checklist:
                            </span>
                            <span>[✓] Serial / Asset Tag Verified</span>
                            <span>[✓] Mechanical &amp; Physical Check Passed</span>
                            <span>[✓] Operational Manual / Key Handed</span>
                            <span>[✓] Gate Log Registry Entry #GL-{String(loan.id).padStart(4, '0')}</span>
                        </div>
                    </div>

                    {/* 5. CARRIER, TRANSPORT & GATE OUTWARD DETAILS */}
                    <div className="border border-slate-300 dark:border-slate-700 rounded-xl p-3 bg-slate-50/60 dark:bg-slate-800/40 grid grid-cols-1 sm:grid-cols-3 gap-3 text-xs print:border-black print:bg-white">
                        <div>
                            <span className="text-[9px] font-bold text-slate-500 uppercase tracking-wider block print:text-black">
                                Transport Vehicle / Trailer Plate No.
                            </span>
                            <span className="font-mono font-bold text-slate-800 dark:text-white print:text-black mt-0.5 block">
                                ________________________________
                            </span>
                        </div>

                        <div>
                            <span className="text-[9px] font-bold text-slate-500 uppercase tracking-wider block print:text-black">
                                Driver / Carrier Personnel Name
                            </span>
                            <span className="font-mono font-bold text-slate-800 dark:text-white print:text-black mt-0.5 block">
                                ________________________________
                            </span>
                        </div>

                        <div>
                            <span className="text-[9px] font-bold text-slate-500 uppercase tracking-wider block print:text-black">
                                Gate Outward Timestamp
                            </span>
                            <span className="font-mono font-semibold text-slate-700 dark:text-slate-300 print:text-black mt-0.5 block">
                                Date: ____________ Time: _______ hrs
                            </span>
                        </div>
                    </div>

                    {/* 6. CONDITIONS OF CUSTODY & STATUTORY CLAUSE */}
                    <div className="p-3 rounded-xl border border-slate-200 dark:border-slate-800 bg-slate-50/50 dark:bg-slate-800/30 text-[9px] text-slate-600 dark:text-slate-400 space-y-1 print:border-slate-400 print:bg-white print:text-slate-700">
                        <div className="font-bold uppercase tracking-wider text-slate-800 dark:text-slate-300 print:text-black">
                            Terms of Equipment Clearance &amp; Custody Responsibility:
                        </div>
                        <p>
                            1. Dispatched materials, machinery, and tools remain the exclusive corporate property of Natanem Engineering &amp; Construction PLC.
                        </p>
                        <p>
                            2. The designated recipient / borrower assumes full financial, mechanical, and operational custody until the asset is formally checked back into the central warehouse inventory.
                        </p>
                        <p>
                            3. Security gate personnel are legally authorized to detain any material movement lacking an authorized signatory signature and official corporate seal.
                        </p>
                    </div>

                    {/* 7. QUADRUPLE OFFICIAL SIGNATURE & STAMP BOXES */}
                    <div className="grid grid-cols-2 sm:grid-cols-4 gap-3 pt-2">
                        {/* 1. Storekeeper */}
                        <div className="border border-slate-300 dark:border-slate-700 rounded-xl p-3 text-center flex flex-col justify-between h-36 print:border-black">
                            <span className="text-[9px] font-black text-slate-600 uppercase tracking-wider block print:text-black">
                                1. STOREKEEPER (ISSUED BY)
                            </span>
                            <div className="h-10 my-1 border border-dashed border-slate-300 dark:border-slate-600 rounded flex items-center justify-center text-[9px] text-slate-400 print:border-slate-400 print:text-slate-500">
                                [ STORE STAMP / ማህተም ]
                            </div>
                            <div>
                                <div className="border-t border-slate-400 dark:border-slate-600 pt-1 text-xs font-bold text-slate-900 dark:text-white print:text-black">
                                    Central Storekeeper
                                </div>
                                <span className="text-[9px] text-slate-400 print:text-black">Signature &amp; Date</span>
                            </div>
                        </div>

                        {/* 2. Borrower / Custodian */}
                        <div className="border border-slate-300 dark:border-slate-700 rounded-xl p-3 text-center flex flex-col justify-between h-36 print:border-black">
                            <span className="text-[9px] font-black text-slate-600 uppercase tracking-wider block print:text-black">
                                2. RECIPIENT (BORROWER)
                            </span>
                            <div className="my-auto text-[8px] text-slate-500 italic leading-tight px-1 print:text-slate-600">
                                "I acknowledge receipt in good order."
                            </div>
                            <div>
                                <div className="border-t border-slate-400 dark:border-slate-600 pt-1 text-xs font-bold text-slate-900 dark:text-white print:text-black truncate">
                                    {borrowerName}
                                </div>
                                <span className="text-[9px] text-slate-400 print:text-black">Signature &amp; Date</span>
                            </div>
                        </div>

                        {/* 3. Approving Authority */}
                        <div className="border border-slate-300 dark:border-slate-700 rounded-xl p-3 text-center flex flex-col justify-between h-36 print:border-black">
                            <span className="text-[9px] font-black text-slate-600 uppercase tracking-wider block print:text-black">
                                3. AUTHORIZED BY (MANAGER)
                            </span>
                            <div className="h-10 my-1 border border-dashed border-slate-300 dark:border-slate-600 rounded flex items-center justify-center text-[9px] text-slate-400 print:border-slate-400 print:text-slate-500">
                                [ APPROVED SEAL ]
                            </div>
                            <div>
                                <div className="border-t border-slate-400 dark:border-slate-600 pt-1 text-xs font-bold text-slate-900 dark:text-white print:text-black truncate">
                                    {loan.approved_by?.name || 'Project Manager'}
                                </div>
                                <span className="text-[9px] text-slate-400 print:text-black">Signature &amp; Date</span>
                            </div>
                        </div>

                        {/* 4. Gate Security Clearance */}
                        <div className="border border-slate-300 dark:border-slate-700 rounded-xl p-3 text-center flex flex-col justify-between h-36 print:border-black">
                            <span className="text-[9px] font-black text-slate-600 uppercase tracking-wider block print:text-black">
                                4. SECURITY CLEARANCE
                            </span>
                            <div className="h-10 my-1 border border-dashed border-slate-300 dark:border-slate-600 rounded flex items-center justify-center text-[9px] text-slate-400 print:border-slate-400 print:text-slate-500">
                                [ GATE PASSED STAMP ]
                            </div>
                            <div>
                                <div className="border-t border-slate-400 dark:border-slate-600 pt-1 text-xs font-bold text-slate-900 dark:text-white print:text-black">
                                    Site Gate Officer
                                </div>
                                <span className="text-[9px] text-slate-400 print:text-black">Outward Logged &amp; Signed</span>
                            </div>
                        </div>
                    </div>

                    {/* 8. SECURITY MICRO-FOOTER */}
                    <div className="pt-3 border-t border-dashed border-slate-300 dark:border-slate-800 flex flex-col sm:flex-row items-center justify-between text-[9px] text-slate-400 print:text-slate-600 print:border-black">
                        <div>
                            Official document issued by Natanem Engineering ERP • Ref: <span className="font-mono font-bold">NE/LOG/GP-{String(loan.id).padStart(5, '0')}</span>
                        </div>
                        <div className="font-mono">
                            Original Gate Security Clearance Copy • Valid Only with Official Corporate Seal
                        </div>
                    </div>
                </div>
            </div>
        </AuthenticatedLayout>
    );
}
