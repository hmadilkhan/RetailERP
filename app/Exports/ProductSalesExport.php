<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class ProductSalesExport implements FromCollection, WithHeadings, WithMapping
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
            'Item Code',
            'Product Name',
            'Receipt No',
            'Date',
            'Quantity',
            'Price',
            'Total Amount',
            'Cost',
            'Order Status',
            'Order Mode',
            'Is Return'
        ];
    }

    public function map($row): array
    {
        return [
            $row->code,
            $row->product_name,
            $row->receipt_no,
            $row->date,
            $row->qty,
            $row->price,
            $row->total_amount,
            $row->cost,
            $row->order_status_name,
            $row->ordermode,
            $row->is_sale_return ? 'Yes' : 'No'
        ];
    }
} 