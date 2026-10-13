<?php

namespace App\Services\Accounting\Posting\Posters;

use App\Services\Accounting\Posting\AccountResolver;
use App\Services\Accounting\Posting\Poster;
use App\Services\Accounting\Posting\PostingDocument;
use App\Services\Accounting\Posting\SourceQuery;
use Illuminate\Support\Facades\DB;

/**
 * Vendor ko maal wapas (har mode — Replacement / Complete / Partial) — har return row ki entry (ref pr_item_details_id):
 *   Dr Accounts Payable / Cr Inventory, value = qty × PO unit price × ratio.
 * Replacement ka naya maal GRN se aaye to GrnPoster dobara Inventory / AP karta hai — vendor ledger sync jaisa.
 * purchase_return_itemdetails me date nahi — us PO + item ki pehli 'Stock Return' report row (adjustment_mode NULL) ki date.
 * Wo row na ho (purana data) to PO ki aakhri GRN report row, warna PO ki date — narration me "date estimated".
 * status 0 = replacement ka maal aa gaya (return phir bhi hua tha) — isliye status filter nahi.
 */
class PurchaseReturnPoster implements Poster
{
    public function __construct(private PurchaseValuation $valuation)
    {
    }

    public function sourceType(): string
    {
        return 'purchase_return';
    }

    public function label(): string
    {
        return 'Purchase Returns';
    }

    public function documents(int $companyId, string $from, string $to, AccountResolver $accounts): iterable
    {
        $branches = implode(',', SourceQuery::branchIds($companyId));

        $rows = DB::select("
            SELECT pr.pr_item_details_id AS id, pr.purchase_id AS po, pr.item_code, pr.quantity,
                (SELECT DATE(MIN(r.date)) FROM inventory_stock_report_table r
                  WHERE r.foreign_id = pr.purchase_id AND r.narration = 'Stock Return'
                    AND r.product_id = pr.item_code AND r.adjustment_mode IS NULL) AS d,
                (SELECT DATE(MAX(r.date)) FROM inventory_stock_report_table r
                  WHERE r.foreign_id = pr.purchase_id AND r.narration = 'Stock Purchase through Purchase Order') AS grn_date,
                g.date AS po_date
            FROM purchase_return_itemdetails pr
            JOIN purchase_general_details g ON g.purchase_id = pr.purchase_id
            WHERE g.branch_id IN ({$branches})");

        foreach ($rows as $row) {
            $estimated = !$row->d;
            $date = $row->d ?: ($row->grn_date ?: (preg_match('/^\d{4}-\d{2}-\d{2}/', (string) $row->po_date) ? substr($row->po_date, 0, 10) : null));
            if (!$date || $date < $from || $date > $to) {
                continue;
            }
            $po = $this->valuation->forPo((int) $row->po);
            $value = $this->valuation->value((int) $row->po, (int) $row->item_code, (float) $row->quantity);
            $doc = new PostingDocument((string) $row->id, $date, $po['branch_id'],
                'Purchase return — PO #' . $row->po . ($estimated ? ' (date estimated)' : ''), (int) $row->po);
            $doc->debit('accounts_payable', $value)->credit('inventory', $value);
            yield $doc;
        }
    }
}
