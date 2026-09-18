<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('daily_progress_reports', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained('projects')->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->date('report_date');
            $table->string('weather')->default('Clear / Sunny'); // Clear / Sunny, Rainy / Muddy, Windy, Extreme Heat
            $table->unsignedInteger('manpower_count')->default(0); // Total tradesmen & laborers on site
            $table->text('work_performed'); // Narrative of civil/structural tasks completed
            $table->text('materials_received')->nullable(); // Delivery notes on site (e.g. 200 bags cement)
            $table->text('machinery_deployed')->nullable(); // Heavy equipment working on site
            $table->text('safety_incidents')->nullable(); // HSE notes or "Zero incidents"
            $table->string('status')->default('submitted'); // submitted, reviewed, approved
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('daily_progress_reports');
    }
};
