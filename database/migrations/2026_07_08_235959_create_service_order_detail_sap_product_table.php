<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('service_order_detail_sap_product', function (Blueprint $table) {
            $table->id();
            $table->foreignId('service_order_detail_id')->constrained('service_order_details')->cascadeOnDelete();
            $table->foreignId('sap_product_id')->constrained('products')->cascadeOnDelete();
            $table->unique(['service_order_detail_id', 'sap_product_id'], 'sod_sap_unique');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('service_order_detail_sap_product');
    }
};
