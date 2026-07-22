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
        Schema::create('client_users', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->unique()->constrained()->cascadeOnDelete();
            $table->foreignId('client_id')->constrained()->cascadeOnDelete();
            $table->string('company_name');
            $table->string('first_name', 80);
            $table->string('last_name', 80);
            $table->string('email_address')->unique();
            $table->string('role')->default('Customer');
            $table->string('password');
            $table->timestamps();

            $table->index(['company_name', 'role']);
        });

        $now = now();

        DB::table('users')
            ->join('clients', 'clients.id', '=', 'users.clients_id')
            ->join('role_user', 'role_user.user_id', '=', 'users.id')
            ->join('roles', 'roles.id', '=', 'role_user.role_id')
            ->where('roles.slug', 'customer')
            ->select([
                'users.id',
                'users.clients_id',
                'users.first_name',
                'users.last_name',
                'users.email',
                'users.password',
                'clients.company_name',
            ])
            ->orderBy('users.id')
            ->each(function (object $user) use ($now): void {
                DB::table('client_users')->insertOrIgnore([
                    'user_id' => $user->id,
                    'client_id' => $user->clients_id,
                    'company_name' => $user->company_name,
                    'first_name' => $user->first_name,
                    'last_name' => $user->last_name,
                    'email_address' => $user->email,
                    'role' => 'Customer',
                    'password' => $user->password,
                    'created_at' => $now,
                    'updated_at' => $now,
                ]);
            });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('client_users');
    }
};
