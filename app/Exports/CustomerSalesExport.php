<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithColumnWidths;

class CustomerSalesExport implements FromView, WithTitle, WithColumnWidths
{
    protected $customerSales;

    protected $queryRecord;
    protected $company;
    protected $dates;

    public function __construct(array $queryRecord, string $company, array $dates)
    {
        $this->queryRecord = $queryRecord;
        $this->company = $company;
        $this->dates = $dates;
    }

    public function columnWidths(): array
    {
        return [
            'B' => 35,
            'C' => 35,
            'D' => 15,
            'E' => 15,
            'F' => 15,
        ];
    }

    public function view(): View
    {
        $record = $this->queryRecord;
        $company = $this->company;
        $dates = $this->dates;
        return view("partials.reports.customer-sales-report-export", compact("record", "company", "dates"));
    }

    public function title(): string
    {
        return 'Customer Sales Report' . date("d M Y", strtotime($this->dates["from"]));
    }
}
