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
        Schema::create('system_settings', function (Blueprint $table) {
            $table->id();
            $table->string('company_name')->default('Xceler8 Technologies Inc.');
            $table->string('system_name')->default('XTI Ticket Support System');
            $table->string('time_zone')->default('America/New_York');
            $table->string('date_format')->default('F j, Y');
            $table->boolean('email_notifications')->default(true);
            $table->boolean('ticket_alerts')->default(true);
            $table->boolean('system_announcements')->default(true);
            $table->boolean('weekly_reports')->default(false);
            $table->string('theme')->default('light');
            $table->string('primary_color', 20)->default('#2563EB');
            $table->string('logo_path')->nullable();
            $table->boolean('auto_backup')->default(true);
            $table->string('backup_frequency')->default('daily');
            $table->boolean('maintenance_mode')->default(false);
            $table->timestamps();
        });

        if (Schema::getConnection()->getDriverName() === 'pgsql') {
            DB::statement('ALTER TABLE system_settings ENABLE ROW LEVEL SECURITY');
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('system_settings');
    }
};
