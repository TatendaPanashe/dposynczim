<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table): void {
            $table->string('dpo_registration_number')->nullable()->after('is_admin');
            $table->text('dpo_address')->nullable()->after('dpo_registration_number');
            $table->json('dpo_qualifications')->nullable()->after('dpo_address');
            $table->string('dpo_certification_status')->nullable()->after('dpo_qualifications');
            $table->string('dpo_reporting_line')->nullable()->after('dpo_certification_status');
            $table->string('dpo_official_phone')->nullable()->after('dpo_reporting_line');
            $table->string('dpo_mobile')->nullable()->after('dpo_official_phone');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table): void {
            $table->dropColumn([
                'dpo_registration_number',
                'dpo_address',
                'dpo_qualifications',
                'dpo_certification_status',
                'dpo_reporting_line',
                'dpo_official_phone',
                'dpo_mobile',
            ]);
        });
    }
};
