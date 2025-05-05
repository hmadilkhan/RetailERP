<?php

namespace App\Livewire;

use App\Models\Branch;
use App\Models\OrderMode;
use App\Models\OrderPayment;
use App\Models\OrderStatus;
use App\Models\Terminal;
use App\Models\User;
use App\Models\UserAuthorization;
use Livewire\Component;
use Illuminate\Support\Facades\DB;
use oasis\names\specification\ubl\schema\xsd\Order_2\OrderType;

class ReportBuilder extends Component
{
    // Filters
    public $fromDate = '';
    public $toDate = '';
    public $branch = '';
    public $terminal = '';
    public $customer = '';
    public $user = '';
    public $status = '';
    public $orderType = '';
    public $paymentMethod = '';

    // controls
    public $branches = [];
    public $terminals = [];
    public $customers = [];
    public $users = [];
    public $statuses = [];
    public $orderTypes = [];
    public $paymentMethods = [];
    
    // Report Builder
    public $selectedFields = [];
    public $filters = [];
    public $groupByFields = [];
    public $calculatedFields = [];
    
    // Report Results
    public $reportResults;
    public $totalResults = 0;
    public $lastPage = 1;

    // Pagination
    public $perPage = 10;
    public $currentPage = 1;

    // Loading state
    public $isGenerating = false;

    public $availableTables = [
        'Sales Receipts' => [
            // ['value' => 'sales_receipts.id', 'label' => 'Id'],
            ['value' => 'customers.name', 'label' => 'Customer Name'],
            ['value' => 'customers.email', 'label' => 'Customer Email'],
            ['value' => 'customers.phone', 'label' => 'CustomerPhone'],
            ['value' => 'customers.address', 'label' => 'Customer Address'],
            ['value' => 'user_details.fullname', 'label' => 'User Name'],
            ['value' => 'sales_receipts.receipt_no', 'label' => 'Receipt No'],
            ['value' => 'sales_receipts.opening_id', 'label' => 'Opening Id'],
            ['value' => 'sales_order_mode.order_mode', 'label' => 'Order Type'],
            ['value' => 'sales_payment.payment_mode', 'label' => 'Payment Method'],
            ['value' => 'sales_receipts.actual_amount', 'label' => 'Actual Amount'],
            ['value' => 'sales_receipts.total_amount', 'label' => 'Total Amount'],
            ['value' => 'branch.branch_name', 'label' => 'Branch'],
            ['value' => 'terminal_details.terminal_name', 'label' => 'Terminal'],
            ['value' => 'sales_receipts.date', 'label' => 'Date'],
            ['value' => 'sales_order_status.order_status_name', 'label' => 'Status'],
            ['value' => 'sales_receipts.created_at', 'label' => 'Created At']
        ],
        'Accounts' => [
            // ['value' => 'sales_receipts.id', 'label' => 'Id'],
            ['value' => 'sales_account_general.receive_amount', 'label' => 'Receive Amount'],
            ['value' => 'sales_account_subdetails.discount_amount', 'label' => 'Discount Amount'],
            ['value' => 'sales_account_subdetails.sales_tax_amount', 'label' => 'FBR Tax Amount'],
            ['value' => 'sales_account_subdetails.srb', 'label' => 'SRB Tax Amount'],
            ['value' => 'sales_account_subdetails.delivery_charges', 'label' => 'Delivery Charges'],
        ],
    ];

    public array $availableFields = [
        'sales_receipts.id',
        'sales_receipts.customer_id',
        'sales_receipts.total_amount',
        'sales_receipts.branch',
        'sales_receipts.date',
        'customers.name',
        'customers.id as customer_id_alias',
    ];

    public function mount()
    {
        $this->branches = Branch::where("company_id", session("company_id"))->get();
        $this->users = User::whereIn("id", UserAuthorization::where("company_id", session("company_id"))->pluck("user_id"))->get();
        $this->statuses = OrderStatus::all();
        $this->orderTypes = OrderMode::all();
        $this->paymentMethods = OrderPayment::all();
        
        // Initialize empty arrays for other collections
        $this->terminals = collect();
        $this->customers = collect();
        $this->reportResults = [];
    }

    public function updatedBranch($value)
    {
        if ($value) {
            $this->terminals = Terminal::where("branch_id", $value)->get();
            $this->terminal = ''; // Reset terminal selection when branch changes
        } else {
            $this->terminals = collect();
            $this->terminal = '';
        }
    }
    

    public function addFilter()
    {
        $this->filters[] = ['field' => '', 'operator' => '=', 'value' => ''];
    }

    public function addCalculatedField()
    {
        $this->calculatedFields[] = ['name' => '', 'formula' => ''];
    }

    public function generateReport()
    {
        $this->isGenerating = true;

        try {
            $query = DB::table('sales_receipts')
                ->join('sales_account_general', 'sales_account_general.receipt_id', '=', 'sales_receipts.id')
                ->join('sales_account_subdetails', 'sales_account_subdetails.receipt_id', '=', 'sales_receipts.id')
                ->join('customers', 'customers.id', '=', 'sales_receipts.customer_id')
                ->join('sales_order_mode', 'sales_order_mode.order_mode_id', '=', 'sales_receipts.order_mode_id')
                ->join('sales_payment', 'sales_payment.payment_id', '=', 'sales_receipts.payment_id')
                ->join('branch', 'branch.branch_id', '=', 'sales_receipts.branch')
                ->join('user_details', 'user_details.id', '=', 'sales_receipts.userid')
                ->join('sales_order_status', 'sales_order_status.order_status_id', '=', 'sales_receipts.status')
                ->join('terminal_details', 'terminal_details.terminal_id', '=', 'sales_receipts.terminal_id');

            // Apply date filters
            if ($this->fromDate) {
                $query->whereDate('sales_receipts.date', '>=', $this->fromDate);
            }
            if ($this->toDate) {
                $query->whereDate('sales_receipts.date', '<=', $this->toDate);
            }

            // Apply branch filter
            if ($this->branch) {
                $query->where('sales_receipts.branch', $this->branch);
            }

            // Apply terminal filter
            if ($this->terminal) {
                $query->where('sales_receipts.terminal_id', $this->terminal);
            }

            // Apply customer filter
            if ($this->customer) {
                $query->where('sales_receipts.customer_id', $this->customer);
            }

            // Apply user filter
            if ($this->user) {
                $query->where('sales_receipts.userid', $this->user);
            }

            // Apply status filter
            if ($this->status) {
                $query->where('sales_receipts.status', $this->status);
            }

            // Apply order type filter
            if ($this->orderType) {
                $query->where('sales_receipts.order_mode_id', $this->orderType);
            }

            // Apply payment method filter
            if ($this->paymentMethod) {
                $query->where('sales_receipts.payment_id', $this->paymentMethod);
            }

            // Apply custom filters
            foreach ($this->filters as $filter) {
                if ($filter['field'] && $filter['operator'] && $filter['value']) {
                    $field = trim($filter['field']); // 🔥 fix space issue
                    $query->where($field, $filter['operator'], $filter['value']);
                }
            }

            // Build select fields
            $selects = $this->selectedFields;

            // Add calculated fields
            foreach ($this->calculatedFields as $calc) {
                if ($calc['name'] && $calc['formula']) {
                    $formula = trim($calc['formula']);
                    $selects[] = DB::raw("{$formula} AS {$calc['name']}");
                }
            }

            $query->select($selects);

            // Group By
            if (!empty($this->groupByFields)) {
                $query->groupBy($this->groupByFields);
            }

            // Get total count for pagination
            $totalResults = $query->count();
            $this->totalResults = $totalResults;
            $this->lastPage = ceil($totalResults / $this->perPage);

            // Apply pagination
            $this->reportResults = $query->skip(($this->currentPage - 1) * $this->perPage)
                                       ->take($this->perPage)
                                       ->get()
                                       ->toArray();
        } catch (\Exception $e) {
            session()->flash('error', 'Error generating report: ' . $e->getMessage());
        } finally {
            $this->isGenerating = false;
        }
    }

    public function removeFilter($index)
    {
        unset($this->filters[$index]);
        $this->filters = array_values($this->filters); // Reindex the array
    }

    public function updatedGroupByFields($value)
    {
        $this->dispatch('updateSelect2', value: $value);
    }

    public function updatedPerPage($value)
    {
        $this->currentPage = 1; // Reset to first page when changing items per page
        $this->generateReport();
    }

    public function nextPage()
    {
        if ($this->currentPage < $this->lastPage) {
            $this->currentPage++;
            $this->generateReport();
        }
    }

    public function previousPage()
    {
        if ($this->currentPage > 1) {
            $this->currentPage--;
            $this->generateReport();
        }
    }

    public function gotoPage($page)
    {
        if ($page >= 1 && $page <= $this->lastPage) {
            $this->currentPage = $page;
            $this->generateReport();
        }
    }

    public function render()
    {
        return view('livewire.report-builder');
    }
}
