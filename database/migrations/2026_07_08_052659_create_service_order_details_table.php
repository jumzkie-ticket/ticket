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
        Schema::create('service_order_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('client_id')->constrained()->cascadeOnDelete();
            $table->foreignId('industry_business_type_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('product_detail_id')->nullable()->constrained('product_details')->nullOnDelete();
            $table->json('sap_product_ids')->nullable();
            $table->string('software_version')->nullable();
            $table->string('patch_or_fp')->nullable();
            $table->date('support_start_date')->nullable();
            $table->date('support_end_date')->nullable();
            $table->boolean('cas_accredited')->default(false);
            $table->string('support_inclusion')->nullable();
            $table->integer('man_days')->nullable();
            $table->integer('used_man_days')->nullable();
            $table->integer('remaining_man_days')->nullable();
            $table->string('license_type')->nullable();
            $table->json('license_counts')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('service_order_details');
    }
};
