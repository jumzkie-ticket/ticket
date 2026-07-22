<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $now = now();

        DB::table('clients')
            ->whereNotNull('sap_product_id')
            ->select(['id', 'sap_product_id'])
            ->orderBy('id')
            ->each(function (object $client) use ($now): void {
                DB::table('client_sap_product')->insertOrIgnore([
                    'client_id' => $client->id,
                    'sap_product_id' => $client->sap_product_id,
                    'created_at' => $now,
                    'updated_at' => $now,
                ]);
            });
    }

    public function down(): void
    {
        // Existing primary-product relationships are intentionally preserved.
    }
};
