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
        Schema::create('clients', function (Blueprint $table) {
            $table->id();
            $table->string('company_name');
            $table->string('contact_person');
            $table->string('email_address');
            $table->string('contact_country_code', 10)->default('+63');
            $table->string('contact_number', 40);
            $table->string('region_code', 20);
            $table->string('region_name');
            $table->string('province_code', 20)->nullable();
            $table->string('province_name')->nullable();
            $table->string('city_municipality_code', 20);
            $table->string('city_municipality_name');
            $table->string('barangay_code', 20);
            $table->string('barangay_name');
            $table->string('building_details', 500);
            $table->string('industry_type');
            $table->string('sap_product_used');
            $table->string('software_version_patch', 120);
            $table->string('company_size');
            $table->string('preferred_support_method');
            $table->text('additional_notes')->nullable();
            $table->boolean('accepted_terms')->default(false);
            $table->timestamp('accepted_at')->nullable();
            $table->string('status')->default('active');
            $table->foreignId('registered_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index('email_address');
            $table->index(['region_code', 'province_code']);
            $table->index(['industry_type', 'status']);
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('clients');
    }
};
