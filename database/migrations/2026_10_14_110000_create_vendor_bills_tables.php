<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// Phase 2.1: Vendor Bill (purchase invoice — PO ke baghair bhi), Debit Note (maal wapas), Credit Note (vendor ka rebate).
return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('vendor_bills')) {
            Schema::create('vendor_bills', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('company_id')->index();
                $table->unsignedBigInteger('branch_id');
                $table->unsignedBigInteger('vendor_id')->index();
                $table->string('type', 12)->default('bill');          // bill | debit_note | credit_note
                $table->string('doc_no', 20);                         // VB-000001 / DN-000001 / CN-000001 (company-wise)
                $table->string('vendor_ref', 100)->nullable();        // vendor ka invoice / bill no
                $table->date('bill_date');
                $table->date('due_date')->nullable();
                $table->unsignedBigInteger('against_bill_id')->nullable()->index(); // note kis bill pe
                $table->decimal('subtotal', 18, 2)->default(0);       // Σ qty × unit price
                $table->decimal('discount', 18, 2)->default(0);
                $table->decimal('tax_amount', 18, 2)->default(0);
                $table->decimal('other_charges', 18, 2)->default(0);  // freight / loading — items ki cost me baant di jati hai
                $table->decimal('total', 18, 2)->default(0);          // vendor ko dena (note: kam hona)
                $table->decimal('paid', 18, 2)->default(0);           // bill pe payment + notes
                $table->string('status', 10)->default('draft');       // draft | posted | cancelled
                $table->unsignedBigInteger('grn_id')->nullable();     // purchase_rec_gen
                $table->text('narration')->nullable();
                $table->unsignedBigInteger('created_by')->nullable();
                $table->unsignedBigInteger('posted_by')->nullable();
                $table->timestamp('posted_at')->nullable();
                $table->timestamp('cancelled_at')->nullable();
                $table->timestamps();
                $table->unique(['company_id', 'doc_no']);
            });
        }

        if (!Schema::hasTable('vendor_bill_lines')) {
            Schema::create('vendor_bill_lines', function (Blueprint $table) {
                $table->id();
                $table->foreignId('vendor_bill_id')->constrained('vendor_bills')->cascadeOnDelete();
                $table->unsignedBigInteger('product_id')->nullable(); // credit note me khali ho sakta hai
                $table->unsignedBigInteger('against_line_id')->nullable(); // debit note line → bill line
                $table->string('description', 255)->nullable();
                $table->decimal('qty', 18, 4)->default(0);            // primary unit
                $table->decimal('unit_price', 18, 4)->default(0);
                $table->decimal('discount', 18, 2)->default(0);       // line ka discount (amount)
                $table->decimal('tax_rate', 8, 4)->default(0);
                $table->decimal('tax_amount', 18, 2)->default(0);
                $table->decimal('amount', 18, 2)->default(0);         // qty × unit price − discount (tax ke baghair)
                $table->decimal('landed_cost', 18, 4)->default(0);    // per unit: (amount + charges ka hissa) / qty
                $table->unsignedBigInteger('stock_id')->nullable();   // inventory_stock lot
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('vendor_bill_lines');
        Schema::dropIfExists('vendor_bills');
    }
};
