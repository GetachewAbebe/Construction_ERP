<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('equipment', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // e.g. "CAT 320D Excavator"
            $table->string('plate_number')->nullable(); // or serial / asset tag e.g. "ET-3-12345"
            $table->string('type'); // Excavator, Loader, Concrete Mixer, Dump Truck, Generator, Crane, etc.
            $table->foreignId('project_id')->nullable()->constrained('projects')->nullOnDelete();
            $table->string('status')->default('operational'); // operational, maintenance, breakdown, idle
            $table->decimal('operating_hours', 10, 2)->default(0.00);
            $table->decimal('next_service_hours', 10, 2)->nullable(); // alert threshold e.g. 250h
            $table->string('fuel_type')->default('Diesel'); // Diesel, Petrol, Electric
            $table->date('purchase_date')->nullable();
            $table->decimal('purchase_cost', 15, 2)->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('equipment_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('equipment_id')->constrained('equipment')->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('log_type'); // service, repair, fuel, hours_update, breakdown
            $table->decimal('hours_at_log', 10, 2)->nullable();
            $table->decimal('cost', 12, 2)->default(0.00);
            $table->text('description')->nullable();
            $table->date('logged_at');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('equipment_logs');
        Schema::dropIfExists('equipment');
    }
};
