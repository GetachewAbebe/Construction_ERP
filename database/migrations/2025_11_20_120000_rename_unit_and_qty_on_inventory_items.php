<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('inventory_items', function (Blueprint $table) {
            // Rename columns
            if (Schema::hasColumn('inventory_items', 'unit')) {
                $table->renameColumn('unit', 'unit_of_measurement');
            }
            if (Schema::hasColumn('inventory_items', 'qty')) {
                $table->renameColumn('qty', 'quantity');
            }
        });
    }

    public function down(): void
    {
        Schema::table('inventory_items', function (Blueprint $table) {
            if (Schema::hasColumn('inventory_items', 'unit_of_measurement')) {
                $table->renameColumn('unit_of_measurement', 'unit');
            }
            if (Schema::hasColumn('inventory_items', 'quantity')) {
                $table->renameColumn('quantity', 'qty');
            }
        });
    }
};
