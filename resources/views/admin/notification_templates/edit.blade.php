@extends('layouts.app')
@section('title', 'Edit Template')

@section('content')
<div class="row align-items-center mb-4 stagger-entrance">
    <div class="col">
        <h1 class="h3 mb-1 fw-800 text-erp-deep">Edit Template</h1>
        <p class="text-muted mb-0">Edit template details and message for: <span class="text-erp-deep fw-800">{{ $notificationTemplate->name }}</span></p>
    </div>
    <div class="col-auto">
        <a href="{{ route('admin.notification-templates.index') }}" class="btn btn-white rounded-pill px-4 shadow-sm border-0">
            <i class="bi bi-arrow-left me-2"></i>Back to Templates
        </a>
    </div>
</div>

<div class="stagger-entrance">
    <div class="card border-0 bg-white shadow-sm">
        <div class="card-body p-4 p-md-5">
                <form action="{{ route('admin.notification-templates.update', $notificationTemplate) }}" method="POST">
                    @csrf
                    @method('PUT')
                    
                    <div class="mb-5">
                        <h5 class="fw-800 text-erp-deep mb-4 d-flex align-items-center gap-2">
                            <i class="bi bi-tag-fill text-primary"></i>
                            Template Details
                        </h5>
                        
                        <div class="row g-4">
                            <div class="col-md-6">
                                <label class="form-label small fw-800 text-muted text-uppercase">Template Key</label>
                                <input type="text" name="key" 
                                       class="form-control border-0 bg-light-soft rounded-4 py-3 px-4 shadow-sm font-monospace @error('key') is-invalid @enderror" 
                                       value="{{ old('key', $notificationTemplate->key) }}" 
                                       required>
                                <small class="text-muted fw-bold mt-2 d-block">Unique key.</small>
                                @error('key') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label small fw-800 text-muted text-uppercase">Template Name</label>
                                <input type="text" name="name" 
                                       class="form-control border-0 bg-light-soft rounded-4 py-3 px-4 shadow-sm @error('name') is-invalid @enderror" 
                                       value="{{ old('name', $notificationTemplate->name) }}" 
                                       required>
                                <small class="text-muted fw-bold mt-2 d-block">Name for this template.</small>
                                @error('name') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label small fw-800 text-muted text-uppercase">Type</label>
                                <select name="type" 
                                        class="form-select border-0 bg-light-soft rounded-4 py-3 px-4 shadow-sm @error('type') is-invalid @enderror" 
                                        required>
                                    <option value="email" @selected(old('type', $notificationTemplate->type) == 'email')>Email</option>
                                    <option value="notification" @selected(old('type', $notificationTemplate->type) == 'notification')>Notification</option>
                                    <option value="sms" @selected(old('type', $notificationTemplate->type) == 'sms')>SMS</option>
                                </select>
                                <small class="text-muted fw-bold mt-2 d-block">Delivery method.</small>
                                @error('type') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label small fw-800 text-muted text-uppercase">Active Status</label>
                                <div class="form-check form-switch mt-3">
                                    <input class="form-check-input" type="checkbox" name="is_active" id="is_active" 
                                           @checked(old('is_active', $notificationTemplate->is_active))>
                                    <label class="form-check-label fw-700 text-dark" for="is_active">
                                        Template is Active
                                    </label>
                                </div>
                                <small class="text-muted fw-bold mt-2 d-block">Inactive templates won't be used.</small>
                            </div>
                        </div>
                    </div>

                    <div class="mb-5">
                        <h5 class="fw-800 text-erp-deep mb-4 d-flex align-items-center gap-2">
                            <i class="bi bi-file-text-fill text-success"></i>
                            Content
                        </h5>
                        
                        <div class="row g-4">
                            <div class="col-12">
                                <label class="form-label small fw-800 text-muted text-uppercase">Subject (Email Only)</label>
                                <input type="text" name="subject" 
                                       class="form-control border-0 bg-light-soft rounded-4 py-3 px-4 shadow-sm @error('subject') is-invalid @enderror" 
                                       value="{{ old('subject', $notificationTemplate->subject) }}">
                                <small class="text-muted fw-bold mt-2 d-block">Subject line for emails.</small>
                                @error('subject') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                            </div>

                            <div class="col-12">
                                <label class="form-label small fw-800 text-muted text-uppercase">Body Content</label>
                                <textarea name="body" rows="8" 
                                          class="form-control border-0 bg-light-soft rounded-4 py-3 px-4 shadow-sm font-monospace @error('body') is-invalid @enderror" 
                                          required>{{ old('body', $notificationTemplate->body) }}</textarea>
                                <small class="text-muted fw-bold mt-2 d-block">
                                    Available Variables: {user_name}, {action_url}, {app_name}
                                </small>
                                @error('body') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                            </div>

                            <div class="col-12">
                                <label class="form-label small fw-800 text-muted text-uppercase">Available Variables</label>
                                <input type="text" name="variables" 
                                       class="form-control border-0 bg-light-soft rounded-4 py-3 px-4 shadow-sm font-monospace @error('variables') is-invalid @enderror" 
                                       value="{{ old('variables', is_array($notificationTemplate->variables) ? implode(', ', $notificationTemplate->variables) : $notificationTemplate->variables) }}" 
                                       placeholder="e.g. {user_name}, {order_id}">
                                <small class="text-muted fw-bold mt-2 d-block">List variables used in this template (comma separated).</small>
                                @error('variables') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                            </div>
                        </div>
                    </div>

                    <div class="d-flex gap-3 justify-content-end pt-4 border-top border-light">
                        <a href="{{ route('admin.notification-templates.index') }}" class="btn btn-white rounded-pill px-4 py-3 fw-700 shadow-sm border-0">
                            <i class="bi bi-x-circle me-2"></i>Cancel
                        </a>
                        <button type="submit" class="btn btn-erp-deep rounded-pill px-5 py-3 fw-800 shadow-lg border-0">
                            <i class="bi bi-check2-circle me-2"></i>Save Changes
                        </button>
                    </div>
                </form>
        </div>
    </div>
</div>
@endsection
