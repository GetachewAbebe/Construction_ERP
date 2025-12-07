<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>@yield('title','Natanem Engineering')</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">

  @vite(['resources/sass/app.scss','resources/js/app.js'])

  <style>
      :root {
          /* Theme colors */
          --erp-green:  #73AF6F;
          --erp-orange: #ff9f40;
          --erp-deep:   #145c32;
      }

      .navbar-gradient {
          background: linear-gradient(135deg, var(--erp-green), var(--erp-green));
      }

      .footer-gradient {
          background: linear-gradient(135deg, var(--erp-green), var(--erp-green));
      }

      .bg-erp-soft {
          background-color: rgba(115, 175, 111, 0.09);
      }

      .text-erp-deep {
          color: var(--erp-deep);
      }

      .shadow-soft {
          box-shadow: 0 0.35rem 1rem rgba(0,0,0,.06);
      }

      .navbar-nav .nav-link {
          position: relative;
          font-weight: 500;
          letter-spacing: 0.02em;
          text-transform: uppercase;
          font-size: 0.78rem;
          opacity: 0.95;
      }

      .navbar-nav .nav-link::after {
          content: "";
          position: absolute;
          left: 0;
          bottom: -0.15rem;
          width: 0;
          height: 2px;
          border-radius: 999px;
          background: rgba(255,255,255,0.9);
          transition: width 0.2s ease-out;
      }

      .navbar-nav .nav-link:hover::after,
      .navbar-nav .nav-link.active::after {
          width: 100%;
      }

      .navbar-nav .nav-link.active {
          opacity: 1;
      }

      .erp-pill-dot {
          width: 6px;
          height: 6px;
          border-radius: 999px;
          background: #ffffff;
          box-shadow: 0 0 0 3px rgba(255,255,255,0.3);
      }

      .brand-pill {
          border-radius: 999px;
          border: 1px solid rgba(255,255,255,0.7);
          background: rgba(255,255,255,0.14);
          padding: 0.22rem 1.3rem;
          font-size: 0.78rem;
          letter-spacing: 0.12em;
          text-transform: uppercase;
          color: #ffffff !important;
          display: inline-flex;
          align-items: center;
          gap: 0.55rem;
          box-shadow: 0 0.25rem 0.75rem rgba(0,0,0,.22);
          backdrop-filter: blur(6px);
          text-decoration: none;
          text-align: center;
          white-space: nowrap;
      }

      .brand-pill span.label {
          font-weight: 600;
      }
  </style>

  @stack('head')
</head>
<body class="bg-light d-flex flex-column min-vh-100">

  {{-- HEADER (hidden on the home route) --}}
  @unless(request()->routeIs('home'))
      <header class="navbar navbar-expand-lg navbar-dark navbar-gradient shadow-soft py-2">
        <div class="container position-relative d-flex align-items-center justify-content-between">

          @php
              // Decide where the brand should point
              $dashboardRoute = 'home';

              if (auth()->check()) {
                  $userRoleRaw  = auth()->user()->role ?? null;
                  $userRoleSlug = $userRoleRaw ? strtolower(trim($userRoleRaw)) : null;

                  if ($userRoleSlug === 'administrator') {
                      $dashboardRoute = 'admin.dashboard';
                  } elseif ($userRoleSlug === 'humanresourcemanager') {
                      $dashboardRoute = 'hr.dashboard';
                  } elseif ($userRoleSlug === 'inventorymanager') {
                      $dashboardRoute = 'inventory.dashboard';
                  } elseif ($userRoleSlug === 'financialmanager') {
                      $dashboardRoute = 'finance.dashboard';
                  }
              }
          @endphp

          {{-- Brand --}}
          <a class="brand-pill"
             href="{{ route($dashboardRoute) }}">
              <span class="erp-pill-dot"></span>
              <span class="label">
                  Natanem Engineering
              </span>
          </a>

          {{-- Toggler for mobile --}}
          <button class="navbar-toggler ms-2" type="button" data-bs-toggle="collapse" data-bs-target="#mainNav">
            <span class="navbar-toggler-icon"></span>
          </button>

          {{-- Nav --}}
          <div class="collapse navbar-collapse" id="mainNav">
            <ul class="navbar-nav ms-auto mb-2 mb-lg-0">

              @auth
                  @php
                      $rawRole = auth()->user()->role ?? null;
                      $role    = $rawRole ? strtolower(trim($rawRole)) : null;
                  @endphp

                  {{-- Administrator --}}
                  @if ($role === 'administrator')
                      <li class="nav-item">
                          <a class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}"
                             href="{{ route('admin.dashboard') }}">
                              Overview
                          </a>
                      </li>
                      <li class="nav-item">
                          <a class="nav-link {{ request()->routeIs('admin.users.*') ? 'active' : '' }}"
                             href="{{ route('admin.users.index') }}">
                              Users
                          </a>
                      </li>
                      <li class="nav-item">
                          <a class="nav-link {{ request()->routeIs('hr.*') ? 'active' : '' }}"
                             href="{{ route('hr.dashboard') }}">
                              HR Section
                          </a>
                      </li>
                      <li class="nav-item">
                          <a class="nav-link {{ request()->routeIs('inventory.*') ? 'active' : '' }}"
                             href="{{ route('inventory.dashboard') }}">
                              Inventory Section
                          </a>
                      </li>
                      <li class="nav-item">
                          <a class="nav-link {{ request()->routeIs('finance.*') ? 'active' : '' }}"
                             href="{{ route('finance.dashboard') }}">
                              Finance Section
                          </a>
                      </li>
                      <li class="nav-item">
                          <a class="nav-link" href="#">
                              Approvals
                          </a>
                      </li>

                  {{-- Human Resource Manager --}}
                  @elseif ($role === 'humanresourcemanager')
                      <li class="nav-item">
                          <a class="nav-link {{ request()->routeIs('hr.dashboard') ? 'active' : '' }}"
                             href="{{ route('hr.dashboard') }}">
                              Home
                          </a>
                      </li>
                      <li class="nav-item">
                          <a class="nav-link {{ request()->routeIs('hr.employees.*') ? 'active' : '' }}"
                             href="{{ route('hr.employees.index') }}">
                              Employees
                          </a>
                      </li>
                      <li class="nav-item">
                          <a class="nav-link {{ request()->routeIs('hr.leaves.index') ? 'active' : '' }}"
                             href="{{ route('hr.leaves.index') }}">
                              Leave Requests
                          </a>
                      </li>
                      <li class="nav-item">
                          <a class="nav-link {{ request()->routeIs('hr.leaves.create') ? 'active' : '' }}"
                             href="{{ route('hr.leaves.create') }}">
                              File Leave
                          </a>
                      </li>

                  {{-- Inventory Manager --}}
                  @elseif ($role === 'inventorymanager')
                      <li class="nav-item">
                          <a class="nav-link {{ request()->routeIs('inventory.dashboard') ? 'active' : '' }}"
                             href="{{ route('inventory.dashboard') }}">
                              Overview
                          </a>
                      </li>
                      <li class="nav-item">
                          <a class="nav-link {{ request()->routeIs('inventory.items.index') ? 'active' : '' }}"
                             href="{{ route('inventory.items.index') }}">
                              Browse Items
                          </a>
                      </li>
                      <li class="nav-item">
                          <a class="nav-link {{ request()->routeIs('inventory.items.create') ? 'active' : '' }}"
                             href="{{ route('inventory.items.create') }}">
                              Add Item
                          </a>
                      </li>
                      <li class="nav-item">
                          <a class="nav-link {{ request()->routeIs('inventory.loans.create') ? 'active' : '' }}"
                             href="{{ route('inventory.loans.create') }}">
                              Lend Item
                          </a>
                      </li>
                      <li class="nav-item">
                          <a class="nav-link {{ request()->routeIs('inventory.loans.index') ? 'active' : '' }}"
                             href="{{ route('inventory.loans.index') }}">
                              Loan History
                          </a>
                      </li>

                  {{-- Financial Manager --}}
                  @elseif ($role === 'financialmanager')
                      <li class="nav-item">
                          <a class="nav-link {{ request()->routeIs('finance.*') ? 'active' : '' }}"
                             href="{{ route('finance.dashboard') }}">
                              Finance
                          </a>
                      </li>
                  @endif

                  <li class="nav-item ms-lg-3">
                      <form method="POST" action="{{ route('logout') }}">
                          @csrf
                          <button type="submit" class="btn btn-sm btn-outline-light">
                              Sign out
                          </button>
                      </form>
                  </li>
              @endauth

              @guest
                  {{-- Login handled on home page; no header link --}}
              @endguest

            </ul>
          </div>
        </div>
      </header>
  @endunless

  {{-- MAIN CONTENT --}}
  <main class="flex-fill">
    @yield('content')
  </main>

  {{-- FOOTER (hidden on the home route) --}}
  @unless(request()->routeIs('home'))
      <footer class="footer-gradient text-white mt-auto py-2">
        <div class="container d-flex flex-column align-items-center justify-content-center text-center small">
          <div>© {{ date('Y') }} Enterprise Resource Planning</div>
        </div>
      </footer>
  @endunless

  @stack('scripts')
</body>
</html>
