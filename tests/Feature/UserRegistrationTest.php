<?php

namespace Tests\Feature;

use App\Models\Client;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class UserRegistrationTest extends TestCase
{
    use RefreshDatabase;

    public function test_the_user_registration_page_is_available_after_login(): void
    {
        $this->seed();
        $this->signIn();

        $response = $this->get(route('users.index', ['create' => 1]));

        $response->assertStatus(200);
        $response->assertSee('User Registration');
        $response->assertSee('Registered Users');
        $response->assertSee('New User');
        $response->assertSee('Register New User');
        $response->assertSee('Company Name');
        $response->assertSee('Dashboard');
        $response->assertSee('Support Schedule');
        $response->assertSee('Sign Out');
    }

    public function test_a_user_can_be_registered_with_a_role(): void
    {
        $this->seed();
        $this->signIn();

        $role = Role::where('slug', 'customer')->firstOrFail();
        $client = Client::factory()->create(['company_name' => 'Acme Support Inc.']);

        $response = $this->post(route('users.store'), [
            'clients_id' => $client->id,
            'first_name' => 'Maria',
            'last_name' => 'Client',
            'role_id' => $role->id,
            'email' => 'maria.client@example.test',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $user = User::where('email', 'maria.client@example.test')->firstOrFail();

        $response->assertRedirect(route('users.index'));
        $this->assertFalse(Schema::hasColumn('users', 'name'));
        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'first_name' => 'Maria',
            'last_name' => 'Client',
            'email' => 'maria.client@example.test',
        ]);
        $this->assertDatabaseHas('role_user', [
            'user_id' => $user->id,
            'role_id' => $role->id,
        ]);
        $this->assertDatabaseHas('client_users', [
            'user_id' => $user->id,
            'client_id' => $client->id,
        ]);
    }

    public function test_a_user_can_be_viewed_updated_and_deleted(): void
    {
        $this->seed();
        $this->signIn();

        $admin = Role::where('slug', 'admin')->firstOrFail();
        $consultant = Role::where('slug', 'consultant')->firstOrFail();
        $originalClient = Client::factory()->create(['company_name' => 'Original Company']);
        $updatedClient = Client::factory()->create(['company_name' => 'Updated Company']);
        $user = User::factory()->create([
            'first_name' => 'View',
            'last_name' => 'Target',
            'email' => 'view.target@example.test',
        ]);
        $user->clientUser()->create(['client_id' => $originalClient->id]);
        $user->roles()->sync([$admin->id]);

        $this->get(route('users.index', ['view' => $user->id]))
            ->assertStatus(200)
            ->assertSee('User Details')
            ->assertSee('Original Company')
            ->assertSee('View')
            ->assertSee('Target');

        $this->put(route('users.update', $user), [
            'clients_id' => $updatedClient->id,
            'first_name' => 'Edited',
            'last_name' => 'Target',
            'role_id' => $consultant->id,
            'email' => 'edited.target@example.test',
        ])->assertRedirect(route('users.index', ['view' => $user->id]));

        $user->refresh();

        $this->assertTrue($user->client->is($updatedClient));
        $this->assertDatabaseHas('client_users', [
            'user_id' => $user->id,
            'client_id' => $updatedClient->id,
        ]);
        $this->assertSame('Edited', $user->first_name);
        $this->assertSame('edited.target@example.test', $user->email);
        $this->assertDatabaseHas('role_user', [
            'user_id' => $user->id,
            'role_id' => $consultant->id,
        ]);

        $this->delete(route('users.destroy', $user))
            ->assertRedirect(route('users.index'));

        $this->assertDatabaseMissing('users', [
            'id' => $user->id,
        ]);
    }

    private function signIn(): User
    {
        $user = User::where('email', 'admin@xceler8.test')->firstOrFail();

        $this->actingAs($user);

        return $user;
    }
}
