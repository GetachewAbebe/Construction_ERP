<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('employees', function (Blueprint $table) {
            // decimal(12,2) gives room for larger salaries; nullable for flexibility
            if (! Schema::hasColumn('employees', 'salary')) {
                $table->decimal('salary', 12, 2)->nullable()->after('hire_date');
            }
        });
    }

    public function down(): void
    {
        Schema::table('employees', function (Blueprint $table) {
            if (Schema::hasColumn('employees', 'salary')) {
                $table->dropColumn('salary');
            }
        });
    }
};
