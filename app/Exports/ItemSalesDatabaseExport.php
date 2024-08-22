<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;

class ItemSalesDatabaseExport implements FromArray, WithHeadings
{
    protected $isdb;

    public function __construct(array $isdb)
    {
        $this->isdb = $isdb;
    }

    public function headings(): array
    {
        return [
            'Terminal',
            'Cost',
            'Amount',
            'Qty',
            'Name',
        ];
    }

    public function array(): array
    {
        return $this->isdb;
    }
}
