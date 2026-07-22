<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ticket_resolutions', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('ticket_id')->constrained()->cascadeOnDelete();
            $table->dateTime('resolution_date');
            $table->text('description');
            $table->text('recommendation')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->index(['ticket_id', 'resolution_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ticket_resolutions');
    }
};
