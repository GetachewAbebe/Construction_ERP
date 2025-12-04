@extends('layouts.app')

@section('title', 'Sign in – Natanem Engineering')

@section('content')
<div class="bg-erp-soft min-vh-100 d-flex align-items-center py-4 py-md-5">
    <div class="container">

        {{-- Two parallel columns: left info, right login --}}
        <div class="row w-100 g-4 g-lg-5 align-items-start">

            {{-- LEFT: System info / feature highlights --}}
            <div class="col-lg-6">

                {{-- Centered title + subtitle with just a spacing "break" above --}}
                <div class="text-center mb-4 mt-2">
                    <h1 class="h3 h-lg-2 fw-bold mb-2 text-erp-deep">
                        Natanem Engineering
                    </h1>
                    <h2 class="h5 h-lg-4 fw-semibold mb-0 text-muted">
                        Manage Inventory, Employees and Finance in one System.
                    </h2>
                </div>

                {{-- Three colorful feature cards (Inventory / HR / Finance) --}}
                <div class="vstack gap-3">

                    {{-- Inventory --}}
                    <div class="card border-0 shadow-sm rounded-4">
                        <div class="card-body d-flex">
                            <div class="rounded-circle bg-white shadow-sm d-inline-flex align-items-center justify-content-center me-3"
                                 style="width: 48px; height: 48px; border: 2px solid var(--erp-green);">
                                {{-- Box / inventory icon --}}
                                <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22"
                                     fill="none" viewBox="0 0 24 24" stroke="var(--erp-green)" stroke-width="1.8">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                          d="M4 7.5 12 3l8 4.5m-16 0L12 12m-8-4.5v9L12 21m8-13.5L12 12m8-4.5v9L12 21"/>
                                </svg>
                            </div>
                            <div>
                                <div class="d-flex align-items-center mb-1">
                                    <span class="fw-semibold text-erp-deep me-2">Inventory</span>
                                    <span class="badge rounded-pill bg-erp-soft text-erp-deep small">
                                        Faster approvals
                                    </span>
                                </div>
                                <div class="text-muted small">
                                    Items • Stock • Site tracking
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Human Resource --}}
                    <div class="card border-0 shadow-sm rounded-4">
                        <div class="card-body d-flex">
                            <div class="rounded-circle bg-white shadow-sm d-inline-flex align-items-center justify-content-center me-3"
                                 style="width: 48px; height: 48px; border: 2px solid rgba(255,159,64,.45);">
                                {{-- People / HR icon --}}
                                <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22"
                                     fill="none" viewBox="0 0 24 24" stroke="rgba(255,159,64,1)" stroke-width="1.8">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                          d="M16 7a4 4 0 1 1-8 0 4 4 0 0 1 8 0ZM4 19a6 6 0 0 1 16 0v1H4v-1Z"/>
                                </svg>
                            </div>
                            <div>
                                <div class="d-flex align-items-center mb-1">
                                    <span class="fw-semibold text-erp-deep me-2">Human Resource</span>
                                    <span class="badge rounded-pill bg-erp-soft text-erp-deep small">
                                        Real-time visibility
                                    </span>
                                </div>
                                <div class="text-muted small">
                                    Employees • Positions • Leave
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Finance --}}
                    <div class="card border-0 shadow-sm rounded-4">
                        <div class="card-body d-flex">
                            <div class="rounded-circle bg-white shadow-sm d-inline-flex align-items-center justify-content-center me-3"
                                 style="width: 48px; height: 48px; border: 2px solid var(--erp-green);">
                                {{-- Finance / currency icon --}}
                                <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22"
                                     fill="none" viewBox="0 0 24 24" stroke="var(--erp-green)" stroke-width="1.8">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                          d="M8 7h8M8 12h5M6 4h12a2 2 0 0 1 2 2v12a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2Z"/>
                                </svg>
                            </div>
                            <div>
                                <div class="d-flex align-items-center mb-1">
                                    <span class="fw-semibold text-erp-deep me-2">Finance</span>
                                    <span class="badge rounded-pill bg-erp-soft text-erp-deep small">
                                        Audit-ready trails
                                    </span>
                                </div>
                                <div class="text-muted small">
                                    Invoices • Payments • Reports
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>

            {{-- RIGHT: Login form --}}
            <div class="col-lg-6">
                <div
                    class="card shadow-sm rounded-4 overflow-hidden h-100"
                    style="
                        border: 1px solid var(--erp-green);
                        border-top: 3px solid #ff9f40;
                    "
                >
                    <div class="card-body p-4 p-md-5 d-flex flex-column justify-content-center">

                        {{-- Centered login title --}}
                        <h2 class="h4 fw-bold mb-4 text-center">
                            Sign in to Natanem ERP
                        </h2>

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

                            <div class="mb-3">
                                <label for="email" class="form-label small text-uppercase text-muted fw-semibold">
                                    Email
                                </label>
                                <div class="input-group input-group-lg">
                                    <span
                                        class="input-group-text bg-white border-end-0"
                                        style="border-color: var(--erp-green);"
                                    >
                                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18"
                                             fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.6">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                  d="M4 6h16v12H4zM4 6l8 6 8-6"/>
                                        </svg>
                                    </span>
                                    <input
                                        type="email"
                                        class="form-control border-start-0 shadow-sm-sm rounded-end-3"
                                        style="border-color: var(--erp-green);"
                                        id="email"
                                        name="email"
                                        value="{{ old('email') }}"
                                        autocomplete="username"
                                        required
                                    >
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="password" class="form-label small text-uppercase text-muted fw-semibold">
                                    Password
                                </label>
                                <div class="input-group input-group-lg">
                                    <span
                                        class="input-group-text bg-white border-end-0"
                                        style="border-color: var(--erp-green);"
                                    >
                                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18"
                                             fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.6">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                  d="M12 17v-3m-4 6h8a2 2 0 0 0 2-2v-5a2 2 0 0 0-2-2H8a2 2 0 0 0-2 2v5a2 2 0 0 0 2 2Zm2-9V7a2 2 0 1 1 4 0v3"/>
                                            </svg>
                                    </span>
                                    <input
                                        type="password"
                                        class="form-control border-start-0 shadow-sm-sm rounded-end-3"
                                        style="border-color: var(--erp-green);"
                                        id="password"
                                        name="password"
                                        autocomplete="current-password"
                                        required
                                    >
                                </div>
                            </div>

                            {{-- Solid theme-colored sign in button --}}
                            <button
                                type="submit"
                                class="btn btn-lg w-100 mt-2 shadow-sm text-white border-0"
                                style="
                                    background: var(--erp-green);
                                    box-shadow: 0 0.5rem 1.25rem rgba(0,0,0,.12);
                                "
                            >
                                Sign in
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
