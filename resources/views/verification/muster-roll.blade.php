<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daily Muster Roll Verification #{{ $musterRoll->muster_roll_no }}</title>
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
        .item-name { font-size: 13px; font-weight: 800; color: #38bdf8; text-transform: uppercase; }
        .item-qty { font-size: 26px; font-weight: 900; color: #fff; margin-top: 4px; }
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
            @if($musterRoll->status === 'paid')
                <div class="badge-valid">✓ Wage Payout Disbursed & Verified</div>
            @elseif($musterRoll->status === 'approved')
                <div class="badge-valid">✓ Approved For Payment</div>
            @else
                <div class="badge-pending">⏳ Status: {{ ucfirst($musterRoll->status) }}</div>
            @endif
            <h1>Natanem Engineering PLC</h1>
            <p>Site Casual Labor Muster Roll Digital Verification</p>
        </div>

        <div class="item-box">
            <div class="item-name">Total Net Labor Payout</div>
            <div class="item-qty">{{ number_format((float)$musterRoll->total_net_amount, 2) }} ETB</div>
            <div style="font-size: 12px; color: #94a3b8; margin-top: 4px;">{{ $musterRoll->total_workers }} Site Workers Recorded • {{ $musterRoll->date ? $musterRoll->date->format('M d, Y') : '' }}</div>
        </div>

        <div class="info-list">
            <div class="info-row">
                <span class="info-label">Muster Roll #</span>
                <span class="info-val" style="font-family: monospace;">{{ $musterRoll->muster_roll_no }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Construction Project</span>
                <span class="info-val">{{ $musterRoll->project ? $musterRoll->project->name : 'N/A' }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Site Location</span>
                <span class="info-val">{{ $musterRoll->project?->location ?? 'On-Site' }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Work Description</span>
                <span class="info-val">{{ $musterRoll->title ?: 'Site Operations' }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Prepared By</span>
                <span class="info-val">{{ $musterRoll->supervisor ? $musterRoll->supervisor->name : 'Site Foreman' }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Approved By</span>
                <span class="info-val">{{ $musterRoll->approvedBy ? $musterRoll->approvedBy->name : 'Pending PM Approval' }}</span>
            </div>
            @if($musterRoll->paid_at)
                <div class="info-row">
                    <span class="info-label">Payment Method</span>
                    <span class="info-val" style="text-transform: uppercase;">{{ $musterRoll->payment_method }} @if($musterRoll->payout_reference) ({{ $musterRoll->payout_reference }}) @endif</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Paid At</span>
                    <span class="info-val">{{ $musterRoll->paid_at->format('M d, Y H:i') }}</span>
                </div>
            @endif
        </div>

        @if($musterRoll->items->isNotEmpty())
            <div style="margin-top: 10px; margin-bottom: 20px; border-top: 1px solid #334155; padding-top: 12px;">
                <div style="font-size: 11px; font-weight: 700; color: #94a3b8; text-transform: uppercase; margin-bottom: 8px; letter-spacing: 0.5px;">Credited Site Labor Force</div>
                <div style="display: flex; flex-direction: column; gap: 6px; max-height: 180px; overflow-y: auto;">
                    @foreach($musterRoll->items as $item)
                        <div style="display: flex; justify-content: space-between; align-items: center; font-size: 11px; background: #0f172a; padding: 6px 10px; border-radius: 8px; border: 1px solid #334155;">
                            <div>
                                <span style="font-weight: 700; color: #f8fafc;">{{ $item->casualLaborer ? $item->casualLaborer->full_name : 'Worker' }}</span>
                                <span style="color: #94a3b8; font-size: 10px; display: block;">{{ $item->trade }} • {{ number_format((float)$item->days_worked, 1) }} days</span>
                            </div>
                            <span style="font-weight: 800; color: #34d399;">{{ number_format((float)$item->net_amount, 2) }} ETB</span>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        <div class="footer">
            Official Cryptographic Digital Record • Natanem Engineering ERP<br>
            Scanned at: {{ now()->format('Y-m-d H:i:s') }}
        </div>
    </div>
</body>
</html>
