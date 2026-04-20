<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('crm_leads', function (Blueprint $table) {
            $table->unsignedBigInteger('converted_customer_id')->nullable()->after('is_converted')->index();
            $table->timestamp('converted_at')->nullable()->after('converted_customer_id');
            $table->unsignedBigInteger('converted_by')->nullable()->after('converted_at')->index();
        });
    }

    public function down(): void
    {
        Schema::table('crm_leads', function (Blueprint $table) {
            $table->dropColumn(['converted_customer_id', 'converted_at', 'converted_by']);
        });
    }
};
