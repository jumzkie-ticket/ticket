<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('work_agreements', function (Blueprint $table): void {
            $table->dropColumn([
                'project_manager_designation',
                'consultant_designation',
            ]);
        });
    }

    public function down(): void
    {
        Schema::table('work_agreements', function (Blueprint $table): void {
            $table->string('project_manager_designation')->nullable()->after('project_manager');
            $table->string('consultant_designation')->nullable()->after('consultant');
        });
    }
};
