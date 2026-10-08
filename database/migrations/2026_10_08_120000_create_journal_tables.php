<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// Phase 1.2: Journal / General Ledger. Har entry pe SUM(debit) = SUM(credit) — JournalService enforce karti hai.
// Posted entry immutable hai; galti ho to reversal entry (reversal_of_id) banti hai.
return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('journal_entries')) {
            Schema::create('journal_entries', function (Blueprint $table) {
                $table->id();
                $table->integer('company_id');
                $table->integer('branch_id')->nullable();
                $table->string('entry_no', 30);
                $table->date('entry_date');
                $table->foreignId('period_id')->constrained('accounting_periods');
                // manual = user ka JV; 1.3 me grn / sale / expense waghera + source_id
                $table->string('source_type', 40)->default('manual');
                $table->unsignedBigInteger('source_id')->nullable();
                $table->string('narration', 500)->nullable();
                $table->string('status', 10)->default('draft'); // draft / posted
                $table->decimal('total', 15, 2)->default(0);
                $table->foreignId('reversal_of_id')->nullable()->constrained('journal_entries');
                $table->integer('created_by')->nullable();
                $table->integer('posted_by')->nullable();
                $table->timestamp('posted_at')->nullable();
                $table->timestamps();

                $table->unique(['company_id', 'entry_no']);
                $table->index(['company_id', 'entry_date']);
                $table->index(['source_type', 'source_id']);
            });
        }

        if (!Schema::hasTable('journal_entry_lines')) {
            Schema::create('journal_entry_lines', function (Blueprint $table) {
                $table->id();
                $table->foreignId('journal_entry_id')->constrained('journal_entries')->cascadeOnDelete();
                $table->foreignId('account_id')->constrained('chart_of_accounts'); // restrict: entries wala account delete nahi hota
                $table->decimal('debit', 15, 2)->default(0);
                $table->decimal('credit', 15, 2)->default(0);
                $table->string('narration', 300)->nullable();
                $table->integer('cost_center_id')->nullable(); // Phase 3.4
                $table->timestamps();

                $table->index('account_id');
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('journal_entry_lines');
        Schema::dropIfExists('journal_entries');
    }
};
