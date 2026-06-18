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

// Login submit
Route::post('/', [SimpleAuthController::class, 'login'])->middleware('throttle:auth')->name('login');

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

// Notifications
Route::middleware('auth')->group(function () {
    Route::get('/notifications', function () {
        $user = auth()->user();
        if ($user->hasRole('Administrator')) {
            return redirect()->route('admin.notifications');
        }
        if ($user->hasRole('HumanResourceManager')) {
            return redirect()->route('hr.notifications');
        }
        if ($user->hasRole('InventoryManager')) {
            return redirect()->route('inventory.notifications');
        }
        if ($user->hasRole('FinancialManager')) {
            return redirect()->route('finance.notifications');
        }

        return redirect()->route('home'); // Fallback
    })->name('notifications.index');
    Route::post('/notifications/{id}/mark-as-read', [App\Http\Controllers\NotificationController::class, 'markAsRead'])->name('notifications.mark-as-read');
    Route::post('/notifications/mark-all-as-read', [App\Http\Controllers\NotificationController::class, 'markAllAsRead'])->name('notifications.mark-all-as-read');
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
