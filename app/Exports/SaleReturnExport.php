<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\RegistersEventListeners;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithTitle;

class SaleReturnExport implements FromView,WithColumnWidths,WithTitle,WithEvents
{
    use RegistersEventListeners;

    protected $queryRecord;
	protected $dates;
	protected $companyname;
    

    public function __construct(object $queryRecord,array $dates,string $companyname)
    {
        $this->queryRecord = $queryRecord;
        $this->dates = $dates;
        $this->companyname = $companyname;
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
            'H' => 20,
            'I' => 20,
            'J' => 20,
            'K' => 20,
            'O' => 20,
            'R' => 20,
            'U' => 30,
            // 'G' => 20,
        ];
    }

    public function view(): View
    {
        $record = $this->queryRecord;
        $dates = $this->dates;
        $companyname = $this->companyname;
        return view("partials.reports.sales-return-excel-export",compact("record","dates","companyname"));
    }
	
	public function title(): string
    {
        return '' . date("d M Y",strtotime($this->dates["from"]));
    }
}
