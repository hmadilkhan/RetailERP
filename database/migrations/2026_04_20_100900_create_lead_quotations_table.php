<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('crm_lead_quotations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('lead_id')->constrained('crm_leads')->cascadeOnUpdate()->cascadeOnDelete();
            $table->string('quotation_no')->unique();
            $table->date('quotation_date');
            $table->date('valid_until')->nullable();
            $table->decimal('subtotal', 15, 2)->default(0);
            $table->decimal('discount', 15, 2)->default(0);
            $table->decimal('tax', 15, 2)->default(0);
            $table->decimal('total', 15, 2)->default(0);
            $table->string('status', 30)->default('draft')->index();
            $table->longText('notes')->nullable();
            $table->unsignedBigInteger('created_by')->nullable()->index();
            $table->timestamps();

            $table->index(['lead_id', 'status'], 'crm_lead_quotations_lead_status_idx');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('crm_lead_quotations');
    }
};
