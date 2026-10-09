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

- [x] **0.1** Expense cash/bank ko touch nahi karta — [ExpenseController.php:52](app/Http/Controllers/ExpenseController.php#L52) me `bank $bank` inject hai par use nahi hota. Expense save hoti hai, cash balance kam nahi hota → cash position hamesha ghalat. Payment mode field + `cash_ledger`/bank entry add karo.
- [x] **0.2** [VendorController.php:741](app/Http/Controllers/VendorController.php#L741) `profitLoss()` — company name hardcoded `"TAYYEB JAMAL"`, aur `$company` line 756 pe define hone se pehle use hota hai → function crash karta hai. Fix karo, ya function delete karke sirf `profitLossStandardReport` rakho.
- [x] **0.3** COGS galat table se — [Vendor.php:181](app/Vendor.php#L181) `master_assign_details` (tailoring job-order ka table) se COGS leta hai, branch filter bhi nahi. `sales_receipt_details.total_cost` pe le jao (jaise [report.php:225](app/report.php#L225) me hai).
- [x] **0.4** GRN vendor ledger adjust nahi karta — [purchaseController.php:472](app/Http/Controllers/purchaseController.php#L472). Short/partial receipt pe vendor balance galat reh jata hai. PO vs actual received ka farq ledger me daalo.

## Phase 1 — Accounting Core / General Ledger (~5–6 hafte) — **sabse zaroori**

- [x] **1.1 Chart of Accounts**
  ```
  chart_of_accounts  (id, company_id, code, name, type[Asset/Liability/Equity/Revenue/Expense],
                      parent_id, is_group, is_active)
  fiscal_years       (id, company_id, start_date, end_date, status[open/closed])
  accounting_periods (id, fiscal_year_id, month, status[open/closed/locked])
  ```
  Seeder me standard retail COA (Cash, Bank, AR, Inventory, AP, Sales, COGS, Expenses...).
  **Scope update (2026-10-08):** module company-wise hai — `accounting_settings` (company_id, enabled, fiscal_year_start_month). Super admin `/accounting-setup` se on karta hai → default COA (`AccountingSetupService::DEFAULT_ACCOUNTS`, 43 accounts) + current fiscal year + 12 periods. `chart_of_accounts.system_key` = posting service (1.3) ka account lookup (cash, accounts_payable, sales, cogs …), system accounts ka sirf code/naam badalta hai. Fiscal year start month per company, pehla saal banne ke baad lock. Pilot: Wild (company 7).

- [x] **1.2 Journal / General Ledger**
  ```
  journal_entries     (id, company_id, branch_id, entry_no, entry_date, period_id,
                       source_type, source_id, narration, status[draft/posted], posted_by)
  journal_entry_lines (id, journal_entry_id, account_id, debit, credit, cost_center_id)
  ```
  Constraint: har entry pe `SUM(debit) = SUM(credit)`.
  **Scope update (2026-10-08):** extra columns `total`, `reversal_of_id`, `created_by`, `posted_at` (entries) + `narration` (lines). Saare rules `App\Services\Accounting\JournalService` me — 1.3 ki posting `createPosted()` call karegi. Posted entry immutable, galti = `reverse()`. Entry no `JV-000001` company-wise.

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
| 2026-10-02 | — (plan se bahar) | Terminal Permissions V2 page (Operations me, sirf roleId 1): Company → Branch → Terminal dropdowns, inline accordion editor for `users_sales_permission`. Legacy `/permission/{id}` untouched. Dropdowns select2 (`master-tailwind` select2 URL list me add). | `app/Livewire/Terminals/TerminalPermissions.php`, `resources/views/livewire/terminals/terminal-permissions.blade.php`, `routes/web.php`, `resources/lang/en/sidebar.php`, `resources/views/layouts/master-tailwind.blade.php`, `database/migrations/2026_10_02_100000_add_terminal_permissions_menu_page.php` |
| 2026-10-02 | — (plan se bahar) | `users_sales_permission.allow_negative_stock` (Inventory section). Branch-wide setting: terminal pe toggle → `branch` + us branch ke saare terminals sync; Branch edit save → saare terminals sync (`BranchService::syncNegativeStock`). Backfill branch value se. Source of truth `branch.allow_negative_stock` (webservice yahi padhta hai). | `database/migrations/2026_10_02_110000_add_allow_negative_stock_to_users_sales_permission.php`, `app/Services/BranchService.php`, `app/Http/Controllers/BranchController.php`, `app/Livewire/Terminals/TerminalPermissions.php` |
| 2026-10-05 | — (plan se bahar) | Packing UOM (`inventory_general.pack_uom` / `pack_qty`, NULL = off). Stock primary (Pcs) me hi rehta hai, Carton sirf entry/display: 1 pack_uom = pack_qty primary. `weight_qty` nahi chheda (POS/reports/webservice same). Product form pe 2 fields + validation (weight_qty > 1 conversion ke saath nahi lag sakta). Display: Inventory List, Stock List, Stock Details, Stock Adjustment (Carton dropdown → qty × pack_qty). Client: AHSAN BUSINESS (company 140). | `database/migrations/2026_10_05_100000_add_pack_uom_to_inventory_general.php`, `app/Helpers/custom_helper.php`, `app/Http/Controllers/InventoryController.php`, `app/Http/Controllers/StockController.php`, `app/inventory.php`, `app/stock.php`, `resources/views/v2/inventory/partials/form.blade.php`, `resources/views/v2/inventory/list.blade.php`, `resources/views/v2/inventory/stockadjustment.blade.php`, `resources/views/v2/stock/list.blade.php`, `resources/views/stock/stockDetails.blade.php` |
| 2026-10-05 | — (plan se bahar) | Packing UOM entry: Opening Stock, Purchase Order (add/edit), GRN receive pe Pcs/Carton dropdown (sirf `pack_qty > 1` wale product pe). Conversion sirf browser me submit se pehle (qty × pack_qty, per-unit price/tax/amount-discount ÷ pack_qty; % discount nahi) — server/DB hamesha primary (Pcs) me, PO↔GRN checks, returns, ledger same. `/get-uom-id` ab `pack_qty`, `uom_name`, `pack_uom_name` bhi deta hai. Note: `purchase_item_details` amounts 2 decimals ke hain, isliye screen pe per-pcs rate dikhaya jata hai. Opening Stock CSV upload me Carton nahi (Pcs me do). | `public/js/pack-uom.js`, `app/inventory.php`, `app/purchase.php`, `app/Http/Controllers/purchaseController.php`, `resources/views/v2/inventory/stockopening.blade.php`, `resources/views/v2/purchase/add-purchase.blade.php`, `resources/views/v2/purchase/edit.blade.php`, `resources/views/Purchase/receive-po.blade.php`, `resources/views/Purchase/partials/pack-unit-select.blade.php` |
| 2026-10-05 | — (plan se bahar) | **Live DB:** pack UOM migration chala di (`2026_10_05_100000`, deploy pe dobara nahi chahiye). Company 140 import (client PDF se, script user ne chalayi): 292 purane products status=0 + unka dummy stock 0, 61/66 purane dept/sub-dept status=0; naye 19 dept, 61 sub-dept, 348 products (codes 2001–2348, 334 pe Carton packing), stock 0 — POS pe tab dikhenge jab Opening Stock/GRN hoga. Undo IDs: `docs/imports/import140-undo-20261005-145237.json`. | `docs/imports/company140-products-v2.xlsx`, `docs/imports/import140-undo-20261005-145237.json` |
| 2026-10-07 | 0.1–0.4 | **0.2** `profitLoss()` (hardcoded TAYYEB JAMAL, `$company` undefined) ab `profitLossStandardReport` pe redirect; `Vendor::profitandloss/profitandlossexpense/cogs/masterAmount` delete (sirf isi me use the). **0.3** `master_assign_details` wala COGS sirf usi dead function me tha — Standard report pehle se `total_COGS` (sales_receipt_details) use karta hai; asal bug Details report me mila: `report::COGS()` `$request->branch` ignore karke session branch leta tha, fix. **0.1** Expense form pe Paid From (Cash/Bank + bank account); save → `cash_ledger` / `bank_deposit_details` debit; edit/delete → reversal row (running-balance ledger, purani row nahi chhedi). `payment_mode` NULL = purani/POS expense, koi ledger effect nahi. **0.4** `changeStatusPo` (har GRN submit ke baad) → `PurchaseLedgerService::syncShortReceipt`: unreceived qty × price × (PO credit / Σqty×price) ka debit adjustment, idempotent (baaki maal aaye to ulta), `purchase_account_details.balance_amount` bhi adjust. Received qty `inventory_stock_report_table` se. Note: `purchase_item_details.total_amount` bharosemand nahi, qty×price use kiya. **Live DB:** migration `2026_10_07_100000` 2026-10-07 ko `--path` se chala di (deploy pe dobara nahi chahiye; baaki 5 purani pending migrations nahi chheri). Purane POs ka backfill nahi hua, agle trigger pe khud sync honge. | `app/Http/Controllers/VendorController.php`, `app/Vendor.php`, `app/report.php`, `app/Http/Controllers/ReportController.php`, `app/Http/Controllers/ExpenseController.php`, `app/expense.php`, `app/Services/ExpensePaymentService.php`, `resources/views/v2/expense/list.blade.php`, `database/migrations/2026_10_07_100000_add_payment_mode_to_expenses.php`, `app/Services/PurchaseLedgerService.php`, `app/Http/Controllers/purchaseController.php` |
| 2026-10-07 | — (plan se bahar) | **Live DB:** company 140 ke 348 active products (codes 2001–2348) ka `product_description` = product name ka Urdu (brand/size transliterate, Lid→ڈھکن, Spoon→چمچ, ml→ملی, oz→اونس waghera). `product_name` nahi chheda. Undo: `UPDATE inventory_general SET product_description=product_name WHERE company_id=140 AND status=1`. PO PDF (FPDF Arial) Urdu nahi chhap sakta → `purchaseController` PDF me non-ASCII description skip. | `app/Http/Controllers/purchaseController.php` |
| 2026-10-07 | 0 (follow-up) | **Purchase return / Replacement / PO cancel → vendor ledger:** `PurchaseLedgerService` ab ek hi formula se (ordered − (GRN received − returned)) × price × ratio rakhta hai; triggers: GRN (`changeStatusPo`), return (`returnInsert`), cancel (`update-status-po`), delete (`DeletePO`). Replacement ka naya maal GRN se aaye to adjustment ulta. Narration `PO receipt adjustment PO #id (GRN/Return/Cancel/Delete)`. Live dry-run: 11 POs (status 4/5/6/7) ka adjustment pending (e.g. 606/969/1291 complete return = poora credit). **P&L All Branches:** `report.php` ke 15 P&L queries me `branch_id = 'all'` kuch match nahi karta tha; `branchIds()` helper — `all` = company ki saari branches. Verify: company 4 me all = branch 3 + 168. Note: company 19 ka `sales_receipt_details.total_cost` data kharab (COGS ~2.4e15) — data issue, code nahi. | `app/Services/PurchaseLedgerService.php`, `app/Http/Controllers/purchaseController.php`, `app/report.php` |
| 2026-10-07 | — (plan se bahar) | **Live DB:** company 140 ke 298 products ka `product_name` client PDF ke "Items" column jaisa kar diya (baqi 50 pehle se same the). Ab kai naam duplicate hain (`500 ml`, `R-10`, `Spoon Milky` waghera) — farq sub-department + Urdu `product_description` (purane poore naam se bani, nahi chheda) se. PDF typo fixes: 2127/2128 = "05 oz ..." (PDF Items me galti se 04 oz), `7.oz`→`7oz`, `0z`→`oz`, `Ml`→`ml`. Undo (purane naam id-wise): `docs/imports/company140-rename-undo-20261007.json`. | `docs/imports/company140-rename-undo-20261007.json` |
| 2026-10-07 | 0 (backfill) | **Live DB:** `PurchaseLedgerService::sync(id, 'Backfill')` 11 POs pe chalaya (599, 600, 601, 603, 606, 607, 627, 819, 825, 969, 1291 — Cancelled / Return / Partially Received). `vendor_ledger` rows #2910–#2920 (narration `PO receipt adjustment PO #id (Backfill)`), re-sync delta 0. **Placed (status 2) POs jaan boojh kar bahar** — maal abhi aana hai, un ka poora credit sahi hai (15 POs). Undo: in rows ko delete + `purchase_account_details.balance_amount` me debit wapas jodo. Note: kuch purane POs (e.g. 606) ka `balance_amount` pehle se ledger credit se zyada tha (tax/UpdateAccounts ka farq), isliye complete return ke baad bhi thoda pending dikhta hai — purana data mismatch. | live DB only |
| 2026-10-07 | — (plan se bahar) | **Billing: Auto Deactivate toggle.** `invoice_setups.auto_deactivate` (default 1 = purana behaviour). Invoice Setup list (v2 + Admin) me Invoices/Edit ke saath Yes/No toggle (POST, confirm). `billing:enforce-overdue` me `auto_deactivate = 0` wali companies poori skip — na deactivate, na terminal lock, na WhatsApp; console + run audit (`skipped_auto_deactivate_off`) me dikhti hain. Jis company ka setup hi nahi wo pehle ki tarah enforce hoti hai. **Live DB:** migration `2026_10_07_110000` `--path` se chala di (deploy pe dobara nahi chahiye). | `database/migrations/2026_10_07_110000_add_auto_deactivate_to_invoice_setups.php`, `app/Models/InvoiceSetup.php`, `app/Http/Controllers/InvoiceSetupController.php`, `routes/web.php`, `app/Console/Commands/EnforceBillingOverdueCommand.php`, `resources/views/v2/billing/invoice-setup/index.blade.php`, `resources/views/Admin/InvoiceSetup/index.blade.php` |
| 2026-10-08 | 1.1 | Chart of Accounts + Fiscal Years + Periods. Tables: `accounting_settings`, `chart_of_accounts` (tree, `system_key`), `fiscal_years`, `accounting_periods`. Screens (Livewire): **Accounting Setup** (Operations, roleId 1 — company chuno, FY start month, Enable/Turn Off), **Chart of Accounts** + **Fiscal Years** (Accounts Operations ke andar, roles/packages page 16 jaise). Sidebar: company ka accounting off ho to ye 2 pages chhupe (`Sidebar::ACCOUNTING_PAGES`); `AccountingSetting::isEnabled` table na ho to false (migration se pehle deploy pe site na gire). Enable idempotent (dobara pe duplicate nahi). Delete: system / children wale account nahi (1.2 me journal lines check add karna). In-memory SQLite pe logic test pass. **Live DB (2026-10-08):** dono migrations `--path` se chala di (deploy pe dobara nahi chahiye); menu pages 168 Accounting Setup (role 1, pkg 6), 169 Chart of Accounts + 170 Fiscal Years (role 2, pkgs 2/6/7/8). Wild (company 7) pe enable: 43 accounts (26 system), FY 2026-27 (Jul–Jun, 12 periods). `enabled_by` NULL (tinker se kiya). Undo: `accounting_settings` row delete / disable; tables drop = migration rollback. | `database/migrations/2026_10_08_100000_create_accounting_core_tables.php`, `database/migrations/2026_10_08_100100_add_accounting_menu_pages.php`, `app/Models/Accounting/*`, `app/Services/Accounting/AccountingSetupService.php`, `app/Livewire/Accounting/*`, `resources/views/livewire/accounting/*`, `app/View/Components/Sidebar.php`, `routes/web.php`, `resources/lang/en/sidebar.php` |
| 2026-10-08 | 1.2 | Journal + General Ledger. Tables `journal_entries`, `journal_entry_lines` (account FK restrict). `JournalService`: `saveDraft` (unbalanced draft chalta hai), `post` (SUM debit = credit paise me, ≥2 lines, har line sirf Dr ya Cr, account usi company ka / active / group nahi, date open FY + open period me), `createPosted`, `reverse` (ulti entry, ek hi baar, reversal ka reversal nahi), `deleteDraft`. Screens: **Journal Entries** (list + filter, manual JV form live Dr/Cr total, Save Draft / Save & Post, detail, Post / Edit / Delete draft, Reverse with date + reason, `?entry=ID`), **General Ledger** (account ya group + descendants, date, branch; opening, running balance Dr/Cr, closing; sirf posted). COA: entries wala account delete nahi (deactivate). Sidebar `ACCOUNTING_PAGES` me dono add. In-memory SQLite: 26 checks pass (service rules + Livewire). **Live DB (2026-10-08):** dono migrations `--path` se chala di (deploy pe dobara nahi chahiye); menu pages 171 Journal Entries + 172 General Ledger (role 2, pkgs 2/6/7/8). Wild ka October 2026 period open — entries lag sakti hain. | `database/migrations/2026_10_08_120000_create_journal_tables.php`, `database/migrations/2026_10_08_120100_add_journal_menu_pages.php`, `app/Models/Accounting/JournalEntry.php`, `app/Models/Accounting/JournalEntryLine.php`, `app/Services/Accounting/JournalService.php`, `app/Livewire/Accounting/JournalEntries.php`, `app/Livewire/Accounting/GeneralLedger.php`, `resources/views/livewire/accounting/journal-entries.blade.php`, `resources/views/livewire/accounting/general-ledger.blade.php`, `app/Livewire/Accounting/ChartOfAccounts.php`, `app/View/Components/Sidebar.php`, `routes/web.php`, `resources/lang/en/sidebar.php`, `resources/lang/ar/sidebar.php` |
| 2026-10-08 | 1.1 / 1.2 (fix) | Sub-Admin ko accounting pages pe click karne se dashboard pe redirect: routes `routes/web.php` ke `roleChecker` group (sirf `role == 1`) me chali gayi thin. Chart of Accounts / Fiscal Years / Journal Entries / General Ledger ab `statusCheck` group me Bank Account (`view-accounts`) ke paas + `->middleware('auth')` (same middleware stack: web, CheckLogin, Authenticate). Accounting Setup admin group me hi. **Note:** naya company page hamesha `view-accounts` wale group me daalo, Terminal Permissions wale (admin-only) me nahi. | `routes/web.php` |
| 2026-10-09 | — (plan se bahar) | **Terminal Manager pagination fix:** company/branch filter ke baad next page pe `GET livewire/update?page=2` → MethodNotAllowed. Views Laravel ka plain `pagination::tailwind` / `pagination::bootstrap-4` use kar rahe the (href links, Livewire update ke baad URL `/livewire/update` ban jata). Ab `livewire::tailwind` (Super Admin view) / `livewire::bootstrap` (old view) — `wire:click` pagination. **Note:** Livewire component me pagination hamesha `->links()` ya `livewire::*` view se, `pagination::*` se nahi. Saath me Super Admin view ke saare 7 dropdowns (filter: Company, Branch, Status, Lock, Device; form: Company, Branch) searchable select2 — Terminal Permissions wala pattern (`wire:ignore` + value-wala `wire:key` + `tmSelect2` helper), `terminal-manager` layout ki select2 URL list me add. Old (non-Super Admin) view nahi chheda. Row ka Actions menu `<details>` (row lambi ho jati thi) se Alpine `terminalActionsMenu` pe — `position: fixed` floating menu (table ke overflow se clip nahi), neeche jagah na ho to upar khulta, bahar click / Esc / scroll pe band. | `resources/views/livewire/terminals/terminal-manager.blade.php`, `resources/views/livewire/terminals/terminal-manager-old.blade.php`, `resources/views/layouts/master-tailwind.blade.php` |
