<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Rename main table
        if (Schema::hasTable('service_order_details') && ! Schema::hasTable('service_order')) {
            Schema::rename('service_order_details', 'service_order');
        }

        // If old pivot exists, create new pivot, copy data, then drop old pivot
        if (Schema::hasTable('service_order_detail_sap_product') && ! Schema::hasTable('service_order_sap_product')) {
            $productsTable = Schema::hasTable('sap_products') ? 'sap_products' : (Schema::hasTable('products') ? 'products' : null);

            Schema::create('service_order_sap_product', function (Blueprint $table) use ($productsTable) {
                $table->id();
                $table->foreignId('service_order_id')->constrained('service_order')->cascadeOnDelete();
                if ($productsTable) {
                    $table->foreignId('sap_product_id')->constrained($productsTable)->cascadeOnDelete();
                } else {
                    $table->unsignedBigInteger('sap_product_id');
                }
                $table->timestamps();
                $table->unique(['service_order_id', 'sap_product_id']);
            });

            // Copy rows from old pivot to new pivot
            $rows = DB::table('service_order_detail_sap_product')->get();

            foreach ($rows as $row) {
                DB::table('service_order_sap_product')->insert([
                    'service_order_id' => $row->service_order_detail_id,
                    'sap_product_id' => $row->sap_product_id,
                    'created_at' => $row->created_at,
                    'updated_at' => $row->updated_at,
                ]);
            }

            Schema::dropIfExists('service_order_detail_sap_product');
        }
    }

    public function down(): void
    {
        // Recreate old pivot and copy data back
        if (Schema::hasTable('service_order_sap_product') && ! Schema::hasTable('service_order_detail_sap_product')) {
            $productsTable = Schema::hasTable('sap_products') ? 'sap_products' : (Schema::hasTable('products') ? 'products' : null);

            Schema::create('service_order_detail_sap_product', function (Blueprint $table) use ($productsTable) {
                $table->id();
                $table->foreignId('service_order_detail_id')->constrained('service_order_details')->cascadeOnDelete();
                if ($productsTable) {
                    $table->foreignId('sap_product_id')->constrained($productsTable)->cascadeOnDelete();
                } else {
                    $table->unsignedBigInteger('sap_product_id');
                }
                $table->timestamps();
                $table->unique(['service_order_detail_id', 'sap_product_id']);
            });

            $rows = DB::table('service_order_sap_product')->get();

            foreach ($rows as $row) {
                DB::table('service_order_detail_sap_product')->insert([
                    'service_order_detail_id' => $row->service_order_id,
                    'sap_product_id' => $row->sap_product_id,
                    'created_at' => $row->created_at,
                    'updated_at' => $row->updated_at,
                ]);
            }

            Schema::dropIfExists('service_order_sap_product');
        }

        // Rename main table back
        if (Schema::hasTable('service_order') && ! Schema::hasTable('service_order_details')) {
            Schema::rename('service_order', 'service_order_details');
        }
    }
};
