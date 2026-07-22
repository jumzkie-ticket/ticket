<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('ticket_resolutions', function (Blueprint $table): void {
            $table->renameColumn('resolution_date', 'date');
            $table->dropForeign(['created_by']);
            $table->dropColumn(['recommendation', 'created_by']);
        });
    }

    public function down(): void
    {
        Schema::table('ticket_resolutions', function (Blueprint $table): void {
            $table->renameColumn('date', 'resolution_date');
            $table->text('recommendation')->nullable()->after('description');
            $table->foreignId('created_by')->nullable()->after('recommendation')->constrained('users')->nullOnDelete();
        });
    }
};
