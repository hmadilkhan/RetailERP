<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// Phase 3.4: cost centers (department / project — branch se alag) + automatic posting ke qaide.
// journal_entry_lines.cost_center_id 1.2 se pehle se maujood hai.
return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('cost_centers')) {
            Schema::create('cost_centers', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('company_id')->index();
                $table->string('code', 20);
                $table->string('name', 150);
                $table->boolean('is_active')->default(true);
                $table->timestamps();
                $table->unique(['company_id', 'code']);
            });
        }

        // automatic posting: branch ka default (sirf income / expense lines) ya expense category ka cost center
        if (!Schema::hasTable('cost_center_assignments')) {
            Schema::create('cost_center_assignments', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('company_id');
                $table->string('source', 20);                  // branch | expense_category
                $table->unsignedBigInteger('source_id');
                $table->unsignedBigInteger('cost_center_id');
                $table->timestamps();
                $table->unique(['company_id', 'source', 'source_id']);
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('cost_center_assignments');
        Schema::dropIfExists('cost_centers');
    }
};
