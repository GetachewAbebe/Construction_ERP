@props(['title' => 'Dashboard'])

@php
    $u = auth()->user();
    $isAdmin = $u && ($u->hasRole('Administrator') || $u->hasRole('Admin'));
    $isHr = $u && ($u->hasRole('Human Resource Manager') || $u->hasRole('HumanResourceManager'));
    $isInv = $u && ($u->hasRole('Inventory Manager') || $u->hasRole('InventoryManager'));
    $isFin = $u && ($u->hasRole('Financial Manager') || $u->hasRole('FinancialManager'));

    if ($isAdmin) {
        $nav = [
            ['header' => null, 'items' => [
                ['label' => 'Dashboard', 'icon' => 'o-home', 'route' => 'admin.dashboard', 'active' => 'admin.dashboard'],
            ]],
            ['header' => 'Approvals', 'items' => [
                ['label' => 'Inventory Loans', 'icon' => 'o-clipboard-document-check', 'route' => 'admin.requests.items', 'active' => 'admin.requests.items'],
                ['label' => 'Expenses', 'icon' => 'o-banknotes', 'route' => 'admin.requests.finance', 'active' => 'admin.requests.finance'],
                ['label' => 'Leave Requests', 'icon' => 'o-calendar-days', 'route' => 'admin.requests.leave-approvals.index', 'active' => 'admin.requests.leave-approvals.*'],
            ]],
            ['header' => 'Management', 'items' => [
                ['label' => 'Users', 'icon' => 'o-users', 'route' => 'admin.users.index', 'active' => 'admin.users.*'],
                ['label' => 'Roles', 'icon' => 'o-shield-check', 'route' => 'admin.roles.index', 'active' => 'admin.roles.*'],
            ]],
            ['header' => 'Human Resources', 'items' => [
                ['label' => 'Employees', 'icon' => 'o-identification', 'route' => 'admin.hr.employees.index', 'active' => 'admin.hr.employees.*'],
                ['label' => 'Attendance', 'icon' => 'o-clock', 'route' => 'admin.hr.attendance.index', 'active' => 'admin.hr.attendance.*'],
                ['label' => 'Leaves', 'icon' => 'o-calendar', 'route' => 'admin.hr.leaves.index', 'active' => 'admin.hr.leaves.*'],
            ]],
            ['header' => 'Inventory', 'items' => [
                ['label' => 'Items', 'icon' => 'o-cube', 'route' => 'admin.inventory.items.index', 'active' => 'admin.inventory.items.*'],
                ['label' => 'Loans', 'icon' => 'o-arrow-path', 'route' => 'admin.inventory.loans.index', 'active' => 'admin.inventory.loans.*'],
                ['label' => 'Audit Logs', 'icon' => 'o-document-text', 'route' => 'admin.inventory.logs.index', 'active' => 'admin.inventory.logs.*'],
            ]],
            ['header' => 'Finance', 'items' => [
                ['label' => 'Projects', 'icon' => 'o-briefcase', 'route' => 'admin.finance.projects.index', 'active' => 'admin.finance.projects.*'],
                ['label' => 'Expenses', 'icon' => 'o-credit-card', 'route' => 'admin.finance.expenses.index', 'active' => 'admin.finance.expenses.*'],
            ]],
            ['header' => 'System', 'items' => [
                ['label' => 'Activity Logs', 'icon' => 'o-list-bullet', 'route' => 'admin.activity-logs', 'active' => 'admin.activity-logs'],
                ['label' => 'Maintenance', 'icon' => 'o-wrench-screwdriver', 'route' => 'admin.maintenance.index', 'active' => 'admin.maintenance.*'],
                ['label' => 'Trash', 'icon' => 'o-trash', 'route' => 'admin.trash.index', 'active' => 'admin.trash.*'],
                ['label' => 'Settings', 'icon' => 'o-cog-6-tooth', 'route' => 'admin.system-settings.index', 'active' => 'admin.system-settings.*'],
            ]],
        ];
    } elseif ($isHr) {
        $nav = [
            ['header' => null, 'items' => [
                ['label' => 'Dashboard', 'icon' => 'o-home', 'route' => 'hr.dashboard', 'active' => 'hr.dashboard'],
                ['label' => 'Employees', 'icon' => 'o-identification', 'route' => 'hr.employees.index', 'active' => 'hr.employees.*'],
                ['label' => 'Attendance', 'icon' => 'o-clock', 'route' => 'hr.attendance.index', 'active' => 'hr.attendance.*'],
                ['label' => 'Leaves', 'icon' => 'o-calendar', 'route' => 'hr.leaves.index', 'active' => 'hr.leaves.*'],
            ]],
        ];
    } elseif ($isInv) {
        $nav = [
            ['header' => null, 'items' => [
                ['label' => 'Dashboard', 'icon' => 'o-home', 'route' => 'inventory.dashboard', 'active' => 'inventory.dashboard'],
                ['label' => 'Items', 'icon' => 'o-cube', 'route' => 'inventory.items.index', 'active' => 'inventory.items.*'],
                ['label' => 'Loans', 'icon' => 'o-arrow-path', 'route' => 'inventory.loans.index', 'active' => 'inventory.loans.*'],
                ['label' => 'Vendors', 'icon' => 'o-truck', 'route' => 'inventory.vendors.index', 'active' => 'inventory.vendors.*'],
                ['label' => 'Audit Logs', 'icon' => 'o-document-text', 'route' => 'inventory.logs.index', 'active' => 'inventory.logs.*'],
            ]],
        ];
    } elseif ($isFin) {
        $nav = [
            ['header' => null, 'items' => [
                ['label' => 'Dashboard', 'icon' => 'o-home', 'route' => 'finance.dashboard', 'active' => 'finance.dashboard'],
                ['label' => 'Projects', 'icon' => 'o-briefcase', 'route' => 'finance.projects.index', 'active' => 'finance.projects.*'],
                ['label' => 'Expenses', 'icon' => 'o-credit-card', 'route' => 'finance.expenses.index', 'active' => 'finance.expenses.*'],
            ]],
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
    <title>{{ $title }} · Natanem Engineering ERP</title>

    @vite(['resources/css/mary.css'])
    @livewireStyles
    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
    {{-- Apply saved theme BEFORE first paint to eliminate FOUC --}}
    <script>
        (function () {
            var saved = localStorage.getItem('erp-theme');
            var preferred = (saved === 'natanem-dark' || (!saved && window.matchMedia('(prefers-color-scheme: dark)').matches))
                ? 'natanem-dark' : 'natanem';
            document.getElementById('html-root').setAttribute('data-theme', preferred);
        })();
    </script>
</head>
<body class="min-h-screen bg-base-200 text-base-content antialiased"
      x-data="{
          sidebar: false,
          darkMode: localStorage.getItem('erp-theme') === 'natanem-dark' || (!localStorage.getItem('erp-theme') && window.matchMedia('(prefers-color-scheme: dark)').matches),
          toggleTheme() {
              this.darkMode = !this.darkMode;
              var theme = this.darkMode ? 'natanem-dark' : 'natanem';
              document.getElementById('html-root').setAttribute('data-theme', theme);
              localStorage.setItem('erp-theme', theme);
          }
      }">

    {{-- Mobile overlay --}}
    <div x-show="sidebar" x-transition.opacity @click="sidebar = false"
         class="fixed inset-0 z-30 bg-black/40 lg:hidden" style="display:none"></div>

    {{-- Sidebar --}}
    <aside :class="sidebar ? 'translate-x-0' : '-translate-x-full'"
           class="fixed inset-y-0 left-0 z-40 flex w-64 transform flex-col overflow-y-auto text-white transition-transform duration-200 lg:translate-x-0"
           style="background:linear-gradient(180deg,#13263b 0%,#0d1b2a 100%);">
        {{-- brand --}}
        <div class="flex h-14 shrink-0 items-center justify-between gap-2 border-b border-white/10 px-5">
            <span class="text-sm font-bold tracking-wide">NATANEM <span class="text-amber-400">ENGINEERING</span></span>
            <button @click="sidebar = false" class="text-white/60 hover:text-white lg:hidden">
                <x-mary-icon name="o-x-mark" class="h-5 w-5" />
            </button>
        </div>

        {{-- nav --}}
        <nav class="flex-1 space-y-6 px-3 py-4">
            @foreach ($nav as $group)
                <div class="space-y-1">
                    @if ($group['header'])
                        <div class="px-3 pb-1 text-[11px] font-semibold uppercase tracking-wider text-white/35">{{ $group['header'] }}</div>
                    @endif
                    @foreach ($group['items'] as $item)
                        <a href="{{ route($item['route']) }}"
                           class="flex items-center gap-3 rounded-lg px-3 py-2 text-sm transition
                                  {{ request()->routeIs($item['active'])
                                        ? 'bg-amber-500/15 font-medium text-amber-300'
                                        : 'text-white/70 hover:bg-white/5 hover:text-white' }}">
                            <x-mary-icon name="{{ $item['icon'] }}" class="h-5 w-5 shrink-0" />
                            <span class="truncate">{{ $item['label'] }}</span>
                        </a>
                    @endforeach
                </div>
            @endforeach
        </nav>
    </aside>

    {{-- Main column --}}
    <div class="lg:pl-64">
        {{-- Topbar --}}
        <header class="sticky top-0 z-20 flex h-14 items-center gap-3 border-b border-base-300 bg-base-100 px-4 shadow-sm sm:px-6">
            <button @click="sidebar = true" class="rounded-md p-1.5 text-base-content/70 hover:bg-base-200 lg:hidden">
                <x-mary-icon name="o-bars-3" class="h-6 w-6" />
            </button>

            <h1 class="truncate text-base font-semibold">{{ $title }}</h1>

            <div class="ml-auto flex items-center gap-1">
                {{-- Dark / Light mode toggle --}}
                <button @click="toggleTheme()"
                        class="rounded-md p-2 text-base-content/60 hover:bg-base-200 transition-colors"
                        :title="darkMode ? 'Switch to light mode' : 'Switch to dark mode'"
                        id="theme-toggle-btn">
                    <x-mary-icon x-show="darkMode" name="o-sun" class="h-5 w-5" />
                    <x-mary-icon x-show="!darkMode" name="o-moon" class="h-5 w-5" />
                </button>

                <a href="{{ route('notifications.index') }}" class="rounded-md p-2 text-base-content/60 hover:bg-base-200" title="Notifications">
                    <x-mary-icon name="o-bell" class="h-5 w-5" />
                </a>

                {{-- user menu --}}
                <div x-data="{ open: false }" class="relative">
                    <button @click="open = !open" class="flex items-center gap-2 rounded-lg p-1 pr-2 hover:bg-base-200">
                        <span class="grid h-8 w-8 place-items-center rounded-full bg-primary/15 text-xs font-semibold text-primary">{{ $initials }}</span>
                        <span class="hidden text-sm font-medium sm:block">{{ $u->name ?? 'User' }}</span>
                        <x-mary-icon name="o-chevron-down" class="hidden h-4 w-4 text-base-content/50 sm:block" />
                    </button>
                    <div x-show="open" @click.outside="open = false" x-transition
                         class="absolute right-0 mt-2 w-44 rounded-xl border border-base-300 bg-base-100 p-1 shadow-lg" style="display:none">
                        <div class="px-3 py-2 text-xs text-base-content/50">{{ $u->email ?? '' }}</div>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="flex w-full items-center gap-2 rounded-lg px-3 py-2 text-left text-sm text-error hover:bg-error/10">
                                <x-mary-icon name="o-arrow-left-start-on-rectangle" class="h-4 w-4" />
                                Sign out
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </header>

        {{-- Page content --}}
        <main class="p-4 sm:p-6">
            {{ $slot }}
        </main>
    </div>

    <x-mary-toast />
    @livewireScripts
    @stack('scripts')
</body>
</html>
