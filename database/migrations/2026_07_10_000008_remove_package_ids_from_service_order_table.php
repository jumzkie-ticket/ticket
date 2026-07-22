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
            ->whereNotNull('package_ids')
            ->select(['id', 'package_ids'])
            ->orderBy('id')
            ->each(function (object $order): void {
                $packageIds = is_array($order->package_ids)
                    ? $order->package_ids
                    : json_decode((string) $order->package_ids, true);

                foreach ((array) $packageIds as $packageId) {
                    DB::table('service_order_package')->insertOrIgnore([
                        'service_order_id' => $order->id,
                        'package_id' => (int) $packageId,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
            });

        Schema::table('service_order', function (Blueprint $table) {
            $table->dropColumn('package_ids');
        });
    }

    public function down(): void
    {
        Schema::table('service_order', function (Blueprint $table) {
            $table->json('package_ids')->nullable()->after('product_detail_id');
        });
    }
};
