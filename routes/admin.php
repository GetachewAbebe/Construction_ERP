<?php

declare(strict_types=1);

use App\Http\Controllers\Admin\InventoryLoanApprovalController;
use App\Http\Controllers\Admin\LeaveApprovalController;
use App\Http\Controllers\AdminUserController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\HR\ApprovedLeavesController;
use App\Http\Controllers\HR\EmployeeController;
use App\Http\Controllers\HR\LeaveRequestController;
use App\Http\Controllers\Inventory\InventoryItemController;
use App\Http\Controllers\Inventory\InventoryLoanController;
use Illuminate\Support\Facades\Route;

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
    Route::get('/admin', [DashboardController::class, 'admin'])->name('admin.dashboard');
    Route::redirect('/admin/home', '/admin')->name('admin.home');

    // Admin "sections" - reusing the same dashboards but keeping /admin prefix for sidebar context
    Route::get('/admin/hr', [DashboardController::class, 'hr'])->name('admin.hr');
    Route::get('/admin/inventory', [DashboardController::class, 'inventory'])->name('admin.inventory');
    Route::get('/admin/finance', [DashboardController::class, 'finance'])->name('admin.finance');
    Route::get('/admin/notifications', [App\Http\Controllers\NotificationController::class, 'index'])->name('admin.notifications');

    /**
     * Admin: requests / approvals (leave, purchases, items, finance)
     */
    Route::prefix('admin/requests')->name('admin.requests.')->group(function () {
        Route::redirect('/leave', '/admin/requests/leave-approvals')->name('leave');
        Route::redirect('/purchases', '/admin/requests/finance')->name('purchases');
        Route::get('/requisitions', [App\Http\Controllers\Inventory\PurchaseRequisitionController::class, 'index'])->name('requisitions');
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

        Route::post('/leave/{leave}/reject', [LeaveApprovalController::class, 'reject'])
            ->name('leave.reject');
    });

    /**
     * Strict Administrator-Only Operations (Users, Roles, System Settings, Maintenance, Trash)
     */
    Route::prefix('admin')->name('admin.')->middleware('role:Administrator,Admin')->group(function () {
        // Users CRUD
        Route::get('/users', [AdminUserController::class, 'index'])->name('users.index');
        Route::get('/users/create', [AdminUserController::class, 'create'])->name('users.create');
        Route::post('/users', [AdminUserController::class, 'store'])->name('users.store');
        Route::get('/users/{user}', [AdminUserController::class, 'show'])->name('users.show');
        Route::get('/users/{user}/edit', [AdminUserController::class, 'edit'])->name('users.edit');
        Route::put('/users/{user}', [AdminUserController::class, 'update'])->name('users.update');
        Route::delete('/users/{user}', [AdminUserController::class, 'destroy'])->name('users.destroy');

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
        Route::post('/maintenance/migrate', [App\Http\Controllers\Admin\MaintenanceController::class, 'runMigrations'])
            ->name('maintenance.migrate');
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
        Route::get('/trash', [App\Http\Controllers\Admin\TrashController::class, 'index'])->name('trash.index');
        Route::post('/trash/restore', [App\Http\Controllers\Admin\TrashController::class, 'restore'])->name('trash.restore');
    });

    // Professional Identity Management (for any manager accessing /admin)
    Route::prefix('admin')->name('admin.')->group(function () {
        Route::get('/profile', [App\Http\Controllers\ProfileController::class, 'show'])->name('profile.show');
        Route::get('/profile/update', [App\Http\Controllers\ProfileController::class, 'edit'])->name('profile.edit');
        Route::match(['PUT', 'POST'], '/profile/update', [App\Http\Controllers\ProfileController::class, 'update'])->name('profile.update');
    });

    // === SHADOW ROUTES FOR ADMIN CONTEXT (Read-Only / Management View) ===
    Route::prefix('admin')->name('admin.')->group(function () {
        // HR Shadow
        Route::prefix('hr')->name('hr.')->group(function () {
            Route::get('/employees', [EmployeeController::class, 'index'])->name('employees.index');
            Route::get('/employees/{employee}/edit', [EmployeeController::class, 'edit'])->name('employees.edit');
            Route::get('/leaves', [LeaveRequestController::class, 'index'])->name('leaves.index');
            Route::get('/attendance', [App\Http\Controllers\HR\AttendanceController::class, 'index'])->name('attendance.index');
            Route::get('/leaves/approved', ApprovedLeavesController::class)->name('leaves.approved');
        });

        // Inventory Shadow
        Route::prefix('inventory')->name('inventory.')->group(function () {
            Route::get('/items', [InventoryItemController::class, 'index'])->name('items.index');
            Route::get('/loans', [InventoryLoanController::class, 'index'])->name('loans.index');
            Route::get('/logs', [App\Http\Controllers\Inventory\InventoryLogController::class, 'index'])->name('logs.index');
        });

        // Finance Shadow
        Route::prefix('finance')->name('finance.')->group(function () {
            // Projects
            Route::resource('projects', App\Http\Controllers\Finance\ProjectController::class);
            Route::post('/projects/{project}/milestones', [App\Http\Controllers\Finance\ProjectMilestoneController::class, 'store'])->name('projects.milestones.store');
            Route::put('/projects/{project}/milestones/{milestone}', [App\Http\Controllers\Finance\ProjectMilestoneController::class, 'update'])->name('projects.milestones.update');
            Route::patch('/projects/{project}/milestones/{milestone}/progress', [App\Http\Controllers\Finance\ProjectMilestoneController::class, 'updateProgress'])->name('projects.milestones.progress');
            Route::delete('/projects/{project}/milestones/{milestone}', [App\Http\Controllers\Finance\ProjectMilestoneController::class, 'destroy'])->name('projects.milestones.destroy');

            // Expenses
            Route::resource('expenses', App\Http\Controllers\Finance\ExpenseController::class);
        });
    });
});
