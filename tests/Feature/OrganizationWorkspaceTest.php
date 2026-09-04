<?php

namespace Tests\Feature;

use App\Models\FormDp1;
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
        ])->assertRedirect(route('compliance.dashboard'));

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
            'application_fee' => 0,
            'total_fee' => 50,
            'entity_profile' => ['entity_name' => 'Second Client'],
            'processing_details' => ['purpose' => 'Testing'],
            'security_measures' => ['access' => 'Restricted'],
        ]);

        $this->post(route('compliance.organizations.switch', $first))
            ->assertRedirect();

        $this->assertCount(0, FormDp1::query()->get());
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
