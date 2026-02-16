<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;

class RawUsageItemSheet implements FromCollection, WithHeadings, WithMapping, WithTitle, WithEvents
{
    protected $allItemUsage;
    protected $from;
    protected $to;

    public function __construct($allItemUsage, $from, $to)
    {
        $this->allItemUsage = $allItemUsage;
        $this->from = $from;
        $this->to = $to;
    }

    public function collection()
    {
        return collect($this->allItemUsage);
    }

    public function headings(): array
    {
        return ['Item Name', 'UOM', 'Cost', 'Wastage', 'Total Usage', 'Total Cost', 'Previous Stock', 'Balance Stock', 'Closing Stock'];
    }

    public function map($item): array
    {
        return [
            $item->product_name,
            $item->uom,
            $item->cost == '' ? 0 : $item->cost,
            $item->wastage == '' ? 0.0 : $item->wastage,
            $item->totalUsage,
            ($item->cost == '' ? 0 : $item->cost) * $item->totalUsage,
            $item->previous_stock,
            $item->current_stock,
            $item->closing_stock == '' ? 0.0 : $item->closing_stock,
        ];
    }

    public function title(): string
    {
        return 'Raw Items Usage';
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) {
                $event->sheet->insertNewRowBefore(1, 2);
                $event->sheet->mergeCells('A1:I1');
                $event->sheet->mergeCells('A2:I2');
                $event->sheet->setCellValue('A1', 'Raw Usage Report - Raw Items Usage');
                $event->sheet->setCellValue('A2', 'Date Range: ' . date('d-M-Y', strtotime($this->from)) . ' to ' . date('d-M-Y', strtotime($this->to)));
                $event->sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);
                $event->sheet->getStyle('A1')->getAlignment()->setHorizontal('center');
                $event->sheet->getStyle('A2')->getFont()->setBold(true);
                $event->sheet->getStyle('A2')->getAlignment()->setHorizontal('center');
                $event->sheet->getStyle('A3:I3')->getFont()->setBold(true);
            },
        ];
    }
}
