<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class CustomerExport implements WithMultipleSheets
{
	protected $balances;
    protected $advances;
	
	public function __construct(array $balanceArray,array $advancesArray)
	{
		$this->balances = $balanceArray;
		$this->advances = $advancesArray;
	}
	
	public function sheets(): array
    {
        return [
			'Balances' => new CustomerBalance($this->balances),
			'Advances' => new CustomerAdvance($this->advances),
        ];
    }
}
