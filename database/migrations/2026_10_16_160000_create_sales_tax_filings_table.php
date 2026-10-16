<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// Phase 4.1: mahana sales tax return ka record (FBR / SRB) — CPR, jama shuda raqam, ikhtiyari GL entry.
return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('sales_tax_filings')) {
            Schema::create('sales_tax_filings', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('company_id');
                $table->char('period', 7);                       // YYYY-MM
                $table->string('authority', 10);                 // fbr | srb
                $table->decimal('output_tax', 18, 2)->default(0);
                $table->decimal('input_tax', 18, 2)->default(0);
                $table->decimal('adjustment', 18, 2)->default(0); // return pe haath se farq (+ / −)
                $table->decimal('paid_amount', 18, 2)->default(0);
                $table->string('payment_ref', 60)->nullable();   // CPR / PSID
                $table->date('filed_on');
                $table->string('notes', 255)->nullable();
                $table->unsignedBigInteger('journal_entry_id')->nullable();
                $table->unsignedBigInteger('created_by')->nullable();
                $table->timestamps();
                $table->unique(['company_id', 'period', 'authority']);
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('sales_tax_filings');
    }
};
