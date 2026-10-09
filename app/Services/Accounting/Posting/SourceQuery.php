<?php

namespace App\Services\Accounting\Posting;

use Illuminate\Support\Facades\DB;

/** Posters ke common helpers — company ki branches, aur bina date index wali badi tables me window ki shuruat. */
class SourceQuery
{
    private static array $branches = [];

    /** @return int[] */
    public static function branchIds(int $companyId): array
    {
        return self::$branches[$companyId] ??= DB::table('branch')->where('company_id', $companyId)
            ->pluck('branch_id')->map(fn ($id) => (int) $id)->all() ?: [0];
    }

    /**
     * inventory_stock_report_table (27 lakh rows) / customer_account pe date ka index nahi. PK ke saath date bhi
     * badhti hai, isliye binary search se pehli id dhoondo jis ki date >= $from (~22 PK lookups, full scan nahi).
     * $margin ids peeche se shuru — kuch rows ki date ulti-seedhi hoti hai (device clock / timezone).
     */
    public static function idFloor(string $table, string $pk, string $dateColumn, string $from, int $margin = 5000): int
    {
        // ek run me teen stock posters ek hi floor maangte hain
        return self::$floors["{$table}:{$from}"] ??= self::findFloor($table, $pk, $dateColumn, $from, $margin);
    }

    private static array $floors = [];

    private static function findFloor(string $table, string $pk, string $dateColumn, string $from, int $margin): int
    {
        $low = (int) DB::table($table)->min($pk);
        $high = (int) DB::table($table)->max($pk);
        if (!$high) {
            return 0;
        }

        while ($low < $high) {
            $mid = intdiv($low + $high, 2);
            $row = DB::table($table)->where($pk, '>=', $mid)->orderBy($pk)->first([$pk, $dateColumn]);
            if (!$row) {
                $high = $mid;
                continue;
            }
            if (substr((string) $row->{$dateColumn}, 0, 10) < $from) {
                $low = (int) $row->{$pk} + 1;
            } else {
                $high = $mid;
            }
        }

        return max(0, $low - $margin);
    }

    /** Server UTC me likhe timestamps (DB default) ko Pakistan date me badalne ka SQL. */
    public static function pkDate(string $column): string
    {
        return "DATE(CONVERT_TZ({$column}, '+00:00', '+05:00'))";
    }
}
