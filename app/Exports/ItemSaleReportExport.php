<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use App\Services\ComplianceService;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithColumnWidths;


class ItemSaleReportExport implements FromView,WithColumnWidths,WithTitle
{
	protected $queryRecord;
	protected $branch;
	protected $dates;
	protected $mode;

    public function __construct(object $queryRecord,object $branch,array $dates,string $mode)
    {
        $this->queryRecord = $queryRecord;
        $this->branch = $branch;
        $this->dates = $dates;
        $this->mode = $mode;
    }
	
    public function columnWidths(): array
    {
        return [
            // 'B' => 25,
            'A' => 5,
            'B' => 5,
            'C' => 5,
            'D' => 25,
            'E' => 10,
            'F' => 10,
            'G' => 10,
            'H' => 10,
            'I' => 10,
            // 'G' => 20,
        ];
    }

    public function view(): View
    {
        $record = $this->queryRecord;
        $branch = $this->branch;
        $dates = $this->dates;
        $mode = $this->mode;
        return view("partials.reports.item-sale-excel-export",compact("record","branch","dates","mode"));
    }
	
	public function title(): string
    {
        return '' . date("d M Y",strtotime($this->dates["from"]));
    }
}
