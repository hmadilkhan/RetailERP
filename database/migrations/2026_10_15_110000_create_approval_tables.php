<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// Phase 2.4: approval rules (company + module + branch + amount slab + approver role) aur requests (log bhi yahi).
// Jis company ka koi active rule nahi, us pe kuch nahi badalta.
return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('approval_rules')) {
            Schema::create('approval_rules', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('company_id')->index();
                $table->string('module', 30);                       // stock_adjustment | transfer | vendor_bill | expense
                $table->unsignedBigInteger('branch_id')->nullable(); // NULL = saari branches
                $table->decimal('min_amount', 18, 2)->default(0);    // is se zyada / barabar pe approval
                $table->unsignedInteger('approver_role_id')->default(2); // Sub-Admin
                $table->boolean('is_active')->default(true);
                $table->unsignedBigInteger('created_by')->nullable();
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('approval_requests')) {
            Schema::create('approval_requests', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('company_id');
                $table->unsignedBigInteger('branch_id')->nullable();
                $table->string('module', 30);
                $table->unsignedBigInteger('subject_id')->nullable(); // transfer id / vendor bill id / expense id
                $table->string('reference', 255)->nullable();
                $table->decimal('amount', 18, 2)->default(0);
                $table->json('payload')->nullable();                 // approve pe kya chalana hai (stock adjustment)
                $table->string('status', 12)->default('pending');   // pending | approved | rejected | cancelled
                $table->unsignedInteger('approver_role_id')->nullable();
                $table->unsignedBigInteger('requested_by')->nullable();
                $table->unsignedBigInteger('decided_by')->nullable();
                $table->timestamp('decided_at')->nullable();
                $table->text('remarks')->nullable();
                $table->text('result')->nullable();                  // approve pe kya hua / error
                $table->timestamps();
                $table->index(['company_id', 'status']);
                $table->index(['module', 'subject_id']);
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('approval_requests');
        Schema::dropIfExists('approval_rules');
    }
};
