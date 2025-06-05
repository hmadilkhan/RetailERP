<div>
    <div class="table-responsive">
        <table class="table table-bordered table-striped">
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Product Name</th>
                    <th>Reference No</th>
                    <th>Transaction Type</th>
                    <th>Quantity</th>
                    <th>Stock Balance</th>
                    <th>User</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @php
                    $stock = 0;
                @endphp
                @forelse($results as $value)
                    @php
                        if ($value->narration == 'Stock Opening') {
                            $stock = (float) $value->stock;
                        } elseif ($value->narration == 'Sales') {
                            $stock = $stock - (preg_match('/Sales/', $value->narration) ? (float) $value->qty ?? (1 / $value->weight_qty ?? 1) : (float) $value->qty ?? 1);
                        } elseif ($value->narration == 'Sales Return') {
                            $stock = (float) $stock + (float) $value->qty;
                        } elseif ($value->narration == 'Stock Purchase through Purchase Order') {
                            $stock = (float) $stock + (float) $value->qty;
                        } elseif ($value->narration == 'Stock Opening from csv file') {
                            $stock = (float) $stock + (float) $value->qty;
                        } elseif ($value->narration == 'Stock Return') {
                            $stock = (float) $stock - (float) $value->qty;
                        } elseif (preg_match('/Stock Adjustment/', $value->narration)) {
                            $stock = (float) $stock + (float) $value->qty;
                        }
                    @endphp
                    <tr>
                        <td>{{ date('d M Y', strtotime($value->date)) }}</td>
                        <td>{{ $value->product_name }}</td>
                        <td>{{ $value->grn_id }}</td>
                        <td>{{ $value->narration }}</td>
                        <td>{{ preg_match('/Sales/', $value->narration) ? $value->qty ?? (1 / $value->weight_qty ?? 1) : $value->qty ?? 1 }}</td>
                        <td>{{ number_format($stock, 2) }}</td>
                        <td>{{ $value->fullname }}</td>
                        <td>
                            @if(preg_match('/Purchase/', $value->narration) && $value->adjustment_mode == '')
                                <a href="{{ route('view', $value->foreign_id) }}" class="p-r-10 f-18 text-info" data-toggle="tooltip" data-placement="top" title="View">
                                    <i class="icofont icofont-printer text-success"></i>
                                </a>
                            @elseif(preg_match('/Sales Return/', $value->narration))
                                <a href="{{ url('sales-return', $value->foreign_id) }}" class="p-r-10 f-18 text-info" data-toggle="tooltip" data-placement="top" title="View">
                                    <i class="icofont icofont-printer text-success"></i>
                                </a>
                            @elseif(preg_match('/Sales/', $value->narration))
                                <a href="{{ url('print', Custom_Helper::getReceiptID($value->foreign_id)) }}" class="p-r-10 f-18 text-info" data-toggle="tooltip" data-placement="top" title="View">
                                    <i class="icofont icofont-printer text-success"></i>
                                </a>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="text-center">No records found</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div> 