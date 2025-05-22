<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class CustomerSaleSummaryExport implements FromCollection, WithHeadings, WithMapping
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
            'Customer ID',
            'Customer Name',
            'Mobile',
            'Membership Card No',
            'Branch Name',
            'Total Orders',
            'Total Sales',
            'Last Order Date'
        ];
    }

    public function map($row): array
    {
        return [
            $row->customer_id,
            $row->customer_name,
            $row->mobile,
            $row->membership_card_no,
            $row->branch_name,
            number_format($row->total_orders),
            number_format($row->total_sales),
            date('d M Y', strtotime($row->last_order_date))
        ];
    }
} 