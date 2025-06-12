<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class StockReportProductwiseExport implements FromCollection, WithHeadings, WithMapping
{
    protected $data;

    public function __construct($data)
    {
        $this->data = $data;
    }

    public function collection()
    {
        return $this->data;
    }

    public function headings(): array
    {
        return [
            'Date',
            'Product Name',
            'Reference No',
            'Transaction Type',
            'Quantity',
            'Stock Balance',
            'Created By'
        ];
    }

    public function map($value): array
    {
        static $stock = 0;

        if ($value->narration == 'Stock Opening') {
            $stock = (float) $value->stock;
        } elseif ($value->narration == 'Sales') {
            $stock = $stock - (preg_match('/Sales/', $value->narration)
                ? (float) $value->qty ?? (1 / $value->weight_qty ?? 1)
                : (float) $value->qty ?? 1);
        } elseif ($value->narration == 'Sales Return') {
            $stock = (float) $stock + (float) $value->qty;
        } elseif ($value->narration == 'Stock Purchase through Purchase Order') {
            $stock = (float) $stock + (float) $value->qty;
        } elseif ($value->narration == 'Stock Opening from csv file') {
            $stock = (float) $stock + (float) $value->qty;
        } elseif ($value->narration == 'Stock Return') {
            $stock = (float) $stock - (float) $value->qty;
        } elseif (preg_match('/Stock Adjustment/', $value->narration)) {
            $stock = (float) $stock + (float) $value->qty;
        }

        $quantity = preg_match('/Sales/', $value->narration) 
            ? $value->qty ?? (1 / $value->weight_qty ?? 1) 
            : $value->qty ?? 1;

        return [
            date('d M Y', strtotime($value->date)),
            $value->product_name,
            $value->grn_id,
            $value->narration,
            $quantity,
            number_format($stock, 2),
            $value->fullname
        ];
    }
} 