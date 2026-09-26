<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Client Payment Certificate Verification #{{ $certificate->certificate_no }}</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif; }
        body { background: #0f172a; color: #f8fafc; padding: 20px 15px; min-height: 100vh; display: flex; align-items: center; justify-content: center; }
        .card { background: #1e293b; border: 1px solid #334155; border-radius: 20px; max-width: 520px; width: 100%; padding: 28px 24px; box-shadow: 0 20px 25px -5px rgba(0,0,0,0.5); }
        .badge-valid { display: inline-flex; align-items: center; gap: 6px; padding: 6px 14px; background: rgba(16, 185, 129, 0.15); border: 1px solid rgba(16, 185, 129, 0.4); color: #34d399; font-size: 11px; font-weight: 800; border-radius: 9999px; text-transform: uppercase; letter-spacing: 0.5px; }
        .badge-pending { display: inline-flex; align-items: center; gap: 6px; padding: 6px 14px; background: rgba(245, 158, 11, 0.15); border: 1px solid rgba(245, 158, 11, 0.4); color: #fbbf24; font-size: 11px; font-weight: 800; border-radius: 9999px; text-transform: uppercase; }
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
        .footer { text-align: center; font-size: 10px; color: #64748b; margin-top: 20px; }
    </style>
</head>
<body>
    <div class="card">
        <div class="header">
            @if($certificate->status === 'certified' || $certificate->status === 'paid' || $certificate->status === 'partially_paid')
                <div class="badge-valid">✓ Certified Payment Certificate</div>
            @else
                <div class="badge-pending">⏳ Status: {{ ucfirst($certificate->status) }}</div>
            @endif
            <h1>Natanem Engineering PLC</h1>
            <p>Client Interim Payment Certificate (IPC) Verification</p>
        </div>

        <div class="item-box">
            <div class="item-name">Total Certified Claim (Inc. VAT)</div>
            <div class="item-qty">{{ number_format((float)$certificate->total_certified_amount, 2) }} ETB</div>
            <div style="font-size: 11px; color: #94a3b8; margin-top: 4px;">Certificate #: {{ $certificate->certificate_no }} (IPC #{{ $certificate->ipc_sequence }})</div>
        </div>

        <div class="info-list">
            <div class="info-row">
                <span class="info-label">Project Title</span>
                <span class="info-val">{{ $certificate->project ? $certificate->project->name : 'N/A' }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Employer / Client</span>
                <span class="info-val">{{ $certificate->client_name ?: 'Client / Owner' }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Supervising Consultant</span>
                <span class="info-val">{{ $certificate->consultant_name ?: 'Consultant Resident Engineer' }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Billing Period</span>
                <span class="info-val">{{ $certificate->period_start ? $certificate->period_start->format('M d, Y') : 'N/A' }} — {{ $certificate->period_end ? $certificate->period_end->format('M d, Y') : 'N/A' }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Retention Deducted ({{ $certificate->retention_rate }}%)</span>
                <span class="info-val" style="color: #fbbf24;">{{ number_format((float)$certificate->retention_deduction, 2) }} ETB</span>
            </div>
            <div class="info-row">
                <span class="info-label">Amount Paid to Date</span>
                <span class="info-val" style="color: #34d399;">{{ number_format((float)$certificate->amount_paid, 2) }} ETB</span>
            </div>
            <div class="info-row">
                <span class="info-label">Remaining Balance Due</span>
                <span class="info-val" style="color: #f87171;">{{ number_format((float)$certificate->balance_due, 2) }} ETB</span>
            </div>
        </div>

        <div class="footer">
            Digitally encrypted verification • Natanem Engineering Enterprise ERP
        </div>
    </div>
</body>
</html>
