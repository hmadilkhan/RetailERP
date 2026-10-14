<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        if (!Schema::hasTable('accounting_settings') || Schema::hasColumn('accounting_settings', 'customer_ledger_reversed')) {
            return;
        }

        Schema::table('accounting_settings', function (Blueprint $table) {
            // 0 = customer_account debit = receivable badha (code wala); 1 = company ledger screen ke hisaab se likhti hai (credit = badha)
            $table->boolean('customer_ledger_reversed')->default(false)->after('posting_start_date');
        });
    }

    public function down()
    {
        if (Schema::hasTable('accounting_settings') && Schema::hasColumn('accounting_settings', 'customer_ledger_reversed')) {
            Schema::table('accounting_settings', function (Blueprint $table) {
                $table->dropColumn('customer_ledger_reversed');
            });
        }
    }
};
