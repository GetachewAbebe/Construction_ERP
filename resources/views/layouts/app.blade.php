<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>@yield('title','Natanem Engineering')</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">

  {{-- Fonts --}}
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

  @vite(['resources/sass/app.scss','resources/js/app.js'])

  <style>
      :root {
          --erp-primary: #059669; /* Emerald 600 */
          --erp-deep:    #064e3b; /* Emerald 900 */
          --erp-accent:  #f59e0b; /* Amber 500 */
          --erp-glass-white: rgba(255, 255, 255, 0.7);
          --erp-glass-dark:  rgba(2, 44, 34, 0.8);
          --erp-border:  rgba(255, 255, 255, 0.15);
          --sidebar-width: 280px;
          --sidebar-collapsed-width: 80px;
          
          --gradient-mesh: radial-gradient(at 0% 0%, rgba(5, 150, 105, 0.12) 0px, transparent 50%),
                           radial-gradient(at 50% 0%, rgba(245, 158, 11, 0.04) 0px, transparent 50%),
                           radial-gradient(at 100% 0%, rgba(5, 150, 105, 0.12) 0px, transparent 50%);
          
          --gradient-primary: linear-gradient(135deg, #059669 0%, #10b981 100%);
          --gradient-success: linear-gradient(135deg, #059669 0%, #10b981 100%);
          --gradient-warning: linear-gradient(135deg, #f59e0b 0%, #fbbf24 100%);
          --gradient-danger:  linear-gradient(135deg, #dc2626 0%, #ef4444 100%);
      }

      body {
          font-family: 'Outfit', sans-serif;
          background: #f8fafc;
          background-image: var(--gradient-mesh);
          background-attachment: fixed;
          color: #1e293b;
          min-height: 100vh;
          overflow-x: hidden;
      }

      /* Premium Hardened Glass utility */
      .hardened-glass, .hardened-glass-static, .glass-card-global {
          background: var(--erp-glass-white);
          backdrop-filter: blur(12px) saturate(180%);
          -webkit-backdrop-filter: blur(12px) saturate(180%);
          border: 1px solid var(--erp-border);
          border-radius: 28px;
          padding: 2rem;
          box-shadow: 0 10px 32px 0 rgba(31, 38, 135, 0.07);
          transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
      }

      .hardened-glass:hover, .glass-card-global:hover {
          transform: translateY(-12px) scale(1.01);
          box-shadow: 0 25px 50px rgba(0,0,0,0.1);
          border-color: var(--erp-primary);
      }

      .metric-icon {
          width: 64px;
          height: 64px;
          border-radius: 20px;
          display: flex;
          align-items: center;
          justify-content: center;
          background: var(--erp-primary);
          color: white;
          box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1);
          flex-shrink: 0;
      }

      /* Entrance Animations */
      @keyframes slideUpFade {
          from { opacity: 0; transform: translateY(30px); }
          to { opacity: 1; transform: translateY(0); }
      }

      .stagger-entrance {
          opacity: 0;
          animation: slideUpFade 0.6s cubic-bezier(0.23, 1, 0.32, 1) forwards;
      }

      /* Pulse Indicator */
      @keyframes pulse-emerald {
          0% { box-shadow: 0 0 0 0 rgba(5, 150, 105, 0.4); }
          70% { box-shadow: 0 0 0 10px rgba(5, 150, 105, 0); }
          100% { box-shadow: 0 0 0 0 rgba(5, 150, 105, 0); }
      }

      .pulse-dot-emerald {
          width: 8px;
          height: 8px;
          background: #10b981;
          border-radius: 50%;
          display: inline-block;
          margin-right: 8px;
          animation: pulse-emerald 2s infinite;
      }

      /* Command Palette Styles */
      .command-palette-overlay {
          position: fixed;
          top: 0;
          left: 0;
          width: 100%;
          height: 100%;
          background: rgba(0, 0, 0, 0.4);
          backdrop-filter: blur(8px);
          z-index: 2000;
          display: none;
          align-items: center;
          justify-content: center;
          padding: 2rem;
      }

      .command-palette-modal {
          width: 100%;
          max-width: 650px;
          background: rgba(255, 255, 255, 0.85);
          backdrop-filter: blur(20px);
          border: 1px solid rgba(255, 255, 255, 0.3);
          border-radius: 24px;
          box-shadow: 0 30px 60px rgba(0,0,0,0.2);
          overflow: hidden;
          animation: slideUpFade 0.3s ease-out;
      }

      .command-input {
          width: 100%;
          padding: 1.5rem 2rem;
          border: none;
          background: transparent;
          font-size: 1.25rem;
          color: var(--erp-deep);
          outline: none;
          border-bottom: 1px solid rgba(0,0,0,0.05);
      }

      /* Sidebar Navigation */
      .app-sidebar {
          width: var(--sidebar-width);
          height: 100vh;
          position: fixed;
          top: 0;
          left: 0;
          z-index: 1050;
          transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
          padding: 1.5rem;
          display: flex;
          flex-direction: column;
          background: var(--erp-glass-dark);
          backdrop-filter: blur(20px);
          border-right: 1px solid rgba(255, 255, 255, 0.1);
          color: white;
          overflow-y: auto;
          overflow-x: hidden;
      }

      /* Custom Scrollbar */
      .app-sidebar::-webkit-scrollbar {
          width: 5px;
      }
      .app-sidebar::-webkit-scrollbar-track {
          background: transparent;
      }
      .app-sidebar::-webkit-scrollbar-thumb {
          background: rgba(255, 255, 255, 0.2);
          border-radius: 10px;
      }
      .app-sidebar::-webkit-scrollbar-thumb:hover {
          background: rgba(255, 255, 255, 0.4);
      }

      /* Sidebar Footer Premium Card */
      .sidebar-footer {
          background: rgba(0, 0, 0, 0.2);
          border-radius: 16px;
          padding: 1.25rem;
          margin-top: auto;
          border: 1px solid rgba(255, 255, 255, 0.05);
          backdrop-filter: blur(10px);
          transition: transform 0.3s ease;
      }
      .sidebar-footer:hover {
          background: rgba(0, 0, 0, 0.3);
          transform: translateY(-2px);
          border-color: rgba(255, 255, 255, 0.1);
      }

      .main-content {
          margin-left: var(--sidebar-width);
          transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
          padding: 2rem;
          min-height: 100vh;
      }

      /* Sidebar Link Base */
      .sidebar-link {
          display: flex;
          align-items: center;
          padding: 0.85rem 1rem;
          margin-bottom: 0.35rem;
          color: rgba(255, 255, 255, 0.7);
          text-decoration: none;
          border-radius: 12px;
          transition: all 0.3s cubic-bezier(0.25, 0.8, 0.25, 1);
          font-weight: 500;
          letter-spacing: 0.01em;
          border: 1px solid transparent;
          position: relative;
          overflow: hidden;
      }

      /* Hover State */
      .sidebar-link:hover {
          background: rgba(255, 255, 255, 0.08);
          color: white;
          padding-left: 1.25rem;
          box-shadow: 0 4px 12px rgba(0,0,0,0.05);
      }

      /* Active State - "The Glow" */
      .sidebar-link.active {
          background: linear-gradient(90deg, rgba(5, 150, 105, 0.15) 0%, rgba(5, 150, 105, 0.05) 100%);
          color: white;
          border: 1px solid rgba(5, 150, 105, 0.3);
          box-shadow: 0 4px 20px rgba(5, 150, 105, 0.15);
          backdrop-filter: blur(12px);
      }

      /* Active Border Indicator */
      .sidebar-link.active::before {
          content: "";
          position: absolute;
          left: 0;
          top: 0;
          height: 100%;
          width: 4px;
          background: var(--erp-primary);
          border-radius: 0 4px 4px 0;
          box-shadow: 2px 0 8px rgba(5, 150, 105, 0.6);
      }

      /* Icons */
      .sidebar-link i {
          font-size: 1.2rem;
          margin-right: 0.85rem;
          width: 24px;
          text-align: center;
          transition: transform 0.3s ease, filter 0.3s ease;
          opacity: 0.8;
      }

      .sidebar-link:hover i {
          transform: scale(1.1);
          opacity: 1;
      }

      .sidebar-link.active i {
          color: #34d399; /* Brighter Emerald */
          transform: scale(1.15);
          filter: drop-shadow(0 0 5px rgba(52, 211, 153, 0.4)); /* Icon Glow */
          opacity: 1;
      }

      /* Sidebar Nested Groups (Submenus) */
      .sidebar-nested-group {
          position: relative;
          margin-top: 0.25rem;
      }

      /* Improved Connector Line */
      .sidebar-nested-group .ps-2.border-start {
          border-left: 1px solid rgba(255, 255, 255, 0.1) !important; /* Thinner, solid line */
          margin-left: 1.6rem !important; /* Align with parent text */
          padding-left: 1rem !important;
      }

      /* Sidebar Sublinks */
      .sidebar-sublink {
          display: flex;
          align-items: center;
          padding: 0.5rem 0.75rem;
          color: rgba(255, 255, 255, 0.5);
          text-decoration: none;
          font-size: 0.85rem;
          transition: all 0.2s ease;
          border-radius: 8px;
          margin-bottom: 2px;
      }

      /* Sublink Bullets */
      .sidebar-sublink::before {
          content: "";
          width: 6px;
          height: 6px;
          background: rgba(255,255,255,0.2);
          border-radius: 50%;
          margin-right: 10px;
          transition: all 0.2s ease;
      }

      .sidebar-sublink:hover {
          color: white;
          background: rgba(255, 255, 255, 0.03);
          transform: translateX(3px);
      }

      .sidebar-sublink:hover::before {
          background: var(--erp-accent);
          box-shadow: 0 0 6px var(--erp-accent);
      }

      .sidebar-sublink.text-white.fw-bold {
          color: white !important;
          background: rgba(255, 255, 255, 0.08);
          font-weight: 600 !important;
      }
      
      .sidebar-sublink.text-white.fw-bold::before {
          background: var(--erp-primary);
          box-shadow: 0 0 8px var(--erp-primary);
      }
      
      /* Section Headers */
      .text-uppercase.x-small {
          letter-spacing: 0.08em;
          font-weight: 700;
          opacity: 0.4;
          font-size: 0.65rem;
      }
      /* Logo Area */
      .sidebar-brand {
          padding: 1rem 0.5rem 2.5rem;
          display: flex;
          align-items: center;
          gap: 0.75rem;
      }

      .sidebar-brand .dot {
          width: 12px;
          height: 12px;
          background: var(--erp-accent);
          border-radius: 50%;
          box-shadow: 0 0 15px var(--erp-accent);
      }
      
      .sidebar-brand h4 {
          background: linear-gradient(135deg, #ffffff 0%, rgba(255,255,255,0.8) 100%);
          -webkit-background-clip: text;
          -webkit-text-fill-color: transparent;
          text-shadow: 0 4px 12px rgba(0,0,0,0.1);
          font-weight: 800;
      }

      .text-erp-deep {
          color: var(--erp-deep);
      }

      .shadow-soft {
          box-shadow: 0 8px 32px rgba(31, 38, 135, 0.07);
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

      .hover-bg-light:hover {
          background: rgba(0,0,0,0.04);
          transform: translateX(5px);
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

      .avatar-circle {
          width: 40px;
          height: 40px;
          background: rgba(255, 255, 255, 0.2);
          border-radius: 12px;
          display: flex;
          align-items: center;
          justify-content: center;
          font-weight: 800;
          color: white;
          text-transform: uppercase;
          border: 1px solid rgba(255, 255, 255, 0.2);
      }

      .x-small { font-size: 0.7rem; }
      .border-white-10 { border-color: rgba(255, 255, 255, 0.1) !important; }
      .tracking-tight { letter-spacing: -0.05em; }

      /* Responsive Adjustments */
      @media (max-width: 991.98px) {
          .app-sidebar {
              transform: translateX(-100%);
          }
          .main-content {
              margin-left: 0;
          }
      }

      /* Glass Cards for general use */
      .glass-card-global {
          background: var(--erp-glass);
          backdrop-filter: blur(12px);
          border: 1px solid var(--erp-glass-border);
          border-radius: 20px;
          box-shadow: 0 8px 32px rgba(0, 0, 0, 0.05);
      }

      /* -----------------------------
         Dropdown submenu (Bootstrap 5)
         ----------------------------- */
      .dropdown-menu {
          border-radius: 16px;
          background: rgba(255, 255, 255, 0.98);
          backdrop-filter: blur(10px);
          box-shadow: 0 12px 40px rgba(0,0,0,.15);
          border: 1px solid rgba(255, 255, 255, 0.5);
          padding: 0.75rem;
      }

      .dropdown-item {
          font-size: .85rem;
          font-weight: 500;
          border-radius: 8px;
          padding: 0.6rem 1rem;
          transition: all 0.2s ease;
      }

      .dropdown-item:hover {
          background: rgba(245, 158, 11, 0.1);
          color: #f59e0b;
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

  {{-- ApexCharts --}}
  <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>

  @stack('head')
</head>
<body class="bg-light">

{{-- Global Command Palette --}}
<div id="commandPalette" class="command-palette-overlay">
    <div class="command-palette-modal">
        <input type="text" class="command-input" placeholder="Jump to anywhere... (try 'Inventory', 'HR', or 'New Employee')" id="cmdInput">
        <div id="cmdResults" class="p-2" style="max-height: 400px; overflow-y: auto;">
            <!-- Results will be injected here -->
        </div>
        <div class="p-3 bg-light border-top d-flex justify-content-between align-items-center x-small text-muted">
            <span>↑↓ to navigate • ↵ to select</span>
            <span>ESC to close</span>
        </div>
    </div>
</div>

@auth
  {{-- Premium App Sidebar --}}
  <div class="app-sidebar">
    <div class="sidebar-brand">
       <div class="dot"></div>
       <h4 class="fw-800 mb-0 tracking-tight">NATANEM</h4>
       <div class="ms-auto" title="System Online">
           <div class="pulse-dot-emerald"></div>
       </div>
    </div>

    <div class="sidebar-nav flex-grow-1">
      <a href="{{ route('home') }}" class="sidebar-link {{ request()->routeIs('home') ? 'active' : '' }}">
        <i class="bi bi-speedometer2"></i>
        <span>Overview</span>
      </a>

      @can('Administrator')
      <div class="sidebar-item">
          <a class="sidebar-link {{ request()->segment(1) == 'admin' ? 'active' : '' }}" 
             data-bs-toggle="collapse" href="#adminMenu" role="button" aria-expanded="{{ request()->segment(1) == 'admin' ? 'true' : 'false' }}">
              <i class="bi bi-shield-lock"></i>
              <span>Admin Dashboard</span>
              <i class="bi bi-chevron-down ms-auto x-small opacity-50"></i>
          </a>
          <div class="collapse {{ request()->segment(1) == 'admin' ? 'show' : '' }}" id="adminMenu">
              <div class="vstack gap-1 ps-4 border-start border-white-10 ms-3 my-2">
                  <a href="{{ route('admin.dashboard') }}" class="sidebar-sublink {{ request()->routeIs('admin.dashboard') ? 'text-white fw-bold' : '' }}">Dashboard</a>
                  
                  <h6 class="text-uppercase x-small text-white-50 mt-3 mb-1">Modules</h6>
                  
                  {{-- Admin: Human Resources --}}
                  <div class="sidebar-nested-group mb-2">
                      <a href="{{ route('admin.hr') }}" class="text-white-50 text-decoration-none fw-bold small d-block mb-1 hover-white">Human Resources</a>
                      <div class="ps-2 border-start border-white-10 ms-1">
                          <a href="{{ route('admin.hr.employees.index') }}" class="sidebar-sublink small py-1 {{ request()->routeIs('admin.hr.employees.*') ? 'text-white' : '' }}">Employees</a>
                          <a href="{{ route('admin.hr.leaves.index') }}" class="sidebar-sublink small py-1 {{ request()->routeIs('admin.hr.leaves.*') ? 'text-white' : '' }}">Leavess</a>
                          <a href="{{ route('admin.hr.attendance.index') }}" class="sidebar-sublink small py-1 {{ request()->routeIs('admin.hr.attendance.*') ? 'text-white' : '' }}">Attendance</a>
                      </div>
                  </div>

                  {{-- Admin: Inventory --}}
                  <div class="sidebar-nested-group mb-2">
                      <a href="{{ route('admin.inventory') }}" class="text-white-50 text-decoration-none fw-bold small d-block mb-1 hover-white">Inventory</a>
                      <div class="ps-2 border-start border-white-10 ms-1">
                          <a href="{{ route('admin.inventory.items.index') }}" class="sidebar-sublink small py-1 {{ request()->routeIs('admin.inventory.items.*') ? 'text-white' : '' }}">Items</a>
                          <a href="{{ route('admin.inventory.loans.index') }}" class="sidebar-sublink small py-1 {{ request()->routeIs('admin.inventory.loans.*') ? 'text-white' : '' }}">Loans</a>
                          <a href="{{ route('admin.inventory.logs.index') }}" class="sidebar-sublink small py-1 {{ request()->routeIs('admin.inventory.logs.*') ? 'text-white' : '' }}">Logs</a>
                      </div>
                  </div>

                  {{-- Admin: Finance --}}
                  <div class="sidebar-nested-group mb-2">
                      <a href="{{ route('admin.finance') }}" class="text-white-50 text-decoration-none fw-bold small d-block mb-1 hover-white">Finance</a>
                      <div class="ps-2 border-start border-white-10 ms-1">
                          <a href="{{ route('admin.finance.projects.index') }}" class="sidebar-sublink small py-1 {{ request()->routeIs('admin.finance.projects.*') ? 'text-white' : '' }}">Projects</a>
                          <a href="{{ route('admin.finance.expenses.index') }}" class="sidebar-sublink small py-1 {{ request()->routeIs('admin.finance.expenses.*') ? 'text-white' : '' }}">Expenses</a>
                      </div>
                  </div>

                  <h6 class="text-uppercase x-small text-white-50 mt-3 mb-1">System</h6>
                  <a href="{{ route('admin.users.index') }}" class="sidebar-sublink {{ request()->routeIs('admin.users.*') ? 'text-white fw-bold' : '' }}">System Users</a>
                  <a href="{{ route('admin.requests.leave') }}" class="sidebar-sublink {{ request()->routeIs('admin.requests.leave') ? 'text-white fw-bold' : '' }}">Approvals</a>
                  <a href="{{ route('admin.attendance-settings.index') }}" class="sidebar-sublink {{ request()->routeIs('admin.attendance-settings.*') ? 'text-white fw-bold' : '' }}">Config</a>
              </div>
          </div>
      </div>
      @endcan

      @if(Auth::user()->hasRole('HumanResourceManager') && !Auth::user()->hasRole('Administrator'))
      <div class="sidebar-item">
          <a class="sidebar-link {{ request()->segment(1) == 'hr' ? 'active' : '' }}" 
             data-bs-toggle="collapse" href="#hrMenu" role="button" aria-expanded="{{ request()->segment(1) == 'hr' ? 'true' : 'false' }}">
              <i class="bi bi-people"></i>
              <span>Human Resource</span>
              <i class="bi bi-chevron-down ms-auto x-small opacity-50"></i>
          </a>
          <div class="collapse {{ request()->segment(1) == 'hr' ? 'show' : '' }}" id="hrMenu">
              <div class="vstack gap-1 ps-4 border-start border-white-10 ms-3 my-2">
                  <a href="{{ route('hr.dashboard') }}" class="sidebar-sublink {{ request()->routeIs('hr.dashboard') ? 'text-white fw-bold' : '' }}">Dashboard</a>
                  <a href="{{ route('hr.employees.index') }}" class="sidebar-sublink {{ request()->routeIs('hr.employees.*') ? 'text-white fw-bold' : '' }}">Employees</a>
                  <a href="{{ route('hr.leaves.index') }}" class="sidebar-sublink {{ request()->routeIs('hr.leaves.index') ? 'text-white fw-bold' : '' }}">Leave Requests</a>
                  <a href="{{ route('hr.leaves.approved') }}" class="sidebar-sublink {{ request()->routeIs('hr.leaves.approved') ? 'text-white fw-bold' : '' }}">Approved Leaves</a>
                  <a href="{{ route('hr.attendance.index') }}" class="sidebar-sublink {{ request()->routeIs('hr.attendance.*') ? 'text-white fw-bold' : '' }}">Attendance</a>
              </div>
          </div>
      </div>
      @endif

      @if(Auth::user()->hasRole('InventoryManager') && !Auth::user()->hasRole('Administrator'))
      <div class="sidebar-item">
          <a class="sidebar-link {{ request()->segment(1) == 'inventory' ? 'active' : '' }}" 
             data-bs-toggle="collapse" href="#invMenu" role="button" aria-expanded="{{ request()->segment(1) == 'inventory' ? 'true' : 'false' }}">
              <i class="bi bi-boxes"></i>
              <span>Inventory</span>
              <i class="bi bi-chevron-down ms-auto x-small opacity-50"></i>
          </a>
          <div class="collapse {{ request()->segment(1) == 'inventory' ? 'show' : '' }}" id="invMenu">
              <div class="vstack gap-1 ps-4 border-start border-white-10 ms-3 my-2">
                  <a href="{{ route('inventory.dashboard') }}" class="sidebar-sublink {{ request()->routeIs('inventory.dashboard') ? 'text-white fw-bold' : '' }}">Dashboard</a>
                  <a href="{{ route('inventory.items.index') }}" class="sidebar-sublink {{ request()->routeIs('inventory.items.*') ? 'text-white fw-bold' : '' }}">Items Catalogue</a>
                  <a href="{{ route('inventory.loans.index') }}" class="sidebar-sublink {{ request()->routeIs('inventory.loans.*') ? 'text-white fw-bold' : '' }}">Item Lending</a>
                  <a href="{{ route('inventory.logs.index') }}" class="sidebar-sublink {{ request()->routeIs('inventory.logs.*') ? 'text-white fw-bold' : '' }}">Audit Logs</a>
              </div>
          </div>
      </div>
      @endif

      @if(Auth::user()->hasRole('FinancialManager') && !Auth::user()->hasRole('Administrator'))
      <div class="sidebar-item">
          <a class="sidebar-link {{ request()->segment(1) == 'finance' ? 'active' : '' }}" 
             data-bs-toggle="collapse" href="#finMenu" role="button" aria-expanded="{{ request()->segment(1) == 'finance' ? 'true' : 'false' }}">
              <i class="bi bi-currency-dollar"></i>
              <span>Finance</span>
              <i class="bi bi-chevron-down ms-auto x-small opacity-50"></i>
          </a>
          <div class="collapse {{ request()->segment(1) == 'finance' ? 'show' : '' }}" id="finMenu">
              <div class="vstack gap-1 ps-4 border-start border-white-10 ms-3 my-2">
                  <a href="{{ route('finance.dashboard') }}" class="sidebar-sublink {{ request()->routeIs('finance.dashboard') ? 'text-white fw-bold' : '' }}">Dashboard</a>
                  <a href="{{ route('finance.projects.index') }}" class="sidebar-sublink {{ request()->routeIs('finance.projects.*') ? 'text-white fw-bold' : '' }}">Projects</a>
                  <a href="{{ route('finance.expenses.index') }}" class="sidebar-sublink {{ request()->routeIs('finance.expenses.*') ? 'text-white fw-bold' : '' }}">Expenses</a>
              </div>
          </div>
      </div>
      @endif
    </div>

    <div class="sidebar-footer">
      <div class="d-flex align-items-center gap-3 px-2 mb-3">
        <div class="avatar-circle">
          {{ substr(Auth::user()->name, 0, 1) }}
        </div>
        <div class="overflow-hidden">
          <div class="text-white fw-bold text-truncate small">{{ Auth::user()->name }}</div>
          <div class="text-white-50 x-small text-truncate text-capitalize">{{ Auth::user()->roles->first()->name ?? 'User' }}</div>
        </div>
      </div>
      <form action="{{ route('logout') }}" method="POST">
        @csrf
        <button type="submit" class="sidebar-link w-100 border-0 bg-transparent text-start">
          <i class="bi bi-box-arrow-right"></i>
          <span>Sign Out</span>
        </button>
      </form>
    </div>
  </div>
@endauth

<main class="{{ Auth::check() ? 'main-content' : 'min-vh-100' }}">
  <div class="{{ Auth::check() ? 'container-fluid' : '' }}">
      @yield('content')
  </div>

  @guest
    <footer class="footer-gradient text-white mt-auto py-2 fixed-bottom">
      <div class="container d-flex flex-column align-items-center justify-content-center text-center small">
        <div>© {{ date('Y') }} Natanem Engineering</div>
      </div>
    </footer>
  @endguest
</main>

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

    // 3D Tilt Interaction for hardened-glass cards
    document.addEventListener('mousemove', function(e) {
      const cards = document.querySelectorAll('.glass-metric, .hardened-glass');
      cards.forEach(card => {
        const rect = card.getBoundingClientRect();
        const x = e.clientX - rect.left;
        const y = e.clientY - rect.top;
        
        if (x > 0 && x < rect.width && y > 0 && y < rect.height) {
          const centerX = rect.width / 2;
          const centerY = rect.height / 2;
          const rotateX = (y - centerY) / 10;
          const rotateY = (centerX - x) / 10;
          
          card.style.transform = `perspective(1000px) rotateX(${rotateX}deg) rotateY(${rotateY}deg) translateY(-10px) scale(1.02)`;
        } else {
          card.style.transform = '';
        }
      });
    });
    // Command Palette Trigger
    const paletteLinks = [
        { name: 'Dashboard Overview', url: '{{ route("home") }}', icon: 'bi-speedometer2' },
        { name: 'Human Resource Hub', url: '{{ route("hr.dashboard") }}', icon: 'bi-people' },
        { name: 'Inventory Manager', url: '{{ route("inventory.dashboard") }}', icon: 'bi-boxes' },
        { name: 'Finance Center', url: '{{ route("finance.dashboard") }}', icon: 'bi-currency-dollar' },
        { name: 'Admin Console', url: '{{ route("admin.dashboard") }}', icon: 'bi-shield-lock' },
        { name: 'New Employee Onboarding', url: '{{ route("hr.employees.create") }}', icon: 'bi-person-plus' },
        { name: 'System Users', url: '{{ route("admin.users.index") }}', icon: 'bi-person-gear' }
    ];

    const cmdInput = document.getElementById('cmdInput');
    const cmdResults = document.getElementById('cmdResults');

    function renderResults(filter = '') {
        const filtered = paletteLinks.filter(l => l.name.toLowerCase().includes(filter.toLowerCase()));
        cmdResults.innerHTML = filtered.map(l => `
            <a href="${l.url}" class="d-flex align-items-center gap-3 p-3 text-decoration-none text-erp-deep rounded-3 hover-bg-light transition-all">
                <i class="bi ${l.icon} fs-5 opacity-75"></i>
                <span class="fw-bold">${l.name}</span>
            </a>
        `).join('');
    }

    document.addEventListener('keydown', function(e) {
        if (e.ctrlKey && e.key === 'k') {
            e.preventDefault();
            const palette = document.getElementById('commandPalette');
            palette.style.display = 'flex';
            cmdInput.focus();
            renderResults();
        }
        if (e.key === 'Escape') {
            document.getElementById('commandPalette').style.display = 'none';
        }
    });

    cmdInput.addEventListener('input', (e) => renderResults(e.target.value));

    // Close on overlay click
    document.getElementById('commandPalette').addEventListener('click', function(e) {
        if (e.target === this) {
            this.style.display = 'none';
        }
    });

    // Stagger entrance initialization
    document.addEventListener('DOMContentLoaded', function() {
        const elements = document.querySelectorAll('.stagger-entrance');
        elements.forEach((el, index) => {
            el.style.animationDelay = `${(index + 1) * 0.1}s`;
        });
    });
  </script>

  @stack('scripts')
</body>
</html>
