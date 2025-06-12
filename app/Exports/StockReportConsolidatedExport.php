<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class StockReportConsolidatedExport implements FromCollection, WithHeadings, WithMapping
{
    protected $data;
    protected $products;
    
    public function __construct($products, $data)
    {
        $this->data = $data;
        $this->products = $products;
    }

    public function collection()
    {
        return $this->products;
    }

    public function headings(): array
    {
        return [
            'Product Code',
            'Product Name',
            'Weight Qty',
            'Opening Stock',
            'Sales',
            'Stock Adjustment',
            'Stock Purchase',
            'Closing Stock'
        ];
    }

    public function map($product): array
    {
        $sales_filtered = array_filter($this->data->toArray(), function ($item) use ($product) {
            return $item['product_id'] == $product->id && $item['narration'] == 'Sales';
        });

        $opening_filtered = array_filter($this->data->toArray(), function ($item) use ($product) {
            return $item['product_id'] == $product->id && $item['narration'] == 'Stock Opening';
        });

        $stock_purchase_filtered = array_filter($this->data->toArray(), function ($item) use ($product) {
            return $item['product_id'] == $product->id && $item['narration'] == 'Stock Purchase through Purchase Order';
        });

        $adjustment_filtered = array_filter($this->data->toArray(), function ($item) use ($product) {
            return $item['product_id'] == $product->id && str_contains($item['narration'], 'Stock Adjustment');
        });

        $opening_stock = array_sum(array_column($opening_filtered, 'qty'));
        $sales = array_sum(array_column($sales_filtered, 'qty'));
        $stock_adjustment = array_sum(array_column($adjustment_filtered, 'qty'));
        $stock_purchased = array_sum(array_column($stock_purchase_filtered, 'qty'));
        $closing_stock = $opening_stock - $sales + $stock_adjustment + $stock_purchased;

        return [
            $product->item_code,
            $product->product_name,
            $product->weight_qty ?? 1,
            $opening_stock,
            $sales,
            $stock_adjustment,
            $stock_purchased,
            $closing_stock
        ];
    }
} 