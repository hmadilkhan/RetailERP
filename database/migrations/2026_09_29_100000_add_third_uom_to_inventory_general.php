<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// 3rd level UOM (display only): Carton -> Packet (weight_qty) -> Unit (weight_qty2)
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('inventory_general', function (Blueprint $table) {
            if (!Schema::hasColumn('inventory_general', 'cuom2')) {
                $table->integer('cuom2')->nullable()->after('cuom');
            }
            if (!Schema::hasColumn('inventory_general', 'weight_qty2')) {
                $table->decimal('weight_qty2', 12, 2)->nullable()->after('weight_qty');
            }
        });
    }

    public function down(): void
    {
        Schema::table('inventory_general', function (Blueprint $table) {
            if (Schema::hasColumn('inventory_general', 'cuom2')) {
                $table->dropColumn('cuom2');
            }
            if (Schema::hasColumn('inventory_general', 'weight_qty2')) {
                $table->dropColumn('weight_qty2');
            }
        });
    }
};
