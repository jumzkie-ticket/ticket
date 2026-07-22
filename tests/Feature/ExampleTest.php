<?php

namespace Tests\Feature;

use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExampleTest extends TestCase
{
    use RefreshDatabase;

    public function test_the_roles_page_returns_a_successful_response(): void
    {
        $this->seed();
        $this->signIn();

        $response = $this->get(route('roles.index'));

        $response->assertStatus(200);
        $response->assertSee('Roles & Permissions', false);
        $response->assertSee('Role Name');
        $response->assertSee('Analytics per Role');
        $response->assertSee('.content-area .header-title h1', false);
        $response->assertSee('color: var(--ink) !important', false);
        $response->assertSee('.content-area .header-title .eyebrow', false);
    }

    public function test_a_role_can_be_created_with_permissions(): void
    {
        $this->seed();
        $this->signIn();

        $analytics = Permission::where('slug', 'analytics')->firstOrFail();

        $response = $this->post(route('roles.store'), [
            'name' => 'Supervisor',
            'permissions' => [$analytics->id],
        ]);

        $role = Role::where('name', 'Supervisor')->firstOrFail();

        $response->assertRedirect(route('roles.index'));
        $this->assertDatabaseHas('roles', [
            'id' => $role->id,
            'name' => 'Supervisor',
            'slug' => 'supervisor',
        ]);
        $this->assertDatabaseHas('permission_role', [
            'role_id' => $role->id,
            'permission_id' => $analytics->id,
        ]);
    }

    public function test_a_role_can_be_updated_and_deleted(): void
    {
        $this->seed();
        $this->signIn();

        $role = Role::where('slug', 'customer')->firstOrFail();
        $reports = Permission::where('slug', 'reports')->firstOrFail();

        $this->put(route('roles.update', $role), [
            'name' => 'Customer Success',
            'permissions' => [$reports->id],
        ])->assertRedirect(route('roles.index'));

        $role->refresh();

        $this->assertSame('Customer Success', $role->name);
        $this->assertSame('customer-success', $role->slug);
        $this->assertDatabaseHas('permission_role', [
            'role_id' => $role->id,
            'permission_id' => $reports->id,
        ]);

        $this->delete(route('roles.destroy', $role))
            ->assertRedirect(route('roles.index'));

        $this->assertDatabaseMissing('roles', [
            'id' => $role->id,
        ]);
    }

    private function signIn(): User
    {
        $user = User::where('email', 'admin@xceler8.test')->firstOrFail();

        $this->actingAs($user);

        return $user;
    }
}
