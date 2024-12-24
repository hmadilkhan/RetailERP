@extends('layouts.master-layout')

@section('title', 'Order Booking')

@section('breadcrumtitle', 'View PF Fund')

@section('navmanage', 'active')

@section('navtaxslabs', 'active')

@section('content')
    <link rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/MaterialDesign-Webfont/5.3.45/css/materialdesignicons.css"
        integrity="sha256-NAxhqDvtY0l4xn+YVa6WjAcmd94NNfttjNsDmNatFVc=" crossorigin="anonymous" />
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous">
    </script>
    <form id="placeOrderForm" method="POST" action="{{ url('place-order') }}">
        @csrf
        <input type="hidden" id="subTotalAmount" name="subTotal" />
        <input type="hidden" id="taxAmount" name="taxAmount" />
        <input type="hidden" id="discountAmount" name="discountAmount" />
        <input type="hidden" id="netAmount" name="totalAmount" />
        <section>
            <div class="card">
                <div class="card-header">Retail Pre-order  Booking</div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3">
                            <div class="form-group">
                                <label class="form-control-label "><i class="icofont icofont-barcode"></i>
                                    Customers</label>
                                <select id="customerId" name="customerId" class="form-control select2"></select>
                                <div id="customerId_message" class="text-danger message"></div>
                            </div>
                        </div>

                        <div class="col-md-3">
                            <div class="form-group">
                                <label class="form-control-label "><i class="icofont icofont-barcode"></i>
                                    Order Type</label>
                                <select class="select2" id="orderTypeId" name="orderTypeId">
                                    <option value="">Order Type</option>
                                    @if (!empty($orderTypes))
                                        @foreach ($orderTypes as $types)
                                            <option value="{{ $types->order_mode_id }}">
                                                {{ $types->order_mode }}
                                            </option>
                                        @endforeach
                                    @endif
                                </select>
                                <div id="orderTypeId_message" class="text-danger message"></div>
                            </div>
                        </div>

                        <div class="col-md-3">
                            <div class="form-group">
                                <label class="form-control-label "><i class="icofont icofont-barcode"></i>
                                    Order Payment</label>
                                <select class="select2" id="paymentId" name="paymentId">
                                    <option value="">Order Payment</option>
                                    @if (!empty($payments))
                                        @foreach ($payments as $payment)
                                            <option value="{{ $payment->payment_id }}">
                                                {{ $payment->payment_mode }}
                                            </option>
                                        @endforeach
                                    @endif
                                </select>
                                <div id="paymentId_message" class="text-danger message"></div>
                            </div>
                        </div>

                        <div class="col-md-3">
                            <div class="form-group">
                                <label class="form-control-label "><i class="icofont icofont-barcode"></i>
                                    Branches</label>
                                <select class="select2" id="branchId" name="branchId">
                                    <option value="">Branches</option>
                                    @if ($branches)
                                        @foreach ($branches as $branch)
                                            <option value="{{ $branch->branch_id }}">
                                                {{ $branch->branch_name }}
                                            </option>
                                        @endforeach
                                    @endif
                                </select>
                                <div id="branchId_message" class="text-danger message"></div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label class="form-control-label "><i class="icofont icofont-barcode"></i>
                                    Terminals</label>
                                <select class="select2" id="terminalId" name="terminalId">
                                    <option value="">Terminals</option>
                                </select>
                                <div id="terminalId_message" class="text-danger message"></div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label class="form-control-label "><i class="icofont icofont-barcode"></i>
                                    Sales Persons</label>
                                <select class="select2" id="salespersonId" name="salespersonId">
                                    <option value="">Sales Persons</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <section>
            <div class="card">
                <div class="card-header">Add Order Items</div>
                <div class="card-body">

                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="form-control-label "><i class="icofont icofont-barcode"></i>
                                    Products</label>
                                <select id="productId" class="js-data-example-ajax form-control select2"></select>
                                <div id="productId_message" class="text-danger item-message"></div>
                            </div>
                        </div>
                        <div class="col-md-2 col-sm-12">
                            <div id="itemcode" class="form-group">
                                <label class="form-control-label "><i class="icofont icofont-barcode"></i> Qty</label>
                                <input class="form-control" type="number" id="qty" placeholder="Enter Qty" onchange="qtyChange()" />
                                <div id="qty_message" class="text-danger item-message"></div>
                            </div>
                        </div>
                        <div class="col-md-2 col-sm-12">
                            <div id="itemcode" class="form-group">
                                <label class="form-control-label "><i class="icofont icofont-barcode"></i>Price</label>
                                <input class="form-control" type="text" id="price" placeholder="Enter Price "  min="1"/>
                                <div id="price_message" class="text-danger item-message"></div>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label class="form-control-label "><i class="icofont icofont-barcode"></i>
                                    Tax</label>
                                <select class="select2" id="taxValue" name="taxValue">
                                    <option value="">Select Tax</option>
                                    @if (!empty($taxes))
                                        @foreach ($taxes as $tax)
                                            <option value="{{ $tax->value }}">
                                                {{ $tax->name . ' ' . $tax->value . '%' }}
                                            </option>
                                        @endforeach
                                    @endif
                                </select>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <div class="d-flex">
                                    <div class="form-check f-right">
                                        <input class="form-check-input" type="radio" name="percentage" checked
                                            value="percentage">
                                        <label class="form-check-label" for="type">
                                            Percentage
                                        </label>
                                    </div>
                                    <div class="form-check f-right m-l-2">
                                        <input class="form-check-input" type="radio" name="percentage" value="amount">
                                        <label class="form-check-label" for="type">
                                            Amount
                                        </label>
                                    </div>
                                </div>
                                <input class="form-control" type="text" id="discountValue"
                                    placeholder="Enter Discount" />
                            </div>
                        </div>
                        <div class="col-md-2 col-sm-12">
                            <div id="itemcode" class="form-group">
                                <label class="form-control-label "><i class="icofont icofont-barcode"></i>Total Price</label>
                                <input class="form-control" type="text" id="totalPrice" placeholder="Enter Price "  min="1"/>
                                <div id="price_message" class="text-danger item-message"></div>
                            </div>
                        </div>
                        <div class="col-md-3 col-sm-12">
                            <div id="itemcode" class="form-group">
                                <label class="form-control-label "><i class="icofont icofont-barcode"></i>Tax</label>
                                <input class="form-control" type="text" id="itemTax" placeholder="Enter Price " />
                                <div id="price_message" class="text-danger item-message"></div>
                            </div>
                        </div>
                        <div class="col-md-3 col-sm-12">
                            <div id="itemcode" class="form-group">
                                <label class="form-control-label "><i class="icofont icofont-barcode"></i>Discount</label>
                                <input class="form-control" type="text" id="itemDiscount" placeholder="Enter Price " />
                                <div id="price_message" class="text-danger item-message"></div>
                            </div>
                        </div>
                        <div class="col-md-2 col-sm-12">
                            <div id="itemcode" class="form-group">
                                <label class="form-control-label "><i class="icofont icofont-barcode"></i>Gross</label>
                                <input class="form-control" type="text" id="itemGross" placeholder="Enter Price "  min="1"/>
                                <div id="price_message" class="text-danger item-message"></div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <button type="button" id="add-order-item" data-placement="bottom"
                                class="btn btn-success  waves-effect waves-light mt-4">Add Item</button>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <section>
            <div class="card">
                <div class="card-header">Order Items Details</div>
                <div class="card-body">
                    <table id="itemsTable" class="table table-striped">
                        <thead>
                            <th>S.No.</th>
                            <th>Product</th>
                            <th>Price</th>
                            <th>Qty</th>
                            <th>Amount</th>
                            <th>Action</th>
                        </thead>
                        <tbody>

                        </tbody>
                    </table>
                </div>
            </div>
        </section>

        <section>
            <div class="card">
                <div class="card-body">
                    <div class="row">
                        <!-- Sub Total -->
                        <div class="col-12 col-lg-12 col-md-12 col-sm-12 py-2 d-flex justify-content-end">
                            <div class="col-8 col-lg-8 col-md-8 col-sm-8"></div>
                            <div class="col-4 col-lg-4 col-md-4 col-sm-4">
                                <div class="col-10 col-lg-10 col-md-10 col-sm-10 text-end">
                                    <span class="fw-bold ">Sub Total :</span>
                                </div>
                                <div class="col-2 col-lg-2 col-md-2 col-sm-2 text-end">
                                    <span id="subTotal">0.00</span>
                                </div>
                            </div>
                        </div>
                        <!-- Discount -->
                        <div class="col-12 col-lg-12 col-md-12 col-sm-12  py-2 d-flex justify-content-end">
                            <div class="col-8 col-lg-8 col-md-8 col-sm-8"></div>
                            <div class="col-4 col-lg-4 col-md-4 col-sm-4">
                                <div class="col-10 col-lg-10 col-md-10 col-sm-10 text-end">
                                    <span class="fw-bold ">Discount :</span>
                                </div>
                                <div class="col-2 col-lg-2 col-md-2 col-sm-2 text-end">
                                    <span id="totalDiscount">0.00</span>
                                </div>
                            </div>
                        </div>
                        <!-- Tax Amount -->
                        <div class="col-12 col-lg-12 col-md-12 col-sm-12  py-2 d-flex justify-content-end">
                            <div class="col-8 col-lg-8 col-md-8"></div>
                            <div class="col-4 col-lg-4 col-md-4 col-sm-4">
                                <div class="col-10 col-lg-10 col-md-10 col-sm-10 text-end">
                                    <span class="fw-bold ">Tax Amount :</span>
                                </div>
                                <div class="col-2 col-lg-2 col-md-2 col-sm-2 text-end">
                                    <span id="totalTax">0.00</span>
                                </div>
                            </div>
                        </div>
                        <!-- Total Amount -->
                        <div class="col-12 col-lg-12 col-md-12 col-sm-12 py-2 d-flex justify-content-end">
                            <div class="ccol-8 col-lg-8 col-md-8"></div>
                            <div class="col-4 col-lg-4 col-md-4 col-sm-4">
                                <div class="col-10 col-lg-10 col-md-10 col-sm-10 text-end">
                                    <span class="fw-bold ">Total Amount :</span>
                                </div>
                                <div class="col-2 col-lg-2 col-md-2 col-sm-2 text-end">
                                    <span id="totalAmount">0.00</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <section>
            <div class="row">
                <div class="col-md-12 text-end">
                    <button type="button" id="placeOrder" class="btn btn-success text-end">Place Order</button>
                </div>
            </div>
        </section>
    </form>
@endsection

@section('scriptcode_three')
    <script>
        $(".select2").select2();

        // Function to initialize Select2 with common options
        function initializeSelect2(selector, ajaxUrl, placeholder, mapFunction) {
            $(selector).select2({
                ajax: {
                    url: ajaxUrl,
                    dataType: 'json',
                    processResults: function(data) {
                        return {
                            results: $.map(data.items, mapFunction)
                        };
                    }
                },
                placeholder: placeholder,
                minimumInputLength: 1,
                language: {
                    searching: function() {
                        return "Searching...";
                    }
                }
            });
        }

        // Initialize Select2 for customer search
        initializeSelect2(
            '#customerId',
            '{{ route('search-customer-by-names') }}',
            'Search for a Customer',
            function(item) {
                return {
                    text: item.name,
                    id: item.id
                };
            }
        );

        // Initialize Select2 for inventory search
        initializeSelect2(
            '.js-data-example-ajax',
            '{{ route('search-inventory') }}',
            'Search for a Product',
            function(item) {
                return {
                    text: item.product_name + " | " + item.item_code,
                    id: item.id
                };
            }
        );

         // Getting Terminal and Sales person according to selected Branch
        $("#branchId").change(function() {
            $.ajax({
                url: "{{ url('get-terminals-and-salespersons') }}",
                type: "POST",
                data: {
                    _token: "{{ csrf_token() }}",
                    branchId: $(this).val(),
                },
                success: function(result) {
                    if (result.status == 200) {
                        if (result.status == 200) {
                            $.each(result.terminals, function(index, value) {
                                $("#terminalId").append("<option value='" + value
                                    .terminal_id + "'>" + value.terminal_name +
                                    "</option>");
                            });
                            $.each(result.salesPersons, function(index, value) {
                                $("#salespersonId").append("<option value='" + value
                                    .serviceprovideruser.user_id + "'>" + value
                                    .provider_name +
                                    "</option>");
                            });
                        }
                    }
                },
            })
        })

        // Getting Price of Selected product
        $("#productId").change(function() {
            $("#price").prop("disabled", true);
            $.ajax({
                url: "{{ url('get-price-of-product') }}",
                type: "POST",
                data: {
                    _token: "{{ csrf_token() }}",
                    id: $(this).val()
                },
                success: function(result) {
                    $("#price").prop("disabled", false);
                    if (result.status == 200) {
                        $("#price").val(result.price.retail_price);
                        $("#qty").val(1);
                        $("#totalPrice").val(result.price.retail_price);
                        
                        itemCalculation();

                    }
                },
            });

        });

        // Check if the item already exist in the cart
        function checkExistence(firstval) {
            let result = false;
            $("#itemsTable tbody tr").each(function(index) {
                let first = $(this).children().eq(0).val();
                if (firstval == first) { //&& secondval == second
                    result = true;
                } else {
                    result = false;
                }
            });
            return result;
        }

        function qtyChange() 
        {
            let price = $("#price").val();
            let qty = $("#qty").val();
            let totalPrice = price * qty;
            $("#totalPrice").val(totalPrice);
            itemCalculation();
        }

        function itemCalculation()
        {
            let price = $("#price").val();
            let qty = $("#qty").val();
            let subTotal = $("#totalPrice").val();
            let tax = $("#taxValue").val();
            

            if (subTotal != "") {
                let taxAmount = calculateTax(subTotal)
                let discountAmount = calculateDiscount(subTotal);
                let grossAmount = (parseFloat(subTotal) + parseFloat(taxAmount)) -  parseFloat(discountAmount); 
                console.log(grossAmount);
                
                $("#itemTax").val(taxAmount);
                $("#itemDiscount").val(discountAmount);
                $("#itemGross").val(Math.round(grossAmount));
                
            }
        }

        // Main method of Calculating cart items Total
        function calculateTotals() {
            let subTotal = 0;
            $("#itemsTable tbody tr").each(function(index) {
                subTotal += $(this).children().eq(9).text() * 1;
            });

            let taxAmount = calculateTax(subTotal);
            let discountAmount = calculateDiscount(subTotal);
            let totalAmount = subTotal + taxAmount - discountAmount;

            // SETTING HIDDEN VARIABLE TO SEND WITH FORM DATA
            $("#subTotalAmount").val(subTotal);
            $("#taxAmount").val(taxAmount);
            $("#discountAmount").val(discountAmount);
            $("#netAmount").val(totalAmount);

            $("#subTotal").html(subTotal.toLocaleString());
            $("#totalTax").html(taxAmount.toLocaleString());
            $("#totalDiscount").html(discountAmount.toLocaleString());
            $("#totalAmount").html(totalAmount.toLocaleString());
        }

        function calculateTax(subTotal) {
            let taxValue = $("#taxValue").val();

            if (taxValue != "") {
                return Math.round((subTotal * (taxValue / 100)));
            }

            return 0;
        }

        function calculateDiscount(subTotal) {
            let discountType = $('input[name="percentage"]:checked').val();
            let discountValue = $("#discountValue").val();

            if (discountValue != "") {
                if (discountType == "percentage") {
                    return Math.round((subTotal * (discountValue / 100)));
                } else {
                    return discountValue;
                }
            }
            return 0;
        }

        $("#taxValue,#discountValue").change(function() {
            itemCalculation();
        })

        function emptyControls() {
            $("#productId").val('').change();
            $("#qty").val('')
            $("#price").val('');
        }

        // Add Item To Cart
        $("#add-order-item").click(function() {
            $(".item-message").html('');
            if ($("#productId").val() == null) {
                $("#productId_message").html("Please Select Product");
            } else if ($("#qty").val() == "") {
                $("#qty_message").html("Please Enter Qty");
            } else if ($("#price").val() == "") {
                $("#price_message").html("Please Enter Price");
            } else {

                let rowLength = $('#itemsTable tbody').find('tr').length;
                let productId = $("#productId").val();
                let productName = $.trim($("#productId option:selected").text());
                let qty = $("#qty").val();
                let price = $("#price").val();
                let amount = qty * price;

                let result = checkExistence(productId);

                if (result == false) {
                    let newRow = "<tr id='row" + (rowLength + 1) + "'>" +
                        '<input type="hidden" value="' + productId + '" name="products[]" />' +
                        '<input type="hidden" value="' + productName + '" name="productnames[]" />' +
                        '<input type="hidden" value="' + qty + '" name="qty[]" />' +
                        '<input type="hidden" value="' + price + '" name="price[]" />' +
                        '<input type="hidden" value="' + amount + '" name="amount[]" />' +


                        "<td>" + (rowLength + 1) + "</td>" +
                        "<td>" + productName + "</td>" +
                        "<td>" + price + "</td>" +
                        "<td>" + qty + "</td>" +
                        "<td>" + amount + "</td>" +

                        "<td colspan='4'>&nbsp;&nbsp;<i style='cursor: pointer;' class='icofont icofont-trash text-danger' onClick=deleteItem(" +
                        (rowLength + 1) + ")>&nbsp;&nbsp;Delete</i></td>" +
                        "</tr>";

                    $("#itemsTable > tbody").append(newRow);
                    calculateTotals();
                    emptyControls();
                } else {
                    alert("already added")
                }
            }

        })

        // Place Order 
        $("#placeOrder").click(function(e) {
            e.preventDefault();
            $(".message").html('');
            let formData = new FormData(document.getElementById('placeOrderForm'));
            // Select all controls and buttons to disable
            let controls = document.querySelectorAll('input, select, button');

            // Disable controls and buttons
            controls.forEach(control => control.disabled = true);

            $.ajax({
                url: "{{ url('place-order') }}",
                type: "POST",
                data: formData,
                processData: false, // Prevent jQuery from processing the data
                contentType: false, // Prevent jQuery from setting the content type
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(result) {
                    if (result.status == 200) {
                        if (result.orderId != "") {
                            window.open("{{ url('order-detail') }}" + "/" + result.orderId);
                        }
                        location.reload();
                    } else {
                        // Enable controls and buttons
                        controls.forEach(control => control.disabled = false);
                    }
                },
                error: function(xhr, status, error) {
                    if (xhr.status === 422) {
                        $.each(xhr.responseJSON.errors, function(key, val) {
                            $("#" + key + "_message").html(val[0]);
                        });
                    }
                    // Enable controls and buttons
                    controls.forEach(control => control.disabled = false);
                }
            })
        })

        // Delete item From Cart
        function deleteItem(id) {
            $("#row" + id).remove();
            calculateTotals();
        }
    </script>
@endsection
