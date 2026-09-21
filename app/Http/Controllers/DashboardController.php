<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Enums\ExpenseStatus;
use App\Enums\LeaveStatus;
use App\Enums\LoanStatus;
use App\Models\Attendance;
use App\Models\Employee;
use App\Models\Expense;
use App\Models\InventoryItem;
use App\Models\InventoryLoan;
use App\Models\LeaveRequest;
use App\Models\Project;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    /**
     * Administrator dashboard
     * Shows high-level overview including pending item loans.
     */
    public function admin()
    {
        // 1. Pending Approvals
        $pendingLoanCount = InventoryLoan::where('status', LoanStatus::Pending->value)->count();
        $pendingExpenseCount = Expense::where('status', ExpenseStatus::Pending->value)->count();
        $pendingExpenseAmount = (float) Expense::where('status', ExpenseStatus::Pending->value)->sum('amount');
        $pendingLeaveCount = LeaveRequest::where('status', LeaveStatus::Pending->value)->count();

        // 2. System Intelligence (Stats)
        $totalUsers = \App\Models\User::count();
        $totalProjects = Project::count();
        $totalEmployees = Employee::count();
        $totalItems = InventoryItem::count();
        $activeLoans = InventoryLoan::where('status', LoanStatus::Approved->value)->count();
        $presentToday = Attendance::whereDate('date', now()->toDateString())
            ->whereIn('status', ['present', 'Present'])
            ->count();

        // 2. Heavy Metric Aggregates (Cached for 180s for performance)
        $cachedMetrics = \Illuminate\Support\Facades\Cache::remember('admin_dashboard_aggregates', 180, function () use ($pendingExpenseCount) {
            $totalBudget = (float) Project::sum('budget');
            $totalSpent = (float) Expense::where('status', ExpenseStatus::Approved->value)->sum('amount');
            $financialStats = [
                'total_budget' => $totalBudget,
                'total_spent' => $totalSpent,
                'remaining_budget' => max(0, $totalBudget - $totalSpent),
                'usage_pct' => $totalBudget > 0 ? min(100, round(($totalSpent / $totalBudget) * 100, 1)) : 0,
            ];

            // Top projects with actual allocated vs spent
            $projectsList = Project::with(['expenses' => function ($q) {
                $q->where('status', ExpenseStatus::Approved->value);
            }])->take(5)->get();

            $projectBreakdown = $projectsList->map(function ($p) {
                $spent = (float) $p->expenses->sum('amount');
                $budget = (float) ($p->budget ?? 0);
                $usagePct = $budget > 0 ? min(100, round(($spent / $budget) * 100, 1)) : 0;

                return [
                    'id' => $p->id,
                    'name' => $p->name,
                    'location' => $p->location ?: 'Site Operations',
                    'status' => $p->status ?: 'active',
                    'budget' => round($budget / 1000000, 2),
                    'spent' => round($spent / 1000000, 2),
                    'raw_budget' => $budget,
                    'raw_spent' => $spent,
                    'usage_pct' => $usagePct,
                    'status_label' => $usagePct > 90 ? 'Critical' : ($usagePct > 75 ? 'Caution' : 'On Track'),
                ];
            });

            // Heavy Machinery Fleet Availability
            $fleetStats = [
                'total' => \App\Models\Equipment::count(),
                'operational' => \App\Models\Equipment::whereIn('status', ['operational', 'active'])->count(),
                'maintenance' => \App\Models\Equipment::whereIn('status', ['maintenance', 'under_maintenance', 'in_service'])->count(),
                'standby' => \App\Models\Equipment::where('status', 'standby')->count(),
            ];

            // Expense Categories Breakdown
            $expenseCategories = DB::table('expenses')
                ->where('status', ExpenseStatus::Approved->value)
                ->whereNull('deleted_at')
                ->select('category', DB::raw('SUM(amount) as total'))
                ->groupBy('category')
                ->orderByDesc('total')
                ->get()
                ->map(fn ($c) => [
                    'category' => ucfirst((string) $c->category),
                    'total' => (float) $c->total,
                    'total_formatted' => number_format((float) $c->total, 0),
                ]);

            // Monthly Cash Flow (Dynamic Trailing 6 Months)
            $cashFlowChartData = [];
            $monthlyBaseline = $totalBudget > 0 ? round($totalBudget / 12, 2) : 500000;

            for ($i = 5; $i >= 0; $i--) {
                $monthDate = now()->subMonths($i);
                $year = $monthDate->year;
                $month = $monthDate->month;

                $monthSpent = (float) Expense::whereIn('status', ['approved', 'Approved', ExpenseStatus::Approved->value])
                    ->whereYear('expense_date', $year)
                    ->whereMonth('expense_date', $month)
                    ->whereNull('deleted_at')
                    ->sum('amount');

                $cashFlowChartData[] = [
                    'month' => $monthDate->format('M'),
                    'actual' => round($monthSpent, 2),
                    'baseline' => $monthlyBaseline,
                ];
            }

            // Dynamic Enterprise Risk Score
            $criticalProjectsCount = Project::with('expenses')->get()->filter(function ($p) {
                $spent = (float) $p->expenses->where('status', ExpenseStatus::Approved->value)->sum('amount');
                $budget = (float) ($p->budget ?? 0);
                return $budget > 0 && ($spent / $budget) >= 0.85;
            })->count();

            $breakdownMachinery = \App\Models\Equipment::whereIn('status', ['breakdown', 'maintenance', 'under_maintenance'])->count();
            $lowStockItems = 0;
            try {
                $lowStockItems = InventoryItem::where('quantity', '<=', 0)->count();
            } catch (\Throwable $e) {
                $lowStockItems = 0;
            }

            $riskPoints = 1.0;
            if ($pendingExpenseCount > 0) $riskPoints += min(2.5, $pendingExpenseCount * 0.4);
            if ($criticalProjectsCount > 0) $riskPoints += min(3.0, $criticalProjectsCount * 1.0);
            if ($breakdownMachinery > 0) $riskPoints += min(2.0, $breakdownMachinery * 0.5);
            if ($lowStockItems > 0) $riskPoints += min(1.5, $lowStockItems * 0.3);

            $riskScore = round(min(9.9, max(1.2, $riskPoints)), 1);
            $riskLevel = $riskScore >= 7.0 ? 'High' : ($riskScore >= 4.0 ? 'Moderate' : 'Low');

            return compact(
                'financialStats',
                'projectBreakdown',
                'fleetStats',
                'expenseCategories',
                'cashFlowChartData',
                'riskScore',
                'riskLevel'
            );
        });

        $financialStats = $cachedMetrics['financialStats'];
        $projectBreakdown = $cachedMetrics['projectBreakdown'];
        $fleetStats = $cachedMetrics['fleetStats'];
        $expenseCategories = $cachedMetrics['expenseCategories'];
        $cashFlowChartData = $cachedMetrics['cashFlowChartData'];
        $riskScore = $cachedMetrics['riskScore'];
        $riskLevel = $cachedMetrics['riskLevel'];

        // 7. Recent Core Module Feeds
        $recentExpenses = Expense::with(['project', 'user'])->latest('id')->take(6)->get();
        $recentLoans = InventoryLoan::with(['item', 'employee.user'])->latest('id')->take(6)->get();
        $recentEmployees = Employee::latest('id')->take(6)->get();
        $recentLeaves = LeaveRequest::with(['employee.user'])->latest('id')->take(6)->get();

        // 8. Department Workforce Distribution
        $departmentStats = \App\Models\Department::has('employees')
            ->withCount('employees')
            ->get()
            ->map(fn ($d) => (object) [
                'department' => $d->name,
                'total' => $d->employees_count,
            ]);

        // 9. Inventory Utilization
        $inventoryUtilization = $totalItems > 0 ? round(($activeLoans / max(1, $totalItems)) * 100) : 0;

        // 10. Activity Stream
        $activities = \App\Models\ActivityLog::with('user')
            ->latest()
            ->take(6)
            ->get();

        // 11. System Health calculation
        $totalCritical = $pendingLoanCount + $pendingExpenseCount + $pendingLeaveCount;
        $systemHealth = $totalCritical > 15 ? max(65, 100 - ($totalCritical * 2)) : 98;

        return Inertia::render('Dashboards/AdminDashboard', [
            'pendingLoanCount' => $pendingLoanCount,
            'pendingLeaveCount' => $pendingLeaveCount,
            'pendingExpenseCount' => $pendingExpenseCount,
            'pendingExpenseAmount' => $pendingExpenseAmount,
            'totalUsers' => $totalUsers,
            'totalProjects' => $totalProjects,
            'totalEmployees' => $totalEmployees,
            'totalItems' => $totalItems,
            'activeLoans' => $activeLoans,
            'presentToday' => $presentToday,
            'financialStats' => $financialStats,
            'projectBreakdown' => $projectBreakdown,
            'expenseCategories' => $expenseCategories,
            'monthlyCashFlow' => $cashFlowChartData,
            'inventoryUtilization' => $inventoryUtilization,
            'recentExpenses' => $recentExpenses,
            'recentLoans' => $recentLoans,
            'recentEmployees' => $recentEmployees,
            'recentLeaves' => $recentLeaves,
            'departmentStats' => $departmentStats,
            'activities' => $activities,
            'systemHealth' => $systemHealth,
            'fleetStats' => $fleetStats,
            'riskScore' => $riskScore,
            'riskLevel' => $riskLevel,
        ]);
    }

    /**
     * Human Resource dashboard
     */
    public function hr()
    {
        $employeeCount = Employee::count();
        $activeEmployees = Employee::where('status', 'Active')->count();

        // Calculate real-time "On Leave Today"
        $today = now()->toDateString();
        try {
            $onLeaveTodayCount = \App\Models\EmployeeOnLeave::where('start_date', '<=', $today)
                ->where('end_date', '>=', $today)
                ->distinct('employee_id')
                ->count();
        } catch (\Throwable $e) {
            // Fallback if view/model missing
            $onLeaveTodayCount = LeaveRequest::where('status', LeaveStatus::Approved->value)
                ->whereDate('start_date', '<=', $today)
                ->whereDate('end_date', '>=', $today)
                ->count();
        }

        $pendingLeaveApprovals = LeaveRequest::where('status', LeaveStatus::Pending->value)->count();
        $recentHires = Employee::where('hire_date', '>=', now()->subDays(30))->count();

        // Latest 6 employees
        $latestEmployees = Employee::with(['department_rel', 'position_rel'])->latest('created_at')->take(6)->get();

        // Pending Leave Requests queue with employee details
        $pendingLeaves = LeaveRequest::with(['employee.department_rel', 'employee.user'])
            ->where('status', LeaveStatus::Pending->value)
            ->latest('id')
            ->take(6)
            ->get();

        // Department Breakdown
        $departmentStats = DB::table('departments')
            ->join('employees', 'departments.id', '=', 'employees.department_id')
            ->select('departments.name', DB::raw('count(employees.id) as total'))
            ->groupBy('departments.name')
            ->orderByDesc('total')
            ->get();

        // Attendance stats for today
        $todayAttendance = Attendance::whereDate('clock_in', $today)->get();
        $presentToday = $todayAttendance->whereIn('status', ['present', 'Present'])->count();
        $lateToday = $todayAttendance->whereIn('status', ['late', 'Late'])->count();
        $totalClockedToday = $todayAttendance->count();
        $attendanceRate = $activeEmployees > 0 ? min(100, round(($totalClockedToday / $activeEmployees) * 100)) : 100;
        $punctualityRate = $totalClockedToday > 0 ? round(($presentToday / $totalClockedToday) * 100) : 95;

        // Attendance 7-Day Chart Data (Split by Status)
        $rawAttendance = Attendance::selectRaw('CAST(clock_in AS DATE) as date, status, count(*) as count')
            ->where('clock_in', '>=', now()->subDays(6)->startOfDay())
            ->groupBy(DB::raw('CAST(clock_in AS DATE)'), 'status')
            ->get();

        $attendanceByDate = [];
        foreach ($rawAttendance as $row) {
            $d = is_string($row->date) ? substr($row->date, 0, 10) : $row->date->format('Y-m-d');
            $attendanceByDate[$d][strtolower((string) $row->status)] = (int) $row->count;
        }

        $chartLabels = [];
        $onTimeData = [];
        $lateData = [];
        $attendanceDailyTotals = [];

        for ($i = 6; $i >= 0; $i--) {
            $dateObj = now()->subDays($i);
            $dateString = $dateObj->format('Y-m-d');

            $chartLabels[] = $dateObj->format('D');
            $onTime = $attendanceByDate[$dateString]['present'] ?? 0;
            $late = $attendanceByDate[$dateString]['late'] ?? 0;

            $onTimeData[] = $onTime;
            $lateData[] = $late;
            $attendanceDailyTotals[] = [
                'day' => $dateObj->format('D'),
                'date' => $dateObj->format('M j'),
                'onTime' => $onTime,
                'late' => $late,
                'total' => $onTime + $late,
            ];
        }

        return Inertia::render('Dashboards/HrDashboard', [
            'employeeCount' => $employeeCount,
            'activeEmployees' => $activeEmployees,
            'onLeaveTodayCount' => $onLeaveTodayCount,
            'pendingLeaveApprovals' => $pendingLeaveApprovals,
            'recentHires' => $recentHires,
            'latestEmployees' => $latestEmployees,
            'pendingLeaves' => $pendingLeaves,
            'departmentStats' => $departmentStats,
            'chartLabels' => $chartLabels,
            'onTimeData' => $onTimeData,
            'lateData' => $lateData,
            'attendanceDailyTotals' => $attendanceDailyTotals,
            'presentToday' => $presentToday,
            'lateToday' => $lateToday,
            'attendanceRate' => $attendanceRate,
            'punctualityRate' => $punctualityRate,
        ]);
    }

    /**
     * Inventory dashboard
     */
    public function inventory()
    {
        // Mutually exclusive categories for structural integrity
        $stableItemsCount = InventoryItem::where('quantity', '>', 5)->count();
        $lowStockCount = InventoryItem::where('quantity', '>', 0)
            ->where('quantity', '<=', 5)
            ->count();
        $zeroStockCount = InventoryItem::where('quantity', '<=', 0)->count();

        $totalItems = $stableItemsCount + $lowStockCount + $zeroStockCount;

        // Open loans: active commitments
        $pendingLoanCount = InventoryLoan::where('status', LoanStatus::Pending->value)->count();
        $openLoanCount = InventoryLoan::whereIn('status', [LoanStatus::Pending->value, LoanStatus::Approved->value])->count();

        // Health = % of catalog that is "Stable" (Stock > 5)
        $healthPercentage = $totalItems > 0 ? round(($stableItemsCount / $totalItems) * 100) : 0;

        // Chart Data (Top 10 Items by Quantity) - excluding zero stock for visibility
        $topItems = InventoryItem::where('quantity', '>', 0)->orderByDesc('quantity')->take(10)->get();
        $chartCategories = $topItems->pluck('name')->toArray();
        $chartData = $topItems->pluck('quantity')->toArray();

        // Critical Alerts: Top 5 items with lowest quantity (but > 0)
        $recentAlerts = InventoryItem::where('quantity', '>', 0)
            ->where('quantity', '<=', 5)
            ->orderBy('quantity', 'asc')
            ->take(5)
            ->get();
        $zeroStockItems = InventoryItem::where('quantity', '<=', 0)->take(5)->get();

        // Heavy Machinery & Equipment Fleet
        $fleetTotal = \App\Models\Equipment::count();
        $fleetOperational = \App\Models\Equipment::whereIn('status', ['operational', 'active'])->count();
        $fleetMaintenance = \App\Models\Equipment::whereIn('status', ['maintenance', 'under_maintenance', 'in_service'])->count();
        $fleetStandby = \App\Models\Equipment::where('status', 'standby')->count();
        $fleetServiceDue = \App\Models\Equipment::whereNotNull('next_service_hours')
            ->whereRaw('operating_hours >= (next_service_hours - 25)')
            ->count();

        // Active Loans Stream with employee, item details
        $activeLoansList = InventoryLoan::with(['item', 'employee.user', 'employee.department_rel'])
            ->whereIn('status', [LoanStatus::Pending->value, LoanStatus::Approved->value])
            ->latest('id')
            ->take(6)
            ->get();

        // Category breakdown
        $categoryBreakdown = InventoryItem::select('category', DB::raw('count(*) as count'), DB::raw('SUM(quantity) as total_qty'))
            ->groupBy('category')
            ->orderByDesc('count')
            ->take(6)
            ->get()
            ->map(fn ($c) => [
                'category' => ucfirst((string) ($c->category ?: 'General Hardware')),
                'count' => (int) $c->count,
                'total_qty' => (float) $c->total_qty,
            ]);

        return Inertia::render('Dashboards/InventoryDashboard', [
            'totalItems' => $totalItems,
            'stableItemsCount' => $stableItemsCount,
            'lowStockCount' => $lowStockCount,
            'zeroStockCount' => $zeroStockCount,
            'openLoanCount' => $openLoanCount,
            'pendingLoanCount' => $pendingLoanCount,
            'healthPercentage' => $healthPercentage,
            'topItems' => $topItems,
            'chartCategories' => $chartCategories,
            'chartData' => $chartData,
            'recentAlerts' => $recentAlerts,
            'zeroStockItems' => $zeroStockItems,
            'fleetTotal' => $fleetTotal,
            'fleetOperational' => $fleetOperational,
            'fleetMaintenance' => $fleetMaintenance,
            'fleetStandby' => $fleetStandby,
            'fleetServiceDue' => $fleetServiceDue,
            'activeLoansList' => $activeLoansList,
            'categoryBreakdown' => $categoryBreakdown,
        ]);
    }

    /**
     * Finance dashboard
     */
    public function finance()
    {
        $totalProjects = Project::count();
        $totalBudget = (float) Project::sum('budget');
        $totalExpenses = (float) Expense::whereIn('status', [ExpenseStatus::Approved->value, 'approved', 'Approved'])->sum('amount');
        $remainingBudget = max(0, $totalBudget - $totalExpenses);
        $usagePercentage = $totalBudget > 0 ? min(100, round(($totalExpenses / $totalBudget) * 100, 1)) : 0;

        // Pending expenses queue
        $pendingExpenses = Expense::with(['project', 'user'])
            ->where('status', ExpenseStatus::Pending->value)
            ->latest('id')
            ->take(6)
            ->get();
        $pendingExpenseCount = Expense::where('status', ExpenseStatus::Pending->value)->count();
        $pendingExpenseAmount = (float) Expense::where('status', ExpenseStatus::Pending->value)->sum('amount');

        // Recent Approved Transactions with vouchers
        $recentTransactions = Expense::with(['project', 'user'])
            ->whereIn('status', [ExpenseStatus::Approved->value, 'approved', 'Approved'])
            ->latest('expense_date')
            ->take(6)
            ->get();

        // Expense Categories Breakdown
        $expenseCategories = DB::table('expenses')
            ->whereIn('status', [ExpenseStatus::Approved->value, 'approved', 'Approved'])
            ->whereNull('deleted_at')
            ->select('category', DB::raw('SUM(amount) as total'))
            ->groupBy('category')
            ->orderByDesc('total')
            ->get()
            ->map(fn ($c) => [
                'category' => ucfirst((string) ($c->category ?: 'General')),
                'total' => (float) $c->total,
                'total_formatted' => number_format((float) $c->total, 2),
            ]);

        // Trailing 6-month monthly cash outflow
        $monthlyCashFlow = [];
        $monthlyBaseline = $totalBudget > 0 ? round($totalBudget / 12, 2) : 500000;
        for ($i = 5; $i >= 0; $i--) {
            $monthDate = now()->subMonths($i);
            $monthSpent = (float) Expense::whereIn('status', [ExpenseStatus::Approved->value, 'approved', 'Approved'])
                ->whereYear('expense_date', $monthDate->year)
                ->whereMonth('expense_date', $monthDate->month)
                ->whereNull('deleted_at')
                ->sum('amount');

            $monthlyCashFlow[] = [
                'month' => $monthDate->format('M'),
                'actual' => round($monthSpent, 2),
                'baseline' => $monthlyBaseline,
            ];
        }

        // Projects with actual allocated vs spent
        $projectsParams = Project::with(['expenses' => function ($q) {
            $q->whereIn('status', [ExpenseStatus::Approved->value, 'approved', 'Approved']);
        }])->orderByDesc('budget')->take(8)->get();

        $projectBreakdown = $projectsParams->map(function ($p) {
            $spent = (float) $p->expenses->sum('amount');
            $budget = (float) ($p->budget ?? 0);
            $usagePct = $budget > 0 ? min(100, round(($spent / $budget) * 100, 1)) : 0;

            return [
                'id' => $p->id,
                'name' => $p->name,
                'location' => $p->location ?: 'Site Operations',
                'status' => $p->status ?: 'active',
                'budget' => $budget,
                'spent' => $spent,
                'remaining' => max(0, $budget - $spent),
                'usage_pct' => $usagePct,
                'status_label' => $usagePct > 90 ? 'Critical' : ($usagePct > 75 ? 'Caution' : 'On Track'),
            ];
        });

        // 30-Day Outflow run rate
        $last30DaysSpend = (float) Expense::whereIn('status', [ExpenseStatus::Approved->value, 'approved', 'Approved'])
            ->where('expense_date', '>=', now()->subDays(30))
            ->whereNull('deleted_at')
            ->sum('amount');
        $avgWeeklyBurn = round($last30DaysSpend / 4.2, 2);

        return Inertia::render('Dashboards/FinanceDashboard', [
            'totalProjects' => $totalProjects,
            'totalBudget' => $totalBudget,
            'totalExpenses' => $totalExpenses,
            'remainingBudget' => $remainingBudget,
            'usagePercentage' => $usagePercentage,
            'avgWeeklyBurn' => $avgWeeklyBurn,
            'pendingExpenseCount' => $pendingExpenseCount,
            'pendingExpenseAmount' => $pendingExpenseAmount,
            'pendingExpenses' => $pendingExpenses,
            'recentTransactions' => $recentTransactions,
            'expenseCategories' => $expenseCategories,
            'monthlyCashFlow' => $monthlyCashFlow,
            'projectBreakdown' => $projectBreakdown,
            'portfolioLabels' => $projectBreakdown->pluck('name')->toArray(),
            'portfolioBudgets' => $projectBreakdown->pluck('budget')->toArray(),
            'portfolioExpenses' => $projectBreakdown->pluck('spent')->toArray(),
            'recentProjects' => Project::latest()->take(3)->get(),
        ]);
    }
}
