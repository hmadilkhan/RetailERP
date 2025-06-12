<?php

namespace App\Livewire\Reports;

use App\Models\Inventory;
use App\Models\InventoryDepartment;
use App\Models\InventoryStockReport;
use App\Models\InventorySubDepartment;
use Livewire\Component;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\StockReportProductwiseExport;
use App\Exports\StockReportConsolidatedExport;

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
                DB::raw('SUM(inventory_stock_report_table.qty) as qty'),
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

        $this->isGenerating = false;
    }

    public function exportToExcel()
    {
        $this->isGenerating = true;

        try {
            $fileName = $this->type == 'productwise' ? 'Stock_Report_Productwise' : 'Stock_Report_Consolidated';
            $fileName .= '_' . date('Y-m-d') . '.xlsx';

            $exportClass = $this->type == 'productwise' 
                ? new StockReportProductwiseExport($this->results)
                : new StockReportConsolidatedExport($this->products,$this->results);

            return Excel::download($exportClass, $fileName);
        } catch (\Exception $e) {
            session()->flash('error', 'Error exporting to Excel: ' . $e->getMessage());
        } finally {
            $this->isGenerating = false;
        }
    }

    public function exportToPdf()
    {
        $this->isGenerating = true;

        try {
            $company = \App\Models\Company::where('company_id', session('company_id'))->first();
            $branchname = "";

            if ($this->branch) {
                $branch = \App\Models\Branch::where("branch_id", $this->branch)->first();
                $branchname = " (" . $branch->branch_name . ") ";
            } else {
                $branchname = " (All Branches)";
            }

            // Initialize MPDF with RTL and Unicode support
            $mpdf = new \Mpdf\Mpdf([
                'mode' => 'utf-8',
                'format' => 'A4',
                'default_font' => 'jameel-noori-nastaleeq',
                'default_font_size' => 12,
                'margin_left' => 15,
                'margin_right' => 15,
                'margin_top' => 15,
                'margin_bottom' => 15,
                'margin_header' => 10,
                'margin_footer' => 10,
                'direction' => 'rtl'
            ]);

            // Add custom Urdu font
            $mpdf->fontdata['jameel-noori-nastaleeq'] = [
                'R' => 'Jameel-Noori-Nastaleeq.ttf',
                'useOTL' => 0xFF,
            ];

            // Start building HTML content
            $html = '
            <html dir="rtl">
            <head>
                <style>
                    body { font-family: jameel-noori-nastaleeq, Arial, sans-serif; }
                    table { width: 100%; border-collapse: collapse; margin-top: 10px; }
                    th { background-color: #000000; color: #FFFFFF; padding: 8px; text-align: right; }
                    td { padding: 8px; border-bottom: 1px solid #ddd; text-align: right; }
                    .header { text-align: center; margin-bottom: 20px; }
                    .total-row { background-color: #000000; color: #FFFFFF; font-weight: bold; }
                </style>
            </head>
            <body>
                <div class="header">
                    <h2>' . $company->name . ' - Stock Report' . $branchname . '</h2>
                    <p>From: ' . date('d M Y', strtotime($this->dateFrom)) . ' To: ' . date('d M Y', strtotime($this->dateTo)) . '</p>
                </div>
                <table>
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Product Name</th>
                            <th>Reference No</th>
                            <th>Transaction Type</th>
                            <th>Quantity</th>
                            <th>Stock Balance</th>
                            <th>User</th>
                        </tr>
                    </thead>
                    <tbody>';

            $stock = 0;
            foreach ($this->results as $row) {
                // Calculate stock balance
                if ($row->narration == 'Stock Opening') {
                    $stock = (float) $row->stock;
                } elseif ($row->narration == 'Sales') {
                    $stock = $stock - (preg_match('/Sales/', $row->narration) ? (float) $row->qty ?? (1 / $row->weight_qty ?? 1) : (float) $row->qty ?? 1);
                } elseif ($row->narration == 'Sales Return') {
                    $stock = (float) $stock + (float) $row->qty;
                } elseif ($row->narration == 'Stock Purchase through Purchase Order') {
                    $stock = (float) $stock + (float) $row->qty;
                } elseif ($row->narration == 'Stock Opening from csv file') {
                    $stock = (float) $stock + (float) $row->qty;
                } elseif ($row->narration == 'Stock Return') {
                    $stock = (float) $stock - (float) $row->qty;
                } elseif (preg_match('/Stock Adjustment/', $row->narration)) {
                    $stock = (float) $stock + (float) $row->qty;
                }

                $html .= sprintf(
                    '<tr>
                        <td>%s</td>
                        <td>%s</td>
                        <td>%s</td>
                        <td>%s</td>
                        <td>%s</td>
                        <td>%s</td>
                        <td>%s</td>
                    </tr>',
                    date('d M Y', strtotime($row->date)),
                    $row->product_name,
                    $row->grn_id ?? 'N/A',
                    $row->narration,
                    preg_match('/Sales/', $row->narration) ? $row->qty ?? (1 / $row->weight_qty ?? 1) : $row->qty ?? 1,
                    number_format($stock, 2),
                    $row->fullname ?? 'N/A'
                );
            }

            $html .= '</tbody></table></body></html>';

            // Write HTML to PDF
            $mpdf->WriteHTML($html);

            $this->isGenerating = false;

            // Output PDF
            return response()->streamDownload(function() use ($mpdf) {
                $mpdf->Output('Stock_Report.pdf', 'I');
            }, 'Stock_Report.pdf');

        } catch (\Exception $e) {
            session()->flash('error', 'Error exporting to PDF: ' . $e->getMessage());
            $this->isGenerating = false;
        }
    }
    

    public function render()
    {
        return view('livewire.reports.stock-report');
    }
}
