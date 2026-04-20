<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('crm_lead_followups', function (Blueprint $table) {
            $table->id();
            $table->foreignId('lead_id')->constrained('crm_leads')->cascadeOnUpdate()->cascadeOnDelete();
            $table->date('followup_date');
            $table->string('followup_type', 50);
            $table->text('remarks');
            $table->date('next_followup_date')->nullable();
            $table->string('followup_result', 150)->nullable();
            $table->unsignedBigInteger('created_by')->nullable()->index();
            $table->timestamps();

            $table->index(['lead_id', 'followup_date'], 'crm_lf_lead_date_idx');
            $table->index('next_followup_date', 'crm_lf_next_followup_idx');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('crm_lead_followups');
    }
};
