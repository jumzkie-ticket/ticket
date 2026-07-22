<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tickets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('client_id')->constrained()->restrictOnDelete();
            $table->string('company_name');
            $table->string('contact_person');
            $table->string('email_address');
            $table->string('contact_number', 80);
            $table->string('product_related')->nullable();
            $table->string('software_version', 40)->nullable();
            $table->string('patch_or_fp', 120)->nullable();
            $table->string('database_version', 120)->nullable();
            $table->string('issue_encountered');
            $table->text('scenario');
            $table->text('expected_result');
            $table->string('full_name');
            $table->string('contact_email');
            $table->string('contact_phone', 20);
            $table->text('other_information')->nullable();
            $table->string('status')->default('new');
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->index(['status', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tickets');
    }
};
