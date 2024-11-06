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
    protected $branch;
    protected $dates;

    public function __construct(object $queryRecord, string $branch, array $dates)
    {
        $this->queryRecord = $queryRecord;
        $this->branch = $branch;
        $this->dates = $dates;
    }

    public function view(): View
    {
        $record = $this->queryRecord;
        $branch = $this->branch;
        $dates = $this->dates;
        return view("partials.reports.order-receiving-report-export",compact("record","branch","dates"));
    }
	
	public function title(): string
    {
        return 'Booking Order Receivable Report' . date("d M Y",strtotime($this->dates["from"]));
    }
}
