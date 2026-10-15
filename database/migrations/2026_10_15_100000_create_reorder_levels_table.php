<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// Phase 2.3: branch + product ke reorder levels. Row na ho to min = inventory_qty_reminders.reminder_qty (product-wide).
return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('reorder_levels')) {
            return;
        }
        Schema::create('reorder_levels', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('company_id')->index();
            $table->unsignedBigInteger('branch_id');
            $table->unsignedBigInteger('product_id');
            $table->decimal('min_qty', 18, 4)->nullable();      // is se kam / barabar → mangwao
            $table->decimal('max_qty', 18, 4)->nullable();      // mangwa kar yahan tak bharo
            $table->decimal('reorder_qty', 18, 4)->nullable();  // max na ho to har baar itna
            $table->unsignedSmallInteger('lead_time_days')->nullable(); // maal aane me kitne din
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamps();
            $table->unique(['branch_id', 'product_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reorder_levels');
    }
};
