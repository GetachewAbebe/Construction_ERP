<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title><?php echo $__env->yieldContent('title','Natanem Engineering'); ?></title>
  <meta name="viewport" content="width=device-width, initial-scale=1">

  <?php echo app('Illuminate\Foundation\Vite')(['resources/sass/app.scss','resources/js/app.js']); ?>

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

  <?php echo $__env->yieldPushContent('head'); ?>
</head>
<body class="bg-light d-flex flex-column min-vh-100">

  
  <?php if (! (request()->routeIs('home'))): ?>
  <header class="navbar navbar-expand-lg navbar-dark navbar-gradient shadow-soft py-2">
    <div class="container d-flex align-items-center">

      
      <a class="brand-pill me-3" href="<?php echo e(route('home')); ?>">
        <span class="erp-pill-dot"></span>
        <span class="label">Natanem Engineering</span>
      </a>

      
      <button class="navbar-toggler ms-auto" type="button" data-bs-toggle="collapse" data-bs-target="#mainNav">
        <span class="navbar-toggler-icon"></span>
      </button>

      
      <div class="collapse navbar-collapse" id="mainNav">
        <ul class="navbar-nav ms-auto mb-2 mb-lg-0">

          <?php if(auth()->guard()->check()): ?>
            <?php
              $rawRole  = auth()->user()->role ?? '';
              $roleSlug = strtolower(trim($rawRole));
            ?>

            
            <?php if($roleSlug === 'administrator'): ?>

              <li class="nav-item">
                <a class="nav-link <?php echo e(request()->routeIs('admin.dashboard') ? 'active' : ''); ?>"
                   href="<?php echo e(route('admin.dashboard')); ?>">
                  Overview
                </a>
              </li>

              <li class="nav-item">
                <a class="nav-link <?php echo e(request()->routeIs('admin.users.*') ? 'active' : ''); ?>"
                   href="<?php echo e(route('admin.users.index')); ?>">
                  Users
                </a>
              </li>

              <li class="nav-item">
                <a class="nav-link <?php echo e(request()->routeIs('hr.*') ? 'active' : ''); ?>"
                   href="<?php echo e(route('hr.dashboard')); ?>">
                  HR Resource
                </a>
              </li>

              <li class="nav-item">
                <a class="nav-link" href="#">
                  Attendance
                </a>
              </li>

              <li class="nav-item">
                <a class="nav-link <?php echo e(request()->routeIs('inventory.*') ? 'active' : ''); ?>"
                   href="<?php echo e(route('inventory.dashboard')); ?>">
                  Inventory
                </a>
              </li>

              <li class="nav-item">
                <a class="nav-link <?php echo e(request()->routeIs('finance.*') ? 'active' : ''); ?>"
                   href="<?php echo e(route('finance.dashboard')); ?>">
                  Finance
                </a>
              </li>

              
              <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle <?php echo e(request()->routeIs('admin.requests.*') ? 'active' : ''); ?>"
                   href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                  Approvals
                </a>

                <ul class="dropdown-menu dropdown-menu-end p-2">

                  
                  <li class="dropdown-submenu">
                    <a class="dropdown-item submenu-toggle" href="#" role="button">
                      HR
                    </a>
                    <ul class="dropdown-menu p-2">
                      <li>
                        <a class="dropdown-item"
                           href="<?php echo e(route('admin.requests.leave-approvals.index')); ?>">
                          Leave Approvals
                        </a>
                      </li>
                    </ul>
                  </li>

                  
                  <li><hr class="dropdown-divider"></li>
                  <li class="dropdown-submenu">
                    <a class="dropdown-item submenu-toggle" href="#" role="button">
                      Inventory
                    </a>
                    <ul class="dropdown-menu p-2">
                      <li>
                        <a class="dropdown-item"
                           href="<?php echo e(route('admin.requests.items')); ?>">
                          Item Lending Requests
                        </a>
                      </li>
                    </ul>
                  </li>

                  
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

            
            <?php elseif($roleSlug === 'humanresourcemanager'): ?>
              <li class="nav-item">
                <a class="nav-link <?php echo e(request()->routeIs('hr.dashboard') ? 'active' : ''); ?>"
                   href="<?php echo e(route('hr.dashboard')); ?>">
                  Home
                </a>
              </li>
              <li class="nav-item">
                <a class="nav-link <?php echo e(request()->routeIs('hr.employees.*') ? 'active' : ''); ?>"
                   href="<?php echo e(route('hr.employees.index')); ?>">
                  Employees
                </a>
              </li>
              <li class="nav-item">
                <a class="nav-link <?php echo e(request()->routeIs('hr.leaves.index') ? 'active' : ''); ?>"
                   href="<?php echo e(route('hr.leaves.index')); ?>">
                  Leave Requests
                </a>
              </li>
              <li class="nav-item">
                <a class="nav-link <?php echo e(request()->routeIs('hr.leaves.create') ? 'active' : ''); ?>"
                   href="<?php echo e(route('hr.leaves.create')); ?>">
                  File Leave
                </a>
              </li>

            
            <?php elseif($roleSlug === 'inventorymanager'): ?>
              <li class="nav-item">
                <a class="nav-link <?php echo e(request()->routeIs('inventory.dashboard') ? 'active' : ''); ?>"
                   href="<?php echo e(route('inventory.dashboard')); ?>">
                  Home
                </a>
              </li>
              <li class="nav-item">
                <a class="nav-link <?php echo e(request()->routeIs('inventory.items.index') ? 'active' : ''); ?>"
                   href="<?php echo e(route('inventory.items.index')); ?>">
                  Browse Items
                </a>
              </li>
              <li class="nav-item">
                <a class="nav-link <?php echo e(request()->routeIs('inventory.items.create') ? 'active' : ''); ?>"
                   href="<?php echo e(route('inventory.items.create')); ?>">
                  Add Item
                </a>
              </li>
              <li class="nav-item">
                <a class="nav-link <?php echo e(request()->routeIs('inventory.loans.*') ? 'active' : ''); ?>"
                   href="<?php echo e(route('inventory.loans.index')); ?>">
                  Lend Items
                </a>
              </li>

            
            <?php elseif($roleSlug === 'financialmanager'): ?>
              <li class="nav-item">
                <a class="nav-link <?php echo e(request()->routeIs('finance.dashboard') ? 'active' : ''); ?>"
                   href="<?php echo e(route('finance.dashboard')); ?>">
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
            <?php endif; ?>

            
            <li class="nav-item ms-lg-3">
              <form method="POST" action="<?php echo e(route('logout')); ?>">
                <?php echo csrf_field(); ?>
                <button type="submit" class="btn btn-sm btn-outline-light">
                  Sign out
                </button>
              </form>
            </li>

          <?php endif; ?>

          <?php if(auth()->guard()->guest()): ?>
            
          <?php endif; ?>

        </ul>
      </div>
    </div>
  </header>
  <?php endif; ?>

  
  <main class="flex-fill">
    <?php echo $__env->yieldContent('content'); ?>
  </main>

  
  <footer class="footer-gradient text-white mt-auto py-2">
    <div class="container d-flex flex-column align-items-center justify-content-center text-center small">
      <div>© <?php echo e(date('Y')); ?> Natanem Engineering</div>
    </div>
  </footer>

  
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

  <?php echo $__env->yieldPushContent('scripts'); ?>
</body>
</html>
<?php /**PATH D:\Projects\Natanem\resources\views/layouts/app.blade.php ENDPATH**/ ?>