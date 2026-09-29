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
        Schema::create('compliance_forms', function (Blueprint $table) {
            $table->id();
            $table->foreignId('organization_id')->constrained()->cascadeOnDelete();
            $table->string('type');
            $table->string('title');
            $table->string('status')->default('draft');
            $table->string('owner')->nullable();
            $table->string('reference')->nullable();
            $table->date('effective_at')->nullable();
            $table->date('expires_at')->nullable();
            $table->date('review_due_at')->nullable();
            $table->text('purpose')->nullable();
            $table->text('data_subjects')->nullable();
            $table->text('legal_basis')->nullable();
            $table->text('authorisation_details')->nullable();
            $table->text('safeguards')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index(['organization_id', 'type']);
            $table->index(['organization_id', 'status']);
            $table->index(['organization_id', 'review_due_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('compliance_forms');
    }
};
