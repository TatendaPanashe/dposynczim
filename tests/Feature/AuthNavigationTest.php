<?php

namespace Tests\Feature;

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
            ->assertRedirect('/compliance/dashboard');

        $this->actingAs($user)->get(route('register'))
            ->assertRedirect('/compliance/dashboard');
    }

    public function test_guest_navigation_pages_are_reachable(): void
    {
        $this->get(route('login'))->assertOk();
        $this->get(route('register'))->assertOk();
    }
}
