<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ropa_records', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('organization_id')->constrained()->cascadeOnDelete();
            $table->string('business_function')->nullable();
            $table->text('storage_location')->nullable();
            $table->string('processing_activity');
            $table->text('purpose');
            $table->text('data_subject_categories');
            $table->text('personal_data_categories');
            $table->string('legal_basis');
            $table->string('controller_name')->nullable();
            $table->string('representative_name')->nullable();
            $table->text('recipients')->nullable();
            $table->string('retention_period');
            $table->text('retention_basis')->nullable();
            $table->string('data_classification')->nullable();
            $table->string('processor_name')->nullable();
            $table->text('third_party_agreement')->nullable();
            $table->text('security_measures');
            $table->text('cross_border_transfer')->nullable();
            $table->text('transfer_security_measures')->nullable();
            $table->text('protection_assessment')->nullable();
            $table->text('data_collection_method')->nullable();
            $table->text('consent_evidence')->nullable();
            $table->text('legitimate_interest_assessment')->nullable();
            $table->string('data_volume')->nullable();
            $table->text('dpia_record')->nullable();
            $table->text('data_risks')->nullable();
            $table->string('risk_impact')->nullable();
            $table->text('data_breaches')->nullable();
            $table->text('breach_notification')->nullable();
            $table->text('risk_actions')->nullable();
            $table->string('action_owner')->nullable();
            $table->date('action_due_date')->nullable();
            $table->string('owner');
            $table->date('reviewed_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ropa_records');
    }
};
