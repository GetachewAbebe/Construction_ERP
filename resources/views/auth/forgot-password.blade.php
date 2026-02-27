@extends('layouts.app')

@section('title', 'Reset Password - Natanem Engineering')

@section('content')
<div class="min-vh-100 d-flex align-items-center py-5">
    <div class="container">
        {{-- Align Items Stretch --}}
        <div class="row w-100 g-5 align-items-stretch">
            
            {{-- LEFT: Info Cards --}}
            <div class="col-lg-6 d-flex flex-column justify-content-center">

                <div class="text-center mb-5 mt-2">
                    <h1 class="display-5 fw-800 text-erp-deep mb-0 tracking-tight">
                        Natanem Engineering
                    </h1>
                </div>

                <div class="vstack gap-3">
                    {{-- Card 1: Secure Process --}}
                    <div class="hardened-glass stagger-entrance transition-all hover-translate-y">
                        <div class="d-flex align-items-center gap-4">
                            <div class="metric-icon" style="background: var(--gradient-primary);">
                                <i class="bi bi-shield-lock-fill fs-3"></i>
                            </div>
                            <div>
                                <h5 class="fw-800 text-erp-deep mb-1">Secure Process</h5>
                                <p class="text-muted small mb-0">Your password reset link is encrypted and expires after 10 minutes.</p>
                            </div>
                        </div>
                    </div>

                    {{-- Card 2: Email Verification --}}
                    <div class="hardened-glass stagger-entrance transition-all hover-translate-y">
                        <div class="d-flex align-items-center gap-4">
                            <div class="metric-icon" style="background: var(--gradient-warning);">
                                <i class="bi bi-envelope-check-fill fs-3"></i>
                            </div>
                            <div>
                                <h5 class="fw-800 text-erp-deep mb-1">Email Verification</h5>
                                <p class="text-muted small mb-0">Reset link will be sent only to your registered email address.</p>
                            </div>
                        </div>
                    </div>

                    {{-- Card 3: Quick Recovery --}}
                    <div class="hardened-glass stagger-entrance transition-all hover-translate-y">
                        <div class="d-flex align-items-center gap-4">
                            <div class="metric-icon" style="background: var(--gradient-danger);">
                                <i class="bi bi-lightning-fill fs-3"></i>
                            </div>
                            <div>
                                <h5 class="fw-800 text-erp-deep mb-1">Quick Recovery</h5>
                                <p class="text-muted small mb-0">Regain access to your account in just a few simple steps.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- RIGHT: Forgot Password form --}}
            <div class="col-lg-6 d-flex">
                <div class="hardened-glass-static p-4 p-md-5 w-100 d-flex flex-column justify-content-center">
                    <div class="text-center mb-5">
                        <h2 class="fw-800 text-erp-deep tracking-tight mb-0">Reset Password</h2>
                    </div>

                    <form method="POST" action="{{ route('password.email') }}" class="mb-3">
                        @csrf
                        
                        <div class="mb-4">
                            <label for="email" class="form-label fw-800 text-erp-deep small text-uppercase tracking-wider">Email Address</label>
                            <div class="input-group">
                                <span class="input-group-text bg-white border-end-0 rounded-start-4">
                                    <i class="bi bi-envelope text-muted"></i>
                                </span>
                                <input type="email" class="form-control border-start-0 rounded-end-4 py-3" id="email" name="email" value="{{ old('email') }}" placeholder="Enter your registered email" required autofocus>
                            </div>
                        </div>

                        {{-- Success Status moved here (Before Button) --}}
                        @if (session('status'))
                            <div class="alert alert-success small mb-4 text-center">
                                {{ session('status') }}
                            </div>
                        @endif

                        <button type="submit" class="btn btn-lg w-100 mt-2 py-3 rounded-4 fw-800 text-white border-0 shadow-lg transition-all"
                                style="background: var(--gradient-primary);">
                            Send Reset Link
                        </button>
                    </form>

                    <div class="text-center">
                        <a href="{{ route('home') }}" class="text-decoration-none fw-semibold" style="color: var(--erp-primary);">
                            <i class="bi bi-arrow-left me-1"></i>
                            Back to Login
                        </a>
                    </div>
                    
                    {{-- Error Notification (Bottom) --}}
                    @if ($errors->any())
                        <div class="alert alert-danger small mt-4 mb-0 text-center">
                            {{ $errors->first() }}
                        </div>
                    @endif

                </div>
            </div>

        </div> {{-- /row --}}
    </div>
</div>
@endsection
