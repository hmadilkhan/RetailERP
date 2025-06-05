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
    public $type = 'productwise';

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

        // Select a.stock_report_id,a.date,a.product_id,a.foreign_id,a.branch_id,SUM(a.qty),SUM(a.stock),b.weight_qty,c.grn_id,e.fullname,a.narration from inventory_stock_report_table a INNER JOIN inventory_general b on b.id = a.product_id LEFT JOIN inventory_stock c on c.stock_id = a.foreign_id and a.adjustment_mode != "NULL" LEFT JOIN purchase_rec_gen d on d.rec_id = c.grn_id LEFT JOIN user_details e on e.id = d.user_id where DATE(a.date) between "2025-05-01" and "2025-05-31" group by a.narration,a.product_id;


        // $query = DB::table('inventory_stock_report_table as a')
        //     ->select([
        //         'a.stock_report_id',
        //         'a.date',
        //         'a.product_id',
        //         'a.foreign_id',
        //         'a.branch_id',
        //         DB::raw('SUM(a.qty) as qty'),
        //         DB::raw('SUM(a.stock) as stock'),
        //         'b.weight_qty',
        //         'c.grn_id',
        //         'e.fullname',
        //         'a.narration'
        //     ])
        //     ->join('inventory_general as b', 'b.id', '=', 'a.product_id')
        //     ->leftJoin('inventory_stock as c', function ($join) {
        //         $join->on('c.stock_id', '=', 'a.foreign_id')
        //             ->where('a.adjustment_mode', '!=', 'NULL');
        //     })
        //     ->leftJoin('purchase_rec_gen as d', 'd.rec_id', '=', 'c.grn_id')
        //     ->leftJoin('user_details as e', 'e.id', '=', 'd.user_id')
        //     ->whereBetween(DB::raw('DATE(a.date)'), ['2025-05-01', '2025-05-31'])
        //     ->groupBy('a.narration', 'a.product_id')
        //     ->get();

        $query = InventoryStockReport::query();

        if ($this->type == 'productwise') {
            $query->select('inventory_stock_report_table.*', 'inventory_general.weight_qty', 'inventory_stock.grn_id', 'user_details.fullname', 'inventory_general.product_name');
        } else {
            $query->select(
                'inventory_stock_report_table.stock_report_id',
                'inventory_stock_report_table.product_id',
                'inventory_stock_report_table.date',
                'inventory_stock_report_table.foreign_id',
                'inventory_stock_report_table.branch_id',
                'inventory_stock_report_table.narration',
                // DB::raw('SUM(inventory_stock_report_table.qty) as qty'),
                // DB::raw('SUM(inventory_stock_report_table.stock) as stock'),
                DB::raw('SUM(CASE WHEN inventory_stock_report_table.narration = "Stock Opening" THEN inventory_stock_report_table.qty ELSE 0 END) as opening_stock'),
                DB::raw('SUM(CASE WHEN inventory_stock_report_table.narration = "Sales" THEN inventory_stock_report_table.qty ELSE 0 END) as sales'),
                DB::raw('SUM(CASE WHEN inventory_stock_report_table.narration = "Stock Return" THEN inventory_stock_report_table.qty ELSE 0 END) as stock_return'),
                DB::raw('SUM(CASE WHEN inventory_stock_report_table.narration = "Stock Purchase" THEN inventory_stock_report_table.qty ELSE 0 END) as stock_purchase'),
                DB::raw('SUM(inventory_stock_report_table.stock) as closing_stock'),
                'inventory_general.weight_qty',
                'inventory_general.product_name',
                'inventory_stock.grn_id',
                'user_details.fullname'
            );
        }

        $query->join('inventory_general', 'inventory_general.id', '=', 'inventory_stock_report_table.product_id')
            ->leftJoin('inventory_stock', function ($join) {
                $join->on('inventory_stock.stock_id', '=', 'inventory_stock_report_table.foreign_id')
                    ->where('inventory_stock_report_table.adjustment_mode', '!=', 'NULL');
            })
            ->leftJoin('purchase_rec_gen', 'purchase_rec_gen.rec_id', '=', 'inventory_stock.grn_id')
            ->leftJoin('user_details', 'user_details.id', '=', 'purchase_rec_gen.user_id');
        if ($this->type == 'productwise') {
            $query->where('inventory_stock_report_table.product_id', $this->product);
        }
        $query->where('inventory_stock_report_table.branch_id', session('branch'))
            ->whereBetween('inventory_stock_report_table.date', [$this->dateFrom, $this->dateTo]);

        if ($this->type == 'consolidated') {
            $query->groupBy('inventory_stock_report_table.narration', 'inventory_stock_report_table.product_id','inventory_general.product_name');
        }

        $this->results = $query->get();
        // dd($query->getBindings());
        // dd($this->results);
        $this->isGenerating = false;
    }


    public function render()
    {
        return view('livewire.reports.stock-report');
    }
}
