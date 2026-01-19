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
        Schema::create('asset_classifications', function (Blueprint $table) {
            $table->id();
            
            // Core Identity
            $table->string('name')->unique();
            $table->string('code')->unique(); // Professional code like MTRL, TOOL-HW
            $table->string('slug')->unique();
            
            // Context
            $table->text('description')->nullable();
            $table->string('icon_identifier')->nullable(); // bi-hammer, etc.
            
            // Hierarchy (Materialized Path + Parent)
            $table->foreignId('parent_id')->nullable()->constrained('asset_classifications')->nullOnDelete();
            $table->string('hierarchy_path')->nullable(); // e.g., 1/5/12/
            $table->integer('depth')->default(0);
            
            $table->timestamps();
            $table->softDeletes();
            
            // Performance Indexes
            $table->index('slug');
            $table->index('code');
            $table->index('hierarchy_path');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('asset_classifications');
    }
};
