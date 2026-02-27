@extends('layouts.app')
@section('title', 'System Audit Trail')

@section('content')
<div class="row align-items-center mb-4 stagger-entrance">
    <div class="col">
        <h1 class="h3 mb-1 fw-800 text-erp-deep">System-Wide Audit Trail</h1>
        <p class="text-muted mb-0">High-fidelity chronological record of all administrative and operational data modifications.</p>
    </div>
</div>

<div class="card hardened-glass border-0 overflow-hidden stagger-entrance shadow-sm">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="bg-light-soft text-erp-deep">
                <tr>
                    <th class="ps-4 py-3">Temporal Marker</th>
                    <th class="py-3">Originator</th>
                    <th class="py-3">Action Signature</th>
                    <th class="py-3">Target Resource</th>
                    <th class="py-3">Schema Delta</th>
                    <th class="pe-4 py-3 text-end">Connection Metadata</th>
                </tr>
            </thead>
            <tbody>
                @forelse($logs as $log)
                    <tr>
                        <td class="ps-4">
                            <div class="d-flex flex-column">
                                <span class="fw-800 text-erp-deep">{{ $log->created_at->format('M d, Y') }}</span>
                                <span class="text-muted small fw-bold">{{ $log->created_at->format('H:i:s') }}</span>
                            </div>
                        </td>
                        <td>
                            <div class="d-flex align-items-center gap-2">
                                <div class="avatar-xs bg-erp-deep text-white rounded-circle d-flex align-items-center justify-content-center fw-800" style="width: 32px; height: 32px; font-size: 11px;">
                                    {{ substr($log->user->name ?? 'S', 0, 1) }}
                                </div>
                                <div>
                                    <div class="fw-700 text-dark small">{{ $log->user->name ?? 'System Process' }}</div>
                                    <div class="text-muted x-small fw-bold">{{ $log->user->role ?? 'N/A' }}</div>
                                </div>
                            </div>
                        </td>
                        <td>
                            @php
                                $badgeClass = match($log->action) {
                                    'created' => 'bg-success-soft text-success',
                                    'updated' => 'bg-primary-soft text-primary',
                                    'deleted' => 'bg-danger-soft text-danger',
                                    'restored' => 'bg-info-soft text-info',
                                    default => 'bg-secondary-soft text-secondary'
                                };
                            @endphp
                            <span class="badge {{ $badgeClass }} border-0 text-uppercase rounded-pill px-3 py-2 fw-800 small">
                                <i class="bi bi-dot me-1"></i>{{ $log->action }}
                            </span>
                        </td>
                        <td>
                            <div class="fw-800 text-erp-deep small">{{ class_basename($log->model_type) }}</div>
                            <div class="text-muted x-small fw-bold">UID: #{{ $log->model_id }}</div>
                        </td>
                        <td>
                            @if($log->action === 'updated' && $log->changes)
                                <button class="btn btn-white btn-sm rounded-pill px-3 shadow-sm border-0 fw-700 small" 
                                        type="button" data-bs-toggle="collapse" data-bs-target="#changes-{{ $log->id }}">
                                    <i class="bi bi-eye-fill me-1"></i>Inspect Delta
                                </button>
                                <div class="collapse mt-2" id="changes-{{ $log->id }}">
                                    <div class="p-3 bg-light-soft rounded-4 border-0 small shadow-inner">
                                        @foreach($log->changes['after'] ?? [] as $key => $value)
                                            @if($key !== 'updated_at')
                                                <div class="mb-2 pb-1 border-bottom border-white opacity-75">
                                                    <div class="fw-800 text-erp-deep text-uppercase x-small mb-1">{{ str_replace('_', ' ', $key) }}</div>
                                                    <div class="d-flex align-items-center gap-2">
                                                        <span class="text-muted text-decoration-line-through">{{ $log->changes['before'][$key] ?? 'void' }}</span>
                                                        <i class="bi bi-arrow-right text-primary"></i>
                                                        <span class="text-erp-deep fw-800">{{ $value }}</span>
                                                    </div>
                                                </div>
                                            @endif
                                        @endforeach
                                    </div>
                                </div>
                            @elseif($log->action === 'created')
                                <span class="text-success small fw-700"><i class="bi bi-plus-circle me-1"></i>Resource Initiation</span>
                            @elseif($log->action === 'deleted')
                                <span class="text-danger small fw-700"><i class="bi bi-dash-circle me-1"></i>Resource Expungement</span>
                            @else
                                <span class="text-muted italic small fw-600">No delta recorded</span>
                            @endif
                        </td>
                        <td class="pe-4 text-end">
                            <div class="text-muted x-small fw-bold">{{ $log->ip_address }}</div>
                            <div class="text-muted x-small text-truncate d-inline-block" style="max-width: 120px;" title="{{ $log->user_agent }}">
                                {{ $log->user_agent }}
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center py-5">
                            <i class="bi bi-clipboard-x fs-1 text-muted opacity-25"></i>
                            <div class="text-muted italic mt-3">The system audit trail is currently unpopulated.</div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($logs->hasPages())
        <div class="card-footer border-0 p-4">
            {{ $logs->links() }}
        </div>
    @endif
</div>
@endsection

