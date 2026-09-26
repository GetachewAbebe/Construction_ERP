<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Fleet Maintenance Verification #{{ $workOrder->work_order_no }}</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif; }
        body { background: #0f172a; color: #f8fafc; padding: 20px 15px; min-height: 100vh; display: flex; align-items: center; justify-content: center; }
        .card { background: #1e293b; border: 1px solid #334155; border-radius: 20px; max-width: 520px; width: 100%; padding: 28px 24px; box-shadow: 0 20px 25px -5px rgba(0,0,0,0.5); }
        .badge-valid { display: inline-flex; align-items: center; gap: 6px; padding: 6px 14px; background: rgba(16, 185, 129, 0.15); border: 1px solid rgba(16, 185, 129, 0.4); color: #34d399; font-size: 11px; font-weight: 800; border-radius: 9999px; text-transform: uppercase; letter-spacing: 0.5px; }
        .badge-pending { display: inline-flex; align-items: center; gap: 6px; padding: 6px 14px; background: rgba(245, 158, 11, 0.15); border: 1px solid rgba(245, 158, 11, 0.4); color: #fbbf24; font-size: 11px; font-weight: 800; border-radius: 9999px; text-transform: uppercase; }
        .badge-breakdown { display: inline-flex; align-items: center; gap: 6px; padding: 6px 14px; background: rgba(239, 68, 68, 0.15); border: 1px solid rgba(239, 68, 68, 0.4); color: #f87171; font-size: 11px; font-weight: 800; border-radius: 9999px; text-transform: uppercase; }
        .header { text-align: center; margin-bottom: 24px; }
        .header h1 { font-size: 18px; font-weight: 900; color: #fff; margin-top: 10px; }
        .header p { font-size: 12px; color: #94a3b8; margin-top: 4px; }
        .item-box { background: #0f172a; border: 1px solid #334155; border-radius: 14px; padding: 16px; margin-bottom: 20px; text-align: center; }
        .item-name { font-size: 13px; font-weight: 800; color: #38bdf8; text-transform: uppercase; }
        .item-qty { font-size: 22px; font-weight: 900; color: #fff; margin-top: 4px; }
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
            @if($workOrder->status === 'completed')
                <div class="badge-valid">✓ Maintenance Completed & Certified</div>
            @elseif($workOrder->order_type === 'breakdown')
                <div class="badge-breakdown">⚠️ Breakdown In Progress</div>
            @else
                <div class="badge-pending">⏳ Status: {{ ucfirst($workOrder->status) }}</div>
            @endif
            <h1>Natanem Engineering PLC</h1>
            <p>Fleet & Heavy Equipment Work Order Verification</p>
        </div>

        <div class="item-box">
            <div class="item-name">{{ $workOrder->equipment ? $workOrder->equipment->name : 'Machinery' }}</div>
            <div class="item-qty">{{ $workOrder->title }}</div>
            <div style="font-size: 12px; color: #94a3b8; margin-top: 4px;">Plate / Tag: {{ $workOrder->equipment?->plate_number ?: 'N/A' }} • {{ number_format((float)$workOrder->operating_hours, 1) }} Operating Hours</div>
        </div>

        <div class="info-list">
            <div class="info-row">
                <span class="info-label">Work Order #</span>
                <span class="info-val" style="font-family: monospace;">{{ $workOrder->work_order_no }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Service Type</span>
                <span class="info-val" style="text-transform: uppercase;">{{ str_replace('_', ' ', $workOrder->order_type) }} ({{ $workOrder->priority }})</span>
            </div>
            <div class="info-row">
                <span class="info-label">Project Site</span>
                <span class="info-val">{{ $workOrder->project ? $workOrder->project->name : 'Central Fleet Workshop' }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Lead Mechanic</span>
                <span class="info-val">{{ $workOrder->assignedMechanic ? $workOrder->assignedMechanic->name : 'Workshop Team' }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Total Repair Cost</span>
                <span class="info-val" style="color: #34d399;">{{ number_format((float)$workOrder->total_cost, 2) }} ETB</span>
            </div>
            @if($workOrder->completed_date)
                <div class="info-row">
                    <span class="info-label">Completed On</span>
                    <span class="info-val">{{ $workOrder->completed_date->format('M d, Y H:i') }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Next Service Due</span>
                    <span class="info-val">{{ number_format((float)($workOrder->equipment?->next_service_hours ?? 0), 1) }} Hours</span>
                </div>
            @endif
        </div>

        @if($workOrder->parts->isNotEmpty())
            <div style="margin-top: 10px; margin-bottom: 20px; border-top: 1px solid #334155; padding-top: 12px;">
                <div style="font-size: 11px; font-weight: 700; color: #94a3b8; text-transform: uppercase; margin-bottom: 8px; letter-spacing: 0.5px;">Parts & Lubricants Installed</div>
                <div style="display: flex; flex-direction: column; gap: 6px; max-height: 160px; overflow-y: auto;">
                    @foreach($workOrder->parts as $part)
                        <div style="display: flex; justify-content: space-between; align-items: center; font-size: 11px; background: #0f172a; padding: 6px 10px; border-radius: 8px; border: 1px solid #334155;">
                            <div>
                                <span style="font-weight: 700; color: #f8fafc;">{{ $part->part_name }}</span>
                                <span style="color: #94a3b8; font-size: 10px; display: block;">{{ number_format((float)$part->quantity, 2) }} {{ $part->unit_of_measurement }} @if($part->part_number) ({{ $part->part_number }}) @endif</span>
                            </div>
                            <span style="font-weight: 800; color: #34d399;">{{ number_format((float)$part->total_price, 2) }} ETB</span>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        <div class="footer">
            Official Machinery Maintenance Record • Natanem Engineering ERP<br>
            Scanned at: {{ now()->format('Y-m-d H:i:s') }}
        </div>
    </div>
</body>
</html>
