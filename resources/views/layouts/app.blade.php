<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>@yield('title','Natanem Engineering')</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">

  @vite(['resources/sass/app.scss','resources/js/app.js'])

  <style>
      :root {
          --erp-green:  #198754;
          --erp-orange: #ff9f40;
          --erp-deep:   #145c32;
      }

      .navbar-gradient {
          background: var(--erp-green);
      }

      .footer-gradient {
          background: var(--erp-green);
      }

      .bg-erp-soft {
          background-color: rgba(25,135,84,0.09);
      }

      .text-erp-deep {
          color: var(--erp-deep);
      }

      .shadow-soft {
          box-shadow: 0 0.35rem 1rem rgba(0,0,0,.06);
      }

      /* Header nav text effects */
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

      /* Shared pill dot */
      .erp-pill-dot {
          width: 6px;
          height: 6px;
          border-radius: 999px;
          background: #ffffff;
          box-shadow: 0 0 0 3px rgba(255,255,255,0.3);
      }

      /* Brand pill */
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

      /* -----------------------------
         Dropdown submenu (Bootstrap 5)
         ----------------------------- */
      .dropdown-menu {
          border-radius: 14px;
          box-shadow: 0 12px 30px rgba(0,0,0,.18);
          border: 1px solid rgba(0,0,0,.06);
      }

      .dropdown-item {
          font-size: .9rem;
      }

      .dropdown-submenu {
          position: relative;
      }

      .dropdown-submenu > .dropdown-menu {
          top: 0;
          left: 100%;
          margin-left: .25rem;
          display: none;
      }

      /* show submenu on hover for desktop */
      @media (min-width: 992px) {
          .dropdown-submenu:hover > .dropdown-menu {
              display: block;
          }
      }

      /* caret for submenu */
      .submenu-toggle::after {
          content: "›";
          float: right;
          margin-left: .6rem;
          opacity: .7;
      }
  </style>

  @stack('head')
</head>
<body class="bg-light d-flex flex-column min-vh-100">

  {{-- HEADER --}}
  @unless (request()->routeIs('home'))
  <header class="navbar navbar-expand-lg navbar-dark navbar-gradient shadow-soft py-2">
    <div class="container d-flex align-items-center">

      {{-- Brand pill (left) --}}
      <a class="brand-pill me-3" href="{{ route('home') }}">
        <span class="erp-pill-dot"></span>
        <span class="label">Natanem Engineering</span>
      </a>

      {{-- Toggler --}}
      <button class="navbar-toggler ms-auto" type="button" data-bs-toggle="collapse" data-bs-target="#mainNav">
        <span class="navbar-toggler-icon"></span>
      </button>

      {{-- Nav --}}
      <div class="collapse navbar-collapse" id="mainNav">
        <ul class="navbar-nav ms-auto mb-2 mb-lg-0">

          @auth
            @php
              $rawRole  = auth()->user()->role ?? '';
              $roleSlug = strtolower(trim($rawRole));
            @endphp

            {{-- =========================
                 ADMIN MENU
                 ========================= --}}
            @if($roleSlug === 'administrator')

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
                  HR Resource
                </a>
              </li>

              <li class="nav-item">
                <a class="nav-link" href="#">
                  Attendance
                </a>
              </li>

              <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('inventory.*') ? 'active' : '' }}"
                   href="{{ route('inventory.dashboard') }}">
                  Inventory
                </a>
              </li>

              <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('finance.*') ? 'active' : '' }}"
                   href="{{ route('finance.dashboard') }}">
                  Finance
                </a>
              </li>

              {{-- Approvals (3 sub-dropdowns) --}}
              <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle {{ request()->routeIs('admin.requests.*') ? 'active' : '' }}"
                   href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                  Approvals
                </a>

                <ul class="dropdown-menu dropdown-menu-end p-2">

                  {{-- HR approvals submenu --}}
                  <li class="dropdown-submenu">
                    <a class="dropdown-item submenu-toggle" href="#" role="button">
                      HR
                    </a>
                    <ul class="dropdown-menu p-2">
                      <li>
                        <a class="dropdown-item"
                           href="{{ route('admin.requests.leave-approvals.index') }}">
                          Leave Approvals
                        </a>
                      </li>
                    </ul>
                  </li>

                  {{-- Inventory approvals submenu --}}
                  <li><hr class="dropdown-divider"></li>
                  <li class="dropdown-submenu">
                    <a class="dropdown-item submenu-toggle" href="#" role="button">
                      Inventory
                    </a>
                    <ul class="dropdown-menu p-2">
                      <li>
                        <a class="dropdown-item"
                           href="{{ route('admin.requests.items') }}">
                          Item Lending Requests
                        </a>
                      </li>
                    </ul>
                  </li>

                  {{-- Finance approvals submenu --}}
                  <li><hr class="dropdown-divider"></li>
                  <li class="dropdown-submenu">
                    <a class="dropdown-item submenu-toggle" href="#" role="button">
                      Finance
                    </a>
                    <ul class="dropdown-menu p-2">
                      <li>
                        <a class="dropdown-item" href="#">
                          Payment Approvals (Soon)
                        </a>
                      </li>
                      <li>
                        <a class="dropdown-item" href="#">
                          Invoice Approvals (Soon)
                        </a>
                      </li>
                    </ul>
                  </li>

                </ul>
              </li>

            {{-- =========================
                 HR MENU
                 ========================= --}}
            @elseif($roleSlug === 'humanresourcemanager')
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

            {{-- =========================
                 INVENTORY MENU
                 ========================= --}}
            @elseif($roleSlug === 'inventorymanager')
              <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('inventory.dashboard') ? 'active' : '' }}"
                   href="{{ route('inventory.dashboard') }}">
                  Home
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
                <a class="nav-link {{ request()->routeIs('inventory.loans.*') ? 'active' : '' }}"
                   href="{{ route('inventory.loans.index') }}">
                  Lend Items
                </a>
              </li>

            {{-- =========================
                 FINANCE MENU
                 ========================= --}}
            @elseif($roleSlug === 'financialmanager')
              <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('finance.dashboard') ? 'active' : '' }}"
                   href="{{ route('finance.dashboard') }}">
                  Home
                </a>
              </li>
              <li class="nav-item">
                <a class="nav-link" href="#">
                  Invoices
                </a>
              </li>
              <li class="nav-item">
                <a class="nav-link" href="#">
                  Payments
                </a>
              </li>
              <li class="nav-item">
                <a class="nav-link" href="#">
                  Reports
                </a>
              </li>
            @endif

            {{-- Sign out (all roles) --}}
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

  {{-- FOOTER --}}
  <footer class="footer-gradient text-white mt-auto py-2">
    <div class="container d-flex flex-column align-items-center justify-content-center text-center small">
      <div>© {{ date('Y') }} Natanem Engineering</div>
    </div>
  </footer>

  {{-- JS: enable submenu click on mobile --}}
  <script>
    document.addEventListener('click', function (e) {
      const toggle = e.target.closest('.dropdown-submenu > a');
      if (!toggle) return;

      // only handle when dropdown is open
      const parentLi = toggle.closest('.dropdown-submenu');
      const menu = parentLi.querySelector('.dropdown-menu');
      if (!menu) return;

      // on mobile/tablet, toggle submenu on click
      if (window.innerWidth < 992) {
        e.preventDefault();
        menu.style.display = (menu.style.display === 'block') ? 'none' : 'block';
      }
    });

    // close any open submenus when the main dropdown closes
    document.addEventListener('hide.bs.dropdown', function (e) {
      const root = e.target;
      root.querySelectorAll('.dropdown-submenu > .dropdown-menu').forEach(m => m.style.display = 'none');
    });
  </script>

  @stack('scripts')
</body>
</html>
