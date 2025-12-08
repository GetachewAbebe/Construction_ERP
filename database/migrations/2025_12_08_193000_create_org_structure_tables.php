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
        // 1. Create Departments Table
        if (!Schema::hasTable('departments')) {
            Schema::create('departments', function (Blueprint $table) {
                $table->id();
                $table->string('name')->unique();
                $table->timestamps();
            });
        }

        // 2. Create Positions Table
        if (!Schema::hasTable('positions')) {
            Schema::create('positions', function (Blueprint $table) {
                $table->id();
                $table->string('title')->unique();
                $table->timestamps();
            });
        }

        // 3. Add FK columns to Employees Table
        Schema::table('employees', function (Blueprint $table) {
            if (!Schema::hasColumn('employees', 'department_id')) {
                $table->foreignId('department_id')->nullable()->constrained('departments')->nullOnDelete();
            }
            if (!Schema::hasColumn('employees', 'position_id')) {
                $table->foreignId('position_id')->nullable()->constrained('positions')->nullOnDelete();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('employees', function (Blueprint $table) {
            $drops = [];
            if (Schema::hasColumn('employees', 'department_id')) $drops[] = 'department_id';
            if (Schema::hasColumn('employees', 'position_id')) $drops[] = 'position_id';
            
            if (!empty($drops)) {
                $table->dropForeign(['department_id']);
                $table->dropForeign(['position_id']);
                $table->dropColumn($drops);
            }
        });

        Schema::dropIfExists('positions');
        Schema::dropIfExists('departments');
    }
};
