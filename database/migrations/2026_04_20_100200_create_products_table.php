<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('crm_products', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_type_id')->constrained('crm_product_types')->cascadeOnUpdate()->restrictOnDelete();
            $table->string('name');
            $table->string('code')->nullable()->unique();
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index(['product_type_id', 'is_active']);
            $table->unique(['product_type_id', 'name']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('crm_products');
    }
};
