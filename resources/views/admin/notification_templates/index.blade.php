@extends('layouts.app')
@section('title', 'Communication Template Registry')

@section('content')
<div class="row align-items-center mb-4 stagger-entrance">
    <div class="col">
        <h1 class="h3 mb-1 fw-800 text-erp-deep">Communication Template Registry</h1>
        <p class="text-muted mb-0">Manage system-wide email and notification templates with dynamic variable support.</p>
    </div>
    <div class="col-auto">
        <a href="{{ route('admin.notification-templates.create') }}" class="btn btn-erp-deep rounded-pill px-4 shadow-sm border-0">
            <i class="bi bi-plus-circle me-2"></i>Register Template
        </a>
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
                    <th class="ps-4 py-3">Template Identity</th>
                    <th class="py-3">Communication Type</th>
                    <th class="py-3">Subject Line</th>
                    <th class="py-3">Available Variables</th>
                    <th class="py-3">Status</th>
                    <th class="pe-4 py-3 text-end">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($templates as $template)
                    <tr>
                        <td class="ps-4">
                            <div class="fw-800 text-erp-deep">{{ $template->name }}</div>
                            <small class="text-muted fw-bold font-monospace">{{ $template->key }}</small>
                        </td>
                        <td>
                            @php
                                $typeClass = match($template->type) {
                                    'email' => 'bg-primary-soft text-primary',
                                    'notification' => 'bg-success-soft text-success',
                                    'sms' => 'bg-warning-soft text-warning',
                                    default => 'bg-secondary-soft text-secondary'
                                };
                                $typeIcon = match($template->type) {
                                    'email' => 'envelope-fill',
                                    'notification' => 'bell-fill',
                                    'sms' => 'phone-fill',
                                    default => 'chat-fill'
                                };
                            @endphp
                            <span class="badge {{ $typeClass }} rounded-pill px-3 py-2 border-0 fw-800 small">
                                <i class="bi bi-{{ $typeIcon }} me-1"></i>{{ ucfirst($template->type) }}
                            </span>
                        </td>
                        <td>
                            <span class="text-dark fw-600 small">{{ $template->subject ?? 'N/A' }}</span>
                        </td>
                        <td>
                            @if(!empty($template->variables))
                                <div class="d-flex flex-wrap gap-1">
                                    @foreach(array_slice($template->variables, 0, 3) as $variable)
                                        <code class="badge bg-light text-dark border px-2 py-1 font-monospace x-small">{{ '{' . $variable . '}' }}</code>
                                    @endforeach
                                    @if(count($template->variables) > 3)
                                        <span class="badge bg-secondary-soft text-secondary px-2 py-1 x-small">+{{ count($template->variables) - 3 }}</span>
                                    @endif
                                </div>
                            @else
                                <span class="text-muted italic small">No variables</span>
                            @endif
                        </td>
                        <td>
                            @if($template->is_active)
                                <span class="badge bg-success-soft text-success rounded-pill px-3 py-2 border-0 fw-700 small">
                                    <i class="bi bi-check-circle-fill me-1"></i>Active
                                </span>
                            @else
                                <span class="badge bg-secondary-soft text-secondary rounded-pill px-3 py-2 border-0 fw-700 small">
                                    <i class="bi bi-pause-circle-fill me-1"></i>Inactive
                                </span>
                            @endif
                        </td>
                        <td class="pe-4 text-end">
                            <div class="btn-group shadow-sm rounded-pill overflow-hidden">
                                <a href="{{ route('admin.notification-templates.preview', $template) }}" 
                                   class="btn btn-white btn-sm px-3" title="Preview Template">
                                    <i class="bi bi-eye-fill"></i>
                                </a>
                                <a href="{{ route('admin.notification-templates.edit', $template) }}" 
                                   class="btn btn-white btn-sm px-3" title="Modify Template">
                                    <i class="bi bi-pencil-square"></i>
                                </a>
                                <form action="{{ route('admin.notification-templates.destroy', $template) }}" 
                                      method="POST" class="d-inline"
                                      onsubmit="return confirm('Authorize deletion of this template?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-white btn-sm px-3 text-danger" title="Expunge Template">
                                        <i class="bi bi-trash3-fill"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center py-5">
                            <i class="bi bi-envelope-x fs-1 text-muted opacity-25"></i>
                            <div class="text-muted italic mt-3">No communication templates have been registered in the system.</div>
                            <a href="{{ route('admin.notification-templates.create') }}" class="btn btn-erp-deep rounded-pill px-4 mt-3">
                                <i class="bi bi-plus-circle me-2"></i>Create First Template
                            </a>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<div class="row mt-4">
    <div class="col-12 stagger-entrance">
        <div class="card hardened-glass border-0 overflow-hidden shadow-sm">
            <div class="card-body p-4">
                <div class="d-flex align-items-start gap-3">
                    <i class="bi bi-info-circle-fill text-primary fs-4"></i>
                    <div>
                        <h6 class="fw-800 text-erp-deep mb-2">Template Variable System</h6>
                        <p class="text-muted mb-0 small fw-600">
                            Templates support dynamic variables using curly brace notation (e.g., <code>{user_name}</code>, <code>{date}</code>). 
                            Define available variables when creating templates, and the system will replace them with actual values during message generation. 
                            Common variables include: <code>{user_name}</code>, <code>{company_name}</code>, <code>{date}</code>, <code>{time}</code>, <code>{link}</code>.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
