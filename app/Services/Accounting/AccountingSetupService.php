<?php

namespace App\Services\Accounting;

use App\Models\Accounting\AccountingPeriod;
use App\Models\Accounting\AccountingSetting;
use App\Models\Accounting\ChartOfAccount;
use App\Models\Accounting\FiscalYear;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

/**
 * Phase 1.1: company pe accounting module on karna — default retail Chart of Accounts + current fiscal year (12 periods).
 * Sab kuch idempotent: dobara enable karne pe pehle se bane accounts / fiscal year dobara nahi bante.
 */
class AccountingSetupService
{
    /**
     * Standard retail COA. [code, name, type, parent code, is_group, system_key]
     * system_key wale accounts posting service (1.3) use karegi — inhe delete / type change nahi kar sakte.
     */
    const DEFAULT_ACCOUNTS = [
        ['1000', 'Assets', 'Asset', null, true, null],
        ['1100', 'Current Assets', 'Asset', '1000', true, null],
        ['1110', 'Cash in Hand', 'Asset', '1100', false, 'cash'],
        ['1120', 'Bank Accounts', 'Asset', '1100', false, 'bank'],
        ['1130', 'Accounts Receivable', 'Asset', '1100', false, 'accounts_receivable'],
        ['1140', 'Inventory', 'Asset', '1100', false, 'inventory'],
        ['1150', 'Advances to Vendors', 'Asset', '1100', false, 'vendor_advance'],
        ['1160', 'Input Tax (GST)', 'Asset', '1100', false, 'input_tax'],
        ['1200', 'Fixed Assets', 'Asset', '1000', true, null],
        ['1210', 'Furniture & Fixtures', 'Asset', '1200', false, null],
        ['1220', 'Equipment & Computers', 'Asset', '1200', false, null],
        ['1230', 'Vehicles', 'Asset', '1200', false, null],
        ['1290', 'Accumulated Depreciation', 'Asset', '1200', false, 'accumulated_depreciation'],

        ['2000', 'Liabilities', 'Liability', null, true, null],
        ['2100', 'Current Liabilities', 'Liability', '2000', true, null],
        ['2110', 'Accounts Payable', 'Liability', '2100', false, 'accounts_payable'],
        ['2120', 'GR/IR Clearing', 'Liability', '2100', false, 'grir_clearing'],
        ['2130', 'Output Tax (GST)', 'Liability', '2100', false, 'output_tax'],
        ['2140', 'Salaries Payable', 'Liability', '2100', false, 'salaries_payable'],
        ['2150', 'Customer Advances', 'Liability', '2100', false, 'customer_advance'],
        ['2160', 'Accrued Expenses', 'Liability', '2100', false, null],
        // automatic posting me source ke amounts ka farq (rounding / kharab data) yahan aata hai — warning ke saath
        ['2190', 'Suspense / Rounding', 'Liability', '2100', false, 'suspense'],
        ['2200', 'Long-term Liabilities', 'Liability', '2000', true, null],
        ['2210', 'Loans Payable', 'Liability', '2200', false, 'loans_payable'],

        ['3000', 'Equity', 'Equity', null, true, null],
        ['3100', "Owner's Capital", 'Equity', '3000', false, 'owners_capital'],
        ['3200', 'Retained Earnings', 'Equity', '3000', false, 'retained_earnings'],
        ['3300', 'Opening Balance Equity', 'Equity', '3000', false, 'opening_balance_equity'],
        ['3400', 'Drawings', 'Equity', '3000', false, null],

        ['4000', 'Revenue', 'Revenue', null, true, null],
        ['4100', 'Sales', 'Revenue', '4000', false, 'sales'],
        ['4200', 'Sales Returns', 'Revenue', '4000', false, 'sales_returns'],
        ['4300', 'Sales Discounts', 'Revenue', '4000', false, 'sales_discounts'],
        ['4900', 'Other Income', 'Revenue', '4000', false, 'other_income'],

        ['5000', 'Cost of Sales', 'Expense', null, true, null],
        ['5100', 'Cost of Goods Sold', 'Expense', '5000', false, 'cogs'],
        ['5200', 'Inventory Gain / Loss', 'Expense', '5000', false, 'inventory_adjustment'],

        ['6000', 'Operating Expenses', 'Expense', null, true, null],
        ['6100', 'Salaries & Wages', 'Expense', '6000', false, 'salary_expense'],
        ['6200', 'Rent', 'Expense', '6000', false, null],
        ['6300', 'Utilities', 'Expense', '6000', false, null],
        ['6400', 'Bank Charges', 'Expense', '6000', false, 'bank_charges'],
        ['6500', 'Depreciation Expense', 'Expense', '6000', false, 'depreciation_expense'],
        ['6900', 'General Expenses', 'Expense', '6000', false, 'general_expense'],
    ];

    public function enable(int $companyId, int $startMonth, $userId = null): AccountingSetting
    {
        return DB::transaction(function () use ($companyId, $startMonth, $userId) {
            $setting = AccountingSetting::firstOrNew(['company_id' => $companyId]);

            // fiscal year ban chuka ho to start month nahi badal sakte, warna saal aapas me overlap honge
            $hasYears = FiscalYear::where('company_id', $companyId)->exists();
            if (!$hasYears) {
                $setting->fiscal_year_start_month = $startMonth;
            }
            $setting->enabled = true;
            $setting->enabled_at = $setting->enabled_at ?? now();
            $setting->enabled_by = $setting->enabled_by ?? $userId;
            $setting->save();

            $this->seedChartOfAccounts($companyId);
            if (!$hasYears) {
                $this->createFiscalYear($companyId, $this->fiscalYearStart(now(), $setting->fiscal_year_start_month));
            }

            return $setting;
        });
    }

    public function disable(int $companyId): void
    {
        // sirf menu band — accounts / fiscal years ka data waisa hi rehta hai
        AccountingSetting::where('company_id', $companyId)->update(['enabled' => false]);
    }

    /** Default COA daalo; jo code pehle se hai use chhod do. */
    public function seedChartOfAccounts(int $companyId): int
    {
        $ids = ChartOfAccount::forCompany($companyId)->pluck('id', 'code');
        $created = 0;

        foreach (self::DEFAULT_ACCOUNTS as [$code, $name, $type, $parentCode, $isGroup, $systemKey]) {
            if (isset($ids[$code])) {
                continue;
            }
            if ($systemKey && ChartOfAccount::forCompany($companyId)->where('system_key', $systemKey)->exists()) {
                continue;
            }
            $account = ChartOfAccount::create([
                'company_id' => $companyId,
                'code' => $code,
                'name' => $name,
                'type' => $type,
                'parent_id' => $parentCode ? ($ids[$parentCode] ?? null) : null,
                'is_group' => $isGroup,
                'is_active' => true,
                'system_key' => $systemKey,
            ]);
            $ids[$code] = $account->id;
            $created++;
        }

        return $created;
    }

    /** Jis fiscal year me $date aati hai us ka pehla din. */
    public function fiscalYearStart(Carbon $date, int $startMonth): Carbon
    {
        $year = $date->month >= $startMonth ? $date->year : $date->year - 1;
        return Carbon::create($year, $startMonth, 1)->startOfDay();
    }

    public function createFiscalYear(int $companyId, Carbon $start): FiscalYear
    {
        $start = $start->copy()->startOfMonth();
        $end = $start->copy()->addYear()->subDay();
        $name = $start->month === 1 ? 'FY ' . $start->year : 'FY ' . $start->year . '-' . $end->format('y');

        $year = FiscalYear::create([
            'company_id' => $companyId,
            'name' => $name,
            'start_date' => $start->toDateString(),
            'end_date' => $end->toDateString(),
            'status' => 'open',
        ]);

        for ($i = 0; $i < 12; $i++) {
            $periodStart = $start->copy()->addMonths($i);
            AccountingPeriod::create([
                'fiscal_year_id' => $year->id,
                'month' => $i + 1,
                'start_date' => $periodStart->toDateString(),
                'end_date' => $periodStart->copy()->endOfMonth()->toDateString(),
                'status' => 'open',
            ]);
        }

        return $year;
    }

    /** Aakhri fiscal year ke baad wala saal banao. */
    public function createNextFiscalYear(int $companyId): FiscalYear
    {
        return DB::transaction(function () use ($companyId) {
            $last = FiscalYear::where('company_id', $companyId)->orderByDesc('end_date')->lockForUpdate()->first();
            $start = $last
                ? Carbon::parse($last->end_date)->addDay()
                : $this->fiscalYearStart(now(), (int) (AccountingSetting::where('company_id', $companyId)->value('fiscal_year_start_month') ?: 7));

            return $this->createFiscalYear($companyId, $start);
        });
    }
}
