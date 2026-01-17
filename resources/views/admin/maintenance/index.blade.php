@extends('layouts.app')
@section('title', 'System Maintenance & Optimization')

@section('content')
<div class="row align-items-center mb-4 stagger-entrance">
    <div class="col">
        <h1 class="h3 mb-1 fw-800 text-erp-deep">System Maintenance & Optimization</h1>
        <p class="text-muted mb-0">Comprehensive utilities for system health monitoring, cache management, and data backup operations.</p>
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

{{-- Migration Sync --}}
<div class="card hardened-glass border-0 overflow-hidden shadow-sm mb-4 stagger-entrance">
    <div class="card-body p-4">
        <div class="d-flex align-items-center justify-content-between flex-wrap gap-3">
            <div class="d-flex align-items-center gap-3">
                <div class="feature-icon-small bg-primary-subtle text-primary rounded-circle p-3">
                    <i class="bi bi-database-fill-up fs-4"></i>
                </div>
                <div>
                    <h5 class="fw-800 text-erp-deep mb-1">Database Schema Synchronization</h5>
                    <p class="text-muted small mb-0 fw-600">
                        @if($pendingMigrations > 0)
                            <span class="text-danger"><i class="bi bi-exclamation-circle-fill me-1"></i>{{ $pendingMigrations }} pending database schema updates detected.</span>
                        @else
                            <span class="text-success"><i class="bi bi-check-circle-fill me-1"></i>Database schema is currently synchronized with the application codebase.</span>
                        @endif
                    </p>
                </div>
            </div>
            <form action="{{ route('admin.maintenance.migrate') }}" method="POST" onsubmit="return confirm('WARNING: This will execute pending database migrations. Database state will be modified. Continue?');">
                @csrf
                <button type="submit" class="btn btn-primary rounded-pill px-4 py-2 fw-800 shadow-sm border-0 d-flex align-items-center gap-2">
                    <i class="bi bi-gear-fill"></i>
                    Sync Database Schema
                </button>
            </form>
        </div>
    </div>
</div>

{{-- System Information --}}
<div class="row g-4 mb-4">
    <div class="col-lg-6 stagger-entrance">
        <div class="card hardened-glass border-0 overflow-hidden shadow-sm h-100">
            <div class="card-header bg-light-soft border-0 p-4">
                <h5 class="fw-800 text-erp-deep mb-0 d-flex align-items-center gap-2">
                    <i class="bi bi-cpu-fill text-primary"></i>
                    System Configuration Matrix
                </h5>
            </div>
            <div class="card-body p-4">
                <div class="row g-3">
                    @foreach($systemInfo as $key => $value)
                        <div class="col-md-6">
                            <div class="small fw-800 text-muted text-uppercase mb-1">{{ str_replace('_', ' ', $key) }}</div>
                            <div class="fw-700 text-dark">{{ $value }}</div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-6 stagger-entrance">
        <div class="card hardened-glass border-0 overflow-hidden shadow-sm h-100">
            <div class="card-header bg-light-soft border-0 p-4">
                <h5 class="fw-800 text-erp-deep mb-0 d-flex align-items-center gap-2">
                    <i class="bi bi-hdd-fill text-success"></i>
                    Storage Utilization Metrics
                </h5>
            </div>
            <div class="card-body p-4">
                <div class="row g-3">
                    @foreach($storageInfo as $key => $value)
                        <div class="col-md-6">
                            <div class="small fw-800 text-muted text-uppercase mb-1">{{ str_replace('_', ' ', $key) }}</div>
                            <div class="fw-700 text-dark">{{ $value }}</div>
                        </div>
                    @endforeach
                    <div class="col-md-6">
                        <div class="small fw-800 text-muted text-uppercase mb-1">Cache Driver</div>
                        <div class="fw-700 text-dark">{{ $cacheInfo['driver'] }}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Maintenance Actions --}}
<div class="row g-4 mb-4">
    <div class="col-lg-4 stagger-entrance">
        <div class="card hardened-glass border-0 overflow-hidden shadow-sm h-100">
            <div class="card-body p-4 text-center">
                <div class="mb-3">
                    <i class="bi bi-trash3-fill text-warning" style="font-size: 3rem;"></i>
                </div>
                <h5 class="fw-800 text-erp-deep mb-2">Cache Purge Protocol</h5>
                <p class="text-muted small fw-600 mb-4">Clear all cached configurations, routes, and views from system memory.</p>
                <form action="{{ route('admin.maintenance.clear-cache') }}" method="POST" onsubmit="return confirm('Execute cache purge operation?');">
                    @csrf
                    <button type="submit" class="btn btn-warning rounded-pill px-4 py-2 fw-800 shadow-sm border-0">
                        <i class="bi bi-trash3-fill me-2"></i>Purge Cache
                    </button>
                </form>
            </div>
        </div>
    </div>

    <div class="col-lg-4 stagger-entrance">
        <div class="card hardened-glass border-0 overflow-hidden shadow-sm h-100">
            <div class="card-body p-4 text-center">
                <div class="mb-3">
                    <i class="bi bi-speedometer2 text-success" style="font-size: 3rem;"></i>
                </div>
                <h5 class="fw-800 text-erp-deep mb-2">Performance Optimization</h5>
                <p class="text-muted small fw-600 mb-4">Rebuild caches and optimize application for maximum performance.</p>
                <form action="{{ route('admin.maintenance.optimize') }}" method="POST" onsubmit="return confirm('Execute optimization protocols?');">
                    @csrf
                    <button type="submit" class="btn btn-success rounded-pill px-4 py-2 fw-800 shadow-sm border-0">
                        <i class="bi bi-speedometer2 me-2"></i>Optimize System
                    </button>
                </form>
            </div>
        </div>
    </div>

    <div class="col-lg-4 stagger-entrance">
        <div class="card hardened-glass border-0 overflow-hidden shadow-sm h-100">
            <div class="card-body p-4 text-center">
                <div class="mb-3">
                    <i class="bi bi-file-earmark-x-fill text-danger" style="font-size: 3rem;"></i>
                </div>
                <h5 class="fw-800 text-erp-deep mb-2">Log Archive Expungement</h5>
                <p class="text-muted small fw-600 mb-4">Remove all historical log files from storage to free up disk space.</p>
                <form action="{{ route('admin.maintenance.clear-logs') }}" method="POST" onsubmit="return confirm('WARNING: This will permanently delete all log files. Continue?');">
                    @csrf
                    <button type="submit" class="btn btn-danger rounded-pill px-4 py-2 fw-800 shadow-sm border-0">
                        <i class="bi bi-file-earmark-x-fill me-2"></i>Clear Logs
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

{{-- Database Backup --}}
<div class="row g-4 mb-4">
    <div class="col-12 stagger-entrance">
        <div class="card hardened-glass border-0 overflow-hidden shadow-sm">
            <div class="card-header bg-light-soft border-0 p-4 d-flex align-items-center justify-content-between">
                <div>
                    <h5 class="fw-800 text-erp-deep mb-1 d-flex align-items-center gap-2">
                        <i class="bi bi-database-fill-check text-primary"></i>
                        Database Backup Repository
                    </h5>
                    <p class="text-muted small mb-0 fw-600">Create and manage database backup archives for disaster recovery.</p>
                </div>
                <form action="{{ route('admin.maintenance.create-backup') }}" method="POST" onsubmit="return confirm('Create new database backup?');">
                    @csrf
                    <button type="submit" class="btn btn-erp-deep rounded-pill px-4 py-2 fw-800 shadow-sm border-0">
                        <i class="bi bi-plus-circle me-2"></i>Create Backup
                    </button>
                </form>
            </div>
            <div class="card-body p-0">
                <div id="backupList" class="p-4">
                    <div class="text-center py-4">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                        <p class="text-muted mt-2 small fw-600">Loading backup archives...</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Recent Log Files --}}
<div class="row g-4">
    <div class="col-12 stagger-entrance">
        <div class="card hardened-glass border-0 overflow-hidden shadow-sm">
            <div class="card-header bg-light-soft border-0 p-4">
                <h5 class="fw-800 text-erp-deep mb-0 d-flex align-items-center gap-2">
                    <i class="bi bi-file-earmark-text-fill text-info"></i>
                    Recent System Log Archives
                </h5>
            </div>
            <div class="card-body p-0">
                @if(count($logFiles) > 0)
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="bg-light-soft">
                                <tr>
                                    <th class="ps-4 py-3">Log File</th>
                                    <th class="py-3">File Size</th>
                                    <th class="pe-4 py-3">Last Modified</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($logFiles as $log)
                                    <tr>
                                        <td class="ps-4">
                                            <code class="badge bg-light text-dark border px-3 py-2 font-monospace">{{ $log['name'] }}</code>
                                        </td>
                                        <td>
                                            <span class="fw-700 text-dark">{{ $log['size'] }}</span>
                                        </td>
                                        <td class="pe-4">
                                            <span class="text-muted fw-600">{{ $log['modified'] }}</span>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="p-4 text-center">
                        <i class="bi bi-file-earmark-x fs-1 text-muted opacity-25"></i>
                        <p class="text-muted italic mt-2 mb-0">No log files found in the system.</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<script>
// Load backup list
document.addEventListener('DOMContentLoaded', function() {
    loadBackups();
});

function loadBackups() {
    fetch('{{ route("admin.maintenance.list-backups") }}')
        .then(response => response.json())
        .then(data => {
            const container = document.getElementById('backupList');
            
            if (data.length === 0) {
                container.innerHTML = `
                    <div class="text-center py-4">
                        <i class="bi bi-database-x fs-1 text-muted opacity-25"></i>
                        <p class="text-muted italic mt-2 mb-0">No backup archives found in repository.</p>
                    </div>
                `;
                return;
            }

            let html = '<div class="table-responsive"><table class="table table-hover align-middle mb-0">';
            html += '<thead class="bg-light-soft"><tr>';
            html += '<th class="ps-4 py-3">Backup Archive</th>';
            html += '<th class="py-3">File Size</th>';
            html += '<th class="py-3">Created Date</th>';
            html += '<th class="pe-4 py-3 text-end">Actions</th>';
            html += '</tr></thead><tbody>';

            data.forEach(backup => {
                html += '<tr>';
                html += `<td class="ps-4"><code class="badge bg-light text-dark border px-3 py-2 font-monospace">${backup.name}</code></td>`;
                html += `<td><span class="fw-700 text-dark">${backup.size}</span></td>`;
                html += `<td><span class="text-muted fw-600">${backup.date}</span></td>`;
                html += `<td class="pe-4 text-end">
                    <a href="/admin/maintenance/backup/download/${backup.name}" class="btn btn-sm btn-white rounded-pill px-3 shadow-sm border-0">
                        <i class="bi bi-download me-1"></i>Download
                    </a>
                </td>`;
                html += '</tr>';
            });

            html += '</tbody></table></div>';
            container.innerHTML = html;
        })
        .catch(error => {
            console.error('Error loading backups:', error);
            document.getElementById('backupList').innerHTML = `
                <div class="alert alert-danger m-4">
                    <i class="bi bi-exclamation-triangle-fill me-2"></i>
                    Failed to load backup archives.
                </div>
            `;
        });
}
</script>
@endsection
