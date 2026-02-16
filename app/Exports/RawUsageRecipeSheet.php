<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;

class RawUsageRecipeSheet implements FromCollection, WithHeadings, WithMapping, WithTitle, WithEvents
{
    protected $totalSaleItems;
    protected $totalItemsArray;
    protected $from;
    protected $to;

    public function __construct($totalSaleItems, $totalItemsArray, $from, $to)
    {
        $this->totalSaleItems = $totalSaleItems;
        $this->totalItemsArray = $totalItemsArray;
        $this->from = $from;
        $this->to = $to;
    }

    public function collection()
    {
        $data = collect();
        foreach ($this->totalSaleItems as $item) {
            $filteredArray = array_filter($this->totalItemsArray, function ($value) use ($item) {
                return $value['recipy_id'] == $item->recipy_id;
            });
            foreach ($filteredArray as $receipyItem) {
                $data->push((object)array_merge(['recipe_name' => $item->item_name, 'recipe_qty' => $item->totalqty], $receipyItem));
            }
        }
        return $data;
    }

    public function headings(): array
    {
        return ['Recipe Name', 'Recipe Total Qty', 'Item Name', 'UOM', 'Per Usage', 'Total Qty', 'Total Usage'];
    }

    public function map($row): array
    {
        return [
            $row->recipe_name,
            $row->recipe_qty,
            $row->item_name,
            $row->uom,
            $row->usage_qty,
            $row->total_qty,
            $row->total_usage,
        ];
    }

    public function title(): string
    {
        return 'Item Wise Details';
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) {
                $event->sheet->insertNewRowBefore(1, 2);
                $event->sheet->mergeCells('A1:G1');
                $event->sheet->mergeCells('A2:G2');
                $event->sheet->setCellValue('A1', 'Raw Usage Report - Item Wise Details');
                $event->sheet->setCellValue('A2', 'Date Range: ' . date('d-M-Y', strtotime($this->from)) . ' to ' . date('d-M-Y', strtotime($this->to)));
                $event->sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);
                $event->sheet->getStyle('A1')->getAlignment()->setHorizontal('center');
                $event->sheet->getStyle('A2')->getFont()->setBold(true);
                $event->sheet->getStyle('A2')->getAlignment()->setHorizontal('center');
                $event->sheet->getStyle('A3:G3')->getFont()->setBold(true);
            },
        ];
    }
}
