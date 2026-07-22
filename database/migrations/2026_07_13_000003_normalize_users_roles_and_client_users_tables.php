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
        $now = now();

        DB::table('users')
            ->join('clients', 'clients.id', '=', 'users.clients_id')
            ->leftJoin('role_user', 'role_user.user_id', '=', 'users.id')
            ->leftJoin('roles', 'roles.id', '=', 'role_user.role_id')
            ->whereNotNull('clients_id')
            ->select([
                'users.id',
                'users.clients_id',
                'users.first_name',
                'users.last_name',
                'users.email',
                'users.password',
                'clients.company_name',
                'roles.name as role_name',
            ])
            ->orderBy('users.id')
            ->each(function (object $user) use ($now): void {
                DB::table('client_users')->updateOrInsert(
                    ['user_id' => $user->id],
                    [
                        'client_id' => $user->clients_id,
                        'company_name' => $user->company_name,
                        'first_name' => $user->first_name,
                        'last_name' => $user->last_name,
                        'email_address' => $user->email,
                        'role' => $user->role_name ?? 'Customer',
                        'password' => $user->password,
                        'updated_at' => $now,
                        'created_at' => $now,
                    ],
                );
            });

        Schema::table('client_users', function (Blueprint $table) {
            $table->dropIndex(['company_name', 'role']);
            $table->dropUnique(['email_address']);
            $table->dropColumn([
                'company_name',
                'first_name',
                'last_name',
                'email_address',
                'role',
                'password',
            ]);
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropConstrainedForeignId('clients_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->foreignId('clients_id')
                ->nullable()
                ->after('id')
                ->constrained('clients')
                ->nullOnDelete();
        });

        Schema::table('client_users', function (Blueprint $table) {
            $table->string('company_name')->nullable()->after('client_id');
            $table->string('first_name', 80)->nullable()->after('company_name');
            $table->string('last_name', 80)->nullable()->after('first_name');
            $table->string('email_address')->nullable()->after('last_name');
            $table->string('role')->default('Customer')->after('email_address');
            $table->string('password')->nullable()->after('role');
            $table->unique('email_address');
            $table->index(['company_name', 'role']);
        });

        DB::table('client_users')
            ->join('users', 'users.id', '=', 'client_users.user_id')
            ->join('clients', 'clients.id', '=', 'client_users.client_id')
            ->select([
                'client_users.id',
                'client_users.user_id',
                'client_users.client_id',
                'users.first_name',
                'users.last_name',
                'users.email',
                'users.password',
                'clients.company_name',
            ])
            ->orderBy('client_users.id')
            ->each(function (object $clientUser): void {
                DB::table('client_users')
                    ->where('id', $clientUser->id)
                    ->update([
                        'company_name' => $clientUser->company_name,
                        'first_name' => $clientUser->first_name,
                        'last_name' => $clientUser->last_name,
                        'email_address' => $clientUser->email,
                        'role' => 'Customer',
                        'password' => $clientUser->password,
                    ]);

                DB::table('users')
                    ->where('id', $clientUser->user_id)
                    ->update(['clients_id' => $clientUser->client_id]);
            });
    }
};
