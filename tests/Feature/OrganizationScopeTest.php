<?php

namespace Tests\Feature;

use App\Models\FormDp1;
use App\Models\Organization;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OrganizationScopeTest extends TestCase
{
    use RefreshDatabase;

    public function test_tenant_queries_only_return_the_authenticated_organizations_records(): void
    {
        $firstOrganization = Organization::create([
            'name' => 'First organization',
            'slug' => 'first-organization',
        ]);
        $secondOrganization = Organization::create([
            'name' => 'Second organization',
            'slug' => 'second-organization',
        ]);

        FormDp1::create($this->draftAttributes($firstOrganization));
        FormDp1::create($this->draftAttributes($secondOrganization));

        $this->actingAs(User::factory()->create(['organization_id' => $firstOrganization->id]));

        $this->assertCount(1, FormDp1::query()->get());
        $this->assertSame($firstOrganization->id, FormDp1::query()->firstOrFail()->organization_id);
    }

    public function test_tenant_queries_are_empty_without_an_authenticated_organization(): void
    {
        $organization = Organization::create([
            'name' => 'Private organization',
            'slug' => 'private-organization',
        ]);
        FormDp1::create($this->draftAttributes($organization));

        $this->assertCount(0, FormDp1::query()->get());
    }

    /** @return array<string, mixed> */
    private function draftAttributes(Organization $organization): array
    {
        return [
            'organization_id' => $organization->id,
            'data_subject_count' => 50,
            'tier' => 'Tier 1',
            'registration_fee' => 50,
            'application_fee' => 0,
            'total_fee' => 50,
            'entity_profile' => ['name' => 'Example'],
            'processing_details' => ['purpose' => 'Testing'],
            'security_measures' => ['access' => 'Restricted'],
        ];
    }
}
