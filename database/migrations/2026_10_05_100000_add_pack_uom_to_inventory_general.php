<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// Packing UOM (entry + display only): stock primary (Pcs) me rehta hai, 1 pack_uom = pack_qty primary.
// NULL = feature off, purane products/companies ka behaviour same rehta hai. weight_qty ko nahi chhedta.
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('inventory_general', function (Blueprint $table) {
            if (!Schema::hasColumn('inventory_general', 'pack_uom')) {
                $table->integer('pack_uom')->nullable()->after('weight_qty2');
            }
            if (!Schema::hasColumn('inventory_general', 'pack_qty')) {
                $table->decimal('pack_qty', 12, 2)->nullable()->after('pack_uom');
            }
        });
    }

    public function down(): void
    {
        Schema::table('inventory_general', function (Blueprint $table) {
            if (Schema::hasColumn('inventory_general', 'pack_qty')) {
                $table->dropColumn('pack_qty');
            }
            if (Schema::hasColumn('inventory_general', 'pack_uom')) {
                $table->dropColumn('pack_uom');
            }
        });
    }
};
