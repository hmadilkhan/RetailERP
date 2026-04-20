<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('crm_lead_attachments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('lead_id')->constrained('crm_leads')->cascadeOnUpdate()->cascadeOnDelete();
            $table->string('file_name');
            $table->string('file_original_name');
            $table->string('file_path');
            $table->string('file_type', 120);
            $table->string('file_extension', 20);
            $table->unsignedBigInteger('file_size');
            $table->unsignedBigInteger('uploaded_by')->nullable()->index();
            $table->timestamps();

            $table->index(['lead_id', 'created_at'], 'crm_latt_lead_created_idx');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('crm_lead_attachments');
    }
};
