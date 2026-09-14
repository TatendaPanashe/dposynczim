<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('ropa_records', 'business_function')) {
            Schema::table('ropa_records', function (Blueprint $table): void {
                $table->string('business_function')->nullable()->after('organization_id');
                $table->text('storage_location')->nullable()->after('business_function');
                $table->string('controller_name')->nullable()->after('legal_basis');
                $table->string('representative_name')->nullable()->after('controller_name');
                $table->text('retention_basis')->nullable()->after('retention_period');
                $table->string('data_classification')->nullable()->after('retention_basis');
                $table->string('processor_name')->nullable()->after('data_classification');
                $table->text('third_party_agreement')->nullable()->after('processor_name');
                $table->text('transfer_security_measures')->nullable()->after('cross_border_transfer');
                $table->text('protection_assessment')->nullable()->after('transfer_security_measures');
                $table->text('data_collection_method')->nullable()->after('protection_assessment');
                $table->text('consent_evidence')->nullable()->after('data_collection_method');
                $table->text('legitimate_interest_assessment')->nullable()->after('consent_evidence');
                $table->string('data_volume')->nullable()->after('legitimate_interest_assessment');
                $table->text('dpia_record')->nullable()->after('data_volume');
                $table->text('data_risks')->nullable()->after('dpia_record');
                $table->string('risk_impact')->nullable()->after('data_risks');
                $table->text('data_breaches')->nullable()->after('risk_impact');
                $table->text('breach_notification')->nullable()->after('data_breaches');
                $table->text('risk_actions')->nullable()->after('breach_notification');
                $table->string('action_owner')->nullable()->after('risk_actions');
                $table->date('action_due_date')->nullable()->after('action_owner');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('ropa_records', 'business_function')) {
            Schema::table('ropa_records', function (Blueprint $table): void {
                $table->dropColumn([
                    'business_function',
                    'storage_location',
                    'controller_name',
                    'representative_name',
                    'retention_basis',
                    'data_classification',
                    'processor_name',
                    'third_party_agreement',
                    'transfer_security_measures',
                    'protection_assessment',
                    'data_collection_method',
                    'consent_evidence',
                    'legitimate_interest_assessment',
                    'data_volume',
                    'dpia_record',
                    'data_risks',
                    'risk_impact',
                    'data_breaches',
                    'breach_notification',
                    'risk_actions',
                    'action_owner',
                    'action_due_date',
                ]);
            });
        }
    }
};
