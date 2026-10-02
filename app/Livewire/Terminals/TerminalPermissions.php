<?php

namespace App\Livewire\Terminals;

use App\Models\Branch;
use App\Models\Company;
use App\Models\Terminal;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;

class TerminalPermissions extends Component
{
    #[Url(as: 'company', except: '')]
    public $companyId = '';

    #[Url(as: 'branch', except: '')]
    public $branchId = '';

    #[Url(as: 'terminal', except: '')]
    public $terminalId = '';

    public $openTerminalId = null;
    public array $perms = [];
    public bool $hasRecord = false;

    /**
     * Column => [label, type]. type: toggle (0/1), count (int >= 0), text (varchar).
     * Column names mirror users_sales_permission exactly.
     */
    public const SECTIONS = [
        'sales' => ['title' => 'Sales & Payments', 'hint' => 'Payment modes and sale types available at the till.', 'fields' => [
            'cash_sale' => ['Cash Sales', 'toggle'],
            'card_sale' => ['Credit Card Sales', 'toggle'],
            'customer_credit_sale' => ['Customer Credit Sales', 'toggle'],
            'wallets_sales' => ['Wallet Sales', 'toggle'],
            'online_sales' => ['Online Sales', 'toggle'],
            'goods' => ['Goods Sales', 'toggle'],
            'retail' => ['Retail', 'toggle'],
            'wholesale' => ['Wholesale', 'toggle'],
            'prepayment' => ['Estimate / PrePayment', 'toggle'],
            'order_booking' => ['Order Booking', 'toggle'],
            'service_provider' => ['Service Provider', 'toggle'],
            'delivery' => ['Delivery', 'toggle'],
            'tables' => ['Tables', 'toggle'],
            'discount' => ['Discount', 'toggle'],
            'cost' => ['Costing', 'toggle'],
        ]],
        'cash' => ['title' => 'Cash & Returns', 'hint' => 'Drawer balances, cash movement and refunds.', 'fields' => [
            'ob' => ['Opening Balance', 'toggle'],
            'cb' => ['Closing Balance', 'toggle'],
            'manual_close' => ['Manual Close Button', 'toggle'],
            'cash_in' => ['Cash In', 'toggle'],
            'cash_out' => ['Cash Out', 'toggle'],
            'expenses' => ['Expense', 'toggle'],
            'sale_return' => ['Sale Return', 'toggle'],
            'void_receipt' => ['Void Receipt', 'toggle'],
            'r_cash' => ['Customer Credit Return (Cash)', 'toggle'],
            'r_card' => ['Customer Credit Return (Card)', 'toggle'],
            'r_cheque' => ['Customer Credit Return (Cheque)', 'toggle'],
        ]],
        'pos' => ['title' => 'POS Behaviour', 'hint' => 'What the cashier can see and change on screen.', 'fields' => [
            'customer' => ['Customer (Add or Select)', 'toggle'],
            'item_delete_pos' => ['Item Delete', 'toggle'],
            'update_item_name' => ['Update Item Name', 'toggle'],
            'Note' => ['Note', 'toggle'],
            'Notifications' => ['Notifications', 'toggle'],
            'Parent_Order_Print' => ['Parent Order Print', 'toggle'],
            'Hold_Bill_Table_Delete' => ['Hold Bill Table Delete', 'toggle'],
            'thank_you_voice' => ['Thank You Voice', 'toggle'],
            'Colors' => ['Colors', 'toggle'],
            'customer_measurement' => ['Customer Measurement', 'toggle'],
            'show_stock_in_pos' => ['Show Stock in POS', 'toggle'],
            'digital_scale_code_extract' => ['Sunmi Barcode Scanner', 'toggle'],
            'open_table_system' => ['Open Table System', 'toggle'],
            'limited_table_system' => ['Limited Table System', 'toggle'],
        ]],
        'inventory' => ['title' => 'Inventory', 'hint' => 'Stock effects of sales and receiving from the terminal.', 'fields' => [
            'stock_deduction' => ['Stock Deduction', 'toggle'],
            'create_inventory' => ['Create Inventory', 'toggle'],
            'closing_raw_inventory' => ['Closing Raw Inventory', 'toggle'],
            'label_printing' => ['Label Printing', 'toggle'],
            'cost_price_with_purchase' => ['Cost Price With Purchase', 'toggle'],
            'no_receving_just_printing' => ['No Receiving, Just Print', 'toggle'],
        ]],
        'sync' => ['title' => 'Sync & Compliance', 'hint' => 'Tax authority integrations and data sync.', 'fields' => [
            'fbr_sync' => ['FBR Sync', 'toggle'],
            'srb_sync' => ['SRB Sync', 'toggle'],
            'vat' => ['VAT Sync', 'toggle'],
            'terminal_auto_sync' => ['Terminal Auto Sync', 'toggle'],
            'timer_terminal_auto_sync' => ['Timer Terminal Auto Sync', 'toggle'],
            'print_to_server' => ['Print to Server', 'toggle'],
            'delete_local_db' => ['Delete Local DB (On Declaration)', 'toggle'],
        ]],
        'layouts' => ['title' => 'Layouts', 'hint' => 'POS screen layout loaded on this terminal.', 'fields' => [
            'retail_layout' => ['Retail', 'toggle'],
            'restaurant_layout' => ['Restaurant', 'toggle'],
            'pharmacy_layout' => ['Pharmacy', 'toggle'],
            'snowhite_retail' => ['Snowhite', 'toggle'],
            'pizzabrry_layout' => ['Pizza', 'toggle'],
            'sindh_food_authority_layout' => ['Sindh Food Authority', 'toggle'],
            'hotel_layout' => ['Hotel', 'toggle'],
            'saloon' => ['Saloon', 'toggle'],
            'order_calling' => ['Order Calling', 'toggle'],
            'order_calling_display' => ['Order Calling Display', 'toggle'],
            'deliveryboy_layout' => ['DeliveryBoy App', 'toggle'],
        ]],
        'receipt' => ['title' => 'Receipt Printing', 'hint' => 'Paper size, templates and number of copies.', 'fields' => [
            'print_receipt' => ['Receipt Copies', 'count'],
            'two_inch_printing' => ['Two Inch Printing', 'toggle'],
            'three_inch_printing' => ['Three Inch Printing', 'toggle'],
            'receipt_1' => ['Receipt 1', 'toggle'],
            'receipt_2' => ['Receipt 2', 'toggle'],
            'receipt_3' => ['Receipt 3', 'toggle'],
            'receipt_4' => ['Receipt 4', 'toggle'],
        ]],
        'reports' => ['title' => 'Reports', 'hint' => 'End-of-day reports and their print copies.', 'fields' => [
            'print_declaration_report' => ['Declaration Report Copies', 'count'],
            'print_expense_report' => ['Expense Report Copies', 'count'],
            'declaration' => ['Declaration', 'toggle'],
            'isdb' => ['ISDB', 'toggle'],
            'cio' => ['CIO', 'toggle'],
            'two_inch_Reports' => ['Two Inch Report', 'toggle'],
            'Email_Reports' => ['Email Reports', 'toggle'],
            'delivery_report' => ['Delivery Report', 'toggle'],
        ]],
        'token' => ['title' => 'Token Print', 'hint' => 'Kitchen / order tokens.', 'fields' => [
            'token_print' => ['Token Print', 'toggle'],
            'print_article_code' => ['Article Code', 'toggle'],
            'Department_Token_Print' => ['Department', 'toggle'],
            'print_name_on_token' => ['Print Name on Token', 'toggle'],
            'Token_3Inch' => ['Token Three Inch', 'toggle'],
            'Token_2Inch' => ['Token Two Inch', 'toggle'],
            'secondary_token_print' => ['Secondary Token Print', 'toggle'],
            'token_timer' => ['Token Timer', 'toggle'],
            'kot_p_q_t' => ['KOT - Price / Qty / Total', 'toggle'],
        ]],
        'display' => ['title' => 'Customer Display', 'hint' => 'Second screen facing the customer.', 'fields' => [
            'Customer_Display' => ['Customer Display', 'toggle'],
            'customer_display_video' => ['Customer Display Video', 'toggle'],
            'Customer_Display_BG' => ['Customer Display BG', 'toggle'],
            'video_name' => ['Video Name', 'text'],
        ]],
        'backup' => ['title' => 'POS Backup', 'hint' => 'Local database backups on the terminal.', 'fields' => [
            'backup_every_receipt' => ['Full backup on every receipt', 'toggle'],
            'backup_on_declaration' => ['Full backup on declaration', 'toggle'],
        ]],
        'messaging' => ['title' => 'SMS & WhatsApp', 'hint' => 'Customer messages sent from POS events.', 'fields' => [
            'sms_booking' => ['Booking SMS', 'toggle'],
            'sms_delivery' => ['Delivery SMS', 'toggle'],
            'sms_takeaway' => ['Takeaway SMS', 'toggle'],
            'sms_reminder' => ['SMS Reminder', 'toggle'],
            'whatsapp_msg' => ['WhatsApp Message', 'toggle'],
        ]],
        'limits' => ['title' => 'Limits & Timers', 'hint' => 'Numeric caps and intervals used by the POS.', 'fields' => [
            'Item_Listing_Count' => ['Item Listing', 'text'],
            'timer_terminal_auto_sync_value' => ['Auto Sync Timer Value', 'count'],
            'online_timer' => ['Online Timer', 'count'],
            'discount_percentage' => ['Max Discount %', 'count'],
            'discount_amount' => ['Max Discount Amount', 'count'],
        ]],
    ];

    public function mount(): void
    {
        abort_unless((int) session('roleId') === 1, 403);

        if ($this->terminalId !== '') {
            $this->openTerminal((int) $this->terminalId);
        }
    }

    public function updatedCompanyId(): void
    {
        $this->branchId = '';
        $this->terminalId = '';
        $this->closeTerminal();
    }

    public function updatedBranchId(): void
    {
        $this->terminalId = '';
        $this->closeTerminal();
    }

    public function updatedTerminalId(): void
    {
        if ($this->terminalId === '') {
            $this->closeTerminal();
            return;
        }

        $this->openTerminal((int) $this->terminalId);
    }

    public function toggleTerminal(int $terminalId): void
    {
        if ((int) $this->openTerminalId === $terminalId) {
            $this->closeTerminal();
            return;
        }

        $this->openTerminal($terminalId);
    }

    public function openTerminal(int $terminalId): void
    {
        $terminal = Terminal::query()
            ->join('branch', 'branch.branch_id', '=', 'terminal_details.branch_id')
            ->where('terminal_details.terminal_id', $terminalId)
            ->select('terminal_details.terminal_id', 'terminal_details.branch_id', 'branch.company_id')
            ->first();

        if (!$terminal) {
            $this->closeTerminal();
            return;
        }

        // Keep the dropdowns in sync when a terminal is opened from a deep link.
        $this->companyId = (string) $terminal->company_id;
        $this->branchId = (string) $terminal->branch_id;

        $row = DB::table('users_sales_permission')->where('terminal_id', $terminalId)->first();

        $this->hasRecord = (bool) $row;
        $this->perms = [];

        foreach (self::fields() as $column => [$label, $type]) {
            $value = $row->{$column} ?? null;
            $this->perms[$column] = match ($type) {
                'toggle' => (int) $value === 1,
                'count' => (int) ($value ?? 0),
                default => (string) ($value ?? ''),
            };
        }

        $this->openTerminalId = $terminalId;
        $this->resetValidation();
    }

    public function closeTerminal(): void
    {
        $this->openTerminalId = null;
        $this->perms = [];
        $this->hasRecord = false;
        $this->resetValidation();
    }

    public function setSection(string $section, bool $enabled): void
    {
        foreach (self::SECTIONS[$section]['fields'] ?? [] as $column => [$label, $type]) {
            if ($type === 'toggle') {
                $this->perms[$column] = $enabled;
            }
        }
    }

    public function save(): void
    {
        abort_unless((int) session('roleId') === 1, 403);

        if (!$this->openTerminalId) {
            return;
        }

        $rules = [];
        foreach (self::fields() as $column => [$label, $type]) {
            $rules['perms.' . $column] = match ($type) {
                'toggle' => ['boolean'],
                'count' => ['nullable', 'integer', 'min:0', 'max:99999'],
                default => ['nullable', 'string', 'max:240'],
            };
        }
        $this->validate($rules, [], collect(self::fields())->mapWithKeys(fn ($field, $column) => ['perms.' . $column => $field[0]])->all());

        $item = [];
        foreach (self::fields() as $column => [$label, $type]) {
            $value = $this->perms[$column] ?? null;
            $item[$column] = match ($type) {
                'toggle' => $value ? 1 : 0,
                'count' => max(0, (int) $value),
                default => trim((string) $value),
            };
        }
        $item['user_id'] = session('userid');

        $existing = DB::table('users_sales_permission')->where('terminal_id', $this->openTerminalId)->first();

        if ($existing) {
            DB::table('users_sales_permission')->where('permission_id', $existing->permission_id)->update($item);
        } else {
            DB::table('users_sales_permission')->insert($item + ['terminal_id' => $this->openTerminalId]);
            $this->hasRecord = true;
        }

        session()->flash('permission_message', 'Permissions saved for ' . (Terminal::find($this->openTerminalId)->terminal_name ?? 'terminal') . '.');
    }

    public static function fields(): array
    {
        return array_merge(...array_values(array_map(fn ($section) => $section['fields'], self::SECTIONS)));
    }

    #[Title('Terminal Permissions')]
    public function render()
    {
        $companies = Company::query()
            ->select('company_id', 'name')
            ->orderBy('name')
            ->get();

        $branches = Branch::query()
            ->select('branch_id', 'branch_name')
            ->when($this->companyId !== '', function ($query) {
                $query->where('company_id', $this->companyId);
            }, function ($query) {
                $query->whereRaw('1 = 0');
            })
            ->orderBy('branch_name')
            ->get();

        $branchTerminals = Terminal::query()
            ->select('terminal_id', 'terminal_name', 'mac_address', 'serial_no', 'model_no', 'status_id')
            ->when($this->branchId !== '', function ($query) {
                $query->where('branch_id', $this->branchId);
            }, function ($query) {
                $query->whereRaw('1 = 0');
            })
            ->orderBy('status_id')
            ->orderBy('terminal_name')
            ->get();

        $terminals = $this->terminalId !== ''
            ? $branchTerminals->where('terminal_id', (int) $this->terminalId)->values()
            : $branchTerminals;

        $toggleColumns = array_keys(array_filter(self::fields(), fn ($field) => $field[1] === 'toggle'));
        $layoutLabels = collect(self::SECTIONS['layouts']['fields'])->map(fn ($field) => $field[0]);

        $summaries = DB::table('users_sales_permission')
            ->whereIn('terminal_id', $terminals->pluck('terminal_id'))
            ->get()
            ->keyBy('terminal_id')
            ->map(function ($row) use ($toggleColumns, $layoutLabels) {
                return [
                    'enabled' => collect($toggleColumns)->filter(fn ($column) => (int) ($row->{$column} ?? 0) === 1)->count(),
                    'layouts' => $layoutLabels->filter(fn ($label, $column) => (int) ($row->{$column} ?? 0) === 1)->values()->all(),
                ];
            });

        return view('livewire.terminals.terminal-permissions', [
            'companies' => $companies,
            'branches' => $branches,
            'branchTerminals' => $branchTerminals,
            'terminals' => $terminals,
            'summaries' => $summaries,
            'toggleTotal' => count($toggleColumns),
            'sections' => self::SECTIONS,
        ])->layout('layouts.master-tailwind');
    }
}
