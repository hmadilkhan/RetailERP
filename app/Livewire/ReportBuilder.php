<?php

namespace App\Livewire;

use App\Models\Branch;
use App\Models\Order;
use App\Models\OrderMode;
use App\Models\OrderPayment;
use App\Models\OrderStatus;
use App\Models\Terminal;
use App\Models\User;
use App\Models\UserAuthorization;
use Livewire\Component;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Title;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\ReportExport;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class ReportBuilder extends Component
{
    #[Title('Report Builder')]
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
    public $showOrderDetails = false;
    public $selectGroup = [];
    
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
        'Order Details' => [
            ['value' => 'inventory_general.item_code', 'label' => 'SKU'],
            ['value' => 'inventory_general.product_name', 'label' => 'Product Name'],
            ['value' => 'sales_receipt_details.total_qty', 'label' => 'Item Quantity'],
            ['value' => 'sales_receipt_details.item_price', 'label' => 'Item Price'],
            ['value' => 'sales_receipt_details.total_amount', 'label' => 'Item Total Amount'],
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

    public function updated($key, $value)
    {
        $explode = Str::of($key)->explode('.');
        $table = ucwords(str_replace('_', ' ', $explode[1]));
        $tableFields = collect($this->availableTables[$table])->pluck('value')->toArray();
     
        if($explode[0] === "selectGroup" && $value === true) {
            $this->selectedFields = array_unique(array_merge($this->selectedFields, $tableFields));
        }else if($explode[0] === "selectGroup" && $value === false) {
            $this->selectedFields = array_diff($this->selectedFields, $tableFields);
        }
    }

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
        // Validate required date fields
        if (empty($this->fromDate) || empty($this->toDate)) {
            $this->dispatch('error', message: 'Both From Date and To Date are required.');
            return;
        }

        // Validate date format and range
        try {
            $fromDate = \Carbon\Carbon::parse($this->fromDate);
            $toDate = \Carbon\Carbon::parse($this->toDate);

            if ($fromDate->gt($toDate)) {
                $this->dispatch('error', message: 'From Date cannot be greater than To Date.');
                return;
            }
        } catch (\Exception $e) {
            $this->dispatch('error', message: 'Invalid date format. Please use YYYY-MM-DD format.');
            return;
        }
        
        $this->isGenerating = true;
        $this->reportResults = null; // Clear results while loading

        try {
            // First, get the main order data
            $mainQuery = DB::table('sales_receipts')
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
                $mainQuery->whereDate('sales_receipts.date', '>=', $this->fromDate);
            }
            if ($this->toDate) {
                $mainQuery->whereDate('sales_receipts.date', '<=', $this->toDate);
            }

            // Apply other filters
            if ($this->branch) {
                $mainQuery->where('sales_receipts.branch', $this->branch);
            }
            if ($this->terminal) {
                $mainQuery->where('sales_receipts.terminal_id', $this->terminal);
            }
            if ($this->customer) {
                $mainQuery->where('sales_receipts.customer_id', $this->customer);
            }
            if ($this->user) {
                $mainQuery->where('sales_receipts.userid', $this->user);
            }
            if ($this->status) {
                $mainQuery->where('sales_receipts.status', $this->status);
            }
            if ($this->orderType) {
                $mainQuery->where('sales_receipts.order_mode_id', $this->orderType);
            }
            if ($this->paymentMethod) {
                $mainQuery->where('sales_receipts.payment_id', $this->paymentMethod);
            }

            // Apply custom filters
            foreach ($this->filters as $filter) {
                if ($filter['field'] && $filter['operator'] && $filter['value']) {
                    $field = trim($filter['field']);
                    $mainQuery->where($field, $filter['operator'], $filter['value']);
                }
            }
         
            // Separate main fields and order detail fields
            $mainFields = [];
            $orderDetailFields = [];
            foreach ($this->selectedFields as $field) {
                if (strpos($field, 'sales_receipt_details.') === 0 || strpos($field, 'inventory_general.') === 0) {
                    $orderDetailFields[] = $field;
                    $this->showOrderDetails = true;
                } else {
                    $mainFields[] = $field;
                }
            }

            // Add calculated fields to main fields
            foreach ($this->calculatedFields as $calc) {
                if ($calc['name'] && $calc['formula']) {
                    $formula = trim($calc['formula']);
                    $mainFields[] = DB::raw("{$formula} AS {$calc['name']}");
                }
            }
            
            array_push($mainFields, 'sales_receipts.id');
           
            // Get main order data
            $mainQuery->select($mainFields);
            
            // Group By for main query
            if (!empty($this->groupByFields)) {
                $mainQuery->groupBy($this->groupByFields);
            }
        
            // Get total count for pagination
            $totalResults = $mainQuery->count();
            $this->totalResults = $totalResults;
            $this->lastPage = ceil($totalResults / $this->perPage);
            // Get paginated main results
            $mainResults = $mainQuery->skip(($this->currentPage - 1) * $this->perPage)
                                   ->take($this->perPage)
                                   ->get()
                                   ->toArray();

            // If order details are selected, fetch them for each order
            if ($this->showOrderDetails && !empty($orderDetailFields)) {
                $orderIds = array_column($mainResults, 'id');
                
                $detailsQuery = DB::table('sales_receipt_details')
                ->join('inventory_general', 'inventory_general.id', '=', 'sales_receipt_details.item_code')
                ->whereIn('sales_receipt_details.receipt_id', $orderIds)
                ->select(array_merge(['sales_receipt_details.receipt_id'], $orderDetailFields));
                
                $orderDetails = $detailsQuery->get()->groupBy('receipt_id');
                
                // Convert array to collection of objects
                $mainResults = collect($mainResults)->map(function($order) {
                    return (object) $order;
                });
                
                // Attach order details to main results
                foreach ($mainResults as $order) {
                    $order->details = $orderDetails[$order->id] ?? collect();
                }
            }
            $this->isGenerating = false;

            $this->reportResults = $mainResults;

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

    public function exportToExcel()
    {
        $this->isGenerating = true;
        
        try {
            // Get all results without pagination
            $mainQuery = DB::table('sales_receipts')
                ->join('sales_account_general', 'sales_account_general.receipt_id', '=', 'sales_receipts.id')
                ->join('sales_account_subdetails', 'sales_account_subdetails.receipt_id', '=', 'sales_receipts.id')
                ->join('customers', 'customers.id', '=', 'sales_receipts.customer_id')
                ->join('sales_order_mode', 'sales_order_mode.order_mode_id', '=', 'sales_receipts.order_mode_id')
                ->join('sales_payment', 'sales_payment.payment_id', '=', 'sales_receipts.payment_id')
                ->join('branch', 'branch.branch_id', '=', 'sales_receipts.branch')
                ->join('user_details', 'user_details.id', '=', 'sales_receipts.userid')
                ->join('sales_order_status', 'sales_order_status.order_status_id', '=', 'sales_receipts.status')
                ->join('terminal_details', 'terminal_details.terminal_id', '=', 'sales_receipts.terminal_id');

            // Apply all the same filters as in generateReport()
            if ($this->fromDate) {
                $mainQuery->whereDate('sales_receipts.date', '>=', $this->fromDate);
            }
            if ($this->toDate) {
                $mainQuery->whereDate('sales_receipts.date', '<=', $this->toDate);
            }
            if ($this->branch) {
                $mainQuery->where('sales_receipts.branch', $this->branch);
            }
            if ($this->terminal) {
                $mainQuery->where('sales_receipts.terminal_id', $this->terminal);
            }
            if ($this->customer) {
                $mainQuery->where('sales_receipts.customer_id', $this->customer);
            }
            if ($this->user) {
                $mainQuery->where('sales_receipts.userid', $this->user);
            }
            if ($this->status) {
                $mainQuery->where('sales_receipts.status', $this->status);
            }
            if ($this->orderType) {
                $mainQuery->where('sales_receipts.order_mode_id', $this->orderType);
            }
            if ($this->paymentMethod) {
                $mainQuery->where('sales_receipts.payment_id', $this->paymentMethod);
            }

            // Apply custom filters
            foreach ($this->filters as $filter) {
                if ($filter['field'] && $filter['operator'] && $filter['value']) {
                    $field = trim($filter['field']);
                    $mainQuery->where($field, $filter['operator'], $filter['value']);
                }
            }

            // Get all fields
            $mainFields = [];
            $orderDetailFields = [];
            foreach ($this->selectedFields as $field) {
                if (strpos($field, 'sales_receipt_details.') === 0 || strpos($field, 'inventory_general.') === 0) {
                    $orderDetailFields[] = $field;
                } else {
                    $mainFields[] = $field;
                }
            }

            // Add calculated fields
            foreach ($this->calculatedFields as $calc) {
                if ($calc['name'] && $calc['formula']) {
                    $formula = trim($calc['formula']);
                    $mainFields[] = DB::raw("{$formula} AS {$calc['name']}");
                }
            }

            array_push($mainFields, 'sales_receipts.id');
            
            // Get all results
            $mainResults = $mainQuery->select($mainFields)->get();

            // If order details are selected, fetch them
            if (!empty($orderDetailFields)) {
                $orderIds = $mainResults->pluck('id')->toArray();
                
                $detailsQuery = DB::table('sales_receipt_details')
                    ->join('inventory_general', 'inventory_general.id', '=', 'sales_receipt_details.item_code')
                    ->whereIn('sales_receipt_details.receipt_id', $orderIds)
                    ->select(array_merge(['sales_receipt_details.receipt_id'], $orderDetailFields));
                
                $orderDetails = $detailsQuery->get()->groupBy('receipt_id');
                
                // Attach details to main results
                foreach ($mainResults as $result) {
                    $result->details = $orderDetails[$result->id] ?? collect();
                }
            }

            // Generate Excel file
            return Excel::download(new ReportExport($mainResults, $this->selectedFields, $this->availableTables), 'report-' . now()->format('Y-m-d') . '.xlsx');
            
        } catch (\Exception $e) {
            session()->flash('error', 'Error exporting to Excel: ' . $e->getMessage());
        } finally {
            $this->isGenerating = false;
        }
    }

    public function render()
    {
        return view('livewire.report-builder');
    }
}
