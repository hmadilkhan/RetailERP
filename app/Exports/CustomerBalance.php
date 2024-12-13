<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;

class CustomerBalance implements FromArray, WithHeadings,WithTitle
{
	protected $balances;
    
	public function __construct(array $balances)
    {
        $this->balances = $balances;
    }
	
	public function headings(): array
    {
        return [
            'Name',
            'Branch',
            'Mobile',
            'CNIC',
			'Address',
			'Balance',
        ];
    }
	
	public function array(): array
    {
        return $this->balances;
    }

    public function title(): string
    {
         return 'Balances';
    }
}