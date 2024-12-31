<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;

class ExpenseReportExport implements FromArray, WithHeadings
{
    protected $vendorledger;

    public function __construct(array $vendorledger)
    {
        $this->vendorledger = $vendorledger;
    }

    public function headings(): array
    {
        return [
            'Sr',
            'Date',
            'Category',
            'Amount',
            'Details',
        ];
    }

    public function array(): array
    {
        return $this->vendorledger;
    }
}
