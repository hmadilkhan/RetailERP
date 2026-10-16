<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// Phase 3.3: fixed asset register + mahana depreciation (GL JV). FixedAssetService.
return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('fixed_assets')) {
            Schema::create('fixed_assets', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('company_id')->index();
                $table->unsignedBigInteger('branch_id')->nullable();
                $table->string('code', 30);                               // FA-0001 company-wise
                $table->string('name', 255);
                $table->unsignedBigInteger('asset_account_id');           // 1210 / 1220 / 1230 …
                $table->date('purchase_date');
                $table->date('depreciation_start');                       // is mahine se depreciation
                $table->decimal('cost', 18, 2);
                $table->decimal('salvage_value', 18, 2)->default(0);      // aakhir me bachi qeemat
                $table->unsignedSmallInteger('useful_life_months');
                $table->string('method', 20)->default('straight_line');  // straight_line | declining
                $table->decimal('rate', 8, 4)->nullable();                // declining: saalana %
                $table->decimal('opening_accumulated', 18, 2)->default(0); // system me laane se pehle ka depreciation
                $table->string('status', 12)->default('active');          // active | disposed
                $table->date('disposed_on')->nullable();
                $table->decimal('disposal_amount', 18, 2)->nullable();
                $table->unsignedBigInteger('purchase_entry_id')->nullable();
                $table->unsignedBigInteger('disposal_entry_id')->nullable();
                $table->text('notes')->nullable();
                $table->unsignedBigInteger('created_by')->nullable();
                $table->timestamps();
                $table->unique(['company_id', 'code']);
            });
        }

        if (!Schema::hasTable('fixed_asset_depreciations')) {
            Schema::create('fixed_asset_depreciations', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('fixed_asset_id');
                $table->char('period', 7);                                 // YYYY-MM
                $table->decimal('amount', 18, 2);
                $table->unsignedBigInteger('journal_entry_id')->nullable();
                $table->timestamps();
                $table->unique(['fixed_asset_id', 'period']);
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('fixed_asset_depreciations');
        Schema::dropIfExists('fixed_assets');
    }
};
