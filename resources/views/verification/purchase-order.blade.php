<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Purchase Order Verification #{{ $order->po_no }}</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif; }
        body { background: #0f172a; color: #f8fafc; padding: 20px 15px; min-height: 100vh; display: flex; align-items: center; justify-content: center; }
        .card { background: #1e293b; border: 1px solid #334155; border-radius: 20px; max-width: 520px; width: 100%; padding: 28px 24px; box-shadow: 0 20px 25px -5px rgba(0,0,0,0.5); }
        .badge-valid { display: inline-flex; align-items: center; gap: 6px; padding: 6px 14px; background: rgba(16, 185, 129, 0.15); border: 1px solid rgba(16, 185, 129, 0.4); color: #34d399; font-size: 11px; font-weight: 800; border-radius: 9999px; text-transform: uppercase; letter-spacing: 0.5px; }
        .badge-pending { display: inline-flex; align-items: center; gap: 6px; padding: 6px 14px; background: rgba(245, 158, 11, 0.15); border: 1px solid rgba(245, 158, 11, 0.4); color: #fbbf24; font-size: 11px; font-weight: 800; border-radius: 9999px; text-transform: uppercase; }
        .badge-cancelled { display: inline-flex; align-items: center; gap: 6px; padding: 6px 14px; background: rgba(239, 68, 68, 0.15); border: 1px solid rgba(239, 68, 68, 0.4); color: #f87171; font-size: 11px; font-weight: 800; border-radius: 9999px; text-transform: uppercase; }
        .header { text-align: center; margin-bottom: 24px; }
        .header h1 { font-size: 18px; font-weight: 900; color: #fff; margin-top: 10px; }
        .header p { font-size: 12px; color: #94a3b8; margin-top: 4px; }
        .item-box { background: #0f172a; border: 1px solid #334155; border-radius: 14px; padding: 16px; margin-bottom: 20px; text-align: center; }
        .item-name { font-size: 14px; font-weight: 800; color: #38bdf8; }
        .item-qty { font-size: 24px; font-weight: 900; color: #fff; margin-top: 4px; }
        .info-list { display: flex; flex-direction: column; gap: 10px; margin-bottom: 24px; }
        .info-row { display: flex; justify-content: space-between; align-items: center; font-size: 12px; padding-bottom: 8px; border-bottom: 1px solid #334155; }
        .info-label { color: #94a3b8; font-weight: 600; }
        .info-val { color: #f1f5f9; font-weight: 700; text-align: right; }
        .items-table { width: 100%; border-collapse: collapse; margin-top: 10px; font-size: 12px; }
        .items-table th { text-align: left; color: #94a3b8; padding: 8px 6px; border-bottom: 1px solid #334155; }
        .items-table td { padding: 8px 6px; border-bottom: 1px solid #1e293b; }
        .footer { text-align: center; font-size: 10px; color: #64748b; margin-top: 20px; }
    </style>
</head>
<body>
    <div class="card">
        <div class="header">
            @if($order->status === 'received' || $order->status === 'issued' || $order->status === 'partially_received')
                <div class="badge-valid">✓ Official Valid Purchase Order</div>
            @elseif($order->status === 'cancelled')
                <div class="badge-cancelled">✕ Order Cancelled</div>
            @else
                <div class="badge-pending">⏳ Status: {{ ucfirst($order->status) }}</div>
            @endif
            <h1>Natanem Engineering PLC</h1>
            <p>Procurement & Purchase Order Verification</p>
        </div>

        <div class="item-box">
            <div class="item-name">Total Contract Value (Inc. VAT)</div>
            <div class="item-qty">{{ number_format((float)$order->total_amount, 2) }} ETB</div>
            <div style="font-size: 11px; color: #94a3b8; margin-top: 4px;">PO #: {{ $order->po_no }} • {{ $order->items->count() }} Line Item(s)</div>
        </div>

        <div class="info-list">
            <div class="info-row">
                <span class="info-label">Vendor / Supplier</span>
                <span class="info-val">{{ $order->vendor->name }} ({{ $order->vendor->code }})</span>
            </div>
            <div class="info-row">
                <span class="info-label">Project Destination</span>
                <span class="info-val">{{ $order->project ? $order->project->name : 'Central Warehouse / General' }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Delivery Location</span>
                <span class="info-val">{{ $order->delivery_site ?: 'Site Delivery' }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Order Date</span>
                <span class="info-val">{{ $order->order_date ? $order->order_date->format('M d, Y') : 'N/A' }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Fulfillment Status</span>
                <span class="info-val" style="color: #38bdf8;">{{ strtoupper(str_replace('_', ' ', $order->status)) }}</span>
            </div>
        </div>

        <div style="margin-top: 15px;">
            <div style="font-size: 12px; font-weight: 700; color: #cbd5e1; margin-bottom: 6px;">Order Line Items:</div>
            <table class="items-table">
                <thead>
                    <tr>
                        <th>Material</th>
                        <th style="text-align: right;">Qty</th>
                        <th style="text-align: right;">Unit Price</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($order->items as $item)
                        <tr>
                            <td style="color: #f1f5f9; font-weight: 600;">{{ $item->item_name }}</td>
                            <td style="text-align: right; color: #94a3b8;">{{ $item->quantity }} {{ $item->unit_of_measurement }}</td>
                            <td style="text-align: right; color: #f1f5f9;">{{ number_format((float)$item->unit_price, 2) }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="footer">
            Digitally encrypted verification • Natanem Engineering Enterprise ERP
        </div>
    </div>
</body>
</html>
