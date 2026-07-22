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
        Schema::table('service_order', function (Blueprint $table) {
            if (! Schema::hasColumn('service_order', 'package_ids')) {
                $table->json('package_ids')->nullable()->after('product_detail_id');
            }
        });

        if (! Schema::hasTable('service_order_package')) {
            Schema::create('service_order_package', function (Blueprint $table) {
                $table->id();
                $table->foreignId('service_order_id')->constrained('service_order')->cascadeOnDelete();
                $table->foreignId('package_id')->constrained('packages')->cascadeOnDelete();
                $table->timestamps();

                $table->unique(['service_order_id', 'package_id']);
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('service_order_package');

        Schema::table('service_order', function (Blueprint $table) {
            if (Schema::hasColumn('service_order', 'package_ids')) {
                $table->dropColumn('package_ids');
            }
        });
    }
};
