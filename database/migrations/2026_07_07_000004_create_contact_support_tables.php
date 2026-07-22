<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('support_contact_infos', function (Blueprint $table) {
            $table->id();
            $table->string('support_email');
            $table->string('phone_number');
            $table->text('office_address');
            $table->string('office_hours');
            $table->timestamps();
        });

        Schema::create('contact_support_requests', function (Blueprint $table) {
            $table->id();
            $table->string('full_name');
            $table->string('email');
            $table->string('subject');
            $table->string('priority');
            $table->string('category');
            $table->text('message');
            $table->string('attachment_path')->nullable();
            $table->string('status')->default('new');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('contact_support_requests');
        Schema::dropIfExists('support_contact_infos');
    }
};
