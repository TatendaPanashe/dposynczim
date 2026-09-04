<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('form_dp2s', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('organization_id')->constrained()->cascadeOnDelete();
            $table->string('full_name');
            $table->text('qualifications');
            $table->string('certification_status');
            $table->string('reporting_line');
            $table->string('official_email');
            $table->string('official_phone');
            $table->text('appointment_declaration');
            $table->date('appointed_at');
            $table->string('status')->default('draft');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('form_dp2s');
    }
};
