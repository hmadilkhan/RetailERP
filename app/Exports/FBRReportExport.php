<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;

class FBRReportExport implements FromArray, WithHeadings
{
    protected $isdb;

    public function __construct(array $isdb)
    {
        $this->isdb = $isdb;
    }

    public function headings(): array
    {
        return [
            'Sales Id',
            'FBR Inv Number',
            'Date',
            'Sales',
            'Sales Tax',
            'Total Amount',
        ];
    }

    public function array(): array
    {
        return $this->isdb;
    }
}
