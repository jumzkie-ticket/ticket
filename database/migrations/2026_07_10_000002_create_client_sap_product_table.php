<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('client_sap_product', function (Blueprint $table) {
            $table->id();
            $table->foreignId('client_id')->constrained()->cascadeOnDelete();
            $table->foreignId('sap_product_id')->constrained('products')->cascadeOnDelete();
            $table->timestamps();
            $table->unique(['client_id', 'sap_product_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('client_sap_product');
    }
};
