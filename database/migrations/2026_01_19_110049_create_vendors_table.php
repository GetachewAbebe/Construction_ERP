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
        Schema::create('vendors', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('code')->unique(); // Professional identifier
            $table->string('slug')->unique();
            
            // Primary Contact
            $table->string('contact_person')->nullable();
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->string('alternate_phone')->nullable();
            
            // Address Details
            $table->text('address')->nullable();
            $table->string('city')->nullable();
            $table->string('country')->default('Ethiopia');
            
            // Financial / Legal
            $table->string('tax_id')->nullable(); // TIN
            $table->string('vat_registration_no')->nullable();
            $table->string('payment_terms')->nullable(); // Net 30, COD, etc.
            $table->text('bank_details')->nullable();
            
            // Categorization & Metadata
            $table->string('category')->nullable(); // Supplier Type (e.g., Raw Materials, Machinery)
            $table->decimal('rating', 3, 2)->default(5.00); // 1.00 to 5.00
            $table->boolean('is_active')->default(true);
            $table->text('internal_notes')->nullable();
            
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vendors');
    }
};
