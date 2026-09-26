<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Site Casual Labor Muster Roll #{{ $musterRoll->muster_roll_no }} - Natanem Engineering</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; }
        body { background: #f3f4f6; padding: 30px 15px; color: #1f2937; }
        .voucher-container { max-width: 980px; margin: 0 auto; background: #fff; padding: 35px; border-radius: 8px; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1); }
        .header { display: flex; justify-content: space-between; align-items: flex-start; border-bottom: 2px solid #1e3a8a; padding-bottom: 18px; margin-bottom: 20px; }
        .logo-title h1 { font-size: 22px; font-weight: 800; color: #1e3a8a; letter-spacing: 0.5px; }
        .logo-title p { font-size: 11px; color: #6b7280; text-transform: uppercase; margin-top: 3px; }
        .voucher-meta { text-align: right; }
        .voucher-meta .badge { font-size: 18px; font-weight: bold; color: #1e3a8a; }
        .voucher-meta p { font-size: 12px; color: #4b5563; margin-top: 4px; }
        .grid { display: grid; grid-template-columns: 1fr 1fr; gap: 15px; margin-bottom: 20px; }
        .field-group { background: #f9fafb; padding: 10px 14px; border-radius: 6px; border: 1px solid #e5e7eb; }
        .field-group .label { font-size: 10px; font-weight: 700; color: #6b7280; text-transform: uppercase; margin-bottom: 3px; }
        .field-group .value { font-size: 13px; font-weight: 600; color: #111827; }
        .table-container { margin-bottom: 20px; }
        table { width: 100%; border-collapse: collapse; margin-top: 8px; font-size: 11px; }
        th { background: #1e3a8a; color: #fff; font-size: 10px; text-transform: uppercase; padding: 8px 8px; text-align: left; }
        td { border-bottom: 1px solid #e5e7eb; padding: 7px 8px; color: #374151; }
        tr.grand-total td { background: #eff6ff; font-size: 12px; font-weight: 800; color: #1e3a8a; border-top: 2px solid #1e3a8a; border-bottom: 2px solid #1e3a8a; }
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
        <button onclick="window.print()" class="print-btn">🖨️ Print Daily Muster Roll / Save PDF</button>
    </div>

    <div class="voucher-container">
        <div class="header">
            <div class="logo-title">
                <h1>NATANEM ENGINEERING PLC</h1>
                <p>General Building & Civil Engineering Contractor • Site Operations</p>
                <div style="font-size: 10px; color: #4b5563; margin-top: 4px;">Addis Ababa, Ethiopia • Construction Site Daily Muster Roll Sheet</div>
            </div>
            <div class="voucher-meta" style="display: flex; align-items: center; gap: 14px;">
                @if(!empty($qrCodeSvg))
                    <div style="text-align: center;">
                        {!! $qrCodeSvg !!}
                        <div style="font-size: 8px; font-weight: 700; color: #1e3a8a; margin-top: 2px; letter-spacing: 0.5px;">SCAN TO VERIFY</div>
                    </div>
                @endif
                <div>
                    <div class="badge">DAILY MUSTER ROLL</div>
                    <p><strong>Sheet #:</strong> {{ $musterRoll->muster_roll_no }}</p>
                    <p><strong>Work Date:</strong> {{ $musterRoll->date ? $musterRoll->date->format('l, F d, Y') : '-' }}</p>
                    <p><strong>Status:</strong> <span style="text-transform: uppercase; color: #1e3a8a; font-weight: bold;">{{ $musterRoll->status }}</span></p>
                </div>
            </div>
        </div>

        <div class="grid">
            <div class="field-group">
                <div class="label">Project & Work Site</div>
                <div class="value">{{ $musterRoll->project ? $musterRoll->project->name : 'N/A' }}</div>
                <div style="font-size: 11px; color: #4b5563; margin-top: 3px;">
                    Site Location: {{ $musterRoll->project?->location ?? 'Site' }}
                </div>
            </div>
            <div class="field-group">
                <div class="label">Work Description / Activities Carried Out</div>
                <div class="value">{{ $musterRoll->title ?: 'Site Operations & Civil Works' }}</div>
                <div style="font-size: 11px; color: #4b5563; margin-top: 3px;">
                    Prepared by: {{ $musterRoll->supervisor ? $musterRoll->supervisor->name : 'Site Foreman' }}
                </div>
            </div>
        </div>

        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th style="width: 25px;">#</th>
                        <th>Worker ID</th>
                        <th>Worker Full Name</th>
                        <th>Trade / Craft</th>
                        <th>Task Assigned</th>
                        <th style="text-align: center;">Days</th>
                        <th style="text-align: right;">Rate (ETB)</th>
                        <th style="text-align: right;">Regular (ETB)</th>
                        <th style="text-align: center;">OT (Hrs)</th>
                        <th style="text-align: right;">OT (ETB)</th>
                        <th style="text-align: right;">Ded. (ETB)</th>
                        <th style="text-align: right;">Net Payable</th>
                        <th style="text-align: center; width: 120px;">Worker Signature / Thumbprint</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($musterRoll->items as $index => $item)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td><span style="font-family: monospace; font-weight: bold;">{{ $item->casualLaborer ? $item->casualLaborer->worker_code : '-' }}</span></td>
                            <td><strong>{{ $item->casualLaborer ? $item->casualLaborer->full_name : 'Worker' }}</strong></td>
                            <td>{{ $item->trade }}</td>
                            <td>{{ $item->task_assigned ?: '-' }}</td>
                            <td style="text-align: center;">{{ number_format((float)$item->days_worked, 1) }}</td>
                            <td style="text-align: right;">{{ number_format((float)$item->daily_rate, 2) }}</td>
                            <td style="text-align: right;">{{ number_format((float)$item->regular_amount, 2) }}</td>
                            <td style="text-align: center;">{{ (float)$item->overtime_hours > 0 ? number_format((float)$item->overtime_hours, 1) : '-' }}</td>
                            <td style="text-align: right;">{{ number_format((float)$item->overtime_amount, 2) }}</td>
                            <td style="text-align: right; color: #dc2626;">{{ (float)$item->deduction_amount > 0 ? '-' . number_format((float)$item->deduction_amount, 2) : '0.00' }}</td>
                            <td style="text-align: right; font-weight: bold; color: #1e3a8a;">{{ number_format((float)$item->net_amount, 2) }}</td>
                            <td style="border-bottom: 1px solid #cbd5e1; height: 30px;"></td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="13" style="text-align: center; color: #9ca3af; padding: 20px;">No casual workers recorded on this muster roll sheet.</td>
                        </tr>
                    @endforelse

                    <tr class="grand-total">
                        <td colspan="5" style="text-align: right;">TOTALS ({{ $musterRoll->total_workers }} WORKERS PRESENT):</td>
                        <td style="text-align: center;">{{ number_format((float)$musterRoll->items->sum('days_worked'), 1) }}</td>
                        <td></td>
                        <td style="text-align: right;">{{ number_format((float)$musterRoll->total_regular_amount, 2) }}</td>
                        <td style="text-align: center;">{{ number_format((float)$musterRoll->items->sum('overtime_hours'), 1) }}</td>
                        <td style="text-align: right;">{{ number_format((float)$musterRoll->total_overtime_amount, 2) }}</td>
                        <td style="text-align: right; color: #dc2626;">-{{ number_format((float)$musterRoll->total_deductions, 2) }}</td>
                        <td style="text-align: right; color: #1e3a8a; font-size: 13px;">{{ number_format((float)$musterRoll->total_net_amount, 2) }} ETB</td>
                        <td></td>
                    </tr>
                </tbody>
            </table>
        </div>

        @if($musterRoll->notes)
            <div style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 6px; padding: 10px 14px; margin-bottom: 20px; font-size: 11px;">
                <strong>Remarks / Site Conditions:</strong> {{ $musterRoll->notes }}
            </div>
        @endif

        @if($musterRoll->status === 'paid')
            <div style="background: #ecfdf5; border: 1px solid #a7f3d0; border-radius: 6px; padding: 10px 14px; margin-bottom: 20px; font-size: 11px; color: #065f46;">
                <strong>✓ Wage Disbursement Confirmed:</strong> Disbursed via <strong>{{ strtoupper((string)$musterRoll->payment_method) }}</strong>
                @if($musterRoll->payout_reference) (Ref: {{ $musterRoll->payout_reference }}) @endif
                on {{ $musterRoll->paid_at ? $musterRoll->paid_at->format('M d, Y H:i') : '' }} by {{ $musterRoll->paidBy ? $musterRoll->paidBy->name : 'Site Cashier' }}.
                @if($musterRoll->expense_id) • <em>Direct Project Expense #{{ $musterRoll->muster_roll_no }} linked.</em> @endif
            </div>
        @endif

        <div class="signatures">
            <div class="sig-box">
                <div class="role">Prepared By (Foreman)</div>
                <div class="name">{{ $musterRoll->supervisor ? $musterRoll->supervisor->name : 'Site Supervisor' }}</div>
            </div>
            <div class="sig-box">
                <div class="role">Checked By (Site Engineer)</div>
                <div class="name">Resident Site Engineer</div>
            </div>
            <div class="sig-box">
                <div class="role">Approved By (Project Manager)</div>
                <div class="name">{{ $musterRoll->approvedBy ? $musterRoll->approvedBy->name : 'Pending Approval' }}</div>
            </div>
            <div class="sig-box">
                <div class="role">Disbursed By (Cashier)</div>
                <div class="name">{{ $musterRoll->paidBy ? $musterRoll->paidBy->name : 'Site Cashier' }}</div>
            </div>
        </div>

        <div class="footer">
            Generated by Natanem Engineering ERP • Document Ref: {{ $musterRoll->muster_roll_no }} • Verification URL: {{ $verificationUrl }} • Printed on {{ now()->format('Y-m-d H:i:s') }}
        </div>
    </div>
</body>
</html>
