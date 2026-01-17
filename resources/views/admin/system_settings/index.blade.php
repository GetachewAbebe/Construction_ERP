@extends('layouts.app')
@section('title', 'System Configuration Matrix')

@section('content')
<div class="row align-items-center mb-4 stagger-entrance">
    <div class="col">
        <h1 class="h3 mb-1 fw-800 text-erp-deep">System Configuration Matrix</h1>
        <p class="text-muted mb-0">Define core organizational parameters and operational defaults for enterprise-wide deployment.</p>
    </div>
    <div class="col-auto">
        <a href="{{ route('admin.dashboard') }}" class="btn btn-white rounded-pill px-4 shadow-sm border-0">
            <i class="bi bi-arrow-left me-2"></i>Return to Command
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

<div class="stagger-entrance">
    <div class="card border-0 bg-white shadow-sm">
        <div class="card-body p-4 p-md-5">
                <form action="{{ route('admin.system-settings.update') }}" method="POST">
                    @csrf
                    
                    <div class="mb-5">
                        <h5 class="fw-800 text-erp-deep mb-4 d-flex align-items-center gap-2">
                            <i class="bi bi-building text-primary"></i>
                            Organizational Identity
                        </h5>
                        
                        <div class="row g-4">
                            <div class="col-md-6">
                                <label class="form-label small fw-800 text-muted text-uppercase">Corporate Designation</label>
                                <input type="text" name="company_name" 
                                       class="form-control border-0 bg-light-soft rounded-4 py-3 px-4 shadow-sm @error('company_name') is-invalid @enderror" 
                                       value="{{ old('company_name', $settings['company_name']) }}" 
                                       required>
                                <small class="text-muted fw-bold mt-2 d-block">Official legal name of the organization.</small>
                                @error('company_name') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label small fw-800 text-muted text-uppercase">Primary Digital Contact</label>
                                <input type="email" name="company_email" 
                                       class="form-control border-0 bg-light-soft rounded-4 py-3 px-4 shadow-sm @error('company_email') is-invalid @enderror" 
                                       value="{{ old('company_email', $settings['company_email']) }}" 
                                       required>
                                <small class="text-muted fw-bold mt-2 d-block">Main email address for system communications.</small>
                                @error('company_email') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label small fw-800 text-muted text-uppercase">Telephonic Gateway</label>
                                <input type="text" name="company_phone" 
                                       class="form-control border-0 bg-light-soft rounded-4 py-3 px-4 shadow-sm @error('company_phone') is-invalid @enderror" 
                                       value="{{ old('company_phone', $settings['company_phone']) }}">
                                <small class="text-muted fw-bold mt-2 d-block">Primary contact number for organizational reach.</small>
                                @error('company_phone') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label small fw-800 text-muted text-uppercase">Physical Coordinates</label>
                                <input type="text" name="company_address" 
                                       class="form-control border-0 bg-light-soft rounded-4 py-3 px-4 shadow-sm @error('company_address') is-invalid @enderror" 
                                       value="{{ old('company_address', $settings['company_address']) }}">
                                <small class="text-muted fw-bold mt-2 d-block">Registered business address location.</small>
                                @error('company_address') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                            </div>
                        </div>
                    </div>

                    <div class="mb-5">
                        <h5 class="fw-800 text-erp-deep mb-4 d-flex align-items-center gap-2">
                            <i class="bi bi-gear-fill text-success"></i>
                            System Operational Defaults
                        </h5>
                        
                        <div class="row g-4">
                            <div class="col-md-6">
                                <label class="form-label small fw-800 text-muted text-uppercase">Temporal Zone Anchor</label>
                                <select name="timezone" 
                                        class="form-select border-0 bg-light-soft rounded-4 py-3 px-4 shadow-sm @error('timezone') is-invalid @enderror" 
                                        required>
                                    <option value="Africa/Addis_Ababa" @selected(old('timezone', $settings['timezone']) == 'Africa/Addis_Ababa')>Africa/Addis Ababa (EAT)</option>
                                    <option value="UTC" @selected(old('timezone', $settings['timezone']) == 'UTC')>UTC (Universal)</option>
                                    <option value="Africa/Nairobi" @selected(old('timezone', $settings['timezone']) == 'Africa/Nairobi')>Africa/Nairobi (EAT)</option>
                                </select>
                                <small class="text-muted fw-bold mt-2 d-block">System-wide timezone for all temporal operations.</small>
                                @error('timezone') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label small fw-800 text-muted text-uppercase">Date Rendering Format</label>
                                <select name="date_format" 
                                        class="form-select border-0 bg-light-soft rounded-4 py-3 px-4 shadow-sm @error('date_format') is-invalid @enderror" 
                                        required>
                                    <option value="Y-m-d" @selected(old('date_format', $settings['date_format']) == 'Y-m-d')>YYYY-MM-DD (2026-01-15)</option>
                                    <option value="d/m/Y" @selected(old('date_format', $settings['date_format']) == 'd/m/Y')>DD/MM/YYYY (15/01/2026)</option>
                                    <option value="m/d/Y" @selected(old('date_format', $settings['date_format']) == 'm/d/Y')>MM/DD/YYYY (01/15/2026)</option>
                                </select>
                                <small class="text-muted fw-bold mt-2 d-block">Standard date display format across all modules.</small>
                                @error('date_format') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label small fw-800 text-muted text-uppercase">Monetary Unit Designation</label>
                                <select name="currency" 
                                        class="form-select border-0 bg-light-soft rounded-4 py-3 px-4 shadow-sm @error('currency') is-invalid @enderror" 
                                        required>
                                    <option value="ETB" @selected(old('currency', $settings['currency']) == 'ETB')>ETB (Ethiopian Birr)</option>
                                    <option value="USD" @selected(old('currency', $settings['currency']) == 'USD')>USD (US Dollar)</option>
                                    <option value="EUR" @selected(old('currency', $settings['currency']) == 'EUR')>EUR (Euro)</option>
                                </select>
                                <small class="text-muted fw-bold mt-2 d-block">Default currency for financial transactions.</small>
                                @error('currency') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label small fw-800 text-muted text-uppercase">Pagination Threshold</label>
                                <input type="number" name="items_per_page" 
                                       class="form-control border-0 bg-light-soft rounded-4 py-3 px-4 shadow-sm @error('items_per_page') is-invalid @enderror" 
                                       value="{{ old('items_per_page', $settings['items_per_page']) }}" 
                                       min="5" max="100" 
                                       required>
                                <small class="text-muted fw-bold mt-2 d-block">Number of records displayed per page in listings.</small>
                                @error('items_per_page') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                            </div>
                        </div>
                    </div>

                    <div class="d-flex gap-3 justify-content-end pt-4 border-top border-light">
                        <a href="{{ route('admin.dashboard') }}" class="btn btn-white rounded-pill px-4 py-3 fw-700 shadow-sm border-0">
                            <i class="bi bi-x-circle me-2"></i>Discard Changes
                        </a>
                        <button type="submit" class="btn btn-erp-deep rounded-pill px-5 py-3 fw-800 shadow-lg border-0">
                            <i class="bi bi-check2-circle me-2"></i>Apply Configuration
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <div class="mt-4 p-4 bg-light-soft rounded-4 stagger-entrance">
            <div class="d-flex align-items-start gap-3">
                <i class="bi bi-shield-exclamation text-warning fs-4"></i>
                <div>
                    <h6 class="fw-800 text-erp-deep mb-2">Critical Configuration Warning</h6>
                    <p class="text-muted mb-0 small fw-600">
                        Modifications to system-wide configuration parameters will immediately propagate across all operational modules. 
                        Timezone and date format changes may affect historical data display. Currency adjustments will not retroactively 
                        convert existing financial records. Ensure all changes align with organizational standards before applying.
                    </p>
                </div>
            </div>
        </div>
</div>
@endsection
