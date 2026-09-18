@php
    $u = Auth::user();
    $isAdmin = $u && ($u->hasRole('Administrator') || $u->hasRole('Admin'));
    $isHr    = $u && ($u->hasRole('Human Resource Manager') || $u->hasRole('HumanResourceManager'));
    $isInv   = $u && ($u->hasRole('Inventory Manager') || $u->hasRole('InventoryManager'));
    $isFin   = $u && ($u->hasRole('Financial Manager') || $u->hasRole('FinancialManager'));

    $shellPendingExpenses = \App\Models\Expense::whereIn('status', ['pending', 'Pending', \App\Enums\ExpenseStatus::Pending->value])->count();
    $shellPendingLoans = \App\Models\InventoryLoan::whereIn('status', ['pending', 'Pending', \App\Enums\LoanStatus::Pending->value])->count();
    $shellPendingLeaves = \App\Models\LeaveRequest::whereIn('status', ['Pending', 'pending', \App\Enums\LeaveStatus::Pending->value])->count();
    $shellStaffCount = \App\Models\Employee::count();
    $shellItemCount = \App\Models\InventoryItem::count();

    if ($isAdmin) {
        $nav = [
            [
                'type' => 'link',
                'label' => 'Overview Dashboard',
                'icon' => 'o-squares-2x2',
                'route' => 'admin.dashboard',
                'active' => 'admin.dashboard',
            ],
            [
                'type' => 'accordion',
                'id' => 'hr-menu',
                'header' => 'Human Resources',
                'icon' => 'o-users',
                'badge' => $shellPendingLeaves > 0 ? (string) $shellPendingLeaves : null,
                'badge_color' => 'badge-warning',
                'active_patterns' => ['hr.employees.*', 'hr.attendance.*', 'hr.leaves.*'],
                'items' => [
                    ['label' => 'Staff Directory', 'route' => 'hr.employees.index', 'active' => 'hr.employees.*'],
                    ['label' => 'Daily Attendance', 'route' => 'hr.attendance.index', 'active' => 'hr.attendance.*'],
                    ['label' => 'Leave Authorizations', 'route' => 'hr.leaves.index', 'active' => 'hr.leaves.*'],
                ],
            ],
            [
                'type' => 'accordion',
                'id' => 'fin-menu',
                'header' => 'Financial Management',
                'icon' => 'o-banknotes',
                'badge' => $shellPendingExpenses > 0 ? (string) $shellPendingExpenses : null,
                'badge_color' => 'badge-warning',
                'active_patterns' => ['finance.expenses.*', 'finance.projects.*'],
                'items' => [
                    ['label' => 'Project Budgets', 'route' => 'finance.projects.index', 'active' => 'finance.projects.*'],
                    ['label' => 'Expense Requisitions', 'route' => 'finance.expenses.index', 'active' => 'finance.expenses.*'],
                ],
            ],
            [
                'type' => 'accordion',
                'id' => 'ops-menu',
                'header' => 'Construction Operations',
                'icon' => 'o-truck',
                'badge' => null,
                'active_patterns' => ['equipment.*', 'projects.daily-reports.*'],
                'items' => [
                    ['label' => 'Heavy Fleet & Machinery', 'route' => 'equipment.index', 'active' => 'equipment.*'],
                    ['label' => 'Daily Progress Reports', 'route' => 'projects.daily-reports.index', 'active' => 'projects.daily-reports.*'],
                    ['label' => 'Project Sites & Caps', 'route' => 'finance.projects.index', 'active' => 'finance.projects.*'],
                ],
            ],
            [
                'type' => 'accordion',
                'id' => 'inv-menu',
                'header' => 'Inventory & Store',
                'icon' => 'o-cube',
                'badge' => $shellPendingLoans > 0 ? (string) $shellPendingLoans : null,
                'badge_color' => 'badge-warning',
                'active_patterns' => ['inventory.items.*', 'inventory.loans.*', 'inventory.logs.*'],
                'items' => [
                    ['label' => 'Item Stock Catalog', 'route' => 'inventory.items.index', 'active' => 'inventory.items.*'],
                    ['label' => 'Material Loans & Passes', 'route' => 'inventory.loans.index', 'active' => 'inventory.loans.*'],
                    ['label' => 'Movement Logs', 'route' => 'inventory.logs.index', 'active' => 'inventory.logs.*'],
                ],
            ],
            [
                'type' => 'accordion',
                'id' => 'adm-menu',
                'header' => 'Administration',
                'icon' => 'o-cog-6-tooth',
                'badge' => null,
                'active_patterns' => ['admin.users.*', 'admin.activity-logs', 'admin.trash.*', 'admin.system-settings.*'],
                'items' => [
                    ['label' => 'User Management', 'route' => 'admin.users.index', 'active' => 'admin.users.*'],
                    ['label' => 'Audit Activity Trail', 'route' => 'admin.activity-logs', 'active' => 'admin.activity-logs'],
                    ['label' => 'Trash & Restore', 'route' => 'admin.trash.index', 'active' => 'admin.trash.*'],
                    ['label' => 'System Settings', 'route' => 'admin.system-settings.index', 'active' => 'admin.system-settings.*'],
                ],
            ],
        ];
    } elseif ($isHr) {
        $nav = [
            [
                'type' => 'link',
                'label' => 'HR Dashboard',
                'icon' => 'o-squares-2x2',
                'route' => 'hr.dashboard',
                'active' => 'hr.dashboard',
            ],
            [
                'type' => 'accordion',
                'id' => 'hr-menu',
                'header' => 'Human Resources',
                'icon' => 'o-users',
                'badge' => null,
                'active_patterns' => ['hr.employees.*', 'hr.attendance.*', 'hr.leaves.*'],
                'items' => [
                    ['label' => 'Staff Directory', 'route' => 'hr.employees.index', 'active' => 'hr.employees.*'],
                    ['label' => 'Daily Attendance', 'route' => 'hr.attendance.index', 'active' => 'hr.attendance.*'],
                    ['label' => 'Leave Authorizations', 'route' => 'hr.leaves.index', 'active' => 'hr.leaves.*'],
                ],
            ],
        ];
    } elseif ($isInv) {
        $nav = [
            [
                'type' => 'link',
                'label' => 'Inventory Dashboard',
                'icon' => 'o-squares-2x2',
                'route' => 'inventory.dashboard',
                'active' => 'inventory.dashboard',
            ],
            [
                'type' => 'accordion',
                'id' => 'inv-menu',
                'header' => 'Inventory & Store',
                'icon' => 'o-cube',
                'badge' => null,
                'active_patterns' => ['inventory.items.*', 'inventory.loans.*', 'inventory.logs.*'],
                'items' => [
                    ['label' => 'Item Stock Catalog', 'route' => 'inventory.items.index', 'active' => 'inventory.items.*'],
                    ['label' => 'Material Loans', 'route' => 'inventory.loans.index', 'active' => 'inventory.loans.*'],
                    ['label' => 'Movement Logs', 'route' => 'inventory.logs.index', 'active' => 'inventory.logs.*'],
                ],
            ],
        ];
    } elseif ($isFin) {
        $nav = [
            [
                'type' => 'link',
                'label' => 'Finance Dashboard',
                'icon' => 'o-squares-2x2',
                'route' => 'finance.dashboard',
                'active' => 'finance.dashboard',
            ],
            [
                'type' => 'accordion',
                'id' => 'fin-menu',
                'header' => 'Financial Management',
                'icon' => 'o-banknotes',
                'badge' => null,
                'active_patterns' => ['finance.expenses.*', 'finance.projects.*'],
                'items' => [
                    ['label' => 'Project Budgets', 'route' => 'finance.projects.index', 'active' => 'finance.projects.*'],
                    ['label' => 'Expenses', 'route' => 'finance.expenses.index', 'active' => 'finance.expenses.*'],
                ],
            ],
        ];
    } else {
        $nav = [];
    }

    $initials = collect(explode(' ', trim((string) ($u->name ?? 'U'))))
        ->filter()->take(2)->map(fn ($p) => mb_strtoupper(mb_substr($p, 0, 1)))->implode('');
@endphp

<!DOCTYPE html>
<html lang="en" id="html-root" data-theme="natanem">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title }} · Natanem ERP</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Public+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    @vite(['resources/css/mary.css', 'resources/js/app.js'])
    @livewireStyles
    <style>
        body { font-family: 'Plus Jakarta Sans', 'Public Sans', -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif; }
    </style>
    <script>
        (function () {
            var saved = localStorage.getItem('erp-theme');
            var preferred = (saved === 'natanem-dark' || (!saved && window.matchMedia('(prefers-color-scheme: dark)').matches))
                ? 'natanem-dark' : 'natanem';
            document.getElementById('html-root').setAttribute('data-theme', preferred);
        })();
    </script>
</head>
<body class="min-h-screen bg-slate-50 dark:bg-slate-950 text-slate-700 dark:text-slate-200 antialiased"
      x-data="{
          mobileSidebar: false,
          darkMode: localStorage.getItem('erp-theme') === 'natanem-dark' || (!localStorage.getItem('erp-theme') && window.matchMedia('(prefers-color-scheme: dark)').matches),
          toggleTheme() {
              this.darkMode = !this.darkMode;
              var theme = this.darkMode ? 'natanem-dark' : 'natanem';
              document.getElementById('html-root').setAttribute('data-theme', theme);
              localStorage.setItem('erp-theme', theme);
          }
      }">

    {{-- Mobile Overlay Backdrop --}}
    <div x-show="mobileSidebar" 
         x-transition.opacity 
         @click="mobileSidebar = false"
         class="fixed inset-0 z-40 bg-slate-900/60 backdrop-blur-sm lg:hidden" 
         style="display:none"></div>

    {{-- Main Desktop & Mobile Sidebar --}}
    <aside :class="mobileSidebar ? 'translate-x-0' : '-translate-x-full lg:translate-x-0'"
           x-data="{
               openGroups: {
                   'hr-menu': {{ request()->routeIs('hr.*') ? 'true' : 'false' }},
                   'ops-menu': {{ request()->routeIs('equipment.*', 'projects.daily-reports.*') ? 'true' : 'false' }},
                   'fin-menu': {{ request()->routeIs('finance.*') ? 'true' : 'false' }},
                   'inv-menu': {{ request()->routeIs('inventory.*') ? 'true' : 'false' }},
                   'adm-menu': {{ request()->routeIs('admin.users.*', 'admin.activity-logs', 'admin.trash.*', 'admin.system-settings.*') ? 'true' : 'false' }}
               },
               toggleGroup(id) {
                   this.openGroups[id] = !this.openGroups[id];
               }
           }"
           class="fixed inset-y-0 left-0 z-50 flex w-64 flex-col bg-white dark:bg-slate-900 border-r border-slate-200 dark:border-slate-800 transition-transform duration-200 shadow-sm">
        
        {{-- Brand Header --}}
        <div class="flex h-16 items-center gap-3 px-5 border-b border-slate-200 dark:border-slate-800 shrink-0">
            <span class="flex h-9 w-9 items-center justify-center rounded-xl bg-gradient-to-br from-blue-900 to-indigo-800 text-white font-black text-sm shadow-md shadow-blue-950/20 shrink-0">
                NE
            </span>
            <div class="flex flex-col min-w-0 flex-1">
                <span class="font-extrabold text-sm tracking-tight text-slate-900 dark:text-white">NATANEM</span>
                <span class="text-[10px] font-semibold text-blue-900 dark:text-blue-400 uppercase tracking-wider truncate">Enterprise ERP</span>
            </div>
            <button @click="mobileSidebar = false" class="p-1 text-slate-400 hover:text-slate-600 lg:hidden">
                <x-mary-icon name="o-x-mark" class="h-5 w-5" />
            </button>
        </div>

        {{-- Nav Accordion Menu --}}
        <nav class="flex-1 overflow-y-auto px-3 py-4 space-y-1.5">
            <div class="px-3 pb-1 text-[10px] font-bold uppercase tracking-widest text-slate-400">
                Core Systems
            </div>

            @foreach ($nav as $item)
                @if ($item['type'] === 'link')
                    @php $isActive = request()->routeIs($item['active']); @endphp
                    <a href="{{ route($item['route']) }}"
                       class="group flex items-center justify-between rounded-xl px-3 py-2.5 text-xs font-semibold transition-colors
                              {{ $isActive
                                    ? 'bg-blue-900 text-white font-bold shadow-sm shadow-blue-900/20'
                                    : 'text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800' }}">
                        <div class="flex items-center gap-3 min-w-0">
                            <x-mary-icon name="{{ $item['icon'] }}" class="h-4 w-4 shrink-0 {{ $isActive ? 'text-white' : 'text-slate-400 group-hover:text-slate-600' }}" />
                            <span class="truncate">{{ $item['label'] }}</span>
                        </div>
                    </a>
                @elseif ($item['type'] === 'accordion')
                    @php
                        $isChildActive = collect($item['items'])->some(fn($sub) => request()->routeIs($sub['active']));
                    @endphp
                    <div class="space-y-1">
                        {{-- Accordion Parent --}}
                        <button type="button"
                                @click="toggleGroup('{{ $item['id'] }}')"
                                class="w-full group flex items-center justify-between rounded-xl px-3 py-2.5 text-xs font-semibold transition-colors
                                       {{ $isChildActive
                                            ? 'bg-blue-50 dark:bg-blue-950/40 text-blue-900 dark:text-blue-300 font-bold'
                                            : 'text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800' }}">
                            <div class="flex items-center gap-3 min-w-0">
                                <x-mary-icon name="{{ $item['icon'] }}" class="h-4 w-4 shrink-0 {{ $isChildActive ? 'text-blue-900 dark:text-blue-400' : 'text-slate-400 group-hover:text-slate-600' }}" />
                                <span class="truncate">{{ $item['header'] }}</span>
                            </div>

                            <div class="flex items-center gap-2 shrink-0">
                                @if ($item['badge'])
                                    <span class="px-1.5 py-0.5 rounded-full text-[9px] font-bold bg-amber-100 text-amber-800 dark:bg-amber-950 dark:text-amber-300">
                                        {{ $item['badge'] }}
                                    </span>
                                @endif
                                <svg class="w-3.5 h-3.5 text-slate-400 transition-transform duration-200 transform"
                                     :class="openGroups['{{ $item['id'] }}'] ? 'rotate-90 text-blue-900 dark:text-blue-400' : ''"
                                     fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7" />
                                </svg>
                            </div>
                        </button>

                        {{-- Accordion Submenu --}}
                        <div x-show="openGroups['{{ $item['id'] }}']"
                             x-transition
                             class="ml-4 pl-3 border-l-2 border-slate-200 dark:border-slate-700 space-y-0.5 py-1">
                            @foreach ($item['items'] as $sub)
                                @php $isSubActive = request()->routeIs($sub['active']); @endphp
                                <a href="{{ route($sub['route']) }}"
                                   class="flex items-center gap-2.5 rounded-lg px-2.5 py-1.5 text-[11px] font-medium transition-colors
                                          {{ $isSubActive
                                                ? 'text-blue-900 dark:text-blue-400 font-bold bg-blue-50 dark:bg-blue-950/60'
                                                : 'text-slate-500 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white hover:bg-slate-50 dark:hover:bg-slate-800' }}">
                                    <span class="w-1.5 h-1.5 rounded-full {{ $isSubActive ? 'bg-blue-900 dark:bg-blue-400 ring-2 ring-blue-900/20' : 'bg-slate-300 dark:bg-slate-600' }}"></span>
                                    <span class="truncate">{{ $sub['label'] }}</span>
                                </a>
                            @endforeach
                        </div>
                    </div>
                @endif
            @endforeach
        </nav>

        {{-- Fixed User Footer --}}
        <div class="p-3 border-t border-slate-200 dark:border-slate-800 bg-slate-50/50 dark:bg-slate-900/80 shrink-0 space-y-2">
            <div class="flex items-center gap-2.5 p-2 rounded-xl bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 shadow-xs">
                <div class="relative shrink-0">
                    <span class="grid h-8 w-8 place-items-center rounded-lg bg-blue-900/10 text-blue-900 dark:text-blue-300 font-bold text-xs">
                        {{ $initials }}
                    </span>
                    <span class="absolute -bottom-0.5 -right-0.5 w-2 h-2 rounded-full bg-emerald-500 ring-2 ring-white dark:ring-slate-800"></span>
                </div>
                <div class="min-w-0 flex-1">
                    <div class="text-xs font-bold text-slate-900 dark:text-white truncate">{{ $u->name ?? 'Administrator' }}</div>
                    <div class="text-[10px] text-slate-400 truncate">{{ $u->role ?? 'Executive Admin' }}</div>
                </div>
                <button @click="toggleTheme()" title="Toggle Theme" class="p-1 rounded-lg text-slate-400 hover:text-slate-700 dark:hover:text-white">
                    <x-mary-icon name="o-moon" class="w-4 h-4" x-show="!darkMode" />
                    <x-mary-icon name="o-sun" class="w-4 h-4 text-amber-500" x-show="darkMode" style="display:none" />
                </button>
            </div>

            <form method="POST" action="{{ route('logout') }}" class="w-full">
                @csrf
                <button type="submit" class="w-full flex items-center justify-center gap-2 py-1 px-3 rounded-lg text-[11px] font-bold text-rose-600 hover:bg-rose-50 dark:hover:bg-rose-950/40 transition-colors">
                    <x-mary-icon name="o-arrow-right-on-rectangle" class="w-3.5 h-3.5" />
                    <span>Sign Out</span>
                </button>
            </form>
        </div>
    </aside>

    {{-- Main View Column --}}
    <div class="lg:pl-64 flex flex-col min-h-screen">
        {{-- Top Floating Bar --}}
        <header class="sticky top-0 z-30 flex h-16 items-center justify-between gap-4 border-b border-slate-200 dark:border-slate-800 bg-white/95 dark:bg-slate-900/95 backdrop-blur px-4 sm:px-8">
            <div class="flex items-center gap-3">
                <button @click="mobileSidebar = true" class="p-2 rounded-lg text-slate-500 hover:bg-slate-100 dark:hover:bg-slate-800 lg:hidden">
                    <x-mary-icon name="o-bars-3" class="h-5 w-5" />
                </button>

                <div class="flex items-center gap-2 text-xs font-semibold text-slate-500">
                    <span class="text-slate-900 dark:text-white font-bold">Natanem</span>
                    <span>/</span>
                    <span>ERP Management</span>
                </div>
            </div>

            {{-- Center Search --}}
            <div class="flex-1 max-w-sm">
                <button @click="$dispatch('open-command-palette')"
                        class="flex w-full items-center justify-between px-3 py-1.5 rounded-xl bg-slate-100 dark:bg-slate-800 text-slate-400 text-xs hover:bg-slate-200/70 transition-colors">
                    <div class="flex items-center gap-2">
                        <x-mary-icon name="o-magnifying-glass" class="w-3.5 h-3.5" />
                        <span>Search (⌘K)...</span>
                    </div>
                    <kbd class="px-1.5 py-0.5 text-[9px] font-mono rounded bg-white dark:bg-slate-700 text-slate-500 border border-slate-200 dark:border-slate-600">Ctrl K</kbd>
                </button>
            </div>

            {{-- Right Controls --}}
            <div class="flex items-center gap-2">
                {{-- Quick Create Dropdown --}}
                <div class="dropdown dropdown-end">
                    <label tabindex="0" class="btn btn-sm bg-blue-900 hover:bg-blue-950 text-white font-bold rounded-xl gap-1 text-xs shadow-xs">
                        <x-mary-icon name="o-plus" class="w-3.5 h-3.5" />
                        <span>Create</span>
                    </label>
                    <ul tabindex="0" class="dropdown-content z-40 menu p-2 shadow-xl bg-white dark:bg-slate-800 rounded-2xl w-56 border border-slate-200 dark:border-slate-700 text-xs space-y-1 mt-2">
                        <li class="menu-title text-slate-400 font-bold uppercase text-[9px]">Quick Record</li>
                        <li><a href="{{ route('hr.employees.index') }}"><x-mary-icon name="o-user-plus" class="w-4 h-4 text-blue-900" /> New Employee</a></li>
                        <li><a href="{{ route('finance.expenses.index') }}"><x-mary-icon name="o-credit-card" class="w-4 h-4 text-amber-600" /> Log Expense</a></li>
                        <li><a href="{{ route('inventory.loans.index') }}"><x-mary-icon name="o-arrow-path" class="w-4 h-4 text-emerald-600" /> Issue Store Loan</a></li>
                    </ul>
                </div>

                {{-- Notification Button --}}
                <a href="{{ $isAdmin ? route('admin.notifications') : ($isFin ? route('finance.notifications') : '#') }}"
                   class="p-2 rounded-xl text-slate-500 hover:bg-slate-100 dark:hover:bg-slate-800 relative">
                    <x-mary-icon name="o-bell" class="w-5 h-5" />
                    @if ($shellPendingExpenses > 0 || $shellPendingLoans > 0 || $shellPendingLeaves > 0)
                        <span class="absolute top-1.5 right-1.5 w-2 h-2 rounded-full bg-rose-500 ring-2 ring-white dark:ring-slate-900"></span>
                    @endif
                </a>

                {{-- User Avatar Dropdown --}}
                <div class="dropdown dropdown-end">
                    <label tabindex="0" class="btn btn-ghost btn-circle avatar btn-sm bg-blue-900/10 text-blue-900 dark:text-blue-300 font-bold text-xs">
                        {{ $initials }}
                    </label>
                    <ul tabindex="0" class="dropdown-content z-40 menu p-2 shadow-xl bg-white dark:bg-slate-800 rounded-2xl w-52 border border-slate-200 dark:border-slate-700 text-xs mt-2">
                        <li class="menu-title text-slate-400 font-medium">Signed in as <strong class="text-slate-900 dark:text-white">{{ $u->name ?? 'User' }}</strong></li>
                        @if ($isAdmin)
                            <li><a href="{{ route('admin.users.index') }}"><x-mary-icon name="o-user" class="w-4 h-4" /> User Management</a></li>
                            <li><a href="{{ route('admin.system-settings.index') }}"><x-mary-icon name="o-cog-6-tooth" class="w-4 h-4" /> System Settings</a></li>
                        @endif
                        <div class="divider my-1"></div>
                        <li>
                            <form method="POST" action="{{ route('logout') }}" class="w-full">
                                @csrf
                                <button type="submit" class="text-rose-600 w-full text-left font-semibold">
                                    <x-mary-icon name="o-arrow-right-on-rectangle" class="w-4 h-4" /> Sign out
                                </button>
                            </form>
                        </li>
                    </ul>
                </div>
            </div>
        </header>

        {{-- Page Content --}}
        <main class="flex-1 p-4 sm:p-8 space-y-6">
            {{ $slot }}
        </main>
    </div>

    {{-- Global Command Palette Modal --}}
    @livewire('components.command-palette')

    {{-- Global Toast --}}
    <x-mary-toast />

    @livewireScripts
    @stack('scripts')
</body>
</html>
