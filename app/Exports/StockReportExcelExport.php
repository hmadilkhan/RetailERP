<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\RegistersEventListeners;

class StockReportExcelExport implements FromView,WithColumnWidths,WithTitle,WithEvents
{
    use RegistersEventListeners;
	protected $queryRecord;
	protected $branch;
	protected $dates;

    public function __construct(object $queryRecord,string $branch,array $dates)
    {
        $this->queryRecord = $queryRecord;
        $this->branch = $branch;
        $this->dates = $dates;
    }

    public function columnWidths(): array
    {
        return [
            // 'B' => 25,
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
            // 'G' => 20,
        ];
    }

    public function view(): View
    {
        $record = $this->queryRecord;
        $branch = $this->branch;
        $dates = $this->dates;
        return view("partials.reports.daily-stock-report-export",compact("record","branch","dates"));
    }
	
	public function title(): string
    {
        return 'Stock Report' . date("d M Y",strtotime($this->dates["from"]));
    }
}
