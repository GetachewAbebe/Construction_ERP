<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('inventory_loans', function (Blueprint $table) {
            // approved_by
            if (! Schema::hasColumn('inventory_loans', 'approved_by')) {
                $table->unsignedBigInteger('approved_by')->nullable()->after('status');
            }

            // approved_at
            if (! Schema::hasColumn('inventory_loans', 'approved_at')) {
                $table->timestamp('approved_at')->nullable()->after('approved_by');
            }

            // rejected_by
            if (! Schema::hasColumn('inventory_loans', 'rejected_by')) {
                $table->unsignedBigInteger('rejected_by')->nullable()->after('approved_at');
            }

            // rejected_at
            if (! Schema::hasColumn('inventory_loans', 'rejected_at')) {
                $table->timestamp('rejected_at')->nullable()->after('rejected_by');
            }

            // rejection_reason
            if (! Schema::hasColumn('inventory_loans', 'rejection_reason')) {
                $table->string('rejection_reason', 500)->nullable()->after('rejected_at');
            }
        });

        // Foreign keys in a separate Schema::table so hasColumn checks above run first
        Schema::table('inventory_loans', function (Blueprint $table) {
            if (Schema::hasColumn('inventory_loans', 'approved_by')) {
                $table->foreign('approved_by')
                    ->references('id')->on('users')
                    ->nullOnDelete();
            }

            if (Schema::hasColumn('inventory_loans', 'rejected_by')) {
                $table->foreign('rejected_by')
                    ->references('id')->on('users')
                    ->nullOnDelete();
            }
        });
    }

    public function down(): void
    {
        Schema::table('inventory_loans', function (Blueprint $table) {
            // Foreign keys may or may not exist, so wrap drops defensively
            if (Schema::hasColumn('inventory_loans', 'approved_by')) {
                $table->dropForeign(['approved_by']);
            }
            if (Schema::hasColumn('inventory_loans', 'rejected_by')) {
                $table->dropForeign(['rejected_by']);
            }

            $drops = [];
            foreach ([
                'approved_by',
                'approved_at',
                'rejected_by',
                'rejected_at',
                'rejection_reason',
            ] as $column) {
                if (Schema::hasColumn('inventory_loans', $column)) {
                    $drops[] = $column;
                }
            }

            if (! empty($drops)) {
                $table->dropColumn($drops);
            }
        });
    }
};
