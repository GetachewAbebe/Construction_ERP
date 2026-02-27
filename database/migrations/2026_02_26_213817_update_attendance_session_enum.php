<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // For PGSQL, ->change() on enums is problematic. Using raw SQL.
        DB::statement("ALTER TABLE attendances DROP CONSTRAINT IF EXISTS attendances_morning_status_check");
        DB::statement("ALTER TABLE attendances ALTER COLUMN morning_status TYPE VARCHAR(255)");
        DB::statement("ALTER TABLE attendances ADD CONSTRAINT attendances_morning_status_check CHECK (morning_status IN ('present', 'absent', 'leave', 'late'))");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('attendances', function (Blueprint $table) {
            //
        });
    }
};
