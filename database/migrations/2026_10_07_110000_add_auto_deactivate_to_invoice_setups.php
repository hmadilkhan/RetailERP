<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        if (Schema::hasColumn('invoice_setups', 'auto_deactivate')) {
            return;
        }

        Schema::table('invoice_setups', function (Blueprint $table) {
            // 1 = billing:enforce-overdue may deactivate the company / lock terminals; 0 = never
            $table->boolean('auto_deactivate')->default(true)->after('is_auto_invoice');
        });
    }

    public function down()
    {
        if (Schema::hasColumn('invoice_setups', 'auto_deactivate')) {
            Schema::table('invoice_setups', function (Blueprint $table) {
                $table->dropColumn('auto_deactivate');
            });
        }
    }
};
