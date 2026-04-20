<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('crm_lead_activities', function (Blueprint $table) {
            $table->id();
            $table->foreignId('lead_id')->constrained('crm_leads')->cascadeOnUpdate()->cascadeOnDelete();
            $table->string('activity_type', 60);
            $table->text('message');
            $table->json('old_value')->nullable();
            $table->json('new_value')->nullable();
            $table->unsignedBigInteger('created_by')->nullable()->index();
            $table->timestamps();

            $table->index(['lead_id', 'created_at'], 'crm_la_lead_created_idx');
            $table->index('activity_type', 'crm_la_type_idx');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('crm_lead_activities');
    }
};
