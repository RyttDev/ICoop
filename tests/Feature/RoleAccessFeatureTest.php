<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RoleAccessFeatureTest extends TestCase
{
    use RefreshDatabase;

    public function test_public_registration_is_not_available(): void
    {
        $this->get('/register')->assertNotFound();
    }

    public function test_admin_can_create_a_staff_account(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $this->actingAs($admin)->post(route('users.store'), [
            'name' => 'Staff Baharu',
            'email' => 'staff@example.test',
            'role' => 'staff',
            'password' => 'kata-laluan-ujian',
            'password_confirmation' => 'kata-laluan-ujian',
        ])->assertRedirect();
        $this->assertDatabaseHas('users', ['email' => 'staff@example.test', 'role' => 'staff']);
    }

    public function test_staff_cannot_open_admin_pages(): void
    {
        $this->actingAs(User::factory()->create(['role' => 'staff']))
            ->get(route('sales.index'))
            ->assertForbidden();
    }

    public function test_admin_can_open_admin_pages(): void
    {
        $this->actingAs(User::factory()->create(['role' => 'admin']))
            ->get(route('sales.index'))
            ->assertOk();
    }
}
