<?php

namespace Tests\Feature;

use App\Models\FormDp2;
use App\Models\Organization;
use App\Models\RopaRecord;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthAndExportTest extends TestCase
{
    use RefreshDatabase;

    public function test_a_visitor_can_create_an_organisation_workspace(): void
    {
        $response = $this->post(route('register.store'), [
            'organization_name' => 'Mosi Legal',
            'name' => 'Tendai Moyo',
            'email' => 'tendai@example.test',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertRedirect(route('compliance.dashboard'));
        $this->assertAuthenticatedAs(User::where('email', 'tendai@example.test')->firstOrFail());
        $this->assertDatabaseHas('organizations', ['name' => 'Mosi Legal']);
    }

    public function test_a_ropa_record_downloads_as_an_xls_workbook(): void
    {
        $organization = Organization::create(['name' => 'Mosi Legal', 'slug' => 'mosi-legal']);
        $user = User::factory()->create(['organization_id' => $organization->id]);
        $record = RopaRecord::create([
            'organization_id' => $organization->id,
            'processing_activity' => 'Customer onboarding',
            'purpose' => 'Open and manage customer accounts',
            'data_subject_categories' => ['Customers'],
            'personal_data_categories' => ['Identity data'],
            'legal_basis' => 'Contract',
            'recipients' => ['Finance team'],
            'retention_period' => 'Seven years',
            'security_measures' => ['Role-based access'],
            'cross_border_transfer' => [],
            'owner' => 'Operations',
        ]);
        RopaRecord::create([
            'organization_id' => $organization->id,
            'processing_activity' => 'Supplier due diligence',
            'purpose' => 'Assess service providers',
            'data_subject_categories' => ['Suppliers'],
            'personal_data_categories' => ['Contact data'],
            'legal_basis' => 'Legitimate interest',
            'recipients' => [],
            'retention_period' => 'Three years',
            'security_measures' => ['Access review'],
            'cross_border_transfer' => [],
            'owner' => 'Procurement',
        ]);

        $response = $this->actingAs($user)->get(route('compliance.ropa.download'));

        $response->assertOk();
        $response->assertHeader('Content-Type', 'application/vnd.ms-excel; charset=UTF-8');
        $response->assertHeader('Content-Disposition', 'attachment; filename=ROPA-register.xls');
        $this->assertStringContainsString('Customer onboarding', $response->streamedContent());
        $this->assertStringContainsString('Supplier due diligence', $response->streamedContent());
    }

    public function test_a_dp2_download_uses_the_official_pdf_template(): void
    {
        $organization = Organization::create(['name' => 'Mosi Legal', 'slug' => 'mosi-legal']);
        $user = User::factory()->create(['organization_id' => $organization->id]);
        $form = FormDp2::create([
            'organization_id' => $organization->id,
            'full_name' => 'Tendai Moyo',
            'controller_name' => 'Mosi Legal',
            'controller_license_number' => 'LIC-001',
            'controller_physical_address' => 'Harare',
            'controller_postal_address' => 'P.O. Box 1, Harare',
            'controller_telephone' => '0242000000',
            'controller_email' => 'legal@example.test',
            'business_scope' => 'Legal services',
            'dpo_address' => 'Harare',
            'qualifications' => ['CIPP/E'],
            'certification_status' => 'Certified',
            'reporting_line' => 'Board',
            'official_email' => 'dpo@example.test',
            'official_phone' => '0770000000',
            'dpo_mobile' => '0771000000',
            'appointment_declaration' => 'I accept the appointment.',
            'appointed_at' => '2026-09-04',
            'status' => 'active',
        ]);

        $response = $this->actingAs($user)->get(route('compliance.dp2.download', $form));

        $response->assertOk();
        $response->assertHeader('Content-Type', 'application/pdf');
        $this->assertStringStartsWith('%PDF-', $response->getContent());
    }
}
