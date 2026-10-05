<?php

namespace Tests\Feature;

use App\Models\OrganisationComplianceObligation;
use App\Models\Organization;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class WorkspaceUserAccessTest extends TestCase
{
    use RefreshDatabase;

    public function test_compliance_officer_can_create_task_users_for_active_workspace(): void
    {
        [$manager, $organization] = $this->managerWithOrganization();

        $this->actingAs($manager)->post(route('compliance.users.store'), [
            'name' => 'Rudo Analyst',
            'email' => 'rudo@example.test',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'role' => 'task_user',
        ])->assertRedirect();

        $taskUser = User::where('email', 'rudo@example.test')->firstOrFail();
        $this->assertSame($organization->id, $taskUser->organization_id);
        $this->assertDatabaseHas('organization_user', [
            'organization_id' => $organization->id,
            'user_id' => $taskUser->id,
            'role' => 'task_user',
        ]);
    }

    public function test_task_user_dashboard_and_calendar_only_show_assigned_work(): void
    {
        [$manager, $organization] = $this->managerWithOrganization();
        $taskUser = $this->taskUser($organization);
        $assigned = OrganisationComplianceObligation::create([
            'organization_id' => $organization->id,
            'assigned_user_id' => $taskUser->id,
            'title' => 'Submit evidence pack',
            'category' => 'Data protection',
            'frequency' => 'monthly',
            'risk_level' => 'high',
            'status' => 'not_started',
            'due_at' => '2026-10-31',
        ]);
        OrganisationComplianceObligation::create([
            'organization_id' => $organization->id,
            'assigned_user_id' => $manager->id,
            'title' => 'Manager-only review',
            'category' => 'Internal',
            'frequency' => 'monthly',
            'risk_level' => 'medium',
            'status' => 'not_started',
            'due_at' => '2026-10-31',
        ]);

        $this->actingAs($taskUser)
            ->withSession(['active_organization_id' => $organization->id])
            ->get(route('compliance.dashboard'))
            ->assertOk()
            ->assertSee('Submit evidence pack')
            ->assertDontSee('Manager-only review');

        $this->actingAs($taskUser)
            ->withSession(['active_organization_id' => $organization->id])
            ->get(route('compliance.calendar.index'))
            ->assertOk()
            ->assertSee('Submit evidence pack')
            ->assertDontSee('Manager-only review')
            ->assertDontSee('Add obligation');

        $this->actingAs($taskUser)
            ->withSession(['active_organization_id' => $organization->id])
            ->get(route('compliance.calendar.show', $assigned))
            ->assertOk()
            ->assertDontSee('Save changes');
    }

    public function test_task_user_cannot_open_manager_pages_or_create_obligations(): void
    {
        [, $organization] = $this->managerWithOrganization();
        $taskUser = $this->taskUser($organization);

        $this->actingAs($taskUser)
            ->withSession(['active_organization_id' => $organization->id])
            ->get(route('compliance.users.index'))
            ->assertForbidden();

        $this->actingAs($taskUser)
            ->withSession(['active_organization_id' => $organization->id])
            ->get(route('compliance.dp1.create'))
            ->assertForbidden();

        $this->actingAs($taskUser)
            ->withSession(['active_organization_id' => $organization->id])
            ->post(route('compliance.calendar.store'), [
                'title' => 'Unauthorized task',
                'due_at' => '2026-10-31',
            ])->assertForbidden();
    }

    /**
     * @return array{0: User, 1: Organization}
     */
    private function managerWithOrganization(): array
    {
        $organization = Organization::create(['name' => 'Mosi Legal', 'slug' => 'mosi-legal']);
        $manager = User::factory()->create(['organization_id' => $organization->id]);
        $manager->organizations()->attach($organization->id, ['role' => 'dpo']);

        return [$manager, $organization];
    }

    private function taskUser(Organization $organization): User
    {
        $user = User::factory()->create(['organization_id' => $organization->id]);
        $user->organizations()->attach($organization->id, ['role' => 'task_user']);

        return $user;
    }
}
