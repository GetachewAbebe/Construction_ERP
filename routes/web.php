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

use App\Models\EmployeeOnLeave;
use App\Models\InventoryLoan;

/**
 * --------------------------------------------------------------------------
 * HOME = LOGIN PAGE (with redirect if already authenticated)
 * --------------------------------------------------------------------------
 * GET  /  -> if guest  -> show login page (home.blade.php)
 *          if authed -> redirect to dashboard based on role
 * POST /  -> perform login and redirect based on role
 */

Route::get('/', function () {
    // If user is already logged in, send them straight to their dashboard
    if (Auth::check()) {
        $user     = Auth::user();
        $rawRole  = $user->role ?? null;
        $roleSlug = $rawRole ? strtolower(trim($rawRole)) : null;

        if ($roleSlug === 'administrator') {
            return redirect()->route('admin.dashboard');
        } elseif ($roleSlug === 'humanresourcemanager') {
            return redirect()->route('hr.dashboard');
        } elseif ($roleSlug === 'inventorymanager') {
            return redirect()->route('inventory.dashboard');
        } elseif ($roleSlug === 'financialmanager') {
            return redirect()->route('finance.dashboard');
        }

        // Unknown / unmapped role → block access clearly
        abort(
            403,
            "Unknown or unmapped role '{$rawRole}'. Please check the user's role value in the database."
        );
    }

    // Guest: show the login form
    return view('home');
})->name('home');

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
 * ADMINISTRATOR AREA
 * Roles: Administrator, HumanResourceManager, InventoryManager, FinancialManager
 * --------------------------------------------------------------------------
 */
Route::middleware([
    'auth',
    // ✅ allow all four management roles into the /admin area
    'role:Administrator,HumanResourceManager,InventoryManager,FinancialManager',
    'prevent-back-history',
])->group(function () {

    // Admin dashboards
    Route::get('/admin',      [DashboardController::class, 'admin'])->name('admin.dashboard');
    Route::get('/admin/home', [DashboardController::class, 'admin'])->name('admin.home');

    // Admin “sections” (HR, Inventory, Finance) if you use them
    Route::prefix('admin/section')->name('admin.section.')->group(function () {
        Route::view('/hr',        'admin.sections.hr')->name('hr');
        Route::view('/inventory', 'admin.sections.inventory')->name('inventory');
        Route::view('/finance',   'admin.sections.finance')->name('finance');
    });

    /**
     * Admin: requests / approvals (leave, purchases, items, finance)
     */
    Route::prefix('admin/requests')->name('admin.requests.')->group(function () {
        Route::view('/leave',     'admin.requests.leave')->name('leave');
        Route::view('/purchases', 'admin.requests.purchases')->name('purchases');
        Route::view('/finance',   'admin.requests.finance')->name('finance');

        // === ITEM LENDING REQUESTS (Inventory loans) ===
        Route::get('/items', function () {
            $loans = InventoryLoan::with(['item', 'employee'])
                ->orderByRaw("CASE WHEN status = 'pending' THEN 0 ELSE 1 END")
                ->latest()
                ->paginate(20);

            return view('admin.requests.items', compact('loans'));
        })->name('items');

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
    'role:Administrator,HumanResourceManager',
    'prevent-back-history',
])->group(function () {
    Route::get('/hr', [DashboardController::class, 'hr'])->name('hr.dashboard');

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
        Route::get('/leaves/approved', function () {
            $approved = EmployeeOnLeave::with('employee', 'approver')
                ->latest('approved_at')
                ->paginate(20);

            return view('hr.leaves.approved', compact('approved'));
        })->name('leaves.approved');
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
    'role:Administrator,InventoryManager',
    'prevent-back-history',
])->group(function () {

    Route::get('/inventory', [DashboardController::class, 'inventory'])->name('inventory.dashboard');

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
    'role:Administrator,FinancialManager',
    'prevent-back-history',
])->group(function () {
    Route::get('/finance', [DashboardController::class, 'finance'])->name('finance.dashboard');

    // Add more finance routes later if needed
});
