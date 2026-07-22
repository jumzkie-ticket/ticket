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
        Schema::create('product_details', function (Blueprint $table) {
            $table->id();
            $table->string('helpdesk_support_packages');
            $table->unsignedInteger('helpdesk_coverage_months');
            $table->decimal('helpdesk_support_fee', 12, 2);
            $table->decimal('total_amount_vat_inc', 12, 2);
            $table->timestamps();

            $table->index('helpdesk_support_packages');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_details');
    }
};
