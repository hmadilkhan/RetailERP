<style>
    /* Ensures checkboxes are centered and not overflowing */
    #order_table th,
    #order_table td {
        vertical-align: middle;
    }

    #order_table th:first-child,
    #order_table td:first-child {
        min-width: 48px;
        width: 48px;
        max-width: 60px;
        text-align: center;
        padding-left: 0.25rem;
        padding-right: 0.25rem;
    }

    #order_table {
        table-layout: fixed;
        width: 100%;
    }

    @media (max-width: 576px) {

        #order_table th,
        #order_table td {
            font-size: 0.85rem;
            padding: 0.3rem;
        }
    }
</style>
<div class="col-md-12 mb-3 d-flex justify-content-between align-items-center">
    <h5 class="mb-0">Orders</h5>
    <div class="btn-group" role="group" aria-label="Bulk actions">
        <button id="void-selected-btn" class="btn btn-danger" disabled>Mark as Void</button>
        <button id="deliver-selected-btn" class="btn btn-success" disabled>Mark As Delivered</button>
    </div>
</div>
<div class="col-md-12 table-responsive">
    <table id="order_table" class="table table-striped  dt-responsive dataTable no-footer dtr-inline" width="100%">
        <thead>
            <tr>
                <th style="width:40px; text-align:center;">
                    <input type="checkbox" id="select-all-orders" class="form-check-input">
                </th>
                <th>Machine/Website</th>
                <th>Order#</th>
                <th>Date</th>
                <th>Time</th>
                <th class="text-center">Category</th>
                <th>Branch</th>
                <th>Terminal</th>
                <th>Receipt#</th>
                <th>Customer</th>
                <th>Order Type</th>
                <th>Payment</th>
                <th>Status</th>
                <th>Amount</th>
                <th>Items / Total</th>
                <th>Sales Person</th>
                <th>Wallet</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            @if ($orders->isNotEmpty())
                @foreach ($orders as $key => $order)
                    <tr id="parent{{ $order->id }}"
                        class="{{ $order->is_sale_return == 1 ? 'table-danger' : '' }} main-row pointer">
                        <td style="text-align:center;">
                            <input type="checkbox" class="form-check-input order-checkbox" value="{{ $order->id }}">
                        </td>
                        <td>{{ $order->web == 1 ? strtoupper($order->url_orderid) : $order->machine_terminal_count }}
                        </td>
                        <td>{{ $order->id }}</td>
                        <td style="width:100px;">
                            <div style="width:80px;" class="btn-group dropend border border-black">
                                <button type="button" class="btn btn-sm btn-default dropdown-toggle"
                                    data-bs-toggle="dropdown" aria-expanded="false">
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
                        <td class="text-center"><label
                                class="label {{ $order->web == 1 ? 'label-warning' : 'label-info' }}">
                                {{ $order->web == 1 ? 'Website' : 'POS' }}</label></td>
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
                        <td>{{ !empty($order->wallet) ? $order->wallet : '-' }}</td>
                        <td>
                            <!-- Large button groups (default and split) -->
                            <div class="btn-group border border-black">
                                <button class="btn btn-default btn-sm dropdown-toggle" type="button"
                                    data-bs-toggle="dropdown" aria-expanded="false">
                                    Actions
                                </button>
                                <ul class="dropdown-menu px-2">
                                    <li onclick='showOrderDetails("{{ $order->id }}")'><a class="dropdown-item"><i
                                                onclick='showOrderDetails("{{ $order->id }}")'
                                                class='icofont icofont-eye-alt icofont-1x text-info mx-2'
                                                data-toggle='tooltip' data-placement='top' title=''
                                                data-original-title='Show Order Details'></i>Show Order Details</a></li>
                                    @if (session('roleId') != 20 && session('roleId') != 19)
                                        <li onclick='showReceipt("{{ $order->receipt_no }}")'><a
                                                class="dropdown-item"><i
                                                    onclick='showReceipt("{{ $order->receipt_no }}")'
                                                    class='icofont icofont-printer text-success mx-2'
                                                    data-toggle='tooltip' data-placement='top' title=''
                                                    data-original-title='Show Receipt'></i>Show Receipt </a></li>
                                        <li onclick='assignToServiceProviderModal("{{ $order->id }}")'><a
                                                class="dropdown-item"><i
                                                    onclick='assignToServiceProviderModal("{{ $order->id }}")'
                                                    class='icofont icofont-business-man mx-2' data-toggle='tooltip'
                                                    data-placement='top' title=''
                                                    data-original-title='Assign To Service Provider'></i>Assign To
                                                Service
                                                Provider </a></li>
                                        @if (empty($order->provider_name) && $order->provider_name == '')
                                            <li
                                                onclick='assignSalesPerson("{{ $order->id }}","{{ $order->branch }}")'>
                                                <a class="dropdown-item"><i
                                                        onclick='assignSalesPerson("{{ $order->id }}","{{ $order->branch }}")'
                                                        class='icofont icofont-business-man mx-2' data-toggle='tooltip'
                                                        data-placement='top' title=''
                                                        data-original-title='Assign Sales Person'></i>Assign Sales
                                                    Person </a>
                                            </li>
                                        @endif
                                    @endif
                                    @if ($order->status != 12 && (session('roleId') != 20 && session('roleId') != 19))
                                        <li onclick='voidReceipt("{{ $order->id }}")'><a class="dropdown-item"><i
                                                    onclick='voidReceipt("{{ $order->id }}")'
                                                    class='alert-confirm text-danger icofont icofont icofont-delete-alt mx-2'
                                                    data-toggle='tooltip' data-placement='top' title=''
                                                    data-original-title='Mark as Void'></i>Mark as Void</a></li>
                                    @endif
                                    @if ($order->status != 4 && session('roleId') != 20 && session('roleId') != 19)
                                        <li onclick='deliveredReceipt("{{ $order->id }}")'><a
                                                class="dropdown-item"><i
                                                    onclick='deliveredReceipt("{{ $order->id }}")'
                                                    class='alert-confirm text-success icofont icofont icofont-tick-mark mx-2'
                                                    data-toggle='tooltip' data-placement='top' title=''
                                                    data-original-title='Mark as Delivered'></i>Mark as Delivered</a>
                                        </li>
                                    @endif
                                    @if (session('roleId') == 20 && $order->status == 6)
                                        <li onclick='assignToBranchModal("{{ $order->id }}")'><a
                                                class="dropdown-item"><i
                                                    onclick='assignToBranchModal("{{ $order->id }}")'
                                                    class='icofont icofont icofont-business-man mx-2'
                                                    data-toggle='tooltip' data-placement='top' title=''
                                                    data-original-title='Assign to Branch'></i>Assign to Branch</a>
                                        </li>
                                    @endif
                                    @if (in_array(session('roleId'), [1, 2, 4]))
                                        <li onclick='discountReceipt("{{ $order->id }}")'><a
                                                class="dropdown-item"><i
                                                    onclick='discountReceipt("{{ $order->id }}")'
                                                    class='alert-confirm text-info icofont icofont icofont-sale-discount mx-2'
                                                    data-toggle='tooltip' data-placement='top' title=''
                                                    data-original-title='Mark as Void'></i>Add Discount</a></li>
                                    @endif
                                </ul>
                            </div>
                        </td>
                    </tr>
                @endforeach
            @else
                <tr>
                    <td colspan="18" class="text-center">No Record Found</td>
                </tr>
            @endif
        </tbody>
    </table>
    <div class="col-md-12">
        {{ $orders->links('pagination::bootstrap-4') }}
    </div>
</div>
<!-- Delivery Date Modal -->
<div class="modal fade in" id="deliveryDateModal" tabindex="-1" aria-labelledby="deliveryDateModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deliveryDateModalLabel">Enter Delivery Date</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label for="delivery-date-input" class="form-label">Delivery Date</label>
                    <input type="date" class="form-control" id="delivery-date-input">
                    <div class="invalid-feedback" id="delivery-date-error">Please select a delivery date.</div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" id="confirm-delivery-date-btn">Mark As
                    Delivered</button>
            </div>
        </div>
    </div>
</div>
<!-- Void Reason Modal -->
<div class="modal fade in" id="void-modal" tabindex="-1" role="dialog" aria-labelledby="voidModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="voidModalLabel">Void Order(s)</h5>
        <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <input type="hidden" id="voidId" value="">
        <div class="form-group">
          <label for="reason">Reason for voiding:</label>
          <textarea id="reason" class="form-control"></textarea>
          <span id="reason_message" class="text-danger"></span>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
        <button type="button" class="btn btn-danger" id="confirm-void-btn">Void</button>
      </div>
    </div>
  </div>
</div>
<script type="text/javascript">
    $(document).ready(function() {
        $('#select-all-orders').on('change', function() {
            $('.order-checkbox').prop('checked', this.checked);
            toggleBulkActionBtns();
        });
        $(document).on('change', '.order-checkbox', function() {
            if (!this.checked) {
                $('#select-all-orders').prop('checked', false);
            } else if ($('.order-checkbox:checked').length === $('.order-checkbox').length) {
                $('#select-all-orders').prop('checked', true);
            }
            toggleBulkActionBtns();
        });

        function toggleBulkActionBtns() {
            if ($('.order-checkbox:checked').length > 0) {
                $('#void-selected-btn').prop('disabled', false);
                $('#deliver-selected-btn').prop('disabled', false);
            } else {
                $('#void-selected-btn').prop('disabled', true);
                $('#deliver-selected-btn').prop('disabled', true);
            }
        }
        // Example bulk action handlers
        let selectedOrderIdsForDelivery = [];
        $('#deliver-selected-btn').off('click').on('click', function() {
            let selectedOrderIds = $('.order-checkbox:checked').map(function() {
                return $(this).val();
            }).get();
            if (selectedOrderIds.length === 0) {
                alert('Please select at least one order.');
                return;
            }
            selectedOrderIdsForDelivery = selectedOrderIds;
            $('#delivery-date-input').val('');
            $('#delivery-date-input').removeClass('is-invalid');
            var deliveryModal = new bootstrap.Modal(document.getElementById('deliveryDateModal'));
            deliveryModal.show();
        });
        $('#confirm-delivery-date-btn').on('click', function() {
            let deliveryDate = $('#delivery-date-input').val();
            if (!deliveryDate) {
                $('#delivery-date-input').addClass('is-invalid');
                return;
            } else {
                $('#delivery-date-input').removeClass('is-invalid');
            }
            let csrfToken = '{{ csrf_token() }}';
            let completed = 0;
            let total = selectedOrderIdsForDelivery.length;
            let deliveryModalEl = document.getElementById('deliveryDateModal');
            let deliveryModal = bootstrap.Modal.getInstance(deliveryModalEl);
            selectedOrderIdsForDelivery.forEach(function(orderId) {
                $.ajax({
                    url: "{{ url('make-receipt-delivered') }}",
                    type: 'POST',
                    data: {
                        _token: csrfToken,
                        id: orderId,
                        reason: deliveryDate
                    },
                    dataType: "json",
                    complete: function() {
                        completed++;
                        if (completed === total) {
                            deliveryModal.hide();
                            alert(
                                'Mark as Delivered requests sent for selected orders.');
                            // Optionally, refresh the table here
                            fetch_data(1);
                        }
                    }
                });
            });
        });

        let selectedOrderIdsForVoid = [];
        $('#void-selected-btn').off('click').on('click', function() {
            let selectedOrderIds = $('.order-checkbox:checked').map(function() {
                return $(this).val();
            }).get();
            if (selectedOrderIds.length === 0) {
                alert('Please select at least one order.');
                return;
            }
            selectedOrderIdsForVoid = selectedOrderIds;
            $('#voidId').val(selectedOrderIdsForVoid.join(','));
            $('#reason').val('');
            $('#reason_message').html('');
            var voidModal = new bootstrap.Modal(document.getElementById('void-modal'));
            voidModal.show();
        });

        $('#confirm-void-btn').off('click').on('click', function() {
            $('#reason_message').html('');
            let reason = $('#reason').val();
            if (!reason) {
                $('#reason_message').html('Please select reason');
                return;
            }
            let csrfToken = '{{ csrf_token() }}';
            let completed = 0;
            let total = selectedOrderIdsForVoid.length;
            let voidModalEl = document.getElementById('void-modal');
            let voidModal = bootstrap.Modal.getInstance(voidModalEl);
            selectedOrderIdsForVoid.forEach(function(orderId) {
                $.ajax({
                    url: "{{ url('make-receipt-void') }}",
                    type: 'POST',
                    data: {
                        _token: csrfToken,
                        id: orderId,
                        reason: reason
                    },
                    dataType: "json",
                    complete: function() {
                        completed++;
                        if (completed === total) {
                            voidModal.hide();
                            alert('Void requests sent for selected orders.');
                            fetch_data(1);
                        }
                    }
                });
            });
        });
    });

    @php
        $collection = collect($totalorders);
        $pending = $collection->filter(fn($item) => $item->order_status_name == 'Pending')->values()->all();
        $processing = $collection->filter(fn($item) => $item->order_status_name == 'Processing')->values()->all();
        $voidOrders = $collection->filter(fn($item) => $item->order_status_name == 'Void')->values()->all();
        $deliveredOrders = $collection->filter(fn($item) => $item->order_status_name == 'Delivered')->values()->all();
        $dispatchOrders = $collection->filter(fn($item) => $item->order_status_name == 'Dispatch')->values()->all();
        $salesReturnOrders = $collection->filter(fn($item) => $item->order_status_name == 'Sales Return')->values()->all();
    @endphp


    $("#pendingorders").html("{{ count($pending) > 0 ? $pending[0]->totalorders : 0 }}");
    $("#totalpendingamount").html("Rs. {{ count($pending) > 0 ? number_format($pending[0]->sales, 0) : 0 }}");
    $("#processingorders").html("{{ count($processing) > 0 ? $processing[0]->totalorders : 0 }}");
    $("#totalprocessingamount").html("Rs. {{ count($processing) > 0 ? number_format($processing[0]->sales, 0) : 0 }}");
    $("#voidorders").html("{{ count($voidOrders) > 0 ? $voidOrders[0]->totalorders : 0 }}");
    $("#totalvoidamount").html("Rs. {{ count($voidOrders) > 0 ? number_format($voidOrders[0]->sales, 0) : 0 }}");
    $("#deliveredorders").html("{{ count($deliveredOrders) > 0 ? $deliveredOrders[0]->totalorders : 0 }}");
    $("#totaldeliveredamount").html(
        "Rs. {{ count($deliveredOrders) > 0 ? number_format($deliveredOrders[0]->sales, 0) : 0 }}");
    $("#dispatchorders").html("{{ count($dispatchOrders) > 0 ? $dispatchOrders[0]->totalorders : 0 }}");
    $("#totaldispatchamount").html(
        "Rs. {{ count($dispatchOrders) > 0 ? number_format($dispatchOrders[0]->sales, 0) : 0 }}");
    $("#salesreturnorders").html("{{ count($salesReturnOrders) > 0 ? $salesReturnOrders[0]->totalorders : 0 }}");
    $("#totalsalesreturnamount").html(
        "Rs. {{ count($salesReturnOrders) > 0 ? number_format($salesReturnOrders[0]->sales, 0) : 0 }}");
    $("#totalorders").html(
        "{{ (count($processing) > 0 ? $processing[0]->totalorders : 0) + (count($voidOrders) > 0 ? $voidOrders[0]->totalorders : 0) + (count($deliveredOrders) > 0 ? $deliveredOrders[0]->totalorders : 0) + (count($pending) > 0 ? $pending[0]->totalorders : 0) + (count($dispatchOrders) > 0 ? $dispatchOrders[0]->totalorders : 0) + (count($salesReturnOrders) > 0 ? $salesReturnOrders[0]->totalorders : 0) }}"
    );
    $("#totalamount").html(
        "Rs. {{ number_format((count($processing) > 0 ? $processing[0]->sales : 0) + (count($voidOrders) > 0 ? $voidOrders[0]->sales : 0) + (count($deliveredOrders) > 0 ? $deliveredOrders[0]->sales : 0) + (count($pending) > 0 ? $pending[0]->sales : 0) + (count($dispatchOrders) > 0 ? $dispatchOrders[0]->sales : 0) + (count($salesReturnOrders) > 0 ? $salesReturnOrders[0]->sales : 0), 0) }}"
    );
    $("#totaltaxorders").html(
        "{{ count($totaltax) > 0 && $totaltax[0]->srbtaxamount > 0 ? $totaltax[0]->totalorders : 0 }}");
    $("#totaltaxamount").html("Rs. {{ count($totaltax) > 0 ? $totaltax[0]->srbtaxamount : 0 }}");
    @if (!empty($orderTimingGraph))
        $(document).ready(function() {
            /* bar chart */
            var chLine = document.getElementById("chLine");
            $("#chLine").empty();
            $("#divLineGraph").append("<canvas id='chLine'></canvas>");
            xaxis = {!! json_encode($orderTimingGraph->pluck('hour_range')) !!};
            data = {!! json_encode($orderTimingGraph->pluck('total_orders')) !!};
            height = "{{ $height }}";

            var chartData = {
                labels: xaxis,
                datasets: [{
                    data: data,
                    backgroundColor: 'transparent',
                    borderColor: colors[1],
                    borderWidth: 4,
                    pointBackgroundColor: colors[1]
                }]
            };
            if (chLine) {
                chLine.height = height;
                new Chart(chLine, {
                    type: 'line',
                    data: chartData,
                    options: {
                        legend: {
                            display: false
                        },
                        hover: { // Disable hover effects
                            mode: null
                        },
                        tooltips: { // Disable tooltips
                            enabled: true
                        },
                        events: [],
                        responsiveAnimationDuration: 0,
                        scales: {
                            xAxes: [{
                                ticks: {
                                    beginAtZero: true
                                }
                            }]
                        },
                        responsive: true
                    }
                });
            }

            /* bar chart */
            var chBarOne = document.getElementById("chBarOne");
            $("#chBarOne").empty();
            xaxis = {!! json_encode($orderTimingGraph->pluck('hour_range')) !!};
            data = {!! json_encode($orderTimingGraph->pluck('total_amount')) !!};
            height = "{{ $height }}";

            if (chBarOne) {
                chBarOne.height = height;
                new Chart(chBarOne, {
                    type: 'bar',
                    data: {
                        labels: xaxis,
                        datasets: [{
                            data: data,
                            backgroundColor: colors[1]
                        }, ]
                    },
                    options: {
                        legend: {
                            display: false
                        },
                        scales: {
                            yAxes: [{
                                display: true // Hide the Y-axis
                            }],
                            xAxes: [{
                                barPercentage: 0.5,
                                categoryPercentage: 1
                            }]
                        }
                    }
                });
            }
        })
    @endif
</script>
