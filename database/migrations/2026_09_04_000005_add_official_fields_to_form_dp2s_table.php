<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('form_dp2s', function (Blueprint $table): void {
            $table->string('controller_license_number')->nullable()->after('full_name');
            $table->string('controller_name')->nullable()->after('controller_license_number');
            $table->text('controller_physical_address')->nullable()->after('controller_license_number');
            $table->text('controller_postal_address')->nullable()->after('controller_physical_address');
            $table->string('controller_telephone')->nullable()->after('controller_postal_address');
            $table->string('controller_fax')->nullable()->after('controller_telephone');
            $table->string('controller_email')->nullable()->after('controller_fax');
            $table->text('business_scope')->nullable()->after('controller_email');
            $table->string('dpo_registration_number')->nullable()->after('business_scope');
            $table->text('dpo_address')->nullable()->after('dpo_registration_number');
            $table->string('dpo_mobile')->nullable()->after('official_phone');
        });
    }

    public function down(): void
    {
        Schema::table('form_dp2s', function (Blueprint $table): void {
            $table->dropColumn([
                'controller_license_number', 'controller_name', 'controller_physical_address',
                'controller_postal_address', 'controller_telephone', 'controller_fax',
                'controller_email', 'business_scope', 'dpo_registration_number',
                'dpo_address', 'dpo_mobile',
            ]);
        });
    }
};
