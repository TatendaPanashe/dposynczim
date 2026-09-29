<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('organizations', function (Blueprint $table): void {
            $table->string('business_sector')->nullable()->after('registration_number');
            $table->string('legal_structure')->nullable()->after('business_sector');
            $table->text('physical_address')->nullable()->after('legal_structure');
            $table->text('postal_address')->nullable()->after('physical_address');
            $table->string('telephone')->nullable()->after('postal_address');
            $table->string('fax')->nullable()->after('telephone');
            $table->string('email')->nullable()->after('fax');
            $table->text('business_scope')->nullable()->after('email');
        });
    }

    public function down(): void
    {
        Schema::table('organizations', function (Blueprint $table): void {
            $table->dropColumn([
                'business_sector',
                'legal_structure',
                'physical_address',
                'postal_address',
                'telephone',
                'fax',
                'email',
                'business_scope',
            ]);
        });
    }
};
