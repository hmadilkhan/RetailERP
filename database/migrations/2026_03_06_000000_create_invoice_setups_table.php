<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('invoice_setups', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('company_id');
            $table->enum('invoice_type', ['branch', 'terminal'])->default('branch');
            $table->decimal('monthly_charges_amount', 10, 2)->default(0);
            $table->integer('billing_cycle_day')->default(1);
            $table->integer('payment_due_days')->default(15);
            $table->string('invoice_prefix', 30)->nullable();
            $table->boolean('is_auto_invoice')->default(true);
            $table->timestamps();
        });

        Schema::create('invoice_setup_billing_rates', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('invoice_setup_id');
            $table->enum('scope_type', ['company', 'branch', 'terminal'])->default('company');
            $table->unsignedBigInteger('scope_id')->nullable();
            $table->string('charge_type');
            $table->decimal('rate', 10, 2);
            $table->date('effective_from');
            $table->date('effective_to')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            
            $table->foreign('invoice_setup_id')->references('id')->on('invoice_setups')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('invoice_setup_billing_rates');
        Schema::dropIfExists('invoice_setups');
    }
};
