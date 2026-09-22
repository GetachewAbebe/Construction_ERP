<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;

/**
 * --------------------------------------------------------------------------
 * FINANCE AREA
 * Roles: "Administrator" OR "FinancialManager"
 * --------------------------------------------------------------------------
 */
Route::middleware([
    'auth',
    'role:Administrator,Admin,Financial Manager,FinancialManager',
    'prevent-back-history',
])->group(function () {
    Route::redirect('/finance', '/finance/dashboard');

    // Finance Management
    Route::prefix('finance')->name('finance.')->group(function () {
        Route::get('/dashboard', [App\Http\Controllers\DashboardController::class, 'finance'])->name('dashboard');
        Route::get('/notifications', [App\Http\Controllers\NotificationController::class, 'index'])->name('notifications');

        // Professional Identity Management
        Route::get('/profile', [App\Http\Controllers\ProfileController::class, 'show'])->name('profile.show');
        Route::get('/profile/update', [App\Http\Controllers\ProfileController::class, 'edit'])->name('profile.edit');
        Route::put('/profile/update', [App\Http\Controllers\ProfileController::class, 'update'])->name('profile.update');

        // Projects & Expenses
        Route::get('/expenses/export', [App\Http\Controllers\Finance\ExpenseController::class, 'exportCsv'])->name('expenses.export');
        Route::resource('projects', App\Http\Controllers\Finance\ProjectController::class);

        // Project WBS & Milestones
        Route::post('/projects/{project}/milestones', [App\Http\Controllers\Finance\ProjectMilestoneController::class, 'store'])->name('projects.milestones.store');
        Route::put('/projects/{project}/milestones/{milestone}', [App\Http\Controllers\Finance\ProjectMilestoneController::class, 'update'])->name('projects.milestones.update');
        Route::patch('/projects/{project}/milestones/{milestone}/progress', [App\Http\Controllers\Finance\ProjectMilestoneController::class, 'updateProgress'])->name('projects.milestones.progress');
        Route::delete('/projects/{project}/milestones/{milestone}', [App\Http\Controllers\Finance\ProjectMilestoneController::class, 'destroy'])->name('projects.milestones.destroy');

        Route::resource('expenses', App\Http\Controllers\Finance\ExpenseController::class);

        // Expense Approval Workflow
        Route::post('/expenses/{expense}/approve', [App\Http\Controllers\Admin\ExpenseApprovalController::class, 'approve'])
            ->name('expenses.approve');
        Route::post('/expenses/{expense}/reject', [App\Http\Controllers\Admin\ExpenseApprovalController::class, 'reject'])
            ->name('expenses.reject');
    });
});
