<?php

namespace Tests\Feature;

use App\Models\Package;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class PackageManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_package_page_requires_authentication(): void
    {
        $this->get(route('packages.index'))
            ->assertRedirect(route('login'));
    }

    public function test_packages_table_contains_package_field(): void
    {
        $this->assertTrue(Schema::hasTable('packages'));
        $this->assertTrue(Schema::hasColumn('packages', 'package'));
    }

    public function test_authenticated_user_can_view_package_ui(): void
    {
        $this->seed();
        $this->signIn();

        Package::create(['package' => 'Standard Support']);

        $this->get(route('packages.index'))
            ->assertStatus(200)
            ->assertSee('Package')
            ->assertSee('Total Packages')
            ->assertSee('Add Package')
            ->assertSee('Package List')
            ->assertSee('Standard Support')
            ->assertSee('aria-label="View Standard Support"', false)
            ->assertSee('aria-label="Edit Standard Support"', false)
            ->assertSee('aria-label="Delete Standard Support"', false)
            ->assertSee('href="'.route('packages.index').'"', false)
            ->assertSee('aria-current="page"', false);
    }

    public function test_authenticated_user_can_create_package(): void
    {
        $this->seed();
        $this->signIn();

        $this->post(route('packages.store'), [
            'package' => 'Premium Support',
        ])->assertRedirect(route('packages.index'))
            ->assertSessionHas('status', 'Package added successfully.');

        $this->assertDatabaseHas('packages', [
            'package' => 'Premium Support',
        ]);
    }

    public function test_package_name_must_be_unique(): void
    {
        $this->seed();
        $this->signIn();

        Package::create(['package' => 'Premium Support']);

        $this->post(route('packages.store'), [
            'package' => 'Premium Support',
        ])->assertSessionHasErrors('package');

        $this->assertDatabaseCount('packages', 1);
    }

    public function test_authenticated_user_can_view_edit_and_delete_package(): void
    {
        $this->seed();
        $this->signIn();

        $package = Package::create(['package' => 'Standard Support']);

        $this->get(route('packages.index', ['view' => $package->id]))
            ->assertStatus(200)
            ->assertSee('View Package')
            ->assertSee('Standard Support');

        $this->get(route('packages.index', ['edit' => $package->id]))
            ->assertStatus(200)
            ->assertSee('Edit Package')
            ->assertSee('Standard Support');

        $this->put(route('packages.update', $package), [
            'package' => 'Standard Support Plus',
        ])->assertRedirect(route('packages.index', ['view' => $package->id]))
            ->assertSessionHas('status', 'Package updated successfully.');

        $this->assertDatabaseHas('packages', [
            'id' => $package->id,
            'package' => 'Standard Support Plus',
        ]);

        $this->delete(route('packages.destroy', $package))
            ->assertRedirect(route('packages.index'))
            ->assertSessionHas('status', 'Package deleted successfully.');

        $this->assertDatabaseMissing('packages', [
            'id' => $package->id,
        ]);
    }

    private function signIn(): User
    {
        $user = User::where('email', 'admin@xceler8.test')->firstOrFail();

        $this->actingAs($user);

        return $user;
    }
}
