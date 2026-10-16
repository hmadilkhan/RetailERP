<?php

namespace App\Services\Purchases;

use App\Models\VendorBill;
use App\Models\VendorBillLine;
use Illuminate\Support\Facades\DB;
use RuntimeException;

/**
 * Phase 2.1: Vendor Bill (purchase invoice) — PO ke baghair. Clients maal stock adjustment / CSV se chadhate the
 * (2025-26: 5 PO GRN vs 1,538 adjustments), vendor ka udhaar record hi nahi hota tha. Bill ek hi jagah:
 *   stock lot (inventory_stock) + stock report + POS sync, vendor_ledger credit, GL (VendorBillPoster).
 *   Debit Note = maal wapas (lot se kam, vendor_ledger debit), Credit Note = vendor ka rebate (sirf vendor_ledger debit).
 *   Pay = vendor_ledger debit + vendor_payment(_details) + cash_ledger / bank_deposit_details (purani payment screen jaisa,
 *   isliye VendorPaymentPoster / Cash / Bank Book posters khud sahi chalte hain).
 * Stock report ki narration "(Stock Adjustment) Vendor Bill …" / "(Stock Adjustment) Debit Note …" — purani stock reports
 * aur ValuationService inhe adjustment ki tarah ginte hain (IN / OUT); StockAdjustmentPoster inhe chhodta hai (GL VendorBillPoster se).
 * vendor_ledger / cash_ledger / bank_deposit_details ke amounts int hain — round ho kar jate hain.
 * PO se jodna (three-way match) abhi nahi: PO ka purana flow Final Submit pe hi vendor_ledger credit karta hai.
 */
class VendorBillService
{
    const STOCK_IN = '(Stock Adjustment) Vendor Bill ';
    const STOCK_OUT = '(Stock Adjustment) Debit Note ';

    /**
     * Draft bill banao / badlo. $data: branch_id, vendor_id, vendor_ref, bill_date, due_date, other_charges, narration,
     * lines: [[product_id, qty, unit_price, discount, tax_rate], ...]
     */
    public function saveDraft(int $companyId, array $data, ?int $id = null, $userId = null): VendorBill
    {
        $this->assertVendor($companyId, (int) $data['vendor_id']);
        $this->assertBranch($companyId, (int) $data['branch_id']);
        $lines = $this->buildLines($companyId, $data['lines'] ?? [], (float) ($data['other_charges'] ?? 0));
        if (!$lines) {
            throw new RuntimeException('Add at least one item.');
        }

        return DB::transaction(function () use ($companyId, $data, $id, $userId, $lines) {
            $bill = $id ? VendorBill::where('company_id', $companyId)->lockForUpdate()->findOrFail($id) : new VendorBill();
            if ($bill->exists && $bill->status !== 'draft') {
                throw new RuntimeException('Only a draft bill can be edited.');
            }

            $bill->fill([
                'company_id' => $companyId,
                'branch_id' => (int) $data['branch_id'],
                'vendor_id' => (int) $data['vendor_id'],
                'type' => 'bill',
                'vendor_ref' => trim((string) ($data['vendor_ref'] ?? '')) ?: null,
                'bill_date' => $data['bill_date'],
                'due_date' => ($data['due_date'] ?? null) ?: null,
                'other_charges' => round((float) ($data['other_charges'] ?? 0), 2),
                'narration' => trim((string) ($data['narration'] ?? '')) ?: null,
                'status' => 'draft',
            ] + $this->totals($lines, (float) ($data['other_charges'] ?? 0)));
            if (!$bill->exists) {
                $bill->doc_no = $this->nextDocNo($companyId, 'bill');
                $bill->created_by = $userId;
            }
            $bill->save();

            $bill->lines()->delete();
            $bill->lines()->createMany($lines);
            return $bill->fresh('lines');
        });
    }

    /** Draft → posted: stock lot, stock report, POS sync, vendor_ledger credit. */
    public function post(int $companyId, int $id, $userId = null): VendorBill
    {
        return DB::transaction(function () use ($companyId, $id, $userId) {
            $bill = VendorBill::where('company_id', $companyId)->where('type', 'bill')->lockForUpdate()->findOrFail($id);
            if ($bill->status !== 'draft') {
                throw new RuntimeException('This bill is already ' . $bill->status . '.');
            }
            $this->assertBranch($companyId, (int) $bill->branch_id);
            $lines = $bill->lines()->get();
            if ($lines->isEmpty()) {
                throw new RuntimeException('Add at least one item.');
            }

            $grnId = $this->createGrn($userId);
            $when = $bill->bill_date->toDateString() . ' ' . date('H:i:s');
            $label = $bill->doc_no . ($bill->vendor_ref ? ' (Bill# ' . $bill->vendor_ref . ')' : '');

            foreach ($lines as $line) {
                $prices = DB::table('inventory_price')->where('product_id', $line->product_id)->where('status_id', 1)->orderByDesc('price_id')->first();
                $uom = DB::table('inventory_general')->where('id', $line->product_id)->value('uom_id');
                $stockId = DB::table('inventory_stock')->insertGetId([
                    'grn_id' => $grnId,
                    'product_id' => $line->product_id,
                    'uom' => $uom,
                    'cost_price' => round((float) $line->landed_cost, 2),
                    'retail_price' => $prices->retail_price ?? '0.00',
                    'wholesale_price' => $prices->wholesale_price ?? '0.00',
                    'discount_price' => $prices->discount_price ?? '0.00',
                    'qty' => $line->qty,
                    'balance' => $line->qty,
                    'status_id' => 1,
                    'branch_id' => $bill->branch_id,
                    'date' => $bill->bill_date->toDateString(),
                ] + \Custom_Helper::lotExpiryFields($line->batch_no, $line->expiry_date));
                $line->update(['stock_id' => $stockId]);
                $this->stockReport((int) $line->product_id, $stockId, (int) $bill->branch_id, (float) $line->qty, (float) $line->landed_cost,
                    $prices->retail_price ?? '0.00', self::STOCK_IN . $label, '1', $when);
                $this->notifyTerminals((int) $bill->branch_id, (int) $line->product_id);
            }

            $this->ledger((int) $bill->vendor_id, 0, (int) round($bill->total), 'Vendor Bill ' . $label, $when);
            $bill->update(['status' => 'posted', 'grn_id' => $grnId, 'posted_by' => $userId, 'posted_at' => now()]);
            return $bill->fresh('lines');
        });
    }

    /** Posted bill wapas: sirf jab koi payment / note nahi aur maal ab tak bika / hila nahi. */
    public function cancel(int $companyId, int $id, $userId = null): VendorBill
    {
        return DB::transaction(function () use ($companyId, $id) {
            $bill = VendorBill::where('company_id', $companyId)->where('type', 'bill')->lockForUpdate()->findOrFail($id);
            if ($bill->status === 'draft') {
                $bill->lines()->delete();
                $bill->delete();
                return $bill;
            }
            if ($bill->status !== 'posted') {
                throw new RuntimeException('This bill is already ' . $bill->status . '.');
            }
            if ((float) $bill->paid > 0 || $bill->notes()->where('status', 'posted')->exists()) {
                throw new RuntimeException('This bill has payments or notes. Use a Debit Note to return goods instead.');
            }

            $lines = $bill->lines()->get();
            foreach ($lines as $line) {
                $lot = DB::table('inventory_stock')->where('stock_id', $line->stock_id)->lockForUpdate()->first();
                if (!$lot || (float) $lot->balance + 0.0001 < (float) $line->qty) {
                    throw new RuntimeException('Stock from this bill has already been sold or moved. Use a Debit Note to return what is left.');
                }
            }

            $when = date('Y-m-d H:i:s');
            foreach ($lines as $line) {
                DB::table('inventory_stock')->where('stock_id', $line->stock_id)->update(['balance' => 0, 'status_id' => 2]);
                $this->stockReport((int) $line->product_id, (int) $line->stock_id, (int) $bill->branch_id, -(float) $line->qty, (float) $line->landed_cost,
                    '0.00', self::STOCK_IN . $bill->doc_no . ' cancelled', '0', $when);
                $this->notifyTerminals((int) $bill->branch_id, (int) $line->product_id);
            }
            $this->ledger((int) $bill->vendor_id, (int) round($bill->total), 0, 'Vendor Bill ' . $bill->doc_no . ' cancelled', $when);
            $bill->update(['status' => 'cancelled', 'cancelled_at' => now()]);
            return $bill;
        });
    }

    /**
     * Debit Note — maal vendor ko wapas. $returns: [bill_line_id => qty]. Amount = bill line (amount + tax) × qty / bill qty.
     * Stock pehle bill ke lot se, kam ho to usi branch ke doosre lots (purane pehle) se.
     */
    public function debitNote(int $companyId, int $billId, array $returns, string $date, ?string $narration = null, $userId = null): VendorBill
    {
        return DB::transaction(function () use ($companyId, $billId, $returns, $date, $narration, $userId) {
            $bill = VendorBill::where('company_id', $companyId)->where('type', 'bill')->where('status', 'posted')->lockForUpdate()->findOrFail($billId);
            $returned = $this->returnedQty($bill->id);

            $lines = [];
            foreach ($bill->lines()->get() as $line) {
                $qty = round((float) ($returns[$line->id] ?? 0), 4);
                if ($qty <= 0) {
                    continue;
                }
                $left = (float) $line->qty - ($returned[$line->id] ?? 0);
                if ($qty > $left + 0.0001) {
                    throw new RuntimeException('Return quantity is more than what is left on the bill (' . $this->num($left) . ').');
                }
                $share = $qty / (float) $line->qty;
                $lines[] = [
                    'source' => $line,
                    'product_id' => $line->product_id,
                    'against_line_id' => $line->id,
                    'qty' => $qty,
                    'unit_price' => $line->unit_price,
                    'discount' => round((float) $line->discount * $share, 2),
                    'tax_rate' => $line->tax_rate,
                    'tax_amount' => round((float) $line->tax_amount * $share, 2),
                    'amount' => round((float) $line->amount * $share, 2),
                    'landed_cost' => $line->landed_cost,
                ];
            }
            if (!$lines) {
                throw new RuntimeException('Enter a quantity to return.');
            }

            $subtotal = array_sum(array_map(fn ($l) => (float) $l['qty'] * (float) $l['unit_price'], $lines));
            $amount = array_sum(array_column($lines, 'amount'));
            $tax = array_sum(array_column($lines, 'tax_amount'));
            $note = VendorBill::create([
                'company_id' => $companyId,
                'branch_id' => $bill->branch_id,
                'vendor_id' => $bill->vendor_id,
                'type' => 'debit_note',
                'doc_no' => $this->nextDocNo($companyId, 'debit_note'),
                'vendor_ref' => $bill->vendor_ref,
                'bill_date' => $date,
                'against_bill_id' => $bill->id,
                'subtotal' => round($subtotal, 2),
                'discount' => round($subtotal - $amount, 2),
                'tax_amount' => round($tax, 2),
                'total' => round($amount + $tax, 2),
                'status' => 'posted',
                'narration' => $narration ?: null,
                'created_by' => $userId,
                'posted_by' => $userId,
                'posted_at' => now(),
            ]);

            $when = $date . ' ' . date('H:i:s');
            foreach ($lines as $l) {
                $source = $l['source'];
                unset($l['source']);
                $this->takeStock((int) $note->branch_id, (int) $source->product_id, (float) $l['qty'], $source->stock_id ? (int) $source->stock_id : null,
                    (float) $source->landed_cost, self::STOCK_OUT . $note->doc_no . ' (' . $bill->doc_no . ')', $when);
                $note->lines()->create($l);
                $this->notifyTerminals((int) $note->branch_id, (int) $source->product_id);
            }

            $this->ledger((int) $bill->vendor_id, (int) round($note->total), 0, 'Debit Note ' . $note->doc_no . ' against ' . $bill->doc_no, $when);
            $bill->update(['paid' => round((float) $bill->paid + (float) $note->total, 2)]);
            return $note->fresh('lines');
        });
    }

    /** Credit Note — vendor ka rebate / qeemat ki kami (maal nahi). Bill ke khule amount tak. */
    public function creditNote(int $companyId, int $billId, float $amount, string $date, ?string $narration = null, $userId = null): VendorBill
    {
        return DB::transaction(function () use ($companyId, $billId, $amount, $date, $narration, $userId) {
            $bill = VendorBill::where('company_id', $companyId)->where('type', 'bill')->where('status', 'posted')->lockForUpdate()->findOrFail($billId);
            $amount = round($amount, 2);
            if ($amount <= 0 || $amount > $bill->open_amount + 0.005) {
                throw new RuntimeException('Credit note amount must be between 0 and the open amount (' . $this->num($bill->open_amount) . ').');
            }

            $note = VendorBill::create([
                'company_id' => $companyId,
                'branch_id' => $bill->branch_id,
                'vendor_id' => $bill->vendor_id,
                'type' => 'credit_note',
                'doc_no' => $this->nextDocNo($companyId, 'credit_note'),
                'vendor_ref' => $bill->vendor_ref,
                'bill_date' => $date,
                'against_bill_id' => $bill->id,
                'subtotal' => $amount,
                'total' => $amount,
                'status' => 'posted',
                'narration' => $narration ?: null,
                'created_by' => $userId,
                'posted_by' => $userId,
                'posted_at' => now(),
            ]);
            $note->lines()->create(['description' => $narration ?: 'Credit note', 'qty' => 1, 'unit_price' => $amount, 'amount' => $amount]);

            $this->ledger((int) $bill->vendor_id, (int) round($amount), 0, 'Credit Note ' . $note->doc_no . ' against ' . $bill->doc_no, $date . ' ' . date('H:i:s'));
            $bill->update(['paid' => round((float) $bill->paid + $amount, 2)]);
            return $note;
        });
    }

    /**
     * Phase 2.5 — Landed Cost: bill post hone ke baad aane wala freight / duty / clearing. Bill ki lines pe value ya qty ke
     * hisaab se baant kar lots ki cost_price badhti hai (ValuationService agle run me average + COGS khud theek karta hai),
     * payee vendor (transporter ya wahi vendor) ke ledger me credit, GL: Dr Inventory / Cr AP. Pay se payment.
     */
    public function landedCost(int $companyId, int $billId, float $amount, string $method, int $payeeVendorId, string $date,
                               ?string $reference = null, ?string $narration = null, $userId = null): VendorBill
    {
        $this->assertVendor($companyId, $payeeVendorId);
        $amount = round($amount, 2);
        if ($amount <= 0) {
            throw new RuntimeException('Enter the landed cost amount.');
        }
        if (!in_array($method, ['value', 'qty'], true)) {
            throw new RuntimeException('Choose how to spread the cost.');
        }

        return DB::transaction(function () use ($companyId, $billId, $amount, $method, $payeeVendorId, $date, $reference, $narration, $userId) {
            $bill = VendorBill::where('company_id', $companyId)->where('type', 'bill')->where('status', 'posted')->lockForUpdate()->findOrFail($billId);
            $lines = $bill->lines()->whereNotNull('stock_id')->get();
            $base = $lines->sum(fn ($l) => $method === 'qty' ? (float) $l->qty : (float) $l->amount);
            if ($lines->isEmpty() || $base <= 0) {
                throw new RuntimeException('This bill has no items to spread the cost over.');
            }

            $doc = VendorBill::create([
                'company_id' => $companyId,
                'branch_id' => $bill->branch_id,
                'vendor_id' => $payeeVendorId,
                'type' => 'landed_cost',
                'doc_no' => $this->nextDocNo($companyId, 'landed_cost'),
                'vendor_ref' => trim((string) $reference) ?: null,
                'bill_date' => $date,
                'against_bill_id' => $bill->id,
                'subtotal' => $amount,
                'total' => $amount,
                'status' => 'posted',
                'narration' => $narration ?: null,
                'created_by' => $userId,
                'posted_by' => $userId,
                'posted_at' => now(),
            ]);

            $left = $amount;
            foreach ($lines->values() as $i => $line) {
                $share = $i === $lines->count() - 1 ? $left : round($amount * ($method === 'qty' ? (float) $line->qty : (float) $line->amount) / $base, 2);
                $left = round($left - $share, 2);
                $perUnit = (float) $line->qty > 0 ? $share / (float) $line->qty : 0;
                $doc->lines()->create([
                    'product_id' => $line->product_id,
                    'against_line_id' => $line->id,
                    'qty' => $line->qty,
                    'unit_price' => round($perUnit, 4),
                    'amount' => $share,
                    'landed_cost' => round($perUnit, 4),
                ]);
                DB::table('inventory_stock')->where('stock_id', $line->stock_id)
                    ->update(['cost_price' => DB::raw('ROUND(cost_price + ' . round($perUnit, 4) . ', 4)')]);
            }

            $label = $doc->doc_no . ' on ' . $bill->doc_no . ($doc->vendor_ref ? ' (Bill# ' . $doc->vendor_ref . ')' : '');
            $this->ledger($payeeVendorId, 0, (int) round($amount), 'Landed Cost ' . $label, $date . ' ' . date('H:i:s'));
            return $doc->fresh('lines');
        });
    }

    /** Bill / Landed Cost ki payment — cash (branch ka cash_ledger) ya bank (bank_deposit_details). */
    public function pay(int $companyId, int $billId, float $amount, string $mode, ?int $bankAccountId, ?string $reference, $branchId = null): int
    {
        return DB::transaction(function () use ($companyId, $billId, $amount, $mode, $bankAccountId, $reference, $branchId) {
            $bill = VendorBill::where('company_id', $companyId)->whereIn('type', VendorBill::PAYABLE)->where('status', 'posted')->lockForUpdate()->findOrFail($billId);
            $amount = (int) round($amount);
            if ($amount <= 0 || $amount > round($bill->open_amount) ) {
                throw new RuntimeException('Payment must be between 1 and the open amount (' . $this->num($bill->open_amount) . ').');
            }
            if ($mode === 'bank') {
                $ok = DB::table('bank_account_generaldetails')->where('bank_account_id', $bankAccountId)
                    ->whereIn('branch_id_company', $this->branchIds($companyId))->exists();
                if (!$ok) {
                    throw new RuntimeException('Select a bank account.');
                }
            }

            $vendor = DB::table('vendors')->where('id', $bill->vendor_id)->value('vendor_name');
            $narration = 'Payment to Vendor ' . $vendor . ' — ' . $bill->doc_no . ($bill->vendor_ref ? ' (Bill# ' . $bill->vendor_ref . ')' : '');
            $ledgerId = $this->ledger((int) $bill->vendor_id, $amount, 0, $narration, null);

            $paymentId = DB::table('vendor_payment')->insertGetId([
                'bankid' => $mode === 'bank' ? $bankAccountId : 0,
                'cheque' => '',
                'payment' => $amount,
                'narration' => $narration,
            ]);
            // BankBookPoster vendor cheque ko vendor_payment ke same bank + cheque se pehchaan kar chhodta hai — khali na ho
            $cheque = $mode === 'bank' ? (trim((string) $reference) ?: $bill->doc_no . '/P' . $paymentId) : '0';
            DB::table('vendor_payment')->where('payment_id', $paymentId)->update(['cheque' => $cheque]);
            DB::table('vendor_payment_details')->insert(['payment_id' => $paymentId, 'account_id' => $ledgerId]);

            if ($mode === 'bank') {
                $last = DB::table('bank_deposit_details')->where('bank_account_id', $bankAccountId)->orderByDesc('bank_deposit_id')->lockForUpdate()->first();
                DB::table('bank_deposit_details')->insert([
                    'bank_account_id' => $bankAccountId,
                    'cheque_number' => $cheque,
                    'cheque_date' => date('Y-m-d'),
                    'debit' => $amount,
                    'credit' => 0,
                    'balance' => ($last->balance ?? 0) - $amount,
                    'narration' => $narration,
                    'mode' => 'Cheque',
                ]);
            } else {
                $branch = $branchId ?: $bill->branch_id;
                $last = DB::table('cash_ledger')->where('branch_id', $branch)->orderByDesc('id')->lockForUpdate()->first();
                DB::table('cash_ledger')->insert([
                    'branch_id' => $branch,
                    'date' => date('Y-m-d'),
                    'debit' => $amount,
                    'credit' => 0,
                    'balance' => ($last->balance ?? 0) - $amount,
                    'narration' => $narration,
                ]);
            }

            $bill->update(['paid' => round((float) $bill->paid + $amount, 2)]);
            return $paymentId;
        });
    }

    /** bill line id => ab tak wapas gayi qty (posted debit notes) */
    public function returnedQty(int $billId): array
    {
        return VendorBillLine::query()
            ->join('vendor_bills', 'vendor_bills.id', '=', 'vendor_bill_lines.vendor_bill_id')
            ->where('vendor_bills.against_bill_id', $billId)->where('vendor_bills.type', 'debit_note')->where('vendor_bills.status', 'posted')
            ->groupBy('vendor_bill_lines.against_line_id')
            ->selectRaw('vendor_bill_lines.against_line_id AS line_id, SUM(vendor_bill_lines.qty) AS qty')
            ->pluck('qty', 'line_id')->map(fn ($q) => (float) $q)->all();
    }

    /** Lines ka hisaab: amount = qty × price − discount, tax = amount × rate%, landed = (amount + charges ka hissa) / qty */
    public function buildLines(int $companyId, array $rows, float $charges): array
    {
        $lines = [];
        foreach ($rows as $row) {
            $product = (int) ($row['product_id'] ?? 0);
            $qty = round((float) ($row['qty'] ?? 0), 4);
            if (!$product && $qty == 0) {
                continue; // khali row
            }
            if (!$product || !DB::table('inventory_general')->where('id', $product)->where('company_id', $companyId)->exists()) {
                throw new RuntimeException('Select a product on every line.');
            }
            $price = round((float) ($row['unit_price'] ?? 0), 4);
            $discount = round((float) ($row['discount'] ?? 0), 2);
            $rate = round((float) ($row['tax_rate'] ?? 0), 4);
            if ($qty <= 0 || $price < 0 || $discount < 0 || $rate < 0) {
                throw new RuntimeException('Quantity must be more than 0; price, discount and tax cannot be negative.');
            }
            $amount = round($qty * $price - $discount, 2);
            if ($amount < 0) {
                throw new RuntimeException('Discount cannot be more than the line amount.');
            }
            $lot = \Custom_Helper::lotExpiryFields($row['batch_no'] ?? null, $row['expiry_date'] ?? null);
            $lines[] = [
                'product_id' => $product,
                'qty' => $qty,
                'unit_price' => $price,
                'discount' => $discount,
                'tax_rate' => $rate,
                'tax_amount' => round($amount * $rate / 100, 2),
                'amount' => $amount,
                'batch_no' => $lot['batch_no'] ?? null,
                'expiry_date' => $lot['expiry_date'] ?? null,
            ];
        }

        $base = array_sum(array_column($lines, 'amount'));
        foreach ($lines as &$line) {
            $share = $base > 0 ? $charges * $line['amount'] / $base : ($lines ? $charges / count($lines) : 0);
            $line['landed_cost'] = round(($line['amount'] + $share) / $line['qty'], 4);
        }
        return $lines;
    }

    public function totals(array $lines, float $charges): array
    {
        $subtotal = array_sum(array_map(fn ($l) => $l['qty'] * $l['unit_price'], $lines));
        $discount = array_sum(array_column($lines, 'discount'));
        $tax = array_sum(array_column($lines, 'tax_amount'));
        return [
            'subtotal' => round($subtotal, 2),
            'discount' => round($discount, 2),
            'tax_amount' => round($tax, 2),
            'total' => round($subtotal - $discount + $tax + $charges, 2),
        ];
    }

    public function nextDocNo(int $companyId, string $type): string
    {
        $prefix = VendorBill::PREFIX[$type];
        $last = VendorBill::where('company_id', $companyId)->where('type', $type)->lockForUpdate()->orderByDesc('id')->value('doc_no');
        $n = $last ? (int) substr($last, strlen($prefix) + 1) : 0;
        return $prefix . '-' . str_pad((string) ($n + 1), 6, '0', STR_PAD_LEFT);
    }

    public function branchIds(int $companyId): array
    {
        return DB::table('branch')->where('company_id', $companyId)->pluck('branch_id')->map(fn ($b) => (int) $b)->all();
    }

    /** vendor_ledger row — balance = pichla + debit − credit (minus = payable) */
    public function ledger(int $vendorId, int $debit, int $credit, string $narration, ?string $when): int
    {
        $last = DB::table('vendor_ledger')->where('vendor_id', $vendorId)->orderByDesc('vendor_account_id')->lockForUpdate()->value('balance');
        $row = [
            'vendor_id' => $vendorId,
            'po_no' => 0,
            'total_amount' => $credit,
            'debit' => $debit,
            'credit' => $credit,
            'balance' => (int) ($last ?? 0) + $debit - $credit,
            'narration' => $narration,
        ];
        if ($when) {
            $row['created_at'] = $when;
        }
        return DB::table('vendor_ledger')->insertGetId($row);
    }

    /** Debit note ka maal: pehle bill ka lot, phir usi branch ke purane lots. Kam pade to ruk jao. */
    private function takeStock(int $branchId, int $productId, float $qty, ?int $preferredLot, float $cost, string $narration, string $when): void
    {
        $lots = DB::table('inventory_stock')->where('product_id', $productId)->where('branch_id', $branchId)
            ->where('status_id', 1)->where('balance', '>', 0)
            ->orderByRaw('stock_id = ? DESC', [(int) $preferredLot])->orderBy('stock_id')->lockForUpdate()->get();
        if ($lots->sum('balance') + 0.0001 < $qty) {
            throw new RuntimeException('Not enough stock in this branch to return (' . $this->num($lots->sum('balance')) . ' available).');
        }

        $left = $qty;
        foreach ($lots as $lot) {
            if ($left <= 0) {
                break;
            }
            $take = min($left, (float) $lot->balance);
            $balance = round((float) $lot->balance - $take, 4);
            DB::table('inventory_stock')->where('stock_id', $lot->stock_id)->update(['balance' => $balance, 'status_id' => $balance > 0 ? 1 : 2]);
            $this->stockReport($productId, (int) $lot->stock_id, $branchId, -$take, $cost, '0.00', $narration, '0', $when);
            $left -= $take;
        }
    }

    private function stockReport(int $productId, int $stockId, int $branchId, float $qty, float $cost, $retail, string $narration, string $mode, string $when): void
    {
        $last = DB::selectOne('SELECT stock FROM inventory_stock_report_table WHERE stock_report_id = (SELECT MAX(stock_report_id) FROM inventory_stock_report_table WHERE product_id = ?)', [$productId]);
        DB::table('inventory_stock_report_table')->insert([
            'date' => $when,
            'product_id' => $productId,
            'foreign_id' => $stockId,
            'branch_id' => $branchId,
            'qty' => $qty,
            'stock' => (float) ($last->stock ?? 0) + $qty,
            'cost' => round($cost, 2),
            'retail' => $retail,
            'narration' => $narration,
            'adjustment_mode' => $mode,
        ]);
    }

    /** POS terminals ko product dobara download karne ka signal (stock adjustment jaisa) */
    private function notifyTerminals(int $branchId, int $productId): void
    {
        foreach (DB::table('terminal_details')->where('branch_id', $branchId)->where('status_id', 1)->pluck('terminal_id') as $terminalId) {
            DB::table('inventory_download_status')->insert([
                'branchId' => $branchId,
                'terminalId' => $terminalId,
                'productId' => $productId,
                'status' => 1,
                'date' => date('Y-m-d'),
                'time' => date('H:i:s'),
            ]);
        }
    }

    private function createGrn($userId): int
    {
        $count = (int) DB::table('purchase_rec_gen')->count();
        return DB::table('purchase_rec_gen')->insertGetId([
            'GRN' => 'GRN-' . ($count + 1),
            'user_id' => $userId,
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        ]);
    }

    private function assertVendor(int $companyId, int $vendorId): void
    {
        if (!DB::table('vendors')->where('id', $vendorId)->where('user_id', $companyId)->exists()) {
            throw new RuntimeException('Select a vendor.');
        }
    }

    private function assertBranch(int $companyId, int $branchId): void
    {
        if (!DB::table('branch')->where('branch_id', $branchId)->where('company_id', $companyId)->where('status_id', 1)->exists()) {
            throw new RuntimeException('Select an active branch.');
        }
    }

    private function num($n): string
    {
        return rtrim(rtrim(number_format((float) $n, 4, '.', ','), '0'), '.');
    }
}
