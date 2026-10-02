<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    // Operations parent menu and the Terminal Manager page whose access is mirrored.
    private const OPERATIONS_PAGE_ID = 4;
    private const TERMINAL_MANAGER_PAGE_ID = 164;

    public function up(): void
    {
        if (!Schema::hasTable('pages_details')) {
            return;
        }

        $existing = DB::table('pages_details')
            ->where('page_url', 'terminal-permissions')
            ->first();

        if ($existing) {
            $pageId = $existing->id;
        } else {
            $pageId = DB::table('pages_details')->insertGetId([
                'page_name' => 'Terminal Permissions',
                'page_url' => 'terminal-permissions',
                'navclass' => 'navterminalpermissions',
                'icofont' => 'icon-arrow-right',
                'parent_id' => self::OPERATIONS_PAGE_ID,
                'page_mode' => 'Child',
                'icofont_arrow' => 0,
            ]);
        }

        if (Schema::hasTable('role_settings')) {
            $roleIds = DB::table('role_settings')
                ->where('page_id', self::TERMINAL_MANAGER_PAGE_ID)
                ->pluck('role_id')
                ->unique();

            foreach ($roleIds as $roleId) {
                $exists = DB::table('role_settings')
                    ->where('role_id', $roleId)
                    ->where('page_id', $pageId)
                    ->exists();

                if (!$exists) {
                    DB::table('role_settings')->insert([
                        'role_id' => $roleId,
                        'page_id' => $pageId,
                    ]);
                }
            }
        }

        if (Schema::hasTable('package_module_permissions')) {
            $packageIds = DB::table('package_module_permissions')
                ->where('page_id', self::TERMINAL_MANAGER_PAGE_ID)
                ->pluck('package_id')
                ->unique();

            foreach ($packageIds as $packageId) {
                $exists = DB::table('package_module_permissions')
                    ->where('package_id', $packageId)
                    ->where('page_id', $pageId)
                    ->exists();

                if (!$exists) {
                    DB::table('package_module_permissions')->insert([
                        'package_id' => $packageId,
                        'page_id' => $pageId,
                        'status_id' => 1,
                    ]);
                }
            }
        }
    }

    public function down(): void
    {
        if (!Schema::hasTable('pages_details')) {
            return;
        }

        $page = DB::table('pages_details')
            ->where('page_url', 'terminal-permissions')
            ->first();

        if (!$page) {
            return;
        }

        if (Schema::hasTable('role_settings')) {
            DB::table('role_settings')->where('page_id', $page->id)->delete();
        }

        if (Schema::hasTable('package_module_permissions')) {
            DB::table('package_module_permissions')->where('page_id', $page->id)->delete();
        }

        DB::table('pages_details')->where('id', $page->id)->delete();
    }
};
