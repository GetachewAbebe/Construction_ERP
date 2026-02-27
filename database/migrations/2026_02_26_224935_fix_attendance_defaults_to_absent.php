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
        DB::statement("ALTER TABLE attendances ALTER COLUMN morning_status SET DEFAULT 'absent'");
        DB::statement("ALTER TABLE attendances ALTER COLUMN afternoon_status SET DEFAULT 'absent'");
        DB::statement("ALTER TABLE attendances ALTER COLUMN total_credit SET DEFAULT 0.0");
        
        // Also update existing NULL values if any (though they were NOT NULL with 'present' default before)
        DB::statement("UPDATE attendances SET morning_status = 'absent' WHERE morning_status IS NULL");
        DB::statement("UPDATE attendances SET afternoon_status = 'absent' WHERE afternoon_status IS NULL");
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
