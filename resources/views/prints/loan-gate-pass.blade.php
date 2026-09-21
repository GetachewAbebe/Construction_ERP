<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Store Gate Pass #PASS-{{ str_pad((string) $loan->id, 5, '0', STR_PAD_LEFT) }} - Natanem Engineering</title>
    <style>
        @page {
            size: A4 portrait;
            margin: 8mm 10mm;
        }
        * { box-sizing: border-box; margin: 0; padding: 0; font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif; }
        body { background: #f1f5f9; padding: 24px; color: #0f172a; -webkit-print-color-adjust: exact; print-color-adjust: exact; }
        .no-print { text-align: center; margin-bottom: 16px; }
        .print-btn { background: #1e3a8a; color: #fff; border: none; padding: 10px 22px; font-size: 13px; font-weight: 700; border-radius: 8px; cursor: pointer; box-shadow: 0 2px 4px rgba(0,0,0,0.1); }
        .print-btn:hover { background: #172554; }
        .voucher-container { max-width: 860px; margin: 0 auto; background: #fff; padding: 32px; border-radius: 12px; box-shadow: 0 4px 12px rgba(0,0,0,0.06); border: 1px solid #e2e8f0; }

        /* Header */
        .header { display: flex; justify-content: space-between; align-items: flex-start; border-bottom: 2.5px solid #1e3a8a; padding-bottom: 16px; margin-bottom: 16px; }
        .brand-block { display: flex; align-items: flex-start; gap: 12px; }
        .brand-emblem { width: 50px; height: 50px; background: #1e3a8a; color: #fff; border-radius: 10px; display: flex; align-items: center; justify-content: center; font-weight: 900; font-size: 20px; letter-spacing: -1px; shrink: 0; }
        .brand-info h1 { font-size: 19px; font-weight: 900; color: #1e3a8a; letter-spacing: -0.3px; line-height: 1.2; }
        .brand-info .amharic { font-size: 11px; font-weight: 700; color: #334155; margin-top: 1px; }
        .brand-info .division { font-size: 10px; font-weight: 700; color: #475569; letter-spacing: 0.5px; text-transform: uppercase; margin-top: 2px; }
        .brand-info .statutory { font-size: 9px; color: #64748b; margin-top: 3px; }

        .pass-badge-box { text-align: right; border: 2px solid #1e3a8a; padding: 10px 14px; border-radius: 8px; background: #f8fafc; }
        .pass-badge-box .title { font-size: 10px; font-weight: 900; color: #1e3a8a; text-transform: uppercase; letter-spacing: 0.8px; }
        .pass-badge-box .pass-id { font-family: monospace; font-size: 18px; font-weight: 900; color: #0f172a; margin-top: 2px; }
        .pass-badge-box .date { font-size: 10px; color: #475569; font-family: monospace; margin-top: 2px; }
        .barcode { display: flex; justify-content: flex-end; gap: 2px; height: 20px; width: 130px; margin-top: 6px; }
        .barcode div { background: #000; height: 100%; }

        /* Classification Strip */
        .strip { display: grid; grid-template-columns: 1fr 1fr 1fr; border: 1px solid #cbd5e1; border-radius: 6px; background: #f8fafc; margin-bottom: 16px; overflow: hidden; }
        .strip-item { padding: 8px 12px; border-right: 1px solid #cbd5e1; font-size: 11px; }
        .strip-item:last-child { border-right: none; }
        .strip-label { font-size: 9px; font-weight: 700; color: #64748b; text-transform: uppercase; display: block; margin-bottom: 2px; }
        .strip-val { font-weight: 800; color: #0f172a; }

        /* Matrix */
        .matrix { border: 1px solid #cbd5e1; border-radius: 6px; overflow: hidden; margin-bottom: 16px; font-size: 12px; }
        .matrix-row { display: grid; grid-template-columns: 1fr 1fr; border-bottom: 1px solid #cbd5e1; }
        .matrix-row:last-child { border-bottom: none; }
        .matrix-col { padding: 12px 14px; border-right: 1px solid #cbd5e1; }
        .matrix-col:last-child { border-right: none; }
        .matrix-label { font-size: 9px; font-weight: 800; color: #64748b; text-transform: uppercase; margin-bottom: 4px; display: block; }
        .matrix-main { font-size: 14px; font-weight: 800; color: #0f172a; }
        .matrix-sub { font-size: 11px; color: #475569; margin-top: 2px; }

        /* Table */
        .table-wrap { border: 2px solid #0f172a; border-radius: 6px; overflow: hidden; margin-bottom: 14px; }
        .table-box { width: 100%; border-collapse: collapse; font-size: 12px; text-align: left; }
        .table-box th { background: #0f172a; color: #fff; padding: 8px 12px; font-size: 10px; font-weight: 800; text-transform: uppercase; letter-spacing: 0.5px; border-right: 1px solid #334155; }
        .table-box th:last-child { border-right: none; }
        .table-box td { padding: 12px; border-right: 1px solid #e2e8f0; border-bottom: 1px solid #e2e8f0; }
        .table-box td:last-child { border-right: none; }
        .table-checklist { background: #f8fafc; padding: 8px 12px; font-size: 10px; color: #475569; display: flex; justify-content: space-between; border-top: 1px solid #cbd5e1; }

        /* Carrier Details */
        .carrier-strip { border: 1px solid #cbd5e1; border-radius: 6px; padding: 10px 14px; background: #f8fafc; display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 12px; font-size: 11px; margin-bottom: 14px; }
        .carrier-field .label { font-size: 9px; font-weight: 700; color: #64748b; text-transform: uppercase; margin-bottom: 2px; }
        .carrier-field .val { font-weight: 700; color: #1e293b; font-family: monospace; }

        /* Terms */
        .terms { border: 1px solid #e2e8f0; border-radius: 6px; padding: 8px 12px; font-size: 9px; color: #475569; line-height: 1.4; margin-bottom: 16px; background: #fafafa; }
        .terms strong { color: #0f172a; text-transform: uppercase; }

        /* Signatures */
        .signatures { display: grid; grid-template-columns: 1fr 1fr 1fr 1fr; gap: 10px; margin-bottom: 16px; }
        .sig-card { border: 1px solid #cbd5e1; border-radius: 6px; padding: 10px; text-align: center; height: 130px; display: flex; flex-direction: column; justify-content: space-between; }
        .sig-role { font-size: 9px; font-weight: 800; color: #475569; text-transform: uppercase; }
        .sig-stamp { border: 1px dashed #cbd5e1; border-radius: 4px; font-size: 8px; color: #94a3b8; height: 40px; display: flex; align-items: center; justify-content: center; }
        .sig-name { border-top: 1px solid #94a3b8; padding-top: 4px; font-size: 11px; font-weight: 700; color: #0f172a; }
        .sig-caption { font-size: 8px; color: #64748b; }

        /* Footer */
        .footer { border-top: 1px dashed #94a3b8; padding-top: 8px; font-size: 9px; color: #64748b; display: flex; justify-content: space-between; font-family: monospace; }

        @media print {
            body { background: #fff; padding: 0; }
            .voucher-container { box-shadow: none; padding: 0; border: none; max-width: 100%; }
            .no-print { display: none; }
        }
    </style>
</head>
<body>
    <div class="no-print">
        <button onclick="window.print()" class="print-btn">🖨️ Print Store Gate Pass / Save PDF</button>
    </div>

    @php
        $passId = 'PASS-' . str_pad((string) $loan->id, 5, '0', STR_PAD_LEFT);
        $borrowerName = $loan->employee->name ?? (trim(($loan->employee->first_name ?? '') . ' ' . ($loan->employee->last_name ?? '')) ?: ($loan->user->name ?? 'Site Personnel'));
        $positionName = $loan->employee->position_rel->name ?? ($loan->employee->position ?? 'Site Personnel');
        $departmentName = $loan->employee->department_rel->name ?? ($loan->employee->department ?? 'Operations');
        $phone = $loan->employee->phone ?? '—';
        $itemCode = $loan->inventoryItem->item_no ?? ('ITM-' . str_pad((string) ($loan->inventory_item_id ?? 0), 4, '0', STR_PAD_LEFT));
        $itemName = $loan->inventoryItem->name ?? 'Material Asset';
        $storeLocation = $loan->inventoryItem->store_location ?? 'Central Warehouse';
        $unit = $loan->inventoryItem->unit_of_measurement ?? 'pcs';
        $issueDate = $loan->requested_at ? $loan->requested_at->format('F d, Y') : ($loan->created_at ? $loan->created_at->format('F d, Y') : now()->format('F d, Y'));
        $dueDate = $loan->due_date ? $loan->due_date->format('F d, Y') : ($loan->expected_return_date ? $loan->expected_return_date->format('F d, Y') : 'Indefinite / Project Duration');
        $purpose = $loan->remarks ?: ($loan->notes ?: 'Site Construction Operations and Equipment Requisition.');
    @endphp

    <div class="voucher-container">
        <!-- Header -->
        <div class="header">
            <div class="brand-block">
                <div class="brand-emblem">NE</div>
                <div class="brand-info">
                    <h1>NATANEM ENGINEERING &amp; CONSTRUCTION PLC</h1>
                    <div class="amharic">ናታኔም ኢንጂነሪንግ እና ኮንስትራክሽን ኃ/የተ/የግ/ማህበር</div>
                    <div class="division">CENTRAL WAREHOUSE, ASSETS &amp; SITE LOGISTICS CLEARANCE DIVISION</div>
                    <div class="statutory">TIN: 0048291048 · VAT: 28491028 · Tel: +251 11 667 8900 · Bole Sub-City, Addis Ababa, Ethiopia</div>
                </div>
            </div>

            <div class="pass-badge-box" style="display: flex; align-items: center; gap: 14px; text-align: left;">
                @if(!empty($qrCodeSvg))
                    <div style="text-align: center; flex-shrink: 0;">
                        {!! $qrCodeSvg !!}
                        <div style="font-size: 7.5px; font-weight: 800; color: #0f172a; margin-top: 2px; letter-spacing: 0.5px;">SCAN TO VERIFY</div>
                    </div>
                @endif
                <div>
                    <div class="title">MATERIAL GATE PASS / የመጋዘን እቃ መውጫ</div>
                    <div class="pass-id">#{{ $passId }}</div>
                    <div class="date">Issue Date: <strong>{{ $issueDate }}</strong></div>
                </div>
            </div>
        </div>

        <!-- Classification Strip -->
        <div class="strip">
            <div class="strip-item">
                <span class="strip-label">Pass Classification</span>
                <span class="strip-val">RETURNABLE EQUIPMENT / TOOL DISPATCH</span>
            </div>
            <div class="strip-item">
                <span class="strip-label">Destination / Site</span>
                <span class="strip-val">{{ Str::contains($purpose, 'Site') ? Str::before($purpose, '.') : 'Warsabi Site Project (Derba Depot)' }}</span>
            </div>
            <div class="strip-item">
                <span class="strip-label">Clearance Status</span>
                <span class="strip-val" style="color: {{ $loan->status === 'approved' ? '#059669' : ($loan->status === 'returned' ? '#2563eb' : '#d97706') }};">
                    {{ $loan->status === 'approved' ? '✓ AUTHORIZED FOR EXIT' : ($loan->status === 'returned' ? '✓ CLOSED & RETURNED' : '⏳ PENDING CLEARANCE') }}
                </span>
            </div>
        </div>

        <!-- Matrix -->
        <div class="matrix">
            <div class="matrix-row">
                <div class="matrix-col">
                    <span class="matrix-label">Recipient / Borrower Staff (ተረካቢ ሠራተኛ)</span>
                    <div class="matrix-main">{{ $borrowerName }}</div>
                    <div class="matrix-sub">{{ $positionName }} · {{ $departmentName }} (Tel: {{ $phone }})</div>
                </div>
                <div class="matrix-col">
                    <span class="matrix-label">Dispatching Depot &amp; Authority (አስረካቢ መጋዘን)</span>
                    <div class="matrix-main">{{ $storeLocation }} Store</div>
                    <div class="matrix-sub">Authorized by: <strong>{{ $loan->approvedBy->name ?? 'Managing Director / Store Supervisor' }}</strong></div>
                </div>
            </div>
            <div class="matrix-row">
                <div class="matrix-col">
                    <span class="matrix-label">Scheduled Return Timeline (የመመለሻ ቀን)</span>
                    <div class="matrix-main" style="font-family: monospace;">{{ $dueDate }}</div>
                    <div class="matrix-sub">Status: {{ $loan->returned_at ? 'Returned on ' . $loan->returned_at->format('F d, Y') : 'Pending Return (In Field)' }}</div>
                </div>
                <div class="matrix-col">
                    <span class="matrix-label">Operational Purpose &amp; Work Description</span>
                    <div class="matrix-sub" style="font-weight: 500; color: #1e293b;">{{ $purpose }}</div>
                </div>
            </div>
        </div>

        <!-- Table -->
        <div class="table-wrap">
            <table class="table-box">
                <thead>
                    <tr>
                        <th style="width: 40px; text-align: center;">No.</th>
                        <th style="width: 130px;">Item Code / SKU</th>
                        <th>Equipment / Material Description &amp; Model</th>
                        <th style="width: 120px;">Store Depot</th>
                        <th style="width: 110px; text-align: right;">Dispatched Qty</th>
                        <th style="width: 140px; text-align: center;">Exit Inspection</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td style="text-align: center; font-weight: bold; color: #64748b;">01</td>
                        <td style="font-family: monospace; font-weight: bold; color: #1e3a8a;">{{ $itemCode }}</td>
                        <td>
                            <div style="font-weight: 800; color: #0f172a; font-size: 13px;">{{ $itemName }}</div>
                            <div style="font-size: 11px; color: #64748b;">Heavy Machinery &amp; Site Plant · Asset Tag: {{ $itemCode }}</div>
                        </td>
                        <td style="font-weight: 600; color: #334155;">{{ $storeLocation }}</td>
                        <td style="text-align: right; font-family: monospace; font-size: 16px; font-weight: 900; color: #1e3a8a;">
                            {{ $loan->quantity }} <span style="font-size: 11px; font-weight: 600; color: #475569;">{{ $unit }}</span>
                        </td>
                        <td style="text-align: center; font-weight: 700; font-size: 11px; color: #059669;">✓ Operational &amp; Inspected</td>
                    </tr>
                </tbody>
            </table>
            <div class="table-checklist">
                <span><strong>Verification Checklist:</strong> [✓] Serial / Asset Tag Verified</span>
                <span>[✓] Mechanical &amp; Physical Check Passed</span>
                <span>[✓] Operational Key / Manual Handed</span>
                <span>[✓] Registry Entry #GL-{{ str_pad((string) $loan->id, 4, '0', STR_PAD_LEFT) }}</span>
            </div>
        </div>

        <!-- Carrier Info -->
        <div class="carrier-strip">
            <div class="carrier-field">
                <div class="label">Transport Vehicle Plate No.</div>
                <div class="val">________________________________</div>
            </div>
            <div class="carrier-field">
                <div class="label">Driver / Carrier Name</div>
                <div class="val">________________________________</div>
            </div>
            <div class="carrier-field">
                <div class="label">Gate Outward Timestamp</div>
                <div class="val" style="font-size: 10px;">Date: ____________ Time: _______ hrs</div>
            </div>
        </div>

        <!-- Terms -->
        <div class="terms">
            <strong>Terms of Equipment Clearance &amp; Custody Responsibility:</strong><br>
            1. Dispatched assets remain the exclusive corporate property of Natanem Engineering &amp; Construction PLC.<br>
            2. The designated recipient assumes full financial and operational custody until formal warehouse check-in.<br>
            3. Security gate personnel are required to detain items without an authorized signature and official corporate seal.
        </div>

        <!-- Signatures -->
        <div class="signatures">
            <div class="sig-card">
                <div class="sig-role">1. Storekeeper (Issued By)</div>
                <div class="sig-stamp">[ STORE STAMP / ማህተም ]</div>
                <div class="sig-name">Central Storekeeper</div>
                <div class="sig-caption">Signature &amp; Date</div>
            </div>
            <div class="sig-card">
                <div class="sig-role">2. Recipient (Borrower)</div>
                <div style="font-size: 8px; color: #64748b; font-style: italic; margin: auto;">"Received in inspected good order."</div>
                <div class="sig-name">{{ $borrowerName }}</div>
                <div class="sig-caption">Signature &amp; Date</div>
            </div>
            <div class="sig-card">
                <div class="sig-role">3. Authorized By (Manager)</div>
                <div class="sig-stamp">[ APPROVED SEAL ]</div>
                <div class="sig-name">{{ $loan->approvedBy->name ?? 'Project Manager' }}</div>
                <div class="sig-caption">Signature &amp; Date</div>
            </div>
            <div class="sig-card">
                <div class="sig-role">4. Security Clearance</div>
                <div class="sig-stamp">[ GATE PASSED STAMP ]</div>
                <div class="sig-name">Site Gate Officer</div>
                <div class="sig-caption">Outward Logged &amp; Signed</div>
            </div>
        </div>

        <!-- Footer -->
        <div class="footer">
            <div>Official document issued by Natanem Engineering ERP • Ref: NE/LOG/GP-{{ str_pad((string) $loan->id, 5, '0', STR_PAD_LEFT) }}</div>
            <div>Original Gate Security Copy • Valid Only with Official Seal</div>
        </div>
    </div>
</body>
</html>
