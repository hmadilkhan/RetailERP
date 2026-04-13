<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('transfer_item_details', function (Blueprint $table) {
            $table->index(
                ['transfer_type', 'transfer_id', 'product_id'],
                'tid_transfer_type_transfer_id_product_id_idx'
            );
        });

        Schema::table('deliverychallan_general_details', function (Blueprint $table) {
            $table->index(
                ['transfer_type', 'Transfer_id', 'branch_from'],
                'dcg_transfer_type_transfer_id_branch_from_idx'
            );
        });

        Schema::table('deliverychallan_item_details', function (Blueprint $table) {
            $table->index(
                ['DC_Id', 'product_id'],
                'dci_dc_id_product_id_idx'
            );
        });
    }

    public function down(): void
    {
        Schema::table('deliverychallan_item_details', function (Blueprint $table) {
            $table->dropIndex('dci_dc_id_product_id_idx');
        });

        Schema::table('deliverychallan_general_details', function (Blueprint $table) {
            $table->dropIndex('dcg_transfer_type_transfer_id_branch_from_idx');
        });

        Schema::table('transfer_item_details', function (Blueprint $table) {
            $table->dropIndex('tid_transfer_type_transfer_id_product_id_idx');
        });
    }
};
