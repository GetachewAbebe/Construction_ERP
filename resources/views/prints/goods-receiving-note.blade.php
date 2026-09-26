<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Store Receiving Voucher #{{ $note->grn_no }} - Natanem Engineering</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; }
        body { background: #f3f4f6; padding: 30px 15px; color: #1f2937; }
        .voucher-container { max-width: 840px; margin: 0 auto; background: #fff; padding: 40px; border-radius: 8px; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1); }
        .header { display: flex; justify-content: space-between; align-items: flex-start; border-bottom: 2px solid #047857; padding-bottom: 20px; margin-bottom: 25px; }
        .logo-title h1 { font-size: 22px; font-weight: 800; color: #047857; letter-spacing: 0.5px; }
        .logo-title p { font-size: 11px; color: #6b7280; text-transform: uppercase; margin-top: 3px; }
        .voucher-meta { text-align: right; }
        .voucher-meta .badge { font-size: 18px; font-weight: bold; color: #047857; }
        .voucher-meta p { font-size: 12px; color: #4b5563; margin-top: 4px; }
        .grid { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 25px; }
        .field-group { background: #f9fafb; padding: 12px 16px; border-radius: 6px; border: 1px solid #e5e7eb; }
        .field-group .label { font-size: 10px; font-weight: 700; color: #6b7280; text-transform: uppercase; margin-bottom: 4px; }
        .field-group .value { font-size: 14px; font-weight: 600; color: #111827; }
        .table-container { margin-bottom: 25px; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th { background: #047857; color: #fff; font-size: 11px; text-transform: uppercase; padding: 10px 12px; text-align: left; }
        td { border-bottom: 1px solid #e5e7eb; padding: 10px 12px; font-size: 13px; color: #374151; }
        .remarks-box { background: #f9fafb; border: 1px solid #e5e7eb; border-radius: 6px; padding: 14px 16px; margin-top: 20px; font-size: 12px; color: #4b5563; }
        .signatures { display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 20px; margin-top: 45px; }
        .sig-box { border-top: 1px solid #9ca3af; padding-top: 8px; text-align: center; }
        .sig-box .role { font-size: 11px; font-weight: bold; color: #4b5563; text-transform: uppercase; }
        .sig-box .name { font-size: 12px; color: #111827; margin-top: 4px; }
        .footer { margin-top: 35px; border-top: 1px dashed #d1d5db; padding-top: 15px; font-size: 10px; color: #9ca3af; text-align: center; }
        .no-print { text-align: center; margin-bottom: 20px; }
        .print-btn { background: #047857; color: #fff; border: none; padding: 10px 24px; font-size: 14px; font-weight: bold; border-radius: 6px; cursor: pointer; }
        @media print {
            body { background: #fff; padding: 0; }
            .voucher-container { box-shadow: none; padding: 20px; }
            .no-print { display: none; }
        }
    </style>
</head>
<body>
    <div class="no-print">
        <button onclick="window.print()" class="print-btn">🖨️ Print Store Receiving Voucher / Save PDF</button>
    </div>

    <div class="voucher-container">
        <div class="header">
            <div class="logo-title">
                <h1>NATANEM ENGINEERING</h1>
                <p>Store Receiving Voucher (SRV / GRN)</p>
                <div style="font-size: 11px; color: #4b5563; margin-top: 6px;">Warehouse & Jobsite Material Receiving Department</div>
            </div>
            <div class="voucher-meta" style="display: flex; align-items: center; gap: 16px;">
                @if(!empty($qrCodeSvg))
                    <div style="text-align: center;">
                        {!! $qrCodeSvg !!}
                        <div style="font-size: 8px; font-weight: 700; color: #047857; margin-top: 2px; letter-spacing: 0.5px;">SCAN TO VERIFY</div>
                    </div>
                @endif
                <div>
                    <div class="badge">GOODS RECEIVING NOTE</div>
                    <p><strong>GRN #:</strong> {{ $note->grn_no }}</p>
                    <p><strong>Date Received:</strong> {{ $note->received_date ? $note->received_date->format('F d, Y') : now()->format('F d, Y') }}</p>
                    <p><strong>PO Reference:</strong> {{ $note->purchaseOrder->po_no }}</p>
                </div>
            </div>
        </div>

        <div class="grid">
            <div class="field-group">
                <div class="label">Delivering Supplier</div>
                <div class="value">{{ $note->vendor->name }}</div>
                <div style="font-size: 12px; color: #4b5563; margin-top: 4px;">
                    Vendor Delivery Note / Waybill #: <strong>{{ $note->delivery_note_no ?? 'N/A' }}</strong><br>
                    Vendor Code: {{ $note->vendor->code }}
                </div>
            </div>
            <div class="field-group">
                <div class="label">Receiving Project / Location</div>
                <div class="value">{{ $note->project ? $note->project->name : 'Central Warehouse' }}</div>
                <div style="font-size: 12px; color: #4b5563; margin-top: 4px;">
                    Location: {{ $note->project?->location ?? 'Central Warehouse' }}<br>
                    Received By: {{ $note->receiver ? $note->receiver->name : 'Site Storekeeper' }}
                </div>
            </div>
        </div>

        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th style="width: 5%;">#</th>
                        <th style="width: 45%;">Item / Material Description</th>
                        <th style="width: 15%; text-align: right;">Delivered</th>
                        <th style="width: 15%; text-align: right;">Accepted</th>
                        <th style="width: 20%; text-align: right;">Rejected & Reason</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($note->items as $index => $item)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>
                                <strong>{{ $item->item_name }}</strong>
                            </td>
                            <td style="text-align: right;">{{ number_format((float)$item->quantity_delivered, 2) }} {{ $item->unit_of_measurement }}</td>
                            <td style="text-align: right; font-weight: bold; color: #047857;">{{ number_format((float)$item->quantity_accepted, 2) }} {{ $item->unit_of_measurement }}</td>
                            <td style="text-align: right; color: {{ (float)$item->quantity_rejected > 0 ? '#b91c1c' : '#6b7280' }};">
                                @if((float)$item->quantity_rejected > 0)
                                    {{ number_format((float)$item->quantity_rejected, 2) }} {{ $item->unit_of_measurement }}
                                    <div style="font-size: 11px;">({{ $item->rejection_reason ?: 'Damaged / Non-conforming' }})</div>
                                @else
                                    0.00
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        @if($note->remarks)
            <div class="remarks-box">
                <strong>Storekeeper Inspection & Remarks:</strong><br>
                {{ $note->remarks }}
            </div>
        @endif

        <div class="signatures">
            <div class="sig-box">
                <div class="role">Storekeeper / Received By</div>
                <div class="name">{{ $note->receiver ? $note->receiver->name : 'Site Storekeeper' }}</div>
            </div>
            <div class="sig-box">
                <div class="role">Delivered By (Driver / Carrier)</div>
                <div class="name">Carrier Representative</div>
            </div>
            <div class="sig-box">
                <div class="role">Quality Inspection / Verified By</div>
                <div class="name">Site Engineer / Project Manager</div>
            </div>
        </div>

        <div class="footer">
            Certified Goods Receiving Voucher • Natanem Engineering Enterprise ERP • Quantities automatically posted to stock
        </div>
    </div>
</body>
</html>
