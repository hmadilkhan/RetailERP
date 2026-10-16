<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// Phase 3.5: RFQ — items + vendors, har vendor ka quote, comparison, jeetne wale vendor ka Vendor Bill draft.
return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('rfqs')) {
            Schema::create('rfqs', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('company_id')->index();
                $table->unsignedBigInteger('branch_id');
                $table->string('doc_no', 20);
                $table->string('title', 150);
                $table->date('rfq_date');
                $table->date('due_date')->nullable();
                $table->string('status', 12)->default('open');   // open | awarded | cancelled
                $table->text('notes')->nullable();
                $table->unsignedBigInteger('created_by')->nullable();
                $table->timestamp('awarded_at')->nullable();
                $table->timestamps();
                $table->unique(['company_id', 'doc_no']);
            });
        }
        if (!Schema::hasTable('rfq_lines')) {
            Schema::create('rfq_lines', function (Blueprint $table) {
                $table->id();
                $table->foreignId('rfq_id')->constrained('rfqs')->cascadeOnDelete();
                $table->unsignedBigInteger('product_id');
                $table->decimal('qty', 14, 3);
                $table->string('notes', 255)->nullable();
                $table->timestamps();
            });
        }
        if (!Schema::hasTable('rfq_vendors')) {
            Schema::create('rfq_vendors', function (Blueprint $table) {
                $table->id();
                $table->foreignId('rfq_id')->constrained('rfqs')->cascadeOnDelete();
                $table->unsignedBigInteger('vendor_id');
                $table->string('status', 12)->default('invited'); // invited | quoted | declined
                $table->unsignedInteger('delivery_days')->nullable();
                $table->date('valid_until')->nullable();
                $table->decimal('other_charges', 18, 2)->default(0);
                $table->string('notes', 255)->nullable();
                $table->timestamp('quoted_at')->nullable();
                $table->unsignedBigInteger('vendor_bill_id')->nullable();
                $table->timestamps();
                $table->unique(['rfq_id', 'vendor_id']);
            });
        }
        if (!Schema::hasTable('rfq_quotes')) {
            Schema::create('rfq_quotes', function (Blueprint $table) {
                $table->id();
                $table->foreignId('rfq_vendor_id')->constrained('rfq_vendors')->cascadeOnDelete();
                $table->foreignId('rfq_line_id')->constrained('rfq_lines')->cascadeOnDelete();
                $table->decimal('unit_price', 18, 4);
                $table->decimal('discount_pct', 6, 2)->default(0);
                $table->decimal('tax_rate', 6, 2)->default(0);
                $table->boolean('awarded')->default(false);
                $table->timestamps();
                $table->unique(['rfq_vendor_id', 'rfq_line_id']);
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('rfq_quotes');
        Schema::dropIfExists('rfq_vendors');
        Schema::dropIfExists('rfq_lines');
        Schema::dropIfExists('rfqs');
    }
};
