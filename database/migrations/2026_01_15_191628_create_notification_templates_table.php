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
        Schema::create('notification_templates', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique()->comment('Unique identifier for the template');
            $table->string('name')->comment('Human-readable template name');
            $table->string('subject')->nullable()->comment('Email subject line');
            $table->text('body')->comment('Template content with variable placeholders');
            $table->enum('type', ['email', 'notification', 'sms'])->default('email');
            $table->json('variables')->nullable()->comment('Available variables for this template');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('notification_templates');
    }
};
