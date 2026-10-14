<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

// Phase 2.2 menu: Expiry Report — Inventory (page 32) ke andar, Stock Details (stock-list) ke roles/packages ke saath.
return new class extends Migration
{
    private const PARENT_PAGE_ID = 32;
    private const MIRROR_URL = 'stock-list';

    public function up(): void
    {
        if (!Schema::hasTable('pages_details')) {
            return;
        }

        $pageId = DB::table('pages_details')->where('page_url', 'expiry-report')->value('id')
            ?? DB::table('pages_details')->insertGetId([
                'page_name' => 'Expiry Report',
                'page_url' => 'expiry-report',
                'navclass' => 'navexpiryreport',
                'icofont' => 'icon-arrow-right',
                'parent_id' => self::PARENT_PAGE_ID,
                'page_mode' => 'Child',
                'icofont_arrow' => 0,
            ]);

        $mirrorId = DB::table('pages_details')->where('page_url', self::MIRROR_URL)->value('id');
        if (!$mirrorId) {
            return;
        }

        foreach (DB::table('role_settings')->where('page_id', $mirrorId)->pluck('role_id')->unique() as $roleId) {
            if (!DB::table('role_settings')->where('role_id', $roleId)->where('page_id', $pageId)->exists()) {
                DB::table('role_settings')->insert(['role_id' => $roleId, 'page_id' => $pageId]);
            }
        }

        foreach (DB::table('package_module_permissions')->where('page_id', $mirrorId)->pluck('package_id')->unique() as $packageId) {
            if (!DB::table('package_module_permissions')->where('package_id', $packageId)->where('page_id', $pageId)->exists()) {
                DB::table('package_module_permissions')->insert(['package_id' => $packageId, 'page_id' => $pageId, 'status_id' => 1]);
            }
        }
    }

    public function down(): void
    {
        if (!Schema::hasTable('pages_details')) {
            return;
        }

        $pageIds = DB::table('pages_details')->where('page_url', 'expiry-report')->pluck('id');
        DB::table('role_settings')->whereIn('page_id', $pageIds)->delete();
        DB::table('package_module_permissions')->whereIn('page_id', $pageIds)->delete();
        DB::table('pages_details')->whereIn('id', $pageIds)->delete();
    }
};
