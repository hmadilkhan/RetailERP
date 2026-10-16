<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// Phase 4.4: PO Bill (three-way match) — vendor ka invoice PO + GRN se juda. Sirf columns; type 'po_bill' purane columns me.
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('vendor_bills', function (Blueprint $table) {
            if (!Schema::hasColumn('vendor_bills', 'purchase_id')) {
                $table->unsignedBigInteger('purchase_id')->nullable()->index();   // purchase_general_details.purchase_id
            }
            if (!Schema::hasColumn('vendor_bills', 'variance')) {
                $table->decimal('variance', 18, 2)->default(0);                  // bill total − GRN pe pehle se book (PO rate)
            }
            if (!Schema::hasColumn('vendor_bills', 'match_status')) {
                $table->string('match_status', 12)->nullable();                   // matched | accepted (farq tolerance se bahar, manzoor)
            }
        });
        Schema::table('vendor_bill_lines', function (Blueprint $table) {
            if (!Schema::hasColumn('vendor_bill_lines', 'po_unit_cost')) {
                $table->decimal('po_unit_cost', 18, 4)->nullable();              // PO rate × ratio (tax / discount / freight samet)
            }
            if (!Schema::hasColumn('vendor_bill_lines', 'booked_value')) {
                $table->decimal('booked_value', 18, 2)->default(0);              // qty × po_unit_cost — GRN pe AP me pehle se
            }
        });
    }

    public function down(): void
    {
        Schema::table('vendor_bill_lines', function (Blueprint $table) {
            foreach (['po_unit_cost', 'booked_value'] as $c) {
                if (Schema::hasColumn('vendor_bill_lines', $c)) {
                    $table->dropColumn($c);
                }
            }
        });
        Schema::table('vendor_bills', function (Blueprint $table) {
            foreach (['purchase_id', 'variance', 'match_status'] as $c) {
                if (Schema::hasColumn('vendor_bills', $c)) {
                    $table->dropColumn($c);
                }
            }
        });
    }
};
