<?php

namespace App\Services\Accounting;

use App\Models\Accounting\ChartOfAccount;
use App\Services\Accounting\Posting\SourceQuery;
use Illuminate\Support\Facades\DB;

/**
 * Opening Balances screen ka "Fill from system": purane ledgers se opening date ka hisaab — sirf form bharta hai, kuch save nahi.
 *   Inventory = aaj ka stock (balance × cost_price) − opening date ke BAAD GL me inventory ki harkat (bechi / aayi cheezein)
 *   Cash      = cash_ledger ka har branch ka aakhri balance (opening date tak)
 *   Bank      = bank_deposit_details har account ka aakhri balance → us ka GL account (mapping, warna Bank Accounts)
 *   AR        = udhaar sales (Customer Credit / Account) − POS + web wasooli (opening date tak)
 *   AP        = vendor_ledger Σ(credit − debit) (opening date tak) — PO, payments, adjustments, vendor opening
 * Pehle se GL me jo balance hai (opening date tak, purani opening entry chhod kar) wo ghata diya jata hai — do baar na gine.
 * Amounts "debit − credit" (positive = Debit column).
 */
class OpeningBalanceSuggester
{
    public function suggest(int $companyId, string $date): array
    {
        $branches = SourceQuery::branchIds($companyId);
        $in = implode(',', $branches);
        $ids = ChartOfAccount::forCompany($companyId)->whereNotNull('system_key')->pluck('id', 'system_key');

        $target = []; // account_id => [amount, note]
        $warnings = [];

        // ---- Inventory
        $stock = DB::selectOne("SELECT COALESCE(SUM(balance * (cost_price + 0)), 0) AS value,
                SUM(CASE WHEN (cost_price + 0) = 0 AND balance > 0 THEN 1 ELSE 0 END) AS zero_cost
            FROM inventory_stock WHERE status_id = 1 AND balance > 0 AND branch_id IN ({$in})");
        $after = $this->glMovement($companyId, (int) $ids['inventory'], '>', $date);
        $target[$ids['inventory']] = [round($stock->value - $after, 2),
            'Stock on hand today ' . number_format($stock->value, 2) . ($after ? ' ' . ($after < 0 ? '+ ' : '− ') . number_format(abs($after), 2) . ' moved after the opening date' : '')];
        if ($stock->zero_cost > 0) {
            $warnings[] = $stock->zero_cost . ' stock lots have no cost price — they are counted as 0. Fix their cost for a correct inventory value.';
        }

        // ---- Cash (cash_ledger: debit = bahar, credit = andar; har branch ka aakhri running balance)
        $dateSql = "(CASE WHEN date LIKE '__-__-____%' THEN DATE(STR_TO_DATE(LEFT(date, 10), '%d-%m-%Y')) ELSE LEFT(date, 10) END)";
        $cash = 0.0;
        foreach ($branches as $branch) {
            $row = DB::selectOne("SELECT balance FROM cash_ledger WHERE branch_id = ? AND {$dateSql} <= ? ORDER BY id DESC LIMIT 1", [$branch, $date]);
            $cash += (float) ($row->balance ?? 0);
        }
        $target[$ids['cash']] = [$cash, 'Cash Ledger balance on the opening date (cash kept in POS tills is not in the Cash Ledger — add it if needed)'];

        // ---- Bank (har bank account ka aakhri balance)
        $accounts = DB::table('bank_account_generaldetails')->whereIn('branch_id_company', $branches)->get(['bank_account_id', 'account_title']);
        $mappings = DB::table('account_mappings')->where('company_id', $companyId)->where('map_type', 'bank_account')->pluck('account_id', 'map_key');
        foreach ($accounts as $account) {
            $row = DB::selectOne('SELECT balance FROM bank_deposit_details WHERE bank_account_id = ? AND ' . SourceQuery::pkDate('date') . ' <= ? ORDER BY bank_deposit_id DESC LIMIT 1',
                [$account->bank_account_id, $date]);
            if (!$row) {
                continue;
            }
            $glId = (int) ($mappings[(string) $account->bank_account_id] ?? $ids['bank']);
            $prev = $target[$glId][0] ?? 0;
            $target[$glId] = [$prev + (float) $row->balance, trim(($target[$glId][1] ?? 'Bank balances:') . ' ' . $account->account_title)];
        }

        // ---- Accounts Receivable
        $credit = (float) DB::selectOne("SELECT COALESCE(SUM(total_amount + 0), 0) AS v FROM sales_receipts
            WHERE branch IN ({$in}) AND date <= ? AND status = 4 AND void_receipt = 0 AND payment_id IN (3, 7)", [$date])->v;
        $pkCa = SourceQuery::pkDate('ca.created_at');
        $collected = (float) DB::selectOne("SELECT COALESCE(SUM(ca.credit), 0) AS v FROM customer_account ca
            JOIN terminal_details t ON t.terminal_id = ca.terminal_id
            WHERE t.branch_id IN ({$in}) AND ca.credit > 0 AND ca.total_amount = 0 AND ca.opening_id > 1 AND {$pkCa} <= ?", [$date])->v;
        $webCash = (float) DB::selectOne("SELECT COALESCE(SUM(credit), 0) AS v FROM cash_ledger
            WHERE branch_id IN ({$in}) AND narration LIKE 'Payment received from Customer%' AND {$dateSql} <= ?", [$date])->v;
        $target[$ids['accounts_receivable']] = [round($credit - $collected - $webCash, 2),
            'Credit sales ' . number_format($credit, 2) . ' − collected ' . number_format($collected + $webCash, 2) . ' (estimate — compare with the customer receivable report)'];

        // ---- Accounts Payable (credit balance → negative)
        $ap = (float) DB::selectOne('SELECT COALESCE(SUM(vl.credit - vl.debit), 0) AS v FROM vendor_ledger vl
            JOIN vendors v ON v.id = vl.vendor_id WHERE v.user_id = ? AND DATE(vl.created_at) <= ?', [$companyId, $date])->v;
        $target[$ids['accounts_payable']] = [-round($ap, 2), 'Vendor ledgers: purchases − payments − returns on the opening date'];

        // ---- GL me pehle se jo hai (opening date tak, purani opening entry ke ilawa) — sirf farq bharo
        $amounts = [];
        $notes = [];
        foreach ($target as $accountId => [$amount, $note]) {
            $existing = $this->glMovement($companyId, (int) $accountId, '<=', $date);
            if (abs($existing) >= 0.01) {
                $note .= ' — ' . number_format(abs($existing), 2) . ($existing > 0 ? ' Dr' : ' Cr') . ' already in the ledger on or before this date, so only the difference is filled';
                $amount -= $existing;
            }
            $amount = round($amount, 2);
            if (abs($amount) < 0.01 && !str_contains($note, 'already')) {
                continue;
            }
            $amounts[$accountId] = $amount;
            $notes[$accountId] = $note;
        }

        if ($amounts[$ids['accounts_receivable']] ?? 0) {
            if ($amounts[$ids['accounts_receivable']] < 0) {
                $warnings[] = 'Customers show more collected than sold on credit — older customer balances may be missing. Check the receivable figure before posting.';
            }
        }

        return ['amounts' => $amounts, 'notes' => $notes, 'warnings' => $warnings];
    }

    /** Account ki posted harkat (debit − credit), opening_balance entries chhod kar. $op '<=' (tak) ya '>' (baad) */
    private function glMovement(int $companyId, int $accountId, string $op, string $date): float
    {
        return (float) DB::table('journal_entry_lines as l')
            ->join('journal_entries as e', 'e.id', '=', 'l.journal_entry_id')
            ->where('e.company_id', $companyId)->where('e.status', 'posted')
            ->where('e.source_type', '!=', 'opening_balance')
            ->where('l.account_id', $accountId)->where('e.entry_date', $op, $date)
            ->sum(DB::raw('l.debit - l.credit'));
    }
}
