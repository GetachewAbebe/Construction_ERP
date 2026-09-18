<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Store Gate Pass #LN-{{ str_pad((string) $loan->id, 5, '0', STR_PAD_LEFT) }} - Natanem Engineering</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; }
        body { background: #f3f4f6; padding: 30px 15px; color: #1f2937; }
        .voucher-container { max-width: 800px; margin: 0 auto; background: #fff; padding: 40px; border-radius: 8px; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1); }
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
        .table-box { width: 100%; border-collapse: collapse; margin-bottom: 30px; }
        .table-box th { background: #f0fdf4; border: 1px solid #d1fae5; padding: 10px; font-size: 11px; text-transform: uppercase; color: #065f46; text-align: left; }
        .table-box td { border: 1px solid #e5e7eb; padding: 12px 10px; font-size: 13px; color: #1f2937; }
        .signatures { display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 20px; margin-top: 50px; }
        .sig-box { border-top: 1px solid #9ca3af; padding-top: 10px; text-align: center; }
        .sig-box .role { font-size: 11px; font-weight: bold; color: #4b5563; text-transform: uppercase; }
        .sig-box .name { font-size: 12px; color: #111827; margin-top: 4px; }
        .footer { margin-top: 40px; border-top: 1px dashed #d1d5db; padding-top: 15px; font-size: 10px; color: #9ca3af; text-align: center; }
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
        <button onclick="window.print()" class="print-btn">🖨️ Print Store Gate Pass / Save PDF</button>
    </div>

    <div class="voucher-container">
        <div class="header">
            <div class="logo-title">
                <h1>NATANEM ENGINEERING</h1>
                <p>Warehouse, Central Store & Asset Dispatch</p>
            </div>
            <div class="voucher-meta">
                <div class="badge">MATERIAL GATE PASS</div>
                <p>#PASS-{{ str_pad((string) $loan->id, 5, '0', STR_PAD_LEFT) }}</p>
                <p>Issue Date: {{ $loan->created_at ? $loan->created_at->format('F d, Y') : now()->format('F d, Y') }}</p>
            </div>
        </div>

        <div class="grid">
            <div class="field-group">
                <div class="label">Recipient / Borrower</div>
                <div class="value">{{ $loan->employee->name ?? ($loan->user->name ?? 'Site Staff') }}</div>
            </div>
            <div class="field-group">
                <div class="label">Target Site / Purpose</div>
                <div class="value">{{ $loan->remarks ?? $loan->notes ?? 'Site Construction Operations' }}</div>
            </div>
            <div class="field-group">
                <div class="label">Expected Return Date</div>
                <div class="value">{{ $loan->expected_return_date ? $loan->expected_return_date->format('F d, Y') : 'Consumable / Not Applicable' }}</div>
            </div>
            <div class="field-group">
                <div class="label">Authorization Status</div>
                <div class="value" style="color: {{ $loan->status === 'approved' ? '#059669' : '#d97706' }};">
                    {{ strtoupper($loan->status) }}
                </div>
            </div>
        </div>

        <table class="table-box">
            <thead>
                <tr>
                    <th>Item Number</th>
                    <th>Material / Tool Description</th>
                    <th>Store Location</th>
                    <th style="text-align: right;">Dispatched Qty</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td style="font-family: monospace; font-weight: bold;">{{ $loan->inventoryItem->item_no ?? 'ITM-000' }}</td>
                    <td>
                        <div style="font-weight: 600;">{{ $loan->inventoryItem->name ?? 'Material Asset' }}</div>
                        <div style="font-size: 11px; color: #6b7280;">{{ $loan->notes ?? 'Standard warehouse dispatch' }}</div>
                    </td>
                    <td>{{ $loan->inventoryItem->store_location ?? 'Central Warehouse' }}</td>
                    <td style="text-align: right; font-weight: bold; font-size: 15px;">
                        {{ $loan->quantity }} {{ $loan->inventoryItem->unit_of_measurement ?? 'Units' }}
                    </td>
                </tr>
            </tbody>
        </table>

        <div class="signatures">
            <div class="sig-box">
                <div class="role">Storekeeper / Issued By</div>
                <div class="name">Storekeeper</div>
            </div>
            <div class="sig-box">
                <div class="role">Received By (Borrower)</div>
                <div class="name">{{ $loan->employee->name ?? ($loan->user->name ?? 'Staff') }}</div>
            </div>
            <div class="sig-box">
                <div class="role">Security / Gate Clearance</div>
                <div class="name">Site Gate Guard</div>
            </div>
        </div>

        <div class="footer">
            Official material movement pass issued by Natanem Engineering ERP on {{ now()->format('Y-m-d H:i:s') }}. Required for site gate check.
        </div>
    </div>
</body>
</html>
