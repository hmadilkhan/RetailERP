<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('users_sales_permission')) {
            return;
        }

        if (!Schema::hasColumn('users_sales_permission', 'allow_negative_stock')) {
            Schema::table('users_sales_permission', function (Blueprint $table) {
                $table->integer('allow_negative_stock')->default(0);
            });
        }

        // Setting is branch-wide (branch.allow_negative_stock), so every terminal starts with its branch's value.
        if (Schema::hasColumn('branch', 'allow_negative_stock')) {
            DB::statement('
                UPDATE users_sales_permission usp
                INNER JOIN terminal_details td ON td.terminal_id = usp.terminal_id
                INNER JOIN branch b ON b.branch_id = td.branch_id
                SET usp.allow_negative_stock = COALESCE(b.allow_negative_stock, 0)
            ');
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('users_sales_permission') && Schema::hasColumn('users_sales_permission', 'allow_negative_stock')) {
            Schema::table('users_sales_permission', function (Blueprint $table) {
                $table->dropColumn('allow_negative_stock');
            });
        }
    }
};
