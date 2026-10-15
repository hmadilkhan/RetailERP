<?php

namespace App\Services\Accounting\Posting\Posters;

use App\Services\Accounting\Posting\AccountResolver;
use App\Services\Accounting\Posting\Poster;
use App\Services\Accounting\Posting\PostingDocument;
use App\Services\Accounting\Posting\SourceQuery;
use Illuminate\Support\Facades\DB;

/**
 * vendor_ledger ki PO ke baghair (po_no = 0) rows jo payment nahi — har row ki entry (ref vendor_account_id):
 *   naya vendor opening balance (narration NULL, sirf credit)  → Dr Opening Balance Equity / Cr Accounts Payable
 *   manual adjustment screen — debit (payable kam)             → Dr Accounts Payable / Cr Adjustments Clearing
 *   manual adjustment screen — credit (payable zyada)          → Dr Adjustments Clearing / Cr Accounts Payable
 * Chhodi jati hain: vendor_payment_details wali rows (VendorPaymentPoster), "PO receipt adjustment" (GRN / return
 * GL me pehle se GrnPoster / PurchaseReturnPoster se aate hain), "Vendor Bill / Debit Note / Credit Note / Landed Cost …" (VendorBillPoster).
 * Tareekh: opening balance created_at DB default (UTC); manual adjustment created_at PHP likhta hai (user ki date, Pakistan).
 */
class VendorAdjustmentPoster implements Poster
{
    public function sourceType(): string
    {
        return 'vendor_adjustment';
    }

    public function label(): string
    {
        return 'Vendor Opening Balances & Adjustments';
    }

    public function documents(int $companyId, string $from, string $to, AccountResolver $accounts): iterable
    {
        $dateSql = "(CASE WHEN vl.narration IS NULL THEN " . SourceQuery::pkDate('vl.created_at') . " ELSE DATE(vl.created_at) END)";

        $rows = DB::select("
            SELECT vl.vendor_account_id AS id, {$dateSql} AS d, vl.debit, vl.credit, vl.narration, v.vendor_name
            FROM vendor_ledger vl
            JOIN vendors v ON v.id = vl.vendor_id
            WHERE v.user_id = ? AND (vl.po_no = 0 OR vl.po_no IS NULL)
              AND COALESCE(vl.narration, '') NOT LIKE 'PO receipt adjustment%'
              AND COALESCE(vl.narration, '') NOT LIKE 'Vendor Bill %' AND COALESCE(vl.narration, '') NOT LIKE 'Debit Note %'
              AND COALESCE(vl.narration, '') NOT LIKE 'Credit Note %'
              AND COALESCE(vl.narration, '') NOT LIKE 'Landed Cost %'
              AND NOT EXISTS (SELECT 1 FROM vendor_payment_details vpd WHERE vpd.account_id = vl.vendor_account_id)
              AND {$dateSql} BETWEEN ? AND ?", [$companyId, $from, $to]);

        foreach ($rows as $row) {
            $isOpening = $row->narration === null && (float) $row->debit == 0;
            $net = (float) $row->credit - (float) $row->debit; // + = payable badha

            $doc = new PostingDocument((string) $row->id, $row->d, null,
                ($isOpening ? 'Opening balance — ' : 'Vendor adjustment — ') . $row->vendor_name . ($row->narration ? ' — ' . $row->narration : ''),
                (int) $row->id);
            $doc->debit($isOpening ? 'opening_balance_equity' : 'adjustments_clearing', $net)->credit('accounts_payable', $net);
            yield $doc;
        }
    }
}
