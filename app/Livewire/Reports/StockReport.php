<?php

namespace App\Livewire\Reports;

use App\Models\Inventory;
use App\Models\InventoryDepartment;
use App\Models\InventoryStockReport;
use App\Models\InventorySubDepartment;
use Livewire\Component;
use Illuminate\Support\Facades\DB;

class StockReport extends Component
{
    // FIELDS
    public $dateFrom = '';
    public $dateTo = '';
    public $department = '';
    public $subDepartment = '';
    public $product = '';

    public $results = [];
    public $departments = [];
    public $subDepartments = [];
    public $products = [];

    // Loading state
    public $isGenerating = false;

    public function mount()
    {
        $this->departments = InventoryDepartment::where("company_id", session("company_id"))->get();
    }

    public function updatedDepartment($value)
    {
        if ($value != "" && $value != "all") {
            $this->subDepartments = InventorySubDepartment::where("department_id", $value)->get();
        }
    }

    public function updatedSubDepartment($value)
    {
        if ($value) {
            $this->products = Inventory::where("department_id", $this->department)->where("sub_department_id", $value)->get();
        }
    }

    public function generateReport()
    {
        $this->isGenerating = true;
        
        $this->results = InventoryStockReport::select('inventory_stock_report_table.*', 'inventory_general.weight_qty', 'inventory_stock.grn_id', 'user_details.fullname')
            ->join('inventory_general', 'inventory_general.id', '=', 'inventory_stock_report_table.product_id')
            ->leftJoin('inventory_stock', function($join) {
                $join->on('inventory_stock.stock_id', '=', 'inventory_stock_report_table.foreign_id')
                    ->where('inventory_stock_report_table.adjustment_mode', '!=', 'NULL');
            })
            ->leftJoin('purchase_rec_gen', 'purchase_rec_gen.rec_id', '=', 'inventory_stock.grn_id')
            ->leftJoin('user_details', 'user_details.id', '=', 'purchase_rec_gen.user_id')
            ->where('inventory_stock_report_table.product_id', $this->product)
            ->where('inventory_stock_report_table.branch_id', session('branch'))
            ->whereBetween('inventory_stock_report_table.date', [$this->dateFrom, $this->dateTo])
            ->get();
        $this->isGenerating = false;
    }


    public function render()
    {
        return view('livewire.reports.stock-report');
    }
}
