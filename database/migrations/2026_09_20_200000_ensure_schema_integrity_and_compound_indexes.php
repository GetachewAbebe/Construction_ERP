<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Ensure inventory_items columns
        if (Schema::hasTable('inventory_items')) {
            Schema::table('inventory_items', function (Blueprint $table) {
                if (! Schema::hasColumn('inventory_items', 'quantity')) {
                    $table->integer('quantity')->default(0);
                }
                if (! Schema::hasColumn('inventory_items', 'unit_of_measurement')) {
                    $table->string('unit_of_measurement', 50)->default('pcs');
                }
                if (! Schema::hasColumn('inventory_items', 'status')) {
                    $table->string('status', 50)->default('active');
                }
            });
        }

        // 2. Ensure users columns
        if (Schema::hasTable('users')) {
            Schema::table('users', function (Blueprint $table) {
                if (! Schema::hasColumn('users', 'bio')) {
                    $table->text('bio')->nullable();
                }
                if (! Schema::hasColumn('users', 'phone')) {
                    $table->string('phone', 50)->nullable();
                }
            });
        }

        // 3. Ensure vendors columns
        if (Schema::hasTable('vendors')) {
            Schema::table('vendors', function (Blueprint $table) {
                if (! Schema::hasColumn('vendors', 'is_active')) {
                    $table->boolean('is_active')->default(true);
                }
                if (! Schema::hasColumn('vendors', 'address')) {
                    $table->string('address')->nullable();
                }
                if (! Schema::hasColumn('vendors', 'phone')) {
                    $table->string('phone')->nullable();
                }
                if (! Schema::hasColumn('vendors', 'tax_number')) {
                    $table->string('tax_number')->nullable();
                }
            });
        }

        // 4. Compound indexes for query performance
        if (Schema::hasTable('expenses')) {
            Schema::table('expenses', function (Blueprint $table) {
                try {
                    $table->index(['project_id', 'status', 'expense_date'], 'expenses_project_status_date_idx');
                } catch (\Throwable $e) {
                }
            });
        }

        if (Schema::hasTable('inventory_loans')) {
            Schema::table('inventory_loans', function (Blueprint $table) {
                try {
                    $table->index(['employee_id', 'status'], 'loans_emp_status_idx');
                } catch (\Throwable $e) {
                }
            });
        }

        if (Schema::hasTable('equipment_logs') && Schema::hasColumn('equipment_logs', 'logged_at')) {
            Schema::table('equipment_logs', function (Blueprint $table) {
                try {
                    $table->index(['equipment_id', 'logged_at'], 'eq_logs_eq_logged_idx');
                } catch (\Throwable $e) {
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('equipment_logs')) {
            Schema::table('equipment_logs', function (Blueprint $table) {
                try {
                    $table->dropIndex('eq_logs_eq_logged_idx');
                } catch (\Throwable $e) {
                }
            });
        }

        if (Schema::hasTable('inventory_loans')) {
            Schema::table('inventory_loans', function (Blueprint $table) {
                try {
                    $table->dropIndex('loans_emp_status_idx');
                } catch (\Throwable $e) {
                }
            });
        }

        if (Schema::hasTable('expenses')) {
            Schema::table('expenses', function (Blueprint $table) {
                try {
                    $table->dropIndex('expenses_project_status_date_idx');
                } catch (\Throwable $e) {
                }
            });
        }
    }
};
