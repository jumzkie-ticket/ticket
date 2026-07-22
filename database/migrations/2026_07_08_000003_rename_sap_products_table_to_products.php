<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (Schema::hasTable('sap_products') && ! Schema::hasTable('products')) {
            Schema::rename('sap_products', 'products');
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('products') && ! Schema::hasTable('sap_products')) {
            Schema::rename('products', 'sap_products');
        }
    }
};
