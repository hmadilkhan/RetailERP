<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// Phase 2.2: batch / expiry lot (inventory_stock) tak — FEFO deduction (Custom_Helper::lotOrder) + Expiry Report.
// Pehle sirf purchase_item_details (PO line) pe tha. inventory_stock ~49k rows — ALTER turant.
return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('inventory_stock', 'expiry_date')) {
            Schema::table('inventory_stock', function (Blueprint $table) {
                $table->string('batch_no', 100)->nullable();
                $table->date('expiry_date')->nullable()->index('inventory_stock_expiry_date_index');
            });
        }

        if (Schema::hasTable('vendor_bill_lines') && !Schema::hasColumn('vendor_bill_lines', 'expiry_date')) {
            Schema::table('vendor_bill_lines', function (Blueprint $table) {
                $table->string('batch_no', 100)->nullable()->after('landed_cost');
                $table->date('expiry_date')->nullable()->after('batch_no');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('inventory_stock', 'expiry_date')) {
            Schema::table('inventory_stock', function (Blueprint $table) {
                $table->dropIndex('inventory_stock_expiry_date_index');
                $table->dropColumn(['batch_no', 'expiry_date']);
            });
        }
        if (Schema::hasTable('vendor_bill_lines') && Schema::hasColumn('vendor_bill_lines', 'expiry_date')) {
            Schema::table('vendor_bill_lines', function (Blueprint $table) {
                $table->dropColumn(['batch_no', 'expiry_date']);
            });
        }
    }
};
