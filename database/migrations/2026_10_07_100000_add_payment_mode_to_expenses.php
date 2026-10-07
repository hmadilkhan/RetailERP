<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// Phase 0.1: web expense ab cash_ledger / bank_deposit_details se paisa kam karti hai.
// payment_mode NULL = purani (ya POS terminal) expense, us ka koi ledger effect nahi.
// cash_ledger_id / bank_deposit_id = jo ledger row is expense ne banayi, edit/delete pe reversal ke liye.
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('expenses', function (Blueprint $table) {
            if (!Schema::hasColumn('expenses', 'payment_mode')) {
                $table->string('payment_mode', 10)->nullable()->after('net_amount');
            }
            if (!Schema::hasColumn('expenses', 'bank_account_id')) {
                $table->integer('bank_account_id')->nullable()->after('payment_mode');
            }
            if (!Schema::hasColumn('expenses', 'cash_ledger_id')) {
                $table->integer('cash_ledger_id')->nullable()->after('bank_account_id');
            }
            if (!Schema::hasColumn('expenses', 'bank_deposit_id')) {
                $table->integer('bank_deposit_id')->nullable()->after('cash_ledger_id');
            }
        });
    }

    public function down(): void
    {
        Schema::table('expenses', function (Blueprint $table) {
            foreach (['bank_deposit_id', 'cash_ledger_id', 'bank_account_id', 'payment_mode'] as $column) {
                if (Schema::hasColumn('expenses', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
