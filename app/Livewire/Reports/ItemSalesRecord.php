<?php

namespace App\Livewire\Reports;

use App\Models\Branch;
use App\Models\Inventory;
use App\Models\InventoryDepartment;
use App\Models\InventorySubDepartment;
use App\Models\OrderDetails;
use App\Models\OrderStatus;
use App\Models\Terminal;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class ItemSalesRecord extends Component
{
    // FIELDS
    public $dateFrom = '';
    public $dateTo = '';
    public $branch = '';
    public $terminal = '';
    public $status = '';
    public $department = '';
    public $subDepartment = '';
    public $product = '';
    public $type = '';
    public $mode = '';
    // ARRAYS 
    public $results = [];
    public $branches = [];
    public $terminals = [];
    public $statuses = [];
    public $departments = [];
    public $subDepartments = [];
    public $products = [];
    // Loading state
    public $isGenerating = false;

    protected $listeners = ['refreshSelect2'];

    public function mount()
    {
        $this->branches = Branch::where("company_id", session("company_id"))->get();
        $this->statuses = OrderStatus::orderBy("order_status_id")->get();
        $this->departments = InventoryDepartment::where("company_id", session("company_id"))->get();
        $this->type = "declaration";
        $this->mode = "all";
        $this->status = "all";
    }

    public function refreshSelect2()
    {
        $this->dispatch('initializeSelect2');
    }

    public function updatedBranch($value)
    {
        if ($value) {
            $this->terminals = Terminal::where("branch_id", $value)->get();
            $this->terminal = ''; // Reset terminal selection when branch changes
            $this->refreshSelect2();
        } else {
            $this->terminals = collect();
            $this->terminal = '';
        }
    }

    public function updatedDepartment($value)
    {
        if ($value != "" && $value != "all") {
            $this->subDepartments = InventorySubDepartment::where("department_id", $value)->get();
            $this->refreshSelect2();
        }
    }

    public function updatedSubDepartment($value)
    {
        if ($value) {
            $this->products = Inventory::where("department_id", $this->department)->where("sub_department_id", $value)->get();
            $this->refreshSelect2();
        }
    }

    public function generateReport()
    {
        $this->isGenerating = true;
    //     dd(
    //     $this->dateFrom,
    //     $this->dateTo,
    //     $this->terminal,
    //    $this->type,
    //     $this->department, // department
    //     $this->subDepartment, // subdepartment
    //     $this->status,
    //     $this->status,
    //     $this->product );

        $this->results = $this->getItemSalesDetails(
            $this->dateFrom,
            $this->dateTo,
            $this->terminal,
            $this->type, // type
            $this->department, // department
            $this->subDepartment, // subdepartment
            $this->status,
            $this->status,
            $this->product // inventory
        );
        // dd($this->results);
        
        $this->isGenerating = false;
    }

    
    public static function getItemSalesDetails($fromdate, $todate, $terminalid, $type, $department, $subdepartment, $mode, $status, $inventory)
    {
        $query = OrderDetails::select([
            'inventory_general.id as itemId',
            'inventory_general.item_code as code',
            'inventory_general.product_name',
            DB::raw('SUM(sales_receipt_details.total_qty) as qty'),
            DB::raw('SUM(sales_receipt_details.total_amount) as amount'),
            'sales_receipt_details.item_price as price',
            DB::raw('SUM(sales_receipt_details.total_cost) as cost'),
            'sales_receipts.void_receipt',
            'inventory_general.weight_qty',
            'sales_receipt_details.is_sale_return',
            'sales_order_status.order_status_name',
            'sales_order_mode.order_mode as ordermode',
            'inventory_general.department_id'
        ])
        ->join('sales_receipts', 'sales_receipts.id', '=', 'sales_receipt_details.receipt_id')
        ->join('inventory_general', 'inventory_general.id', '=', 'sales_receipt_details.item_code')
        ->join('sales_order_status', 'sales_order_status.order_status_id', '=', 'sales_receipts.status')
        ->join('sales_order_mode', 'sales_order_mode.order_mode_id', '=', 'sales_receipts.order_mode_id')
        ->whereIn('sales_receipt_details.receipt_id', function($query) use ($fromdate, $todate, $terminalid) {
            $query->select('id')
                ->from('sales_receipts')
                ->whereIn('opening_id', function($q) use ($fromdate, $todate, $terminalid) {
                    $q->select('opening_id')
                        ->from('sales_opening')
                        ->whereBetween('date', [$fromdate, $todate])
                        ->where('terminal_id', $terminalid);
                })
                ->where('web', 0);
        });

        if ($department != "") {
            $query->whereIn('inventory_general.department_id', $department);
        }

        if ($subdepartment != "") {
            $query->where('inventory_general.sub_department_id', $subdepartment);
        }

        if ($inventory != "") {
            $query->where('inventory_general.id', $inventory);
        }

        if ($mode != "all") {
            $query->where('sales_receipts.order_mode_id', $mode);
        }

        if ($status != "all") {
            $query->where('sales_receipts.status', $mode);
        }

        return $query->groupBy('sales_receipt_details.item_code', 'sales_receipts.status')
                    ->take(10)->get();
    }
    
    public function render()
    {
        return view('livewire.reports.item-sales-record');
    }
}
