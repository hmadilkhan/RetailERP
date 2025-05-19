<?php

namespace App\Livewire\Reports;

use App\Models\Branch;
use App\Models\Order;
use App\Models\Terminal;
use Livewire\Component;
use App\Exports\Reports\SalesGeneralExport;
use Maatwebsite\Excel\Facades\Excel;

class SalesGeneral extends Component
{
    // FIELDS
    public $dateFrom = '';
    public $dateTo = '';
    public $branch = '';
    public $terminal = '';
    public $customer = '';
    // ARRAYS 
    public $results = [];
    public $branches = [];
    public $terminals = [];

    // Loading state
    public $isGenerating = false;

    // public function updated($field)
    // {
    //     // $this->generateReport();
    // }

    public function mount()
    {
        $this->branches = Branch::where("company_id", session("company_id"))->get();
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

    public function generateReport()
    {
        $this->isGenerating = true;

        $order = Order::query();
        $order->with([
            "orderdetails" => function($query) {
                $query->select('receipt_id')
                    ->selectRaw('COUNT(DISTINCT item_code) as total_items')
                    ->selectRaw('SUM(total_qty) as total_qty')
                    ->groupBy('receipt_id');
            },
            "customer",
            "terminal",
            "branch",
            "payment",
            "mode",
            "orderAccount",
            "orderAccountSub"
        ]);
        if ($this->branch) {
            $order->where("branch", $this->branch);
        }

        if ($this->terminal) {
            $order->where("terminal_id", $this->terminal);
        }

        if ($this->customer) {
            $order->where("customer_id", $this->customer);
        }

        if ($this->dateFrom) {
            $order->where("date", ">=", $this->dateFrom);
        }

        if ($this->dateTo) {
            $order->where("date", "<=", $this->dateTo);
        }

        $orders = $order->get();

        $this->results = $orders;

        $this->isGenerating = false;
    }

    public function exportToExcel()
    {
        $this->isGenerating = true;

        $export = new SalesGeneralExport(
            $this->dateFrom,
            $this->dateTo,
            $this->branch,
            $this->terminal,
            $this->customer
        );

        $this->isGenerating = false;
        
        return Excel::download($export, 'sales-general-report.xlsx');
    }

    public function render()
    {
        return view('livewire.reports.sales-general');
    }
}
