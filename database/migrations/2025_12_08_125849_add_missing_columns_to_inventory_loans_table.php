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
        Schema::table('inventory_loans', function (Blueprint $table) {
            // Add notes column (controller uses this instead of remarks)
            if (! Schema::hasColumn('inventory_loans', 'notes')) {
                $table->text('notes')->nullable()->after('remarks');
            }

            // Add quantity column (controller uses this for loan quantity)
            if (! Schema::hasColumn('inventory_loans', 'quantity')) {
                $table->unsignedInteger('quantity')->default(1)->after('approved_by_user_id');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('inventory_loans', function (Blueprint $table) {
            $drops = [];

            if (Schema::hasColumn('inventory_loans', 'notes')) {
                $drops[] = 'notes';
            }

            if (Schema::hasColumn('inventory_loans', 'quantity')) {
                $drops[] = 'quantity';
            }

            if (! empty($drops)) {
                $table->dropColumn($drops);
            }
        });
    }
};
