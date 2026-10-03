<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('compliance_categories', function (Blueprint $table): void {
            $table->id();
            $table->string('name')->unique();
            $table->string('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('compliance_obligation_templates', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('compliance_category_id')->nullable()->constrained()->nullOnDelete();
            $table->string('title');
            $table->string('short_code')->unique();
            $table->text('description')->nullable();
            $table->string('regulator')->nullable();
            $table->string('jurisdiction')->default('Zimbabwe');
            $table->string('business_type')->nullable();
            $table->string('frequency')->default('annual');
            $table->json('due_date_rule')->nullable();
            $table->date('manual_due_date')->nullable();
            $table->json('reminder_days')->nullable();
            $table->boolean('evidence_required')->default(true);
            $table->boolean('is_active')->default(true);
            $table->string('risk_level')->default('medium');
            $table->string('legal_reference')->nullable();
            $table->boolean('is_configurable_template')->default(true);
            $table->timestamps();
            $table->softDeletes();
            $table->index(['is_active', 'risk_level']);
        });

        Schema::create('compliance_template_checklist_items', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('compliance_obligation_template_id')->constrained()->cascadeOnDelete();
            $table->string('title');
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->timestamps();
        });

        Schema::create('organisation_compliance_obligations', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('organization_id')->constrained()->cascadeOnDelete();
            $table->foreignId('compliance_obligation_template_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('parent_obligation_id')->nullable()->constrained('organisation_compliance_obligations')->nullOnDelete();
            $table->foreignId('assigned_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('reviewer_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('completed_by_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('title');
            $table->string('short_code')->nullable();
            $table->text('description')->nullable();
            $table->string('category')->nullable();
            $table->string('regulator')->nullable();
            $table->string('jurisdiction')->nullable();
            $table->string('frequency')->default('once-off');
            $table->json('reminder_days')->nullable();
            $table->boolean('evidence_required')->default(false);
            $table->string('risk_level')->default('medium');
            $table->string('legal_reference')->nullable();
            $table->string('status')->default('not_started');
            $table->date('due_at');
            $table->date('first_due_at')->nullable();
            $table->date('completed_at')->nullable();
            $table->timestamp('reviewed_at')->nullable();
            $table->text('waiver_reason')->nullable();
            $table->date('waived_at')->nullable();
            $table->string('linked_record_type')->nullable();
            $table->unsignedBigInteger('linked_record_id')->nullable();
            $table->json('linked_record_snapshot')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();
            $table->index(['organization_id', 'due_at', 'status']);
            $table->index(['assigned_user_id', 'due_at']);
            $table->index(['linked_record_type', 'linked_record_id']);
        });

        Schema::create('compliance_obligation_checklist_items', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('organisation_compliance_obligation_id')->constrained()->cascadeOnDelete();
            $table->string('title');
            $table->boolean('is_completed')->default(false);
            $table->timestamp('completed_at')->nullable();
            $table->foreignId('completed_by_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->timestamps();
        });

        Schema::create('compliance_obligation_evidence', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('organisation_compliance_obligation_id')->constrained()->cascadeOnDelete();
            $table->foreignId('uploaded_by_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('label');
            $table->string('disk')->default('private');
            $table->string('path')->nullable();
            $table->string('linked_record_type')->nullable();
            $table->unsignedBigInteger('linked_record_id')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->index(['linked_record_type', 'linked_record_id']);
        });

        Schema::create('compliance_obligation_activity_logs', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('organisation_compliance_obligation_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('action');
            $table->json('properties')->nullable();
            $table->timestamps();
            $table->index(['action', 'created_at']);
        });

        Schema::create('compliance_reminder_logs', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('organisation_compliance_obligation_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('reminder_key');
            $table->integer('days_before_due')->nullable();
            $table->timestamp('sent_at');
            $table->json('channels')->nullable();
            $table->timestamps();
            $table->unique(['organisation_compliance_obligation_id', 'user_id', 'reminder_key'], 'compliance_reminder_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('compliance_reminder_logs');
        Schema::dropIfExists('compliance_obligation_activity_logs');
        Schema::dropIfExists('compliance_obligation_evidence');
        Schema::dropIfExists('compliance_obligation_checklist_items');
        Schema::dropIfExists('organisation_compliance_obligations');
        Schema::dropIfExists('compliance_template_checklist_items');
        Schema::dropIfExists('compliance_obligation_templates');
        Schema::dropIfExists('compliance_categories');
    }
};
