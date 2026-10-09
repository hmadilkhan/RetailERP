<?php

namespace App\Services\Accounting\Posting\Posters;

use App\Services\Accounting\Posting\AccountResolver;
use App\Services\Accounting\Posting\Poster;
use App\Services\Accounting\Posting\PostingDocument;
use App\Services\Accounting\Posting\SourceQuery;
use Illuminate\Support\Facades\DB;

/**
 * Stock adjustment — branch + din ki ek entry (ref "branch:date"):
 *   kami (adjustment_mode '0')   → Dr Inventory Gain / Loss, Cr Inventory
 *   izafa (adjustment_mode '1')  → Dr Inventory,             Cr Inventory Gain / Loss
 * Source: inventory_stock_report_table narration '(Stock Adjustment)%'. Sign adjustment_mode se (purani rows me qty
 * positive bhi hai). Cost = lot ki cost_price (foreign_id = stock_id), warna report ki cost.
 * POS void restock ('Stock Return', sale price pe) yahan NAHI — void sale SalesPoster me pehle se shamil hi nahi hoti.
 * Note: kuch users naya maal positive adjustment se daalte hain — wo yahan "gain" ban kar aata hai.
 */
class StockAdjustmentPoster implements Poster
{
    public function sourceType(): string
    {
        return 'stock_adjustment';
    }

    public function label(): string
    {
        return 'Stock Adjustments';
    }

    public function documents(int $companyId, string $from, string $to, AccountResolver $accounts): iterable
    {
        $branches = implode(',', SourceQuery::branchIds($companyId));
        $floor = SourceQuery::idFloor('inventory_stock_report_table', 'stock_report_id', 'date', $from);

        $rows = DB::select("
            SELECT r.branch_id, DATE(r.date) AS d,
                SUM(CASE WHEN r.adjustment_mode = '0' THEN -1 ELSE 1 END
                    * ABS(r.qty) * COALESCE(CAST(NULLIF(s.cost_price, '') AS DECIMAL(18,4)), r.cost)) AS value
            FROM inventory_stock_report_table r
            LEFT JOIN inventory_stock s ON s.stock_id = r.foreign_id
            WHERE r.stock_report_id >= ? AND r.narration LIKE '(Stock Adjustment)%'
              AND r.branch_id IN ({$branches}) AND DATE(r.date) BETWEEN ? AND ?
            GROUP BY r.branch_id, DATE(r.date)", [$floor, $from, $to]);

        foreach ($rows as $row) {
            $ref = $row->branch_id . ':' . $row->d;
            $doc = new PostingDocument($ref, $row->d, (int) $row->branch_id, 'Stock adjustments — branch #' . $row->branch_id . ' — ' . $row->d);
            // value positive = izafa; negative ho to PostingDocument / engine side ulat deta hai
            $doc->debit('inventory', $row->value)->credit('inventory_adjustment', $row->value);
            yield $doc;
        }
    }
}
