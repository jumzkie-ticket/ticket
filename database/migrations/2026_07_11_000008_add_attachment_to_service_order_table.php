<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('service_order', function (Blueprint $table): void {
            $table->string('attach_service_order')->nullable()->after('notes');
            $table->string('attach_service_order_original_name')->nullable()->after('attach_service_order');
        });
    }

    public function down(): void
    {
        Schema::table('service_order', function (Blueprint $table): void {
            $table->dropColumn(['attach_service_order', 'attach_service_order_original_name']);
        });
    }
};
