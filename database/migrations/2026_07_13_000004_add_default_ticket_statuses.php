<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $now = now();

        DB::table('ticket_status')->insertOrIgnore(collect([
            'hold',
            'no-helpdesk',
            'no-maintenance',
            'pending-from-client',
            'pending-from-dev',
            'pending-from-sales',
            'pending-from-xti',
        ])->map(fn (string $status): array => [
            'status' => $status,
            'created_at' => $now,
            'updated_at' => $now,
        ])->all());
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::table('ticket_status')->whereIn('status', [
            'hold',
            'no-helpdesk',
            'no-maintenance',
            'pending-from-client',
            'pending-from-dev',
            'pending-from-sales',
            'pending-from-xti',
        ])->whereNotIn('id', DB::table('tickets')->whereNotNull('ticket_status_id')->select('ticket_status_id'))->delete();
    }
};
