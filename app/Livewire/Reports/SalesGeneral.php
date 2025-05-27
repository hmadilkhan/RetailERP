<?php

namespace App\Livewire\Reports;

use App\Models\Branch;
use App\Models\Order;
use App\Models\Terminal;
use Livewire\Component;
use App\Exports\Reports\SalesGeneralExport;
use App\Models\OrderMode;
use App\Models\OrderStatus;
use App\Services\OrderService;
use Maatwebsite\Excel\Facades\Excel;
use Mpdf\Mpdf;

class SalesGeneral extends Component
{
    // FIELDS
    public $dateFrom = '';
    public $dateTo = '';
    public $branch = '';
    public $terminal = '';
    public $customer = '';
    public $mode = '';
    public $salesPerson = '';
    public $status = '';

    // MODELS
    public $orderService;

    // ARRAYS 
    public $results = [];
    public $branches = [];
    public $terminals = [];
    public $modes = [];
    public $statuses = [];
    public $serviceProviders = [];

    // Loading state
    public $isGenerating = false;

    public function mount()
    {
        // $this->orderService = $orderService;
        $this->branches = Branch::where("company_id", session("company_id"))->get();
        $this->modes = OrderMode::all();
        $this->statuses = OrderStatus::all();
    }

    public function updatedBranch($value, OrderService $orderService)
    {
        if ($value) {
            $this->terminals = Terminal::where("branch_id", $value)->get();
            $this->serviceProviders = $orderService->getServiceProviders($this->branch);
            $this->terminal = ''; // Reset terminal selection when branch changes
            $this->salesPerson = ''; // Reset terminal selection when branch changes
        } else {
            $this->terminals = collect();
            $this->serviceProviders = collect();
            $this->terminal = '';
            $this->salesPerson = '';
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
            "orderAccountSub",
            "salesperson"
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

        if ($this->mode) {
            $order->where("order_mode_id", $this->mode);
        }

        if ($this->salesPerson) {
            $order->where("sales_person_id", $this->salesPerson);
        }

        if ($this->status) {
            $order->where("status", $this->status);
        }

        $orders = $order->take(5)->get();
        // dd($orders);
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
            $this->customer,
            $this->salesPerson
        );

        $this->isGenerating = false;
        
        return Excel::download($export, 'Sales Invoices.xlsx');
    }

    public function exportToPdf()
    {
        $this->isGenerating = true;

        $company = \App\Models\Company::where('company_id', session('company_id'))->first();
        $branchname = "";

        if ($this->branch) {
            $branchname = Branch::where("branch_id", $this->branch)->first();
            $branchname = " (" . $branchname->branch_name . ") ";
        } else {
            $branchname = " ( All Branches )";
        }

        // Initialize MPDF with RTL and Unicode support
        $mpdf = new Mpdf([
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
                h1 { line-height: 0.6; }
                .text-bold { font-weight: bold; }
                p { font-size: 14px; line-height: 0.9; margin: 5px 0; }
                h2 { font-size: 14px; line-height: 0.6; color: green; }
                .text-center { text-align: center; }
                table { width: 100%; border-collapse: collapse; }
                td { vertical-align: top; padding: 5px; }
                .right-align { text-align: right; }
                .company-info { width: 50%; }
                .qr-section { width: 50%; }
                table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
                .header-row td { font-size: 18px; font-weight: bold; padding: 10px; text-align: center; background-color: #f8f9fa; border-bottom: 2px solid #dee2e6; }
                thead th { background-color: #1a4567; color: white; padding: 12px 8px; text-align: center; font-weight: bold; border: 1px solid #0d2235; font-size: 14px; }
                tbody td { padding: 8px; text-align: center; border: 1px solid #dee2e6; font-size: 13px; }
                tbody tr:nth-child(even) { background-color: #f8f9fa; }
                .summary { margin-top: 20px; background-color: #f9f9f9; padding: 10px; }
                .void { background-color: #ffcccc; }
                .return { background-color: #ffd9cc; }
                .normal { background-color: #f2f2f2; }
            </style>
        </head>
        <body>';

        // Add company header
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
        $html .= '<h4 style="text-align: center;">Date: ' . $this->dateFrom . ' From ' . $this->dateTo . ' To </h4>';
        $html .= '<h2 style="text-align: center;">Sales General Report' . $branchname . '</h2>';

        // Process terminals and create tables
        if ($this->terminal) {
            $terminals = Terminal::where('terminal_id', $this->terminal)->get();
        } else {
            $terminals = Terminal::where('branch_id', $this->branch)->get();
        }

        foreach ($terminals as $terminal) {
            $html .= '<h3 style="text-align: center;background-color: #1a4567;color: #FFFFFF;">Terminal: ' . $terminal->terminal_name . '</h3>';

            $orders = Order::with([
                "orderdetails" => function($query) {
                    $query->select('receipt_id')
                        ->selectRaw('COUNT(DISTINCT item_code) as total_items')
                        ->selectRaw('SUM(total_qty) as total_qty')
                        ->groupBy('receipt_id');
                },
                "customer",
                "terminal",
                "branchrelation",
                "payment",
                "mode",
                "orderAccount",
                "orderAccountSub",
                "salesperson"
            ])
            ->where('terminal_id', $terminal->terminal_id);

            if ($this->dateFrom) {
                $orders->where("date", ">=", $this->dateFrom);
            }

            if ($this->dateTo) {
                $orders->where("date", "<=", $this->dateTo);
            }

            if ($this->customer) {
                $orders->where("customer_id", $this->customer);
            }

            if ($this->salesPerson) {
                $orders->where("sales_person_id", $this->salesPerson);
            }

            if ($this->status) {
                $orders->where("status", $this->status);
            }

            $orders = $orders->get();

            if ($orders->isEmpty()) {
                $html .= '<div style="text-align: center; padding: 20px; background-color: #f9f9f9; border: 1px solid #ddd; margin: 10px 0;">
                    <p style="color: #666; font-size: 16px; margin: 0;">No Data Found</p>
                </div>';
                continue;
            }

            $html .= '
            <table>
                <thead>
                    <tr>
                        <th>Receipt No</th>
                        <th>Date</th>
                        <th>Customer</th>
                        <th>Total Items</th>
                        <th>Total Qty</th>
                        <th>Base Amount</th>
                        <th>Tax</th>
                        <th>Discount</th>
                        <th>Total Amount</th>
                        <th>Order Mode</th>
                        <th>Payment Mode</th>
                        <th>Sales Person</th>
                    </tr>
                </thead>
                <tbody>';

            $totalItems = 0;
            $totalQty = 0;
            $totalBase = 0;
            $totalTax = 0;
            $totalDiscount = 0;
            $totalAmount = 0;

            foreach ($orders as $order) {
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
                        <td>%s</td>
                    </tr>',
                    $order->receipt_no,
                    date('d M Y', strtotime($order->date)),
                    $order->customer ? $order->customer->name : 'N/A',
                    $order->orderdetails->first() ? $order->orderdetails->first()->total_items : 0,
                    $order->orderdetails->first() ? $order->orderdetails->first()->total_qty : 0,
                    number_format($order->actual_amount),
                    number_format($order->orderAccount ? $order->orderAccount->sales_tax_amount : 0),
                    number_format($order->orderAccount ? $order->orderAccount->discount_amount : 0),
                    number_format($order->total_amount),
                    $order->mode ? $order->mode->order_mode : 'N/A',
                    $order->payment ? $order->payment->payment_mode : 'N/A',
                    $order->salesperson ? $order->salesperson->fullname : 'N/A'
                );

                $totalItems += $order->orderdetails->first() ? $order->orderdetails->first()->total_items : 0;
                $totalQty += $order->orderdetails->first() ? $order->orderdetails->first()->total_qty : 0;
                $totalBase += $order->actual_amount;
                $totalTax += $order->orderAccount ? $order->orderAccount->sales_tax_amount : 0;
                $totalDiscount += $order->orderAccount ? $order->orderAccount->discount_amount : 0;
                $totalAmount += $order->total_amount;
            }

            // Add totals row
            $html .= sprintf(
                '<tr style="font-weight: bold;background-color: #000000;color: #FFFFFF;">
                    <td colspan="3" style="color: #FFFFFF;font-weight: bold;">Total</td>
                    <td style="color: #FFFFFF;font-weight: bold;">%s</td>
                    <td style="color: #FFFFFF;font-weight: bold;">%s</td>
                    <td style="color: #FFFFFF;font-weight: bold;">%s</td>
                    <td style="color: #FFFFFF;font-weight: bold;">%s</td>
                    <td style="color: #FFFFFF;font-weight: bold;">%s</td>
                    <td style="color: #FFFFFF;font-weight: bold;">%s</td>
                    <td colspan="3" style="color: #FFFFFF;font-weight: bold;">%s</td>   
                </tr>',
                number_format($totalItems),
                number_format($totalQty),
                number_format($totalBase),
                number_format($totalTax),
                number_format($totalDiscount),
                number_format($totalAmount),
                '-'
            );

            $html .= '</tbody></table>';
        }

        $html .= '</body></html>';

        // Write HTML to PDF
        $mpdf->WriteHTML($html);

        $this->isGenerating = false;
        
        // Output PDF
        return response()->streamDownload(function() use ($mpdf) {
            $mpdf->Output('Sales_General_Report.pdf', 'I');
        }, 'Sales_General_Report.pdf');

        
    }

    public function render()
    {
        return view('livewire.reports.sales-general');
    }
}
