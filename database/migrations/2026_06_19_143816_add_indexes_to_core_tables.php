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
        Schema::table('leave_requests', function (Blueprint $table) {
            $table->index('status');
        });

        Schema::table('inventory_loans', function (Blueprint $table) {
            $table->index('status');
        });

        Schema::table('expenses', function (Blueprint $table) {
            $table->index('status');
            $table->index('expense_date');
        });

        Schema::table('attendances', function (Blueprint $table) {
            $table->index('status');
            $table->index(['employee_id', 'date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('attendances', function (Blueprint $table) {
            $table->dropIndex(['status']);
            $table->dropIndex(['employee_id', 'date']);
        });

        Schema::table('expenses', function (Blueprint $table) {
            $table->dropIndex(['status']);
            $table->dropIndex(['expense_date']);
        });

        Schema::table('inventory_loans', function (Blueprint $table) {
            $table->dropIndex(['status']);
        });

        Schema::table('leave_requests', function (Blueprint $table) {
            $table->dropIndex(['status']);
        });
    }
};
