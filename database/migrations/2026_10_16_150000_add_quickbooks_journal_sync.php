<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// Phase 3.6: QuickBooks — company-wise realm + GL journal sync (posted journal entries → QBO JournalEntry).
// quickbook_settings purana (migration me nahi) — sirf columns jodte hain, purane customer / item / invoice jobs waise hi.
return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('quickbook_settings')) {
            Schema::table('quickbook_settings', function (Blueprint $table) {
                if (!Schema::hasColumn('quickbook_settings', 'realm_id')) {
                    $table->string('realm_id', 40)->nullable();
                }
                if (!Schema::hasColumn('quickbook_settings', 'sync_journals')) {
                    $table->boolean('sync_journals')->default(false);
                }
                if (!Schema::hasColumn('quickbook_settings', 'sync_from_date')) {
                    $table->date('sync_from_date')->nullable();
                }
                if (!Schema::hasColumn('quickbook_settings', 'last_journal_sync_at')) {
                    $table->timestamp('last_journal_sync_at')->nullable();
                }
            });
        }
        if (!Schema::hasTable('qbo_account_map')) {
            Schema::create('qbo_account_map', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('company_id');
                $table->unsignedBigInteger('account_id');        // chart_of_accounts.id
                $table->string('qbo_account_id', 40);
                $table->string('qbo_account_name', 150)->nullable();
                $table->timestamps();
                $table->unique(['company_id', 'account_id']);
            });
        }
        if (!Schema::hasTable('qbo_journal_sync')) {
            Schema::create('qbo_journal_sync', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('company_id');
                $table->unsignedBigInteger('journal_entry_id');
                $table->string('status', 10);                    // synced | failed
                $table->string('qbo_id', 40)->nullable();
                $table->text('error')->nullable();
                $table->unsignedInteger('attempts')->default(0);
                $table->timestamp('synced_at')->nullable();
                $table->timestamps();
                $table->unique(['company_id', 'journal_entry_id']);
                $table->index(['company_id', 'status']);
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('qbo_journal_sync');
        Schema::dropIfExists('qbo_account_map');
        if (Schema::hasTable('quickbook_settings')) {
            Schema::table('quickbook_settings', function (Blueprint $table) {
                foreach (['realm_id', 'sync_journals', 'sync_from_date', 'last_journal_sync_at'] as $col) {
                    if (Schema::hasColumn('quickbook_settings', $col)) {
                        $table->dropColumn($col);
                    }
                }
            });
        }
    }
};
