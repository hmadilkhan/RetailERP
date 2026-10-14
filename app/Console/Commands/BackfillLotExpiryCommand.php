<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

// Phase 2.2: purane PO GRN lots ko PO line ka batch / expiry do (pehle sirf purchase_item_details pe tha).
// Lot → purchase_rec_details (GRN + item) → purchase_item_details. Sirf khali lots bharta hai — dobara chalana safe. --dry-run = sirf ginti.
class BackfillLotExpiryCommand extends Command
{
    protected $signature = 'stock:backfill-lot-expiry {--company= : Sirf is company ke lots} {--dry-run}';

    protected $description = 'Copy batch / expiry from purchase order lines to the stock lots received against them';

    public function handle(): int
    {
        if (!\Custom_Helper::hasLotExpiry()) {
            $this->error('inventory_stock.expiry_date missing — run migration 2026_10_14_120000 first.');
            return self::FAILURE;
        }

        $company = $this->option('company') ? (int) $this->option('company') : null;
        $rows = DB::table('inventory_stock as s')
            ->join('purchase_rec_details as r', fn ($j) => $j->on('r.GRN', '=', 's.grn_id')->on('r.item_id', '=', 's.product_id'))
            ->join('purchase_item_details as p', 'p.p_item_details_id', '=', 'r.po_rec_details_id')
            ->join('branch as b', 'b.branch_id', '=', 's.branch_id')
            ->when($company, fn ($q) => $q->where('b.company_id', $company))
            ->whereNull('s.expiry_date')->whereNull('s.batch_no')
            ->where(fn ($q) => $q->where('p.batch_no', '<>', '')->orWhereNotNull('p.expiry_date'))
            ->select('s.stock_id', 's.balance', 'b.company_id', 'p.batch_no', 'p.expiry_date')
            ->get();

        $updates = [];
        foreach ($rows as $row) {
            $fields = \Custom_Helper::lotExpiryFields($row->batch_no, $row->expiry_date);
            if ($fields) {
                $updates[$row->stock_id] = $fields + ['company_id' => $row->company_id, 'balance' => (float) $row->balance];
            }
        }

        $byCompany = collect($updates)->groupBy('company_id')->map(fn ($g) => sprintf('%d lots (%d with stock left)', $g->count(), $g->where('balance', '>', 0)->count()));
        foreach ($byCompany as $co => $text) {
            $this->line("  company {$co}: {$text}");
        }
        $this->info(count($updates) . ' lots to update');
        if ($this->option('dry-run')) {
            return self::SUCCESS;
        }

        // ek hi UPDATE … JOIN (har lot ki alag query remote DB pe minutes leti thi); shart wahi jo upar ginti me
        $affected = DB::update("
            UPDATE inventory_stock s
            JOIN purchase_rec_details r ON r.GRN = s.grn_id AND r.item_id = s.product_id
            JOIN purchase_item_details p ON p.p_item_details_id = r.po_rec_details_id
            JOIN branch b ON b.branch_id = s.branch_id
            SET s.batch_no = NULLIF(TRIM(p.batch_no), ''),
                s.expiry_date = CASE WHEN p.expiry_date > '1900-01-01' THEN p.expiry_date END
            WHERE s.expiry_date IS NULL AND s.batch_no IS NULL
              AND (COALESCE(TRIM(p.batch_no), '') <> '' OR p.expiry_date > '1900-01-01')" . ($company ? ' AND b.company_id = ' . $company : ''));
        $this->info("Done — {$affected} lots updated.");
        return self::SUCCESS;
    }
}
