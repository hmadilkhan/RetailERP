<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;

class CustomerLedgerExport implements FromArray, WithHeadings
{
    protected $customerledger;

    public function __construct(array $customerledger)
    {
        $this->customerledger = $customerledger;
    }

    public function headings(): array
    {
        return [
            'Serial',
            'Name',
            'Contact',
            'Balance',
        ];
    }

    public function array(): array
    {
        return $this->customerledger;
    }


}
