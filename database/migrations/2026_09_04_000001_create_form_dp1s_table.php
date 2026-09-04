<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('form_dp1s', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('organization_id')->constrained()->cascadeOnDelete();
            $table->string('status')->default('draft')->index();
            $table->unsignedInteger('data_subject_count');
            $table->string('tier');
            $table->decimal('registration_fee', 12, 2);
            $table->decimal('application_fee', 12, 2);
            $table->decimal('total_fee', 12, 2);
            $table->text('entity_profile');
            $table->text('processing_details');
            $table->text('sensitive_data_details')->nullable();
            $table->text('processors')->nullable();
            $table->text('cross_border_transfers')->nullable();
            $table->text('security_measures');
            $table->json('attachments')->nullable();
            $table->timestamp('submitted_at')->nullable();
            $table->date('renewal_due_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('form_dp1s');
    }
};
