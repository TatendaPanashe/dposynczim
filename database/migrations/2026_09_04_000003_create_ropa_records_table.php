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
            $table->string('processing_activity');
            $table->text('purpose');
            $table->text('data_subject_categories');
            $table->text('personal_data_categories');
            $table->string('legal_basis');
            $table->text('recipients')->nullable();
            $table->string('retention_period');
            $table->text('security_measures');
            $table->text('cross_border_transfer')->nullable();
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
