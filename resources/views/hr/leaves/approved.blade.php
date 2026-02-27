@extends('layouts.app')
@section('title', 'Authorized Absence Logs')

@section('content')
<div class="row align-items-center mb-4 stagger-entrance">
    <div class="col">
        <h1 class="h3 mb-1 fw-800 text-erp-deep">Authorized Absence Logs</h1>
        <p class="text-muted mb-0">Historical record of approved leave transactions and administrative authorizations.</p>
    </div>
    <div class="col-auto">
        <a href="{{ route('hr.leaves.index') }}" class="btn btn-white rounded-pill px-4 shadow-sm border-0">
            <i class="bi bi-arrow-left me-2"></i>Active Portfolio
        </a>
    </div>
</div>

<div class="card hardened-glass border-0 overflow-hidden stagger-entrance shadow-sm">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="bg-light-soft text-erp-deep border-0">
                <tr>
                    <th class="ps-4 py-3">Personnel Identity</th>
                    <th class="py-3">Absence Span</th>
                    <th class="py-3">Validator</th>
                    <th class="py-3">Validation Date</th>
                    <th class="text-end pe-4 py-3">Outcome Status</th>
                </tr>
            </thead>
            <tbody class="border-0">
                @forelse($approved as $row)
                    <tr>
                        <td class="ps-4">
                            <div class="fw-800 text-erp-deep">{{ $row->employee->name ?? 'Legacy Identity' }}</div>
                            <small class="text-muted fw-bold">ID: #LV-{{ $row->id }}</small>
                        </td>
                        <td>
                            <div class="d-flex flex-column font-monospace">
                                <span class="fw-700 text-dark">
                                    {{ \Carbon\Carbon::parse($row->start_date)->format('Y-m-d') }}
                                </span>
                                <span class="text-muted small">to {{ \Carbon\Carbon::parse($row->end_date)->format('Y-m-d') }}</span>
                            </div>
                        </td>
                        <td>
                            <div class="d-flex align-items-center gap-2">
                                <div class="avatar-xs bg-light rounded-circle p-1" style="width: 24px; height: 24px;">
                                    <i class="bi bi-person-check text-success"></i>
                                </div>
                                <span class="fw-700 text-dark small">{{ $row->approver->name ?? 'System Process' }}</span>
                            </div>
                        </td>
                        <td>
                            <div class="small fw-700 text-muted">
                                {{ $row->approved_at ? $row->approved_at->format('M d, Y') : 'N/A' }}
                                <div class="x-small fw-normal">at {{ $row->approved_at ? $row->approved_at->format('H:i') : '—' }}</div>
                            </div>
                        </td>
                        <td class="text-end pe-4">
                            <span class="badge bg-success-soft text-success rounded-pill px-3 py-2 border-0 fw-800">
                                <i class="bi bi-shield-check me-1"></i>VERIFIED
                            </span>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="text-center py-5">
                            <i class="bi bi-archive fs-1 text-muted opacity-25"></i>
                            <div class="text-muted italic mt-3">The authorized absence archive is currently vacant.</div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($approved->hasPages())
        <div class="card-footer border-0 p-4">
            {{ $approved->links() }}
        </div>
    @endif
</div>
@endsection

