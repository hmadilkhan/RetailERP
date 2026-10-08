<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

// Phase 1.2 menu: Journal Entries + General Ledger, Accounts Operations (page 16) ke andar, us ke roles/packages ke saath.
// Sidebar me sirf accounting-enabled company ko dikhte hain (App\View\Components\Sidebar::ACCOUNTING_PAGES).
return new class extends Migration
{
    private const ACCOUNTS_OPERATIONS_PAGE_ID = 16;

    private const PAGES = [
        ['Journal Entries', 'journal-entries', 'navjournalentries'],
        ['General Ledger', 'general-ledger', 'navgeneralledger'],
    ];

    public function up(): void
    {
        if (!Schema::hasTable('pages_details')) {
            return;
        }

        foreach (self::PAGES as [$name, $url, $navclass]) {
            $pageId = DB::table('pages_details')->where('page_url', $url)->value('id')
                ?? DB::table('pages_details')->insertGetId([
                    'page_name' => $name,
                    'page_url' => $url,
                    'navclass' => $navclass,
                    'icofont' => 'icon-arrow-right',
                    'parent_id' => self::ACCOUNTS_OPERATIONS_PAGE_ID,
                    'page_mode' => 'Child',
                    'icofont_arrow' => 0,
                ]);

            foreach (DB::table('role_settings')->where('page_id', self::ACCOUNTS_OPERATIONS_PAGE_ID)->pluck('role_id')->unique() as $roleId) {
                if (!DB::table('role_settings')->where('role_id', $roleId)->where('page_id', $pageId)->exists()) {
                    DB::table('role_settings')->insert(['role_id' => $roleId, 'page_id' => $pageId]);
                }
            }

            foreach (DB::table('package_module_permissions')->where('page_id', self::ACCOUNTS_OPERATIONS_PAGE_ID)->pluck('package_id')->unique() as $packageId) {
                if (!DB::table('package_module_permissions')->where('package_id', $packageId)->where('page_id', $pageId)->exists()) {
                    DB::table('package_module_permissions')->insert(['package_id' => $packageId, 'page_id' => $pageId, 'status_id' => 1]);
                }
            }
        }
    }

    public function down(): void
    {
        if (!Schema::hasTable('pages_details')) {
            return;
        }

        $pageIds = DB::table('pages_details')->whereIn('page_url', array_column(self::PAGES, 1))->pluck('id');
        DB::table('role_settings')->whereIn('page_id', $pageIds)->delete();
        DB::table('package_module_permissions')->whereIn('page_id', $pageIds)->delete();
        DB::table('pages_details')->whereIn('id', $pageIds)->delete();
    }
};
