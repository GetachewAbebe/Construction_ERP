<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('employees_on_leave')) {
            Schema::create('employees_on_leave', function (Blueprint $table) {
                $table->id();
                $table->foreignId('employee_id')->constrained('employees')->cascadeOnDelete();
                $table->date('start_date');
                $table->date('end_date');
                $table->string('reason')->nullable();
                $table->foreignId('approved_by')->constrained('users')->cascadeOnDelete();
                $table->timestamp('approved_at');
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('employees_on_leave');
    }
};
