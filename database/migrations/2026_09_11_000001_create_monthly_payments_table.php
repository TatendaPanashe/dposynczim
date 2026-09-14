<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('monthly_payments', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('organization_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('month', 7);
            $table->unsignedInteger('amount_cents')->default(199);
            $table->string('currency', 3)->default('USD');
            $table->string('status')->default('pending');
            $table->string('merchant_reference')->unique();
            $table->string('pesepay_reference')->nullable()->index();
            $table->string('redirect_url', 2048)->nullable();
            $table->json('metadata')->nullable();
            $table->timestamp('paid_at')->nullable();
            $table->timestamps();

            $table->index(['organization_id', 'month', 'status'], 'monthly_payments_access_lookup');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('monthly_payments');
    }
};
