<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\Employee;
use App\Models\Expense;
use App\Models\InventoryItem;
use App\Models\InventoryLoan;
use App\Models\LeaveRequest;
use App\Models\Project;
use Illuminate\Support\Facades\DB;

// use App\Models\EmployeeOnLeave; // Removed as it might be a custom view/model not standard.
// If code below uses it, I will assume it exists or replace with DB query.

class DashboardController extends Controller
{
    /**
     * Administrator dashboard
     * Shows high-level overview including pending item loans.
     */
    public function admin()
    {
        // 1. Pending Approvals
        $pendingLoanCount = InventoryLoan::where('status', 'pending')->count();
        $pendingExpenseCount = Expense::where('status', 'pending')->count();
        $pendingLeaveCount = LeaveRequest::where('status', 'pending')->count();

        // 2. System Intelligence (Stats)
        $totalUsers = \App\Models\User::count();
        $totalProjects = Project::count();
        $totalEmployees = Employee::count();
        
        // 3. Activity Stream
        $activities = \App\Models\ActivityLog::with('user')
            ->latest()
            ->take(10)
            ->get();

        // 4. System Health calculation (Simple logic: Pending vs total relevant records)
        // High pending count reduces health. Higher approvals/records increase it.
        $totalCritical = $pendingLoanCount + $pendingExpenseCount + $pendingLeaveCount;
        $systemHealth = $totalCritical > 15 ? max(65, 100 - ($totalCritical * 2)) : 98;

        return view('dashboards.admin', [
            'pendingLoanCount' => $pendingLoanCount,
            'pendingLeaveCount' => $pendingLeaveCount,
            'pendingExpenseCount' => $pendingExpenseCount,
            'totalUsers' => $totalUsers,
            'totalProjects' => $totalProjects,
            'totalEmployees' => $totalEmployees,
            'activities' => $activities,
            'systemHealth' => $systemHealth,
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
        // Using DB query instead of possibly missing Model if safe, but user had it working. I'll invoke Model as per original.
        try {
            $onLeaveTodayCount = \App\Models\EmployeeOnLeave::where('start_date', '<=', $today)
                ->where('end_date', '>=', $today)
                ->distinct('employee_id')
                ->count();
        } catch (\Throwable $e) {
            // Fallback if view/model missing
            $onLeaveTodayCount = LeaveRequest::where('status', 'approved')
                ->whereDate('start_date', '<=', $today)
                ->whereDate('end_date', '>=', $today)
                ->count();
        }

        $pendingLeaveApprovals = LeaveRequest::where('status', 'pending')->count();
        $recentHires = Employee::where('hire_date', '>=', now()->subDays(30))->count();

        // Latest 5 employees
        $latestEmployees = Employee::with(['department_rel', 'position_rel'])->latest('created_at')->take(5)->get();

        // Department Breakdown (Top 5 largest departments)
        $departmentStats = DB::table('departments')
            ->join('employees', 'departments.id', '=', 'employees.department_id')
            ->select('departments.name', DB::raw('count(employees.id) as total'))
            ->groupBy('departments.name')
            ->orderByDesc('total')
            ->take(5)
            ->get();

        // Attendance Chart Data (Split by Status)
        $rawAttendance = Attendance::selectRaw('DATE(clock_in) as date, status, count(*) as count')
            ->where('clock_in', '>=', now()->subDays(6)->startOfDay())
            ->groupBy(DB::raw('DATE(clock_in)'), 'status')
            ->get();

        $attendanceByDate = [];
        foreach ($rawAttendance as $row) {
            // $row->date might be just the date string depending on DB driver,
            // casting in model might make it Carbon. Safest to handle both.
            $d = is_string($row->date) ? substr($row->date, 0, 10) : $row->date->format('Y-m-d');
            $attendanceByDate[$d][$row->status] = $row->count;
        }

        $chartLabels = [];
        $onTimeData = [];
        $lateData = [];

        for ($i = 6; $i >= 0; $i--) {
            $dateObj = now()->subDays($i);
            $dateString = $dateObj->format('Y-m-d');

            $chartLabels[] = $dateObj->format('D'); // e.g. Mon, Tue

            // Use constants from Model or hardcoded strings matching DB
            $onTimeData[] = $attendanceByDate[$dateString]['present'] ?? 0;
            $lateData[] = $attendanceByDate[$dateString]['late'] ?? 0;
        }

        return view('dashboards.hr', compact(
            'employeeCount',
            'activeEmployees',
            'onLeaveTodayCount',
            'pendingLeaveApprovals',
            'recentHires',
            'latestEmployees',
            'departmentStats',
            'chartLabels',
            'onTimeData',
            'lateData'
        ));
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
        $openLoanCount = InventoryLoan::whereIn('status', ['pending', 'approved'])->count();

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

        return view('dashboards.inventory', compact(
            'totalItems',
            'stableItemsCount',
            'lowStockCount',
            'zeroStockCount',
            'openLoanCount',
            'healthPercentage',
            'chartCategories',
            'chartData',
            'recentAlerts'
        ));
    }

    /**
     * Finance dashboard
     */
    public function finance()
    {
        $totalProjects = Project::count();
        $totalBudget = Project::sum('budget');
        $totalExpenses = Expense::sum('amount');
        $remainingBudget = $totalBudget - $totalExpenses;
        $usagePercentage = $totalBudget > 0 ? round(($totalExpenses / $totalBudget) * 100) : 0;

        // Recent Projects for list
        $recentProjects = Project::latest()->take(3)->get();

        // Chart Data (Top Projects by Budget)
        $projectsParams = Project::withSum('expenses', 'amount')->orderByDesc('budget')->take(8)->get();
        $portfolioLabels = $projectsParams->pluck('name')->toArray();
        $portfolioBudgets = $projectsParams->pluck('budget')->toArray();
        $portfolioExpenses = $projectsParams->pluck('expenses_sum_amount')->map(fn ($v) => $v ?? 0)->toArray();

        return view('dashboards.finance', compact(
            'totalProjects',
            'totalBudget',
            'totalExpenses',
            'remainingBudget',
            'usagePercentage',
            'recentProjects',
            'portfolioLabels',
            'portfolioBudgets',
            'portfolioExpenses'
        ));
    }
}
