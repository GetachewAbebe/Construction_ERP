<?php

declare(strict_types=1);

namespace App\Livewire\Components;

use App\Models\Employee;
use App\Models\InventoryItem;
use App\Models\Project;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Livewire\Attributes\On;
use Livewire\Component;

class CommandPalette extends Component
{
    public bool $isOpen = false;

    public string $query = '';

    public int $selectedIndex = 0;

    #[On('open-command-palette')]
    public function open(): void
    {
        $this->isOpen = true;
        $this->query = '';
        $this->selectedIndex = 0;
    }

    public function close(): void
    {
        $this->isOpen = false;
        $this->query = '';
    }

    /**
     * Get filtered navigation routes based on user role and search query.
     *
     * @return array<int, array{category: string, title: string, subtitle: string, url: string, icon: string}>
     */
    public function getResultsProperty(): array
    {
        $user = Auth::user();
        if (! $user) {
            return [];
        }

        $results = [];
        $q = mb_strtolower(trim($this->query));

        // 1. Navigation items
        $routes = $this->getNavigationRoutes($user);
        foreach ($routes as $route) {
            if ($q === '' || str_contains(mb_strtolower($route['title']), $q) || str_contains(mb_strtolower($route['category']), $q)) {
                $results[] = $route;
            }
        }

        // If query is present, search Eloquent records
        if ($q !== '') {
            // 2. Search Employees
            $employees = Employee::query()
                ->where(function ($query) use ($q) {
                    $query->where('first_name', 'like', "%{$q}%")
                        ->orWhere('last_name', 'like', "%{$q}%")
                        ->orWhere('email', 'like', "%{$q}%");
                })
                ->take(4)
                ->get();

            foreach ($employees as $emp) {
                $url = route('hr.employees.index', ['q' => $emp->name]);
                $results[] = [
                    'category' => 'Employees',
                    'title' => (string) $emp->name,
                    'subtitle' => (string) ($emp->department.' · '.($emp->position ?? 'Staff')),
                    'url' => $url,
                    'icon' => 'o-identification',
                ];
            }

            // 3. Search Projects
            $projects = Project::query()
                ->where(function ($query) use ($q) {
                    $query->where('name', 'like', "%{$q}%")
                        ->orWhere('location', 'like', "%{$q}%");
                })
                ->take(4)
                ->get();

            foreach ($projects as $proj) {
                $results[] = [
                    'category' => 'Projects',
                    'title' => (string) $proj->name,
                    'subtitle' => 'Budget: ETB '.number_format((float) $proj->budget, 2).' · Location: '.($proj->location ?? 'N/A'),
                    'url' => route('finance.projects.index'),
                    'icon' => 'o-briefcase',
                ];
            }

            // 4. Search Inventory Items
            $items = InventoryItem::query()
                ->where(function ($query) use ($q) {
                    $query->where('name', 'like', "%{$q}%")
                        ->orWhere('item_no', 'like', "%{$q}%");
                })
                ->take(4)
                ->get();

            foreach ($items as $item) {
                $results[] = [
                    'category' => 'Inventory',
                    'title' => "{$item->item_no} - {$item->name}",
                    'subtitle' => "Stock: {$item->quantity} {$item->unit_of_measurement} · Location: {$item->store_location}",
                    'url' => route('inventory.items.index', ['q' => $item->item_no]),
                    'icon' => 'o-cube',
                ];
            }

            // 5. Search Heavy Machinery
            $equipment = \App\Models\Equipment::query()
                ->where(function ($query) use ($q) {
                    $query->where('name', 'like', "%{$q}%")
                        ->orWhere('plate_number', 'like', "%{$q}%")
                        ->orWhere('type', 'like', "%{$q}%");
                })
                ->take(4)
                ->get();

            foreach ($equipment as $machinery) {
                $results[] = [
                    'category' => 'Fleet & Equipment',
                    'title' => "{$machinery->name} ({$machinery->plate_number})",
                    'subtitle' => "Type: {$machinery->type} · Status: {$machinery->status} · Meter: {$machinery->operating_hours}h",
                    'url' => route('equipment.index', ['q' => $machinery->name]),
                    'icon' => 'o-truck',
                ];
            }
        }

        return $results;
    }

    /**
     * Navigation catalog filtered by permissions.
     *
     * @param  \App\Models\User  $user
     * @return array<int, array{category: string, title: string, subtitle: string, url: string, icon: string}>
     */
    private function getNavigationRoutes($user): array
    {
        $isAdmin = $user->hasRole('Administrator') || $user->hasRole('Admin');
        $isHr = $user->hasRole('Human Resource Manager') || $user->hasRole('HumanResourceManager');
        $isInv = $user->hasRole('Inventory Manager') || $user->hasRole('InventoryManager');
        $isFin = $user->hasRole('Financial Manager') || $user->hasRole('FinancialManager');

        $nav = [];

        // Global Dashboard
        $nav[] = ['category' => 'Navigation', 'title' => 'Dashboard Overview', 'subtitle' => 'System dashboard home', 'url' => route($user->getDashboardRouteName()), 'icon' => 'o-home'];

        // Engineering & Fleet
        $nav[] = ['category' => 'Engineering & Fleet', 'title' => 'Heavy Equipment & Fleet Tracking', 'subtitle' => 'Excavators, mixers, trucks, and service alerts', 'url' => route('equipment.index'), 'icon' => 'o-truck'];
        $nav[] = ['category' => 'Engineering & Fleet', 'title' => 'Site Daily Progress Reports (DPR)', 'subtitle' => 'Daily diaries, manpower, and work logs', 'url' => route('projects.daily-reports.index'), 'icon' => 'o-document-chart-bar'];

        if ($isAdmin || $isHr) {
            $nav[] = ['category' => 'Human Resources', 'title' => 'Employee Directory', 'subtitle' => 'Manage staff and profiles', 'url' => route('hr.employees.index'), 'icon' => 'o-users'];
            $nav[] = ['category' => 'Human Resources', 'title' => 'Attendance Management', 'subtitle' => 'Daily check-in and batch attendance', 'url' => route('hr.attendance.index'), 'icon' => 'o-clock'];
            $nav[] = ['category' => 'Human Resources', 'title' => 'Leave Requests', 'subtitle' => 'Staff leave applications', 'url' => route('hr.leaves.index'), 'icon' => 'o-calendar'];
        }

        if ($isAdmin || $isInv) {
            $nav[] = ['category' => 'Inventory', 'title' => 'Inventory Items & Catalog', 'subtitle' => 'Stock tracking & storage units', 'url' => route('inventory.items.index'), 'icon' => 'o-cube'];
            $nav[] = ['category' => 'Inventory', 'title' => 'Item Loans & Lending', 'subtitle' => 'Borrowing requests and returns', 'url' => route('inventory.loans.index'), 'icon' => 'o-arrow-path'];
            $nav[] = ['category' => 'Inventory', 'title' => 'Vendors & Suppliers', 'subtitle' => 'Material supplier profiles', 'url' => route('inventory.vendors.index'), 'icon' => 'o-truck'];
            $nav[] = ['category' => 'Inventory', 'title' => 'Inventory Audit Logs', 'subtitle' => 'Historical stock movement trace', 'url' => route('inventory.logs.index'), 'icon' => 'o-document-text'];
        }

        if ($isAdmin || $isFin) {
            $nav[] = ['category' => 'Finance', 'title' => 'Projects & Budgets', 'subtitle' => 'Project allocation and portfolio', 'url' => route('finance.projects.index'), 'icon' => 'o-briefcase'];
            $nav[] = ['category' => 'Finance', 'title' => 'Expense Records', 'subtitle' => 'Field expenditures and receipts', 'url' => route('finance.expenses.index'), 'icon' => 'o-credit-card'];
        }

        if ($isAdmin) {
            $nav[] = ['category' => 'Approvals', 'title' => 'Inventory Loan Approvals', 'subtitle' => 'Authorize item checkout requests', 'url' => route('admin.requests.items'), 'icon' => 'o-clipboard-document-check'];
            $nav[] = ['category' => 'Approvals', 'title' => 'Expense Approvals', 'subtitle' => 'Review project cash requisitions', 'url' => route('admin.requests.finance'), 'icon' => 'o-banknotes'];
            $nav[] = ['category' => 'Approvals', 'title' => 'Leave Approvals', 'subtitle' => 'Authorize staff leave applications', 'url' => route('admin.requests.leave-approvals.index'), 'icon' => 'o-calendar-days'];
            $nav[] = ['category' => 'System Administration', 'title' => 'User Management', 'subtitle' => 'Provision accounts and roles', 'url' => route('admin.users.index'), 'icon' => 'o-shield-check'];
            $nav[] = ['category' => 'System Administration', 'title' => 'System Settings', 'subtitle' => 'Company profile, currency & params', 'url' => route('admin.system-settings.index'), 'icon' => 'o-cog-6-tooth'];
            $nav[] = ['category' => 'System Administration', 'title' => 'System Maintenance', 'subtitle' => 'Caches, logs, backups & health', 'url' => route('admin.maintenance.index'), 'icon' => 'o-wrench-screwdriver'];
            $nav[] = ['category' => 'System Administration', 'title' => 'Trash Vault', 'subtitle' => 'Recover deleted assets and records', 'url' => route('admin.trash.index'), 'icon' => 'o-trash'];
        }

        return $nav;
    }

    public function render(): View
    {
        return view('livewire.components.command-palette', [
            'results' => $this->getResultsProperty(),
        ]);
    }
}
