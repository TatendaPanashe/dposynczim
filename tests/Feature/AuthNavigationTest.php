<?php

namespace Tests\Feature;

use App\Models\FormDp2;
use App\Models\Organization;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthNavigationTest extends TestCase
{
    use RefreshDatabase;

    public function test_login_and_register_pages_keep_authenticated_users_on_the_same_host(): void
    {
        $organization = Organization::create(['name' => 'Mosi Legal', 'slug' => 'mosi-legal']);
        $user = User::factory()->create(['organization_id' => $organization->id]);

        $this->actingAs($user)->get(route('login'))
            ->assertRedirect('/compliance/dpo-profile');

        $this->actingAs($user)->get(route('register'))
            ->assertRedirect('/compliance/dpo-profile');
    }

    public function test_authenticated_user_with_appointed_dpo_is_not_forced_to_complete_personal_dpo_profile(): void
    {
        $organization = Organization::create(['name' => 'Mosi Legal', 'slug' => 'mosi-legal']);
        $user = User::factory()->create(['organization_id' => $organization->id]);
        $user->organizations()->attach($organization->id, ['role' => 'dpo']);

        FormDp2::create([
            'organization_id' => $organization->id,
            'full_name' => 'Tendai Moyo',
            'controller_name' => 'Mosi Legal',
            'controller_physical_address' => '10 Samora Machel Avenue, Harare',
            'controller_email' => 'privacy@mosi.test',
            'business_scope' => 'Legal services',
            'qualifications' => ['LLB'],
            'certification_status' => 'Certified',
            'reporting_line' => 'Board of Directors',
            'official_email' => 'tendai@example.test',
            'official_phone' => '0242000000',
            'dpo_mobile' => '0770000000',
            'dpo_address' => '10 Nelson Mandela Avenue, Harare',
            'appointment_declaration' => 'Appointed by board resolution.',
            'appointed_at' => '2026-09-29',
            'status' => 'active',
        ]);

        $this->actingAs($user)->get(route('login'))
            ->assertRedirect('/compliance/dashboard');

        $this->actingAs($user)->get(route('compliance.dashboard'))
            ->assertOk();
    }

    public function test_guest_navigation_pages_are_reachable(): void
    {
        $this->get(route('login'))->assertOk();
        $this->get(route('register'))->assertOk();
    }
}
