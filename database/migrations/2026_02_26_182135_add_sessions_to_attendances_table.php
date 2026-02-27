<?php

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
        Schema::table('attendances', function (Blueprint $table) {
            $table->enum('morning_status', ['present', 'absent', 'leave'])->default('present')->after('date');
            $table->enum('afternoon_status', ['present', 'absent', 'leave'])->default('present')->after('morning_status');
            $table->decimal('total_credit', 3, 2)->default(1.00)->after('afternoon_status');

            // Note: clock_in and clock_out now become optional for back-office entry
            $table->timestamp('clock_in')->nullable()->change();
            $table->timestamp('clock_out')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('attendances', function (Blueprint $table) {
            $table->dropColumn(['morning_status', 'afternoon_status', 'total_credit']);
        });
    }
};
