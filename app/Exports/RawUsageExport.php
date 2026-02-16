<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class RawUsageExport implements WithMultipleSheets
{
    protected $totalSaleItems;
    protected $totalItemsArray;
    protected $allItemUsage;
    protected $from;
    protected $to;

    public function __construct($totalSaleItems, $totalItemsArray, $allItemUsage, $from, $to)
    {
        $this->totalSaleItems = $totalSaleItems;
        $this->totalItemsArray = $totalItemsArray;
        $this->allItemUsage = $allItemUsage;
        $this->from = $from;
        $this->to = $to;
    }

    public function sheets(): array
    {
        return [
            new RawUsageRecipeSheet($this->totalSaleItems, $this->totalItemsArray, $this->from, $this->to),
            new RawUsageItemSheet($this->allItemUsage, $this->from, $this->to),
        ];
    }
}
