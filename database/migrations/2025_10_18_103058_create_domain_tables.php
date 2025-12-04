<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('departments', function (Blueprint $t) {
            $t->id();
            $t->string('name')->unique();
            $t->timestamps();
        });

        Schema::create('positions', function (Blueprint $t) {
            $t->id();
            $t->string('title');
            $t->timestamps();
        });

        Schema::create('employees', function (Blueprint $t) {
            $t->id();
            $t->string('first_name');
            $t->string('last_name');
            $t->string('email')->unique();
            $t->foreignId('department_id')->nullable()->constrained()->nullOnDelete();
            $t->foreignId('position_id')->nullable()->constrained()->nullOnDelete();
            $t->date('hire_date')->nullable();
            $t->decimal('salary', 12, 2)->nullable();
            $t->timestamps();
        });

        Schema::create('vendors', function (Blueprint $t) {
            $t->id();
            $t->string('name');
            $t->string('email')->nullable();
            $t->string('phone')->nullable();
            $t->timestamps();
        });

        Schema::create('items', function (Blueprint $t) {
            $t->id();
            $t->string('sku')->unique();
            $t->string('name');
            $t->string('unit')->default('pcs');
            $t->decimal('unit_cost', 12, 2)->default(0);
            $t->timestamps();
        });

        Schema::create('locations', function (Blueprint $t) {
            $t->id();
            $t->string('name')->unique();
            $t->timestamps();
        });

        Schema::create('stock_movements', function (Blueprint $t) {
            $t->id();
            $t->foreignId('item_id')->constrained()->cascadeOnDelete();
            $t->foreignId('location_id')->constrained()->cascadeOnDelete();
            $t->enum('type', ['in','out']);
            $t->decimal('quantity', 14, 3);
            $t->text('note')->nullable();
            $t->timestamps();
            $t->index(['item_id','location_id','type']);
        });

        Schema::create('purchase_orders', function (Blueprint $t) {
            $t->id();
            $t->string('po_number')->unique();
            $t->foreignId('vendor_id')->constrained()->cascadeOnDelete();
            $t->date('po_date');
            $t->enum('status', ['draft','approved','received','cancelled'])->default('draft');
            $t->timestamps();
            $t->index(['po_number','status']);
        });

        Schema::create('purchase_order_lines', function (Blueprint $t) {
            $t->id();
            $t->foreignId('purchase_order_id')->constrained()->cascadeOnDelete();
            $t->foreignId('item_id')->constrained()->cascadeOnDelete();
            $t->decimal('quantity', 14, 3);
            $t->decimal('unit_price', 12, 2);
            $t->decimal('line_total', 14, 2);
            $t->timestamps();
        });

        Schema::create('accounts', function (Blueprint $t) {
            $t->id();
            $t->string('name')->unique();
            $t->enum('type', ['asset','liability','equity','income','expense']);
            $t->timestamps();
        });

        Schema::create('invoices', function (Blueprint $t) {
            $t->id();
            $t->string('invoice_number')->unique();
            $t->date('invoice_date');
            $t->decimal('total', 14, 2)->default(0);
            $t->enum('status', ['draft','sent','paid','void'])->default('draft');
            $t->timestamps();
        });

        Schema::create('payments', function (Blueprint $t) {
            $t->id();
            $t->foreignId('invoice_id')->constrained()->cascadeOnDelete();
            $t->date('payment_date');
            $t->decimal('amount', 14, 2);
            $t->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payments');
        Schema::dropIfExists('invoices');
        Schema::dropIfExists('accounts');
        Schema::dropIfExists('purchase_order_lines');
        Schema::dropIfExists('purchase_orders');
        Schema::dropIfExists('stock_movements');
        Schema::dropIfExists('locations');
        Schema::dropIfExists('items');
        Schema::dropIfExists('vendors');
        Schema::dropIfExists('employees');
        Schema::dropIfExists('positions');
        Schema::dropIfExists('departments');
    }
};
