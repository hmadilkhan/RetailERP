<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithTitle;

class OrderReportCompany74Export implements FromView, WithColumnWidths, WithTitle
{
    protected $queryRecord;

    protected $branch;

    protected $dates;

    protected $customersByMobile;

    public function __construct(object $queryRecord, object $branch, array $dates, array $customersByMobile)
    {
        $this->queryRecord = $queryRecord;
        $this->branch = $branch;
        $this->dates = $dates;
        $this->customersByMobile = $customersByMobile;
    }

    public function columnWidths(): array
    {
        return [
            'A' => 10,
            'B' => 10,
            'C' => 12,
            'D' => 12,
            'E' => 15,
            'F' => 15,
            'G' => 20,
            'H' => 10,
            'I' => 15,
            'J' => 15,
            'K' => 15,
        ];
    }

    public function view(): View
    {
        return view('partials.reports.order-report-excel-export-company74', [
            'record' => $this->queryRecord,
            'branch' => $this->branch,
            'dates' => $this->dates,
            'customersByMobile' => $this->customersByMobile,
        ]);
    }

    public function title(): string
    {
        return '' . date('d M Y', strtotime($this->dates['from']));
    }
}
