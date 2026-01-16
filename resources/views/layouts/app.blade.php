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

      .btn-emerald {
          background: #10b981;
          color: white;
          border: none;
          transition: all 0.2s ease;
      }
      .btn-emerald:hover {
          background: #059669;
          color: white;
          transform: translateY(-1px);
          box-shadow: 0 4px 12px rgba(16, 185, 129, 0.3);
      }

      .btn-erp-deep {
          background: var(--erp-deep) !important;
          color: #ffffff !important;
          border: none !important;
          transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1) !important;
          box-shadow: 0 4px 10px rgba(6, 78, 59, 0.15);
      }
      .btn-erp-deep:hover {
          background: var(--erp-primary) !important;
          color: #ffffff !important;
          transform: translateY(-2px);
          box-shadow: 0 8px 15px rgba(6, 78, 59, 0.2);
      }

      .bg-black-20 { background: rgba(0,0,0,0.2); }
      .border-white-10 { border-color: rgba(255, 255, 255, 0.1) !important; }

      .hardened-glass:hover, .glass-card-global:hover {
          transform: translateY(-5px);
          box-shadow: 0 25px 50px rgba(0,0,0,0.1);
          border-color: var(--erp-primary);
      }

      /* High Contrast Button for Glass Backgrounds */
      .btn-white {
          background: #ffffff !important;
          color: var(--erp-deep) !important;
          border: 1px solid rgba(0,0,0,0.1) !important;
          box-shadow: 0 2px 5px rgba(0,0,0,0.05);
          transition: all 0.2s ease;
      }
      .btn-white:hover {
          background: #f8fafc !important;
          color: var(--erp-primary) !important;
          border-color: var(--erp-primary) !important;
          transform: translateY(-1px);
          box-shadow: 0 4px 10px rgba(0,0,0,0.08);
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

       /* Priority Labels */
       .priority-stripe {
           position: absolute;
           left: 0;
           top: 0;
           bottom: 0;
           width: 4px;
           border-radius: 4px 0 0 4px;
       }
       .priority-high { background: #f43f5e; box-shadow: 0 0 10px rgba(244, 63, 94, 0.4); }
       .priority-medium { background: #3b82f6; }
       
       .btn-outline-danger:hover {
           background-color: #f43f5e;
           border-color: #f43f5e;
           color: white;
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

      .user-profile-trigger:hover {
          background: rgba(255, 255, 255, 0.08) !important;
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
          border-radius: 50%;
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
          color: var(--erp-deep) !important; /* Force dark text */
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

      /* Notification System Styles */
      .notification-dropdown {
          width: 380px;
          border: 1px solid rgba(255,255,255,0.15);
          background: rgba(15, 23, 42, 0.95);
          backdrop-filter: blur(25px);
          border-radius: 24px !important;
          padding: 0 !important;
          overflow: hidden;
          box-shadow: 0 50px 100px -20px rgba(0,0,0,0.5);
          margin-bottom: 0.5rem !important;
      }
      .notification-header {
          padding: 1.25rem;
          border-bottom: 1px solid rgba(255,255,255,0.08);
          background: rgba(255,255,255,0.03);
      }
      .notification-item {
          padding: 1rem 1.25rem;
          border-bottom: 1px solid rgba(255,255,255,0.05);
          transition: all 0.2s ease;
          position: relative;
          color: rgba(255,255,255,0.85);
          text-decoration: none;
          display: block;
      }
      .notification-item:hover {
          background: rgba(255,255,255,0.05);
          color: white;
      }
      .notification-item.unread::before {
          content: "";
          position: absolute;
          left: 8px;
          top: 50%;
          transform: translateY(-50%);
          width: 6px;
          height: 6px;
          background: #10b981;
          border-radius: 50%;
          box-shadow: 0 0 10px #10b981;
      }
      .notification-icon {
          width: 36px;
          height: 36px;
          border-radius: 10px;
          display: flex;
          align-items: center;
          justify-content: center;
          font-size: 1.1rem;
          flex-shrink: 0;
      }
      .badge-notify-count {
          position: absolute;
          top: -2px;
          right: -2px;
          background: #ef4444;
          color: white;
          font-size: 0.65rem;
          padding: 2px 6px;
          border-radius: 999px;
          border: 2px solid #0f172a;
          font-weight: 800;
      }
      .notification-list {
          max-height: 400px;
          overflow-y: auto;
      }
      .notification-list::-webkit-scrollbar { width: 4px; }
      .notification-list::-webkit-scrollbar-thumb { background: rgba(255,255,255,0.1); border-radius: 10px; }
  </style>

  {{-- ApexCharts --}}
  <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>

  @stack('head')
</head>
<body class="bg-light">

{{-- Premium Toast Notifications --}}
<div class="toast-container position-fixed bottom-0 end-0 p-4" style="z-index: 9999;">
    <div id="erpToast" class="toast hardened-glass border-0" role="alert" aria-live="assertive" aria-atomic="true" style="background: rgba(255,255,255,0.9); backdrop-filter: blur(20px);">
        <div class="d-flex align-items-center p-3">
            <div id="toastIconContainer" class="me-3 rounded-circle d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                <i id="toastIcon" class="bi fs-5"></i>
            </div>
            <div class="flex-grow-1">
                <h6 id="toastTitle" class="mb-0 fw-800 text-erp-deep">System Notification</h6>
                <p id="toastMessage" class="mb-0 small text-muted"></p>
            </div>
            <button type="button" class="btn-close ms-2" data-bs-dismiss="toast" aria-label="Close"></button>
        </div>
    </div>
</div>

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
       <a href="{{ route('home') }}" class="d-flex align-items-center gap-2 w-100 text-decoration-none text-white p-0 m-0">
           <div class="dot flex-shrink-0"></div>
           <h5 class="fw-800 mb-0 tracking-tight text-nowrap">
               NATANEM
                @php
                    $roleName = '';
                    if(Auth::user()->hasRole('Administrator') || Auth::user()->hasRole('Admin')) {
                        $roleName = 'Admin';
                    } elseif(Auth::user()->hasRole('Human Resource Manager') || Auth::user()->hasRole('HumanResourceManager')) {
                        $roleName = 'Human Resource';
                    } elseif(Auth::user()->hasRole('Inventory Manager') || Auth::user()->hasRole('InventoryManager')) {
                        $roleName = 'Inventory';
                    } elseif(Auth::user()->hasRole('Financial Manager') || Auth::user()->hasRole('FinancialManager')) {
                        $roleName = 'Finance';
                    }
                @endphp
               @if($roleName)
                   <span class="fw-medium opacity-75 fs-6">- {{ $roleName }}</span>
               @endif
           </h5>
           <div class="ms-auto" title="System Online">
               <div class="pulse-dot-emerald"></div>
           </div>
       </a>
    </div>

    <div class="sidebar-nav flex-grow-1 pt-2">
      @php
          $unreadNotifications = Auth::user()->unreadNotifications;
          $totalUnread = $unreadNotifications->count();
          $hrCount     = Auth::user()->getUnreadCountByModule('hr');
          $invCount    = Auth::user()->getUnreadCountByModule('inventory');
          $finCount    = Auth::user()->getUnreadCountByModule('finance');

          // Granular counts for sub-menus
          $leaveReqCount   = $unreadNotifications->filter(fn($n) => ($n->data['type'] ?? '') === 'leave_request')->count();
          $inventoryReqCount = $unreadNotifications->filter(fn($n) => ($n->data['type'] ?? '') === 'inventory_request')->count();
          $expenseReqCount = $unreadNotifications->filter(fn($n) => ($n->data['type'] ?? '') === 'expense_request')->count();
      @endphp


      @if(Auth::user()->hasRole('Administrator') || Auth::user()->hasRole('Admin'))
        {{-- =========================================
             ADMINISTRATOR VIEW (Flat Structure)
             ========================================= --}}
        
        <a href="{{ route('admin.dashboard') }}" class="sidebar-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
            <i class="bi bi-speedometer2"></i> <span>Dashboard</span>
        </a>
        
        {{-- HR MODULE --}}
        <a class="sidebar-link {{ request()->routeIs('admin.hr*') ? 'active' : '' }}" data-bs-toggle="collapse" href="#adminHrMenu" role="button" aria-expanded="{{ request()->routeIs('admin.hr*') ? 'true' : 'false' }}">
             <i class="bi bi-people"></i> <span>Human Resources</span>
             @if($hrCount > 0)
                 <span class="badge bg-danger rounded-pill x-small ms-auto">{{ $hrCount }}</span>
             @else
                 <i class="bi bi-chevron-down ms-auto x-small opacity-50"></i>
             @endif
        </a>
        <div class="collapse {{ request()->routeIs('admin.hr*') ? 'show' : '' }}" id="adminHrMenu">
            <div class="ms-1 ps-3 border-start border-white-10 mb-2">
                <a href="{{ route('admin.hr') }}" class="sidebar-sublink {{ request()->routeIs('admin.hr') ? 'text-white fw-bold' : '' }}">Dashboard</a>
                <a href="{{ route('admin.hr.employees.index') }}" class="sidebar-sublink {{ request()->routeIs('admin.hr.employees.*') ? 'text-white fw-bold' : '' }}">Employees</a>
                <a href="{{ route('admin.hr.leaves.index') }}" class="sidebar-sublink d-flex align-items-center justify-content-between {{ request()->routeIs('admin.hr.leaves.*') ? 'text-white fw-bold' : '' }}">
                    <span>Leave Management</span>
                    @if($leaveReqCount > 0)
                        <span class="badge bg-danger rounded-pill x-small">{{ $leaveReqCount }}</span>
                    @endif
                </a>
                <a href="{{ route('admin.hr.attendance.index') }}" class="sidebar-sublink {{ request()->routeIs('admin.hr.attendance.*') ? 'text-white fw-bold' : '' }}">Attendance</a>
            </div>
        </div>

        {{-- INVENTORY MODULE --}}
        <a class="sidebar-link {{ request()->routeIs('admin.inventory*') ? 'active' : '' }}" data-bs-toggle="collapse" href="#adminInvMenu" role="button" aria-expanded="{{ request()->routeIs('admin.inventory*') ? 'true' : 'false' }}">
             <i class="bi bi-boxes"></i> <span>Inventory</span>
             @if($invCount > 0)
                 <span class="badge bg-danger rounded-pill x-small ms-auto">{{ $invCount }}</span>
             @else
                 <i class="bi bi-chevron-down ms-auto x-small opacity-50"></i>
             @endif
        </a>
        <div class="collapse {{ request()->routeIs('admin.inventory*') ? 'show' : '' }}" id="adminInvMenu">
            <div class="ms-1 ps-3 border-start border-white-10 mb-2">
                 <a href="{{ route('admin.inventory') }}" class="sidebar-sublink {{ request()->routeIs('admin.inventory') ? 'text-white fw-bold' : '' }}">Dashboard</a>
                 <a href="{{ route('admin.inventory.items.index') }}" class="sidebar-sublink {{ request()->routeIs('admin.inventory.items.*') ? 'text-white fw-bold' : '' }}">Items Catalogue</a>
                 <a href="{{ route('admin.inventory.loans.index') }}" class="sidebar-sublink d-flex align-items-center justify-content-between {{ request()->routeIs('admin.inventory.loans.*') ? 'text-white fw-bold' : '' }}">
                     <span>Lending Requests</span>
                     @if($inventoryReqCount > 0)
                        <span class="badge bg-danger rounded-pill x-small">{{ $inventoryReqCount }}</span>
                     @endif
                 </a>
                 <a href="{{ route('admin.inventory.logs.index') }}" class="sidebar-sublink {{ request()->routeIs('admin.inventory.logs.*') ? 'text-white fw-bold' : '' }}">Audit Logs</a>
            </div>
        </div>

        {{-- FINANCE MODULE --}}
        <a class="sidebar-link {{ request()->routeIs('admin.finance*') ? 'active' : '' }}" data-bs-toggle="collapse" href="#adminFinMenu" role="button" aria-expanded="{{ request()->routeIs('admin.finance*') ? 'true' : 'false' }}">
             <i class="bi bi-currency-dollar"></i> <span>Finance</span>
             @if($finCount > 0)
                 <span class="badge bg-danger rounded-pill x-small ms-auto">{{ $finCount }}</span>
             @else
                 <i class="bi bi-chevron-down ms-auto x-small opacity-50"></i>
             @endif
        </a>
        <div class="collapse {{ request()->routeIs('admin.finance*') ? 'show' : '' }}" id="adminFinMenu">
            <div class="ms-1 ps-3 border-start border-white-10 mb-2">
                 <a href="{{ route('admin.finance') }}" class="sidebar-sublink {{ request()->routeIs('admin.finance') ? 'text-white fw-bold' : '' }}">Dashboard</a>
                 <a href="{{ route('admin.finance.projects.index') }}" class="sidebar-sublink {{ request()->routeIs('admin.finance.projects.*') ? 'text-white fw-bold' : '' }}">Projects</a>
                 <a href="{{ route('admin.finance.expenses.index') }}" class="sidebar-sublink d-flex align-items-center justify-content-between {{ request()->routeIs('admin.finance.expenses.*') ? 'text-white fw-bold' : '' }}">
                     <span>Expenses</span>
                     @if($expenseReqCount > 0)
                        <span class="badge bg-danger rounded-pill x-small">{{ $expenseReqCount }}</span>
                     @endif
                 </a>
            </div>
        </div>

        {{-- SYSTEM MODULE --}}
        <a class="sidebar-link {{ request()->routeIs('admin.users*') || request()->routeIs('admin.attendance-settings*') ? 'active' : '' }}" data-bs-toggle="collapse" href="#adminSysMenu" role="button" aria-expanded="{{ request()->routeIs('admin.users*') ? 'true' : 'false' }}">
            <i class="bi bi-gear"></i> <span>System</span>
            <i class="bi bi-chevron-down ms-auto x-small opacity-50"></i>
        </a>
        <div class="collapse {{ request()->routeIs('admin.users*') || request()->routeIs('admin.attendance-settings*') ? 'show' : '' }}" id="adminSysMenu">
            <div class="ms-1 ps-3 border-start border-white-10 mb-2">
                <a href="{{ route('admin.users.index') }}" class="sidebar-sublink {{ request()->routeIs('admin.users.*') ? 'text-white fw-bold' : '' }}">System Users</a>
                <a href="{{ route('admin.attendance-settings.index') }}" class="sidebar-sublink {{ request()->routeIs('admin.attendance-settings.*') ? 'text-white fw-bold' : '' }}">Configuration</a>
                <a href="{{ route('admin.activity-logs') }}" class="sidebar-sublink {{ request()->routeIs('admin.activity-logs') ? 'text-white fw-bold' : '' }}">Audit Trails</a>
                <a href="{{ route('admin.trash.index') }}" class="sidebar-sublink {{ request()->routeIs('admin.trash.index') ? 'text-white fw-bold' : '' }}">Trash Recovery</a>
            </div>
        </div>

      @else
        {{-- =========================================
             ROLE SPECIFIC VIEWS (Non-Admin)
             ========================================= --}}

        @if(Auth::user()->hasRole('Human Resource Manager') || Auth::user()->hasRole('HumanResourceManager'))
            <a href="{{ route('hr.dashboard') }}" class="sidebar-link {{ request()->routeIs('hr.dashboard') ? 'active' : '' }}">
                <i class="bi bi-grid-1x2"></i> <span>Dashboard</span>
            </a>
            <a href="{{ route('hr.employees.index') }}" class="sidebar-link {{ request()->routeIs('hr.employees.*') ? 'active' : '' }}">
                <i class="bi bi-people"></i> <span>Employees</span>
            </a>
            <a href="{{ route('hr.leaves.index') }}" class="sidebar-link {{ request()->routeIs('hr.leaves.*') ? 'active' : '' }}">
                <i class="bi bi-calendar-check"></i> <span>Leave Management</span>
                @if($leaveReqCount > 0)
                    <span class="badge bg-danger rounded-pill x-small ms-auto">{{ $leaveReqCount }}</span>
                @endif
            </a>
            <a href="{{ route('hr.attendance.index') }}" class="sidebar-link {{ request()->routeIs('hr.attendance.*') ? 'active' : '' }}">
                <i class="bi bi-clock-history"></i> <span>Attendance</span>
            </a>
        @endif

        @if(Auth::user()->hasRole('Inventory Manager') || Auth::user()->hasRole('InventoryManager'))
            <a href="{{ route('inventory.dashboard') }}" class="sidebar-link {{ request()->routeIs('inventory.dashboard') ? 'active' : '' }}">
                <i class="bi bi-grid-1x2"></i> <span>Dashboard</span>
            </a>
            <a href="{{ route('inventory.items.index') }}" class="sidebar-link {{ request()->routeIs('inventory.items.*') ? 'active' : '' }}">
                <i class="bi bi-box-seam"></i> <span>Items Catalogue</span>
            </a>
            <a href="{{ route('inventory.loans.index') }}" class="sidebar-link {{ request()->routeIs('inventory.loans.*') ? 'active' : '' }}">
                <i class="bi bi-arrow-left-right"></i> <span>Item Lending</span>
                @php
                    // For managers, we show total module count on main link if they want, 
                    // or specific counts for actions. Let's use inventory_request here.
                    $invSubCount = $unreadNotifications->filter(fn($n) => ($n->data['type'] ?? '') === 'inventory_request')->count();
                @endphp
                @if($invSubCount > 0)
                    <span class="badge bg-danger rounded-pill x-small ms-auto">{{ $invSubCount }}</span>
                @endif
            </a>
            <a href="{{ route('inventory.logs.index') }}" class="sidebar-link {{ request()->routeIs('inventory.logs.*') ? 'active' : '' }}">
                <i class="bi bi-journal-text"></i> <span>Audit Logs</span>
            </a>
        @endif

        @if(Auth::user()->hasRole('Financial Manager') || Auth::user()->hasRole('FinancialManager'))
            <a href="{{ route('finance.dashboard') }}" class="sidebar-link {{ request()->routeIs('finance.dashboard') ? 'active' : '' }}">
                <i class="bi bi-grid-1x2"></i> <span>Dashboard</span>
            </a>
            <a href="{{ route('finance.projects.index') }}" class="sidebar-link {{ request()->routeIs('finance.projects.*') ? 'active' : '' }}">
                <i class="bi bi-briefcase"></i> <span>Projects</span>
            </a>
            <a href="{{ route('finance.expenses.index') }}" class="sidebar-link {{ request()->routeIs('finance.expenses.*') ? 'active' : '' }}">
                <i class="bi bi-receipt"></i> <span>Expenses</span>
                @php
                    $finSubCount = $unreadNotifications->filter(fn($n) => ($n->data['type'] ?? '') === 'expense_request')->count();
                @endphp
                @if($finSubCount > 0)
                    <span class="badge bg-danger rounded-pill x-small ms-auto">{{ $finSubCount }}</span>
                @endif
            </a>
        @endif

      @endif

    </div>

    <div class="sidebar-footer">
        <div class="dropup w-100">
            <button href="#" class="d-flex align-items-center gap-3 w-100 p-2 border-0 bg-transparent text-start user-profile-trigger" 
                    data-bs-toggle="dropdown" aria-expanded="false" style="border-radius: 16px; transition: all 0.2s ease;">
                
                {{-- Avatar --}}
                <div class="position-relative">
                    @if(optional(Auth::user()->employee)->profile_picture_url)
                        <img src="{{ Auth::user()->employee->profile_picture_url }}" alt="Profile" 
                             class="rounded-circle border border-2 border-white-20 shadow-sm" 
                             style="width: 44px; height: 44px; object-fit: cover;">
                    @else
                        <div class="avatar-circle rounded-circle shadow-sm" style="width: 44px; height: 44px; font-size: 1rem; background: linear-gradient(135deg, #3b82f6, #2563eb);">
                        {{ substr(Auth::user()->first_name ?? Auth::user()->name, 0, 1) }}
                        </div>
                    @endif
                    <span class="position-absolute bottom-0 end-0 bg-success border border-2 border-dark rounded-circle p-1"></span>
                </div>
                
                {{-- User Info --}}
                <div class="overflow-hidden flex-grow-1">
                    <div class="text-white fw-bold text-truncate" style="font-size: 0.95rem;">{{ Auth::user()->name }}</div>
                    <div class="text-white-50 x-small text-truncate text-uppercase tracking-tight">
                        {{ Auth::user()->roles->first()->name ?? 'Team Member' }}
                    </div>
                </div>

                {{-- Chevron --}}
                <i class="bi bi-chevron-up text-white-50 small"></i>
            </button>

            {{-- Dropup Menu --}}
            <ul class="dropdown-menu shadow-lg border-0 mb-2 w-100 p-2" style="background: rgba(255, 255, 255, 0.95); backdrop-filter: blur(10px); border-radius: 16px;">
                <li>
                    <h6 class="dropdown-header text-uppercase x-small fw-bold text-muted opacity-75 mb-1">Account</h6>
                </li>
                {{-- My Profile --}}
                <li>
                    <a class="dropdown-item rounded-3 d-flex align-items-center gap-2 py-2" href="{{ Auth::user()->getProfileUrl() }}">
                        @if(optional(Auth::user()->employee)->profile_picture_url)
                            <img src="{{ Auth::user()->employee->profile_picture_url }}" alt="Img" 
                                 class="rounded-circle border border-primary-subtle" 
                                 style="width: 20px; height: 20px; object-fit: cover;">
                        @else
                             <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center" 
                                  style="width: 20px; height: 20px; font-size: 0.6rem;">
                                 {{ substr(Auth::user()->first_name ?? Auth::user()->name, 0, 1) }}
                             </div>
                        @endif
                        <span>My Profile</span>
                    </a>
                </li>
                <li><hr class="dropdown-divider opacity-25 my-2"></li>
                <li>
                    <form action="{{ route('logout') }}" method="POST">
                        @csrf
                        <button type="submit" class="dropdown-item rounded-3 d-flex align-items-center gap-2 py-2 text-danger hover-danger">
                            <i class="bi bi-box-arrow-right"></i> Sign Out
                        </button>
                    </form>
                </li>
            </ul>
        </div>
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
  </div>

  {{-- Global Rejection Modal --}}
  <div class="modal fade" id="globalRejectModal" tabindex="-1" aria-hidden="true">
      <div class="modal-dialog modal-dialog-centered">
          <div class="modal-content hardened-glass-static p-0" style="background: white; border-radius: 24px;">
              <form id="globalRejectForm" method="POST">
                  @csrf
                  <div class="modal-header border-0 p-4 pb-0">
                      <h5 class="fw-800 text-erp-deep mb-0">Reject <span id="rejectTypeLabel">Request</span></h5>
                      <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                  </div>
                  <div class="modal-body p-4">
                      <p class="text-muted small mb-4">Are you sure you want to reject this request? Please provide a brief reason for the record.</p>
                      <div class="mb-3">
                          <label class="form-label fw-bold small text-uppercase mb-2 text-erp-deep">Reason for Rejection</label>
                          <textarea name="rejection_reason" id="globalRejectReason" class="form-control rounded-3 bg-light border-0" rows="3" required placeholder="e.g. Documentation required..."></textarea>
                      </div>
                  </div>
                  <div class="modal-footer border-0 p-4 pt-0">
                      <button type="button" class="btn btn-light rounded-pill px-4 fw-bold" data-bs-dismiss="modal">Cancel</button>
                      <button type="submit" class="btn btn-danger rounded-pill px-4 fw-bold shadow-sm">Confirm Rejection</button>
                  </div>
              </form>
          </div>
      </div>
  </div>

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
        { name: 'System Users', url: '{{ route("admin.users.index") }}', icon: 'bi-person-gear' },
        { name: 'Audit Trails (System Logs)', url: '{{ route("admin.activity-logs") }}', icon: 'bi-journal-text' },
        { name: 'Trash & Data Recovery', url: '{{ route("admin.trash.index") }}', icon: 'bi-trash-fill' }
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

    function markNotificationRead(id) {
        fetch(`/notifications/${id}/mark-as-read`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Accept': 'application/json'
            }
        });
    }

    function openGlobalRejectModal(actionUrl, typeLabel, notificationId) {
        const modal = new bootstrap.Modal(document.getElementById('globalRejectModal'));
        document.getElementById('globalRejectForm').action = actionUrl;
        document.getElementById('rejectTypeLabel').innerText = typeLabel;
        
        // Ensure the notification is marked as read when the form is submitted
        document.getElementById('globalRejectForm').onsubmit = function() {
            markNotificationRead(notificationId);
            return true;
        };
        
        modal.show();
    }

    // Premium Toast Helper
    function showToast(message, type = 'success', title = 'System Update') {
        const toastEl = document.getElementById('erpToast');
        const iconEl = document.getElementById('toastIcon');
        const iconContainer = document.getElementById('toastIconContainer');
        const titleEl = document.getElementById('toastTitle');
        const messageEl = document.getElementById('toastMessage');

        // Reset classes
        iconContainer.className = 'me-3 rounded-circle d-flex align-items-center justify-content-center ';
        
        if (type === 'success') {
            iconContainer.classList.add('bg-success-soft', 'text-success');
            iconEl.className = 'bi bi-check-circle-fill';
        } else if (type === 'danger' || type === 'error') {
            iconContainer.classList.add('bg-danger-soft', 'text-danger');
            iconEl.className = 'bi bi-exclamation-triangle-fill';
        } else {
            iconContainer.classList.add('bg-primary-soft', 'text-primary');
            iconEl.className = 'bi bi-info-circle-fill';
        }

        titleEl.innerText = title;
        messageEl.innerText = message;

        const toast = new bootstrap.Toast(toastEl, { delay: 5000 });
        toast.show();
    }

    // Auto-show session flashes as toasts
    document.addEventListener('DOMContentLoaded', function() {
        @if(session('status'))
            showToast("{{ session('status') }}", 'success', 'Success');
        @endif
        @if(session('error'))
            showToast("{{ session('error') }}", 'danger', 'Error Occurred');
        @endif
        @if(session('success'))
            showToast("{{ session('success') }}", 'success', 'Success');
        @endif
    });
  </script>

  @stack('scripts')
</body>
</html>
