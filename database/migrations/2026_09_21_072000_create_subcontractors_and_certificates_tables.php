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
        if (! Schema::hasTable('subcontractors')) {
            Schema::create('subcontractors', function (Blueprint $table) {
                $table->id();
                $table->foreignId('project_id')->nullable()->constrained()->nullOnDelete();
                $table->string('name');
                $table->string('trade'); // e.g. Masonry, Steel Fixing, Electrical, Plumbing, Painting
                $table->string('contract_number')->nullable()->unique();
                $table->decimal('contract_sum', 15, 2)->default(0);
                $table->string('contact_person')->nullable();
                $table->string('phone')->nullable();
                $table->string('email')->nullable();
                $table->string('status')->default('active'); // active, completed, suspended
                $table->text('notes')->nullable();
                $table->timestamps();
                $table->softDeletes();
            });
        }

        if (! Schema::hasTable('subcontractor_certificates')) {
            Schema::create('subcontractor_certificates', function (Blueprint $table) {
                $table->id();
                $table->foreignId('subcontractor_id')->constrained()->cascadeOnDelete();
                $table->foreignId('project_id')->nullable()->constrained()->nullOnDelete();
                $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();
                $table->string('certificate_number')->unique();
                $table->date('period_start');
                $table->date('period_end');
                $table->text('work_description');
                $table->decimal('gross_amount', 15, 2);
                $table->decimal('retention_percent', 5, 2)->default(5.00); // 5% default retention
                $table->decimal('retention_amount', 15, 2)->default(0);
                $table->decimal('net_payable', 15, 2);
                $table->string('status')->default('pending'); // pending, approved, paid, rejected
                $table->text('rejection_reason')->nullable();
                $table->timestamps();
                $table->softDeletes();

                $table->index(['subcontractor_id', 'status']);
                $table->index(['project_id', 'status']);
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('subcontractor_certificates');
        Schema::dropIfExists('subcontractors');
    }
};
