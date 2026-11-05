<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_guests_cannot_access_user_management(): void
    {
        $response = $this->get('/users');
        $response->assertRedirect('/login');
    }

    public function test_warehouse_users_cannot_access_user_management(): void
    {
        $warehouseUser = User::factory()->create(['role' => 'warehouse']);

        $response = $this->actingAs($warehouseUser)->get('/users');
        $response->assertForbidden();

        $createResponse = $this->actingAs($warehouseUser)->post('/users', [
            'name' => 'Otro',
            'email' => 'otro@example.com',
            'role' => 'warehouse',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);
        $createResponse->assertForbidden();
    }

    public function test_admin_can_view_user_list(): void
    {
        $admin = User::factory()->admin()->create();

        $response = $this->actingAs($admin)->get('/users');
        $response->assertOk();
        $response->assertSeeText($admin->email);
    }

    public function test_admin_can_create_a_new_user(): void
    {
        $admin = User::factory()->admin()->create();

        $response = $this->actingAs($admin)->post('/users', [
            'name' => 'Carlos Almacén',
            'email' => 'carlos@almacen.com',
            'role' => 'warehouse',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertRedirect(route('users.index'));
        $this->assertDatabaseHas('users', [
            'email' => 'carlos@almacen.com',
            'role' => 'warehouse',
            'is_active' => true,
        ]);
    }

    public function test_admin_cannot_deactivate_themselves(): void
    {
        $admin = User::factory()->admin()->create();

        $response = $this->actingAs($admin)->patch("/users/{$admin->id}/toggle");

        $response->assertSessionHasErrors('error');
        $this->assertTrue($admin->fresh()->is_active);
    }

    public function test_admin_cannot_deactivate_the_last_active_admin(): void
    {
        $admin = User::factory()->admin()->create();
        $otherAdmin = User::factory()->admin()->create();

        // There are 2 admins now. Let's make otherAdmin the logged in one and try to deactivate admin
        // But if we have only 1 admin, trying to deactivate him should fail
        $singleAdmin = User::factory()->admin()->create();
        // Remove the others to simulate single active admin
        User::where('id', '!=', $singleAdmin->id)->delete();

        // Create a temporary admin just to perform the action
        $actor = User::factory()->admin()->create();

        // Now actor deactivates $singleAdmin - this should succeed because actor is also admin
        $this->actingAs($actor)->patch("/users/{$singleAdmin->id}/toggle");
        $this->assertFalse($singleAdmin->fresh()->is_active);

        // But now $actor is the ONLY active admin. Trying to deactivate $actor fails
        $response = $this->actingAs($actor)->patch("/users/{$actor->id}/toggle");
        $response->assertSessionHasErrors('error');
        $this->assertTrue($actor->fresh()->is_active);
    }

    public function test_admin_cannot_demote_the_last_active_admin(): void
    {
        $actor = User::factory()->admin()->create();
        $targetAdmin = User::factory()->admin()->create();

        // Actor demotes targetAdmin to warehouse -> ok because actor is still active admin
        $this->actingAs($actor)->patch("/users/{$targetAdmin->id}/role", [
            'role' => 'warehouse',
        ]);
        $this->assertEquals('warehouse', $targetAdmin->fresh()->role);

        // Now actor is the ONLY admin. Actor tries to demote another admin (which doesn't exist)
        // If someone sends a request to demote actor, the controller blocks self-demote:
        $response = $this->actingAs($actor)->patch("/users/{$actor->id}/role", [
            'role' => 'warehouse',
        ]);
        $response->assertSessionHasErrors('error');
        $this->assertEquals('admin', $actor->fresh()->role);
    }
}
