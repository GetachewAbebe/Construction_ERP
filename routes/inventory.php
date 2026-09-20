<?php

declare(strict_types=1);

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Inventory\InventoryItemController;
use App\Http\Controllers\Inventory\InventoryLoanController;
use Illuminate\Support\Facades\Route;

/**
 * --------------------------------------------------------------------------
 * INVENTORY AREA
 * Roles: "Administrator" OR "InventoryManager"
 * --------------------------------------------------------------------------
 */
Route::middleware([
    'auth',
    'role:Administrator,Admin,Inventory Manager,InventoryManager',
    'prevent-back-history',
])->group(function () {

    Route::get('/inventory', [DashboardController::class, 'inventory'])->name('inventory.dashboard');
    Route::redirect('/inventory/dashboard', '/inventory');
    Route::get('/inventory/notifications', [App\Http\Controllers\NotificationController::class, 'index'])->name('inventory.notifications');

    // Professional Identity Management
    Route::get('/inventory/profile', [App\Http\Controllers\ProfileController::class, 'show'])->name('inventory.profile.show');
    Route::get('/inventory/profile/update', [App\Http\Controllers\ProfileController::class, 'edit'])->name('inventory.profile.edit');
    Route::put('/inventory/profile/update', [App\Http\Controllers\ProfileController::class, 'update'])->name('inventory.profile.update');

    Route::prefix('inventory')->name('inventory.')->group(function () {
        /**
         * ITEMS
         */
        Route::get('/items', [InventoryItemController::class, 'index'])->name('items.index');
        Route::get('/items/create', [InventoryItemController::class, 'create'])->name('items.create');
        Route::post('/items', [InventoryItemController::class, 'store'])->name('items.store');
        Route::get('/items/{item}/edit', [InventoryItemController::class, 'edit'])->name('items.edit');
        Route::put('/items/{item}', [InventoryItemController::class, 'update'])->name('items.update');
        Route::delete('/items/{item}', [InventoryItemController::class, 'destroy'])->name('items.destroy');

        /**
         * VENDORS
         */
        Route::resource('vendors', App\Http\Controllers\Inventory\VendorController::class);

        // Inventory Audit Trail (Logs)
        Route::get('/logs', [App\Http\Controllers\Inventory\InventoryLogController::class, 'index'])
            ->name('logs.index');

        /**
         * LOANS (lending items to employees)
         */
        Route::get('/loans', [InventoryLoanController::class, 'index'])->name('loans.index');
        Route::get('/loans/export', [InventoryLoanController::class, 'exportCsv'])->name('loans.export');
        Route::get('/loans/create', [InventoryLoanController::class, 'create'])->name('loans.create');
        Route::post('/loans', [InventoryLoanController::class, 'store'])->name('loans.store');
        Route::get('/loans/{loan}', [InventoryLoanController::class, 'show'])->name('loans.show');
        Route::get('/loans/{loan}/edit', [InventoryLoanController::class, 'edit'])->name('loans.edit');
        Route::put('/loans/{loan}', [InventoryLoanController::class, 'update'])->name('loans.update');
        Route::delete('/loans/{loan}', [InventoryLoanController::class, 'destroy'])->name('loans.destroy');

        /**
         * EQUIPMENT & HEAVY MACHINERY FLEET (Asset Inventory)
         */
        Route::get('/equipment', [\App\Http\Controllers\Operations\EquipmentController::class, 'index'])->name('equipment.index');
        Route::get('/equipment/export', [\App\Http\Controllers\Operations\EquipmentController::class, 'exportCsv'])->name('equipment.export');
        Route::post('/equipment', [\App\Http\Controllers\Operations\EquipmentController::class, 'store'])->name('equipment.store');
        Route::put('/equipment/{equipment}', [\App\Http\Controllers\Operations\EquipmentController::class, 'update'])->name('equipment.update');
        Route::delete('/equipment/{equipment}', [\App\Http\Controllers\Operations\EquipmentController::class, 'destroy'])->name('equipment.destroy');
        Route::post('/equipment/{equipment}/logs', [\App\Http\Controllers\Operations\EquipmentController::class, 'storeLog'])->name('equipment.logs.store');

        Route::post(
            '/loans/{loan}/mark-returned',
            [InventoryLoanController::class, 'markReturned']
        )->name('loans.mark-returned');

        Route::resource('asset-classifications', App\Http\Controllers\Admin\AssetClassificationController::class)
            ->except(['show'])
            ->names([
                'index' => 'asset-classifications.index',
                'create' => 'asset-classifications.create',
                'store' => 'asset-classifications.store',
                'edit' => 'asset-classifications.edit',
                'update' => 'asset-classifications.update',
                'destroy' => 'asset-classifications.destroy',
            ]);
    });
});
