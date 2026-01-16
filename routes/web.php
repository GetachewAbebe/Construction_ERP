<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

use App\Http\Controllers\SimpleAuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\AdminUserController;

use App\Http\Controllers\HR\EmployeeController;
use App\Http\Controllers\HR\LeaveRequestController;
use App\Http\Controllers\Admin\LeaveApprovalController;

use App\Http\Controllers\Inventory\InventoryItemController;
use App\Http\Controllers\Inventory\InventoryLoanController;
use App\Http\Controllers\Admin\InventoryLoanApprovalController;

use App\Http\Controllers\HomeController;
use App\Http\Controllers\HR\ApprovedLeavesController;

/**
 * --------------------------------------------------------------------------
 * HOME = LOGIN PAGE (with redirect if already authenticated)
 * --------------------------------------------------------------------------
 * GET  /  -> if guest  -> show login page (home.blade.php)
 *          if authed -> redirect to dashboard based on role
 * POST /  -> perform login and redirect based on role
 */

Route::get('/', HomeController::class)->name('home');

// Login submit
Route::post('/', [SimpleAuthController::class, 'login'])->name('login');

/**
 * --------------------------------------------------------------------------
 * OPTIONAL PUBLIC PAGES
 * --------------------------------------------------------------------------
 */
Route::view('/about', 'about')->name('about');

/**
 * --------------------------------------------------------------------------
 * LOGOUT
 * --------------------------------------------------------------------------
 * POST /logout → logs out and redirects to "/"
 */
Route::post('/logout', [SimpleAuthController::class, 'logout'])
    ->middleware('auth')
    ->name('logout');

/**
 * --------------------------------------------------------------------------
 * PROFILE PICTURE SERVING (Symlink-Independent)
 * --------------------------------------------------------------------------
 * Serves employee profile pictures directly from storage
 * Works even without public/storage symlink
 */
Route::get('/employee/profile-picture', [App\Http\Controllers\ProfilePictureController::class, 'show'])
    ->name('employee.profile-picture');


// Notifications
Route::middleware('auth')->group(function () {
    Route::get('/notifications', function () {
        $user = auth()->user();
        if ($user->hasRole('Administrator')) return redirect()->route('admin.notifications');
        if ($user->hasRole('HumanResourceManager')) return redirect()->route('hr.notifications');
        if ($user->hasRole('InventoryManager')) return redirect()->route('inventory.notifications');
        if ($user->hasRole('FinancialManager')) return redirect()->route('finance.notifications');
        
        return redirect()->route('home'); // Fallback
    })->name('notifications.index');
    Route::post('/notifications/{id}/mark-as-read', [App\Http\Controllers\NotificationController::class, 'markAsRead'])->name('notifications.mark-as-read');
    Route::post('/notifications/mark-all-as-read', [App\Http\Controllers\NotificationController::class, 'markAllAsRead'])->name('notifications.mark-all-as-read');
});

/**
 * --------------------------------------------------------------------------
 * ADMINISTRATOR AREA
 * Roles: Administrator, HumanResourceManager, InventoryManager, FinancialManager
 * --------------------------------------------------------------------------
 */
Route::middleware([
    'auth',
    // ✅ allow all management roles into the /admin area
    'role:Administrator,Admin,Human Resource Manager,Inventory Manager,Financial Manager',
    'prevent-back-history',
])->group(function () {

    // Admin dashboards
    Route::get('/admin',      [DashboardController::class, 'admin'])->name('admin.dashboard');
    Route::get('/admin/home', [DashboardController::class, 'admin'])->name('admin.home');

    // Admin "sections" - reusing the same dashboards but keeping /admin prefix for sidebar context
    Route::get('/admin/hr',        [DashboardController::class, 'hr'])->name('admin.hr');
    Route::get('/admin/inventory', [DashboardController::class, 'inventory'])->name('admin.inventory');
    Route::get('/admin/finance',   [DashboardController::class, 'finance'])->name('admin.finance');
    Route::get('/admin/notifications', [App\Http\Controllers\NotificationController::class, 'index'])->name('admin.notifications');

    /**
     * Admin: requests / approvals (leave, purchases, items, finance)
     */
    Route::prefix('admin/requests')->name('admin.requests.')->group(function () {
        Route::view('/leave',     'admin.requests.leave')->name('leave');
        Route::view('/purchases', 'admin.requests.purchases')->name('purchases');
        Route::get('/finance', [App\Http\Controllers\Admin\ExpenseApprovalController::class, 'index'])->name('finance');
        Route::post('/finance/{expense}/approve', [App\Http\Controllers\Admin\ExpenseApprovalController::class, 'approve'])->name('finance.approve');
        Route::post('/finance/{expense}/reject', [App\Http\Controllers\Admin\ExpenseApprovalController::class, 'reject'])->name('finance.reject');

        Route::get('/items', [InventoryLoanApprovalController::class, 'index'])->name('items');

        // Approve / Reject inventory loans
        Route::post('/items/{loan}/approve', [InventoryLoanApprovalController::class, 'approve'])
            ->name('items.approve');

        Route::post('/items/{loan}/reject', [InventoryLoanApprovalController::class, 'reject'])
            ->name('items.reject');

        // === Leave approvals (already existing) ===
        Route::get('/leave-approvals', [LeaveApprovalController::class, 'index'])
            ->name('leave-approvals.index');

        Route::post('/leave/{leave}/approve', [LeaveApprovalController::class, 'approve'])
            ->name('leave.approve');

        Route::post('/leave/{leave}/reject',  [LeaveApprovalController::class, 'reject'])
            ->name('leave.reject');
    });

    /**
     * Admin Users CRUD
     */
    Route::prefix('admin')->name('admin.')->group(function () {
        Route::get('/users',              [AdminUserController::class, 'index'])->name('users.index');
        Route::get('/users/create',       [AdminUserController::class, 'create'])->name('users.create');
        Route::post('/users',             [AdminUserController::class, 'store'])->name('users.store');
        Route::get('/users/{user}/edit',  [AdminUserController::class, 'edit'])->name('users.edit');
        Route::put('/users/{user}',       [AdminUserController::class, 'update'])->name('users.update');
        Route::delete('/users/{user}',    [AdminUserController::class, 'destroy'])->name('users.destroy');

        // Attendance Settings
        Route::get('/attendance-settings', [App\Http\Controllers\Admin\AttendanceSettingsController::class, 'index'])
            ->name('attendance-settings.index');
        Route::post('/attendance-settings', [App\Http\Controllers\Admin\AttendanceSettingsController::class, 'update'])
            ->name('attendance-settings.update');

        // System Settings
        Route::get('/system-settings', [App\Http\Controllers\Admin\SystemSettingsController::class, 'index'])
            ->name('system-settings.index');
        Route::post('/system-settings', [App\Http\Controllers\Admin\SystemSettingsController::class, 'update'])
            ->name('system-settings.update');

        // Role & Permission Management
        Route::resource('roles', App\Http\Controllers\Admin\RolePermissionController::class)->except(['show']);
        Route::get('/permissions', [App\Http\Controllers\Admin\RolePermissionController::class, 'permissions'])
            ->name('roles.permissions');
        Route::post('/permissions', [App\Http\Controllers\Admin\RolePermissionController::class, 'storePermission'])
            ->name('roles.permissions.store');
        Route::delete('/permissions/{permission}', [App\Http\Controllers\Admin\RolePermissionController::class, 'destroyPermission'])
            ->name('roles.permissions.destroy');

        // Notification Templates
        Route::resource('notification-templates', App\Http\Controllers\Admin\NotificationTemplateController::class)->except(['show']);
        Route::get('/notification-templates/{notificationTemplate}/preview', [App\Http\Controllers\Admin\NotificationTemplateController::class, 'preview'])
            ->name('notification-templates.preview');

        // System Maintenance & Backup
        Route::get('/maintenance', [App\Http\Controllers\Admin\MaintenanceController::class, 'index'])
            ->name('maintenance.index');
        Route::post('/maintenance/clear-cache', [App\Http\Controllers\Admin\MaintenanceController::class, 'clearCache'])
            ->name('maintenance.clear-cache');
        Route::post('/maintenance/optimize', [App\Http\Controllers\Admin\MaintenanceController::class, 'optimizeSystem'])
            ->name('maintenance.optimize');
        Route::post('/maintenance/clear-logs', [App\Http\Controllers\Admin\MaintenanceController::class, 'clearLogs'])
            ->name('maintenance.clear-logs');
        Route::post('/maintenance/create-backup', [App\Http\Controllers\Admin\MaintenanceController::class, 'createBackup'])
            ->name('maintenance.create-backup');
        Route::get('/maintenance/list-backups', [App\Http\Controllers\Admin\MaintenanceController::class, 'listBackups'])
            ->name('maintenance.list-backups');
        Route::get('/maintenance/backup/download/{filename}', [App\Http\Controllers\Admin\MaintenanceController::class, 'downloadBackup'])
            ->name('maintenance.download-backup');

        // Activity Logs
        Route::get('/activity-logs', [App\Http\Controllers\Admin\ActivityLogController::class, 'index'])
            ->name('activity-logs');

        // Trash Recovery
        Route::get('/trash',           [App\Http\Controllers\Admin\TrashController::class, 'index'])->name('trash.index');
        Route::post('/trash/restore',  [App\Http\Controllers\Admin\TrashController::class, 'restore'])->name('trash.restore');
    });

    // === SHADOW ROUTES FOR ADMIN CONTEXT (Read-Only / Management View) ===
    Route::prefix('admin')->name('admin.')->group(function () {
        // HR Shadow
        Route::prefix('hr')->name('hr.')->group(function () {
            Route::get('/employees',      [EmployeeController::class, 'index'])->name('employees.index');
            Route::get('/employees/{employee}/edit', [EmployeeController::class, 'edit'])->name('employees.edit'); // Keep edit accessible for viewing? Or just index. User said "approval and viewer". Let's keep index/show mostly. The controller limits write.
            // Actually, the Views check for Admin role to hide edit buttons. So we can map the generic routes or specific ones. 
            // Lets map index and generic read pages.
            Route::get('/leaves',         [LeaveRequestController::class, 'index'])->name('leaves.index');
            Route::get('/attendance',     [App\Http\Controllers\HR\AttendanceController::class, 'index'])->name('attendance.index');
            Route::get('/leaves/approved', ApprovedLeavesController::class)->name('leaves.approved');
        });

        // Inventory Shadow
        Route::prefix('inventory')->name('inventory.')->group(function () {
            Route::get('/items',          [InventoryItemController::class, 'index'])->name('items.index');
            Route::get('/loans',          [InventoryLoanController::class, 'index'])->name('loans.index');
            Route::get('/logs',           [App\Http\Controllers\Inventory\InventoryLogController::class, 'index'])->name('logs.index');
        });

        // Finance Shadow
        Route::prefix('finance')->name('finance.')->group(function () {
            // Projects
            Route::resource('projects', App\Http\Controllers\Finance\ProjectController::class);

            // Expenses
            Route::resource('expenses', App\Http\Controllers\Finance\ExpenseController::class);
            
            // Expense Approval Workflow
            Route::post('/expenses/{expense}/approve', [App\Http\Controllers\Finance\ExpenseController::class, 'approve'])
                ->name('expenses.approve');
            Route::post('/expenses/{expense}/reject', [App\Http\Controllers\Finance\ExpenseController::class, 'reject'])
                ->name('expenses.reject');
        });
    });
});

/**
 * --------------------------------------------------------------------------
 * HUMAN RESOURCE AREA
 * Roles: "Administrator" OR "HumanResourceManager"
 * --------------------------------------------------------------------------
 */
Route::middleware([
    'auth',
    'role:Administrator,Admin,Human Resource Manager',
    'prevent-back-history',
])->group(function () {
    Route::get('/hr', [DashboardController::class, 'hr'])->name('hr.dashboard');
    Route::get('/hr/notifications', [App\Http\Controllers\NotificationController::class, 'index'])->name('hr.notifications');

    Route::prefix('hr')->name('hr.')->group(function () {
        // Employees CRUD
        Route::get('/employees',                 [EmployeeController::class, 'index'])->name('employees.index');
        Route::get('/employees/create',          [EmployeeController::class, 'create'])->name('employees.create');
        Route::post('/employees',                [EmployeeController::class, 'store'])->name('employees.store');
        Route::get('/employees/{employee}/edit', [EmployeeController::class, 'edit'])->name('employees.edit');
        Route::put('/employees/{employee}',      [EmployeeController::class, 'update'])->name('employees.update');
        Route::delete('/employees/{employee}',   [EmployeeController::class, 'destroy'])->name('employees.destroy');

        // Leave Requests
        Route::get('/leaves',        [LeaveRequestController::class, 'index'])->name('leaves.index');
        Route::get('/leaves/create', [LeaveRequestController::class, 'create'])->name('leaves.create');
        Route::post('/leaves',       [LeaveRequestController::class, 'store'])->name('leaves.store');
        // Approved leaves view
        Route::get('/leaves/approved', ApprovedLeavesController::class)->name('leaves.approved');

        Route::get('/leaves/{leave}', [LeaveRequestController::class, 'show'])->name('leaves.show');

        // Attendance
        Route::get('/attendance', [App\Http\Controllers\HR\AttendanceController::class, 'index'])
            ->name('attendance.index');

        Route::post('/attendance/check-in', [App\Http\Controllers\HR\AttendanceController::class, 'checkIn'])
            ->name('attendance.check-in');

        Route::post('/attendance/check-out/{id}', [App\Http\Controllers\HR\AttendanceController::class, 'checkOut'])
            ->name('attendance.check-out');

        // ✅ NEW: Monthly summary & export (Admin + HR only, via gate)
        Route::get('/attendance/monthly-summary', [App\Http\Controllers\HR\AttendanceController::class, 'monthlySummary'])
            ->middleware('can:manage-attendance')
            ->name('attendance.monthly-summary');

        Route::get('/attendance/monthly-summary/export', [App\Http\Controllers\HR\AttendanceController::class, 'exportMonthlySummaryCsv'])
            ->middleware('can:manage-attendance')
            ->name('attendance.monthly-summary.export');

        // Fetch employee leave dates for UI validation
        Route::get('/employees/{employee}/leave-dates', [LeaveRequestController::class, 'getLeaveDates'])
            ->name('employees.leave-dates');
    });
});

/**
 * --------------------------------------------------------------------------
 * INVENTORY AREA
 * Roles: "Administrator" OR "InventoryManager"
 * --------------------------------------------------------------------------
 */
Route::middleware([
    'auth',
    'role:Administrator,Admin,Inventory Manager',
    'prevent-back-history',
])->group(function () {

    Route::get('/inventory', [DashboardController::class, 'inventory'])->name('inventory.dashboard');
    Route::get('/inventory/notifications', [App\Http\Controllers\NotificationController::class, 'index'])->name('inventory.notifications');

    Route::prefix('inventory')->name('inventory.')->group(function () {
        /**
         * ITEMS
         */
        Route::get('/items',              [InventoryItemController::class, 'index'])->name('items.index');
        Route::get('/items/create',       [InventoryItemController::class, 'create'])->name('items.create');
        Route::post('/items',             [InventoryItemController::class, 'store'])->name('items.store');
        Route::get('/items/{item}/edit',  [InventoryItemController::class, 'edit'])->name('items.edit');
        Route::put('/items/{item}',       [InventoryItemController::class, 'update'])->name('items.update');
        Route::delete('/items/{item}',    [InventoryItemController::class, 'destroy'])->name('items.destroy');

        // Inventory Audit Trail (Logs)
        Route::get('/logs', [App\Http\Controllers\Inventory\InventoryLogController::class, 'index'])
            ->name('logs.index');

        /**
         * LOANS (lending items to employees)
         */
        Route::get('/loans',              [InventoryLoanController::class, 'index'])->name('loans.index');
        Route::get('/loans/create',       [InventoryLoanController::class, 'create'])->name('loans.create');
        Route::post('/loans',             [InventoryLoanController::class, 'store'])->name('loans.store');
        Route::get('/loans/{loan}',       [InventoryLoanController::class, 'show'])->name('loans.show');
        Route::get('/loans/{loan}/edit',  [InventoryLoanController::class, 'edit'])->name('loans.edit');
        Route::put('/loans/{loan}',       [InventoryLoanController::class, 'update'])->name('loans.update');
        Route::delete('/loans/{loan}',    [InventoryLoanController::class, 'destroy'])->name('loans.destroy');

        Route::post(
            '/loans/{loan}/mark-returned',
            [InventoryLoanController::class, 'markReturned']
        )->name('loans.mark-returned');
    });
});

/**
 * --------------------------------------------------------------------------
 * FINANCE AREA
 * Roles: "Administrator" OR "FinancialManager"
 * --------------------------------------------------------------------------
 */
Route::middleware([
    'auth',
    'role:Administrator,Admin,Financial Manager',
    'prevent-back-history',
])->group(function () {
    // Finance Management
    Route::prefix('finance')->name('finance.')->group(function () {
        Route::get('/dashboard', [App\Http\Controllers\DashboardController::class, 'finance'])->name('dashboard');
        Route::get('/notifications', [App\Http\Controllers\NotificationController::class, 'index'])->name('notifications');

        // Projects & Expenses
        Route::resource('projects', App\Http\Controllers\Finance\ProjectController::class);
        Route::resource('expenses', App\Http\Controllers\Finance\ExpenseController::class);
    });
});

Route::get('/debug-db', function() {
    return [
        'database'   => \Illuminate\Support\Facades\DB::connection()->getDatabaseName(),
        'schema'     => \Illuminate\Support\Facades\DB::select('SHOW search_path'),
        'expenses'   => \Illuminate\Support\Facades\Schema::getColumnListing('expenses'),
        'employees'  => \Illuminate\Support\Facades\Schema::getColumnListing('employees'),
        'users'      => \Illuminate\Support\Facades\Schema::getColumnListing('users'),
        'projects'   => \Illuminate\Support\Facades\Schema::getColumnListing('projects'),
    ];
});

