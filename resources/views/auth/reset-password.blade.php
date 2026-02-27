@extends('layouts.app')

@section('title', 'Create New Password - Natanem Engineering')

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

            {{-- RIGHT: Create New Password form --}}
            <div class="col-lg-6 d-flex">
                <div class="hardened-glass-static p-4 p-md-5 w-100 d-flex flex-column justify-content-center">
                    <div class="text-center mb-5">
                        <h2 class="fw-800 text-erp-deep tracking-tight mb-0">Create New Password</h2>
                    </div>

                    <form method="POST" action="{{ route('password.update') }}" class="mb-3">
                        @csrf
                        <input type="hidden" name="token" value="{{ $request->route('token') }}">

                        {{-- Email Address (Readonly) --}}
                        <div class="mb-4">
                            <label for="email" class="form-label fw-800 text-erp-deep small text-uppercase tracking-wider">Email Address</label>
                            <div class="input-group">
                                <span class="input-group-text bg-white border-end-0 rounded-start-4">
                                    <i class="bi bi-envelope text-muted"></i>
                                </span>
                                <input type="email" class="form-control border-start-0 rounded-end-4 py-3" id="email" name="email" value="{{ old('email', $request->email) }}" readonly>
                            </div>
                        </div>

                        {{-- New Password with Toggle + Strength --}}
                        <div class="mb-4">
                            <label for="password" class="form-label fw-800 text-erp-deep small text-uppercase tracking-wider">New Password</label>
                            <div class="input-group">
                                <span class="input-group-text bg-white border-end-0 rounded-start-4">
                                    <i class="bi bi-lock text-muted"></i>
                                </span>
                                <input type="password" class="form-control border-start-0 border-end-0 py-3" id="password" name="password" placeholder="Min. 8 characters" required autofocus onkeyup="checkStrength(this.value); checkMatch();">
                                <button class="btn btn-secondary border-start-0 rounded-end-4" type="button" onclick="togglePasswordVis('password', 'reset-text')" style="min-width: 80px;">
                                    <span id="reset-text">Show</span> <i class="bi bi-eye ms-1"></i>
                                </button>
                            </div>
                            {{-- Strength Meter --}}
                            <div class="mt-2 d-none" id="strength-container">
                                <div class="progress" style="height: 4px;">
                                    <div class="progress-bar transition-all" role="progressbar" id="strength-bar" style="width: 0%"></div>
                                </div>
                                <div class="d-flex justify-content-between align-items-center mt-1">
                                    <small class="text-muted" style="font-size: 0.75rem;">Strength</small>
                                    <small class="fw-bold" id="strength-text" style="font-size: 0.75rem;">Weak</small>
                                </div>
                            </div>
                        </div>

                        {{-- Confirm Password with Toggle --}}
                        <div class="mb-4">
                            <label for="password_confirmation" class="form-label fw-800 text-erp-deep small text-uppercase tracking-wider">Confirm Password</label>
                            <div class="input-group">
                                <span class="input-group-text bg-white border-end-0 rounded-start-4">
                                    <i class="bi bi-lock-fill text-muted"></i>
                                </span>
                                <input type="password" class="form-control border-start-0 border-end-0 py-3" id="password_confirmation" name="password_confirmation" placeholder="Re-enter new password" required onkeyup="checkMatch()">
                                <button class="btn btn-secondary border-start-0 rounded-end-4" type="button" onclick="togglePasswordVis('password_confirmation', 'confirm-text')" style="min-width: 80px;">
                                    <span id="confirm-text">Show</span> <i class="bi bi-eye ms-1"></i>
                                </button>
                            </div>
                            {{-- Match Message --}}
                            <div class="mt-2 d-none" id="match-message" style="font-size: 0.85rem;"></div>
                        </div>

                        {{-- Error Notification (Moved Here) --}}
                        @if ($errors->any())
                            <div class="alert alert-danger small mb-3 text-center rounded-3">
                                @if($errors->first() == 'This password reset token is invalid.')
                                    Token is expired
                                @else
                                    {{ $errors->first() }}
                                @endif
                            </div>
                        @endif

                        <button type="submit" class="btn btn-lg w-100 mt-2 py-3 rounded-4 fw-800 text-white border-0 shadow-lg transition-all"
                                style="background: var(--gradient-primary);">
                            Reset Password
                        </button>
                    </form>

                    {{-- Back to Login --}}
                    <div class="text-center">
                        <a href="{{ route('home') }}" class="text-decoration-none fw-semibold" style="color: var(--erp-primary);">
                            <i class="bi bi-arrow-left me-1"></i>
                            Back to Login
                        </a>
                    </div>

                </div>
            </div>

        </div> {{-- /row --}}
    </div>
</div>

<script>
    function togglePasswordVis(inputId, textId) {
        const input = document.getElementById(inputId);
        const textSpan = document.getElementById(textId);
        if (input.type === "password") {
            input.type = "text";
            textSpan.textContent = "Hide";
        } else {
            input.type = "password";
            textSpan.textContent = "Show";
        }
    }

    function checkStrength(password) {
        const container = document.getElementById('strength-container');
        const bar = document.getElementById('strength-bar');
        const text = document.getElementById('strength-text');
        
        if (password.length > 0) {
            container.classList.remove('d-none');
        } else {
            container.classList.add('d-none');
            return;
        }

        let strength = 0;
        if (password.length >= 8) strength += 1;
        if (password.match(/[A-Z]/)) strength += 1;
        if (password.match(/[0-9]/)) strength += 1;
        if (password.match(/[^a-zA-Z0-9]/)) strength += 1;

        if (password.length < 8) {
            // Force weak if simply too short
            strength = 0; 
        }

        switch(strength) {
            case 0:
            case 1:
                bar.style.width = "33%";
                bar.className = "progress-bar bg-danger";
                text.innerHTML = "Weak";
                text.className = "fw-bold text-danger";
                break;
            case 2:
            case 3:
                bar.style.width = "66%";
                bar.className = "progress-bar bg-warning";
                text.innerHTML = "Medium";
                text.className = "fw-bold text-warning";
                break;
            case 4:
                bar.style.width = "100%";
                bar.className = "progress-bar bg-success";
                text.innerHTML = "Strong";
                text.className = "fw-bold text-success";
                break;
        }
    }

    function checkMatch() {
        const p1 = document.getElementById('password').value;
        const p2 = document.getElementById('password_confirmation').value;
        const msg = document.getElementById('match-message');
        
        if (p2.length === 0) {
            msg.classList.add('d-none');
            return;
        }
        msg.classList.remove('d-none');
        
        if (p1 === p2) {
            msg.innerHTML = '<span class="text-success fw-bold"><i class="bi bi-check-circle-fill me-1"></i> Passwords Match</span>';
        } else {
            msg.innerHTML = '<span class="text-danger fw-bold"><i class="bi bi-x-circle-fill me-1"></i> Passwords do not match</span>';
        }
    }
</script>
@endsection
