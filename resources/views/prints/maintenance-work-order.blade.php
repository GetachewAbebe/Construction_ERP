<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Equipment Maintenance Job Card #{{ $workOrder->work_order_no }} - Natanem Engineering</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; }
        body { background: #f3f4f6; padding: 30px 15px; color: #1f2937; }
        .voucher-container { max-width: 900px; margin: 0 auto; background: #fff; padding: 35px; border-radius: 8px; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1); }
        .header { display: flex; justify-content: space-between; align-items: flex-start; border-bottom: 2px solid #1e3a8a; padding-bottom: 18px; margin-bottom: 20px; }
        .logo-title h1 { font-size: 22px; font-weight: 800; color: #1e3a8a; letter-spacing: 0.5px; }
        .logo-title p { font-size: 11px; color: #6b7280; text-transform: uppercase; margin-top: 3px; }
        .voucher-meta { text-align: right; }
        .voucher-meta .badge { font-size: 18px; font-weight: bold; color: #1e3a8a; }
        .voucher-meta p { font-size: 12px; color: #4b5563; margin-top: 4px; }
        .grid { display: grid; grid-template-columns: 1fr 1fr; gap: 15px; margin-bottom: 20px; }
        .field-group { background: #f9fafb; padding: 12px 14px; border-radius: 6px; border: 1px solid #e5e7eb; }
        .field-group .label { font-size: 10px; font-weight: 700; color: #6b7280; text-transform: uppercase; margin-bottom: 3px; }
        .field-group .value { font-size: 13px; font-weight: 600; color: #111827; }
        .table-container { margin-bottom: 20px; }
        table { width: 100%; border-collapse: collapse; margin-top: 8px; font-size: 12px; }
        th { background: #1e3a8a; color: #fff; font-size: 11px; text-transform: uppercase; padding: 9px 10px; text-align: left; }
        td { border-bottom: 1px solid #e5e7eb; padding: 8px 10px; color: #374151; }
        tr.total-row td { background: #f8fafc; font-weight: 600; }
        tr.grand-total td { background: #eff6ff; font-size: 13px; font-weight: 800; color: #1e3a8a; border-top: 2px solid #1e3a8a; border-bottom: 2px solid #1e3a8a; }
        .signatures { display: grid; grid-template-columns: 1fr 1fr 1fr 1fr; gap: 15px; margin-top: 40px; }
        .sig-box { border-top: 1px solid #9ca3af; padding-top: 6px; text-align: center; }
        .sig-box .role { font-size: 10px; font-weight: bold; color: #4b5563; text-transform: uppercase; }
        .sig-box .name { font-size: 11px; color: #111827; margin-top: 3px; }
        .footer { margin-top: 30px; border-top: 1px dashed #d1d5db; padding-top: 12px; font-size: 9px; color: #9ca3af; text-align: center; }
        .no-print { text-align: center; margin-bottom: 20px; }
        .print-btn { background: #1e3a8a; color: #fff; border: none; padding: 10px 24px; font-size: 14px; font-weight: bold; border-radius: 6px; cursor: pointer; }
        @media print {
            body { background: #fff; padding: 0; }
            .voucher-container { box-shadow: none; padding: 15px; max-width: 100%; }
            .no-print { display: none; }
        }
    </style>
</head>
<body>
    <div class="no-print">
        <button onclick="window.print()" class="print-btn">🖨️ Print Maintenance Work Order / Save PDF</button>
    </div>

    <div class="voucher-container">
        <div class="header">
            <div class="logo-title">
                <h1>NATANEM ENGINEERING PLC</h1>
                <p>Heavy Equipment & Fleet Maintenance Workshop • Job Card</p>
                <div style="font-size: 10px; color: #4b5563; margin-top: 4px;">Addis Ababa, Ethiopia • Plant & Fleet Division</div>
            </div>
            <div class="voucher-meta" style="display: flex; align-items: center; gap: 14px;">
                @if(!empty($qrCodeSvg))
                    <div style="text-align: center;">
                        {!! $qrCodeSvg !!}
                        <div style="font-size: 8px; font-weight: 700; color: #1e3a8a; margin-top: 2px; letter-spacing: 0.5px;">SCAN TO VERIFY</div>
                    </div>
                @endif
                <div>
                    <div class="badge">MAINTENANCE WORK ORDER</div>
                    <p><strong>Order #:</strong> {{ $workOrder->work_order_no }}</p>
                    <p><strong>Service Type:</strong> <span style="text-transform: uppercase; font-weight: bold; color: #1e3a8a;">{{ str_replace('_', ' ', $workOrder->order_type) }}</span></p>
                    <p><strong>Priority:</strong> <span style="text-transform: uppercase; font-weight: bold; color: #b45309;">{{ str_replace('_', ' ', $workOrder->priority) }}</span></p>
                    <p><strong>Status:</strong> <span style="text-transform: uppercase; font-weight: bold;">{{ $workOrder->status }}</span></p>
                </div>
            </div>
        </div>

        <div class="grid">
            <div class="field-group">
                <div class="label">Equipment Machine Details</div>
                <div class="value">{{ $workOrder->equipment ? $workOrder->equipment->name : 'Equipment' }}</div>
                <div style="font-size: 12px; color: #4b5563; margin-top: 4px;">
                    Plate / Serial #: <strong>{{ $workOrder->equipment?->plate_number ?: 'N/A' }}</strong><br>
                    Machine Type: {{ $workOrder->equipment?->type }} • Fuel: {{ $workOrder->equipment?->fuel_type }}<br>
                    Hour Meter at Service: <strong>{{ number_format((float)$workOrder->operating_hours, 1) }} Hours</strong>
                </div>
            </div>
            <div class="field-group">
                <div class="label">Deployment & Assignment</div>
                <div class="value">{{ $workOrder->project ? $workOrder->project->name : 'Central Fleet Workshop' }}</div>
                <div style="font-size: 12px; color: #4b5563; margin-top: 4px;">
                    Location: {{ $workOrder->project?->location ?? 'Central Workshop' }}<br>
                    Lead Mechanic: <strong>{{ $workOrder->assignedMechanic ? $workOrder->assignedMechanic->name : 'Workshop Team' }}</strong><br>
                    Date Opened: {{ $workOrder->start_date ? $workOrder->start_date->format('M d, Y H:i') : now()->format('M d, Y') }}
                </div>
            </div>
        </div>

        <div class="field-group" style="margin-bottom: 20px;">
            <div class="label">Work Title & Problem Description</div>
            <div class="value" style="font-size: 14px; margin-bottom: 4px;">{{ $workOrder->title }}</div>
            <div style="font-size: 12px; color: #4b5563;">
                {{ $workOrder->fault_description ?: 'Routine scheduled maintenance as per manufacturer guidelines.' }}
            </div>
        </div>

        @if($workOrder->work_performed)
            <div class="field-group" style="margin-bottom: 20px; background: #ecfdf5; border-color: #a7f3d0;">
                <div class="label" style="color: #065f46;">Mechanic Corrective Actions & Work Performed</div>
                <div style="font-size: 12px; color: #064e3b; margin-top: 2px;">
                    {{ $workOrder->work_performed }}
                </div>
                @if($workOrder->completed_date)
                    <div style="font-size: 10px; color: #047857; margin-top: 4px;">
                        Completed on {{ $workOrder->completed_date->format('M d, Y H:i') }} by {{ $workOrder->completer ? $workOrder->completer->name : 'Mechanic' }} • Total Downtime: <strong>{{ number_format((float)$workOrder->downtime_hours, 1) }} Hours</strong>
                    </div>
                @endif
            </div>
        @endif

        <div class="table-container">
            <div style="font-size: 12px; font-weight: bold; color: #1e3a8a; text-transform: uppercase; margin-bottom: 4px;">
                Spare Parts, Lubricants & Filters Consumed
            </div>
            <table>
                <thead>
                    <tr>
                        <th style="width: 25px;">#</th>
                        <th>Part / Material Description</th>
                        <th>OEM Part #</th>
                        <th style="text-align: center;">Qty</th>
                        <th>Unit</th>
                        <th style="text-align: right;">Unit Price (ETB)</th>
                        <th style="text-align: right;">Total Price (ETB)</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($workOrder->parts as $idx => $part)
                        <tr>
                            <td>{{ $idx + 1 }}</td>
                            <td><strong>{{ $part->part_name }}</strong></td>
                            <td style="font-family: monospace;">{{ $part->part_number ?: '-' }}</td>
                            <td style="text-align: center; font-weight: bold;">{{ number_format((float)$part->quantity, 2) }}</td>
                            <td>{{ $part->unit_of_measurement }}</td>
                            <td style="text-align: right;">{{ number_format((float)$part->unit_price, 2) }}</td>
                            <td style="text-align: right; font-weight: bold;">{{ number_format((float)$part->total_price, 2) }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" style="text-align: center; color: #9ca3af; padding: 15px;">No inventory spare parts or fluids logged for this work order.</td>
                        </tr>
                    @endforelse

                    <tr class="total-row">
                        <td colspan="6" style="text-align: right;">Total Parts & Lubricants Cost:</td>
                        <td style="text-align: right; font-weight: bold;">{{ number_format((float)$workOrder->parts_cost, 2) }} ETB</td>
                    </tr>
                    @if((float)$workOrder->labor_cost > 0)
                        <tr class="total-row">
                            <td colspan="6" style="text-align: right;">Mechanic Labor Cost:</td>
                            <td style="text-align: right; font-weight: bold;">{{ number_format((float)$workOrder->labor_cost, 2) }} ETB</td>
                        </tr>
                    @endif
                    @if((float)$workOrder->external_cost > 0)
                        <tr class="total-row">
                            <td colspan="6" style="text-align: right;">External Specialist / Lathe Workshop Cost:</td>
                            <td style="text-align: right; font-weight: bold;">{{ number_format((float)$workOrder->external_cost, 2) }} ETB</td>
                        </tr>
                    @endif
                    <tr class="grand-total">
                        <td colspan="6" style="text-align: right;">TOTAL MAINTENANCE COST:</td>
                        <td style="text-align: right;">{{ number_format((float)$workOrder->total_cost, 2) }} ETB</td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div class="signatures">
            <div class="sig-box">
                <div class="role">Equipment Operator</div>
                <div class="name">Machine Operator</div>
            </div>
            <div class="sig-box">
                <div class="role">Assigned Lead Mechanic</div>
                <div class="name">{{ $workOrder->assignedMechanic ? $workOrder->assignedMechanic->name : 'Lead Technician' }}</div>
            </div>
            <div class="sig-box">
                <div class="role">Plant & Fleet Engineer</div>
                <div class="name">Workshop Supervisor</div>
            </div>
            <div class="sig-box">
                <div class="role">Project Manager / Director</div>
                <div class="name">{{ $workOrder->completer ? $workOrder->completer->name : 'Approved' }}</div>
            </div>
        </div>

        <div class="footer">
            Official Equipment Job Card • Natanem Engineering ERP • Verification URL: {{ $verificationUrl }} • Printed on {{ now()->format('Y-m-d H:i:s') }}
        </div>
    </div>
</body>
</html>
