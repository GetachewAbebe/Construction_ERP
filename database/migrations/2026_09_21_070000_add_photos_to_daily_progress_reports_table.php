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
        if (Schema::hasTable('daily_progress_reports') && ! Schema::hasColumn('daily_progress_reports', 'photos')) {
            Schema::table('daily_progress_reports', function (Blueprint $table) {
                $table->json('photos')->nullable()->after('safety_incidents');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('daily_progress_reports') && Schema::hasColumn('daily_progress_reports', 'photos')) {
            Schema::table('daily_progress_reports', function (Blueprint $table) {
                $table->dropColumn('photos');
            });
        }
    }
};
