<?php

namespace App\Exports;

use App\Models\Order;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class ReceiptExport implements FromView
{
	protected $orderId;
	
	public function _construct($orderId)
	{
		$this->orderId = $orderId;
	}
	
	public function view(): View
    {
        return view('exports.saleinvoices', [
            'invoices' => Order::where("id",$this->orderId)->get(),
        ]);
    }
}
