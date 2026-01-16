@extends('layouts.app')
@section('title', 'Resource Restoration Vault')

@section('content')
<div class="row align-items-center mb-4 stagger-entrance">
    <div class="col">
        <h1 class="h3 mb-1 fw-800 text-erp-deep">Resource Restoration Vault</h1>
        <p class="text-muted mb-0">Secure environment for the adjudication and recovery of previously expunged data assets.</p>
    </div>
</div>

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show hardened-glass border-0 shadow-sm stagger-entrance" role="alert">
        <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

@if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show hardened-glass border-0 shadow-sm stagger-entrance" role="alert">
        <i class="bi bi-exclamation-triangle-fill me-2"></i>{{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

<div class="card hardened-glass border-0 overflow-hidden stagger-entrance shadow-sm">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="bg-light-soft text-erp-deep">
                <tr>
                    <th class="ps-4 py-3">Asset Classification</th>
                    <th class="py-3">Resource Identity</th>
                    <th class="py-3">Expungement Timestamp</th>
                    <th class="pe-4 py-3 text-end">Restoration Protocol</th>
                </tr>
            </thead>
            <tbody>
                @forelse($trashedItems as $item)
                    <tr>
                        <td class="ps-4">
                            <span class="badge bg-secondary-soft text-secondary text-uppercase border-0 px-3 py-2 fw-800 small rounded-pill">
                                <i class="bi bi-layers-half me-1"></i>{{ str_replace('_', ' ', $item['type']) }}
                            </span>
                        </td>
                        <td>
                            <div class="fw-800 text-erp-deep">{{ $item['name'] }}</div>
                            <small class="text-muted fw-bold">System UID: #{{ $item['id'] }}</small>
                        </td>
                        <td>
                            <div class="d-flex flex-column font-monospace">
                                <span class="fw-700 text-dark">
                                    {{ $item['deleted_at']->format('M d, Y') }}
                                </span>
                                <span class="text-muted small">at {{ $item['deleted_at']->format('H:i') }}</span>
                            </div>
                        </td>
                        <td class="pe-4 text-end">
                            <form action="{{ route('admin.trash.restore') }}" method="POST" class="d-inline">
                                @csrf
                                <input type="hidden" name="model" value="{{ $item['model'] }}">
                                <input type="hidden" name="id" value="{{ $item['id'] }}">
                                <button type="submit" class="btn btn-erp-deep btn-sm rounded-pill px-4 shadow-sm border-0 fw-800">
                                    <i class="bi bi-arrow-counterclockwise me-1"></i>Initiate Recovery
                                </button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="text-center py-5">
                            <i class="bi bi-trash3 fs-1 text-muted opacity-25"></i>
                            <div class="text-muted italic mt-3">The restoration vault is currently vacant. No expunged assets found.</div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection

