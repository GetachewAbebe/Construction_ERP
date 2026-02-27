@extends('layouts.app')
@section('title', 'Inventory Audit Trail | Natanem Engineering')

@section('content')
<div class="row align-items-center mb-4 stagger-entrance">
    <div class="col">
        <h1 class="h3 mb-1 fw-800 text-erp-deep">Inventory Audit Trail</h1>
        <p class="text-muted mb-0">Complete historical record of stock movements and adjustments.</p>
    </div>
    <div class="col-auto">
        <a href="{{ route('inventory.items.index') }}" class="btn btn-white rounded-pill px-4 shadow-sm border-0">
            <i class="bi bi-arrow-left me-2"></i>Inventory Overview
        </a>
    </div>
</div>

<div class="card hardened-glass border-0 overflow-hidden shadow-lg stagger-entrance">
    <div class="card-body p-0">
        <div class="p-4 bg-light-soft border-bottom d-flex align-items-center justify-content-between">
            <div class="d-flex align-items-center gap-3">
                <div class="avatar-sm bg-white shadow-sm rounded-circle text-primary d-flex align-items-center justify-content-center">
                    <i class="bi bi-clock-history fs-5"></i>
                </div>
                <div>
                    <h6 class="fw-800 text-erp-deep mb-0">Transaction Log</h6>
                    <small class="text-muted">Showing latest stock activities</small>
                </div>
            </div>
            <div class="badge bg-white text-muted border rounded-pill px-3 py-2">
                Total Records: {{ $logs->total() }}
            </div>
        </div>

        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light-soft text-erp-deep">
                    <tr>
                        <th class="ps-4">Timestamp</th>
                        <th>Item Details</th>
                        <th>Performed By</th>
                        <th class="text-center">Adjustment</th>
                        <th class="text-center">Balance</th>
                        <th class="pe-4">Context</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($logs as $log)
                        <tr>
                            <td class="ps-4">
                                <div class="fw-bold text-dark">{{ $log->created_at->format('M d, Y') }}</div>
                                <div class="small text-muted">{{ $log->created_at->format('H:i:s') }}</div>
                            </td>
                            <td>
                                <div class="fw-700 text-erp-deep">{{ optional($log->item)->name ?? 'Archived Item' }}</div>
                                <div class="x-small text-muted fw-bold font-monospace">{{ optional($log->item)->item_no ?? '---' }}</div>
                            </td>
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    <div class="avatar-xs bg-light text-muted rounded-circle d-flex align-items-center justify-content-center" style="width: 24px; height: 24px; font-size: 10px;">
                                        {{ substr(optional($log->user)->name ?? 'S', 0, 1) }}
                                    </div>
                                    <span class="small fw-600 text-dark">{{ optional($log->user)->name ?? 'System Process' }}</span>
                                </div>
                            </td>
                            <td class="text-center">
                                @if($log->change_amount > 0)
                                    <span class="badge bg-success-soft text-success rounded-pill px-3 py-1 border-0 fw-bold">
                                        +{{ $log->change_amount }}
                                    </span>
                                @elseif($log->change_amount < 0)
                                    <span class="badge bg-danger-soft text-danger rounded-pill px-3 py-1 border-0 fw-bold">
                                        {{ $log->change_amount }}
                                    </span>
                                @else
                                    <span class="badge bg-light text-muted rounded-pill px-3 py-1 border fw-bold">0</span>
                                @endif
                            </td>
                            <td class="text-center">
                                <div class="d-flex flex-column align-items-center">
                                    <span class="fw-800 text-dark">{{ $log->new_quantity }}</span>
                                    <span class="x-small text-muted" style="text-decoration: line-through;">{{ $log->previous_quantity }}</span>
                                </div>
                            </td>
                            <td class="pe-4">
                                <div class="badge bg-light border text-dark mb-1">{{ ucfirst(str_replace('_', ' ', $log->reason)) }}</div>
                                @if($log->remarks)
                                    <div class="small text-muted text-truncate" style="max-width: 250px;" title="{{ $log->remarks }}">
                                        {{ $log->remarks }}
                                    </div>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-5">
                                <i class="bi bi-clock-history fs-1 text-muted opacity-25"></i>
                                <div class="text-muted italic mt-3">No activity logs recorded.</div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($logs->hasPages())
            <div class="card-footer border-0 bg-white p-4">
                {{ $logs->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
