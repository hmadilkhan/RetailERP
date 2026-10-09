<?php

namespace App\Services\Accounting\Posting\Posters;

use App\Services\Accounting\Posting\AccountResolver;
use App\Services\Accounting\Posting\Poster;
use App\Services\Accounting\Posting\PostingDocument;
use App\Services\Accounting\Posting\SourceQuery;
use Illuminate\Support\Facades\DB;

/**
 * POS sales — har branch ki har din ki EK summary entry (ref "branch:date").
 *   Dr Cash / Bank / AR / Customer Advance (payment mode ke hisaab se)   = total_amount
 *   Dr Sales Discounts                                                    = discount
 *   Cr Sales                                                              = gross − inclusive tax
 *   Cr Output Tax                                                         = sales_tax + srb
 *   Cr Other Income                                                       = service charge + delivery
 *   Dr COGS / Cr Inventory                                                = Σ sales_receipt_details.total_cost
 * Returns (web status 14 + POS sales_return) isi din ki entry me ulte.
 *
 * POS rows external webservice likhta hai (model events nahi chalte) — isliye roz ka total padh kar sync hota hai;
 * void / edit aaye to din ka hash badalta hai aur entry reverse + repost hoti hai.
 * Business date = sales_receipts.date (sales_opening.date nahi — shifts dino tak khuli rehti hain).
 * Tax inclusive ya exclusive ka setting nahi — har receipt se pehchaana: total = gross − discount + charges ho
 * aur tax > 0 to tax total ke andar hai. Source ka farq (kharab rows) suspense me jata hai.
 */
class SalesPoster implements Poster
{
    /** sales_payment.payment_id → system account. account_mappings (payment_mode) se badla ja sakta hai. */
    const PAYMENT_ACCOUNTS = [
        1 => 'cash',                 // Cash
        2 => 'bank',                 // Card
        3 => 'accounts_receivable',  // Customer Credit
        4 => 'bank',                 // Cheque
        5 => 'cash',                 // COD — rider cash laata hai
        6 => 'customer_advance',     // Pre-Payment / Estimate — advance se kata
        7 => 'accounts_receivable',  // Account
        8 => 'bank',                 // Online / wallet
        9 => 'bank',                 // JazzCash
        10 => 'bank',                // EasyPaisa
    ];

    public function sourceType(): string
    {
        return 'pos_sales';
    }

    public function label(): string
    {
        return 'POS Sales (daily summary)';
    }

    public function documents(int $companyId, string $from, string $to, AccountResolver $accounts): iterable
    {
        $docs = [];
        $doc = function ($branch, $date) use (&$docs) {
            $ref = $branch . ':' . $date;
            return $docs[$ref] ??= new PostingDocument($ref, $date, (int) $branch, 'POS sales — branch #' . $branch . ' — ' . $date);
        };
        $payAccount = fn ($paymentId) => $accounts->mapped('payment_mode', (int) $paymentId, self::PAYMENT_ACCOUNTS[(int) $paymentId] ?? 'cash');

        // branch IN (...) + date → sales_receipts ka (branch, date, status) index lagta hai
        $branches = implode(',', SourceQuery::branchIds($companyId));

        // sales (status 4) aur web returns (status 14) — dono same columns
        $rows = DB::select("
            SELECT r.branch, r.date, r.payment_id, r.status,
                SUM(r.actual_amount + 0) AS gross,
                SUM(r.total_amount + 0) AS net,
                SUM(COALESCE(s.discount_amount, 0)) AS discount,
                SUM(COALESCE(s.sales_tax_amount, 0) + COALESCE(s.srb + 0, 0)) AS tax,
                SUM(COALESCE(s.service_tax_amount, 0) + COALESCE(s.delivery_charges_amount, 0)) AS charges,
                SUM(CASE WHEN (COALESCE(s.sales_tax_amount, 0) + COALESCE(s.srb + 0, 0)) > 0
                          AND ABS((r.total_amount + 0) - ((r.actual_amount + 0) - COALESCE(s.discount_amount, 0)
                                  + COALESCE(s.service_tax_amount, 0) + COALESCE(s.delivery_charges_amount, 0))) < 1
                         THEN COALESCE(s.sales_tax_amount, 0) + COALESCE(s.srb + 0, 0) ELSE 0 END) AS inclusive_tax
            FROM sales_receipts r
            LEFT JOIN sales_account_subdetails s ON s.receipt_id = r.id
            WHERE r.branch IN ({$branches}) AND r.date BETWEEN ? AND ?
              AND ((r.status = 4 AND r.void_receipt = 0) OR (r.status = 14 AND r.is_sale_return = 1))
            GROUP BY r.branch, r.date, r.payment_id, r.status", [$from, $to]);

        foreach ($rows as $row) {
            $d = $doc($row->branch, $row->date);

            if ((int) $row->status === 14) {
                // web return (SalesReturnDuplicateService): total = gross, tax / discount copy nahi hote
                $d->debit('sales_returns', $row->gross, 'Sales return');
                $d->credit($payAccount($row->payment_id), $row->net, 'Sales return refund');
                continue;
            }

            $d->debit($payAccount($row->payment_id), $row->net);
            $d->debit('sales_discounts', $row->discount);
            $d->credit('sales', $row->gross - $row->inclusive_tax);
            $d->credit('output_tax', $row->tax);
            $d->credit('other_income', $row->charges, 'Service / delivery charges');
        }

        // COGS — sale pe Dr COGS / Cr Inventory, web return pe ulta
        $cogs = DB::select("
            SELECT r.branch, r.date, r.status, SUM(d.total_cost + 0) AS cost
            FROM sales_receipts r
            JOIN sales_receipt_details d ON d.receipt_id = r.id
            WHERE r.branch IN ({$branches}) AND r.date BETWEEN ? AND ?
              AND ((r.status = 4 AND r.void_receipt = 0) OR (r.status = 14 AND r.is_sale_return = 1))
            GROUP BY r.branch, r.date, r.status", [$from, $to]);

        foreach ($cogs as $row) {
            $cost = (float) $row->cost * ((int) $row->status === 14 ? -1 : 1);
            $doc($row->branch, $row->date)->debit('cogs', $cost)->credit('inventory', $cost);
        }

        // POS sales_return (purana tareeqa, refund cash me; cost record nahi hoti)
        $returns = DB::select("
            SELECT t.branch_id AS branch, DATE(sr.timestamp) AS date, SUM(sr.amount + 0) AS amount
            FROM sales_return sr
            JOIN sales_opening so ON so.opening_id = sr.opening_id
            JOIN terminal_details t ON t.terminal_id = so.terminal_id
            JOIN branch b ON b.branch_id = t.branch_id
            WHERE b.company_id = ? AND DATE(sr.timestamp) BETWEEN ? AND ?
            GROUP BY t.branch_id, DATE(sr.timestamp)", [$companyId, $from, $to]);

        foreach ($returns as $row) {
            $doc($row->branch, $row->date)->debit('sales_returns', $row->amount, 'POS return')->credit('cash', $row->amount, 'POS return refund');
        }

        return array_values($docs);
    }
}
