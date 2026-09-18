<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Voucher #EXP-{{ str_pad((string) $expense->id, 5, '0', STR_PAD_LEFT) }} - Natanem Engineering</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; }
        body { background: #f3f4f6; padding: 30px 15px; color: #1f2937; }
        .voucher-container { max-width: 800px; margin: 0 auto; background: #fff; padding: 40px; border-radius: 8px; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1); }
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
        .description-box { background: #f9fafb; padding: 16px; border-radius: 6px; border: 1px solid #e5e7eb; margin-bottom: 30px; }
        .description-box .label { font-size: 10px; font-weight: 700; color: #6b7280; text-transform: uppercase; margin-bottom: 6px; }
        .description-box .value { font-size: 13px; line-height: 1.5; color: #374151; }
        .amount-summary { background: #eff6ff; border: 2px solid #bfdbfe; border-radius: 6px; padding: 16px 20px; display: flex; justify-content: space-between; align-items: center; margin-bottom: 40px; }
        .amount-summary .title { font-size: 13px; font-weight: 700; color: #1e40af; text-transform: uppercase; }
        .amount-summary .amount { font-size: 22px; font-weight: 800; color: #1e3a8a; }
        .signatures { display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 20px; margin-top: 50px; }
        .sig-box { border-top: 1px solid #9ca3af; padding-top: 10px; text-align: center; }
        .sig-box .role { font-size: 11px; font-weight: bold; color: #4b5563; text-transform: uppercase; }
        .sig-box .name { font-size: 12px; color: #111827; margin-top: 4px; }
        .footer { margin-top: 40px; border-top: 1px dashed #d1d5db; padding-top: 15px; font-size: 10px; color: #9ca3af; text-align: center; }
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
        <button onclick="window.print()" class="print-btn">🖨️ Print Voucher / Save PDF</button>
    </div>

    <div class="voucher-container">
        <div class="header">
            <div class="logo-title">
                <h1>NATANEM ENGINEERING</h1>
                <p>Construction, General Contracting & Engineering</p>
            </div>
            <div class="voucher-meta">
                <div class="badge">PAYMENT VOUCHER</div>
                <p>#EXP-{{ str_pad((string) $expense->id, 5, '0', STR_PAD_LEFT) }}</p>
                <p>Date: {{ $expense->expense_date ? $expense->expense_date->format('F d, Y') : now()->format('F d, Y') }}</p>
            </div>
        </div>

        <div class="grid">
            <div class="field-group">
                <div class="label">Project / Cost Center</div>
                <div class="value">{{ $expense->project->name ?? 'Headquarters / Overhead' }}</div>
            </div>
            <div class="field-group">
                <div class="label">Expense Category</div>
                <div class="value">{{ ucfirst($expense->category) }}</div>
            </div>
            <div class="field-group">
                <div class="label">Prepared / Requested By</div>
                <div class="value">{{ $expense->user->name ?? 'Staff Member' }}</div>
            </div>
            <div class="field-group">
                <div class="label">Authorization Status</div>
                <div class="value" style="color: {{ $expense->status === 'approved' ? '#059669' : '#d97706' }};">
                    {{ strtoupper($expense->status) }}
                </div>
            </div>
        </div>

        <div class="description-box">
            <div class="label">Description & Expenditure Justification</div>
            <div class="value">{{ $expense->description ?: 'Payment disbursed for project operations and site procurement requirements.' }}</div>
        </div>

        <div class="amount-summary">
            <div class="title">Total Disbursed Amount</div>
            <div class="amount">ETB {{ number_format((float) $expense->amount, 2) }}</div>
        </div>

        <div class="signatures">
            <div class="sig-box">
                <div class="role">Prepared By</div>
                <div class="name">{{ $expense->user->name ?? 'Staff' }}</div>
            </div>
            <div class="sig-box">
                <div class="role">Checked / Verified By</div>
                <div class="name">Internal Auditor</div>
            </div>
            <div class="sig-box">
                <div class="role">Authorized By</div>
                <div class="name">{{ $expense->approvedBy->name ?? 'Finance / Managing Director' }}</div>
            </div>
        </div>

        <div class="footer">
            Generated electronically by Natanem Engineering ERP on {{ now()->format('Y-m-d H:i:s') }} · Valid without physical alteration.
        </div>
    </div>
</body>
</html>
