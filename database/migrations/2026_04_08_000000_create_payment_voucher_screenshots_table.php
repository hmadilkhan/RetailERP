<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payment_voucher_screenshots', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('payment_voucher_id');
            $table->string('disk', 50)->default('public');
            $table->string('file_path', 255);
            $table->string('original_name', 255)->nullable();
            $table->string('file_name', 255)->nullable();
            $table->string('mime_type', 100)->nullable();
            $table->unsignedBigInteger('file_size')->nullable();
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();

            $table->index(['payment_voucher_id', 'sort_order'], 'pvs_voucher_sort_idx');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payment_voucher_screenshots');
    }
};
