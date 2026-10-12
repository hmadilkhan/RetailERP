<?php

namespace App\Services\Accounting\Posting\Posters;

use App\Services\Accounting\Posting\AccountResolver;
use App\Services\Accounting\Posting\Poster;
use App\Services\Accounting\Posting\PostingDocument;
use App\Services\Accounting\Posting\SourceQuery;
use Illuminate\Support\Facades\DB;

/**
 * Branch se branch stock transfer (delivery challan) — har challan ki DO entries, taake har branch ka inventory sahi rahe:
 *   "{DC}:out" bhejne wali branch:  Dr Inter-branch Clearing / Cr Inventory
 *   "{DC}:in"  lene wali branch:    Dr Inventory / Cr Inter-branch Clearing
 * Company level pe dono barabar — Inter-branch Clearing ka total 0 rehta hai.
 * Value = deliverd_qty × cost_price (source branch ki cost; kai rows me 0). Shipment charges ka payment source nahi — post nahi.
 */
class TransferPoster implements Poster
{
    public function sourceType(): string
    {
        return 'transfer';
    }

    public function label(): string
    {
        return 'Branch Transfers';
    }

    public function documents(int $companyId, string $from, string $to, AccountResolver $accounts): iterable
    {
        $branches = implode(',', SourceQuery::branchIds($companyId));

        $rows = DB::select("
            SELECT g.DC_id, g.DC_No, LEFT(g.date, 10) AS d, g.branch_from, g.branch_to,
                   SUM(i.deliverd_qty * COALESCE(CAST(NULLIF(i.cost_price, '') AS DECIMAL(18,4)), 0)) AS value
            FROM deliverychallan_general_details g
            JOIN deliverychallan_item_details i ON i.DC_Id = g.DC_id
            WHERE g.branch_from IN ({$branches}) AND LEFT(g.date, 10) BETWEEN ? AND ?
            GROUP BY g.DC_id, g.DC_No, d, g.branch_from, g.branch_to", [$from, $to]);

        foreach ($rows as $row) {
            $label = 'Transfer DC #' . $row->DC_No . ' — branch #' . $row->branch_from . ' → #' . $row->branch_to;

            $out = new PostingDocument($row->DC_id . ':out', $row->d, (int) $row->branch_from, $label . ' (out)', (int) $row->DC_id);
            $out->debit('inter_branch', $row->value)->credit('inventory', $row->value);
            yield $out;

            $in = new PostingDocument($row->DC_id . ':in', $row->d, (int) $row->branch_to, $label . ' (in)', (int) $row->DC_id);
            $in->debit('inventory', $row->value)->credit('inter_branch', $row->value);
            yield $in;
        }
    }
}
