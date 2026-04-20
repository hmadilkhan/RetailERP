<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('crm_leads', function (Blueprint $table) {
            $table->id();
            $table->string('lead_code')->unique();
            $table->string('contact_person_name');
            $table->string('company_name')->nullable();
            $table->string('contact_number', 50);
            $table->string('alternate_number', 50)->nullable();
            $table->string('whatsapp_number', 50)->nullable();
            $table->string('email')->nullable()->index();
            $table->string('country')->nullable();
            $table->string('city')->nullable()->index();
            $table->text('address')->nullable();
            $table->string('website')->nullable();
            $table->foreignId('lead_source_id')->constrained('crm_lead_sources')->cascadeOnUpdate()->restrictOnDelete();
            $table->string('campaign_name')->nullable();
            $table->string('referral_person_name')->nullable();
            $table->foreignId('product_type_id')->constrained('crm_product_types')->cascadeOnUpdate()->restrictOnDelete();
            $table->foreignId('product_id')->nullable()->constrained('crm_products')->cascadeOnUpdate()->nullOnDelete();
            $table->string('inquiry_type')->nullable();
            $table->string('business_type')->nullable();
            $table->unsignedInteger('required_quantity')->nullable();
            $table->unsignedInteger('branch_count')->nullable();
            $table->string('existing_system')->nullable();
            $table->string('competitor_name')->nullable();
            $table->string('budget_range')->nullable();
            $table->date('expected_go_live_date')->nullable();
            $table->longText('requirement_summary');
            $table->foreignId('status_id')->constrained('crm_lead_statuses')->cascadeOnUpdate()->restrictOnDelete();
            $table->string('priority', 30)->default('medium')->index();
            $table->string('temperature', 30)->default('warm')->index();
            $table->unsignedBigInteger('assigned_to')->nullable()->index();
            $table->unsignedSmallInteger('lead_score')->default(0);
            $table->decimal('expected_deal_value', 15, 2)->nullable();
            $table->unsignedTinyInteger('probability_percent')->default(0);
            $table->date('last_contact_date')->nullable();
            $table->date('next_followup_date')->nullable()->index();
            $table->string('preferred_contact_method')->nullable();
            $table->text('lost_reason')->nullable();
            $table->boolean('is_converted')->default(false);
            $table->unsignedBigInteger('created_by')->nullable()->index();
            $table->unsignedBigInteger('updated_by')->nullable()->index();
            $table->timestamps();

            $table->index('contact_number', 'crm_leads_contact_number_idx');
            $table->index(
                ['lead_source_id', 'product_type_id', 'product_id', 'status_id'],
                'crm_leads_pipeline_filter_idx'
            );
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('crm_leads');
    }
};
