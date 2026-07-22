<?php

namespace Tests\Feature;

use App\Models\AccountManager;
use App\Models\AssignFc;
use App\Models\Client;
use App\Models\IndustryBusinessType;
use App\Models\SapProduct;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class ClientLookupManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_lookup_pages_require_authentication(): void
    {
        $this->get(route('industry-business-types.index'))
            ->assertRedirect(route('login'));

        $this->get(route('sap-products.index'))
            ->assertRedirect(route('login'));

        $this->get(route('account-managers.index'))
            ->assertRedirect(route('login'));
    }

    public function test_authenticated_user_can_view_industry_business_type_ui(): void
    {
        $this->seed();
        $this->signIn();

        $this->get(route('industry-business-types.index'))
            ->assertStatus(200)
            ->assertSee('Industry / Business Type')
            ->assertSee('Total Industries')
            ->assertSee('In Use')
            ->assertSee('Unused')
            ->assertSee('Add Industry')
            ->assertSee('Industry List')
            ->assertSee('Action')
            ->assertSee('Manufacturing')
            ->assertSee('aria-label="View Manufacturing"', false)
            ->assertSee('aria-label="Edit Manufacturing"', false)
            ->assertSee('aria-label="Delete Manufacturing"', false)
            ->assertSee('Related Clients')
            ->assertSee('aria-current="page"', false);
    }

    public function test_authenticated_user_can_create_industry_business_type(): void
    {
        $this->seed();
        $this->signIn();

        $this->post(route('industry-business-types.store'), [
            'industry' => 'Logistics',
        ])->assertRedirect(route('industry-business-types.index'));

        $this->assertDatabaseHas('industry_business_types', [
            'industry' => 'Logistics',
        ]);
    }

    public function test_authenticated_user_can_view_edit_and_delete_industry_business_type(): void
    {
        $this->seed();
        $this->signIn();

        $industry = IndustryBusinessType::create(['industry' => 'Logistics']);

        $this->get(route('industry-business-types.index', ['view' => $industry->id]))
            ->assertStatus(200)
            ->assertSee('View Industry')
            ->assertSee('Logistics');

        $this->get(route('industry-business-types.index', ['edit' => $industry->id]))
            ->assertStatus(200)
            ->assertSee('Edit Industry')
            ->assertSee('Logistics');

        $this->put(route('industry-business-types.update', $industry), [
            'industry' => 'Logistics Services',
        ])->assertRedirect(route('industry-business-types.index', ['view' => $industry->id]));

        $this->assertDatabaseHas('industry_business_types', [
            'id' => $industry->id,
            'industry' => 'Logistics Services',
        ]);

        $this->delete(route('industry-business-types.destroy', $industry))
            ->assertRedirect(route('industry-business-types.index'));

        $this->assertDatabaseMissing('industry_business_types', [
            'id' => $industry->id,
        ]);
    }

    public function test_authenticated_user_can_view_sap_product_ui(): void
    {
        $this->seed();
        $this->signIn();

        $this->get(route('sap-products.index'))
            ->assertStatus(200)
            ->assertSee('Product Used')
            ->assertSee('Total Products')
            ->assertSee('In Use')
            ->assertSee('Unused')
            ->assertSee('Add Product')
            ->assertSee('Product List')
            ->assertSee('Action')
            ->assertSee('SAP Business One')
            ->assertSee('aria-label="View SAP Business One"', false)
            ->assertSee('aria-label="Edit SAP Business One"', false)
            ->assertSee('aria-label="Delete SAP Business One"', false)
            ->assertSee('Related Clients')
            ->assertSee('aria-current="page"', false);
    }

    public function test_authenticated_user_can_view_account_manager_ui(): void
    {
        $this->seed();
        $this->signIn();

        $this->get(route('account-managers.index'))
            ->assertStatus(200)
            ->assertSee('Account Manager')
            ->assertSee('Total Managers')
            ->assertSee('In Use')
            ->assertSee('Unused')
            ->assertSee('Add Account Manager')
            ->assertSee('Account Manager List')
            ->assertSee('Action')
            ->assertSee('Juan Dela Cruz')
            ->assertSee('aria-label="View Juan Dela Cruz"', false)
            ->assertSee('aria-label="Edit Juan Dela Cruz"', false)
            ->assertSee('aria-label="Delete Juan Dela Cruz"', false)
            ->assertSee('Related Clients')
            ->assertSee('aria-current="page"', false);
    }

    public function test_assign_fc_form_actions_are_aligned_and_theme_aware(): void
    {
        $this->seed();
        $this->signIn();

        $this->get(route('assign-fcs.index'))
            ->assertStatus(200)
            ->assertSee('Assign FC')
            ->assertSee('Designation')
            ->assertSee('class="lookup-form-actions"', false)
            ->assertSee('class="lookup-button"', false)
            ->assertSee('class="lookup-link-button"', false)
            ->assertSee('background: var(--blue)')
            ->assertSee('background: var(--panel)');

        $this->assertTrue(Schema::hasColumn('assign_fc', 'designation'));
    }

    public function test_authenticated_user_can_manage_assign_fc_with_designation(): void
    {
        $this->seed();
        $this->signIn();

        $this->post(route('assign-fcs.store'), [
            'assign_fc' => 'FC-100',
            'designation' => 'Senior Functional Consultant',
        ])->assertRedirect(route('assign-fcs.index'));

        $assignFc = AssignFc::query()->where('assign_fc', 'FC-100')->firstOrFail();
        $this->assertSame('Senior Functional Consultant', $assignFc->designation);

        $this->get(route('assign-fcs.index', ['edit' => $assignFc->id]))
            ->assertOk()
            ->assertSee('FC-100')
            ->assertSee('Senior Functional Consultant');

        $this->put(route('assign-fcs.update', $assignFc), [
            'assign_fc' => 'FC-100',
            'designation' => 'Lead Functional Consultant',
        ])->assertRedirect(route('assign-fcs.index', ['view' => $assignFc->id]));

        $this->assertDatabaseHas('assign_fc', [
            'id' => $assignFc->id,
            'assign_fc' => 'FC-100',
            'designation' => 'Lead Functional Consultant',
        ]);

        $this->delete(route('assign-fcs.destroy', $assignFc))
            ->assertRedirect(route('assign-fcs.index'));

        $this->assertDatabaseMissing('assign_fc', ['id' => $assignFc->id]);
    }

    public function test_authenticated_user_can_create_account_manager(): void
    {
        $this->seed();
        $this->signIn();

        $this->post(route('account-managers.store'), [
            'account_manager' => 'Liza Ramos',
        ])->assertRedirect(route('account-managers.index'));

        $this->assertDatabaseHas('account_manager', [
            'account_manager' => 'Liza Ramos',
        ]);
    }

    public function test_authenticated_user_can_view_edit_and_delete_account_manager(): void
    {
        $this->seed();
        $this->signIn();

        $accountManager = AccountManager::create(['account_manager' => 'Liza Ramos']);

        $this->get(route('account-managers.index', ['view' => $accountManager->id]))
            ->assertStatus(200)
            ->assertSee('View Account Manager')
            ->assertSee('Liza Ramos');

        $this->get(route('account-managers.index', ['edit' => $accountManager->id]))
            ->assertStatus(200)
            ->assertSee('Edit Account Manager')
            ->assertSee('Liza Ramos');

        $this->put(route('account-managers.update', $accountManager), [
            'account_manager' => 'Liza Ramos Updated',
        ])->assertRedirect(route('account-managers.index', ['view' => $accountManager->id]));

        $this->assertDatabaseHas('account_manager', [
            'id' => $accountManager->id,
            'account_manager' => 'Liza Ramos Updated',
        ]);

        $this->delete(route('account-managers.destroy', $accountManager))
            ->assertRedirect(route('account-managers.index'));

        $this->assertDatabaseMissing('account_manager', [
            'id' => $accountManager->id,
        ]);
    }

    public function test_account_manager_cannot_be_deleted_when_related_to_clients(): void
    {
        $this->seed();
        $this->signIn();

        $accountManager = AccountManager::where('account_manager', 'Juan Dela Cruz')->firstOrFail();
        $industry = IndustryBusinessType::where('industry', 'Distribution / Retail')->firstOrFail();
        $product = SapProduct::where('sap_product', 'SAP Business One')->firstOrFail();

        $client = Client::create([
            'company_name' => 'Northwind Distribution Inc.',
            'contact_person' => 'Maria Santos',
            'designation' => 'IT Manager',
            'email_address' => 'maria.santos@northwind.test',
            'contact_country_code' => '+63',
            'contact_number' => '917-123-4567',
            'region_code' => '010000000',
            'region_name' => 'Region 1 - Ilocos Region',
            'province_code' => '012800000',
            'province_name' => 'Ilocos Norte',
            'city_municipality_code' => '012805000',
            'city_municipality_name' => 'City of Batac',
            'barangay_code' => '012805001',
            'barangay_name' => 'Aglipay (Pob.)',
            'building_details' => 'Unit 8, Solar Century Tower',
            'industry_business_type_id' => $industry->id,
            'version_number' => '10.00.130',
            'patch_or_fp' => 'FP 2008',
            'company_size' => '26-50',
            'account_manager_id' => $accountManager->id,
            'preferred_support_method' => 'support-portal',
            'accepted_terms' => true,
            'accepted_at' => now(),
            'status' => 'active',
        ]);
        $client->sapProducts()->sync([$product->id]);

        $this->delete(route('account-managers.destroy', $accountManager))
            ->assertRedirect(route('account-managers.index', ['view' => $accountManager->id]))
            ->assertSessionHasErrors('account_manager');

        $this->assertDatabaseHas('account_manager', [
            'id' => $accountManager->id,
        ]);
    }

    public function test_authenticated_user_can_create_sap_product(): void
    {
        $this->seed();
        $this->signIn();

        $this->post(route('sap-products.store'), [
            'sap_product' => 'SAP Field Service Management',
        ])->assertRedirect(route('sap-products.index'));

        $this->assertDatabaseHas('products', [
            'sap_product' => 'SAP Field Service Management',
        ]);
    }

    public function test_authenticated_user_can_view_edit_and_delete_product_used(): void
    {
        $this->seed();
        $this->signIn();

        $product = SapProduct::create(['sap_product' => 'SAP Field Service Management']);

        $this->get(route('sap-products.index', ['view' => $product->id]))
            ->assertStatus(200)
            ->assertSee('View Product')
            ->assertSee('SAP Field Service Management');

        $this->get(route('sap-products.index', ['edit' => $product->id]))
            ->assertStatus(200)
            ->assertSee('Edit Product')
            ->assertSee('SAP Field Service Management');

        $this->put(route('sap-products.update', $product), [
            'sap_product' => 'SAP Field Service Management Cloud',
        ])->assertRedirect(route('sap-products.index', ['view' => $product->id]));

        $this->assertDatabaseHas('products', [
            'id' => $product->id,
            'sap_product' => 'SAP Field Service Management Cloud',
        ]);

        $this->delete(route('sap-products.destroy', $product))
            ->assertRedirect(route('sap-products.index'));

        $this->assertDatabaseMissing('products', [
            'id' => $product->id,
        ]);
    }

    private function signIn(): User
    {
        $user = User::where('email', 'admin@xceler8.test')->firstOrFail();

        $this->actingAs($user);

        return $user;
    }
}
