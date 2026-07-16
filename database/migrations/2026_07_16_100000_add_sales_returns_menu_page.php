<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('pages_details')) {
            return;
        }

        $existing = DB::table('pages_details')
            ->where('page_url', 'sales-returns/duplicate')
            ->first();

        if ($existing) {
            $pageId = $existing->id;
        } else {
            $pageId = DB::table('pages_details')->insertGetId([
                'page_name' => 'Sales Returns',
                'page_url' => 'sales-returns/duplicate',
                'navclass' => 'navsalesreturns',
                'icofont' => 'icon-arrow-right',
                'parent_id' => 103,
                'page_mode' => 'Child',
                'icofont_arrow' => 0,
            ]);
        }

        if (!Schema::hasTable('role_settings')) {
            return;
        }

        $roleIds = DB::table('role_settings')
            ->where('page_id', 14)
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

    public function down(): void
    {
        if (!Schema::hasTable('pages_details')) {
            return;
        }

        $page = DB::table('pages_details')
            ->where('page_url', 'sales-returns/duplicate')
            ->first();

        if (!$page) {
            return;
        }

        if (Schema::hasTable('role_settings')) {
            DB::table('role_settings')->where('page_id', $page->id)->delete();
        }

        DB::table('pages_details')->where('id', $page->id)->delete();
    }
};
