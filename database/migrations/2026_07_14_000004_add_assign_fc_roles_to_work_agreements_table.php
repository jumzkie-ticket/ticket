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
            $table->foreignId('project_manager_assign_fc_id')
                ->nullable()
                ->after('estimated_man_days')
                ->constrained('assign_fc')
                ->restrictOnDelete();
            $table->foreignId('consultant_assign_fc_id')
                ->nullable()
                ->after('project_manager')
                ->constrained('assign_fc')
                ->restrictOnDelete();
        });

        $assignFcIds = DB::table('assign_fc')->pluck('id', 'assign_fc');
        DB::table('work_agreements')
            ->select(['id', 'project_manager', 'consultant'])
            ->orderBy('id')
            ->each(function (object $agreement) use ($assignFcIds): void {
                DB::table('work_agreements')->where('id', $agreement->id)->update([
                    'project_manager_assign_fc_id' => $assignFcIds[$agreement->project_manager] ?? null,
                    'consultant_assign_fc_id' => $assignFcIds[$agreement->consultant] ?? null,
                ]);
            });
    }

    public function down(): void
    {
        Schema::table('work_agreements', function (Blueprint $table): void {
            $table->dropConstrainedForeignId('project_manager_assign_fc_id');
            $table->dropConstrainedForeignId('consultant_assign_fc_id');
        });
    }
};
