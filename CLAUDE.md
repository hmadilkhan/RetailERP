# Sabify — Retail ERP (Laravel 11 + Livewire 3)

Multi-tenant retail/restaurant ERP. Multi-company, multi-branch, POS + inventory + HR + CRM.

## Architecture — do generations

| Layer | Kahan | Style |
|---|---|---|
| **Legacy core** | `app/*.php` root classes ([purchase.php](app/purchase.php), [stock.php](app/stock.php), [Vendor.php](app/Vendor.php), [report.php](app/report.php), [order.php](app/order.php)) | Raw `DB::select()` SQL, schema DB me hai — migrations me **nahi** |
| **Naya layer** | `app/Models/` + `app/Services/` (Billing/Invoice, CRM, Shopify, FBR, StockAdjustment) | Eloquent + proper migrations |

- Sirf 27 migrations hain aur wo sab naye modules ke. Core schema raw DB me hai — naya table banane se pehle live DB check karo.
- Sidebar DB-driven hai (`pages` table), labels [resources/lang/en/sidebar.php](resources/lang/en/sidebar.php) me.
- PDF reports FPDF / custom `pdfClass` se bante hain, Excel `maatwebsite/excel` se.
- Legacy code chhedte waqt: purane flows break mat karo — naya behaviour events/observers/services se attach karo.

---

# Supply Chain & Accounting — Implementation Plan

> **Baseline audit: 2026-09-21.** Ye plan us audit par bana hai. Har kaam ke baad neeche wala **Progress Log** update karna zaroori hai.

## Current status

| Department | Status | Note |
|---|---|---|
| Supply Chain | ~65% | Operations solid, **planning layer zero** |
| Accounting | ~30% | Subsidiary ledgers hain, **double-entry accounting nahi** |

### Supply Chain — kya hai
- **Procurement:** [purchaseController.php](app/Http/Controllers/purchaseController.php) (1002 ln) — PO Draft → Create → Final Submit → Receive → GRN → Return → Report
- **Vendor:** [VendorController.php](app/Http/Controllers/VendorController.php) (1233 ln) — master, vendor-product map, ledger, payable, advance payment
- **Internal replenishment (sabse mazboot):** Branch Demand → Received Demand → Transfer Order → Delivery Challan → Receiving GRN
  ([DemandController.php](app/Http/Controllers/DemandController.php) → [ReceivedDemandController.php](app/Http/Controllers/ReceivedDemandController.php) → [TransferController.php](app/Http/Controllers/TransferController.php))
- **Stock:** lot-wise (GRN-wise) deduction [TransferController.php:200](app/Http/Controllers/TransferController.php#L200), stock opening, adjustment, reports, CSV import/export

### Supply Chain — kya nahi
Reorder level / min-max / safety stock · Vendor RFQ & quotation comparison (quotation sirf CRM leads me) · PO approval workflow (sirf `status_id` flags) · Three-way match — **Vendor Bill entity hi nahi hai** · Batch/Expiry sirf PO line pe, `inventory_stock` tak nahi jata ([purchaseController.php:202](app/Http/Controllers/purchaseController.php#L202)) · Landed cost · Vendor scorecard / lead time · Demand forecasting ([SalesForecastService.php](app/Services/SalesForecastService.php) sirf sales-chat hai, planning se juda nahi)

### Accounting — kya hai
AP ledger (`vendor_ledger`) · AR ledger (`customer_account`) + aging [ReportController.php:2044](app/Http/Controllers/ReportController.php#L2044) · Cash `cash_ledger` + Bank + cheque clearance [BankController.php](app/Http/Controllers/BankController.php) · Expense + categories + voucher · Payroll→cash integration · Reports: P&L Standard [ReportController.php:1387](app/Http/Controllers/ReportController.php#L1387), P&L Details, Cash In/Out, FBR, Sales Declaration · Billing module [InvoiceSettlementService.php](app/Services/InvoiceSettlementService.php) — **ye SaaS billing hai, retail accounting nahi**

### Accounting — kya nahi (asal masla)
**Chart of Accounts bilkul nahi** (`chart_of_account` / `journal_entry` / `general_ledger` / `trial_balance` / `balance_sheet` = 0 matches) · Koi double-entry nahi, har ledger apna running balance rakhta hai · P&L ad-hoc SQL se, GL se nahi · Fiscal year, opening balance, period close/lock · Bank reconciliation · Credit/Debit note · Fixed assets & depreciation · Tax ledger ([TaxController.php](app/Http/Controllers/TaxController.php) sirf 91 ln ka rate CRUD) · Multi-currency · QuickBooks stub hai ([QuickBooksController.php](app/Http/Controllers/QuickBooksController.php), 66 ln — sirf customer CRUD)

---

## Phase 0 — Bug fixes (~1 hafta) — **isse pehle kuch mat karo**

Ye bugs client ko **ghalat financial numbers** de rahe hain. Feature gap nahi, risk hai.

- [ ] **0.1** Expense cash/bank ko touch nahi karta — [ExpenseController.php:52](app/Http/Controllers/ExpenseController.php#L52) me `bank $bank` inject hai par use nahi hota. Expense save hoti hai, cash balance kam nahi hota → cash position hamesha ghalat. Payment mode field + `cash_ledger`/bank entry add karo.
- [ ] **0.2** [VendorController.php:741](app/Http/Controllers/VendorController.php#L741) `profitLoss()` — company name hardcoded `"TAYYEB JAMAL"`, aur `$company` line 756 pe define hone se pehle use hota hai → function crash karta hai. Fix karo, ya function delete karke sirf `profitLossStandardReport` rakho.
- [ ] **0.3** COGS galat table se — [Vendor.php:181](app/Vendor.php#L181) `master_assign_details` (tailoring job-order ka table) se COGS leta hai, branch filter bhi nahi. `sales_receipt_details.total_cost` pe le jao (jaise [report.php:225](app/report.php#L225) me hai).
- [ ] **0.4** GRN vendor ledger adjust nahi karta — [purchaseController.php:472](app/Http/Controllers/purchaseController.php#L472). Short/partial receipt pe vendor balance galat reh jata hai. PO vs actual received ka farq ledger me daalo.

## Phase 1 — Accounting Core / General Ledger (~5–6 hafte) — **sabse zaroori**

- [ ] **1.1 Chart of Accounts**
  ```
  chart_of_accounts  (id, company_id, code, name, type[Asset/Liability/Equity/Revenue/Expense],
                      parent_id, is_group, is_active)
  fiscal_years       (id, company_id, start_date, end_date, status[open/closed])
  accounting_periods (id, fiscal_year_id, month, status[open/closed/locked])
  ```
  Seeder me standard retail COA (Cash, Bank, AR, Inventory, AP, Sales, COGS, Expenses...).

- [ ] **1.2 Journal / General Ledger**
  ```
  journal_entries     (id, company_id, branch_id, entry_no, entry_date, period_id,
                       source_type, source_id, narration, status[draft/posted], posted_by)
  journal_entry_lines (id, journal_entry_id, account_id, debit, credit, cost_center_id)
  ```
  Constraint: har entry pe `SUM(debit) = SUM(credit)`.

- [ ] **1.3 Posting Service** — `app/Services/Accounting/PostingService.php`. **Sabse critical piece.** Events/observers se attach karo, legacy flows mat chhedo.

  | Event | Dr | Cr |
  |---|---|---|
  | GRN receive | Inventory | GR/IR Clearing |
  | Vendor Bill | GR/IR + Input Tax | Accounts Payable |
  | Vendor Payment | Accounts Payable | Cash/Bank |
  | POS Sale | Cash/Bank/AR | Sales + Output Tax |
  | Sale (COGS) | COGS | Inventory |
  | Sales Return | Sales Return | Cash/AR |
  | Expense | Expense head | Cash/Bank/AP |
  | Payroll | Salary Expense | Cash/Bank + Payables |
  | Stock Adjustment | Inventory Gain/Loss | Inventory |
  | Transfer Out/In | Inventory (Branch B) | Inventory (Branch A) |

- [ ] **1.4 Inventory valuation** — `inventory_stock` me lot-wise rows (`grn_id`, `cost_price`, `balance`) pehle se hain, FIFO ki bunyaad mojood hai. `stock_valuation_layers` banao; `AVG(cost_price)` wali queries ([report.php:247](app/report.php#L247)) ko weighted-average / FIFO se replace karo.
- [ ] **1.5 Financial statements** — Trial Balance → Balance Sheet → P&L (ab GL se) → Cash Flow. Saath me opening balance entry screen + period close/lock.
- [ ] **1.6 Backfill** — `vendor_ledger`, `customer_account`, `cash_ledger`, `expenses`, `sales_receipts` ko cut-off date se GL me migrate karne ki script.

## Phase 2 — Supply Chain gaps (~3–4 hafte)

- [ ] **2.1 Vendor Bill + Three-way match** — `vendor_bills` + `vendor_bill_lines`. PO ↔ GRN ↔ Bill match tolerance ke saath. AP ab Bill pe book hogi, PO pe nahi. Credit/Debit note bhi.
- [ ] **2.2 Batch/Expiry zinda karo** — `batch_no` / `expiry_date` ko `inventory_stock` tak le jao, FEFO deduction, near-expiry report + alert. **Sabse kam mehnat, sabse zyada value** — data pehle se capture ho raha hai.
- [ ] **2.3 Reorder / Replenishment** — product+branch level pe `min_qty`, `max_qty`, `reorder_qty`, `lead_time_days`. Auto-demand suggestion screen (current stock + pending PO/transfer + sales velocity). Existing Demand module ke upar bane, alag module nahi.
- [ ] **2.4 Approval workflow** — `approval_rules` (module, branch, amount slab, approver role) + `approval_logs`. PO, Demand, Transfer, Stock Adjustment, Expense pe lagao.
- [ ] **2.5 Landed cost** — PO/GRN pe freight/duty/clearing; value ya qty ke hisab se lines pe apportion → asli cost price.
- [ ] **2.6 Vendor scorecard** — on-time delivery %, fill rate, price variance, return rate. Data PO+GRN me pehle se hai, sirf report banani hai.

## Phase 3 — Nice to have (~2–3 hafte)

- [ ] **3.1** Bank reconciliation
- [ ] **3.2** Budget vs actual
- [ ] **3.3** Fixed assets + depreciation
- [ ] **3.4** Cost centers
- [ ] **3.5** RFQ / vendor quotation comparison
- [ ] **3.6** QuickBooks full invoice + journal sync

### Target

| | Abhi | Phase 1+2 ke baad |
|---|---|---|
| Supply Chain | ~65% | ~90% |
| Accounting | ~30% | ~85% |

**Sabse sasta win:** Phase 0 ke 4 bugs (~1 hafta) + Phase 2.2 batch/expiry (~3 din).

---

# Progress Log

> **Rule:** Har kaam ke baad (a) upar wala checkbox `[x]` karo, (b) neeche table me ek row add karo: date, phase, kya hua, kaunse files. Scope badle to isi plan me update karo — alag file mat banao.

| Date | Phase | Kya hua | Files |
|---|---|---|---|
| 2026-09-21 | — | Baseline audit + plan likha gaya. Abhi tak koi code change nahi. | `CLAUDE.md` (naya) |
