@extends('layouts.app')
@section('title', 'System Identities Control')

@section('content')
<div class="row align-items-center mb-4 stagger-entrance">
    <div class="col">
        <h1 class="h3 mb-1 fw-800 text-erp-deep">System Identities</h1>
        <p class="text-muted mb-0">Registry of administrative associates and operational personnel credentials.</p>
    </div>
    <div class="col-auto">
        <a href="{{ route('admin.users.create') }}" class="btn btn-erp-deep rounded-pill px-4 shadow-sm border-0">
            <i class="bi bi-person-plus-fill me-2"></i>Provision New Identity
        </a>
    </div>
</div>

{{-- Identity Search & Triage --}}
<div class="card hardened-glass border-0 mb-4 stagger-entrance shadow-sm">
    <div class="card-body p-4">
        <form action="{{ route('admin.users.index') }}" method="GET" class="row g-3 align-items-center">
            <div class="col-lg-8">
                <div class="input-group bg-light-soft rounded-pill overflow-hidden shadow-sm px-3 border-0">
                    <span class="input-group-text bg-transparent border-0 text-muted">
                        <i class="bi bi-search"></i>
                    </span>
                    <input type="text" name="q" value="{{ $q ?? '' }}" 
                           class="form-control border-0 bg-transparent py-3" 
                           placeholder="Scan records by identity name or digital signature (email)...">
                </div>
            </div>
            <div class="col-lg-4 text-lg-end">
                @if($q)
                    <a href="{{ route('admin.users.index') }}" class="btn btn-white rounded-pill px-4 me-2 border-0 shadow-sm">Reset</a>
                @endif
                <button type="submit" class="btn btn-erp-deep rounded-pill px-5 border-0 shadow-sm">Execute Search</button>
            </div>
        </form>
    </div>
</div>

{{-- Identity Grid --}}
<div class="card hardened-glass border-0 overflow-hidden stagger-entrance">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="bg-light-soft text-erp-deep">
                <tr>
                    <th class="ps-4">Personnel Cluster</th>
                    <th>Digital Signature</th>
                    <th>Authorization Role</th>
                    <th>Operational Status</th>
                    <th class="text-end pe-4">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($users as $user)
                    <tr>
                        <td class="ps-4">
                            <div class="d-flex align-items-center gap-3">
                                <div class="avatar-sm bg-erp-deep text-white rounded-pill d-flex align-items-center justify-content-center fw-800" style="width: 40px; height: 40px;">
                                    {{ strtoupper(substr($user->first_name, 0, 1) . substr($user->last_name, 0, 1)) }}
                                </div>
                                <div>
                                    <div class="fw-800 text-erp-deep">{{ $user->name }}</div>
                                    <small class="text-muted fw-bold">{{ $user->position ?? 'Unassigned Professional' }}</small>
                                </div>
                            </div>
                        </td>
                        <td>
                            <span class="text-muted fw-600">{{ $user->email }}</span>
                        </td>
                        <td>
                            @php
                                $roleClass = match($user->role) {
                                    'Administrator' => 'bg-danger-soft text-danger',
                                    'HumanResourceManager' => 'bg-primary-soft text-primary',
                                    'InventoryManager' => 'bg-success-soft text-success',
                                    'FinancialManager' => 'bg-warning-soft text-warning',
                                    default => 'bg-light text-muted'
                                };
                            @endphp
                            <span class="badge {{ $roleClass }} rounded-pill px-3 py-2 border-0 fw-800">
                                <i class="bi bi-shield-lock me-1"></i>{{ $user->role ?? 'Standard Access' }}
                            </span>
                        </td>
                        <td>
                            @php
                                $statusClass = match($user->status) {
                                    'Active' => 'text-success',
                                    'Inactive' => 'text-muted',
                                    'Suspended' => 'text-danger',
                                    default => 'text-info'
                                };
                            @endphp
                            <span class="d-flex align-items-center gap-2 fw-700 {{ $statusClass }}">
                                <i class="bi bi-circle-fill" style="font-size: 8px;"></i>
                                {{ $user->status ?? 'Active' }}
                            </span>
                        </td>
                        <td class="text-end pe-4">
                            <div class="btn-group shadow-sm rounded-pill overflow-hidden">
                                <a href="{{ route('admin.users.edit', $user) }}" 
                                   class="btn btn-white btn-sm px-3" title="Modify Identity">
                                    <i class="bi bi-pencil-square"></i>
                                </a>
                                @if($user->id !== auth()->id())
                                    <form action="{{ route('admin.users.destroy', $user) }}" 
                                          method="POST" class="d-inline"
                                          onsubmit="return confirm('Authorize deletion of this identity record?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-white btn-sm px-3 text-danger" title="Expunge Record">
                                            <i class="bi bi-trash3-fill"></i>
                                        </button>
                                    </form>
                                @else
                                    <button class="btn btn-white btn-sm px-3 text-muted disabled" title="Self Session Protected">
                                        <i class="bi bi-lock-fill"></i>
                                    </button>
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="text-center py-5">
                            <div class="text-muted italic">No identity records matched the current triaging parameters.</div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($users->hasPages())
        <div class="card-footer border-0 p-4">
            {{ $users->links() }}
        </div>
    @endif
</div>
@endsection

