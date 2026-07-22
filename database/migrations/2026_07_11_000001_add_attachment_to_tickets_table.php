<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tickets', function (Blueprint $table): void {
            $table->string('attachment_path')->nullable()->after('other_information');
            $table->string('attachment_original_name')->nullable()->after('attachment_path');
        });
    }

    public function down(): void
    {
        Schema::table('tickets', function (Blueprint $table): void {
            $table->dropColumn(['attachment_path', 'attachment_original_name']);
        });
    }
};
