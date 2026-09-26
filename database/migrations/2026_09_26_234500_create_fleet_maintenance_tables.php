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
        // 1. Preventive Maintenance Schedules / Service Packages
        Schema::create('equipment_maintenance_schedules', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // e.g. "250h Engine & Oil Service"
            $table->string('equipment_type')->nullable(); // Excavator, Dump Truck, Wheel Loader, etc. Null = all
            $table->integer('interval_hours')->default(250); // Operating hours interval
            $table->integer('interval_days')->nullable(); // Optional calendar interval in days (e.g. 90)
            $table->text('description')->nullable();
            $table->json('checklist_tasks')->nullable(); // Inspection checklist array
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // 2. Equipment Breakdown & Maintenance Work Orders
        Schema::create('maintenance_work_orders', function (Blueprint $table) {
            $table->id();
            $table->string('work_order_no')->unique(); // e.g. WO-2026-0001
            $table->foreignId('equipment_id')->constrained('equipment')->cascadeOnDelete();
            $table->foreignId('project_id')->nullable()->constrained('projects')->nullOnDelete();
            $table->string('order_type')->default('preventive'); // preventive, breakdown, inspection, tire_tracks
            $table->string('priority')->default('routine'); // routine, urgent, critical_downtime
            $table->string('status')->default('pending'); // pending, in_progress, completed, cancelled
            $table->string('title'); // Problem or Service Title
            $table->decimal('operating_hours', 10, 2)->default(0.00); // Hour meter reading at maintenance
            $table->text('fault_description')->nullable(); // Operator issue description
            $table->text('work_performed')->nullable(); // Mechanic action taken
            $table->foreignId('assigned_mechanic_id')->nullable()->constrained('users')->nullOnDelete();
            $table->dateTime('start_date')->nullable();
            $table->dateTime('completed_date')->nullable();
            $table->decimal('downtime_hours', 8, 2)->default(0.00); // Idle/breakdown hours lost
            $table->decimal('parts_cost', 14, 2)->default(0.00);
            $table->decimal('labor_cost', 14, 2)->default(0.00);
            $table->decimal('external_cost', 14, 2)->default(0.00);
            $table->decimal('total_cost', 14, 2)->default(0.00);
            $table->foreignId('expense_id')->nullable()->constrained('expenses')->nullOnDelete(); // Auto-posted project expense
            $table->text('notes')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('completed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->softDeletes();
            $table->timestamps();

            $table->index(['equipment_id', 'status']);
            $table->index(['project_id', 'order_type']);
        });

        // 3. Spare Parts & Lubricants Consumed in Maintenance
        Schema::create('maintenance_work_order_parts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('maintenance_work_order_id')->constrained('maintenance_work_orders')->cascadeOnDelete();
            $table->foreignId('inventory_item_id')->nullable()->constrained('inventory_items')->nullOnDelete();
            $table->string('part_name');
            $table->string('part_number')->nullable(); // OEM part # (e.g. 1R-0716)
            $table->decimal('quantity', 10, 2)->default(1.00);
            $table->string('unit_of_measurement')->default('pcs');
            $table->decimal('unit_price', 12, 2)->default(0.00);
            $table->decimal('total_price', 12, 2)->default(0.00);
            $table->timestamps();

            $table->index(['maintenance_work_order_id', 'inventory_item_id'], 'mwop_order_item_idx');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('maintenance_work_order_parts');
        Schema::dropIfExists('maintenance_work_orders');
        Schema::dropIfExists('equipment_maintenance_schedules');
    }
};
