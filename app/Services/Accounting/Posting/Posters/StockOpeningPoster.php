<?php

namespace App\Services\Accounting\Posting\Posters;

use App\Services\Accounting\Posting\AccountResolver;
use App\Services\Accounting\Posting\Poster;
use App\Services\Accounting\Posting\PostingDocument;
use App\Services\Accounting\Posting\SourceQuery;
use Illuminate\Support\Facades\DB;

/**
 * Opening stock (form + CSV) — branch + din ki ek entry (ref "branch:date"):  Dr Inventory / Cr Opening Balance Equity.
 * Source: inventory_stock_report_table narration 'Stock Opening' / 'Stock Openend from csv file', value = qty × cost.
 * Note: CSV wali rows me cost aksar 0 hoti hai — un ki value 0 jati hai.
 */
class StockOpeningPoster implements Poster
{
    public function sourceType(): string
    {
        return 'stock_opening';
    }

    public function label(): string
    {
        return 'Opening Stock';
    }

    public function documents(int $companyId, string $from, string $to, AccountResolver $accounts): iterable
    {
        $branches = implode(',', SourceQuery::branchIds($companyId));
        $floor = SourceQuery::idFloor('inventory_stock_report_table', 'stock_report_id', 'date', $from);

        $rows = DB::select("
            SELECT r.branch_id, DATE(r.date) AS d, SUM(ABS(r.qty) * r.cost) AS value
            FROM inventory_stock_report_table r
            WHERE r.stock_report_id >= ? AND r.narration IN ('Stock Opening', 'Stock Openend from csv file')
              AND r.branch_id IN ({$branches}) AND DATE(r.date) BETWEEN ? AND ?
            GROUP BY r.branch_id, DATE(r.date)", [$floor, $from, $to]);

        foreach ($rows as $row) {
            $ref = $row->branch_id . ':' . $row->d;
            $doc = new PostingDocument($ref, $row->d, (int) $row->branch_id, 'Opening stock — branch #' . $row->branch_id . ' — ' . $row->d);
            $doc->debit('inventory', $row->value)->credit('opening_balance_equity', $row->value);
            yield $doc;
        }
    }
}
