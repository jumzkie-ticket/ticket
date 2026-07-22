<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('service_order', function (Blueprint $table) {
            $table->dropColumn('remaining_man_days');
        });
    }

    public function down(): void
    {
        Schema::table('service_order', function (Blueprint $table) {
            $table->integer('remaining_man_days')->nullable()->after('used_man_days');
        });
    }
};
