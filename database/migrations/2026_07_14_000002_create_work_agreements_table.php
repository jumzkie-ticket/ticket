<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('work_agreements', function (Blueprint $table): void {
            $table->id();
            $table->string('work_agreement_no')->nullable()->unique();
            $table->date('agreement_date');
            $table->foreignId('assign_fc_id')->constrained('assign_fc')->restrictOnDelete();
            $table->foreignId('client_id')->constrained()->restrictOnDelete();
            $table->text('address');
            $table->boolean('billable')->default(false);
            $table->boolean('non_billable')->default(false);
            $table->text('scope');
            $table->text('objective');
            $table->text('current_issue');
            $table->text('proposed_solutions');
            $table->text('note')->nullable();
            $table->decimal('estimated_man_days', 8, 2);
            $table->string('project_manager');
            $table->string('consultant');
            $table->string('accepted_by');
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index(['agreement_date', 'client_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('work_agreements');
    }
};
