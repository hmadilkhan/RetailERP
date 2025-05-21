<?php

namespace App\Exports\Reports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithTitle;

class ItemSalesRecordExport implements FromCollection, WithHeadings, WithStyles, WithColumnWidths, WithTitle
{
    protected $data;
    protected $title;

    public function __construct($data, $title = 'Item Sales Record')
    {
        $this->data = $data;
        $this->title = $title;
    }

    public function collection()
    {
        return collect($this->data);
    }

    public function headings(): array
    {
        return [
            'Item Code',
            'Product Name',
            'Quantity',
            'Price',
            'Amount',
            'COGS',
            'Margin',
            'Status'
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']], 'fill' => ['fillType' => 'solid', 'startColor' => ['rgb' => '1A4567']]],
            'A1:H1' => ['alignment' => ['horizontal' => 'center']],
            'A:H' => ['alignment' => ['horizontal' => 'center']],
        ];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 15, // Item Code
            'B' => 40, // Product Name
            'C' => 15, // Quantity
            'D' => 15, // Price
            'E' => 15, // Amount
            'F' => 15, // COGS
            'G' => 15, // Margin
            'H' => 15, // Status
        ];
    }

    public function title(): string
    {
        return $this->title;
    }
} 