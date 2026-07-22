<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('account_manager', function (Blueprint $table) {
            $table->id();
            $table->string('account_manager')->unique();
            $table->timestamps();
        });

        Schema::table('clients', function (Blueprint $table) {
            $table->foreignId('account_manager_id')
                ->nullable()
                ->after('company_size')
                ->constrained('account_manager')
                ->nullOnDelete();
        });

        if (Schema::hasColumn('clients', 'account_manager')) {
            DB::table('clients')
                ->whereNotNull('account_manager')
                ->where('account_manager', '<>', '')
                ->select('account_manager')
                ->distinct()
                ->orderBy('account_manager')
                ->get()
                ->each(function (object $row): void {
                    DB::table('account_manager')->updateOrInsert(
                        ['account_manager' => $row->account_manager],
                        [
                            'created_at' => now(),
                            'updated_at' => now(),
                        ],
                    );
                });

            DB::table('clients')
                ->whereNotNull('account_manager')
                ->where('account_manager', '<>', '')
                ->orderBy('id')
                ->get(['id', 'account_manager'])
                ->each(function (object $client): void {
                    $accountManagerId = DB::table('account_manager')
                        ->where('account_manager', $client->account_manager)
                        ->value('id');

                    DB::table('clients')
                        ->where('id', $client->id)
                        ->update(['account_manager_id' => $accountManagerId]);
                });

            Schema::table('clients', function (Blueprint $table) {
                $table->dropColumn('account_manager');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('clients', function (Blueprint $table) {
            $table->string('account_manager')->nullable()->after('company_size');
        });

        DB::table('clients')
            ->leftJoin('account_manager', 'clients.account_manager_id', '=', 'account_manager.id')
            ->whereNotNull('clients.account_manager_id')
            ->select('clients.id', 'account_manager.account_manager')
            ->orderBy('clients.id')
            ->get()
            ->each(function (object $client): void {
                DB::table('clients')
                    ->where('id', $client->id)
                    ->update(['account_manager' => $client->account_manager]);
            });

        Schema::table('clients', function (Blueprint $table) {
            $table->dropConstrainedForeignId('account_manager_id');
        });

        Schema::dropIfExists('account_manager');
    }
};
