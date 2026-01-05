@extends('layouts.app')

@section('title', 'Natanem Engineering')

@section('content')
<div class="min-vh-100 d-flex align-items-center py-5">
    <div class="container">
        <div class="row w-100 g-5 align-items-center">
            {{-- LEFT: System info / feature highlights --}}
            <div class="col-lg-6">

                {{-- Centered title + subtitle with just a spacing "break" above --}}
                <div class="text-center mb-5 mt-2">
                    <h1 class="display-5 fw-800 text-erp-deep mb-0 tracking-tight">
                        Natanem Engineering
                    </h1>
                </div>

                {{-- Three colorful feature cards (Inventory / HR / Finance) --}}
                <div class="vstack gap-3">

                    {{-- Inventory --}}
                    <div class="hardened-glass stagger-entrance transition-all hover-translate-y">
                        <div class="d-flex align-items-center gap-4">
                            <div class="metric-icon" style="background: var(--gradient-primary);">
                                <i class="bi bi-box-seam-fill fs-3"></i>
                            </div>
                            <div>
                                <h5 class="fw-800 text-erp-deep mb-1">Inventory Management</h5>
                                <p class="text-muted small mb-0">High-precision material tracking and stock logistics.</p>
                            </div>
                        </div>
                    </div>

                    {{-- Human Resource --}}
                    <div class="hardened-glass stagger-entrance transition-all hover-translate-y">
                        <div class="d-flex align-items-center gap-4">
                            <div class="metric-icon" style="background: var(--gradient-warning);">
                                <i class="bi bi-person-badge-fill fs-3"></i>
                            </div>
                            <div>
                                <h5 class="fw-800 text-erp-deep mb-1">Human Resource Management</h5>
                                <p class="text-muted small mb-0">Strategic organizational analytics and team management.</p>
                            </div>
                        </div>
                    </div>

                    {{-- Finance --}}
                    <div class="hardened-glass stagger-entrance transition-all hover-translate-y">
                        <div class="d-flex align-items-center gap-4">
                            <div class="metric-icon" style="background: var(--gradient-danger);">
                                <i class="bi bi-bar-chart-steps fs-3"></i>
                            </div>
                            <div>
                                <h5 class="fw-800 text-erp-deep mb-1">Financial Management</h5>
                                <p class="text-muted small mb-0">Comprehensive project budgeting and cash flow audits.</p>
                            </div>
                        </div>
                    </div>

                </div>
            </div>

            {{-- RIGHT: Login form --}}
            <div class="col-lg-6">
                <div class="hardened-glass-static p-4 p-md-5">
                    <div class="text-center mb-5">
                        <h2 class="fw-800 text-erp-deep tracking-tight mb-0">Login</h2>
                    </div>

                        {{-- Status / flash message --}}
                        @if (session('status'))
                            <div class="alert alert-success small mb-3 text-center">
                                {{ session('status') }}
                            </div>
                        @endif

                        {{-- Validation errors --}}
                        @if ($errors->any())
                            <div class="alert alert-danger small mb-3 text-center">
                                {{ $errors->first() }}
                            </div>
                        @endif

                        <form method="POST" action="{{ route('login') }}" class="mb-3">
                            @csrf

                            <div class="mb-4">
                                <label for="email" class="form-label fw-800 text-erp-deep small text-uppercase tracking-wider">Email</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-white border-end-0 rounded-start-4">
                                        <i class="bi bi-envelope text-muted"></i>
                                    </span>
                                    <input type="email" class="form-control border-start-0 rounded-end-4 py-3" id="email" name="email" value="{{ old('email') }}" required autocomplete="username">
                                </div>
                            </div>

                            <div class="mb-4">
                                <label for="password" class="form-label fw-800 text-erp-deep small text-uppercase tracking-wider">Password</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-white border-end-0 rounded-start-4">
                                        <i class="bi bi-lock text-muted"></i>
                                    </span>
                                    <input type="password" class="form-control border-start-0 rounded-end-4 py-3" id="password" name="password" required autocomplete="current-password">
                                </div>
                            </div>

                            <button type="submit" class="btn btn-lg w-100 mt-2 py-3 rounded-4 fw-800 text-white border-0 shadow-lg transition-all"
                                    style="background: var(--gradient-primary);">
                                Login
                            </button>
                        </form>

                        {{-- Centered helper text under the form --}}
                        <p class="text-muted small mb-0 text-center">
                            Having trouble signing in? <span class="fw-semibold">Contact the Admin.</span>
                        </p>
                    </div>
                </div>
            </div>

        </div> {{-- /row --}}
    </div>
</div>
@endsection
