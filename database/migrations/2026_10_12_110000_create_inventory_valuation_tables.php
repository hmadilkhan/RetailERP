<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// Phase 1.4: moving weighted-average cost (company + product). ValuationService source documents se har run me
// posting_start_date se dobara hisaab lagati hai — POS ki total_cost / lot balances pe bharosa nahi.
//   product_valuations     : har product ki abhi ki haalat (qty, average cost, value) — Inventory Valuation screen
//   stock_valuation_daily  : branch + din + product ka sale COGS — SalesPoster yahin se COGS leta hai
return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('product_valuations')) {
            Schema::create('product_valuations', function (Blueprint $table) {
                $table->id();
                $table->integer('company_id');
                $table->integer('product_id');
                $table->decimal('qty', 18, 4)->default(0);
                $table->decimal('avg_cost', 18, 4)->default(0);
                $table->decimal('value', 18, 2)->default(0);
                $table->string('cost_source', 20)->nullable(); // opening / purchase / adjustment / price_list / none
                $table->date('last_in_date')->nullable();
                $table->unsignedInteger('unvalued_sales')->default(0); // cost na hone ki wajah se 0 pe biki lines
                $table->timestamp('computed_at')->nullable();

                $table->unique(['company_id', 'product_id']);
            });
        }

        if (!Schema::hasTable('stock_valuation_daily')) {
            Schema::create('stock_valuation_daily', function (Blueprint $table) {
                $table->id();
                $table->integer('company_id');
                $table->integer('branch_id');
                $table->date('date');
                $table->integer('product_id');
                $table->decimal('qty', 18, 4)->default(0);   // bechi (return minus)
                $table->decimal('cogs', 18, 2)->default(0);
                $table->unsignedInteger('unvalued_lines')->default(0);

                $table->index(['company_id', 'date', 'branch_id']);
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('stock_valuation_daily');
        Schema::dropIfExists('product_valuations');
    }
};
