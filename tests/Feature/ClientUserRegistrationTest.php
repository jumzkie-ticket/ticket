<?php

namespace Tests\Feature;

use App\Models\Client;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class ClientUserRegistrationTest extends TestCase
{
    use RefreshDatabase;

    public function test_login_links_to_the_public_client_user_registration_form(): void
    {
        $this->seed();
        Client::factory()->create(['company_name' => 'Acme Customer Corp.']);

        $this->get(route('login'))
            ->assertOk()
            ->assertSee('Register New User')
            ->assertSee(route('register'));

        $this->get(route('register'))
            ->assertOk()
            ->assertSee('Register New User')
            ->assertSee('Company Name')
            ->assertSee('First Name')
            ->assertSee('Last Name')
            ->assertSee('Email Address')
            ->assertSee('Role')
            ->assertSee('Customer')
            ->assertSee('Password Validation')
            ->assertSee('name="company_name"', false);
    }

    public function test_public_registration_creates_a_customer_with_a_client_relationship(): void
    {
        $this->seed();
        $client = Client::factory()->create(['company_name' => 'Northwind Customer Corp.']);
        $adminRole = Role::where('slug', 'admin')->firstOrFail();
        $customerRole = Role::where('slug', 'customer')->firstOrFail();

        $this->post(route('register.store'), [
            'company_name' => 'nOrThWiNd',
            'first_name' => 'Maria',
            'last_name' => 'Santos',
            'email' => 'maria.santos@example.test',
            'role_id' => $adminRole->id,
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ])->assertRedirect(route('login'))
            ->assertSessionHas('status', 'Registration successful. You can now sign in.');

        $user = User::where('email', 'maria.santos@example.test')->firstOrFail();

        $this->assertGuest();
        $this->assertTrue($user->client->is($client));
        $this->assertTrue($user->roles->contains($customerRole));
        $this->assertFalse($user->roles->contains($adminRole));
        $this->assertTrue(Schema::hasTable('client_users'));
        $this->assertFalse(Schema::hasColumn('users', 'clients_id'));
        $this->assertDatabaseHas('client_users', [
            'user_id' => $user->id,
            'client_id' => $client->id,
        ]);
        $this->assertFalse(Schema::hasColumn('client_users', 'company_name'));
        $this->assertFalse(Schema::hasColumn('client_users', 'first_name'));
        $this->assertFalse(Schema::hasColumn('client_users', 'last_name'));
        $this->assertFalse(Schema::hasColumn('client_users', 'email_address'));
        $this->assertFalse(Schema::hasColumn('client_users', 'role'));
        $this->assertFalse(Schema::hasColumn('client_users', 'password'));
        $this->assertFalse(Schema::hasColumn('client_users', 'password_confirmation'));
    }

    public function test_public_registration_rejects_an_ambiguous_partial_company_name(): void
    {
        $this->seed();
        Client::factory()->create(['company_name' => 'Northwind East Corp.']);
        Client::factory()->create(['company_name' => 'Northwind West Corp.']);

        $this->from(route('register'))
            ->post(route('register.store'), [
                'company_name' => 'Northwind',
                'first_name' => 'Maria',
                'last_name' => 'Santos',
                'email' => 'ambiguous@example.test',
                'password' => 'password123',
                'password_confirmation' => 'password123',
            ])
            ->assertRedirect(route('register'))
            ->assertSessionHasErrors('company_name');

        $this->assertDatabaseMissing('users', [
            'email' => 'ambiguous@example.test',
        ]);
    }
}
