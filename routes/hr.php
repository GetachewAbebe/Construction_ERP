<?php

declare(strict_types=1);

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\HR\ApprovedLeavesController;
use App\Http\Controllers\HR\EmployeeController;
use App\Http\Controllers\HR\LeaveRequestController;
use Illuminate\Support\Facades\Route;

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

    // Professional Identity Management
    Route::get('/hr/profile', [App\Http\Controllers\ProfileController::class, 'show'])->name('hr.profile.show');
    Route::get('/hr/profile/update', [App\Http\Controllers\ProfileController::class, 'edit'])->name('hr.profile.edit');
    Route::put('/hr/profile/update', [App\Http\Controllers\ProfileController::class, 'update'])->name('hr.profile.update');

    Route::prefix('hr')->name('hr.')->group(function () {
        // Employees CRUD
        Route::get('/employees', [EmployeeController::class, 'index'])->name('employees.index');
        Route::get('/employees/create', [EmployeeController::class, 'create'])->name('employees.create');
        Route::post('/employees', [EmployeeController::class, 'store'])->name('employees.store');
        Route::get('/employees/{employee}/edit', [EmployeeController::class, 'edit'])->name('employees.edit');
        Route::put('/employees/{employee}', [EmployeeController::class, 'update'])->name('employees.update');
        Route::delete('/employees/{employee}', [EmployeeController::class, 'destroy'])->name('employees.destroy');

        // Leave Requests
        Route::get('/leaves', [LeaveRequestController::class, 'index'])->name('leaves.index');
        Route::get('/leaves/create', [LeaveRequestController::class, 'create'])->name('leaves.create');
        Route::post('/leaves', [LeaveRequestController::class, 'store'])->name('leaves.store');
        // Approved leaves view
        Route::get('/leaves/approved', ApprovedLeavesController::class)->name('leaves.approved');

        Route::get('/leaves/{leave}', [LeaveRequestController::class, 'show'])->name('leaves.show');

        // Attendance
        Route::get('/attendance', [App\Http\Controllers\HR\AttendanceController::class, 'index'])
            ->name('attendance.index');

        // --- NEW: Session Based Attendance ---
        Route::get('/attendance/daily-sheet', [App\Http\Controllers\HR\AttendanceController::class, 'dailySheet'])
            ->name('attendance.daily-sheet');
        Route::post('/attendance/daily-sheet', [App\Http\Controllers\HR\AttendanceController::class, 'storeDailySheet'])
            ->name('attendance.daily-sheet.store');

        Route::get('/attendance/weekly-sheet', [App\Http\Controllers\HR\AttendanceController::class, 'weeklySheet'])
            ->name('attendance.weekly-sheet');
        Route::post('/attendance/weekly-sheet', [App\Http\Controllers\HR\AttendanceController::class, 'storeWeeklySheet'])
            ->name('attendance.weekly-sheet.store');

        Route::post('/attendance/toggle', [App\Http\Controllers\HR\AttendanceController::class, 'toggleSession'])
            ->name('attendance.toggle');

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

        // ✅ NEW: Weekly Salary Analysis
        Route::get('/attendance/weekly-salary', [App\Http\Controllers\HR\AttendanceController::class, 'weeklySalary'])
            ->middleware('can:manage-attendance')
            ->name('attendance.weekly-salary');

        // Fetch employee leave dates for UI validation
        Route::get('/employees/{employee}/leave-dates', [LeaveRequestController::class, 'getLeaveDates'])
            ->name('employees.leave-dates');
    });
});
