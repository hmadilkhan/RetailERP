<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

// Phase 1.1 menu: Accounting Setup (Operations, Terminal Permissions jaisa access — super admin)
// + Chart of Accounts / Fiscal Years (Accounts Operations ke andar, us ke roles/packages ke saath).
// Company pages sidebar me sirf tab dikhte hain jab company ka accounting on ho (App\View\Components\Sidebar).
return new class extends Migration
{
    private const OPERATIONS_PAGE_ID = 4;
    private const ACCOUNTS_OPERATIONS_PAGE_ID = 16;

    private const PAGES = [
        // [name, url, navclass, parent, page whose roles/packages are mirrored]
        ['Accounting Setup', 'accounting-setup', 'navaccountingsetup', self::OPERATIONS_PAGE_ID, 'terminal-permissions'],
        ['Chart of Accounts', 'chart-of-accounts', 'navchartofaccounts', self::ACCOUNTS_OPERATIONS_PAGE_ID, self::ACCOUNTS_OPERATIONS_PAGE_ID],
        ['Fiscal Years', 'fiscal-years', 'navfiscalyears', self::ACCOUNTS_OPERATIONS_PAGE_ID, self::ACCOUNTS_OPERATIONS_PAGE_ID],
    ];

    public function up(): void
    {
        if (!Schema::hasTable('pages_details')) {
            return;
        }

        foreach (self::PAGES as [$name, $url, $navclass, $parentId, $mirror]) {
            $pageId = DB::table('pages_details')->where('page_url', $url)->value('id')
                ?? DB::table('pages_details')->insertGetId([
                    'page_name' => $name,
                    'page_url' => $url,
                    'navclass' => $navclass,
                    'icofont' => 'icon-arrow-right',
                    'parent_id' => $parentId,
                    'page_mode' => 'Child',
                    'icofont_arrow' => 0,
                ]);

            $mirrorId = is_int($mirror) ? $mirror : DB::table('pages_details')->where('page_url', $mirror)->value('id');
            if (!$mirrorId) {
                continue;
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
