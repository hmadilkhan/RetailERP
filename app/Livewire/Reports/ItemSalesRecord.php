<?php

namespace App\Livewire\Reports;

use App\Models\Branch;
use App\Models\Company;
use App\Models\Inventory;
use App\Models\InventoryDepartment;
use App\Models\InventorySubDepartment;
use App\Models\OrderDetails;
use App\Models\OrderStatus;
use App\Models\Terminal;
use App\report;
use App\Exports\Reports\ItemSalesRecordExport;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Maatwebsite\Excel\Facades\Excel;

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

    public function exportToExcel()
    {
        $report = new report();
        $data = [];
        $branchname = "";

        if ($this->branch != "all") {
            $branchname = Branch::where("branch_id", $this->branch)->first();
            $branchname = " (" . $branchname->branch_name . ") ";
        } else {
            $branchname = " ( All Branches )";
        }

        // Process terminals and create data
        if ($this->terminal == 0) {
            $terminals = $report->getTerminals($this->branch);
        } else {
            $terminals = $report->get_terminals_byid($this->terminal);
        }

        foreach ($terminals as $terminal) {
            $modes = $report->itemSalesOrderMode(
                $this->dateFrom,
                $this->dateTo,
                $terminal->terminal_id,
                $this->mode,
                $this->status
            );

            if (empty($modes)) {
                continue;
            }

            foreach ($modes as $mode) {
                $details = $report->itemsale_details(
                    $this->dateFrom,
                    $this->dateTo,
                    $terminal->terminal_id,
                    $mode->order_mode_id,
                    $this->department,
                    $this->subDepartment,
                    $this->mode,
                    $this->status,
                    $this->product
                );

                if (!empty($details)) {
                    foreach ($details as $item) {
                        if ($item->void_receipt != 1) {
                            $data[] = [
                                'code' => $item->code,
                                'product_name' => $item->product_name,
                                'qty' => number_format($item->qty),
                                'price' => number_format($item->price),
                                'amount' => number_format($item->amount),
                                'cost' => number_format($item->cost),
                                'margin' => number_format($item->amount - $item->cost),
                                'status' => $item->order_status_name
                            ];
                        }
                    }
                }
            }
        }

        $title = 'Item Sale Database' . $branchname;
        return Excel::download(new ItemSalesRecordExport($data, $title), 'Item_Sale_Database.xlsx');
    }

    public function exportToPdf(report $report)
    {
        $company = Company::where("company_id", session('company_id'))->first();
        $departments = [];
        $branchname = "";

        if ($this->branch != "all") {
            $branchname = Branch::where("branch_id", $this->branch)->first();
            $branchname = " (" . $branchname->branch_name . ") ";
        } else {
            $branchname = " ( All Branches )";
        }

        if (is_array($this->department)) {
            $departments = InventoryDepartment::whereIn("department_id", $this->department)
                ->select("department_id", "department_name")
                ->get();
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
    <html dir="ltr">
    <head>
        <style>
            body { font-family: jameel-noori-nastaleeq; }
              h1{
                    line-height: 0.6;
                }
                .text-bold{
                    font-weight: bold;
                }
                p{
                    font-size: 14px;
                    line-height: 0.9;
                    margin: 5px 0;
                }
                h2{
                    font-size: 14px;
                    line-height: 0.6;
                    color: green;
                }
                .text-center{
                    text-align: center;
                }
                table {
                    width: 100%;
                    border-collapse: collapse;
                }
                td {
                    vertical-align: top;
                    padding: 5px;
                }
                .right-align {
                    text-align: right;
                }
                .company-info {
                    width: 50%;
                }
                .qr-section {
                    width: 50%;
                }
                table {
                    width: 100%;
                    border-collapse: collapse;
                    margin-bottom: 20px;
                }

        
                .header-row td {
                    font-size: 18px;
                    font-weight: bold;
                    padding: 10px;
                    text-align: center;
                    background-color: #f8f9fa;
                    border-bottom: 2px solid #dee2e6;
                }

            
                thead th {
                    background-color: #1a4567;
                    color: white;
                    padding: 12px 8px;
                    text-align: center;
                    font-weight: bold;
                    border: 1px solid #0d2235;
                    font-size: 14px;
                }

        
                tbody td {
                    padding: 8px;
                    text-align: center;
                    border: 1px solid #dee2e6;
                    font-size: 13px;
                }

    
                tbody tr:nth-child(even) {
                    background-color: #f8f9fa;
                }
                .summary { 
                    margin-top: 20px;
                    background-color: #f9f9f9;
                    padding: 10px;
                }
          
            .void { background-color: #ffcccc; }
            .return { background-color: #ffd9cc; }
            .normal { background-color: #f2f2f2; }
        </style>
    </head>
    <body>';

        $html .= '
        <table>
            <tr>
               <td class="company-info">
                    <table style="width: auto;">
                        <tr>
                            <td>
                                <img width="100" height="100" src="' . asset('storage/images/company/' . $company->logo) . '" alt="">
                            </td>
                            <td style="padding-left: 16px;">
                                <p>Company Name:</p>
                                <h4 class="text-bold">' . $company->name . '</h4>
                                <p>Contact Number</p>
                                <p class="text-bold">0' . $company->ptcl_contact . '</p>
                                <p>Company Address</p>
                                <p class="text-bold">' . $company->address . '</p>
                            </td>
                        </tr>
                    </table>
                </td>
                <td class="qr-section" style="width: 100px; text-align: right;">
                    <img width="100" height="100" src="' . asset('storage/images/company/qrcode.png') . '" alt="" style="margin-left: auto; display: block;">
                </td>
            </tr>
        </table>';

        // Add date range
        $fromdate = date('Y-m-d', strtotime($this->dateFrom));
        $todate = date('Y-m-d', strtotime($this->dateTo));
        $html .= '<h4 style="text-align: center;">Date: ' . $fromdate . ' From ' . $todate . ' To </h4>';
        $html .= '<h2 style="text-align: center;">Item Sale Database' . $branchname . '</h2>';

        // Process terminals and create tables
        if ($this->terminal == 0) {
            $terminals = $report->getTerminals($this->branch);
        } else {
            $terminals = $report->get_terminals_byid($this->terminal);
        }

        foreach ($terminals as $terminal) {
            $html .= '<h3 style="text-align: center;background-color: #1a4567;color: #FFFFFF;">Terminal: ' . $terminal->terminal_name . '</h3>';

            $modes = $report->itemSalesOrderMode(
                $this->dateFrom,
                $this->dateTo,
                $terminal->terminal_id,
                $this->mode,
                $this->status
            );

            if (empty($modes)) {
                // If no modes found for this terminal
                $html .= '<div style="text-align: center; padding: 20px; background-color: #f9f9f9; border: 1px solid #ddd; margin: 10px 0;">
                    <p style="color: #666; font-size: 16px; margin: 0;">Modes are empty<p>
                    <p style="color: #999; font-size: 14px; margin: 5px 0 0 0;">No Data Found</p>
                </div>';
                continue; // Skip to next terminal
            }

            foreach ($modes as $mode) {
                $html .= '<h5 style="text-align: center;background-color: #ddd;color: #000;margin-bottom: -2px;padding: 12px 8px;">' . $mode->ordermode . '</h5>';

                $html .= '
            <table >
                <thead>
                    <tr>
                        <th>Item code</th>
                        <th>Product</th>
                        <th>Qty</th>
                        <th>Price</th>
                        <th>Amount</th>
                        <th>COGS</th>
                        <th>Margin</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>';

                $details = $report->itemsale_details(
                    $this->dateFrom,
                    $this->dateTo,
                    $terminal->terminal_id,
                    $mode->order_mode_id,
                    $this->department,
                    $this->subDepartment,
                    $this->mode,
                    $this->status,
                    $this->product
                );

                if (empty($details)) {
                    // If no details found for this mode
                    $html .= '<div style="text-align: center; padding: 15px; background-color: #f5f5f5; border: 1px solid #eee; margin-bottom: 20px;">
                        <p style="color: #666; font-size: 14px; margin: 0;">No Data Found</p>
                    </div>';
                    continue; // Skip to next mode
                }

                $totalCount = 0;
                $totalQty = 0;
                $totalAmount = 0;
                $totalCost = 0;
                $totalMargin = 0;
                if (!empty($details)) {
                    foreach ($details as $item) {
                        $rowClass = $item->void_receipt == 1 ? 'void' : ($item->is_sale_return == 1 ? 'return' : 'normal');

                        $html .= sprintf(
                            '
                        <tr class="%s">
                            <td>%s</td>
                            <td>%s</td>
                            <td>%s</td>
                            <td>%s</td>
                            <td>%s</td>
                            <td>%s</td>
                            <td>%s</td>
                            <td>%s</td>
                        </tr>',
                            $rowClass,
                            $item->code,
                            $item->product_name,
                            number_format($item->qty),
                            number_format($item->price),
                            number_format($item->amount),
                            number_format($item->cost),
                            number_format($item->amount - $item->cost),
                            $item->order_status_name
                        );
                        if($item->void_receipt != 1){
                            $totalCount++;
                            $totalQty += $item->qty;
                            $totalAmount += $item->amount;
                            $totalCost += $item->cost;
                            $totalMargin += ($item->amount - $item->cost);
                        }
                    }
                } else {
                    $html .= '<tr><td colspan="8" style="text-align: center;">No data found</td></tr>';
                }

                // Add totals row
                $html .= sprintf(
                    '
                <tr style="font-weight: bold;background-color: #00000;color: #FFFFFF;">
                    <td colspan="2" style="color: #FFFFFF;font-weight: bold;">Total Items: %s</td>
                    <td style="color: #FFFFFF;font-weight: bold;">%s</td>
                    <td style="color: #FFFFFF;font-weight: bold;">-</td>
                    <td style="color: #FFFFFF;font-weight: bold;">%s</td>
                    <td style="color: #FFFFFF;font-weight: bold;">%s</td>
                    <td style="color: #FFFFFF;font-weight: bold;">%s</td>
                    <td style="color: #FFFFFF;font-weight: bold;">-</td>
                </tr>',
                    $totalCount,
                    number_format($totalQty),
                    number_format($totalAmount),
                    number_format($totalCost),
                    number_format($totalMargin)
                );

                $html .= '</tbody></table>';
            }
        }

        $html .= '</body></html>';

        // Write HTML to PDF
        $mpdf->WriteHTML($html);

        // Output PDF
        // $mpdf->Output('Item_Sale_Database_Urdu.pdf', 'I');

        return response()->streamDownload(function() use ($mpdf) {
            $mpdf->Output('Item_Sale_Database_Urdu.pdf', 'I');
        }, 'Item_Sale_Database_Urdu.pdf');
    }

    public static function getItemSalesDetails($fromdate, $todate, $terminalid, $type, $department, $subdepartment, $mode, $status, $inventory)
    {
        $query = OrderDetails::select([
            'inventory_general.id as itemId',
            'inventory_general.item_code as code',
            'inventory_general.product_name',
            DB::raw('SUM(sales_receipt_details.total_qty) as qty'),
            DB::raw('SUM(sales_receipt_details.total_amount) as total_amount'),
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
            $query->where('inventory_general.department_id', $department);
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
                    // ->take(10)
                    ->get();
    }
    
    public function render()
    {
        return view('livewire.reports.item-sales-record');
    }
}
