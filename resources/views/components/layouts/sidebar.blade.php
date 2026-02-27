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

          // Live Data Metrics
          $totalEmployees = \App\Models\Employee::count();
          $pendingLeaves = \App\Models\LeaveRequest::where('status', 'pending')->count();
          
          // Today's attendance summary
          $todayAttendanceRecords = \App\Models\Attendance::today()->count();
          $attendanceStatus = $totalEmployees > 0 ? round(($todayAttendanceRecords / $totalEmployees) * 100) : 0;
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
                <a href="{{ route('admin.hr.attendance.index') }}" class="sidebar-sublink {{ request()->routeIs('admin.hr.attendance.*') ? 'text-white fw-bold' : '' }}">Attendance Records</a>
                <a href="{{ route('hr.attendance.daily-sheet') }}" class="sidebar-sublink {{ request()->routeIs('hr.attendance.daily-sheet') ? 'text-white fw-bold' : '' }}">Standard Daily Sheet</a>
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
                 <a href="{{ route('inventory.asset-classifications.index') }}" class="sidebar-sublink {{ request()->routeIs('inventory.asset-classifications.*') ? 'text-white fw-bold' : '' }}">Asset Classifications</a>
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
                  <a href="{{ route('admin.finance.projects.index') }}" class="sidebar-sublink {{ request()->routeIs('admin.finance.projects.*') ? 'text-white fw-bold' : '' }}">Project Registry</a>
                  <a href="{{ route('admin.finance.expenses.index') }}" class="sidebar-sublink d-flex align-items-center justify-content-between {{ request()->routeIs('admin.finance.expenses.*') ? 'text-white fw-bold' : '' }}">
                      <span>Financial Requisitions</span>
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
                <i class="bi bi-people"></i> 
                <span>Employees</span>
                <span class="badge bg-white-10 text-white-50 ms-auto x-small">{{ $totalEmployees }} Total</span>
            </a>
            <a href="{{ route('hr.leaves.index') }}" class="sidebar-link {{ request()->routeIs('hr.leaves.*') ? 'active' : '' }}">
                <i class="bi bi-calendar-check"></i> 
                <span>Leave Management</span>
                @if($pendingLeaves > 0)
                    <span class="badge bg-danger rounded-pill x-small ms-auto">{{ $pendingLeaves }} Pending</span>
                @endif
            </a>
            <a href="{{ route('hr.attendance.index') }}" class="sidebar-link {{ request()->routeIs('hr.attendance.index') ? 'active' : '' }}">
                <i class="bi bi-clock-history"></i> 
                <span>Attendance Records</span>
                <span class="badge bg-{{ $attendanceStatus < 80 ? 'warning' : 'success' }} rounded-pill x-small ms-auto">{{ $attendanceStatus }}% Today</span>
            </a>
            {{-- Mobile Only: Daily Sheet --}}
            <a href="{{ route('hr.attendance.daily-sheet') }}" class="sidebar-link d-lg-none {{ request()->routeIs('hr.attendance.daily-sheet') ? 'active' : '' }}">
                <i class="bi bi-shield-check"></i> <span>Daily Session Sheet</span>
            </a>
            {{-- Desktop Only: Weekly Sheet --}}
            <a href="{{ route('hr.attendance.weekly-sheet') }}" class="sidebar-link d-none d-lg-flex {{ request()->routeIs('hr.attendance.weekly-sheet') ? 'active' : '' }}">
                <i class="bi bi-calendar-range"></i> <span>Weekly Batch Sheet</span>
            </a>
        @endif

        @if(Auth::user()->hasRole('Inventory Manager') || Auth::user()->hasRole('InventoryManager'))
            <a href="{{ route('inventory.dashboard') }}" class="sidebar-link {{ request()->routeIs('inventory.dashboard') ? 'active' : '' }}">
                <i class="bi bi-grid-1x2"></i> <span>Dashboard</span>
            </a>
            <a href="{{ route('inventory.items.index') }}" class="sidebar-link {{ request()->routeIs('inventory.items.*') ? 'active' : '' }}">
                <i class="bi bi-box-seam"></i> <span>Items Catalogue</span>
            </a>
            <a href="{{ route('inventory.asset-classifications.index') }}" class="sidebar-link {{ request()->routeIs('inventory.asset-classifications.*') ? 'active' : '' }}">
                <i class="bi bi-tags"></i> <span>Asset Classifications</span>
            </a>
            <a href="{{ route('inventory.vendors.index') }}" class="sidebar-link {{ request()->routeIs('inventory.vendors.*') ? 'active' : '' }}">
                <i class="bi bi-truck"></i> <span>Vendor Registry</span>
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
                <i class="bi bi-briefcase"></i> <span>Project Registry</span>
            </a>
            <a href="{{ route('finance.expenses.index') }}" class="sidebar-link {{ request()->routeIs('finance.expenses.*') ? 'active' : '' }}">
                <i class="bi bi-receipt"></i> <span>Financial Requisitions</span>
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
                    @php 
                        $avatarUrl = optional(Auth::user()->employee)->profile_picture_url;
                        $initial = substr(Auth::user()->first_name ?? Auth::user()->name, 0, 1);
                    @endphp
                    <div class="avatar-circle rounded-circle shadow-sm overflow-hidden border border-2 border-white-20 d-flex align-items-center justify-content-center" 
                         style="width: 44px; height: 44px; background: linear-gradient(135deg, #3b82f6, #2563eb);">
                        @if($avatarUrl)
                            <img src="{{ $avatarUrl }}" alt="Profile" 
                                 class="w-100 h-100 object-fit-cover"
                                 onerror="this.style.display='none'; this.parentNode.querySelector('.avatar-initial').style.display='flex';">
                        @endif
                        <span class="text-white fw-900 avatar-initial" style="display: {{ $avatarUrl ? 'none' : 'flex' }};">{{ $initial }}</span>
                    </div>
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
                        @php 
                            $avatarUrl = optional(Auth::user()->employee)->profile_picture_url;
                            $initial = substr(Auth::user()->first_name ?? Auth::user()->name, 0, 1);
                        @endphp
                        <div class="rounded-circle d-flex align-items-center justify-content-center overflow-hidden border border-primary-subtle" 
                             style="width: 24px; height: 24px; background: #6366f1;">
                            @if($avatarUrl)
                                <img src="{{ $avatarUrl }}" alt="Img" 
                                     class="w-100 h-100 object-fit-cover"
                                     onerror="this.style.display='none'; this.parentNode.querySelector('.avatar-initial-sm').style.display='flex';">
                            @endif
                            <span class="text-white fw-bold avatar-initial-sm" style="font-size: 0.6rem; display: {{ $avatarUrl ? 'none' : 'flex' }};">{{ $initial }}</span>
                        </div>
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
