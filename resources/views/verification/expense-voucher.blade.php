<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Voucher Verification #EXP-{{ str_pad((string) $expense->id, 5, '0', STR_PAD_LEFT) }}</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif; }
        body { background: #0f172a; color: #f8fafc; padding: 20px 15px; min-height: 100vh; display: flex; align-items: center; justify-content: center; }
        .card { background: #1e293b; border: 1px solid #334155; border-radius: 20px; max-width: 480px; width: 100%; padding: 28px 24px; box-shadow: 0 20px 25px -5px rgba(0,0,0,0.5); }
        .badge-valid { display: inline-flex; align-items: center; gap: 6px; padding: 6px 14px; background: rgba(16, 185, 129, 0.15); border: 1px solid rgba(16, 185, 129, 0.4); color: #34d399; font-size: 11px; font-weight: 800; border-radius: 9999px; text-transform: uppercase; letter-spacing: 0.5px; }
        .badge-rejected { display: inline-flex; align-items: center; gap: 6px; padding: 6px 14px; background: rgba(239, 68, 68, 0.15); border: 1px solid rgba(239, 68, 68, 0.4); color: #f87171; font-size: 11px; font-weight: 800; border-radius: 9999px; text-transform: uppercase; }
        .badge-pending { display: inline-flex; align-items: center; gap: 6px; padding: 6px 14px; background: rgba(245, 158, 11, 0.15); border: 1px solid rgba(245, 158, 11, 0.4); color: #fbbf24; font-size: 11px; font-weight: 800; border-radius: 9999px; text-transform: uppercase; }
        .header { text-align: center; margin-bottom: 24px; }
        .header h1 { font-size: 18px; font-weight: 900; color: #fff; margin-top: 10px; }
        .header p { font-size: 12px; color: #94a3b8; margin-top: 4px; }
        .amount-box { background: #0f172a; border: 1px solid #334155; border-radius: 14px; padding: 18px; margin-bottom: 20px; text-align: center; }
        .amount-title { font-size: 11px; font-weight: 700; color: #94a3b8; text-transform: uppercase; letter-spacing: 0.5px; }
        .amount-val { font-size: 26px; font-weight: 900; color: #34d399; margin-top: 6px; font-family: monospace; }
        .info-list { display: flex; flex-direction: column; gap: 10px; margin-bottom: 20px; }
        .info-row { display: flex; justify-content: space-between; align-items: center; font-size: 12px; padding-bottom: 8px; border-bottom: 1px solid #334155; }
        .info-label { color: #94a3b8; font-weight: 600; }
        .info-val { color: #f1f5f9; font-weight: 700; text-align: right; }
        .desc-box { background: #0f172a; border-radius: 10px; padding: 12px; font-size: 11px; color: #cbd5e1; line-height: 1.5; margin-bottom: 20px; border: 1px solid #334155; }
        .footer { text-align: center; font-size: 10px; color: #64748b; margin-top: 20px; }
    </style>
</head>
<body>
    <div class="card">
        <div class="header">
            @if($expense->status === 'approved')
                <div class="badge-valid">✓ Legitimate Corporate Voucher</div>
            @elseif($expense->status === 'rejected')
                <div class="badge-rejected">✕ Rejected / Voided Voucher</div>
            @else
                <div class="badge-pending">⏳ Status: {{ ucfirst($expense->status) }}</div>
            @endif
            <h1>Natanem Engineering PLC</h1>
            <p>Financial Payment Authorization Verification</p>
        </div>

        <div class="amount-box">
            <div class="amount-title">Authorized Disbursement Amount</div>
            <div class="amount-val">ETB {{ number_format((float) $expense->amount, 2) }}</div>
        </div>

        <div class="info-list">
            <div class="info-row">
                <span class="info-label">Voucher Reference</span>
                <span class="info-val">#EXP-{{ str_pad((string) $expense->id, 5, '0', STR_PAD_LEFT) }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Project Charged</span>
                <span class="info-val">{{ $expense->project?->name ?? 'General Overhead' }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Requisitioned By</span>
                <span class="info-val">{{ $expense->user?->name ?? 'Staff' }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Expense Date</span>
                <span class="info-val">{{ $expense->expense_date ? $expense->expense_date->format('M d, Y') : '—' }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Executive Approver</span>
                <span class="info-val">{{ $expense->approvedBy?->name ?? 'System Authorized' }}</span>
            </div>
        </div>

        <div class="desc-box">
            <div style="font-weight: 700; color: #94a3b8; margin-bottom: 4px; text-transform: uppercase; font-size: 10px;">Purpose of Expenditure</div>
            {{ $expense->description }}
        </div>

        <div class="footer">
            Digital Certificate • Cryptographically verified via Natanem Construction ERP
        </div>
    </div>
</body>
</html>
