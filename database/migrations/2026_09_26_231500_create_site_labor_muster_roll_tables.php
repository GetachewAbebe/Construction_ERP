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
        // 1. Casual Laborers (Daily site workers)
        Schema::create('casual_laborers', function (Blueprint $table) {
            $table->id();
            $table->string('worker_code')->unique(); // e.g. LAB-0001
            $table->string('first_name');
            $table->string('middle_name')->nullable();
            $table->string('last_name');
            $table->string('phone')->nullable();
            $table->string('id_card_number')->nullable(); // Kebele / National ID
            $table->string('trade'); // Daily Laborer, Mason, Carpenter, Bar Bender, Plasterer, Painter, Welder, Electrician, Plumber, Helper, Operator
            $table->string('skill_level')->default('unskilled'); // unskilled, semi_skilled, skilled, master
            $table->decimal('daily_wage_rate', 12, 2)->default(0.00); // Standard daily wage in ETB
            $table->decimal('overtime_hourly_rate', 12, 2)->nullable()->default(0.00); // Standard OT hourly rate in ETB
            $table->string('emergency_contact_name')->nullable();
            $table->string('emergency_contact_phone')->nullable();
            $table->foreignId('project_id')->nullable()->constrained('projects')->nullOnDelete(); // Default site assignment
            $table->boolean('is_active')->default(true);
            $table->text('notes')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->softDeletes();
            $table->timestamps();

            $table->index(['trade', 'is_active']);
            $table->index('project_id');
        });

        // 2. Muster Rolls (Daily / Periodical site labor attendance & wage sheets)
        Schema::create('muster_rolls', function (Blueprint $table) {
            $table->id();
            $table->string('muster_roll_no')->unique(); // e.g. MR-2026-0001
            $table->foreignId('project_id')->constrained('projects')->cascadeOnDelete();
            $table->date('date');
            $table->string('title')->nullable(); // e.g. "2nd Floor Slab Concrete Casting & Steel Fixing"
            $table->foreignId('supervisor_id')->nullable()->constrained('users')->nullOnDelete(); // Site foreman / supervisor who prepared it
            $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('approved_at')->nullable();
            $table->string('status')->default('draft'); // draft, submitted, approved, paid, cancelled
            $table->integer('total_workers')->default(0);
            $table->decimal('total_regular_amount', 14, 2)->default(0.00);
            $table->decimal('total_overtime_amount', 14, 2)->default(0.00);
            $table->decimal('total_deductions', 14, 2)->default(0.00);
            $table->decimal('total_net_amount', 14, 2)->default(0.00);
            $table->string('payment_method')->nullable(); // cash, telebirr, cbe_birr, bank_transfer
            $table->foreignId('paid_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('paid_at')->nullable();
            $table->string('payout_reference')->nullable(); // Transaction reference / Petty cash voucher #
            $table->foreignId('expense_id')->nullable()->constrained('expenses')->nullOnDelete(); // Auto-posted project expense
            $table->text('notes')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->softDeletes();
            $table->timestamps();

            $table->index(['project_id', 'date']);
            $table->index('status');
        });

        // 3. Muster Roll Line Items (Worker-specific daily attendance & wage breakdown)
        Schema::create('muster_roll_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('muster_roll_id')->constrained('muster_rolls')->cascadeOnDelete();
            $table->foreignId('casual_laborer_id')->constrained('casual_laborers')->cascadeOnDelete();
            $table->string('trade');
            $table->string('attendance_status')->default('full_day'); // full_day, half_day, absent, overtime_only
            $table->decimal('days_worked', 4, 2)->default(1.00); // 1.00 = full day, 0.50 = half day, 0 = absent
            $table->decimal('daily_rate', 12, 2)->default(0.00);
            $table->decimal('regular_amount', 12, 2)->default(0.00);
            $table->decimal('overtime_hours', 6, 2)->default(0.00);
            $table->decimal('overtime_rate', 12, 2)->default(0.00);
            $table->decimal('overtime_amount', 12, 2)->default(0.00);
            $table->decimal('deduction_amount', 12, 2)->default(0.00); // Advance recovery / fines
            $table->decimal('net_amount', 12, 2)->default(0.00);
            $table->string('task_assigned')->nullable(); // e.g. "Concrete vibrator operation", "Bar bending"
            $table->boolean('signed')->default(false); // Disbursed & worker signed/thumbprinted
            $table->string('remarks')->nullable();
            $table->timestamps();

            $table->index(['muster_roll_id', 'casual_laborer_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('muster_roll_items');
        Schema::dropIfExists('muster_rolls');
        Schema::dropIfExists('casual_laborers');
    }
};
