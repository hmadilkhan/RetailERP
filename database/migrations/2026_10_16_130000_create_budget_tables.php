<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// Phase 3.2: budget (fiscal year, ikhtiyari branch / cost center) — income / expense account × mahina.
return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('budgets')) {
            Schema::create('budgets', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('company_id')->index();
                $table->unsignedBigInteger('fiscal_year_id');
                $table->string('name', 150);
                $table->unsignedBigInteger('branch_id')->nullable();
                $table->unsignedBigInteger('cost_center_id')->nullable();
                $table->unsignedBigInteger('created_by')->nullable();
                $table->timestamps();
            });
        }
        if (!Schema::hasTable('budget_lines')) {
            Schema::create('budget_lines', function (Blueprint $table) {
                $table->id();
                $table->foreignId('budget_id')->constrained('budgets')->cascadeOnDelete();
                $table->unsignedBigInteger('account_id');
                $table->char('period', 7);                      // YYYY-MM
                $table->decimal('amount', 18, 2)->default(0);   // income: aamdani, expense: kharcha (dono positive)
                $table->timestamps();
                $table->unique(['budget_id', 'account_id', 'period']);
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('budget_lines');
        Schema::dropIfExists('budgets');
    }
};
