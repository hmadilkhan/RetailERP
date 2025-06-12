<div>
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <div class="card-title m-0">
                <h3 class="m-0">Consolidated Stock Report</h3>
            </div>
            <div class="ms-auto d-flex gap-2">
                <button class="btn btn-danger px-4" type="button" wire:click="exportToPdf"
                    {{ empty($results) ? 'disabled' : '' }} @if ($isGenerating) disabled @endif>
                    @if ($isGenerating)
                        <span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>
                        Exporting...
                    @else
                        Export to PDF
                    @endif
                </button>
                <button class="btn btn-success px-4" type="button" wire:click="exportToExcel"
                    {{ empty($results) ? 'disabled' : '' }} @if ($isGenerating) disabled @endif>
                    @if ($isGenerating)
                        <span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>
                        Exporting...
                    @else
                        Export to Excel
                    @endif
                </button>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>Product Code</th>
                            <th>Product Name</th>
                            <th>Weight Qty</th>
                            <th>Opening Stock</th>
                            <th>Sales</th>
                            <th>Stock Return</th>
                            <th>Stock Purchase</th>
                            <th>Closing Stock</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($products as $product)
                            @php
                                $sales_filtered = array_filter($results->toArray(), function ($item) use ($product) {
                                    return $item['product_id'] == $product->id && $item['narration'] == 'Sales';
                                });

                                $opening_filtered = array_filter($results->toArray(), function ($item) use ($product) {
                                    return $item['product_id'] == $product->id && $item['narration'] == 'Stock Opening';
                                });

                                $stock_purchase_filtered = array_filter($results->toArray(), function ($item) use (
                                    $product,
                                ) {
                                    return $item['product_id'] == $product->id &&
                                        $item['narration'] == 'Stock Purchase through Purchase Order';
                                });

                                $adjustment_filtered = array_filter($results->toArray(), function ($item) use (
                                    $product,
                                ) {
                                    return $item['product_id'] == $product->id &&
                                        str_contains($item['narration'], 'Stock Adjustment');
                                });
                                $opening_stock = array_sum(array_column($opening_filtered, 'qty'));
                                $sales = array_sum(array_column($sales_filtered, 'qty'));
                                $stock_adjustment = array_sum(array_column($adjustment_filtered, 'qty'));
                                $stock_purchased = array_sum(array_column($stock_purchase_filtered, 'qty'));
                                $closing_stock = $opening_stock - $sales + $stock_adjustment + $stock_purchased;
                                // if ($product->item_code == 'tq35') {
                                //     dd($opening_filtered, $sales, $stock_adjustment, $closing_stock);
                                // }
                            @endphp
                            <tr>
                                <td>{{ $product->item_code }}</td>
                                <td>{{ $product->product_name }}</td>
                                <td class="text-center">{{ $product->weight_qty ?? 1 }}</td>
                                <td class="text-center">{{ $opening_stock }}</td>
                                <td class="text-center">{{ $sales }}</td>
                                <td class="text-center">{{ $stock_adjustment }}</td>
                                <td class="text-center">{{ $stock_purchased }}</td>
                                <td class="text-center {{ $closing_stock < 0 ? 'text-danger fw-bold' : '' }}">
                                    {{ $closing_stock }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center">No records found</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
