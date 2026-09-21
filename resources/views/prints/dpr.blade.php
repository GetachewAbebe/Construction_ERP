<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daily Progress Report #DPR-{{ str_pad((string) $report->id, 5, '0', STR_PAD_LEFT) }} - {{ $report->project?->name ?? 'Project' }}</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; }
        body { background: #f3f4f6; padding: 30px 15px; color: #1f2937; }
        .dpr-container { max-width: 850px; margin: 0 auto; background: #fff; padding: 40px; border-radius: 8px; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1); }
        .header { display: flex; justify-content: space-between; align-items: flex-start; border-bottom: 2px solid #0f766e; padding-bottom: 20px; margin-bottom: 25px; }
        .logo-title h1 { font-size: 22px; font-weight: 800; color: #0f766e; letter-spacing: 0.5px; }
        .logo-title p { font-size: 11px; color: #6b7280; text-transform: uppercase; margin-top: 3px; }
        .dpr-meta { text-align: right; }
        .dpr-meta .badge { font-size: 16px; font-weight: bold; color: #0f766e; }
        .dpr-meta p { font-size: 12px; color: #4b5563; margin-top: 4px; }
        .grid-3 { display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 15px; margin-bottom: 20px; }
        .grid-2 { display: grid; grid-template-columns: 1fr 1fr; gap: 15px; margin-bottom: 20px; }
        .field-group { background: #f9fafb; padding: 12px 16px; border-radius: 6px; border: 1px solid #e5e7eb; }
        .field-group .label { font-size: 10px; font-weight: 700; color: #6b7280; text-transform: uppercase; margin-bottom: 4px; }
        .field-group .value { font-size: 14px; font-weight: 600; color: #111827; }
        .section-box { background: #f9fafb; padding: 16px; border-radius: 6px; border: 1px solid #e5e7eb; margin-bottom: 20px; }
        .section-box .label { font-size: 11px; font-weight: 800; color: #0f766e; text-transform: uppercase; margin-bottom: 8px; display: flex; align-items: center; gap: 6px; }
        .section-box .value { font-size: 13px; line-height: 1.6; color: #374151; white-space: pre-line; }
        .safety-alert { background: #fef2f2; border-color: #fecaca; }
        .safety-alert .label { color: #dc2626; }
        .photos-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 15px; margin-top: 10px; }
        .photo-card { border: 1px solid #e5e7eb; border-radius: 6px; overflow: hidden; background: #fff; }
        .photo-card img { width: 100%; height: 200px; object-fit: cover; display: block; }
        .photo-card .caption { padding: 6px 10px; font-size: 10px; color: #6b7280; text-align: center; }
        .signatures { display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 20px; margin-top: 40px; }
        .sig-box { border-top: 1px solid #9ca3af; padding-top: 10px; text-align: center; }
        .sig-box .role { font-size: 11px; font-weight: bold; color: #4b5563; text-transform: uppercase; }
        .sig-box .name { font-size: 12px; color: #111827; margin-top: 4px; }
        .footer { margin-top: 30px; border-top: 1px dashed #d1d5db; padding-top: 15px; font-size: 10px; color: #9ca3af; text-align: center; }
        .no-print { text-align: center; margin-bottom: 20px; }
        .print-btn { background: #0f766e; color: #fff; border: none; padding: 10px 24px; font-size: 14px; font-weight: bold; border-radius: 6px; cursor: pointer; }
        @media print {
            body { background: #fff; padding: 0; }
            .dpr-container { box-shadow: none; padding: 20px; max-width: 100%; }
            .no-print { display: none; }
            .photo-card img { height: 180px; }
        }
    </style>
</head>
<body>
    <div class="no-print">
        <button onclick="window.print()" class="print-btn">🖨️ Print Daily Report / Save PDF</button>
    </div>

    <div class="dpr-container">
        <div class="header">
            <div class="logo-title">
                <h1>NATANEM ENGINEERING</h1>
                <p>Construction, General Contracting & Engineering</p>
            </div>
            <div class="dpr-meta">
                <div class="badge">DAILY PROGRESS REPORT (DPR)</div>
                <p>#DPR-{{ str_pad((string) $report->id, 5, '0', STR_PAD_LEFT) }}</p>
                <p>Date: {{ $report->report_date ? $report->report_date->format('F d, Y') : now()->format('F d, Y') }}</p>
            </div>
        </div>

        <div class="grid-3">
            <div class="field-group">
                <div class="label">Project Site</div>
                <div class="value">{{ $report->project?->name ?? 'N/A' }}</div>
            </div>
            <div class="field-group">
                <div class="label">Weather Conditions</div>
                <div class="value">{{ $report->weather ?? 'Clear / Normal' }}</div>
            </div>
            <div class="field-group">
                <div class="label">Site Workforce Tally</div>
                <div class="value">{{ $report->manpower_count ?? 0 }} Personnel</div>
            </div>
        </div>

        <div class="section-box">
            <div class="label">🔨 Construction Works & Activities Executed</div>
            <div class="value">{{ $report->work_performed }}</div>
        </div>

        <div class="grid-2">
            <div class="section-box">
                <div class="label">📦 Materials Received on Site</div>
                <div class="value">{{ $report->materials_received ?: 'None logged for this work cycle.' }}</div>
            </div>
            <div class="section-box">
                <div class="label">🚜 Heavy Plant & Machinery Deployed</div>
                <div class="value">{{ $report->machinery_deployed ?: 'None logged for this work cycle.' }}</div>
            </div>
        </div>

        @if($report->safety_incidents)
            <div class="section-box safety-alert">
                <div class="label">⚠️ Safety / Health / Environment Incident Log</div>
                <div class="value">{{ $report->safety_incidents }}</div>
            </div>
        @endif

        @if(!empty($report->photos) && is_array($report->photos))
            <div class="section-box">
                <div class="label">📷 Visual Progress & Photographic Evidence ({{ count($report->photos) }} Captured)</div>
                <div class="photos-grid">
                    @foreach($report->photos as $index => $photo)
                        <div class="photo-card">
                            <img src="{{ $photo }}" alt="Site Photo {{ $index + 1 }}">
                            <div class="caption">Field Evidence #{{ $index + 1 }} • {{ $report->project?->name }}</div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        <div class="signatures">
            <div class="sig-box">
                <div class="role">Prepared By (Site Engineer)</div>
                <div class="name">{{ $report->author?->name ?? 'Site Engineer' }}</div>
            </div>
            <div class="sig-box">
                <div class="role">Project Manager / Resident Eng.</div>
                <div class="name">{{ $report->project?->manager?->name ?? 'Authorized Engineer' }}</div>
            </div>
            <div class="sig-box">
                <div class="role">Consultant / Client Verification</div>
                <div class="name">Supervising Consultant</div>
            </div>
        </div>

        <div class="footer">
            Generated via Natanem Engineering Enterprise Resource Planning (ERP) System • Official Site Record
        </div>
    </div>
</body>
</html>
