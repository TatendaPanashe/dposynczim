<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('breach_incidents', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('organization_id')->constrained()->cascadeOnDelete();
            $table->string('reference')->unique();
            $table->string('title');
            $table->text('description');
            $table->timestamp('occurred_at');
            $table->timestamp('detected_at');
            $table->timestamp('sla_due_at')->index();
            $table->string('severity')->default('medium');
            $table->string('status')->default('open')->index();
            $table->text('affected_data_subjects')->nullable();
            $table->timestamp('dp3_submitted_at')->nullable();
            $table->text('lessons_learned')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('breach_incidents');
    }
};
