<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ticket_status', function (Blueprint $table): void {
            $table->id();
            $table->string('status', 100)->unique();
            $table->timestamps();
        });

        $now = now();
        DB::table('ticket_status')->insert(collect([
            17 => 'New',
            2 => 'open',
            3 => 'in-progress',
            4 => 'resolved',
            5 => 'closed',
        ])->map(fn (string $status, int $id): array => [
            'id' => $id,
            'status' => $status,
            'created_at' => $now,
            'updated_at' => $now,
        ])->values()->all());

        Schema::table('tickets', function (Blueprint $table): void {
            $table->foreignId('ticket_status_id')->nullable()->after('status')->constrained('ticket_status')->nullOnDelete();
        });

        DB::table('tickets')->orderBy('id')->eachById(function (object $ticket): void {
            DB::table('tickets')->where('id', $ticket->id)->update([
                'ticket_status_id' => DB::table('ticket_status')->whereRaw('LOWER(status) = ?', [strtolower($ticket->status)])->value('id'),
            ]);
        });
    }

    public function down(): void
    {
        Schema::table('tickets', function (Blueprint $table): void {
            $table->dropConstrainedForeignId('ticket_status_id');
        });
        Schema::dropIfExists('ticket_status');
    }
};
