<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;

/**
 * Web expense ka paisa cash_ledger ya bank_deposit_details se nikalta hai.
 * Dono running-balance ledgers hain, isliye edit/delete pe purani row nahi chhedte — reversal row daalte hain.
 * Ledger columns int hain, amount round ho kar jata hai (baaki ledgers bhi aise hi likhte hain).
 */
class ExpensePaymentService
{
    /** Expense ki payment ledger me post karo aur bani hui row ka id expense pe save karo. */
    public function post(int $expenseId): void
    {
        $expense = DB::table('expenses')->where('exp_id', $expenseId)->first();
        if (!$expense || !in_array($expense->payment_mode, ['cash', 'bank'])) {
            return;
        }

        $amount = (int) round($expense->net_amount);
        $narration = 'Expense #' . $expense->exp_id . ' : ' . $expense->expense_details;

        if ($expense->payment_mode === 'cash') {
            $id = $this->cashEntry($expense->branch_id, $amount, 0, $narration);
            DB::table('expenses')->where('exp_id', $expenseId)->update(['cash_ledger_id' => $id]);
        } else {
            $id = $this->bankEntry($expense->bank_account_id, $amount, 0, $narration);
            DB::table('expenses')->where('exp_id', $expenseId)->update(['bank_deposit_id' => $id]);
        }
    }

    /** Pehle wali posting ko ulta karo (paisa wapas). Sirf tab jab is expense ne ledger row banayi thi. */
    public function reverse(int $expenseId, string $reason): void
    {
        $expense = DB::table('expenses')->where('exp_id', $expenseId)->first();
        if (!$expense) {
            return;
        }

        $narration = 'Reversal (' . $reason . ') Expense #' . $expense->exp_id . ' : ' . $expense->expense_details;

        if ($expense->cash_ledger_id) {
            $original = DB::table('cash_ledger')->where('id', $expense->cash_ledger_id)->first();
            if ($original) {
                $this->cashEntry($original->branch_id, 0, $original->debit, $narration);
            }
        }

        if ($expense->bank_deposit_id) {
            $original = DB::table('bank_deposit_details')->where('bank_deposit_id', $expense->bank_deposit_id)->first();
            if ($original) {
                $this->bankEntry($original->bank_account_id, 0, $original->debit, $narration);
            }
        }

        DB::table('expenses')->where('exp_id', $expenseId)->update(['cash_ledger_id' => null, 'bank_deposit_id' => null]);
    }

    private function cashEntry($branchId, int $debit, int $credit, string $narration): int
    {
        $last = DB::table('cash_ledger')->where('branch_id', $branchId)->orderByDesc('id')->lockForUpdate()->first();
        $balance = ($last->balance ?? 0) - $debit + $credit;

        return DB::table('cash_ledger')->insertGetId([
            'branch_id' => $branchId,
            'date' => date('Y-m-d'),
            'debit' => $debit,
            'credit' => $credit,
            'balance' => $balance,
            'narration' => $narration,
        ]);
    }

    private function bankEntry($accountId, int $debit, int $credit, string $narration): int
    {
        $last = DB::table('bank_deposit_details')->where('bank_account_id', $accountId)->orderByDesc('bank_deposit_id')->lockForUpdate()->first();
        $balance = ($last->balance ?? 0) - $debit + $credit;

        return DB::table('bank_deposit_details')->insertGetId([
            'bank_account_id' => $accountId,
            'cheque_number' => '',
            'cheque_date' => date('Y-m-d'),
            'debit' => $debit,
            'credit' => $credit,
            'balance' => $balance,
            'narration' => $narration,
            'mode' => 'Expense',
        ]);
    }
}
