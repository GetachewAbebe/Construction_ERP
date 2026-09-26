<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Purchase Order #{{ $order->po_no }} - Natanem Engineering</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; }
        body { background: #f3f4f6; padding: 30px 15px; color: #1f2937; }
        .voucher-container { max-width: 840px; margin: 0 auto; background: #fff; padding: 40px; border-radius: 8px; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1); }
        .header { display: flex; justify-content: space-between; align-items: flex-start; border-bottom: 2px solid #1e3a8a; padding-bottom: 20px; margin-bottom: 25px; }
        .logo-title h1 { font-size: 22px; font-weight: 800; color: #1e3a8a; letter-spacing: 0.5px; }
        .logo-title p { font-size: 11px; color: #6b7280; text-transform: uppercase; margin-top: 3px; }
        .voucher-meta { text-align: right; }
        .voucher-meta .badge { font-size: 18px; font-weight: bold; color: #1e3a8a; }
        .voucher-meta p { font-size: 12px; color: #4b5563; margin-top: 4px; }
        .grid { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 25px; }
        .field-group { background: #f9fafb; padding: 12px 16px; border-radius: 6px; border: 1px solid #e5e7eb; }
        .field-group .label { font-size: 10px; font-weight: 700; color: #6b7280; text-transform: uppercase; margin-bottom: 4px; }
        .field-group .value { font-size: 14px; font-weight: 600; color: #111827; }
        .table-container { margin-bottom: 25px; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th { background: #1e3a8a; color: #fff; font-size: 11px; text-transform: uppercase; padding: 10px 12px; text-align: left; }
        td { border-bottom: 1px solid #e5e7eb; padding: 10px 12px; font-size: 13px; color: #374151; }
        .totals-table { width: 280px; margin-left: auto; margin-top: 15px; }
        .totals-table td { padding: 6px 12px; font-size: 13px; border: none; }
        .totals-table tr.grand-total td { font-size: 16px; font-weight: 800; color: #1e3a8a; border-top: 2px solid #1e3a8a; }
        .terms-box { background: #f9fafb; border: 1px solid #e5e7eb; border-radius: 6px; padding: 14px 16px; margin-top: 25px; font-size: 12px; color: #4b5563; }
        .signatures { display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 20px; margin-top: 45px; }
        .sig-box { border-top: 1px solid #9ca3af; padding-top: 8px; text-align: center; }
        .sig-box .role { font-size: 11px; font-weight: bold; color: #4b5563; text-transform: uppercase; }
        .sig-box .name { font-size: 12px; color: #111827; margin-top: 4px; }
        .footer { margin-top: 35px; border-top: 1px dashed #d1d5db; padding-top: 15px; font-size: 10px; color: #9ca3af; text-align: center; }
        .no-print { text-align: center; margin-bottom: 20px; }
        .print-btn { background: #1e3a8a; color: #fff; border: none; padding: 10px 24px; font-size: 14px; font-weight: bold; border-radius: 6px; cursor: pointer; }
        @media print {
            body { background: #fff; padding: 0; }
            .voucher-container { box-shadow: none; padding: 20px; }
            .no-print { display: none; }
        }
    </style>
</head>
<body>
    <div class="no-print">
        <button onclick="window.print()" class="print-btn">🖨️ Print Purchase Order / Save PDF</button>
    </div>

    <div class="voucher-container">
        <div class="header">
            <div class="logo-title">
                <h1>NATANEM ENGINEERING</h1>
                <p>General Contractor & Engineering Services</p>
                <div style="font-size: 11px; color: #4b5563; margin-top: 6px;">Addis Ababa, Ethiopia • Tel: +251 11 000 0000</div>
            </div>
            <div class="voucher-meta" style="display: flex; align-items: center; gap: 16px;">
                @if(!empty($qrCodeSvg))
                    <div style="text-align: center;">
                        {!! $qrCodeSvg !!}
                        <div style="font-size: 8px; font-weight: 700; color: #1e3a8a; margin-top: 2px; letter-spacing: 0.5px;">SCAN TO VERIFY</div>
                    </div>
                @endif
                <div>
                    <div class="badge">OFFICIAL PURCHASE ORDER</div>
                    <p><strong>PO #:</strong> {{ $order->po_no }}</p>
                    <p><strong>Date:</strong> {{ $order->order_date ? $order->order_date->format('F d, Y') : now()->format('F d, Y') }}</p>
                    @if($order->delivery_due_date)
                        <p><strong>Delivery Due:</strong> {{ $order->delivery_due_date->format('M d, Y') }}</p>
                    @endif
                </div>
            </div>
        </div>

        <div class="grid">
            <div class="field-group">
                <div class="label">Vendor / Supplier</div>
                <div class="value">{{ $order->vendor->name }}</div>
                <div style="font-size: 12px; color: #4b5563; margin-top: 4px;">
                    Code: {{ $order->vendor->code }} • Tel: {{ $order->vendor->phone ?? 'N/A' }}<br>
                    TIN: {{ $order->vendor->tax_id ?? 'N/A' }} • VAT No: {{ $order->vendor->vat_registration_no ?? 'N/A' }}
                </div>
            </div>
            <div class="field-group">
                <div class="label">Delivery Destination & Project</div>
                <div class="value">{{ $order->project ? $order->project->name : 'Central Warehouse / General' }}</div>
                <div style="font-size: 12px; color: #4b5563; margin-top: 4px;">
                    Site Location: {{ $order->delivery_site ?: ($order->project?->location ?? 'Central Store') }}<br>
                    Payment Terms: {{ $order->payment_terms ?: ($order->vendor?->payment_terms ?? 'Standard Terms') }}
                </div>
            </div>
        </div>

        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th style="width: 5%;">#</th>
                        <th style="width: 45%;">Description / Material Specification</th>
                        <th style="width: 15%; text-align: right;">Quantity</th>
                        <th style="width: 15%; text-align: right;">Unit Price (ETB)</th>
                        <th style="width: 20%; text-align: right;">Total Price (ETB)</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($order->items as $index => $item)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>
                                <strong>{{ $item->item_name }}</strong>
                            </td>
                            <td style="text-align: right;">{{ number_format((float)$item->quantity, 2) }} {{ $item->unit_of_measurement }}</td>
                            <td style="text-align: right;">{{ number_format((float)$item->unit_price, 2) }}</td>
                            <td style="text-align: right; font-weight: 600;">{{ number_format((float)$item->total_price, 2) }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            <table class="totals-table">
                <tr>
                    <td style="text-align: right; color: #6b7280;">Subtotal:</td>
                    <td style="text-align: right; font-weight: 600;">{{ number_format((float)$order->subtotal, 2) }} ETB</td>
                </tr>
                <tr>
                    <td style="text-align: right; color: #6b7280;">VAT ({{ $order->tax_rate }}%):</td>
                    <td style="text-align: right; font-weight: 600;">{{ number_format((float)$order->tax_amount, 2) }} ETB</td>
                </tr>
                <tr class="grand-total">
                    <td style="text-align: right;">Grand Total:</td>
                    <td style="text-align: right;">{{ number_format((float)$order->total_amount, 2) }} ETB</td>
                </tr>
            </table>
        </div>

        @if($order->notes)
            <div class="terms-box">
                <strong>Special Instructions / Notes:</strong><br>
                {{ $order->notes }}
            </div>
        @endif

        <div class="signatures">
            <div class="sig-box">
                <div class="role">Prepared By</div>
                <div class="name">{{ $order->creator ? $order->creator->name : 'Procurement Officer' }}</div>
            </div>
            <div class="sig-box">
                <div class="role">Reviewed & Checked</div>
                <div class="name">Store & Inventory Manager</div>
            </div>
            <div class="sig-box">
                <div class="role">Approved By</div>
                <div class="name">Project Manager / General Manager</div>
            </div>
        </div>

        <div class="footer">
            Generated via Natanem Engineering Enterprise ERP • All deliveries must be accompanied by this PO & Vendor Waybill
        </div>
    </div>
</body>
</html>
