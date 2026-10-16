<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// Phase 3.1: bank statement ko GL ke bank account (chart_of_accounts) se milana. Purani bank_deposit_details live pe khali hai,
// asal bank hisaab GL me (POS card / online sales, vendor payments, JV) — isliye reconciliation GL lines pe.
return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('bank_reconciliations')) {
            Schema::create('bank_reconciliations', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('company_id')->index();
                $table->unsignedBigInteger('account_id');              // chart_of_accounts (bank)
                $table->date('statement_from');
                $table->date('statement_to');
                $table->decimal('opening_balance', 18, 2)->default(0); // statement ka
                $table->decimal('closing_balance', 18, 2)->default(0); // statement ka
                $table->string('status', 12)->default('open');         // open | reconciled
                $table->unsignedBigInteger('created_by')->nullable();
                $table->unsignedBigInteger('reconciled_by')->nullable();
                $table->timestamp('reconciled_at')->nullable();
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('bank_statement_lines')) {
            Schema::create('bank_statement_lines', function (Blueprint $table) {
                $table->id();
                $table->foreignId('reconciliation_id')->constrained('bank_reconciliations')->cascadeOnDelete();
                $table->date('txn_date');
                $table->string('description', 500)->nullable();
                $table->string('reference', 100)->nullable();
                $table->decimal('amount', 18, 2);                       // + bank me aaya, − bank se gaya
                $table->unsignedBigInteger('journal_entry_id')->nullable(); // is line se bani entry (bank charges waghera)
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('bank_matches')) {
            Schema::create('bank_matches', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('reconciliation_id')->index();
                $table->unsignedBigInteger('statement_line_id')->index();
                $table->unsignedBigInteger('journal_entry_line_id')->unique(); // ek GL line ek hi baar clear
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('bank_matches');
        Schema::dropIfExists('bank_statement_lines');
        Schema::dropIfExists('bank_reconciliations');
    }
};
