<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('service_order', function (Blueprint $table) {
            if (DB::getDriverName() === 'pgsql') {
                $table->dropColumn('product_detail_id');
            } else {
                $table->dropConstrainedForeignId('product_detail_id');
            }
        });
    }

    public function down(): void
    {
        Schema::table('service_order', function (Blueprint $table) {
            $table->foreignId('product_detail_id')->nullable()->after('industry_business_type_id')->constrained('product_details')->nullOnDelete();
        });
    }
};
