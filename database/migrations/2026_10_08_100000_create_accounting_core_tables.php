<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// Phase 1.1: Chart of Accounts + fiscal years + periods. Module company-wise on hota hai (accounting_settings),
// enable pe default retail COA + current fiscal year seed hota hai (AccountingSetupService).
return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('accounting_settings')) {
            Schema::create('accounting_settings', function (Blueprint $table) {
                $table->id();
                $table->integer('company_id')->unique();
                $table->boolean('enabled')->default(false);
                $table->unsignedTinyInteger('fiscal_year_start_month')->default(7); // 7 = July–June
                $table->timestamp('enabled_at')->nullable();
                $table->integer('enabled_by')->nullable();
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('chart_of_accounts')) {
            Schema::create('chart_of_accounts', function (Blueprint $table) {
                $table->id();
                $table->integer('company_id')->index();
                $table->string('code', 20);
                $table->string('name', 150);
                $table->string('type', 20); // Asset / Liability / Equity / Revenue / Expense
                $table->foreignId('parent_id')->nullable()->constrained('chart_of_accounts')->nullOnDelete();
                $table->boolean('is_group')->default(false);
                $table->boolean('is_active')->default(true);
                // posting service (1.3) is key se account dhoondta hai — Cash, AP, Sales waghera. NULL = user ka banaya account
                $table->string('system_key', 40)->nullable();
                $table->timestamps();

                $table->unique(['company_id', 'code']);
                $table->unique(['company_id', 'system_key']);
            });
        }

        if (!Schema::hasTable('fiscal_years')) {
            Schema::create('fiscal_years', function (Blueprint $table) {
                $table->id();
                $table->integer('company_id')->index();
                $table->string('name', 30);
                $table->date('start_date');
                $table->date('end_date');
                $table->string('status', 10)->default('open'); // open / closed
                $table->timestamps();

                $table->unique(['company_id', 'start_date']);
            });
        }

        if (!Schema::hasTable('accounting_periods')) {
            Schema::create('accounting_periods', function (Blueprint $table) {
                $table->id();
                $table->foreignId('fiscal_year_id')->constrained('fiscal_years')->cascadeOnDelete();
                $table->unsignedTinyInteger('month'); // fiscal year ka 1–12 wala mahina
                $table->date('start_date');
                $table->date('end_date');
                $table->string('status', 10)->default('open'); // open / closed / locked
                $table->timestamps();

                $table->unique(['fiscal_year_id', 'month']);
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('accounting_periods');
        Schema::dropIfExists('fiscal_years');
        Schema::dropIfExists('chart_of_accounts');
        Schema::dropIfExists('accounting_settings');
    }
};
