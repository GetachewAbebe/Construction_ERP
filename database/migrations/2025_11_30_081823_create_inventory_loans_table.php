<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('inventory_loans', function (Blueprint $table) {
            $table->id();

            // Which item is being lent
            $table->foreignId('inventory_item_id')
                ->constrained('inventory_items')
                ->cascadeOnDelete();

            // Who uses/borrows it (must be an employee)
            $table->foreignId('employee_id')
                ->constrained('employees')
                ->cascadeOnDelete();

            // Who submitted the request in the system
            $table->foreignId('requested_by_user_id')
                ->constrained('users')
                ->cascadeOnDelete();

            // Who approved (Admin)
            $table->foreignId('approved_by_user_id')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->unsignedInteger('quantity');

            // pending = waiting for admin
            // approved = admin approved, item should be issued
            // rejected = admin refused
            // returned = item came back
            $table->string('status', 20)->default('pending');

            $table->date('due_date')->nullable();
            $table->timestamp('requested_at')->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->timestamp('returned_at')->nullable();

            $table->text('remarks')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('inventory_loans');
    }
};
