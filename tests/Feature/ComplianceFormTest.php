<?php

namespace Tests\Feature;

use App\Models\ComplianceForm;
use App\Models\Organization;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ComplianceFormTest extends TestCase
{
    use RefreshDatabase;

    public function test_a_dpo_can_record_a_consent_form_for_the_active_organisation(): void
    {
        [$user, $organization] = $this->userWithOrganisation();

        $this->actingAs($user)->post(route('compliance.forms.store'), [
            'type' => 'consent',
            'title' => 'Customer marketing consent',
            'status' => 'active',
            'owner' => 'Marketing Lead',
            'reference' => 'CONS-001',
            'effective_at' => '2026-09-29',
            'review_due_at' => '2027-03-29',
            'purpose' => 'Record customer consent for promotional communication.',
            'data_subjects' => 'Customers',
            'legal_basis' => 'Consent',
            'authorisation_details' => 'Captured through the onboarding form.',
            'safeguards' => 'Double opt-in and withdrawal log.',
        ])->assertRedirect(route('compliance.forms.index'));

        $this->assertDatabaseHas('compliance_forms', [
            'organization_id' => $organization->id,
            'type' => 'consent',
            'title' => 'Customer marketing consent',
            'status' => 'active',
        ]);
    }

    public function test_compliance_forms_register_is_visible(): void
    {
        [$user, $organization] = $this->userWithOrganisation();

        ComplianceForm::create([
            'organization_id' => $organization->id,
            'type' => 'cross_border_authorisation',
            'title' => 'Cloud backup transfer approval',
            'status' => 'submitted',
        ]);

        $this->actingAs($user)->get(route('compliance.forms.index'))
            ->assertOk()
            ->assertSee('Compliance forms')
            ->assertSee('Cloud backup transfer approval')
            ->assertSee('Cross-border authorisation');
    }

    public function test_dashboard_shows_organisation_compliance_statuses(): void
    {
        [$user, $organization] = $this->userWithOrganisation([
            'business_sector' => 'Financial services',
            'physical_address' => '10 Samora Machel Avenue, Harare',
        ]);

        ComplianceForm::create([
            'organization_id' => $organization->id,
            'type' => 'general',
            'title' => 'Privacy notice approval',
            'status' => 'approved',
        ]);

        $this->actingAs($user)->get(route('compliance.dashboard'))
            ->assertOk()
            ->assertSee('Organisation compliance status')
            ->assertSee('Consent forms')
            ->assertSee('Cross-border authorisation')
            ->assertSee('General compliance forms')
            ->assertSee('Compliance form statuses');
    }

    /**
     * @param  array<string, mixed>  $organizationAttributes
     * @return array{0: User, 1: Organization}
     */
    private function userWithOrganisation(array $organizationAttributes = []): array
    {
        $organization = Organization::create($organizationAttributes + [
            'name' => 'Mosi Legal',
            'slug' => 'mosi-legal',
        ]);

        $user = User::factory()->create([
            'organization_id' => $organization->id,
            'dpo_address' => '10 Nelson Mandela Avenue, Harare',
            'dpo_qualifications' => ['LLB'],
            'dpo_certification_status' => 'Certified',
            'dpo_reporting_line' => 'Board of Directors',
            'dpo_official_phone' => '0242000000',
            'dpo_mobile' => '0770000000',
        ]);

        $user->organizations()->attach($organization->id, ['role' => 'dpo']);

        return [$user, $organization];
    }
}
