<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('assign_fc', function (Blueprint $table): void {
            $table->string('designation')->nullable()->after('assign_fc');
        });
    }

    public function down(): void
    {
        Schema::table('assign_fc', function (Blueprint $table): void {
            $table->dropColumn('designation');
        });
    }
};
