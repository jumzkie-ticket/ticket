<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('security_level', function (Blueprint $table): void {
            $table->id();
            $table->string('level_no', 40)->unique();
            $table->string('description');
            $table->string('sla', 120);
            $table->timestamps();
        });

        Schema::table('tickets', function (Blueprint $table): void {
            $table->foreignId('security_level_id')->nullable()->after('status')->constrained('security_level')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('tickets', function (Blueprint $table): void {
            $table->dropConstrainedForeignId('security_level_id');
        });
        Schema::dropIfExists('security_level');
    }
};
