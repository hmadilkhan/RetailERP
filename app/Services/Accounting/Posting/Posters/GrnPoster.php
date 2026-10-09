<?php

namespace App\Services\Accounting\Posting\Posters;

use App\Services\Accounting\Posting\AccountResolver;
use App\Services\Accounting\Posting\Poster;
use App\Services\Accounting\Posting\PostingDocument;
use App\Services\Accounting\Posting\SourceQuery;
use Illuminate\Support\Facades\DB;

/**
 * PO ka maal aaya (GRN) — PO + din ki ek entry (ref "po:date"):  Dr Inventory / Cr Accounts Payable.
 * Source: inventory_stock_report_table 'Stock Purchase through Purchase Order' (append-only; foreign_id = PO).
 * Vendor Bill (Phase 2.1) abhi nahi, isliye AP GRN pe hi book hota hai (GR/IR Clearing baad me).
 * Branch PO se (report row session branch rakhta hai, kabhi ghalat).
 */
class GrnPoster implements Poster
{
    public function __construct(private PurchaseValuation $valuation)
    {
    }

    public function sourceType(): string
    {
        return 'grn';
    }

    public function label(): string
    {
        return 'Goods Received (GRN)';
    }

    public function documents(int $companyId, string $from, string $to, AccountResolver $accounts): iterable
    {
        $branches = implode(',', SourceQuery::branchIds($companyId));
        $floor = SourceQuery::idFloor('inventory_stock_report_table', 'stock_report_id', 'date', $from);

        $rows = DB::select("
            SELECT r.foreign_id AS po, DATE(r.date) AS d, r.product_id, SUM(r.qty) AS qty
            FROM inventory_stock_report_table r
            JOIN purchase_general_details g ON g.purchase_id = r.foreign_id
            WHERE r.stock_report_id >= ? AND r.narration = 'Stock Purchase through Purchase Order'
              AND g.branch_id IN ({$branches}) AND DATE(r.date) BETWEEN ? AND ?
            GROUP BY r.foreign_id, DATE(r.date), r.product_id", [$floor, $from, $to]);

        $docs = [];
        foreach ($rows as $row) {
            $po = $this->valuation->forPo((int) $row->po);
            $ref = $row->po . ':' . $row->d;
            $doc = $docs[$ref] ??= new PostingDocument($ref, $row->d, $po['branch_id'], 'Goods received — PO #' . $row->po, (int) $row->po);
            $value = $this->valuation->value((int) $row->po, (int) $row->product_id, (float) $row->qty);
            $doc->debit('inventory', $value)->credit('accounts_payable', $value);
        }

        return array_values($docs);
    }
}
