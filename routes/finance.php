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
    'role:Administrator,Admin,Financial Manager',
    'prevent-back-history',
])->group(function () {
    // Finance Management
    Route::prefix('finance')->name('finance.')->group(function () {
        Route::get('/dashboard', [App\Http\Controllers\DashboardController::class, 'finance'])->name('dashboard');
        Route::get('/notifications', [App\Http\Controllers\NotificationController::class, 'index'])->name('notifications');

        // Professional Identity Management
        Route::get('/profile', [App\Http\Controllers\ProfileController::class, 'show'])->name('profile.show');
        Route::get('/profile/update', [App\Http\Controllers\ProfileController::class, 'edit'])->name('profile.edit');
        Route::put('/profile/update', [App\Http\Controllers\ProfileController::class, 'update'])->name('profile.update');

        // Projects & Expenses
        Route::resource('projects', App\Http\Controllers\Finance\ProjectController::class);
        Route::resource('expenses', App\Http\Controllers\Finance\ExpenseController::class);

        // Expense Approval Workflow
        Route::post('/expenses/{expense}/approve', [App\Http\Controllers\Finance\ExpenseController::class, 'approve'])
            ->name('expenses.approve');
        Route::post('/expenses/{expense}/reject', [App\Http\Controllers\Finance\ExpenseController::class, 'reject'])
            ->name('expenses.reject');
    });
});
