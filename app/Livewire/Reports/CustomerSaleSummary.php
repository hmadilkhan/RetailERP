<?php

namespace App\Livewire\Reports;

use App\Models\Branch;
use App\Models\Order;
use App\Models\OrderMode;
use App\Models\Company;
use Illuminate\Support\Facades\DB;
use App\Exports\CustomerSaleSummaryExport;
use Livewire\Component;
use Maatwebsite\Excel\Facades\Excel;
use Mpdf\Mpdf;

class CustomerSaleSummary extends Component
{
    // FIELDS
    public $dateFrom = '';
    public $dateTo = '';
    public $branch = '';
    public $customer = '';
    // ARRAYS 
    public $results = [];
    public $branches = [];

    // Loading state
    public $isGenerating = false;


    public function mount()
    {
        $this->branches = Branch::where("company_id", session("company_id"))->get();
    }

    public function generateReport()
    {
        $this->isGenerating = true;

        $summary = Order::query()
        ->join('customers', 'sales_receipts.customer_id', '=', 'customers.id')
        ->leftJoin('branch', 'sales_receipts.branch', '=', 'branch.branch_id') // adjust as needed
        ->when($this->branch, fn($q) => $q->where('sales_receipts.branch', $this->branch))
        ->when($this->customer, fn($q) => $q->where('sales_receipts.customer_id', $this->customer))
        ->when($this->dateFrom, fn($q) => $q->where('sales_receipts.date', '>=', $this->dateFrom))
        ->when($this->dateTo, fn($q) => $q->where('sales_receipts.date', '<=', $this->dateTo))
        ->select(
            'sales_receipts.customer_id',
            'customers.name as customer_name',
            'customers.mobile',
            'customers.membership_card_no',
            'branch.branch_name',
            DB::raw('COUNT(sales_receipts.id) as total_orders'),
            DB::raw('SUM(sales_receipts.total_amount) as total_sales'), // optional
            DB::raw('MAX(sales_receipts.date) as last_order_date')
        )
        ->groupBy('sales_receipts.customer_id', 'customers.name', 'branch.branch_name')
        ->get();

        $this->results = $this->customerSalesQuery();

        $this->isGenerating = false;
    }

    public function customerSalesQuery()
    {
        $summary = Order::query()
        ->join('customers', 'sales_receipts.customer_id', '=', 'customers.id')
        ->leftJoin('branch', 'sales_receipts.branch', '=', 'branch.branch_id') // adjust as needed
        ->when($this->branch, fn($q) => $q->where('sales_receipts.branch', $this->branch))
        ->when($this->customer, fn($q) => $q->where('sales_receipts.customer_id', $this->customer))
        ->when($this->dateFrom, fn($q) => $q->where('sales_receipts.date', '>=', $this->dateFrom))
        ->when($this->dateTo, fn($q) => $q->where('sales_receipts.date', '<=', $this->dateTo))
        ->select(
            'sales_receipts.customer_id',
            'customers.name as customer_name',
            'customers.mobile',
            'customers.membership_card_no',
            'branch.branch_name',
            DB::raw('COUNT(sales_receipts.id) as total_orders'),
            DB::raw('SUM(sales_receipts.total_amount) as total_sales'), // optional
            DB::raw('MAX(sales_receipts.date) as last_order_date')
        )
        ->groupBy('sales_receipts.customer_id', 'customers.name', 'branch.branch_name')
        ->get();

        return $summary;
    }

    public function exportToExcel()
    {
        $summary = Order::query()
        ->join('customers', 'sales_receipts.customer_id', '=', 'customers.id')
        ->leftJoin('branch', 'sales_receipts.branch', '=', 'branch.branch_id')
        ->when($this->branch, fn($q) => $q->where('sales_receipts.branch', $this->branch))
        ->when($this->customer, fn($q) => $q->where('sales_receipts.customer_id', $this->customer))
        ->when($this->dateFrom, fn($q) => $q->where('sales_receipts.date', '>=', $this->dateFrom))
        ->when($this->dateTo, fn($q) => $q->where('sales_receipts.date', '<=', $this->dateTo))
        ->select(
            'sales_receipts.customer_id',
            'customers.name as customer_name',
            'customers.mobile',
            'customers.membership_card_no',
            'branch.branch_name',
            DB::raw('COUNT(sales_receipts.id) as total_orders'),
            DB::raw('SUM(sales_receipts.total_amount) as total_sales'),
            DB::raw('MAX(sales_receipts.date) as last_order_date')
        )
        ->groupBy('sales_receipts.customer_id', 'customers.name', 'branch.branch_name')
        ->get();

        return Excel::download(new CustomerSaleSummaryExport($summary), 'customer-sale-summary.xlsx');
    }

    public function exportToPdf()
    {
        $this->isGenerating = true;

        $company = Company::where('company_id', session('company_id'))->first();
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

        // Add date range and title
        $html .= '<h4 style="text-align: center;">From  Date: ' . $this->dateFrom . ' To Date: ' . $this->dateTo . ' </h4>';
        $html .= '<h2 style="text-align: center;">Customer Sales Summary Report' . $branchname . '</h2>';

        // Add table
        $html .= '
        <table>
            <thead>
                <tr>
                    <th>No</th>
                    <th>Customer</th>
                    <th>Branch</th>
                    <th>Contact</th>
                    <th>Membership No #</th>
                    <th>Total Orders</th>
                    <th>Total Sales</th>
                    <th>Last Order Date</th>
                </tr>
            </thead>
            <tbody>';

        $totalOrders = 0;
        $totalSales = 0;

        $this->results = $this->customerSalesQuery();

        foreach ($this->results as $index => $row) {
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
                </tr>',
                $index + 1,
                $row->customer_name,
                $row->branch_name ?? 'N/A',
                $row->mobile,
                $row->membership_card_no ?? 'N/A',
                number_format($row->total_orders),
                number_format($row->total_sales),
                date('d M Y', strtotime($row->last_order_date))
            );

            $totalOrders += $row->total_orders;
            $totalSales += $row->total_sales;
        }

        // Add totals row
        $html .= sprintf(
            '<tr style="font-weight: bold;background-color: #000000;color: #FFFFFF;">
                <td colspan="5" style="color: #FFFFFF;font-weight: bold;">Total</td>
                <td style="color: #FFFFFF;font-weight: bold;">%s</td>
                <td style="color: #FFFFFF;font-weight: bold;">%s</td>
                <td style="color: #FFFFFF;font-weight: bold;">-</td>
            </tr>',
            number_format($totalOrders),
            number_format($totalSales)
        );

        $html .= '</tbody></table>';
        $html .= '</body></html>';

        // Write HTML to PDF
        $mpdf->WriteHTML($html);

        $this->isGenerating = false;
        
        // Output PDF
        return response()->streamDownload(function() use ($mpdf) {
            $mpdf->Output('Customer_Sales_Summary_Report.pdf', 'I');
        }, 'Customer_Sales_Summary_Report.pdf');
    }

    public function render()
    {
        return view('livewire.reports.customer-sale-summary');
    }
}
