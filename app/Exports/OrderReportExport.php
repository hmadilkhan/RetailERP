<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\RegistersEventListeners;
use Dompdf\Dompdf;
use Dompdf\Options;


class OrderReportExport implements FromView,WithColumnWidths,WithTitle,WithEvents
{
	use RegistersEventListeners;
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

    public function registerEvents(): array
    {
        return [
            BeforeSheet::class => function (BeforeSheet $event) {
                $dompdf = new Dompdf();
                $options = new Options();
                $options->set('isHtml5ParserEnabled', true);
                $options->set('isRemoteEnabled', true);

                $dompdf->setOptions($options);
                $dompdf->setPaper('A4', 'landscape'); // Change 'landscape' to 'portrait' if needed
            },
        ];
    }
		
	// public static function beforeExport(BeforeExport $event)
    // {
        
    // }

    // public static function beforeWriting(BeforeWriting $event)
    // {
        
    // }

    // public static function beforeSheet(BeforeSheet $event)
	// {
		// $event->sheet->getActiveSheet()->getPageSetup()
			// ->setOrientation(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::ORIENTATION_LANDSCAPE);
	// }

    // public static function afterSheet(AfterSheet $event)
    // {
        
    // }
	
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
        $mode = $this->mode;
        return view("partials.reports.order-report-excel-export",compact("record","branch","dates","mode"));
    }
	
	public function title(): string
    {
        return '' . date("d M Y",strtotime($this->dates["from"]));
    }
}
