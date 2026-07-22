<?php

namespace Tests\Feature;

use App\Models\ProductDetail;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class ProductDetailsTest extends TestCase
{
    use RefreshDatabase;

    public function test_product_details_requires_authentication(): void
    {
        $this->get(route('product-details'))
            ->assertRedirect(route('login'));
    }

    public function test_product_details_table_contains_helpdesk_package_fields(): void
    {
        $this->assertTrue(Schema::hasTable('product_details'));

        foreach ([
            'helpdesk_support_packages',
            'man_days',
            'helpdesk_coverage_months',
            'helpdesk_support_fee',
            'total_amount_vat_inc',
        ] as $column) {
            $this->assertTrue(Schema::hasColumn('product_details', $column), "Missing product_details.{$column}");
        }
    }

    public function test_authenticated_user_can_view_empty_product_details_ui(): void
    {
        $this->seed();
        $this->signIn();

        $this->get(route('product-details'))
            ->assertStatus(200)
            ->assertSee('Product Details')
            ->assertSee('Product Analytics')
            ->assertSee('Total Amount VAT Inc per Product')
            ->assertSee('No product analytics yet')
            ->assertSee('Total Amount VAT Inc')
            ->assertSee('Helpdesk Support Packages')
            ->assertSee('Man-days')
            ->assertSee('Helpdesk Coverage in months')
            ->assertSee('Helpdesk Support Fee')
            ->assertSee('PHP 0.00')
            ->assertSee('Only numbers are allowed.')
            ->assertSee('No product details yet')
            ->assertSee('href="'.route('product-details').'"', false)
            ->assertSee('aria-current="page"', false);
    }

    public function test_authenticated_user_can_view_product_bar_graph_analytics(): void
    {
        $this->seed();
        $this->signIn();

        ProductDetail::create($this->validPayload());
        ProductDetail::create([
            'helpdesk_support_packages' => 'Premium Helpdesk Support',
            'man_days' => 10,
            'helpdesk_coverage_months' => 24,
            'helpdesk_support_fee' => 48000.00,
            'total_amount_vat_inc' => 53760.00,
        ]);

        $this->get(route('product-details'))
            ->assertStatus(200)
            ->assertSee('Product Analytics')
            ->assertSee('Total Amount VAT Inc per Product')
            ->assertSee('Standard Helpdesk Support')
            ->assertSee('Premium Helpdesk Support')
            ->assertSee('5 man-days')
            ->assertSee('24 months')
            ->assertSee('PHP 28,000.00')
            ->assertSee('Fee PHP 48,000.00')
            ->assertSee('style="--bar-width: 100%;"', false)
            ->assertSee('style="--bar-width: 52.08%;"', false);
    }

    public function test_authenticated_user_can_create_product_detail(): void
    {
        $this->seed();
        $this->signIn();

        $payload = $this->validPayload();
        $payload['helpdesk_support_fee'] = 'PHP 25,000.00';
        $payload['total_amount_vat_inc'] = 'PHP 28,000.00';

        $this->post(route('product-details.store'), $payload)
            ->assertRedirect(route('product-details'))
            ->assertSessionHas('status', 'Product detail added successfully.');

        $this->assertDatabaseHas('product_details', [
            'helpdesk_support_packages' => 'Standard Helpdesk Support',
            'man_days' => 5,
            'helpdesk_coverage_months' => 12,
            'helpdesk_support_fee' => 25000.00,
            'total_amount_vat_inc' => 28000.00,
        ]);
    }

    public function test_helpdesk_coverage_months_must_be_numbers_only(): void
    {
        $this->seed();
        $this->signIn();

        $payload = $this->validPayload();
        $payload['helpdesk_coverage_months'] = '12 months';

        $this->post(route('product-details.store'), $payload)
            ->assertSessionHasErrors('helpdesk_coverage_months');

        $this->assertDatabaseCount('product_details', 0);
    }

    public function test_man_days_must_be_numbers_only(): void
    {
        $this->seed();
        $this->signIn();

        $payload = $this->validPayload();
        $payload['man_days'] = '5 days';

        $this->post(route('product-details.store'), $payload)
            ->assertSessionHasErrors([
                'man_days' => 'Only numbers are allowed.',
            ]);

        $this->assertDatabaseCount('product_details', 0);
    }

    public function test_currency_fields_must_be_numbers_only(): void
    {
        $this->seed();
        $this->signIn();

        $payload = $this->validPayload();
        $payload['helpdesk_support_fee'] = 'PHP 25,000 abc';
        $payload['total_amount_vat_inc'] = 'PHP 28,000 xyz';

        $this->post(route('product-details.store'), $payload)
            ->assertSessionHasErrors([
                'helpdesk_support_fee' => 'Only numbers are allowed.',
                'total_amount_vat_inc' => 'Only numbers are allowed.',
            ]);

        $this->assertDatabaseCount('product_details', 0);
    }

    public function test_authenticated_user_can_view_edit_and_delete_product_detail(): void
    {
        $this->seed();
        $this->signIn();

        $productDetail = ProductDetail::create($this->validPayload());

        $this->get(route('product-details', ['view' => $productDetail->id]))
            ->assertStatus(200)
            ->assertSee('View Product Detail')
            ->assertSee('Standard Helpdesk Support')
            ->assertSee('PHP 25,000.00')
            ->assertSee('PHP 28,000.00');

        $this->get(route('product-details', ['edit' => $productDetail->id]))
            ->assertStatus(200)
            ->assertSee('Edit Product Detail')
            ->assertSee('Standard Helpdesk Support');

        $this->put(route('product-details.update', $productDetail), [
            'helpdesk_support_packages' => 'Premium Helpdesk Support',
            'man_days' => '10',
            'helpdesk_coverage_months' => '24',
            'helpdesk_support_fee' => 'PHP 48,000.00',
            'total_amount_vat_inc' => 'PHP 53,760.00',
        ])->assertRedirect(route('product-details', ['view' => $productDetail->id]));

        $this->assertDatabaseHas('product_details', [
            'id' => $productDetail->id,
            'helpdesk_support_packages' => 'Premium Helpdesk Support',
            'man_days' => 10,
            'helpdesk_coverage_months' => 24,
            'helpdesk_support_fee' => 48000.00,
            'total_amount_vat_inc' => 53760.00,
        ]);

        $this->delete(route('product-details.destroy', $productDetail))
            ->assertRedirect(route('product-details'));

        $this->assertDatabaseMissing('product_details', [
            'id' => $productDetail->id,
        ]);
    }

    /**
     * @return array<string, mixed>
     */
    private function validPayload(): array
    {
        return [
            'helpdesk_support_packages' => 'Standard Helpdesk Support',
            'man_days' => '5',
            'helpdesk_coverage_months' => '12',
            'helpdesk_support_fee' => '25000.00',
            'total_amount_vat_inc' => '28000.00',
        ];
    }

    private function signIn(): User
    {
        $user = User::where('email', 'admin@xceler8.test')->firstOrFail();

        $this->actingAs($user);

        return $user;
    }
}
