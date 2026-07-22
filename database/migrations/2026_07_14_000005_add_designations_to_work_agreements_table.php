<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('work_agreements', function (Blueprint $table): void {
            $table->string('project_manager_designation')->nullable()->after('project_manager');
            $table->string('consultant_designation')->nullable()->after('consultant');
            $table->string('accepted_by_designation')->nullable()->after('accepted_by');
        });

        DB::table('work_agreements')
            ->select(['id', 'project_manager_assign_fc_id', 'consultant_assign_fc_id'])
            ->orderBy('id')
            ->each(function (object $agreement): void {
                DB::table('work_agreements')->where('id', $agreement->id)->update([
                    'project_manager_designation' => DB::table('assign_fc')
                        ->where('id', $agreement->project_manager_assign_fc_id)
                        ->value('designation'),
                    'consultant_designation' => DB::table('assign_fc')
                        ->where('id', $agreement->consultant_assign_fc_id)
                        ->value('designation'),
                ]);
            });
    }

    public function down(): void
    {
        Schema::table('work_agreements', function (Blueprint $table): void {
            $table->dropColumn([
                'project_manager_designation',
                'consultant_designation',
                'accepted_by_designation',
            ]);
        });
    }
};
