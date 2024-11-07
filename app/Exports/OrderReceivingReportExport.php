<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\RegistersEventListeners;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithTitle;

class OrderReceivingReportExport implements FromView, WithTitle
{
    use RegistersEventListeners;
    protected $queryRecord;
    protected $company;
    protected $dates;

    public function __construct(array $queryRecord, string $company, array $dates)
    {
        $this->queryRecord = $queryRecord;
        $this->company = $company;
        $this->dates = $dates;
    }

    public function view(): View
    {
        $record = $this->queryRecord;
        $company = $this->company;
        $dates = $this->dates;
        return view("partials.reports.order-receiving-report-export",compact("record","company","dates"));
    }
	
	public function title(): string
    {
        return 'Booking Order Receivable Report' . date("d M Y",strtotime($this->dates["from"]));
    }
}
