<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('bookings', function (Blueprint $table) {
            $table->id();
            $table->integer('customer_id');
            $table->integer('service_provider_id');
            $table->date('service_date');
            $table->time('service_time');
            $table->string('status')->default('pending'); // pending, confirmed, cancelled
            $table->timestamps();

            // Foreign Keys
            $table->foreign('customer_id')->references('id')->on('customers')->onDelete('cascade');
            // Note: ServiceProvider model uses 'service_provider_details' table
            $table->foreign('service_provider_id')->references('id')->on('service_provider_details')->onDelete('cascade');
        });

        // Pivot table for Many-to-Many relationship between Booking and SaloonService
        Schema::create('booking_pivot', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('booking_id');
            $table->unsignedBigInteger('saloon_service_id');
            $table->timestamps();

            $table->foreign('booking_id')->references('id')->on('bookings')->onDelete('cascade');
            $table->foreign('saloon_service_id')->references('id')->on('saloon_services')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('booking_pivot');
        Schema::dropIfExists('bookings');
    }
};
