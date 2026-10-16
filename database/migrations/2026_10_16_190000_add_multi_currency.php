<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// Phase 4.5: foreign currency vendor bills. Saari purani raqam columns base currency me hi rehti hain (bill ke rate pe);
// foreign raqam alag columns me. Payment ka rate alag ho to farq Exchange Gain / Loss (fx_payments → VendorPaymentPoster).
return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('exchange_rates')) {
            Schema::create('exchange_rates', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('company_id');
                $table->char('currency', 3);
                $table->date('rate_date');
                $table->decimal('rate', 18, 6);              // 1 foreign = rate × base
                $table->unsignedBigInteger('created_by')->nullable();
                $table->timestamps();
                $table->unique(['company_id', 'currency', 'rate_date']);
            });
        }
        Schema::table('vendor_bills', function (Blueprint $table) {
            if (!Schema::hasColumn('vendor_bills', 'currency')) {
                $table->char('currency', 3)->nullable();      // NULL = base currency
            }
            if (!Schema::hasColumn('vendor_bills', 'fx_rate')) {
                $table->decimal('fx_rate', 18, 6)->default(1);
            }
            if (!Schema::hasColumn('vendor_bills', 'fx_total')) {
                $table->decimal('fx_total', 18, 2)->nullable();
            }
        });
        Schema::table('vendor_bill_lines', function (Blueprint $table) {
            if (!Schema::hasColumn('vendor_bill_lines', 'fx_unit_price')) {
                $table->decimal('fx_unit_price', 18, 4)->nullable();
            }
        });
        if (!Schema::hasTable('fx_payments')) {
            Schema::create('fx_payments', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('company_id');
                $table->unsignedBigInteger('vendor_bill_id');
                $table->unsignedBigInteger('ledger_id')->unique();     // vendor_ledger.vendor_account_id (payment row)
                $table->char('currency', 3);
                $table->decimal('fx_amount', 18, 2);
                $table->decimal('bill_rate', 18, 6);
                $table->decimal('pay_rate', 18, 6);
                $table->decimal('cleared', 18, 2);                      // AP kam hua (bill rate)
                $table->decimal('cash', 18, 2);                         // cash / bank se gaya (payment rate)
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('fx_payments');
        Schema::dropIfExists('exchange_rates');
        Schema::table('vendor_bill_lines', function (Blueprint $table) {
            if (Schema::hasColumn('vendor_bill_lines', 'fx_unit_price')) {
                $table->dropColumn('fx_unit_price');
            }
        });
        Schema::table('vendor_bills', function (Blueprint $table) {
            foreach (['currency', 'fx_rate', 'fx_total'] as $c) {
                if (Schema::hasColumn('vendor_bills', $c)) {
                    $table->dropColumn($c);
                }
            }
        });
    }
};
