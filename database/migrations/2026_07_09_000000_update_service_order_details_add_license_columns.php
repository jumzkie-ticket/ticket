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
        Schema::table('service_order_details', function (Blueprint $table) {
            if (Schema::hasColumn('service_order_details', 'license_counts')) {
                $table->dropColumn('license_counts');
            }

            $table->integer('professional')->default(0)->nullable();
            $table->integer('limited')->default(0)->nullable();
            $table->integer('indirect')->default(0)->nullable();
            $table->integer('starter')->default(0)->nullable();
            $table->integer('mssql')->default(0)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('service_order_details', function (Blueprint $table) {
            if (Schema::hasColumn('service_order_details', 'professional')) {
                $table->dropColumn(['professional','limited','indirect','starter','mssql']);
            }

            $table->json('license_counts')->nullable();
        });
    }
};
