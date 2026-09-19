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
        if (Schema::hasTable('vendors')) {
            Schema::table('vendors', function (Blueprint $table) {
                if (! Schema::hasColumn('vendors', 'is_active')) {
                    $table->boolean('is_active')->default(true);
                }
                if (! Schema::hasColumn('vendors', 'code')) {
                    $table->string('code')->nullable();
                }
                if (! Schema::hasColumn('vendors', 'slug')) {
                    $table->string('slug')->nullable();
                }
                if (! Schema::hasColumn('vendors', 'contact_person')) {
                    $table->string('contact_person')->nullable();
                }
                if (! Schema::hasColumn('vendors', 'alternate_phone')) {
                    $table->string('alternate_phone')->nullable();
                }
                if (! Schema::hasColumn('vendors', 'address')) {
                    $table->text('address')->nullable();
                }
                if (! Schema::hasColumn('vendors', 'city')) {
                    $table->string('city')->nullable();
                }
                if (! Schema::hasColumn('vendors', 'country')) {
                    $table->string('country')->default('Ethiopia');
                }
                if (! Schema::hasColumn('vendors', 'tax_id')) {
                    $table->string('tax_id')->nullable();
                }
                if (! Schema::hasColumn('vendors', 'vat_registration_no')) {
                    $table->string('vat_registration_no')->nullable();
                }
                if (! Schema::hasColumn('vendors', 'payment_terms')) {
                    $table->string('payment_terms')->nullable();
                }
                if (! Schema::hasColumn('vendors', 'bank_details')) {
                    $table->text('bank_details')->nullable();
                }
                if (! Schema::hasColumn('vendors', 'category')) {
                    $table->string('category')->nullable();
                }
                if (! Schema::hasColumn('vendors', 'rating')) {
                    $table->decimal('rating', 3, 2)->default(5.00);
                }
                if (! Schema::hasColumn('vendors', 'internal_notes')) {
                    $table->text('internal_notes')->nullable();
                }
                if (! Schema::hasColumn('vendors', 'deleted_at')) {
                    $table->softDeletes();
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Safe no-op
    }
};
