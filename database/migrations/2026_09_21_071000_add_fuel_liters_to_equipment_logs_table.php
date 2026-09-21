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
        if (Schema::hasTable('equipment_logs') && ! Schema::hasColumn('equipment_logs', 'fuel_liters')) {
            Schema::table('equipment_logs', function (Blueprint $table) {
                $table->decimal('fuel_liters', 8, 2)->nullable()->after('hours_at_log');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('equipment_logs') && Schema::hasColumn('equipment_logs', 'fuel_liters')) {
            Schema::table('equipment_logs', function (Blueprint $table) {
                $table->dropColumn('fuel_liters');
            });
        }
    }
};
