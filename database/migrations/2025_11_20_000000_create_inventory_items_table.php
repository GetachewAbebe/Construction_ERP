<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // If the table doesn't exist, create it from scratch
        if (!Schema::hasTable('inventory_items')) {
            Schema::create('inventory_items', function (Blueprint $table) {
                $table->id();

                // Core identity
                $table->string('item_no')->nullable();          // e.g., 01, 08-A, etc.
                $table->string('name');                          // short name (e.g., "Electrical mixer 750 liter")
                $table->string('description')->nullable();       // optional long description

                // Quantities / unit
                $table->string('unit')->default('pcs');          // pcs, m, etc.
                $table->integer('qty')->default(0);

                // Locations & dates
                $table->string('store_location')->nullable();    // Waserbi site, Derba site, etc.
                $table->date('in_date')->nullable();
                $table->string('transfer_location')->nullable();
                $table->string('transfer_person')->nullable();
                $table->date('out_date')->nullable();

                // Logistics / approval
                $table->string('driver_name')->nullable();
                $table->string('plate_number')->nullable();
                $table->string('approved_by')->nullable();
                $table->text('remark')->nullable();

                // (Optional legacy fields — keep if you were already using them)
                $table->string('sku')->nullable();
                $table->string('category')->nullable();
                $table->decimal('purchase_price', 12, 2)->nullable();
                $table->date('purchase_date')->nullable();
                $table->string('location')->nullable();
                $table->string('supplier')->nullable();
                $table->string('status')->default('active');     // active|inactive|retired

                // Audit
                $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
                $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();

                $table->timestamps();

                // Helpful indexes
                $table->index(['item_no']);
                $table->index(['name']);
                $table->index(['store_location']);
            });
        } else {
            // If table already exists (e.g., in other environments), add missing columns defensively
            Schema::table('inventory_items', function (Blueprint $table) {
                foreach ([
                    'item_no' => 'string',
                    'description' => 'string',
                    'qty' => 'integer',
                    'store_location' => 'string',
                    'in_date' => 'date',
                    'transfer_location' => 'string',
                    'transfer_person' => 'string',
                    'out_date' => 'date',
                    'driver_name' => 'string',
                    'plate_number' => 'string',
                    'approved_by' => 'string',
                    'remark' => 'text',
                ] as $col => $type) {
                    if (!Schema::hasColumn('inventory_items', $col)) {
                        // add columns with sane defaults
                        match ($type) {
                            'integer' => $table->integer($col)->nullable(),
                            'date'    => $table->date($col)->nullable(),
                            'text'    => $table->text($col)->nullable(),
                            default   => $table->string($col)->nullable(),
                        };
                    }
                }
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('inventory_items');
    }
};
