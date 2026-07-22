<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('service_order')
            ->whereNotNull('sap_product_ids')
            ->select(['id', 'sap_product_ids'])
            ->orderBy('id')
            ->each(function (object $order): void {
                $productIds = is_array($order->sap_product_ids)
                    ? $order->sap_product_ids
                    : json_decode((string) $order->sap_product_ids, true);

                foreach ((array) $productIds as $productId) {
                    DB::table('service_order_sap_product')->insertOrIgnore([
                        'service_order_id' => $order->id,
                        'sap_product_id' => (int) $productId,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
            });

        Schema::table('service_order', function (Blueprint $table) {
            $table->dropColumn('sap_product_ids');
        });
    }

    public function down(): void
    {
        Schema::table('service_order', function (Blueprint $table) {
            $table->json('sap_product_ids')->nullable()->after('product_detail_id');
        });
    }
};
