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

// Digital Verification Endpoints (Field QR Scanning)
Route::get('/verify/gate-pass/{loan}', [\App\Http\Controllers\VerificationController::class, 'verifyGatePass'])->name('verify.gate-pass');
Route::post('/verify/gate-pass/{loan}/confirm', [\App\Http\Controllers\VerificationController::class, 'confirmGatePassReceipt'])->name('verify.gate-pass.confirm');
Route::get('/verify/expense-voucher/{expense}', [\App\Http\Controllers\VerificationController::class, 'verifyExpenseVoucher'])->name('verify.expense-voucher');
Route::get('/verify/purchase-order/{order}', [\App\Http\Controllers\VerificationController::class, 'verifyPurchaseOrder'])->name('verify.purchase-order');
Route::get('/verify/goods-receiving/{note}', [\App\Http\Controllers\VerificationController::class, 'verifyGoodsReceivingNote'])->name('verify.goods-receiving');
Route::get('/verify/client-ipc/{certificate}', [\App\Http\Controllers\VerificationController::class, 'verifyClientIpc'])->name('verify.client-ipc');
Route::get('/verify/muster-roll/{musterRollNo}', [\App\Http\Controllers\VerificationController::class, 'verifyMusterRoll'])->name('verify.muster-roll');
Route::get('/verify/maintenance-order/{workOrderNo}', [\App\Http\Controllers\VerificationController::class, 'verifyMaintenanceOrder'])->name('verify.maintenance-order');

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

    // Fleet & Machinery Maintenance Work Orders
    Route::get('/equipment/maintenance', [\App\Http\Controllers\Operations\MaintenanceWorkOrderController::class, 'index'])->name('operations.maintenance.index');
    Route::get('/equipment/maintenance/create', [\App\Http\Controllers\Operations\MaintenanceWorkOrderController::class, 'create'])->name('operations.maintenance.create');
    Route::post('/equipment/maintenance', [\App\Http\Controllers\Operations\MaintenanceWorkOrderController::class, 'store'])->name('operations.maintenance.store');
    Route::get('/equipment/maintenance/{workOrder}', [\App\Http\Controllers\Operations\MaintenanceWorkOrderController::class, 'show'])->name('operations.maintenance.show');
    Route::post('/equipment/maintenance/{workOrder}/complete', [\App\Http\Controllers\Operations\MaintenanceWorkOrderController::class, 'complete'])->name('operations.maintenance.complete');
    Route::delete('/equipment/maintenance/{workOrder}', [\App\Http\Controllers\Operations\MaintenanceWorkOrderController::class, 'destroy'])->name('operations.maintenance.destroy');
    Route::get('/equipment/maintenance/{workOrder}/print', [\App\Http\Controllers\Operations\MaintenanceWorkOrderController::class, 'print'])->name('operations.maintenance.print');

    Route::get('/projects/daily-reports', [\App\Http\Controllers\Operations\DailyReportController::class, 'index'])->name('projects.daily-reports.index');
    Route::post('/projects/daily-reports', [\App\Http\Controllers\Operations\DailyReportController::class, 'store'])->name('projects.daily-reports.store');
    Route::get('/projects/daily-reports/{report}', [\App\Http\Controllers\Operations\DailyReportController::class, 'show'])->name('projects.daily-reports.show');
    Route::get('/projects/daily-reports/{report}/print', [\App\Http\Controllers\Operations\DailyReportController::class, 'print'])->name('projects.daily-reports.print');

    // Engineering Blueprints & Document Management (EDMS)
    Route::get('/operations/documents', [\App\Http\Controllers\Operations\ProjectDocumentController::class, 'index'])->name('operations.documents.index');
    Route::post('/projects/{project}/documents', [\App\Http\Controllers\Operations\ProjectDocumentController::class, 'store'])->name('projects.documents.store');
    Route::get('/documents/{document}/download', [\App\Http\Controllers\Operations\ProjectDocumentController::class, 'download'])->name('documents.download');
    Route::patch('/documents/{document}/status', [\App\Http\Controllers\Operations\ProjectDocumentController::class, 'updateStatus'])->name('documents.status');
    Route::delete('/documents/{document}', [\App\Http\Controllers\Operations\ProjectDocumentController::class, 'destroy'])->name('documents.destroy');

    // Subcontractors & Progress Measurement Certificates
    Route::get('/contracts/subcontractors', [\App\Http\Controllers\Contracts\SubcontractorController::class, 'index'])->name('contracts.subcontractors.index');
    Route::post('/contracts/subcontractors', [\App\Http\Controllers\Contracts\SubcontractorController::class, 'store'])->name('contracts.subcontractors.store');
    Route::post('/contracts/subcontractors/{subcontractor}/certificates', [\App\Http\Controllers\Contracts\SubcontractorController::class, 'storeCertificate'])->name('contracts.subcontractors.certificates.store');

    // Print & Document Generation
    Route::get('/finance/expenses/{expense}/print', [App\Http\Controllers\PrintController::class, 'expenseVoucher'])->name('finance.expenses.print');
    Route::get('/inventory/loans/{loan}/print', [App\Http\Controllers\PrintController::class, 'loanGatePass'])->name('inventory.loans.print');
    Route::get('/inventory/purchase-orders/{purchaseOrder}/print', [\App\Http\Controllers\Inventory\PurchaseOrderController::class, 'print'])->name('inventory.purchase-orders.print');
    Route::get('/inventory/goods-receiving/{goodsReceiving}/print', [\App\Http\Controllers\Inventory\GoodsReceivingNoteController::class, 'print'])->name('inventory.goods-receiving.print');
    Route::get('/finance/client-certificates/{clientCertificate}/print', [\App\Http\Controllers\Finance\ClientPaymentCertificateController::class, 'print'])->name('finance.client-certificates.print');
    Route::get('/prints/expenses/{expense}', [App\Http\Controllers\PrintController::class, 'expenseVoucher'])->name('prints.expense-voucher');
    Route::get('/prints/expense-voucher/{expense}', [App\Http\Controllers\PrintController::class, 'expenseVoucher']);
    Route::get('/prints/loans/{loan}', [App\Http\Controllers\PrintController::class, 'loanGatePass'])->name('prints.loan-gate-pass');
    Route::get('/prints/loan-gate-pass/{loan}', [App\Http\Controllers\PrintController::class, 'loanGatePass']);
    Route::get('/prints/purchase-orders/{purchaseOrder}', [\App\Http\Controllers\Inventory\PurchaseOrderController::class, 'print'])->name('prints.purchase-order');
    Route::get('/prints/goods-receiving/{goodsReceiving}', [\App\Http\Controllers\Inventory\GoodsReceivingNoteController::class, 'print'])->name('prints.goods-receiving');
    Route::get('/prints/client-ipc/{clientCertificate}', [\App\Http\Controllers\Finance\ClientPaymentCertificateController::class, 'print'])->name('prints.client-ipc');
    Route::get('/prints/muster-roll/{musterRoll}', [\App\Http\Controllers\Labor\MusterRollController::class, 'print'])->name('prints.muster-roll');
    Route::get('/prints/maintenance-work-order/{workOrder}', [\App\Http\Controllers\Operations\MaintenanceWorkOrderController::class, 'print'])->name('prints.maintenance-work-order');
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
require __DIR__.'/labor.php';
