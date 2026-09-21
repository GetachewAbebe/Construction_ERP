<?php

declare(strict_types=1);

use App\Http\Controllers\HomeController;
use App\Http\Controllers\SimpleAuthController;
use Illuminate\Support\Facades\Route;

/**
 * --------------------------------------------------------------------------
 * HOME = LOGIN PAGE (with redirect if already authenticated)
 * --------------------------------------------------------------------------
 * GET  /  -> if guest  -> show login page (home.blade.php)
 *          if authed -> redirect to dashboard based on role
 * POST /  -> perform login and redirect based on role
 */
Route::get('/', HomeController::class)->middleware('throttle:system_global')->name('home');
Route::get('/login', HomeController::class)->middleware('throttle:system_global');

// Login submit
Route::post('/', [SimpleAuthController::class, 'login'])->middleware('throttle:auth')->name('login');
Route::post('/login', [SimpleAuthController::class, 'login'])->middleware('throttle:auth');

// Centralized Role-Based Dashboard Route
Route::get('/dashboard', function () {
    return redirect()->route(\Illuminate\Support\Facades\Auth::user()->getDashboardRouteName());
})->middleware(['auth', 'prevent-back-history'])->name('dashboard');

/**
 * --------------------------------------------------------------------------
 * PASSWORD RESET
 * --------------------------------------------------------------------------
 * GET  /forgot-password → show forgot password form
 * POST /forgot-password → send password reset link
 * GET  /reset-password/{token} → show reset password form
 * POST /reset-password → update password
 */
use App\Http\Controllers\Auth\NewPasswordController;
use App\Http\Controllers\Auth\PasswordResetLinkController;

Route::get('/forgot-password', [PasswordResetLinkController::class, 'create'])
    ->middleware('guest')
    ->name('password.request');

Route::post('/forgot-password', [PasswordResetLinkController::class, 'store'])
    ->middleware(['guest', 'throttle:auth'])
    ->name('password.email');

Route::get('/reset-password/{token}', [NewPasswordController::class, 'create'])
    ->middleware('guest')
    ->name('password.reset');

Route::post('/reset-password', [NewPasswordController::class, 'store'])
    ->middleware(['guest', 'throttle:auth'])
    ->name('password.update');

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
 * PROFILE PICTURE SERVING (Clean Path Standard)
 * --------------------------------------------------------------------------
 */
Route::get('/profile-picture/{filename}', [App\Http\Controllers\ProfilePictureController::class, 'show'])
    ->where('filename', '.*') // Capture subdirectories if any
    ->name('employee.profile-picture');

// Notifications & Profile
Route::middleware('auth')->group(function () {
    Route::get('/profile', function () {
        $user = auth()->user();
        if ($user->hasRole(['HumanResourceManager', 'Human Resource Manager']) || str_contains(strtolower((string) $user->role), 'hr') || str_contains(strtolower((string) $user->role), 'human')) {
            return redirect()->route('hr.profile.show');
        }
        if ($user->hasRole(['InventoryManager', 'Inventory Manager']) || str_contains(strtolower((string) $user->role), 'inventory')) {
            return redirect()->route('inventory.profile.show');
        }
        if ($user->hasRole(['FinancialManager', 'Financial Manager']) || str_contains(strtolower((string) $user->role), 'financ')) {
            return redirect()->route('finance.profile.show');
        }
        return redirect()->route('admin.profile.show');
    })->name('profile');

    Route::get('/notifications', function () {
        $user = auth()->user();
        if ($user->hasRole(['HumanResourceManager', 'Human Resource Manager']) || str_contains(strtolower((string) $user->role), 'hr') || str_contains(strtolower((string) $user->role), 'human')) {
            return redirect()->route('hr.notifications');
        }
        if ($user->hasRole(['InventoryManager', 'Inventory Manager']) || str_contains(strtolower((string) $user->role), 'inventory')) {
            return redirect()->route('inventory.notifications');
        }
        if ($user->hasRole(['FinancialManager', 'Financial Manager']) || str_contains(strtolower((string) $user->role), 'financ')) {
            return redirect()->route('finance.notifications');
        }
        if ($user->hasRole(['Administrator', 'Admin']) || str_contains(strtolower((string) $user->role), 'admin')) {
            return redirect()->route('admin.notifications');
        }

        return redirect()->route('home'); // Fallback
    })->name('notifications.index');
    Route::get('/notifications/{id}/open', [App\Http\Controllers\NotificationController::class, 'open'])->name('notifications.open');
    Route::post('/notifications/{id}/mark-as-read', [App\Http\Controllers\NotificationController::class, 'markAsRead'])->name('notifications.mark-as-read');
    Route::post('/notifications/mark-all-as-read', [App\Http\Controllers\NotificationController::class, 'markAllAsRead'])->name('notifications.mark-all-as-read');

    // Construction & Fleet Workflows
    Route::get('/equipment', [\App\Http\Controllers\Operations\EquipmentController::class, 'index'])->name('equipment.index');
    Route::get('/equipment/export', [\App\Http\Controllers\Operations\EquipmentController::class, 'exportCsv'])->name('equipment.export');
    Route::post('/equipment', [\App\Http\Controllers\Operations\EquipmentController::class, 'store'])->name('equipment.store');
    Route::put('/equipment/{equipment}', [\App\Http\Controllers\Operations\EquipmentController::class, 'update'])->name('equipment.update');
    Route::delete('/equipment/{equipment}', [\App\Http\Controllers\Operations\EquipmentController::class, 'destroy'])->name('equipment.destroy');
    Route::post('/equipment/{equipment}/logs', [\App\Http\Controllers\Operations\EquipmentController::class, 'storeLog'])->name('equipment.logs.store');

    Route::get('/projects/daily-reports', [\App\Http\Controllers\Operations\DailyReportController::class, 'index'])->name('projects.daily-reports.index');
    Route::post('/projects/daily-reports', [\App\Http\Controllers\Operations\DailyReportController::class, 'store'])->name('projects.daily-reports.store');
    Route::get('/projects/daily-reports/{report}', [\App\Http\Controllers\Operations\DailyReportController::class, 'show'])->name('projects.daily-reports.show');

    // Print & Document Generation
    Route::get('/finance/expenses/{expense}/print', [App\Http\Controllers\PrintController::class, 'expenseVoucher'])->name('finance.expenses.print');
    Route::get('/inventory/loans/{loan}/print', [App\Http\Controllers\PrintController::class, 'loanGatePass'])->name('inventory.loans.print');
    Route::get('/prints/expenses/{expense}', [App\Http\Controllers\PrintController::class, 'expenseVoucher'])->name('prints.expense-voucher');
    Route::get('/prints/expense-voucher/{expense}', [App\Http\Controllers\PrintController::class, 'expenseVoucher']);
    Route::get('/prints/loans/{loan}', [App\Http\Controllers\PrintController::class, 'loanGatePass'])->name('prints.loan-gate-pass');
    Route::get('/prints/loan-gate-pass/{loan}', [App\Http\Controllers\PrintController::class, 'loanGatePass']);
});

/**
 * --------------------------------------------------------------------------
 * LIVEWIRE + MARY UI — PROOF OF CONCEPT (isolated, safe to remove)
 * --------------------------------------------------------------------------
 * Reactive Inventory Loans table on a standalone Tailwind/daisyUI layout.
 * Does not affect any existing Bootstrap screens.
 */
Route::get('/poc/inventory-loans', \App\Livewire\Inventory\LoansTable::class)
    ->middleware('auth')
    ->name('poc.inventory-loans');

Route::get('/poc/inventory-items', \App\Livewire\Inventory\ItemsTable::class)
    ->middleware('auth')
    ->name('poc.inventory-items');

require __DIR__.'/admin.php';
require __DIR__.'/hr.php';
require __DIR__.'/inventory.php';
require __DIR__.'/finance.php';
