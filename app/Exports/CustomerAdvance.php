<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;

class CustomerAdvance implements FromArray, WithHeadings,WithTitle
{
    protected $advances;
    
	public function __construct(array $advances)
    {
        $this->advances = $advances;
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
        return $this->advances;
    }

    public function title(): string
    {
         return 'Advances';
    }
}
