<?php

namespace App\Livewire\Reports;

use App\Models\Branch;
use App\Models\Inventory;
use App\Models\InventoryDepartment;
use App\Models\InventorySubDepartment;
use App\Models\OrderDetails;
use App\Models\OrderStatus;
use App\Models\Terminal;
use App\report;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use App\Exports\ProductSalesExport;
use Maatwebsite\Excel\Facades\Excel;

class ProductSalesReport extends Component
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

    public function mount()
    {
        $this->branches = Branch::where("company_id", session("company_id"))->get();
        $this->statuses = OrderStatus::orderBy("order_status_id")->get();
        $this->departments = InventoryDepartment::where("company_id", session("company_id"))->get();
        $this->type = "declaration";
        $this->status = "all";
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

    public function resetFilters()
    {
        $this->reset([
            'dateFrom',
            'dateTo',
            'branch',
            'terminal',
            'type',
            'department',
            'subDepartment',
            'product',
            'status'
        ]);

        // Reset Select2 dropdowns
        $this->dispatch('initializeSelect2');
    }

    public function generateReport()
    {
       

        // Validate required fields
        $this->validate([
            'dateFrom' => 'required|date',
            'dateTo' => 'required|date|after_or_equal:dateFrom',
            'branch' => 'required',
            'terminal' => 'required|exists:terminal_details,terminal_id',
            'type' => 'required|in:declaration,sales',
            'department' => 'required',
            'subDepartment' => 'required',
            'product' => 'required'
        ], [
            'dateFrom.required' => 'Start date is required',
            'dateTo.required' => 'End date is required',
            'dateTo.after_or_equal' => 'End date must be greater than or equal to start date',
            'branch.required' => 'Please select a branch',
            'branch.exists' => 'Selected branch is invalid',
            'terminal.required' => 'Please select a terminal',
            'terminal.exists' => 'Selected terminal is invalid',
            'type.required' => 'Report type is required',
            'type.in' => 'Invalid report type selected',
            'department.exists' => 'Selected department is invalid',
            'subDepartment.exists' => 'Selected sub-department is invalid',
            'product.exists' => 'Selected product is invalid'
        ]);

        $this->isGenerating = true;

        $this->results = $this->getItemSalesDetails(
            $this->dateFrom,
            $this->dateTo,
            $this->terminal,
            $this->type,
            $this->department,
            $this->subDepartment,
            $this->status,
            $this->status,
            $this->product
        );

        $this->isGenerating = false;
    }

    public static function getItemSalesDetails($fromdate, $todate, $terminalid, $type, $department, $subdepartment, $mode, $status, $inventory)
    {
        $query = OrderDetails::select([
            'inventory_general.id as itemId',
            'inventory_general.item_code as code',
            'inventory_general.product_name',
            'sales_receipt_details.receipt_id',
            'sales_receipts.receipt_no',
            'sales_receipts.date',
            'sales_receipt_details.total_qty as qty',
            'sales_receipt_details.total_amount as total_amount',
            'sales_receipt_details.item_price as price',
            'sales_receipt_details.total_cost as cost',
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
            ->whereIn('sales_receipt_details.receipt_id', function ($query) use ($fromdate, $todate, $terminalid) {
                $query->select('id')
                    ->from('sales_receipts')
                    ->whereIn('opening_id', function ($q) use ($fromdate, $todate, $terminalid) {
                        $q->select('opening_id')
                            ->from('sales_opening')
                            ->whereBetween('date', [$fromdate, $todate])
                            ->where('terminal_id', $terminalid);
                    })
                    ->where('web', 0);
            });

        if ($department != "") {
            $query->where('inventory_general.department_id', $department);
        }

        if ($subdepartment != "") {
            $query->where('inventory_general.sub_department_id', $subdepartment);
        }

        if ($inventory != "") {
            $query->where('inventory_general.id', $inventory);
        }

        return $query->get();
    }

    public function exportToExcel()
    {
        $data = $this->getItemSalesDetails(
            $this->dateFrom,
            $this->dateTo,
            $this->terminal,
            $this->type,
            $this->department,
            $this->subDepartment,
            $this->status,
            $this->status,
            $this->product
        );

        return Excel::download(new ProductSalesExport($data), 'product-sales-report.xlsx');
    }

    public function exportToPdf()
    {
        $this->isGenerating = true;

        $data = $this->getItemSalesDetails(
            $this->dateFrom,
            $this->dateTo,
            $this->terminal,
            $this->type,
            $this->department,
            $this->subDepartment,
            $this->status,
            $this->status,
            $this->product
        );

        // Initialize MPDF
        $mpdf = new \Mpdf\Mpdf([
            'mode' => 'utf-8',
            'format' => 'A4',
            'margin_left' => 15,
            'margin_right' => 15,
            'margin_top' => 15,
            'margin_bottom' => 15,
            'margin_header' => 10,
            'margin_footer' => 10
        ]);

        // Add company info
        $company = \App\Models\Company::where('company_id', session('company_id'))->first();
        $branchname = "";
        if ($this->branch) {
            $branch = Branch::where("branch_id", $this->branch)->first();
            $branchname = " (" . $branch->branch_name . ") ";
        } else {
            $branchname = " (All Branches)";
        }

        // Build HTML content
        $html = '
        <html>
        <head>
            <style>
                body { font-family: Arial, sans-serif; }
                table { width: 100%; border-collapse: collapse; margin-top: 10px; }
                th { background-color: #000000; color: #FFFFFF; padding: 8px; text-align: left; }
                td { padding: 8px; border-bottom: 1px solid #ddd; }
                .header { text-align: center; margin-bottom: 20px; }
                .total-row { background-color: #000000; color: #FFFFFF; font-weight: bold; }
            </style>
        </head>
        <body>
            <div class="header">
                <h2>' . $company->name . '</h2>
                <h3>Product Sales Report' . $branchname . '</h3>
                <p>From: ' . date('d M Y', strtotime($this->dateFrom)) . ' To: ' . date('d M Y', strtotime($this->dateTo)) . '</p>
            </div>
            <table>
                <thead>
                    <tr>
                        <th>Item Code</th>
                        <th>Product Name</th>
                        <th>Receipt No</th>
                        <th>Date</th>
                        <th>Quantity</th>
                        <th>Price</th>
                        <th>Total Amount</th>
                        <th>Cost</th>
                        <th>Order Status</th>
                        <th>Order Mode</th>
                        <th>Is Return</th>
                    </tr>
                </thead>
                <tbody>';

        $totalQty = 0;
        $totalAmount = 0;
        $totalCost = 0;

        foreach ($data as $row) {
            $html .= sprintf(
                '<tr>
                    <td>%s</td>
                    <td>%s</td>
                    <td>%s</td>
                    <td>%s</td>
                    <td>%s</td>
                    <td>%s</td>
                    <td>%s</td>
                    <td>%s</td>
                    <td>%s</td>
                    <td>%s</td>
                    <td>%s</td>
                </tr>',
                $row->code,
                $row->product_name,
                $row->receipt_no,
                date('d M Y', strtotime($row->date)),
                number_format($row->qty, 2),
                number_format($row->price, 2),
                number_format($row->total_amount, 2),
                number_format($row->cost, 2),
                $row->order_status_name,
                $row->ordermode,
                $row->is_sale_return ? 'Yes' : 'No'
            );

            $totalQty += $row->qty;
            $totalAmount += $row->total_amount;
            $totalCost += $row->cost;
        }

        // Add totals row
        $html .= sprintf(
            '<tr class="total-row">
                <td colspan="4">Total</td>
                <td>%s</td>
                <td>-</td>
                <td>%s</td>
                <td>%s</td>
                <td colspan="3">-</td>
            </tr>',
            number_format($totalQty, 2),
            number_format($totalAmount, 2),
            number_format($totalCost, 2)
        );

        $html .= '</tbody></table></body></html>';

        // Write HTML to PDF
        $mpdf->WriteHTML($html);

        $this->isGenerating = false;

        // Output PDF
        return response()->streamDownload(function() use ($mpdf) {
            $mpdf->Output('Product_Sales_Report.pdf', 'I');
        }, 'Product_Sales_Report.pdf');
    }

    public function render()
    {
        return view('livewire.reports.product-sales-report');
    }
}
