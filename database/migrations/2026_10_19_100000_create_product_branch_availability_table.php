<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// Branch Availability: row = product is branch ke POS me BAND. Row na ho = chalu (purana behaviour).
return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('product_branch_availability')) {
            return;
        }
        Schema::create('product_branch_availability', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('company_id')->index();
            $table->unsignedBigInteger('product_id');
            $table->unsignedBigInteger('branch_id')->index();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->timestamps();
            $table->unique(['product_id', 'branch_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('product_branch_availability');
    }
};
