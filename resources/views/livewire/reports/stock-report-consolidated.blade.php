<div>
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Consolidated Stock Report</h3>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-striped">
                    <thead>
                        <tr>
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
                        @forelse($results as $result)
                            <tr>
                                <td>{{ $result->product_name }}</td>
                                <td>{{ $result->weight_qty }}</td>
                                <td>{{ number_format($result->opening_stock, 2) }}</td>
                                <td>{{ number_format($result->sales, 2) }}</td>
                                <td>{{ number_format($result->stock_return, 2) }}</td>
                                <td>{{ number_format($result->stock_purchase, 2) }}</td>
                                <td>{{ number_format($result->closing_stock, 2) }}</td>
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