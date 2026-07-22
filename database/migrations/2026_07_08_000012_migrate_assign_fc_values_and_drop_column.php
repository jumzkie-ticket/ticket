<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::transaction(function () {
            // Collect distinct legacy assign_fc values from clients
            $values = DB::table('clients')
                ->whereNotNull('assign_fc')
                ->distinct()
                ->pluck('assign_fc')
                ->filter()
                ->values()
                ->all();

            // Insert into assign_fc table and build mapping
            $mapping = [];
            foreach ($values as $val) {
                $existing = DB::table('assign_fc')->where('assign_fc', $val)->value('id');
                if ($existing) {
                    $mapping[$val] = $existing;
                    continue;
                }

                $id = DB::table('assign_fc')->insertGetId([
                    'assign_fc' => $val,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

                $mapping[$val] = $id;
            }

            // Update clients to reference new assign_fc ids
            foreach ($mapping as $val => $id) {
                DB::table('clients')->where('assign_fc', $val)->update(['assign_fc_id' => $id]);
            }

            // Finally drop the legacy column
            if (Schema::hasColumn('clients', 'assign_fc')) {
                Schema::table('clients', function (Blueprint $table) {
                    $table->dropColumn('assign_fc');
                });
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::transaction(function () {
            // Recreate legacy column
            if (!Schema::hasColumn('clients', 'assign_fc')) {
                Schema::table('clients', function (Blueprint $table) {
                    $table->string('assign_fc')->nullable()->after('account_manager_id');
                });
            }

            // Copy back values from assign_fc table
            $rows = DB::table('assign_fc')->select('id', 'assign_fc')->get();
            foreach ($rows as $row) {
                DB::table('clients')->where('assign_fc_id', $row->id)->update(['assign_fc' => $row->assign_fc]);
            }

            // Remove foreign key and column assign_fc_id
            if (Schema::hasColumn('clients', 'assign_fc_id')) {
                Schema::table('clients', function (Blueprint $table) {
                    $table->dropForeign(['assign_fc_id']);
                    $table->dropColumn('assign_fc_id');
                });
            }
        });
    }
};
