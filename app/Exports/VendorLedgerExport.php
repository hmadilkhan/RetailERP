<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;

class VendorLedgerExport implements FromArray, WithHeadings
{
    protected $vendorledger;

    public function __construct(array $vendorledger)
    {
        $this->vendorledger = $vendorledger;
    }

    public function headings(): array
    {
        return [
            'Serial',
            'Name',
            'Contact',
            'Company',
            'Balance',
        ];
    }

    public function array(): array
    {
        return $this->vendorledger;
    }


}
