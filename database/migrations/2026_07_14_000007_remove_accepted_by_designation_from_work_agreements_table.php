<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('work_agreements', function (Blueprint $table): void {
            $table->dropColumn('accepted_by_designation');
        });
    }

    public function down(): void
    {
        Schema::table('work_agreements', function (Blueprint $table): void {
            $table->string('accepted_by_designation')->nullable()->after('accepted_by');
        });
    }
};
