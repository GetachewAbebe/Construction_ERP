<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        if (!Schema::hasTable('employees')) {
            Schema::create('employees', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
                $table->string('name');
                $table->string('email')->unique();
                $table->string('department')->nullable();
                $table->string('position')->nullable();
                $table->string('phone')->nullable();
                $table->date('hire_date')->nullable();
                $table->string('status')->default('Active');
                $table->timestamps();
            });
        }
    }

    public function down(): void {
        Schema::dropIfExists('employees');
    }
};
