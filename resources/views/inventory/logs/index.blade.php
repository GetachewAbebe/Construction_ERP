@extends('layouts.app')
@section('title', 'Inventory Audit Trail')

@section('content')
<div class="container py-4">
    {{-- HEADER --}}
    <div class="row mb-4">
        <div class="col d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
            <div>
                <h1 class="h4 mb-1 text-erp-deep">Inventory Audit Trail</h1>
                <p class="text-muted small mb-0">
                    Track every quantity change, who made it, and the reason for the adjustment.
                </p>
            </div>
            <a href="{{ route('inventory.dashboard') }}" class="btn btn-sm btn-outline-secondary">
                Back to Dashboard
            </a>
        </div>
    </div>

    {{-- LOGS TABLE --}}
    <div class="row">
        <div class="col">
            <div class="card shadow-soft border-0">
                <div class="card-body">
                    
                    <div class="table-responsive">
                        <table class="table table-sm align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th scope="col" style="width: 15%">Date & Time</th>
                                    <th scope="col">Item</th>
                                    <th scope="col">User</th>
                                    <th scope="col" class="text-center">Change</th>
                                    <th scope="col" class="text-center">Result</th>
                                    <th scope="col">Reason / Remarks</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($logs as $log)
                                    <tr>
                                        <td class="small text-muted">
                                            {{ $log->created_at->format('Y-m-d H:i:s') }}
                                        </td>
                                        <td>
                                            <div class="fw-semibold">
                                                {{ $log->item->name ?? 'Deleted Item' }}
                                            </div>
                                            @if($log->item)
                                                <div class="small text-muted">{{ $log->item->item_no }}</div>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="small fw-medium">{{ $log->user->name ?? 'System' }}</div>
                                            <div class="text-muted" style="font-size: 0.75rem;">{{ $log->user->email ?? '' }}</div>
                                        </td>
                                        <td class="text-center">
                                            @if($log->change_amount > 0)
                                                <span class="badge bg-success-subtle text-success fw-bold">
                                                    +{{ $log->change_amount }}
                                                </span>
                                            @elseif($log->change_amount < 0)
                                                <span class="badge bg-danger-subtle text-danger fw-bold">
                                                    {{ $log->change_amount }}
                                                </span>
                                            @else
                                                <span class="badge bg-light text-muted">0</span>
                                            @endif
                                        </td>
                                        <td class="text-center">
                                            <div class="small text-muted" style="text-decoration: line-through;">{{ $log->previous_quantity }}</div>
                                            <div class="fw-bold text-erp-deep">{{ $log->new_quantity }}</div>
                                        </td>
                                        <td>
                                            <div class="badge bg-info-subtle text-erp-deep small mb-1">
                                                {{ ucfirst($log->reason) }}
                                            </div>
                                            @if($log->remarks)
                                                <div class="small text-muted">{{ $log->remarks }}</div>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center text-muted py-5">
                                            No logs found in the audit trail.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    {{-- PAGINATION --}}
                    <div class="mt-4 d-flex justify-content-end">
                        {{ $logs->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
