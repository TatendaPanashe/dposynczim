<?php

namespace Tests\Feature;

use App\Models\FormDp1;
use App\Models\FormDp2;
use App\Models\Organization;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OrganizationWorkspaceTest extends TestCase
{
    use RefreshDatabase;

    public function test_a_dpo_can_add_and_switch_between_client_organisations(): void
    {
        $first = Organization::create(['name' => 'First Client', 'slug' => 'first-client']);
        $user = User::factory()->create(['organization_id' => $first->id]);
        $user->organizations()->attach($first->id, ['role' => 'dpo']);

        $this->actingAs($user)->post(route('compliance.organizations.store'), [
            'name' => 'Second Client',
            'registration_number' => 'REG-2',
        ])->assertRedirect(route('compliance.dp2.create'));

        $second = Organization::where('slug', 'like', 'second-client-%')->firstOrFail();
        $this->assertDatabaseHas('organization_user', [
            'organization_id' => $second->id,
            'user_id' => $user->id,
            'role' => 'dpo',
        ]);

        FormDp1::create([
            'organization_id' => $second->id,
            'data_subject_count' => 50,
            'tier' => 'Tier 1',
            'registration_fee' => 50,
            'application_fee' => 30,
            'total_fee' => 80,
            'entity_profile' => ['entity_name' => 'Second Client'],
            'processing_details' => ['purpose' => 'Testing'],
            'security_measures' => ['access' => 'Restricted'],
        ]);

        $this->post(route('compliance.organizations.switch', $first))
            ->assertRedirect();

        $this->assertCount(0, FormDp1::query()->get());
    }

    public function test_dpo_profile_details_prefill_client_dp2_applications(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)->put(route('compliance.dpo-profile.update'), [
            'name' => 'Tendai Moyo',
            'email' => 'tendai@example.test',
            'dpo_registration_number' => 'DPO-123',
            'dpo_address' => '10 Nelson Mandela Avenue, Harare',
            'dpo_qualifications' => "LLB\nCIPP/E",
            'dpo_certification_status' => 'Certified',
            'dpo_reporting_line' => 'Board of Directors',
            'dpo_official_phone' => '0242000000',
            'dpo_mobile' => '0770000000',
        ])->assertRedirect(route('compliance.organizations.index'));

        $organization = Organization::create(['name' => 'Client', 'slug' => 'client']);
        $user->organizations()->attach($organization->id, ['role' => 'dpo']);
        $this->withSession(['active_organization_id' => $organization->id]);

        $this->get(route('compliance.dp2.create'))
            ->assertOk()
            ->assertSee('value="Tendai Moyo"', false)
            ->assertSee('value="DPO-123"', false)
            ->assertSee('value="tendai@example.test"', false)
            ->assertSee('value="0242000000"', false)
            ->assertSee('value="0770000000"', false)
            ->assertSee('10 Nelson Mandela Avenue, Harare')
            ->assertSee('Board of Directors');
    }

    public function test_appointed_dpo_details_prefill_client_dp1_applications(): void
    {
        $organization = Organization::create([
            'name' => 'Client',
            'slug' => 'client',
            'registration_number' => 'REG-001',
            'business_sector' => 'Legal services',
            'legal_structure' => 'Private company',
            'physical_address' => '1 Samora Machel Avenue, Harare',
        ]);
        $user = User::factory()->create(['organization_id' => $organization->id]);
        $user->organizations()->attach($organization->id, ['role' => 'dpo']);
        FormDp2::create([
            'organization_id' => $organization->id,
            'full_name' => 'Tendai Moyo',
            'controller_name' => 'Client',
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
            'official_phone' => '0242111111',
            'dpo_mobile' => '0771000000',
            'appointment_declaration' => 'I accept the appointment.',
            'appointed_at' => '2026-09-04',
            'status' => 'active',
        ]);

        $this->actingAs($user)
            ->withSession(['active_organization_id' => $organization->id])
            ->get(route('compliance.dp1.create'))
            ->assertOk()
            ->assertSee('value="Tendai Moyo"', false)
            ->assertSee('value="0242111111"', false)
            ->assertSee('value="dpo@example.test"', false);
    }

    public function test_a_dpo_cannot_switch_to_an_unrelated_organisation(): void
    {
        $client = Organization::create(['name' => 'Client', 'slug' => 'client']);
        $unrelated = Organization::create(['name' => 'Unrelated', 'slug' => 'unrelated']);
        $user = User::factory()->create(['organization_id' => $client->id]);
        $user->organizations()->attach($client->id, ['role' => 'dpo']);

        $this->actingAs($user)
            ->post(route('compliance.organizations.switch', $unrelated))
            ->assertForbidden();
    }
}
