<?php

declare(strict_types=1);

use App\Http\Controllers\Labor\CasualLaborerController;
use App\Http\Controllers\Labor\MusterRollController;
use Illuminate\Support\Facades\Route;

/**
 * --------------------------------------------------------------------------
 * SITE CASUAL LABOR & MUSTER ROLL ROUTES
 * Roles: Administrator, Human Resource Manager, Financial Manager
 * --------------------------------------------------------------------------
 */
Route::middleware([
    'auth',
    'role:Administrator,Admin,Human Resource Manager,HumanResourceManager,Financial Manager,FinancialManager',
    'prevent-back-history',
])->prefix('labor')->name('labor.')->group(function () {
    // Casual Laborers (Site Workers) Directory
    Route::get('/workers', [CasualLaborerController::class, 'index'])->name('workers.index');
    Route::post('/workers', [CasualLaborerController::class, 'store'])->name('workers.store');
    Route::put('/workers/{worker}', [CasualLaborerController::class, 'update'])->name('workers.update');
    Route::delete('/workers/{worker}', [CasualLaborerController::class, 'destroy'])->name('workers.destroy');
    Route::get('/workers/quick-list', [CasualLaborerController::class, 'quickList'])->name('workers.quick-list');

    // Daily Site Muster Rolls
    Route::get('/muster-rolls', [MusterRollController::class, 'index'])->name('muster-rolls.index');
    Route::get('/muster-rolls/create', [MusterRollController::class, 'create'])->name('muster-rolls.create');
    Route::post('/muster-rolls', [MusterRollController::class, 'store'])->name('muster-rolls.store');
    Route::get('/muster-rolls/{musterRoll}', [MusterRollController::class, 'show'])->name('muster-rolls.show');
    Route::delete('/muster-rolls/{musterRoll}', [MusterRollController::class, 'destroy'])->name('muster-rolls.destroy');

    // Workflow actions
    Route::post('/muster-rolls/{musterRoll}/approve', [MusterRollController::class, 'approve'])->name('muster-rolls.approve');
    Route::post('/muster-rolls/{musterRoll}/payout', [MusterRollController::class, 'recordPayout'])->name('muster-rolls.payout');
    Route::get('/muster-rolls/{musterRoll}/print', [MusterRollController::class, 'print'])->name('muster-rolls.print');
});
