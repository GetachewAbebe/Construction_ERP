@extends('layouts.app')
@section('title', 'Template Preview')

@section('content')
<div class="row align-items-center mb-4 stagger-entrance">
    <div class="col">
        <h1 class="h3 mb-1 fw-800 text-erp-deep">Template Preview</h1>
        <p class="text-muted mb-0">Visual rendering of: <span class="text-erp-deep fw-800">{{ $notificationTemplate->name }}</span></p>
    </div>
    <div class="col-auto d-flex gap-2">
        <a href="{{ route('admin.notification-templates.edit', $notificationTemplate) }}" class="btn btn-white rounded-pill px-4 shadow-sm border-0">
            <i class="bi bi-pencil-square me-2"></i>Edit Template
        </a>
        <a href="{{ route('admin.notification-templates.index') }}" class="btn btn-white rounded-pill px-4 shadow-sm border-0">
            <i class="bi bi-arrow-left me-2"></i>Back to Templates
        </a>
    </div>
</div>

<div class="row g-4">
    <div class="col-lg-8 stagger-entrance">
        <div class="card hardened-glass border-0 overflow-hidden shadow-lg">
            <div class="card-header bg-light-soft border-0 p-4">
                <h5 class="fw-800 text-erp-deep mb-0 d-flex align-items-center gap-2">
                    <i class="bi bi-eye-fill text-primary"></i>
                    Rendered Output (Sample Data)
                </h5>
            </div>
            <div class="card-body p-4">
                @if($notificationTemplate->type === 'email')
                    <div class="mb-4 p-3 bg-light-soft rounded-4">
                        <div class="small fw-800 text-muted text-uppercase mb-2">Subject:</div>
                        <div class="fw-700 text-dark">{{ $notificationTemplate->subject }}</div>
                    </div>
                @endif

                <div class="p-4 bg-white rounded-4 border shadow-sm" style="min-height: 300px;">
                    <div style="white-space: pre-wrap; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif; line-height: 1.6;">{{ $renderedBody }}</div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-4 stagger-entrance">
        <div class="card hardened-glass border-0 overflow-hidden shadow-sm mb-4">
            <div class="card-header bg-light-soft border-0 p-4">
                <h6 class="fw-800 text-erp-deep mb-0">Template Information</h6>
            </div>
            <div class="card-body p-4">
                <div class="mb-3">
                    <div class="small fw-800 text-muted text-uppercase mb-1">Template Key</div>
                    <code class="badge bg-light text-dark border px-3 py-2 font-monospace">{{ $notificationTemplate->key }}</code>
                </div>

                <div class="mb-3">
                    <div class="small fw-800 text-muted text-uppercase mb-1">Type</div>
                    @php
                        $typeClass = match($notificationTemplate->type) {
                            'email' => 'bg-primary-soft text-primary',
                            'notification' => 'bg-success-soft text-success',
                            'sms' => 'bg-warning-soft text-warning',
                            default => 'bg-secondary-soft text-secondary'
                        };
                        $typeIcon = match($notificationTemplate->type) {
                            'email' => 'envelope-fill',
                            'notification' => 'bell-fill',
                            'sms' => 'phone-fill',
                            default => 'chat-fill'
                        };
                    @endphp
                    <span class="badge {{ $typeClass }} rounded-pill px-3 py-2 border-0 fw-800">
                        <i class="bi bi-{{ $typeIcon }} me-1"></i>{{ ucfirst($notificationTemplate->type) }}
                    </span>
                </div>

                <div class="mb-3">
                    <div class="small fw-800 text-muted text-uppercase mb-1">Status</div>
                    @if($notificationTemplate->is_active)
                        <span class="badge bg-success-soft text-success rounded-pill px-3 py-2 border-0 fw-700">
                            <i class="bi bi-check-circle-fill me-1"></i>Active
                        </span>
                    @else
                        <span class="badge bg-secondary-soft text-secondary rounded-pill px-3 py-2 border-0 fw-700">
                            <i class="bi bi-pause-circle-fill me-1"></i>Inactive
                        </span>
                    @endif
                </div>
            </div>
        </div>

        <div class="card hardened-glass border-0 overflow-hidden shadow-sm">
            <div class="card-header bg-light-soft border-0 p-4">
                <h6 class="fw-800 text-erp-deep mb-0">Sample Variable Values</h6>
            </div>
            <div class="card-body p-4">
                @if(!empty($sampleData))
                    <div class="list-group list-group-flush">
                        @foreach($sampleData as $key => $value)
                            <div class="list-group-item bg-transparent border-0 px-0 py-2">
                                <code class="badge bg-light text-dark border px-2 py-1 font-monospace x-small">{<span>{{ $key }}</span>}</code>
                                <div class="text-muted small mt-1 fw-600">{{ $value }}</div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <p class="text-muted italic small mb-0">No variables defined for this template.</p>
                @endif
            </div>
        </div>
    </div>
</div>

<div class="row mt-4">
    <div class="col-12 stagger-entrance">
        <div class="card hardened-glass border-0 overflow-hidden shadow-sm">
            <div class="card-body p-4">
                <div class="d-flex align-items-start gap-3">
                    <i class="bi bi-info-circle-fill text-info fs-4"></i>
                    <div>
                        <h6 class="fw-800 text-erp-deep mb-2">Preview Notice</h6>
                        <p class="text-muted mb-0 small fw-600">
                            This preview shows how the template will appear with sample data. In production, actual values will replace the variable placeholders. 
                            The formatting and styling may vary depending on the email client or notification system used for delivery.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
