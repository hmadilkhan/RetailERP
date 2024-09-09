<div class="col-md-12 table-responsive">
    <table id="order_table" class="table table-striped table-bordered  nowrap flex-nowrap col-md-12 col-sm-12"
        width="100%">
        <thead>
            <tr>
                <th>Machine#</th>
                <th>Order#</th>
                <th>Date</th>
                <th>Time</th>
                <th>Branch</th>
                <th>Terminal</th>
                <th>Receipt#</th>
                <th>Customer</th>
                <th>OrderType</th>
                <th>Payment</th>
                <th>Status</th>
                <th>Amount</th>
                <th>Items/Total</th>
                <th>Sales Person</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            @if ($orders)
                @foreach ($orders as $key => $order)
                    <tr class="{{ $order->is_sale_return == 1 ? 'table-danger' : '' }}">
                        <td>{{ $order->machine_terminal_count }}</td>
                        <td>{{ $order->id }}</td>
                        <td>
                            <div class="btn-group dropend border border-black">
                                <button type="button" class="btn btn-default dropdown-toggle" data-bs-toggle="dropdown"
                                    aria-expanded="false">
                                    {{ date('d M Y ', strtotime($order->date)) }}
                                </button>
                                <ul class="dropdown-menu px-4">
                                    <li><a class="dropdown-item" href="#"><strong>Stamp Date </strong><br />
                                            {{ date('d M Y ', strtotime($order->delivery_date)) }}</a></li>
                                    @if ($order->order_delivery_date != '')
                                        <li>
                                            <hr class="dropdown-divider">
                                        </li>
                                        <li><a class="dropdown-item" href="#"><strong>Order Delivery
                                                    Date</strong></a>{{ $order->order_delivery_date != '' ? date('d M Y ', strtotime($order->order_delivery_date)) : '' }}
                                        </li>
                                    @endif
                                </ul>
                            </div>
                        </td>
                        <td>{{ date('h:i a', strtotime($order->time)) }}</td>
                        <td>{{ $order->branch_name }}</td>
                        <td>{{ $order->terminal_name }}</td>
                        <td>{{ $order->receipt_no }}</td>
                        <td>{{ $order->name }}</td>
                        <td>{{ $order->order_mode }}</td>
                        <td>{{ $order->payment_mode }}</td>
                        <td><label
                                class="label {{ Custom_Helper::getColorName($order->order_status_name) }}">{{ Custom_Helper::getOrderStatus($order->order_status_name, $order->is_sale_return) }}</label>
                        </td>
                        <td>{{ $order->total_amount }}</td>
                        <td>{{ $order->itemcount }}/{{ $order->itemstotalqty }}</td>
                        <td>{{ !empty($order->provider_name) ? $order->provider_name : '-' }}</td>
                        {{-- <td>
                            <i onclick='showOrderDetails("{{ $order->id }}")'
                                class='icofont icofont icofont-eye-alt icofont-1x text-info' data-toggle='tooltip'
                                data-placement='top' title='' data-original-title='Show Order Details'></i>
                            @if (session('roleId') != 20 && session('roleId') != 19)
                                <i onclick='showReceipt("{{ $order->receipt_no }}")'
                                    class='icofont icofont icofont-printer text-success' data-toggle='tooltip'
                                    data-placement='top' title='' data-original-title='Show Receipt'></i>
                                <i onclick='assignToServiceProviderModal("{{ $order->id }}")'
                                    class='icofont icofont icofont-business-man' data-toggle='tooltip'
                                    data-placement='top' title=''
                                    data-original-title='Assign To Service Provider'></i>
                            @endif
                            @if ($order->status != 12 && (session('roleId') != 20 && session('roleId') != 19))
                                <i onclick='voidReceipt("{{ $order->id }}")'
                                    class='alert-confirm text-danger icofont icofont icofont-delete-alt'
                                    data-toggle='tooltip' data-placement='top' title=''
                                    data-original-title='Mark as Void'></i>
                            @endif
                            @if ($order->status != 4 && session('roleId') != 20 && session('roleId') != 19)
                                <i onclick='deliveredReceipt("{{ $order->id }}")'
                                    class='alert-confirm text-success icofont icofont icofont-tick-mark'
                                    data-toggle='tooltip' data-placement='top' title=''
                                    data-original-title='Mark as Delivered'></i>
                            @endif
                            @if (session('roleId') == 20 && $order->status == 6)
                                <i onclick='assignToBranchModal("{{ $order->id }}")'
                                    class='icofont icofont icofont-business-man' data-toggle='tooltip'
                                    data-placement='top' title='' data-original-title='Assign to Branch'></i>
                            @endif
                        </td> --}}
                        <td>
                            <!-- Large button groups (default and split) -->
                            <div class="btn-group border border-black">
                                <button class="btn btn-default btn-sm dropdown-toggle" type="button"
                                    data-bs-toggle="dropdown" aria-expanded="false">
                                    Actions
                                </button>
                                <ul class="dropdown-menu px-2">
                                    <li><a class="dropdown-item"><i onclick='showOrderDetails("{{ $order->id }}")'
                                                class='icofont icofont icofont-eye-alt icofont-1x text-info mx-2'
                                                data-toggle='tooltip' data-placement='top' title=''
                                                data-original-title='Show Order Details'></i>Show Order Details</a></li>
                                    @if (session('roleId') != 20 && session('roleId') != 19)
                                        <li><a class="dropdown-item"><i
                                                    onclick='showReceipt("{{ $order->receipt_no }}")'
                                                    class='icofont icofont icofont-printer text-success mx-2'
                                                    data-toggle='tooltip' data-placement='top' title=''
                                                    data-original-title='Show Receipt'></i>Show Receipt </a></li>
                                        <li><a class="dropdown-item"><i
                                                    onclick='assignToServiceProviderModal("{{ $order->id }}")'
                                                    class='icofont icofont icofont-business-man mx-2'
                                                    data-toggle='tooltip' data-placement='top' title=''
                                                    data-original-title='Assign To Service Provider'></i>Assign To
                                                Service
                                                Provider </a></li>
                                    @endif
                                    @if ($order->status != 12 && (session('roleId') != 20 && session('roleId') != 19))
                                        <li><a class="dropdown-item"><i onclick='voidReceipt("{{ $order->id }}")'
                                                    class='alert-confirm text-danger icofont icofont icofont-delete-alt mx-2'
                                                    data-toggle='tooltip' data-placement='top' title=''
                                                    data-original-title='Mark as Void'></i>Mark as Void</a></li>
                                    @endif
                                    @if ($order->status != 4 && session('roleId') != 20 && session('roleId') != 19)
                                        <li><a class="dropdown-item"><i
                                                    onclick='deliveredReceipt("{{ $order->id }}")'
                                                    class='alert-confirm text-success icofont icofont icofont-tick-mark mx-2'
                                                    data-toggle='tooltip' data-placement='top' title=''
                                                    data-original-title='Mark as Delivered'></i>Mark as Delivered</a>
                                        </li>
                                    @endif
                                    @if (session('roleId') == 20 && $order->status == 6)
                                        <li><a class="dropdown-item"><i
                                                    onclick='assignToBranchModal("{{ $order->id }}")'
                                                    class='icofont icofont icofont-business-man mx-2'
                                                    data-toggle='tooltip' data-placement='top' title=''
                                                    data-original-title='Assign to Branch'></i>Assign to Branch</a></li>
                                    @endif
                                </ul>
                            </div>
                        </td>
                    </tr>
                @endforeach
            @endif
        </tbody>
    </table>
    {{ $orders->links('pagination::bootstrap-4') }}
</div>
<script type="text/javascript">
    $(function() {
        $('.collapse-item').click(function(e) {
            e.preventDefault();

            const target = $(this).data('target');

            if (!$(target).hasClass('show')) {
                $('.collapse-content').removeClass('show');
            }

            $(target).toggleClass('show');
        });
    });

    @php
        $collection = collect($totalorders);
        $pending = $collection->filter(fn($item) => $item->order_status_name == 'Pending')->values()->all();
        $processing = $collection->filter(fn($item) => $item->order_status_name == 'Processing')->values()->all();
        $voidOrders = $collection->filter(fn($item) => $item->order_status_name == 'Void')->values()->all();
        $deliveredOrders = $collection->filter(fn($item) => $item->order_status_name == 'Delivered')->values()->all();
    @endphp


    $("#pendingorders").html("{{ count($pending) > 0 ? $pending[0]->totalorders : 0 }}");
    $("#processingorders").html("{{ count($processing) > 0 ? $processing[0]->totalorders : 0 }}");
    $("#voidorders").html("{{ count($voidOrders) > 0 ? $voidOrders[0]->totalorders : 0 }}");
    $("#deliveredorders").html("{{ count($deliveredOrders) > 0 ? $deliveredOrders[0]->totalorders : 0 }}");
    $("#totalorders").html(
        "{{ (count($processing) > 0 ? $processing[0]->totalorders : 0) + (count($voidOrders) > 0 ? $voidOrders[0]->totalorders : 0) + (count($deliveredOrders) > 0 ? $deliveredOrders[0]->totalorders : 0) + (count($pending) > 0 ? $pending[0]->totalorders : 0) }}"
    );
</script>
