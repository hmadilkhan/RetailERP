<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// Phase 1.3: automatic posting. Source tables (expenses, GRN, POS sales ...) ko scan karke GL entries banti hain
// (PostingService) — legacy flows nahi chhede. source_ref + source_hash se pata chalta hai kya post ho chuka / badal gaya.
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('accounting_settings', function (Blueprint $table) {
            if (!Schema::hasColumn('accounting_settings', 'posting_start_date')) {
                $table->date('posting_start_date')->nullable()->after('fiscal_year_start_month'); // NULL = auto posting band
            }
            if (!Schema::hasColumn('accounting_settings', 'last_posted_at')) {
                $table->timestamp('last_posted_at')->nullable()->after('posting_start_date');
            }
        });

        Schema::table('journal_entries', function (Blueprint $table) {
            if (!Schema::hasColumn('journal_entries', 'source_ref')) {
                // source ke andar key: expense id, GRN id, ya "branch:date" (POS daily summary)
                $table->string('source_ref', 60)->nullable()->after('source_id');
                $table->char('source_hash', 40)->nullable()->after('source_ref');
                $table->index(['company_id', 'source_type', 'source_ref']);
            }
        });

        if (!Schema::hasTable('account_mappings')) {
            Schema::create('account_mappings', function (Blueprint $table) {
                $table->id();
                $table->integer('company_id');
                $table->string('map_type', 40); // expense_category / bank_account
                $table->string('map_key', 60);
                $table->foreignId('account_id')->constrained('chart_of_accounts');
                $table->timestamps();

                $table->unique(['company_id', 'map_type', 'map_key']);
            });
        }

        if (!Schema::hasTable('posting_errors')) {
            Schema::create('posting_errors', function (Blueprint $table) {
                $table->id();
                $table->integer('company_id');
                $table->string('source_type', 40);
                $table->string('source_ref', 60);
                $table->string('level', 10)->default('error'); // error = post nahi hua, warning = post hua (suspense)
                $table->text('message');
                $table->unsignedInteger('attempts')->default(1);
                $table->timestamps();

                $table->unique(['company_id', 'source_type', 'source_ref']);
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('posting_errors');
        Schema::dropIfExists('account_mappings');

        Schema::table('journal_entries', function (Blueprint $table) {
            if (Schema::hasColumn('journal_entries', 'source_ref')) {
                $table->dropIndex(['company_id', 'source_type', 'source_ref']);
                $table->dropColumn(['source_ref', 'source_hash']);
            }
        });

        Schema::table('accounting_settings', function (Blueprint $table) {
            foreach (['last_posted_at', 'posting_start_date'] as $column) {
                if (Schema::hasColumn('accounting_settings', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
