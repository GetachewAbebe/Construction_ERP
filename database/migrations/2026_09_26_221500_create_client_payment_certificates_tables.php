<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations for Client Progress Billing (Client IPC & Retention Tracking).
     */
    public function up(): void
    {
        // 1. Client Interim Payment Certificates (Client IPC)
        Schema::create('client_payment_certificates', function (Blueprint $table) {
            $table->id();
            $table->string('certificate_no')->unique();
            $table->foreignId('project_id')->constrained('projects')->cascadeOnDelete();
            $table->integer('ipc_sequence')->default(1);
            $table->date('period_start');
            $table->date('period_end');
            $table->date('submission_date');
            $table->date('certification_date')->nullable();
            $table->date('payment_due_date')->nullable();
            $table->string('client_name')->nullable();
            $table->string('consultant_name')->nullable();
            $table->text('work_description')->nullable();

            // Financial breakdown
            $table->decimal('cumulative_gross_amount', 14, 2)->default(0);
            $table->decimal('previous_gross_amount', 14, 2)->default(0);
            $table->decimal('current_gross_amount', 14, 2)->default(0);
            $table->decimal('materials_on_site', 14, 2)->default(0);
            $table->decimal('retention_rate', 5, 2)->default(5.00); // 5% standard retention
            $table->decimal('retention_deduction', 14, 2)->default(0);
            $table->decimal('advance_recoupment_rate', 5, 2)->default(0.00); // Advance recovery %
            $table->decimal('advance_deduction', 14, 2)->default(0);
            $table->decimal('other_deductions', 14, 2)->default(0);
            $table->decimal('subtotal_net_amount', 14, 2)->default(0);
            $table->decimal('tax_rate', 5, 2)->default(15.00); // 15% VAT
            $table->decimal('tax_amount', 14, 2)->default(0);
            $table->decimal('total_certified_amount', 14, 2)->default(0);
            $table->decimal('amount_paid', 14, 2)->default(0);
            $table->decimal('balance_due', 14, 2)->default(0);

            // Workflow status
            $table->string('status')->default('submitted'); // draft, submitted, certified, partially_paid, paid, disputed
            $table->foreignId('prepared_by')->constrained('users')->cascadeOnDelete();
            $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['project_id', 'status']);
            $table->index('submission_date');
        });

        // 2. Client Payment Receipts (Cash Collections against IPC)
        Schema::create('client_payment_receipts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('client_payment_certificate_id')->constrained('client_payment_certificates')->cascadeOnDelete();
            $table->string('receipt_no')->unique();
            $table->date('payment_date');
            $table->decimal('amount', 14, 2);
            $table->string('payment_method')->default('Bank Transfer'); // Bank Transfer, CPO, Check, Cash
            $table->string('bank_name')->nullable();
            $table->string('transaction_reference')->nullable(); // CPO/Slip reference
            $table->foreignId('received_by')->constrained('users')->cascadeOnDelete();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index('client_payment_certificate_id');
            $table->index('payment_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('client_payment_receipts');
        Schema::dropIfExists('client_payment_certificates');
    }
};
