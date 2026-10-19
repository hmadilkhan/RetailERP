<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithTitle;

// Stock Details "Export" (stock::getStockByBranchForExcel)
class StockReportExport implements FromCollection, WithHeadings, WithMapping, WithTitle, ShouldAutoSize
{
    protected $data;
    protected $branch;
    protected $row = 0;

    public function __construct($data, $branch)
    {
        $this->data = $data;
        $this->branch = (string) $branch;
    }

    public function collection()
    {
        return $this->data;
    }

    public function headings(): array
    {
        return [
            'S.No',
            'Item Code',
            'Product Name',
            'Department',
            'Sub Department',
            'UOM',
            'Stock Qty',
            'Retail Price',
            'Stock Value (Retail)',
            'Reminder Qty',
        ];
    }

    public function map($value): array
    {
        $qty = (float) ($value->qty ?? 0);
        $price = (float) ($value->amount ?? 0);

        return [
            ++$this->row,
            $value->item_code,
            $value->product_name,
            $value->department_name,
            $value->sub_depart_name,
            $value->name,
            round($qty, 2),
            round($price, 2),
            round($qty * $price, 2),
            $value->reminder_qty,
        ];
    }

    public function title(): string
    {
        // Excel sheet title: max 31 chars, no : \ / ? * [ ]
        $title = trim(preg_replace('/[:\\\\\/?*\[\]]/', '', 'Stock ' . $this->branch));

        return mb_substr($title, 0, 31);
    }
}
