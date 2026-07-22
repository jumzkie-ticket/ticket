<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('service_order', function (Blueprint $table) {
            $table->index('support_start_date', 'service_order_support_start_idx');
            $table->index('support_end_date', 'service_order_support_end_idx');
            $table->index('software_version', 'service_order_version_idx');
            $table->index('patch_or_fp', 'service_order_patch_idx');
            $table->index(['client_id', 'support_end_date'], 'service_order_client_end_idx');
        });

        Schema::table('service_order_sap_product', function (Blueprint $table) {
            $table->index('sap_product_id', 'service_order_product_lookup_idx');
        });

        Schema::table('service_order_package', function (Blueprint $table) {
            $table->index('package_id', 'service_order_package_lookup_idx');
        });

        Schema::table('client_sap_product', function (Blueprint $table) {
            $table->index('sap_product_id', 'client_product_lookup_idx');
        });
    }

    public function down(): void
    {
        Schema::table('service_order', function (Blueprint $table) {
            $table->dropIndex('service_order_support_start_idx');
            $table->dropIndex('service_order_support_end_idx');
            $table->dropIndex('service_order_version_idx');
            $table->dropIndex('service_order_patch_idx');
            $table->dropIndex('service_order_client_end_idx');
        });

        Schema::table('service_order_sap_product', fn (Blueprint $table) => $table->dropIndex('service_order_product_lookup_idx'));
        Schema::table('service_order_package', fn (Blueprint $table) => $table->dropIndex('service_order_package_lookup_idx'));
        Schema::table('client_sap_product', fn (Blueprint $table) => $table->dropIndex('client_product_lookup_idx'));
    }
};
