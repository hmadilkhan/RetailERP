<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\RegistersEventListeners;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithTitle;

class SalesDeclarationExport implements FromView,WithColumnWidths,WithTitle,WithEvents
{
    use RegistersEventListeners;
    
	protected $queryRecord;
	protected $branch;
	protected $dates;
	protected $terminal;

    public function __construct(object $queryRecord,array $branch,array $dates,string $terminal)
    {
        $this->queryRecord = $queryRecord;
        $this->branch = $branch;
        $this->dates = $dates;
        $this->terminal = $terminal;
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
        $branch = $this->branch;
        $dates = $this->dates;
        $mode = $this->terminal;
        return view("partials.reports.sales-declaration-excel-export",compact("record","branch","dates","mode"));
    }
	
	public function title(): string
    {
        return '' . date("d M Y",strtotime($this->dates["from"]));
    }
}
