<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Interim Payment Certificate #{{ $certificate->certificate_no }} - Natanem Engineering</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; }
        body { background: #f3f4f6; padding: 30px 15px; color: #1f2937; }
        .voucher-container { max-width: 860px; margin: 0 auto; background: #fff; padding: 40px; border-radius: 8px; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1); }
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
        table { width: 100%; border-collapse: collapse; margin-top: 10px; font-size: 13px; }
        th { background: #1e3a8a; color: #fff; font-size: 11px; text-transform: uppercase; padding: 10px 12px; text-align: left; }
        td { border-bottom: 1px solid #e5e7eb; padding: 9px 12px; color: #374151; }
        tr.highlight td { background: #f8fafc; font-weight: 600; }
        tr.grand-total td { background: #eff6ff; font-size: 15px; font-weight: 800; color: #1e3a8a; border-top: 2px solid #1e3a8a; border-bottom: 2px solid #1e3a8a; }
        .signatures { display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 20px; margin-top: 50px; }
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
        <button onclick="window.print()" class="print-btn">🖨️ Print Interim Payment Certificate / Save PDF</button>
    </div>

    <div class="voucher-container">
        <div class="header">
            <div class="logo-title">
                <h1>NATANEM ENGINEERING PLC</h1>
                <p>General Building & Civil Engineering Contractor</p>
                <div style="font-size: 11px; color: #4b5563; margin-top: 6px;">Addis Ababa, Ethiopia • Commercial Registration #01-9921</div>
            </div>
            <div class="voucher-meta" style="display: flex; align-items: center; gap: 16px;">
                @if(!empty($qrCodeSvg))
                    <div style="text-align: center;">
                        {!! $qrCodeSvg !!}
                        <div style="font-size: 8px; font-weight: 700; color: #1e3a8a; margin-top: 2px; letter-spacing: 0.5px;">SCAN TO VERIFY</div>
                    </div>
                @endif
                <div>
                    <div class="badge">INTERIM PAYMENT CERTIFICATE</div>
                    <p><strong>Certificate #:</strong> {{ $certificate->certificate_no }} (IPC #{{ $certificate->ipc_sequence }})</p>
                    <p><strong>Submission Date:</strong> {{ $certificate->submission_date ? $certificate->submission_date->format('F d, Y') : now()->format('F d, Y') }}</p>
                    <p><strong>Status:</strong> <span style="text-transform: uppercase; color: #1e3a8a; font-weight: bold;">{{ str_replace('_', ' ', $certificate->status) }}</span></p>
                </div>
            </div>
        </div>

        <div class="grid">
            <div class="field-group">
                <div class="label">Project Title & Location</div>
                <div class="value">{{ $certificate->project ? $certificate->project->name : 'Construction Project' }}</div>
                <div style="font-size: 12px; color: #4b5563; margin-top: 4px;">
                    Location: {{ $certificate->project?->location ?? 'Site Location' }}<br>
                    Contract Sum: <strong>{{ number_format((float)($certificate->project?->budget ?? 0), 2) }} ETB</strong>
                </div>
            </div>
            <div class="field-group">
                <div class="label">Employer / Client & Consultant</div>
                <div class="value">{{ $certificate->client_name ?: 'Client / Employer' }}</div>
                <div style="font-size: 12px; color: #4b5563; margin-top: 4px;">
                    Supervising Engineer: <strong>{{ $certificate->consultant_name ?: 'Consultant Resident Engineer' }}</strong><br>
                    Billing Period: {{ $certificate->period_start ? $certificate->period_start->format('M d, Y') : 'N/A' }} — {{ $certificate->period_end ? $certificate->period_end->format('M d, Y') : 'N/A' }}
                </div>
            </div>
        </div>

        <div style="background: #f9fafb; border: 1px solid #e5e7eb; border-radius: 6px; padding: 12px 16px; margin-bottom: 20px; font-size: 12px;">
            <strong style="color: #4b5563; text-transform: uppercase; font-size: 10px;">Scope of Work Completed This Period:</strong><br>
            <span style="color: #111827; font-weight: 500;">{{ $certificate->work_description }}</span>
        </div>

        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th style="width: 10%;">Item</th>
                        <th style="width: 65%;">Statement of Claim & Valuation Breakdown</th>
                        <th style="width: 25%; text-align: right;">Amount (ETB)</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>1.0</td>
                        <td>Cumulative Gross Value of Permanent Work Done to Date</td>
                        <td style="text-align: right; font-weight: 600;">{{ number_format((float)$certificate->cumulative_gross_amount, 2) }}</td>
                    </tr>
                    <tr>
                        <td>2.0</td>
                        <td>Less: Previous Certified Cumulative Gross Value</td>
                        <td style="text-align: right; color: #6b7280;">({{ number_format((float)$certificate->previous_gross_amount, 2) }})</td>
                    </tr>
                    <tr class="highlight">
                        <td><strong>3.0</strong></td>
                        <td><strong>Current Gross Value of Work Done This Period (1.0 - 2.0)</strong></td>
                        <td style="text-align: right; font-weight: bold; color: #1e3a8a;">{{ number_format((float)$certificate->current_gross_amount, 2) }}</td>
                    </tr>
                    @if((float)$certificate->materials_on_site > 0)
                        <tr>
                            <td>4.0</td>
                            <td>Add: Materials on Site (MOS 80% Incorporation Value)</td>
                            <td style="text-align: right; font-weight: 600;">{{ number_format((float)$certificate->materials_on_site, 2) }}</td>
                        </tr>
                    @endif
                    <tr>
                        <td>5.0</td>
                        <td>Less: Retention Money Deduction ({{ $certificate->retention_rate }}%)</td>
                        <td style="text-align: right; color: #b91c1c;">({{ number_format((float)$certificate->retention_deduction, 2) }})</td>
                    </tr>
                    @if((float)$certificate->advance_deduction > 0)
                        <tr>
                            <td>6.0</td>
                            <td>Less: Mobilization Advance Recoupment ({{ $certificate->advance_recoupment_rate }}%)</td>
                            <td style="text-align: right; color: #b91c1c;">({{ number_format((float)$certificate->advance_deduction, 2) }})</td>
                        </tr>
                    @endif
                    @if((float)$certificate->other_deductions > 0)
                        <tr>
                            <td>7.0</td>
                            <td>Less: Other Contractual Deductions / Penalties</td>
                            <td style="text-align: right; color: #b91c1c;">({{ number_format((float)$certificate->other_deductions, 2) }})</td>
                        </tr>
                    @endif
                    <tr class="highlight">
                        <td><strong>8.0</strong></td>
                        <td><strong>Subtotal Net Interim Valuation</strong></td>
                        <td style="text-align: right; font-weight: bold;">{{ number_format((float)$certificate->subtotal_net_amount, 2) }}</td>
                    </tr>
                    <tr>
                        <td>9.0</td>
                        <td>Value Added Tax (VAT {{ $certificate->tax_rate }}%)</td>
                        <td style="text-align: right; font-weight: 600;">{{ number_format((float)$certificate->tax_amount, 2) }}</td>
                    </tr>
                    <tr class="grand-total">
                        <td><strong>10.0</strong></td>
                        <td><strong>TOTAL CERTIFIED NET AMOUNT PAYABLE BY CLIENT (8.0 + 9.0)</strong></td>
                        <td style="text-align: right;"><strong>{{ number_format((float)$certificate->total_certified_amount, 2) }} ETB</strong></td>
                    </tr>
                    <tr>
                        <td>11.0</td>
                        <td>Amount Collected / Received to Date</td>
                        <td style="text-align: right; color: #047857; font-weight: 600;">{{ number_format((float)$certificate->amount_paid, 2) }} ETB</td>
                    </tr>
                    <tr class="highlight">
                        <td><strong>12.0</strong></td>
                        <td><strong>Current Outstanding Balance Due</strong></td>
                        <td style="text-align: right; font-weight: bold; color: {{ (float)$certificate->balance_due > 0 ? '#b91c1c' : '#047857' }};">
                            {{ number_format((float)$certificate->balance_due, 2) }} ETB
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div class="signatures">
            <div class="sig-box">
                <div class="role">Submitted By (Contractor)</div>
                <div class="name">Project Manager / Commercial Director</div>
                <div style="font-size: 10px; color: #6b7280; margin-top: 2px;">Natanem Engineering PLC</div>
            </div>
            <div class="sig-box">
                <div class="role">Certified By (Supervising Engineer)</div>
                <div class="name">Resident Consultant Engineer</div>
                <div style="font-size: 10px; color: #6b7280; margin-top: 2px;">Consulting Engineering Firm</div>
            </div>
            <div class="sig-box">
                <div class="role">Approved For Payment (Employer)</div>
                <div class="name">Authorized Employer Representative</div>
                <div style="font-size: 10px; color: #6b7280; margin-top: 2px;">Project Owner / Client</div>
            </div>
        </div>

        <div class="footer">
            Certified Civil Engineering Payment Certificate • Natanem Engineering Enterprise ERP • Retention money tracked in project balance sheet
        </div>
    </div>
</body>
</html>
