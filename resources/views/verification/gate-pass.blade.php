<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gate Pass Verification #GP-{{ str_pad((string) $loan->id, 5, '0', STR_PAD_LEFT) }}</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif; }
        body { background: #0f172a; color: #f8fafc; padding: 20px 15px; min-height: 100vh; display: flex; align-items: center; justify-content: center; }
        .card { background: #1e293b; border: 1px solid #334155; border-radius: 20px; max-width: 480px; width: 100%; padding: 28px 24px; box-shadow: 0 20px 25px -5px rgba(0,0,0,0.5); }
        .badge-valid { display: inline-flex; align-items: center; gap: 6px; padding: 6px 14px; background: rgba(16, 185, 129, 0.15); border: 1px solid rgba(16, 185, 129, 0.4); color: #34d399; font-size: 11px; font-weight: 800; border-radius: 9999px; text-transform: uppercase; letter-spacing: 0.5px; }
        .badge-pending { display: inline-flex; align-items: center; gap: 6px; padding: 6px 14px; background: rgba(245, 158, 11, 0.15); border: 1px solid rgba(245, 158, 11, 0.4); color: #fbbf24; font-size: 11px; font-weight: 800; border-radius: 9999px; text-transform: uppercase; }
        .badge-returned { display: inline-flex; align-items: center; gap: 6px; padding: 6px 14px; background: rgba(59, 130, 246, 0.15); border: 1px solid rgba(59, 130, 246, 0.4); color: #60a5fa; font-size: 11px; font-weight: 800; border-radius: 9999px; text-transform: uppercase; }
        .header { text-align: center; margin-bottom: 24px; }
        .header h1 { font-size: 18px; font-weight: 900; color: #fff; margin-top: 10px; }
        .header p { font-size: 12px; color: #94a3b8; margin-top: 4px; }
        .item-box { background: #0f172a; border: 1px solid #334155; border-radius: 14px; padding: 16px; margin-bottom: 20px; text-align: center; }
        .item-name { font-size: 16px; font-weight: 800; color: #38bdf8; }
        .item-qty { font-size: 24px; font-weight: 900; color: #fff; margin-top: 6px; }
        .info-list { display: flex; flex-direction: column; gap: 10px; margin-bottom: 24px; }
        .info-row { display: flex; justify-content: space-between; align-items: center; font-size: 12px; padding-bottom: 8px; border-bottom: 1px solid #334155; }
        .info-label { color: #94a3b8; font-weight: 600; }
        .info-val { color: #f1f5f9; font-weight: 700; text-align: right; }
        .action-btn { display: block; width: 100%; padding: 14px; border: none; border-radius: 12px; background: linear-gradient(135deg, #2563eb, #1d4ed8); color: #fff; font-size: 13px; font-weight: 800; text-align: center; cursor: pointer; text-decoration: none; box-shadow: 0 4px 6px -1px rgba(37,99,235,0.4); }
        .action-btn:hover { background: #1e40af; }
        .alert-success { background: rgba(16, 185, 129, 0.15); border: 1px solid rgba(16, 185, 129, 0.4); color: #34d399; padding: 12px; border-radius: 10px; font-size: 12px; font-weight: 600; margin-bottom: 15px; text-align: center; }
        .footer { text-align: center; font-size: 10px; color: #64748b; margin-top: 20px; }
    </style>
</head>
<body>
    <div class="card">
        <div class="header">
            @if($loan->status === 'approved')
                <div class="badge-valid">✓ Official Authentic Gate Pass</div>
            @elseif($loan->status === 'returned')
                <div class="badge-returned">✓ Returned & Closed</div>
            @else
                <div class="badge-pending">⏳ Status: {{ ucfirst($loan->status) }}</div>
            @endif
            <h1>Natanem Engineering PLC</h1>
            <p>Store Gate Pass & Material Issue Verification</p>
        </div>

        @if(session('success'))
            <div class="alert-success">✓ {{ session('success') }}</div>
        @endif

        <div class="item-box">
            <div class="item-name">{{ $loan->inventoryItem?->name ?? 'Equipment / Material' }}</div>
            <div class="item-qty">{{ $loan->quantity }} {{ $loan->inventoryItem?->unit_of_measurement ?? 'Units' }}</div>
            <div style="font-size: 11px; color: #94a3b8; margin-top: 4px;">Code: {{ $loan->inventoryItem?->item_code ?? 'N/A' }}</div>
        </div>

        <div class="info-list">
            <div class="info-row">
                <span class="info-label">Pass Reference</span>
                <span class="info-val">#GP-{{ str_pad((string) $loan->id, 5, '0', STR_PAD_LEFT) }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Issued To / Borrower</span>
                <span class="info-val">{{ $loan->employee ? ($loan->employee->first_name . ' ' . $loan->employee->last_name) : ($loan->user?->name ?? 'Staff') }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Department / Unit</span>
                <span class="info-val">{{ $loan->employee?->department_rel?->name ?? ($loan->employee?->department ?? 'Operations') }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Date Issued</span>
                <span class="info-val">{{ $loan->loan_date ? \Carbon\Carbon::parse($loan->loan_date)->format('M d, Y') : '—' }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Expected Return</span>
                <span class="info-val">{{ $loan->return_date ? \Carbon\Carbon::parse($loan->return_date)->format('M d, Y') : 'Consumable / Not specified' }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Authorized By</span>
                <span class="info-val">{{ $loan->approvedBy?->name ?? 'Store Manager' }}</span>
            </div>
        </div>

        @if($loan->status === 'approved')
            <form action="{{ route('verify.gate-pass.confirm', $loan->id) }}" method="POST">
                @csrf
                <button type="submit" class="action-btn">
                    📦 Confirm Delivery Received on Site
                </button>
            </form>
        @endif

        <div class="footer">
            Digital Certificate • Cryptographically verified via Natanem Construction ERP
        </div>
    </div>
</body>
</html>
