<?php

namespace App\Exports\Reports;

use App\Models\Order;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class SalesGeneralExport implements FromCollection, WithHeadings, WithMapping
{
    protected $dateFrom;
    protected $dateTo;
    protected $branch;
    protected $terminal;
    protected $customer;

    public function __construct($dateFrom, $dateTo, $branch, $terminal, $customer)
    {
        $this->dateFrom = $dateFrom;
        $this->dateTo = $dateTo;
        $this->branch = $branch;
        $this->terminal = $terminal;
        $this->customer = $customer;
    }

    public function collection()
    {
        $order = Order::query();
        $order->with([
            "orderdetails" => function($query) {
                $query->select('receipt_id')
                    ->selectRaw('COUNT(DISTINCT item_code) as total_items')
                    ->selectRaw('SUM(total_qty) as total_qty')
                    ->groupBy('receipt_id');
            },
            "customer",
            "terminal",
            "branchrelation",
            "payment",
            "mode",
            "orderAccount",
            "orderAccountSub"
        ]);

        if ($this->branch) {
            $order->where("branch", $this->branch);
        }

        if ($this->terminal) {
            $order->where("terminal_id", $this->terminal);
        }

        if ($this->customer) {
            $order->where("customer_id", $this->customer);
        }

        if ($this->dateFrom) {
            $order->where("date", ">=", $this->dateFrom);
        }

        if ($this->dateTo) {
            $order->where("date", "<=", $this->dateTo);
        }

        return $order->get();
    }

    public function headings(): array
    {
        return [
            'Receipt No',
            'Date',
            'Customer',
            'Branch',
            'Terminal',
            'Total Items',
            'Total Quantity',
            'Total Amount',
            'Payment Mode',
        ];
    }

    public function map($order): array
    {
        return [
            $order->receipt_no,
            $order->date,
            $order->customer ? $order->customer->name : 'N/A',
            !empty($order->branch) ? $order->branchrelation->branch_name : 'N/A',
            !empty($order->terminal) ? $order->terminal->terminal_name : 'N/A',
            !empty($order->orderdetails->first()) ? $order->orderdetails->first()->total_items : 0,
            !empty($order->orderdetails->first()) ? $order->orderdetails->first()->total_qty : 0,
            $order->total_amount ?? 0,
            !empty($order->mode) ? $order->mode->order_mode : 'N/A',
            
        ];
    }
} 