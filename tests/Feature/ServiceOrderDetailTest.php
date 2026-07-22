<?php

namespace Tests\Feature;

use App\Models\Client;
use App\Models\IndustryBusinessType;
use App\Models\Package;
use App\Models\SapProduct;
use App\Models\ServiceOrderDetail;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ServiceOrderDetailTest extends TestCase
{
    use RefreshDatabase;

    public function test_service_order_detail_page_requires_authentication(): void
    {
        $this->get(route('service-order-details.index'))
            ->assertRedirect(route('login'));
    }

    public function test_service_order_detail_view_uses_service_order_table_data(): void
    {
        $this->seed();
        $this->signIn();

        $industry = IndustryBusinessType::firstOrCreate(['industry' => 'Retail']);
        $client = Client::factory()->create([
            'company_name' => 'Acme Solutions',
            'industry_business_type_id' => $industry->id,
            'version_number' => '10.0',
            'patch_or_fp' => 'FP01',
        ]);
        $product = SapProduct::firstOrCreate(['sap_product' => 'SAP Business One']);

        $serviceOrder = ServiceOrderDetail::create([
            'client_id' => $client->id,
            'industry_business_type_id' => $industry->id,
            'software_version' => '10.0',
            'patch_or_fp' => 'FP01',
            'support_start_date' => '2026-07-01',
            'support_end_date' => '2026-12-31',
            'cas_accredited' => true,
            'support_inclusion' => 'Support Included',
            'man_days' => 6,
            'unused_man_days' => 2,
            'used_man_days' => 2,
            'professional' => 2,
            'limited' => 1,
            'indirect' => 0,
            'starter' => 1,
            'mssql' => 4,
            'notes' => 'Priority customer.',
        ]);
        $serviceOrder->sapProducts()->sync([$product->id]);

        $this->get(route('service-order-details.detail', ['service_order_id' => $serviceOrder->id]))
            ->assertStatus(200)
            ->assertSee('Service Order List')
            ->assertSee('Filters')
            ->assertSee('Service Order List')
            ->assertSee('Service Order Overview')
            ->assertDontSee('Man-days Overview')
            ->assertDontSee('Top Products Used')
            ->assertDontSee('Support Inclusion')
            ->assertSeeInOrder(['aria-label="Service order analytics"', 'Service Order Overview', 'Filters', 'Service Order List'], false)
            ->assertSee('SO-2026-'.str_pad((string) $serviceOrder->id, 4, '0', STR_PAD_LEFT))
            ->assertSee('Acme Solutions')
            ->assertSee('Retail')
            ->assertSee('SAP Business One')
            ->assertSee('8.00')
            ->assertDontSee('Support Included')
            ->assertSee('Remaining')
            ->assertSee('aria-label="View SO-2026-', false)
            ->assertSee('aria-label="Edit SO-2026-', false)
            ->assertSee(route('service-order-details.show', $serviceOrder), false)
            ->assertSee(route('service-order-details.edit', $serviceOrder), false)
            ->assertDontSee('More actions for');

        $this->get(route('service-order-details.show', $serviceOrder))
            ->assertStatus(200)
            ->assertSee('View Service Order')
            ->assertSee('SO-2026-'.str_pad((string) $serviceOrder->id, 4, '0', STR_PAD_LEFT))
            ->assertSee('Acme Solutions')
            ->assertSee('SAP Business One')
            ->assertSee('Client Information')
            ->assertSee('Product & Service', false)
            ->assertSee('Man-days')
            ->assertSee('License')
            ->assertSee('Priority customer.')
            ->assertSee(route('service-order-details.edit', $serviceOrder), false);
    }

    public function test_authenticated_user_can_view_service_order_detail_form(): void
    {
        $this->seed();
        $this->signIn();

        Client::factory()->create([
            'company_name' => 'Acme Solutions',
            'version_number' => '10.0',
            'patch_or_fp' => 'FP01',
        ]);
        IndustryBusinessType::firstOrCreate(['industry' => 'Retail']);
        SapProduct::firstOrCreate(['sap_product' => 'SAP Business One']);
        Package::create(['package' => 'Standard Support']);
        Package::create(['package' => 'Premium Support']);

        $this->get(route('service-order-details.index'))
            ->assertStatus(200)
            ->assertSee('Service Order')
            ->assertSee('Client Name')
            ->assertSee('Industry / Business Type')
            ->assertSee('Product Used')
            ->assertDontSee('Select Products')
            ->assertSee('<textarea id="product_used_display"', false)
            ->assertSee("productUsedDisplay.value = (client.products || []).map(product => product.name).join('\\n')", false)
            ->assertSee('support_start_date')
            ->assertSee('support_end_date')
            ->assertSee('sap_product_ids')
            ->assertSee('Software Version')
            ->assertSee('Patch or FP')
            ->assertSee('Product')
            ->assertSee('Service')
            ->assertSee('Support Start Date')
            ->assertSee('Support End Date')
            ->assertSee('CAS Accredited')
            ->assertSee('Support Inclusion')
            ->assertSee('Package')
            ->assertSee('Standard Support')
            ->assertSee('Premium Support')
            ->assertSee('Select Package')
            ->assertSee('id="packageModal"', false)
            ->assertSee('<textarea id="package_selection_display"', false)
            ->assertSee("selectedNames.join('\\n')", false)
            ->assertSee('name="package_ids[]"', false)
            ->assertDontSee('Hold Ctrl or Command')
            ->assertDontSee('product_detail_id')
            ->assertSee('Man-days')
            ->assertSee('Used Man-days')
            ->assertSee('Un-Used Man-days (Prev.Yrs)')
            ->assertSee('Remaining Man-days')
            ->assertSeeInOrder(['Man-days (Entitled)', 'Un-Used Man-days (Prev.Yrs)', 'Used Man-days', 'Remaining Man-days'])
            ->assertSeeInOrder(['Client Information', 'Product & Service', 'id="manDaysTitle"', 'Notes', 'Summary'], false)
            ->assertSee('Summary is a live readout of the Man-days fields above.')
            ->assertSee('remainingDisplay.value = remaining.toFixed(2)', false)
            ->assertSee('.content-area .man-days-col-header', false)
            ->assertSee('background: var(--panel-muted) !important', false)
            ->assertSee('.summary-section .summary-value', false)
            ->assertSee('color: var(--green) !important', false)
            ->assertSee('License');
    }

    public function test_service_order_package_relation_table_exists(): void
    {
        $this->assertTrue(Schema::hasTable('service_order_package'));
        $this->assertTrue(Schema::hasColumns('service_order_package', [
            'id',
            'service_order_id',
            'package_id',
            'created_at',
            'updated_at',
        ]));
    }

    public function test_service_order_success_message_uses_themed_notification(): void
    {
        $this->seed();
        $this->signIn();

        $this->withSession(['status' => 'Service order saved successfully.'])
            ->get(route('service-order-details.index'))
            ->assertStatus(200)
            ->assertSee('class="app-status-prompt"', false)
            ->assertSee('role="status"', false)
            ->assertSee('Service order saved successfully.')
            ->assertSee('Dismiss notification');
    }

    public function test_authenticated_user_can_create_service_order_detail(): void
    {
        Storage::fake('local');
        $this->seed();
        $this->signIn();

        $client = Client::factory()->create([
            'company_name' => 'Acme Solutions',
            'version_number' => '10.0',
            'patch_or_fp' => 'FP01',
        ]);
        $industry = IndustryBusinessType::firstOrCreate(['industry' => 'Retail']);
        $product = SapProduct::firstOrCreate(['sap_product' => 'SAP Business One']);
        $secondProduct = SapProduct::firstOrCreate(['sap_product' => 'SAP S/4HANA']);
        $client->sapProducts()->sync([$product->id, $secondProduct->id]);
        $package = Package::create(['package' => 'Standard Helpdesk Support Plan']);
        $secondPackage = Package::create(['package' => 'Premium Support']);
        ServiceOrderDetail::create([
            'client_id' => $client->id,
            'industry_business_type_id' => $industry->id,
            'support_end_date' => '2025-12-31',
            'man_days' => 2,
            'used_man_days' => 0,
        ]);

        $this->post(route('service-order-details.store'), [
            'client_id' => $client->id,
            'industry_business_type_id' => $industry->id,
            'package_ids' => [$package->id, $secondPackage->id],
            'sap_product_ids' => [$product->id, $secondProduct->id],
            'software_version' => '10.0',
            'patch_or_fp' => 'FP01',
            'product_and_service' => 'Standard Support',
            'support_start_date' => '2026-07-01',
            'support_end_date' => '2026-12-31',
            'cas_accredited' => '1',
            'support_inclusion' => 'Support Included',
            'man_days' => '15',
            'unused_man_days' => '2',
            'used_man_days' => '3',
            'license_type' => 'Professional',
            'attach_service_order' => UploadedFile::fake()->create('service-order.pdf', 100, 'application/pdf'),
        ])->assertRedirect(route('service-order-details.index'))
            ->assertSessionHas('status', 'Service order saved successfully.');

        $this->assertDatabaseHas('service_order', [
            'client_id' => $client->id,
            'license_type' => 'Professional',
            'support_inclusion' => 'Support Included',
            'man_days' => 6,
            'unused_man_days' => 2,
            'attach_service_order_original_name' => 'service-order.pdf',
        ]);

        $savedDetail = ServiceOrderDetail::query()->where('client_id', $client->id)->latest('id')->first();
        Storage::disk('local')->assertExists($savedDetail->attach_service_order);
        $this->get(route('service-order-details.show', $savedDetail))
            ->assertOk()
            ->assertSee('Attach Service Order')
            ->assertSee('service-order.pdf');
        $this->get(route('service-order-details.attachment', $savedDetail))
            ->assertOk()
            ->assertHeader('content-disposition', 'inline; filename="service-order.pdf"');
        $this->assertSame(5, $savedDetail->remaining_man_days);
        $this->assertSame([$package->id, $secondPackage->id], $savedDetail->packages()->pluck('packages.id')->sort()->values()->all());
        $this->assertSame([$product->id, $secondProduct->id], $savedDetail->sapProducts()->pluck('products.id')->sort()->values()->all());
        $this->assertSame([$package->id, $secondPackage->id], $savedDetail->packages()->pluck('packages.id')->sort()->values()->all());
        $this->assertTrue($package->serviceOrders()->whereKey($savedDetail->id)->exists());
        $this->assertTrue($secondPackage->serviceOrders()->whereKey($savedDetail->id)->exists());
    }

    public function test_authenticated_user_can_edit_and_update_service_order_detail(): void
    {
        $this->seed();
        $this->signIn();

        $industry = IndustryBusinessType::firstOrCreate(['industry' => 'Retail']);
        $client = Client::factory()->create([
            'company_name' => 'Acme Solutions',
            'industry_business_type_id' => $industry->id,
            'version_number' => '10.0',
            'patch_or_fp' => 'FP01',
        ]);
        $firstProduct = SapProduct::firstOrCreate(['sap_product' => 'SAP Business One']);
        $secondProduct = SapProduct::firstOrCreate(['sap_product' => 'SAP S/4HANA']);
        $client->sapProducts()->sync([$secondProduct->id]);
        $firstPackage = Package::create(['package' => 'Standard Support']);
        $secondPackage = Package::create(['package' => 'Premium Support']);
        ServiceOrderDetail::create([
            'client_id' => $client->id,
            'industry_business_type_id' => $industry->id,
            'support_end_date' => '2025-12-31',
            'man_days' => 4,
            'used_man_days' => 0,
        ]);

        $serviceOrder = ServiceOrderDetail::create([
            'client_id' => $client->id,
            'industry_business_type_id' => $industry->id,
            'software_version' => '10.0',
            'patch_or_fp' => 'FP01',
            'support_start_date' => '2026-07-01',
            'support_end_date' => '2027-07-01',
            'cas_accredited' => true,
            'support_inclusion' => 'Standard coverage',
            'man_days' => 10,
            'unused_man_days' => 3,
            'used_man_days' => 2,
            'professional' => 1,
            'limited' => 1,
            'indirect' => 0,
            'starter' => 0,
            'mssql' => 2,
            'notes' => 'Original note',
        ]);
        $serviceOrder->packages()->sync([$firstPackage->id]);
        $serviceOrder->sapProducts()->sync([$firstProduct->id]);

        $this->get(route('service-order-details.edit', $serviceOrder))
            ->assertStatus(200)
            ->assertSee('Edit Service Order')
            ->assertSee('value="PUT"', false)
            ->assertSee('Standard coverage')
            ->assertSee('Original note')
            ->assertSee('id="unused_man_days" name="unused_man_days" min="0" step="1" value="3" readonly', false)
            ->assertSee('Update Service Order');

        $this->put(route('service-order-details.update', $serviceOrder), [
            'client_id' => $client->id,
            'industry_business_type_id' => $industry->id,
            'package_ids' => [$secondPackage->id],
            'sap_product_ids' => [$secondProduct->id],
            'software_version' => '10.0',
            'patch_or_fp' => 'FP02',
            'support_start_date' => '2026-08-01',
            'support_end_date' => '2027-08-01',
            'cas_accredited' => '0',
            'support_inclusion' => 'Premium coverage',
            'man_days' => '20',
            'unused_man_days' => '4',
            'used_man_days' => '5',
            'professional' => '2',
            'limited' => '1',
            'indirect' => '1',
            'starter' => '1',
            'mssql' => '999',
            'notes' => 'Updated note',
        ])->assertRedirect(route('service-order-details.detail', ['service_order_id' => $serviceOrder->id]))
            ->assertSessionHas('status', 'Service order updated successfully.');

        $this->get(route('service-order-details.detail', ['service_order_id' => $serviceOrder->id]))
            ->assertStatus(200)
            ->assertSee('class="app-status-prompt"', false)
            ->assertSee('role="status"', false)
            ->assertSee('Service order updated successfully.')
            ->assertSee('Dismiss notification');

        $serviceOrder->refresh();

        $this->assertSame('FP02', $serviceOrder->patch_or_fp);
        $this->assertSame('Premium coverage', $serviceOrder->support_inclusion);
        $this->assertSame(4, $serviceOrder->unused_man_days);
        $this->assertSame(19, $serviceOrder->remaining_man_days);
        $this->assertSame(4, $serviceOrder->mssql);
        $this->assertFalse($serviceOrder->cas_accredited);
        $this->assertSame([$secondPackage->id], $serviceOrder->packages()->pluck('packages.id')->all());
        $this->assertSame([$secondProduct->id], $serviceOrder->sapProducts()->pluck('products.id')->all());
    }

    private function signIn(): User
    {
        $user = User::where('email', 'admin@xceler8.test')->firstOrFail();

        $this->actingAs($user);

        return $user;
    }
}
