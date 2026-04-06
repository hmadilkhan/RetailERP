<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payment_vouchers', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('voucher_no', 50)->unique();
            $table->unsignedBigInteger('company_id');
            $table->date('payment_date');
            $table->unsignedBigInteger('payment_mode_id')->nullable();
            $table->decimal('total_received_amount', 14, 2)->default(0);
            $table->string('reference_no', 100)->nullable();
            $table->string('narration', 255)->nullable();
            $table->unsignedBigInteger('received_by')->nullable();
            $table->string('whatsapp_to', 30)->nullable();
            $table->string('pdf_path', 255)->nullable();
            $table->timestamp('whatsapp_sent_at')->nullable();
            $table->string('whatsapp_status', 30)->nullable();
            $table->timestamps();

            $table->index(['company_id', 'payment_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payment_vouchers');
    }
};
